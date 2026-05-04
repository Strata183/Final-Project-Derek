<?php
require_once __DIR__ . '/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = $DB_HOST ?? $servername ?? 'localhost';
$user = $DB_USER ?? $username ?? 'root';
$pass = $DB_PASS ?? $password ?? '';
$database = $DB_NAME ?? $dbname ?? '';
$dbPort = (int)($DB_PORT ?? $port ?? 3306);

$conn = new mysqli($host, $user, $pass, $database, $dbPort);
$conn->set_charset('utf8mb4');
