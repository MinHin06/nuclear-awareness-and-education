<?php
session_start();

if (isset($_SESSION['theme']) && $_SESSION['theme'] === 'dark') {
    $_SESSION['theme'] = 'light';
} else {
    $_SESSION['theme'] = 'dark';
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
