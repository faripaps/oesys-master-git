<?php
session_start();

function redirect($url) {
    header("Location: " . $url);
    exit;
}

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        redirect(BASE_URL . '/index.php');
    }
}

function require_role($role) {
    require_login();
    if ($_SESSION['role'] !== $role) {
        if ($_SESSION['role'] === 'examiner') {
            redirect(BASE_URL . '/admin/dashboard.php');
        } else {
            redirect(BASE_URL . '/student/index.php');
        }
    }
}

function loginUser($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
}

function logoutUser() {
    session_destroy();
    redirect(BASE_URL . '/index.php');
}
