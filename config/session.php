<?php
// Configuración de sesión - Solo se ejecuta si no hay sesión activa
if (session_status() === PHP_SESSION_NONE) {
    // 30 minutos de inactividad (1800 segundos)
    ini_set('session.gc_maxlifetime', 1800);
    session_set_cookie_params(1800);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_httponly', 1);
    
    session_start();
}

// Verificar y actualizar tiempo de última actividad
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > 1800) {
        session_unset();
        session_destroy();
        if (!defined('BASE_URL')) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            $scriptName = dirname($_SERVER['SCRIPT_NAME']);
            $baseUrl = $protocol . "://" . $host . $scriptName;
            define('BASE_URL', rtrim($baseUrl, '/') . '/');
        }
        header('Location: ' . BASE_URL . 'auth/login?expired=1');
        exit();
    }
}

// Actualizar tiempo de última actividad
$_SESSION['last_activity'] = time();
?>