<?php
// Definir BASE_URL primero
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = $protocol . "://" . $host . $scriptName;
define('BASE_URL', rtrim($baseUrl, '/') . '/');

session_start();
session_destroy();
header('Location: ' . BASE_URL . 'auth/login');
exit();
?>