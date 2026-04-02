<?php
require_once __DIR__ . '/../includes/header.php';
require_role('student');

$exam_id = $_GET['id'] ?? null;
if (!$exam_id) redirect(BASE_URL . '/student/index.php');

$stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ? AND status = 'published'");
$stmt->execute([$exam_id]);
$exam = $stmt->fetch();

if (!$exam) redirect(BASE_URL . '/student/index.php');

$user_id = $_SESSION['user_id'];

// Check for existing attempt
$stmt = $pdo->prepare("SELECT * FROM exam_attempts WHERE exam_id = ? AND user_id = ?");
$stmt->execute([$exam_id, $user_id]);
$attempt = $stmt->fetch();

$current_time = new DateTime();

if ($attempt && $attempt['status'] === 'completed') {
    // Already taken
    redirect(BASE_URL . '/student/results.php');
}

if (!$attempt) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_exam'])) {
        // Start attempt
        $start = new DateTime();
        $end = clone $start;
        $end->modify("+{$exam['time_limit']} minutes");
        
        $stmt = $pdo->prepare("INSERT INTO exam_attempts (exam_id, user_id, start_time, end_time, status) VALUES (?, ?, ?, ?, 'in_progress')");
        $stmt->execute([
            $exam_id, 
            $user_id, 
            $start->format('Y-m-d H:i:s'), 
            $end->format('Y-m-d H:i:s')
        ]);
        
        // Refresh page to load questions
        redirect(BASE_URL . "/student/exam.php?id=$exam_id");
    } else {
        // Show start page
        ?>
        <div class="card auth-container text-center">
            <h2 class="card-title"><?= escape($exam['title']) ?></h2>
            <p class="mb-4">Time Limit: <strong><?= format_time($exam['time_limit']) ?></strong></p>
            <div class="alert alert-warning text-left">
                <strong>Attention:</strong>
                <ul class="mt-1" style="margin-left: 1.5rem;">
                    <li>Do not refresh the page or navigate away once the exam starts.</li>
                    <li>The exam will auto-submit when the timer reaches zero.</li>
                </ul>
            </div>
            <form method="POST">
                <input type="hidden" name="start_exam" value="1">
                <button type="submit" class="btn btn-primary btn-block">Start Exam Now</button>
            </form>
        </div>
        <?php
        require_once __DIR__ . '/../includes/footer.php';
        exit;
    }
}

// If we are here, we have an in-progress attempt.
$end_time = new DateTime($attempt['end_time']);
if ($current_time >= $end_time && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Attempt expired but wasn't submitted yet, force submit logic via JS or redirect
    // We can auto evaluate it with 0 score or whatever was saved. 
    // For simplicity, handle it during POST or force a POST below.
}

// Fetch all questions
$stmt = $pdo->prepare("SELECT * FROM questions WHERE exam_id = ?");
$stmt->execute([$exam_id]);
$questions = $stmt->fetchAll();

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_exam'])) {
    $total_score = 0;
    $total_marks = 0;
    
    foreach ($questions as $q) {
        $q_id = $q['id'];
        $total_marks += $q['marks'];
        $given = $_POST['q_' . $q_id] ?? null;
        
        $is_correct = false;
        $awarded = 0;
        
        if ($given === $q['correct_answer']) {
            $is_correct = true;
            $awarded = $q['marks'];
            $total_score += $awarded;
        }
        
        // Save answer
        $astmt = $pdo->prepare("INSERT INTO student_answers (attempt_id, question_id, given_answer, is_correct, marks_awarded) VALUES (?, ?, ?, ?, ?)");
        $astmt->execute([$attempt['id'], $q_id, $given, $is_correct ? 1 : 0, $awarded]);
    }
    
    // Calculate passing
    $percentage = ($total_marks > 0) ? ($total_score / $total_marks) * 100 : 0;
    $passed = $percentage >= PASSING_PERCENTAGE ? 1 : 0;
    
    // Save results
    $rstmt = $pdo->prepare("INSERT INTO results (attempt_id, score, total_marks, passed) VALUES (?, ?, ?, ?)");
    $rstmt->execute([$attempt['id'], $total_score, $total_marks, $passed]);
    
    // Complete attempt
    $cstmt = $pdo->prepare("UPDATE exam_attempts SET status = 'completed' WHERE id = ?");
    $cstmt->execute([$attempt['id']]);
    
    redirect(BASE_URL . '/student/results.php');
}

// Remaining JS time in seconds
$remaining_seconds = $end_time->getTimestamp() - $current_time->getTimestamp();
// Let's protect against negative if they just missed the submit boundary
if ($remaining_seconds < 0) $remaining_seconds = 0;
?>

<div class="exam-header mb-4">
    <h2 style="margin:0; font-size:1.25rem;"><?= escape($exam['title']) ?></h2>
    <div class="timer" id="exam-timer" data-seconds="<?= $remaining_seconds ?>">
        Loading...
    </div>
</div>

<form method="POST" id="exam-form" class="card">
    <input type="hidden" name="submit_exam" value="1">
    
    <?php foreach ($questions as $index => $q): ?>
        <div class="question-box">
            <div class="question-text"><?= ($index + 1) ?>. <?= escape($q['question_text']) ?></div>
            <?php if ($q['type'] === 'mcq'): 
                $options = json_decode($q['options_json'], true);
                foreach ($options as $key => $val):
                    if (!$val) continue;
            ?>
                <label class="option-label">
                    <input type="radio" name="q_<?= $q['id'] ?>" value="<?= escape($key) ?>">
                    <span><?= escape($key) ?>) <?= escape($val) ?></span>
                </label>
            <?php 
                endforeach;
            endif; ?>
        </div>
    <?php endforeach; ?>
    
    <div class="mt-4" style="text-align: right;">
        <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to completely submit the exam?');">Submit Exam</button>
    </div>
</form>

<script src="<?= BASE_URL ?>/assets/js/exam.js"></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
