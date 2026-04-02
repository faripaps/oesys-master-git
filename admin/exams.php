<?php
require_once __DIR__ . '/../includes/header.php';
require_role('examiner');

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create_exam') {
        $title = trim($_POST['title'] ?? '');
        $time_limit = (int)($_POST['time_limit'] ?? 60);

        if ($title && $time_limit > 0) {
            $stmt = $pdo->prepare("INSERT INTO exams (title, time_limit, created_by) VALUES (?, ?, ?)");
            if ($stmt->execute([$title, $time_limit, $_SESSION['user_id']])) {
                $success = "Exam created successfully.";
            } else {
                $error = "Warning: Failed to create exam.";
            }
        } else {
            $error = "Please fill in valid exam details.";
        }
    }
}

$stmt = $pdo->query("SELECT * FROM exams ORDER BY created_at DESC");
$exams = $stmt->fetchAll();
?>

<div class="card">
    <h2 class="card-title">Manage Exams</h2>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= escape($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= escape($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="mb-4">
        <input type="hidden" name="action" value="create_exam">
        <h3>Create New Exam</h3>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label" for="title">Exam Title</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="time_limit">Time Limit (minutes)</label>
                <input type="number" id="time_limit" name="time_limit" class="form-control" value="60" min="1" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Create Exam</button>
    </form>

    <h3>Existing Exams</h3>
    <?php if ($exams): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Time Limit</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exams as $exam): ?>
                    <tr>
                        <td><?= $exam['id'] ?></td>
                        <td><?= escape($exam['title']) ?></td>
                        <td><?= $exam['time_limit'] ?> mins</td>
                        <td><span class="badge"><?= escape($exam['status']) ?></span></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/questions.php?exam_id=<?= $exam['id'] ?>" class="btn btn-sm btn-outline">Manage Questions</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">No exams created yet.</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
