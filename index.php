<?php
require_once __DIR__.'/includes/bootstrap.php'; require_login();
$counts=[];
foreach (['departments'=>'Departments','courses'=>'Courses','students'=>'Students','student_academics'=>'Academic Records'] as $table=>$label) {
    try { $counts[$label]=(int)db()->query('SELECT COUNT(*) FROM '.$table)->fetchColumn(); }
    catch(Throwable $ex) { error_log((string)$ex); $counts[$label]='—'; }
}
$page_title='Dashboard'; require __DIR__.'/includes/header.php';
?>
<h1>Dashboard</h1><p>Welcome, <?=e($_SESSION['username'] ?? 'Administrator')?>.</p><div class="grid"><?php foreach($counts as $label=>$count): ?><section class="card"><div><?=e($label)?></div><div class="count"><?=e($count)?></div></section><?php endforeach; ?></div><section class="panel"><h2>Quick links</h2><p><a class="button" href="departments.php">Manage Departments</a> <a class="button" href="courses.php">Manage Courses</a> <a class="button" href="students.php">Manage Students</a> <a class="button" href="academics.php">Academic History</a></p></section>
<?php require __DIR__.'/includes/footer.php'; ?>
