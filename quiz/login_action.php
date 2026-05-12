<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        redirect('login.php');
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        if ($user['role'] === 'admin') {
            redirect('admin_dashboard.php');
        } else {
            redirect('index.php');
        }
    } else {
        $_SESSION['error'] = "Invalid username or password.";
        redirect('login.php');
    }
} else {
    redirect('login.php');
}
?>
