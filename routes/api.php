<?php

use App\Http\Controllers\DeploymentController;
use Illuminate\Support\Facades\Route;

Route::post('/git-deploy-webhook', [DeploymentController::class, 'deploy']);
