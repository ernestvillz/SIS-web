<?php
require_once __DIR__.'/includes/bootstrap.php';
$message='';
try {
    $n=(int)db()->query('SELECT COUNT(*) FROM admins')->fetchColumn();
    if ($n===0) {
        $st=db()->prepare('INSERT INTO admins (username,password_hash) VALUES (?,?)');
        $st->execute(['admin',password_hash('admin123', PASSWORD_DEFAULT)]);
        $message='Admin created. Username: admin. Temporary password: admin123. DELETE setup_admin.php NOW and change the password before public use.';
    } else { $message='An admin account already exists. No changes made. Delete setup_admin.php now.'; }
} catch(Throwable $ex) { http_response_code(500); error_log((string)$ex); $message='Setup failed. Confirm database configuration and import database/schema.sql.'; }
?><!doctype html><html><head><meta charset="utf-8"><title>One-time Admin Setup</title></head><body><h1>One-time Admin Setup</h1><p><?=htmlspecialchars($message,ENT_QUOTES,'UTF-8')?></p></body></html>
