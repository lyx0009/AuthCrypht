<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($pass, $row['password'])) {
            $_SESSION['user_id']  = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role']     = $row['role'];

            // Update last_login and set active
            $upd = $conn->prepare("UPDATE users SET last_login = NOW(), is_active = 1 WHERE id = ?");
            $upd->bind_param("i", $row['id']);
            $upd->execute();

            header("Location: " . ($row['role'] === 'admin' ? "admin_dashboard.php" : "dashboard.php"));
            exit();
        } else {
            $_SESSION['msg'] = "Error: Invalid password.";
        }
    } else {
        $_SESSION['msg'] = "Error: Username not found.";
    }

    header("Location: index.php");
    exit();
}
?>
