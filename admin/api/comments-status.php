<?php
require_once '../../functions.php';

header('Content-Type:application/json');

if (empty($_GET['id']) && empty($_POST['status'])) {
	exit('<h1>缺少相应参数</h1>');
}

$rows = xiu_execute("update comments set status ='{$_POST['status']}' where id in({$_GET['id']});");


echo json_encode($rows>0);