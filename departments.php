<?php
require_once __DIR__.'/includes/bootstrap.php'; require_login(); csrf_check();
$meta = ['table'=>'departments','title'=>'Departments','fields'=>[
 ['name'=>'department_name','label'=>'Department Name','required'=>true,'max'=>150],
 ['name'=>'description','label'=>'Description','max'=>500]
]];
$table=$meta['table']; $title=$meta['title']; $fields=$meta['fields'];
$editId=(int)($_GET['edit'] ?? 0); $editing=null;
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action=(string)($_POST['action'] ?? '');
    try {
        if ($action==='delete') {
            $st=db()->prepare('DELETE FROM '.$table.' WHERE id=?'); $st->execute([(int)($_POST['id'] ?? 0)]);
            flash($title.' record deleted.'); log_event($title.' record deleted'); header('Location: departments.php'); exit;
        }
        if ($action==='save') {
            $values=[];
            foreach($fields as $f) {
                $v=trim((string)($_POST[$f['name']] ?? ''));
                if (!empty($f['required']) && $v==='') throw new RuntimeException($f['label'].' is required.');
                $values[]=$v==='' ? null : $v;
            }
            if (!empty($_POST['id'])) {
                $sets=implode(',',array_map(fn($f)=>$f['name'].'=?',$fields));
                $values[]=(int)$_POST['id']; $st=db()->prepare('UPDATE '.$table.' SET '.$sets.' WHERE id=?'); $st->execute($values);
                flash($title.' record updated.'); log_event($title.' record updated');
            } else {
                $cols=implode(',',array_column($fields,'name')); $marks=implode(',',array_fill(0,count($fields),'?'));
                $st=db()->prepare('INSERT INTO '.$table.' ('.$cols.') VALUES ('.$marks.')'); $st->execute($values);
                flash($title.' record added.'); log_event($title.' record added');
            }
            header('Location: departments.php'); exit;
        }
    } catch(Throwable $ex) { error_log((string)$ex); $error=$ex instanceof RuntimeException ? $ex->getMessage() : 'The operation failed. Check for duplicate values or related records.'; }
}
if($editId){$st=db()->prepare('SELECT * FROM '.$table.' WHERE id=?');$st->execute([$editId]);$editing=$st->fetch() ?: null;}
$rows=db()->query('SELECT * FROM '.$table.' ORDER BY id DESC')->fetchAll();
$page_title=$title; require __DIR__.'/includes/header.php';
?>
<h1><?=e($title)?></h1><section class="panel"><h2><?=$editing?'Edit':'Add'?> <?=e(rtrim($title,'s'))?></h2>
<?php if(!empty($error)): ?><p class="error"><?=e($error)?></p><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?=e($editing['id'] ?? '')?>">
<?php foreach($fields as $f): ?><label><?=e($f['label'])?></label><input name="<?=e($f['name'])?>" value="<?=e($editing[$f['name']] ?? '')?>" <?=!empty($f['required'])?'required':''?> maxlength="<?=e($f['max'] ?? 180)?>"><?php endforeach; ?>
<button type="submit">Save</button><?php if($editing): ?> <a class="button" href="departments.php">Cancel</a><?php endif; ?></form></section>
<section class="panel"><h2>All <?=e($title)?></h2><div style="overflow-x:auto"><table><thead><tr><th>ID</th><?php foreach($fields as $f): ?><th><?=e($f['label'])?></th><?php endforeach; ?><th>Actions</th></tr></thead><tbody>
<?php foreach($rows as $row): ?><tr><td><?=e($row['id'])?></td><?php foreach($fields as $f): ?><td><?=e($row[$f['name']] ?? '')?></td><?php endforeach; ?><td><a href="departments.php?edit=<?=e($row['id'])?>">Edit</a> <form class="inline" method="post" onsubmit="return confirm('Delete this record?')"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=e($row['id'])?>"><button class="danger" type="submit">Delete</button></form></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="<?=count($fields)+2?>">No records yet.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require __DIR__.'/includes/footer.php'; ?>
