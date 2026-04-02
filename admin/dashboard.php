<?php
require_once __DIR__ . '/../includes/header.php';
require_role('examiner');

// Fetch some basic stats
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'");
$studentsCount = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM exams");
$examsCount = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM exam_attempts");
$attemptsCount = $stmt->fetch()['count'];
?>

<div class="card">
    <h1 class="card-title">Examiner Dashboard</h1>
    <p>Welcome to the admin panel. Here is a quick overview of the system.</p>
</div>

<div class="grid-2 mt-4">
    <div class="card text-center">
        <h2><?= number_format($studentsCount) ?></h2>
        <p class="text-muted">Total Students</p>
    </div>
    
    <div class="card text-center">
        <h2><?= number_format($examsCount) ?></h2>
        <p class="text-muted">Total Exams</p>
        <a href="<?= BASE_URL ?>/admin/exams.php" class="btn btn-sm btn-outline mt-2">Manage Exams</a>
    </div>
    
    <div class="card text-center">
        <h2><?= number_format($attemptsCount) ?></h2>
        <p class="text-muted">Total Attempts</p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
