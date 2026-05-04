<?php
require_once __DIR__ . '/config.php';

if (!extension_loaded('mysqli')) {
    http_response_code(500);
    exit('PHP mysqli extension is not enabled. Enable mysqli in PHP before running this site.');
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = $DB_HOST ?? $servername ?? 'localhost';
$user = $DB_USER ?? $username ?? 'root';
$pass = $DB_PASS ?? $password ?? '';
$database = $DB_NAME ?? $dbname ?? '';
$dbPort = (int)($DB_PORT ?? $port ?? 3306);

try {
    $conn = new mysqli($host, $user, $pass, $database, $dbPort);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit('Database connection failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
}
