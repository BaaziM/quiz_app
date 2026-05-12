<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

$stmt = $pdo->query("SELECT * FROM questions ORDER BY created_at DESC");
$questions = $stmt->fetchAll();

$stmtUsers = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$totalUsers = $stmtUsers->fetch()['total'];

$stmtScores = $pdo->query("SELECT COUNT(*) as total FROM user_scores");
$totalScores = $stmtScores->fetch()['total'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - QuizApp</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <a href="admin_dashboard.php" class="nav-brand"><i class="fas fa-brain"></i> FahanAdmin</a>
        <div class="nav-links">
            <span style="color: var(--text-muted)">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="container fade-in">
        <div class="header-section">
            <h1>Dashboard Overview</h1>
        </div>
        
        <div class="dashboard-stats">
            <div class="glass-panel stat-card">
                <div class="stat-value"><?php echo count($questions); ?></div>
                <div class="stat-label">Total Questions</div>
            </div>
            <div class="glass-panel stat-card">
                <div class="stat-value"><?php echo $totalUsers; ?></div>
                <div class="stat-label">Registered Users</div>
            </div>
            <div class="glass-panel stat-card">
                <div class="stat-value"><?php echo $totalScores; ?></div>
                <div class="stat-label">Quizzes Taken</div>
            </div>
        </div>
        
        <div class="glass-panel" style="margin-top: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2>Manage Questions</h2>
                <a href="admin_add_question.php" class="btn" style="width: auto;"><i class="fas fa-plus"></i> Add Question</a>
            </div>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Correct Option</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($questions) > 0): ?>
                            <?php foreach($questions as $q): ?>
                            <tr>
                                <td><?php echo $q['id']; ?></td>
                                <td><?php echo htmlspecialchars(strlen($q['question_text']) > 50 ? substr($q['question_text'], 0, 50) . '...' : $q['question_text']); ?></td>
                                <td><?php echo strtoupper($q['correct_option']); ?></td>
                                <td>
                                    <a href="admin_edit_question.php?id=<?php echo $q['id']; ?>" class="action-btn action-edit"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="admin_delete_question.php?id=<?php echo $q['id']; ?>" class="action-btn action-delete" onclick="return confirm('Are you sure you want to delete this question?');"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted)">No questions found. Add some!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
