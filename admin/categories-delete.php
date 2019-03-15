<?php 

require_once '../functions.php';

if (empty($_GET['id'])) {
	exit('缺少相应参数');
}

$id = $_GET['id'];

$rows = xiu_execute('delete from categories where id in (' . $id . ');');

header('location: /admin/categories.php');
