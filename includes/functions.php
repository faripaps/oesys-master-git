<?php
function escape($html) {
    if ($html === null) return '';
    return htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

function format_time($minutes) {
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    if ($hours > 0) {
        return "{$hours}h {$mins}m";
    }
    return "{$mins}m";
}
