<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

use FastRoute\RouteCollector;
use App\Controllers\CurrencyController;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$dispatcher = FastRoute\simpleDispatcher(function(RouteCollector $r) {
    $r->addRoute('GET', '/', [CurrencyController::class, 'index']);
    $r->addRoute('POST', '/rates', [CurrencyController::class, 'getRates']);
    $r->addRoute('POST', '/base', [CurrencyController::class, 'setBaseCurrency']);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;
        
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
        
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        
        $controllerClass = $handler[0];
        $method = $handler[1];
        
        $controller = new $controllerClass();
        $controller->$method($vars);
        break;
} 