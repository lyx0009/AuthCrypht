<?php
/**
 * Database Connection for AuthCrypht
 * Works for both Local (Laragon) and Live (Railway)
 */

// 1. Get variables from Railway's environment
// If they don't exist, it uses the 'internal' Railway defaults as a fallback
$host = getenv('DB_HOST') ?: 'mysql.railway.internal';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'IKQCeEbFglXqPypkrgNcvCWQUpXusyiT';
$name = getenv('DB_NAME') ?: 'railway'; 
$port = (int)(getenv('DB_PORT') ?: 3306);

// 2. Create the connection
// We use the @ symbol to suppress the default PHP warning so we can handle it with our own error message
$conn = @new mysqli($host, $user, $pass, $name, $port);

// 3. Check connection
if ($conn->connect_error) {
    // If the internal connection fails, we show the exact error for debugging
    die("Database Connection Failed: " . $conn->connect_error . " (Host: $host, Port: $port)");
}

// 4. Set character set to UTF-8
$conn->set_charset("utf8mb4");

// Success! Your database is connected.awddwa
?>