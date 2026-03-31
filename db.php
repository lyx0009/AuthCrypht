<?php
// DB Connection for AuthCrypht
// Internal Railway settings
$host = getenv('DB_HOST') ?: 'mysql.railway.internal';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'IKQCeEbFglXqPypkrgNcvCWQUpXusyiT';
$name = getenv('DB_NAME') ?: 'railway'; 
$port = (int)(getenv('DB_PORT') ?: 3306);

// Create connection
$conn = @new mysqli($host, $user, $pass, $name, $port);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>