<?php
// Load environment variables from .env
$env = parse_ini_file(__DIR__ . '/.env');

// Create the connection
$conn = new mysqli($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME']);

// Check connection
if ($conn->connect_error) {
    throw new RuntimeException("Database Connection Failed: " . $conn->connect_error);
}

// Set character set to UTF-8
$conn->set_charset("utf8mb4");
