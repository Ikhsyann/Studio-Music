<?php

// Jangan start session di sini, biar controller yang handle
// session_start(); // REMOVED - Controller will handle this

require_once '../core/Controller.php';
require_once '../core/Model.php';

// Default redirect ke login jika tidak ada URL
$url = isset($_GET['url']) ? $_GET['url'] : 'auth/login';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

$controllerName = isset($url[0]) && !empty($url[0]) ? ucfirst($url[0]) . 'Controller' : 'AuthController';
$controllerFile = '../app/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();
    
    $method = isset($url[1]) && !empty($url[1]) ? $url[1] : 'login';
    
    $params = isset($url[2]) ? array_slice($url, 2) : [];
    
    if (method_exists($controller, $method)) {
        call_user_func_array([$controller, $method], $params);
    } else {
        http_response_code(404);
        echo "404 - Method not found: " . $method;
    }
} else {
    http_response_code(404);
    echo "404 - Controller not found: " . $controllerName;
}
?>
