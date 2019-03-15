<?php
require_once '../functions.php' ;
if (empty($_GET['id'])) {
	exit('缺少相应参数');
}

$id = $_GET['id'];

// string(99) "1070,1069,1068,1067,1066,1065,1064,1063,1062,1061,1060,1059,1058,1057,1056"

$rows = xiu_execute('delete from posts where id in (' . $id . ');');

// $rows = xiu_execute("delete from posts where id in ({$id});");

header('location: /admin/posts.php');


