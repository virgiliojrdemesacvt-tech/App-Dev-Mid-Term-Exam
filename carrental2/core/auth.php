<?php
// Login / session helpers. Load AFTER dbConfig.php and models.php.
require_once __DIR__ . '/models.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect helper that works from the root folder AND from the core/ folder
function redirectTo($page) {
    $inCore = basename(dirname($_SERVER['SCRIPT_NAME'])) === 'core';
    header("Location: " . ($inCore ? "../" : "") . $page);
    exit;
}

function isLoggedIn() {
    return !empty($_SESSION['user_id']);
}

// Put this at the top of every protected page: not logged in -> login.php
function requireLogin() {
    if (!isLoggedIn()) {
        redirectTo('login.php');
    }
}

function currentUserId() {
    return (int)($_SESSION['user_id'] ?? 0);
}

// The "Logged in as ... [Logout]" bar shown on every protected page
function renderUserBar() {
    echo '<div class="userbar"><span>Logged in as <b>' . e($_SESSION['full_name'] ?? '') . '</b> (@'
         . e($_SESSION['username'] ?? '') . ')</span> <a class="logout" href="logout.php">Logout</a></div>';
}

// One-time messages (errors / success) shown after a redirect
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function showFlash() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert ' . e($f['type']) . '">' . e($f['message']) . '</div>';
    }
}
