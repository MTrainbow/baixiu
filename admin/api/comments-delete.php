<?php
require_once '../../functions.php' ;
if (empty($_GET['id'])) {
	exit('缺少相应参数');
}

$id = $_GET['id'];

$rows = xiu_execute('delete from comments where id in (' . $id . ');');

header('Content-type:applcation/json');

echo json_encode($rows>0);


