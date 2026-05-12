<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        redirect('register.php');
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Username or email already exists.";
        redirect('register.php');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $email, $hash])) {
        $_SESSION['success'] = "Registration successful! Please login.";
        redirect('login.php');
    } else {
        $_SESSION['error'] = "Something went wrong.";
        redirect('register.php');
    }
} else {
    redirect('register.php');
}
?>
