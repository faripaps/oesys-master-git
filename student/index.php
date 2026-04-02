<?php
require_once __DIR__ . '/../includes/header.php';
require_role('student');

// Get available published exams that the student hasn't completed yet
$stmt = $pdo->prepare("
    SELECT e.* 
    FROM exams e
    WHERE e.status = 'published'
    AND e.id NOT IN (
        SELECT exam_id FROM exam_attempts WHERE user_id = ? AND status = 'completed'
    )
    ORDER BY e.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$available_exams = $stmt->fetchAll();

// Get recently completed exams
$stmt = $pdo->prepare("
    SELECT e.title, r.score, r.total_marks, r.passed, r.completed_at
    FROM results r
    JOIN exam_attempts a ON r.attempt_id = a.id
    JOIN exams e ON a.exam_id = e.id
    WHERE a.user_id = ? AND a.status = 'completed'
    ORDER BY r.completed_at DESC
    LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$recent_results = $stmt->fetchAll();
?>

<div class="card mb-4">
    <h1 class="card-title">Student Dashboard</h1>
    <p>Welcome back, <?= escape($_SESSION['username']) ?>! Here are your available exams and recent activity.</p>
</div>

<div class="grid-2">
    <div class="card">
        <h3>Available Exams</h3>
        <?php if ($available_exams): ?>
            <ul style="list-style:none; padding:0; margin-top:1rem;">
                <?php foreach ($available_exams as $exam): ?>
                    <li style="padding:1rem; border:1px solid var(--border-color); border-radius:var(--radius); margin-bottom:1rem; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <strong><?= escape($exam['title']) ?></strong><br>
                            <small class="text-muted"><?= format_time($exam['time_limit']) ?></small>
                        </div>
                        <a href="<?= BASE_URL ?>/student/exam.php?id=<?= $exam['id'] ?>" class="btn btn-sm btn-primary">Take Exam</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted mt-2">No exams available for you right now.</p>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h3>Recent Results</h3>
        <?php if ($recent_results): ?>
            <ul style="list-style:none; padding:0; margin-top:1rem;">
                <?php foreach ($recent_results as $result): ?>
                    <li style="padding:1rem; border:1px solid var(--border-color); border-radius:var(--radius); margin-bottom:1rem;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
                            <strong><?= escape($result['title']) ?></strong>
                            <?php if ($result['passed']): ?>
                                <span style="color:var(--secondary-color); font-weight:bold;">Passed</span>
                            <?php else: ?>
                                <span style="color:var(--danger-color); font-weight:bold;">Failed</span>
                            <?php endif; ?>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:0.9rem;">
                            <span class="text-muted">Score: <?= $result['score'] ?> / <?= $result['total_marks'] ?></span>
                            <span class="text-muted"><?= date('M j, Y', strtotime($result['completed_at'])) ?></span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a href="<?= BASE_URL ?>/student/results.php" class="btn btn-sm btn-outline mt-2">View All Results</a>
        <?php else: ?>
            <p class="text-muted mt-2">You haven't completed any exams yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
