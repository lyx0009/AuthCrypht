<?php
// Load environment variables from .env
$env = parse_ini_file(__DIR__ . '/.env');

$host = getenv('DB_HOST') ?: $env['DB_HOST'];
$user = getenv('DB_USER') ?: $env['DB_USER'];
$pass = getenv('DB_PASS') ?: $env['DB_PASS'];
$name = getenv('DB_NAME') ?: $env['DB_NAME'];
$port = (int)(getenv('DB_PORT') ?: $env['DB_PORT'] ?? 3306);

// Create the connection
$conn = new mysqli($host, $user, $pass, $name, $port);

// Check connection
if ($conn->connect_error) {
    throw new RuntimeException("Database Connection Failed: " . $conn->connect_error);
}

// Set character set to UTF-8
$conn->set_charset("utf8mb4");
