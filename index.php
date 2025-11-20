<?php
// Definir BASE_URL primero
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = $protocol . "://" . $host . $scriptName;
define('BASE_URL', rtrim($baseUrl, '/') . '/');

// Cargar configuración de sesión PRIMERO
require_once 'config/session.php';

// Cargar configuración de base de datos
require_once 'config/database.php';

// Enrutamiento básico
$url = isset($_GET['url']) ? $_GET['url'] : 'dashboard';
$url = rtrim($url, '/');
$url = explode('/', $url);

$controllerName = ucfirst($url[0]) . 'Controller';
$methodName = isset($url[1]) ? $url[1] : 'index';

// Parámetros adicionales
$params = array_slice($url, 2);

// Manejar rutas de error
if ($url[0] === 'error') {
    $errorCode = $url[1] ?? '404';
    if ($errorCode === '403') {
        require_once 'views/components/403.php';
    } else {
        require_once 'views/components/404.php';
    }
    exit();
}

// Verificar autenticación para rutas protegidas
$publicRoutes = ['auth/login', 'auth/logout'];
$currentRoute = $url[0] . '/' . (isset($url[1]) ? $url[1] : 'index');

if (!in_array($currentRoute, $publicRoutes) && !isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'auth/login');
    exit();
}

// Cargar controlador
$controllerFile = 'controllers/' . $controllerName . '.php';
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        
        if (method_exists($controller, $methodName)) {
            // Llamar al método del controlador con parámetros
            if (!empty($params)) {
                call_user_func_array([$controller, $methodName], $params);
            } else {
                $controller->$methodName();
            }
        } else {
            // Método no encontrado - 404
            require_once 'views/components/404.php';
        }
    } else {
        // Controlador no encontrado - 404
        require_once 'views/components/404.php';
    }
} else {
    // Archivo no encontrado - 404
    require_once 'views/components/404.php';
}
?>