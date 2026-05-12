<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - QuizApp</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="glass-panel auth-form">
            <h2>Welcome Back</h2>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="login_action.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required placeholder="Enter your username">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="Enter your password">
                </div>
                
                <button type="submit" class="btn">Login</button>
            </form>
            
            <div class="auth-links">
                Don't have an account? <a href="register.php">Sign up</a>
            </div>
        </div>
    </div>
</body>
</html>
