<?php
// We only check getenv() because Railway provides these directly.
// We provide defaults for local development (Laragon).

$host = getenv('DB_HOST') ?: '127.0.0.1';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: ''; 
$name = getenv('DB_NAME') ?: 'auth_system'; 
$port = (int)(getenv('DB_PORT') ?: 3306);

$conn = new mysqli($host, $user, $pass, $name, $port);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");