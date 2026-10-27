<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params(['httponly'=>true, 'secure'=>!empty($_SERVER['HTTPS']), 'samesite'=>'Lax']);
    session_start();
}
require_once __DIR__ . '/../config/database.php';
function e(mixed $v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_check(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sent = $_POST['csrf'] ?? '';
        if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
            http_response_code(403); exit('Invalid request token. Refresh and try again.');
        }
    }
}
function require_login(): void {
    if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
}
function log_event(string $event): void {
    $line = date('c') . ' ' . preg_replace('/[^a-zA-Z0-9 _.-]/', '', $event) . PHP_EOL;
    error_log($line, 3, __DIR__ . '/../logs/app.log');
}
function flash(string $message): void { $_SESSION['flash'] = $message; }
function show_flash(): void {
    if (!empty($_SESSION['flash'])) { echo '<p class="notice">'.e($_SESSION['flash']).'</p>'; unset($_SESSION['flash']); }
}
