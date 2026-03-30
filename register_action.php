<?php
session_start();
include 'db.php'; // Finds db.php in the same folder

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Capture data from the form
    $user = trim($_POST['username']);
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    // 2. Security Check: Do passwords match?
    if ($pass !== $confirm_pass) {
        $_SESSION['msg'] = "Error: Passwords do not match!";
        header("Location: index.php");
        exit();
    }

    // 3. Security Check: Does username already exist?
    $check_user = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check_user->bind_param("s", $user);
    $check_user->execute();
    $result = $check_user->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['msg'] = "Error: Username is already taken!";
        header("Location: index.php");
        exit();
    }

    // 4. Secure the password (Hashing)
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

    // 5. Insert user using Prepared Statements (to prevent SQL Injection)
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $user, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['msg'] = "Success! Account created. You can now login.";
    } else {
        $_SESSION['msg'] = "Error: Database registration failed.";
    }

    // 6. Redirect back to index.php to show the message
    header("Location: index.php");
    exit();
}
?>