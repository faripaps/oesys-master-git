<?php
require_once __DIR__ . '/../includes/header.php';
require_role('student');

$stmt = $pdo->prepare("
    SELECT e.title, r.score, r.total_marks, r.passed, r.completed_at
    FROM results r
    JOIN exam_attempts a ON r.attempt_id = a.id
    JOIN exams e ON a.exam_id = e.id
    WHERE a.user_id = ? AND a.status = 'completed'
    ORDER BY r.completed_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$results = $stmt->fetchAll();
?>

<div class="card">
    <h2 class="card-title">Your Exam Results</h2>
    
    <?php if ($results): ?>
        <div class="table-responsive mt-4">
            <table>
                <thead>
                    <tr>
                        <th>Exam Title</th>
                        <th>Score</th>
                        <th>Status</th>
                        <th>Date Completed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result): ?>
                    <tr>
                        <td><strong><?= escape($result['title']) ?></strong></td>
                        <td><?= $result['score'] ?> / <?= $result['total_marks'] ?></td>
                        <td>
                            <?php if ($result['passed']): ?>
                                <span style="color:var(--secondary-color); font-weight:bold;">Passed</span>
                            <?php else: ?>
                                <span style="color:var(--danger-color); font-weight:bold;">Failed</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('M j, Y, g:i a', strtotime($result['completed_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted mt-4">You have not completed any exams yet.</p>
        <a href="<?= BASE_URL ?>/student/index.php" class="btn btn-primary mt-2">View Available Exams</a>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
