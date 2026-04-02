<?php
require_once __DIR__ . '/../config/settings.php';
session_start();

function redirect($url) {
    header("Location: " . $url);
    exit;
}

redirect(BASE_URL . '/admin/dashboard.php');
