<?php

require_once '../../functions.php';

$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];

$length = 20;
$skipNum = ($page - 1) * $length; //越过的页码

//总条数
$total_Num = xiu_fetch_one('select count(1) as count
from comments
inner join posts on comments.id = posts.id 
')['count'];
//总页码
$total_page = (int)ceil($total_Num/$length);

$comments = xiu_fetch_all("select 
comments.*,
posts.title as post_title
from comments
inner join posts on comments.id = posts.id
order by comments.created desc
limit {$skipNum},{$length} 
");

header('Content-type:applcation/json');

$array = array(
	'data' => $comments,
	'total_page' => $total_page
);

echo json_encode($array);
