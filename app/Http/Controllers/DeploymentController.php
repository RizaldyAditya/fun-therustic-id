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
            abort(403, 'Invalid signature');
        }

        // 1. Identify where things are
        $path = base_path();

        // 2. Execute with a simpler approach
        // We'll use a single string which Laravel 12 converts to a shell command
        $result = Process::path($path)->run('git fetch origin && git reset --hard origin/main 2>&1');

        if ($result->failed()) {
            Log::error('Deployment Failed', [
                'output' => $result->output(),
                'error'  => $result->errorOutput(),
            ]);

            return response()->json([
                'message' => 'Deploy failed',
                'details' => $result->errorOutput() ?: $result->output(),
            ], 500);
        }

        // 3. If git succeeded, run the rest
        Process::path($path)->run('
            composer install --no-dev &&
            php artisan migrate --force &&
            php artisan shield:generate --all &&
            php artisan filament:upgrade &&
            php artisan filament:optimize
        ');

        return response()->json(['message' => 'Deployed successfully']);
    }
}
