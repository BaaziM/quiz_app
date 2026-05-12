<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Get user scores
$stmt = $pdo->prepare("SELECT * FROM user_scores WHERE user_id = ? ORDER BY completed_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$scores = $stmt->fetchAll();

// Get highest score
$stmtHigh = $pdo->prepare("SELECT MAX(score) as high_score, total_questions FROM user_scores WHERE user_id = ? GROUP BY total_questions");
$stmtHigh->execute([$user_id]);
$highScoreData = $stmtHigh->fetch();

$highScore = $highScoreData ? $highScoreData['high_score'] . '/' . $highScoreData['total_questions'] : 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - QuizApp</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="nav-brand"><i class="fas fa-bolt"></i> Fahan</a>
        <div class="nav-links">
            <span style="color: var(--text-muted)">Hi, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <?php if(isAdmin()): ?>
                <a href="admin_dashboard.php"><i class="fas fa-shield-alt"></i> Admin</a>
            <?php endif; ?>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="container fade-in" id="dashboard-section">
        <div class="header-section">
            <h1>Ready to test your knowledge?</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Take the quiz and see how you score.</p>
        </div>

        <div style="display: flex; justify-content: center; margin-bottom: 4rem;">
            <button id="start-quiz-btn" class="btn" style="width: auto; padding: 1rem 3rem; font-size: 1.2rem; border-radius: 50px;">
                Start Quiz <i class="fas fa-play" style="margin-left: 0.5rem;"></i>
            </button>
        </div>

        <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
            <div class="glass-panel stat-card" style="flex: 1; min-width: 250px;">
                <div class="stat-value"><?php echo count($scores); ?></div>
                <div class="stat-label">Quizzes Taken</div>
            </div>
            <div class="glass-panel stat-card" style="flex: 1; min-width: 250px;">
                <div class="stat-value"><?php echo $highScore; ?></div>
                <div class="stat-label">Highest Score</div>
            </div>
        </div>

        <?php if(count($scores) > 0): ?>
        <div class="glass-panel" style="margin-top: 2rem;">
            <h3>Recent Activity</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Score</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($scores as $s): ?>
                    <tr>
                        <td><?php echo date('M j, Y g:i A', strtotime($s['completed_at'])); ?></td>
                        <td><span style="font-weight: bold; color: var(--primary)"><?php echo $s['score']; ?></span> / <?php echo $s['total_questions']; ?></td>
                        <td>
                            <?php 
                                $percent = round(($s['score'] / $s['total_questions']) * 100);
                                $color = $percent >= 80 ? 'var(--success)' : ($percent >= 50 ? '#fbbf24' : 'var(--error)');
                            ?>
                            <span style="color: <?php echo $color; ?>"><?php echo $percent; ?>%</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <div class="quiz-container" id="quiz-section">
        <!-- Injected by JS -->
    </div>

    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
