<?php
require_once __DIR__ . '/../includes/header.php';
require_role('examiner');

$exam_id = $_GET['exam_id'] ?? null;
if (!$exam_id) {
    redirect(BASE_URL . '/admin/exams.php');
}

$stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
$stmt->execute([$exam_id]);
$exam = $stmt->fetch();

if (!$exam) {
    redirect(BASE_URL . '/admin/exams.php');
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_question') {
        $question_text = trim($_POST['question_text'] ?? '');
        $opt_a = trim($_POST['opt_a'] ?? '');
        $opt_b = trim($_POST['opt_b'] ?? '');
        $opt_c = trim($_POST['opt_c'] ?? '');
        $opt_d = trim($_POST['opt_d'] ?? '');
        $correct_answer = trim($_POST['correct_answer'] ?? '');
        
        if ($question_text && $opt_a && $opt_b && $correct_answer) {
            $options = json_encode([
                'A' => $opt_a,
                'B' => $opt_b,
                'C' => $opt_c,
                'D' => $opt_d
            ]);
            
            $stmt = $pdo->prepare("INSERT INTO questions (exam_id, type, question_text, options_json, correct_answer) VALUES (?, 'mcq', ?, ?, ?)");
            if ($stmt->execute([$exam_id, $question_text, $options, $correct_answer])) {
                $success = "Question added successfully.";
            } else {
                $error = "Failed to add question.";
            }
        } else {
            $error = "Please fill in the question, at least options A and B, and select the correct answer.";
        }
    } elseif ($_POST['action'] === 'publish_exam') {
        $stmt = $pdo->prepare("UPDATE exams SET status = 'published' WHERE id = ?");
        $stmt->execute([$exam_id]);
        $success = "Exam has been published and is now available for students.";
        $exam['status'] = 'published'; // update local view
    }
}

$stmt = $pdo->prepare("SELECT * FROM questions WHERE exam_id = ?");
$stmt->execute([$exam_id]);
$questions = $stmt->fetchAll();
?>

<div class="card mb-4">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2 class="card-title">Questions for: <?= escape($exam['title']) ?></h2>
        <?php if ($exam['status'] === 'draft' && count($questions) > 0): ?>
            <form method="POST" action="" data-confirm="Are you sure you want to publish this exam?">
                <input type="hidden" name="action" value="publish_exam">
                <button type="submit" class="btn btn-sm btn-success">Publish Exam</button>
            </form>
        <?php elseif ($exam['status'] === 'published'): ?>
            <span style="color:var(--secondary-color); font-weight:bold;">Published</span>
        <?php endif; ?>
    </div>
    <a href="<?= BASE_URL ?>/admin/exams.php" class="btn btn-sm btn-outline mt-2">&larr; Back to Exams</a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= escape($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><?= escape($error) ?></div>
<?php endif; ?>

<div class="grid-2">
    <div class="card">
        <h3>Add MCQ Question</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_question">
            
            <div class="form-group">
                <label class="form-label" for="question_text">Question Details</label>
                <textarea id="question_text" name="question_text" class="form-control" rows="3" required></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Option A</label>
                <input type="text" name="opt_a" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Option B</label>
                <input type="text" name="opt_b" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Option C</label>
                <input type="text" name="opt_c" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Option D</label>
                <input type="text" name="opt_d" class="form-control">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="correct_answer">Correct Answer</label>
                <select name="correct_answer" id="correct_answer" class="form-control" required>
                    <option value="">Select correct option...</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Add Question</button>
        </form>
    </div>
    
    <div class="card">
        <h3>Existing Questions (<?= count($questions) ?>)</h3>
        <?php if ($questions): ?>
            <div style="max-height:600px; overflow-y:auto; padding-right:10px;">
                <?php foreach ($questions as $index => $q): ?>
                    <div class="question-box" style="margin-bottom:1rem; padding-bottom:1rem;">
                        <strong>Q<?= $index + 1 ?>:</strong> <?= escape($q['question_text']) ?>
                        <div class="mt-1 text-muted">
                            <small>
                                <?php 
                                $opts = json_decode($q['options_json'], true);
                                foreach ($opts as $k => $v) {
                                    if ($v) {
                                        echo "<strong>$k)</strong> " . escape($v) . " | ";
                                    }
                                }
                                ?>
                            </small>
                            <br>
                            <span style="color:var(--secondary-color);">Correct: <?= escape($q['correct_answer']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">No questions added yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
