<?php
require_once '../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $score = isset($data['score']) ? intval($data['score']) : 0;
    $total = isset($data['total']) ? intval($data['total']) : 0;
    $user_id = $_SESSION['user_id'];

    if ($total > 0) {
        $stmt = $pdo->prepare("INSERT INTO user_scores (user_id, score, total_questions) VALUES (?, ?, ?)");
        if ($stmt->execute([$user_id, $score, $total])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save score']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
