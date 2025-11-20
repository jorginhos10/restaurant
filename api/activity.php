<?php
// api/activity.php - Endpoint para registrar actividad del usuario
require_once '../config/database.php';

// Definir BASE_URL primero
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = $protocol . "://" . $host . $scriptName;
define('BASE_URL', rtrim($baseUrl, '/') . '/');

require_once '../config/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simplemente actualizar la sesión existente
    // La lógica de expiración ya está en session.php
    echo json_encode(['status' => 'active']);
    exit();
}

echo json_encode(['status' => 'error']);
?>