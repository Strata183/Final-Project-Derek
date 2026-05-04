<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function is_logged_in(): bool {
    return !empty($_SESSION['UserID']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header("Location: login.html");
        exit;
    }
}

function current_user_id(): ?int {
    return isset($_SESSION['UserID']) ? (int)$_SESSION['UserID'] : null;
}

function current_username(): ?string {
    return $_SESSION['Username'] ?? null;
}

function is_admin(): bool {
    return !empty($_SESSION['Admin']);
}