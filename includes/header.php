<?php
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

// Avoid outputting header multiple times if included repeatedly (though require_once handles this, but good to be safe)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape(SITE_NAME) ?></title>
    <!-- Use Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Local CSS -->
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="<?= BASE_URL ?>/index.php" class="logo"><?= escape(SITE_NAME) ?></a>
            </div>
            <div class="nav-menu">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="nav-welcome">Hi, <?= escape($_SESSION['username']) ?></span>
                    <?php if ($_SESSION['role'] === 'examiner'): ?>
                        <a href="<?= BASE_URL ?>/admin/dashboard.php">Dashboard</a>
                        <a href="<?= BASE_URL ?>/admin/exams.php">Exams</a>
                        <a href="<?= BASE_URL ?>/admin/users.php">Users</a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/student/index.php">Dashboard</a>
                        <a href="<?= BASE_URL ?>/student/results.php">Results</a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/logout.php" class="btn btn-sm btn-danger ml-2">Logout</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/index.php">Login</a>
                    <a href="<?= BASE_URL ?>/register.php" class="btn btn-sm btn-primary ml-2">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="main-content">
