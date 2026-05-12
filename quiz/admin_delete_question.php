<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['success'] = "Question deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete question.";
    }
}

redirect('admin_dashboard.php');
?>
