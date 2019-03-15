<?php 
/**
 * 根据用户邮箱获取用户头像
 * email => image
 */
require_once '../../config.php';

if (empty($_GET['email'])) {
	exit('<h1>缺少相应参数</h1>');
}

$email = $_GET['email'];

$conn = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
mysqli_set_charset($conn,'utf8');
if (!$conn) {
	exit('连接数据库失败');
}
$query = mysqli_query($conn,"select avatar from users where email='{$email}' limit 1 ;");
if (!$query) {
	exit('查询失败');
}
$data = mysqli_fetch_assoc($query);

// $json = json_encode($data);
echo $data['avatar'];
// echo $json;