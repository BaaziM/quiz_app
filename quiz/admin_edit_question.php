<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    redirect('admin_dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_text = trim($_POST['question_text']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $option_d = trim($_POST['option_d']);
    $correct_option = strtolower(trim($_POST['correct_option']));

    if (empty($question_text) || empty($option_a) || empty($option_b) || empty($option_c) || empty($option_d) || empty($correct_option)) {
        $_SESSION['error'] = "All fields are required.";
    } elseif (!in_array($correct_option, ['a', 'b', 'c', 'd'])) {
        $_SESSION['error'] = "Correct option must be A, B, C, or D.";
    } else {
        $stmt = $pdo->prepare("UPDATE questions SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ? WHERE id = ?");
        if ($stmt->execute([$question_text, $option_a, $option_b, $option_c, $option_d, $correct_option, $id])) {
            $_SESSION['success'] = "Question updated successfully!";
            redirect('admin_dashboard.php');
        } else {
            $_SESSION['error'] = "Failed to update question.";
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->execute([$id]);
$question = $stmt->fetch();

if (!$question) {
    redirect('admin_dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Question - QuizApp</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <a href="admin_dashboard.php" class="nav-brand"><i class="fas fa-brain"></i> QuizAdmin</a>
        <div class="nav-links">
            <a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="container fade-in">
        <div class="glass-panel" style="max-width: 800px; margin: 0 auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2>Edit Question</h2>
                <a href="admin_dashboard.php" class="btn btn-outline" style="width: auto; color: var(--text-main);"><i class="fas fa-arrow-left"></i> Back</a>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="admin_edit_question.php?id=<?php echo $id; ?>" method="POST">
                <div class="form-group">
                    <label for="question_text">Question Text</label>
                    <textarea id="question_text" name="question_text" class="form-control" rows="4" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="option_a">Option A</label>
                    <input type="text" id="option_a" name="option_a" class="form-control" value="<?php echo htmlspecialchars($question['option_a']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="option_b">Option B</label>
                    <input type="text" id="option_b" name="option_b" class="form-control" value="<?php echo htmlspecialchars($question['option_b']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="option_c">Option C</label>
                    <input type="text" id="option_c" name="option_c" class="form-control" value="<?php echo htmlspecialchars($question['option_c']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="option_d">Option D</label>
                    <input type="text" id="option_d" name="option_d" class="form-control" value="<?php echo htmlspecialchars($question['option_d']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="correct_option">Correct Option</label>
                    <select id="correct_option" name="correct_option" class="form-control" required style="appearance: auto;">
                        <option value="a" <?php if($question['correct_option'] === 'a') echo 'selected'; ?>>A</option>
                        <option value="b" <?php if($question['correct_option'] === 'b') echo 'selected'; ?>>B</option>
                        <option value="c" <?php if($question['correct_option'] === 'c') echo 'selected'; ?>>C</option>
                        <option value="d" <?php if($question['correct_option'] === 'd') echo 'selected'; ?>>D</option>
                    </select>
                </div>
                
                <button type="submit" class="btn" style="margin-top: 1rem;">Update Question</button>
            </form>
        </div>
    </div>
</body>
</html>
