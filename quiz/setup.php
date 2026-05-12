<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create DB
    $pdo->exec("CREATE DATABASE IF NOT EXISTS quiz_app");
    $pdo->exec("USE quiz_app");
    
    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create questions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_text TEXT NOT NULL,
        option_a VARCHAR(255) NOT NULL,
        option_b VARCHAR(255) NOT NULL,
        option_c VARCHAR(255) NOT NULL,
        option_d VARCHAR(255) NOT NULL,
        correct_option CHAR(1) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create user_scores table
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_scores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        score INT NOT NULL,
        total_questions INT NOT NULL,
        completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    
    // Check if admin exists
    $stmt = $pdo->query("SELECT id FROM users WHERE username = 'admin'");
    if ($stmt->rowCount() == 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (username, email, password_hash, role) VALUES ('admin', 'admin@quiz.com', '$hash', 'admin')");
        echo "Admin user created (admin / admin123)\n";
    }

    echo "Database setup completed successfully.\n";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
