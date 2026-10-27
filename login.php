<?php
require_once __DIR__.'/includes/bootstrap.php';
if (!empty($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    csrf_check();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    try {
        $st = db()->prepare('SELECT id, username, password_hash FROM admins WHERE username = ? LIMIT 1');
        $st->execute([$username]); $admin = $st->fetch();
        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true); $_SESSION['admin_id']=(int)$admin['id']; $_SESSION['username']=$admin['username'];
            log_event('Successful admin login'); header('Location: index.php'); exit;
        }
        log_event('Failed admin login'); $error='Invalid username or password.';
    } catch (Throwable $ex) { error_log((string)$ex); $error='Unable to sign in. Check database setup.'; }
}
$page_title='Admin Login'; require __DIR__.'/includes/header.php';
?>
<section class="panel" style="max-width:440px;margin:50px auto"><h1>Administrator Login</h1><?php if($error): ?><p class="error"><?=e($error)?></p><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label>Username</label><input name="username" required autocomplete="username"><label>Password</label><input type="password" name="password" required autocomplete="current-password"><button type="submit">Sign in</button></form><p class="muted">For initial setup, run setup_admin.php once, then delete it.</p></section>
<?php require __DIR__.'/includes/footer.php'; ?>
