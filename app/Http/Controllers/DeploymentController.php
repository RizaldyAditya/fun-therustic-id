<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class DeploymentController extends Controller
{
    public function deploy(Request $request)
    {
        // Validate the GitHub Signature (Security)
        $signature = $request->header('X-Hub-Signature-256');
        $payload = $request->getContent();
        $secret = config('app.deploy_secret');
        $hash = 'sha256='.hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($hash, $signature)) {
            Log::warning('Deployment attempt with invalid signature');
            abort(403, 'Invalid signature');
        }

        $path = base_path();
        $php = PHP_BINARY;
        $gitResult = Process::path($path)->run('git fetch origin && git reset --hard origin/main 2>&1');
        if ($gitResult->failed()) {
            Log::error('Git Pull Failed', ['output' => $gitResult->output()]);

            return response()->json(['message' => 'Git failed'], 500);
        }

        $commands = [
            'composer install --no-dev --optimize-autoloader --no-scripts',
            "$php artisan migrate --force",
            "$php artisan shield:generate --all",
            "$php artisan permission:cache-reset",
            "$php artisan filament:upgrade",
            "$php artisan optimize:clear",
            "$php artisan filament:optimize",
            "$php artisan config:cache",
            "$php artisan route:cache",
            "$php artisan view:cache",
        ];
        $fullCommand = implode(' && ', $commands);
        $processResult = Process::path($path)->run($fullCommand);

        if ($processResult->failed()) {
            // Check Laravel Logs (storage/logs/laravel.log)
            Log::error('Deployment Failed at Command Phase', [
                'exit_code' => $processResult->exitCode(),
                'error_output' => $processResult->errorOutput(), // This captures the ACTUAL terminal error
                'standard_output' => $processResult->output(),
            ]);

            return response()->json([
                'message' => 'Commands failed',
                'error' => $processResult->errorOutput(),
            ], 500);
        }

        return response()->json(['message' => 'Deployed successfully']);
    }
}
