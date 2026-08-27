<?php

use App\Http\Controllers\Api\GFormWebhookController;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$request = Request::capture();

// Bind request to container before bootstrapping so UrlGenerator gets the request
$app->instance('request', $request);

$kernel->bootstrap();

try {
    $controller = $app->make(GFormWebhookController::class);
    $response = $controller->handle($request);
    $response->send();
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}

$kernel->terminate($request, $response ?? null);
