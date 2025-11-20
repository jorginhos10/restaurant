<?php
// api/session-status.php - Endpoint para verificar estado de sesión
require_once '../config/database.php';

// Definir BASE_URL primero
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = $protocol . "://" . $host . $scriptName;
define('BASE_URL', rtrim($baseUrl, '/') . '/');

require_once '../config/session.php';

header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && isset($_SESSION['last_activity'])) {
    // Verificar si la sesión sigue activa
    if (time() - $_SESSION['last_activity'] > 1800) {
        echo json_encode(['status' => 'expired']);
    } else {
        echo json_encode(['status' => 'active']);
    }
} else {
    echo json_encode(['status' => 'expired']);
}
?>