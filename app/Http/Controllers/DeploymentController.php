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
        $payload   = $request->getContent();
        $secret    = config('app.deploy_secret');

        $hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($hash, $signature)) {
            Log::warning('Deployment attempt with invalid signature');
            abort(403, 'Invalid signature');
        }

        $path = base_path();

        // 1. Pull the code
        $gitResult = Process::path($path)->run('git fetch origin && git reset --hard origin/main 2>&1');

        if ($gitResult->failed()) {
            Log::error('Git Pull Failed', ['output' => $gitResult->output()]);
            return response()->json(['message' => 'Git failed'], 500);
        }

        // 2. Run the technical updates
        // Added --no-scripts to prevent the exit code 255 error
        // Added permission:cache-reset to apply Shield changes
        $processResult = Process::path($path)->run('
            composer install --no-dev --optimize-autoloader --no-scripts &&
            php artisan migrate --force &&
            php artisan shield:generate --all &&
            php artisan permission:cache-reset &&
            php artisan filament:upgrade &&
            php artisan optimize:clear &&
            php artisan filament:optimize &&
            php artisan config:cache &&
            php artisan route:cache &&
            php artisan view:cache
        ');

        if ($processResult->failed()) {
            Log::error('Deployment Commands Failed', [
                'output' => $processResult->output(),
            ]);
            return response()->json(['message' => 'Post-pull commands failed'], 500);
        }

        return response()->json(['message' => 'Deployed successfully']);
    }
}
