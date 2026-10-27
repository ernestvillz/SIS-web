<?php
require_once __DIR__.'/includes/bootstrap.php'; require_login(); csrf_check();
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 try{
  if(($_POST['action']??'')==='delete'){ $s=db()->prepare('DELETE FROM courses WHERE id=?');$s->execute([(int)$_POST['id']]);flash('Course deleted.');log_event('Course deleted');header('Location: courses.php');exit;}
  if(($_POST['action']??'')==='save'){
   $id=(int)($_POST['id']??0);$dep=(int)($_POST['department_id']??0);$code=trim((string)($_POST['course_code']??''));$name=trim((string)($_POST['course_name']??''));$desc=trim((string)($_POST['description']??''));
   if(!$dep||$code===''||$name==='')throw new RuntimeException('Department, course code, and course name are required.');
   if($id){$s=db()->prepare('UPDATE courses SET department_id=?,course_code=?,course_name=?,description=? WHERE id=?');$s->execute([$dep,$code,$name,$desc?:null,$id]);}else{$s=db()->prepare('INSERT INTO courses(department_id,course_code,course_name,description) VALUES(?,?,?,?)');$s->execute([$dep,$code,$name,$desc?:null]);}
   flash('Course saved.');log_event('Course saved');header('Location: courses.php');exit;
  }
 }catch(Throwable $ex){error_log((string)$ex);$error=$ex instanceof RuntimeException?$ex->getMessage():'Could not save course. Check duplicate course codes and department selection.';}
}
$edit=null;if(isset($_GET['edit'])){$s=db()->prepare('SELECT * FROM courses WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$departments=db()->query('SELECT id,department_name FROM departments ORDER BY department_name')->fetchAll();
$rows=db()->query('SELECT c.*,d.department_name FROM courses c JOIN departments d ON d.id=c.department_id ORDER BY c.id DESC')->fetchAll();
$page_title='Courses';require __DIR__.'/includes/header.php';?>
<h1>Course Management</h1><section class="panel"><h2><?= $edit?'Edit':'Add' ?> Course</h2><?php if($error):?><p class="error"><?=e($error)?></p><?php endif;?>
<?php if(!$departments):?><p>Add a department before adding courses.</p><?php else:?><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?=e($edit['id']??'')?>"><label>Department</label><select name="department_id" required><?php foreach($departments as $d):?><option value="<?=e($d['id'])?>" <?=((int)($edit['department_id']??0)===(int)$d['id'])?'selected':''?>><?=e($d['department_name'])?></option><?php endforeach;?></select><label>Course Code</label><input name="course_code" required maxlength="30" value="<?=e($edit['course_code']??'')?>"><label>Course Name</label><input name="course_name" required maxlength="180" value="<?=e($edit['course_name']??'')?>"><label>Description</label><input name="description" maxlength="500" value="<?=e($edit['description']??'')?>"><button>Save Course</button></form><?php endif;?></section>
<section class="panel"><h2>All Courses</h2><div style="overflow-x:auto"><table><tr><th>ID</th><th>Department</th><th>Code</th><th>Course</th><th>Description</th><th>Actions</th></tr><?php foreach($rows as $r):?><tr><td><?=e($r['id'])?></td><td><?=e($r['department_name'])?></td><td><?=e($r['course_code'])?></td><td><?=e($r['course_name'])?></td><td><?=e($r['description'])?></td><td><a href="?edit=<?=e($r['id'])?>">Edit</a> <form class="inline" method="post" onsubmit="return confirm('Delete course?')"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=e($r['id'])?>"><button class="danger">Delete</button></form></td></tr><?php endforeach;?></table></div></section><?php require __DIR__.'/includes/footer.php';?>