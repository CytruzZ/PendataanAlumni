<?php

use App\Http\Controllers\Api\GFormWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Pendataan Alumni MNI IPB
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/gform-webhook', [GFormWebhookController::class, 'handle'])->name('api.gform_webhook');
