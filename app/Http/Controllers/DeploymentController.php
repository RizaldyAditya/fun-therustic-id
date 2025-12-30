<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class DeploymentController extends Controller
{
    public function deploy(Request $request)
    {
        // 1. Validate the GitHub Signature (Security)
        $signature = $request->header('X-Hub-Signature-256');
        $payload   = $request->getContent();
        $secret    = config('app.deploy_secret');

        $hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($hash, $signature)) {
            abort(403, 'Invalid signature');
        }

        // 2. Run the Deployment Commands
        // We use Laravel 12's Process facade for clean execution
        $result = Process::path(base_path())
            ->run([
                'git', 'fetch', 'origin',
                'git', 'reset', '--hard', 'origin/main',
                'composer', 'install', '--no-dev', '--optimize-autoloader',
                'php', 'artisan', 'migrate', '--force',
                'php', 'artisan', 'optimize',
            ]);

        if ($result->successful()) {
            return response()->json(['message' => 'Deployed successfully'], 200);
        }

        return response()->json(['message' => 'Deploy failed', 'error' => $result->errorOutput()], 500);
    }
}
