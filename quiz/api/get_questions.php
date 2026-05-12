<?php
require_once '../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

try {
    // Fetch all questions, randomize order
    $stmt = $pdo->query("SELECT id, question_text, option_a, option_b, option_c, option_d, correct_option FROM questions ORDER BY RAND() LIMIT 20");
    $questions = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'questions' => $questions
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch questions.']);
}
?>
