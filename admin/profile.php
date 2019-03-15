<?php 
require_once '../functions.php';

$user = xiu_get_current_user();

$id = $user['id'];
/**
 * 更新个人信息
 * @return [type] [description]
 * select * from users where email = '{$email}' limit 1;
 */
function update () {
  global $mess_err;
  global $id;
  global $user;
//*****************************************************
 $data = xiu_fetch_all("select * from users where id = {$id};")[0];
 //************************************************************** 
  if (empty($_POST['avatar']) || empty($_POST['slug']) || empty($_POST['nickname']) || empty($_POST['textarea']) ) {
    $mess_err = '请正确填写表单数据';
  }
//*********************************************
$avatar = empty($_POST['avatar']) ? $data['avatar'] : $_POST['avatar'];
//***********************************************
$slug = empty($_POST['slug']) ? $user['slug'] : $_POST['slug'];
$nickname = empty($_POST['nickname']) ? $user['nickname'] : $_POST['nickname'];

$sql = sprintf("update users set slug ='%s', nickname ='%s', avatar = '%s' where id = %d",$slug,$nickname,$avatar,$id);

// $sql = sprintf("update categories set slug ='%s', name ='%s' where id = %d",$slug,$name,$id);
// $rows = xiu_execute("update users set avatar ='{$avatar}', email ='{$email}',slug ='{$slug}',nickname ='{$nickname}' where id = {$id}; ") 
// 
$rows = xiu_execute($sql);

$mess_err = $rows <= 0 ? '更新失败！' : '更新成功！';

$data = xiu_fetch_all("select * from users where id = {$id};")[0];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  update();
}
$data = xiu_fetch_all("select * from users where id = {$id};")[0];


?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'ins/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>我的个人资料</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <?php  // if (isset($mess_err)): ?>
        <div class="alert alert-danger">
        <strong><?php // echo $mess_err; ?></strong>
      </div>
      <?php // endif ?> -->
      <form class="form-horizontal" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" nctype = "multipart/form-data">
        <div class="form-group">
          <label class="col-sm-3 control-label">头像</label>
          <div class="col-sm-6">
            <label class="form-image">
              <input id="avatar" type="file">
              <img src="<?php echo $data['avatar'];?>">
              <input type="hidden" name="avatar" value="">
              <i class="mask fa fa-upload"></i>
            </label>
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-3 control-label">邮箱</label>
          <div class="col-sm-6">
            <input id="email" class="form-control" name="email" type="type" value="<?php echo $data['email'] ?>" placeholder="邮箱" readonly>
            <p class="help-block">登录邮箱不允许修改</p>
          </div>
        </div>
        <div class="form-group">
          <label for="slug" class="col-sm-3 control-label">别名</label>
          <div class="col-sm-6">
            <input id="slug" class="form-control" name="slug" type="type" value="<?php echo $data['slug'] ?>" placeholder="slug">
            <p class="help-block">https://zce.me/author/<strong>zce</strong></p>
          </div>
        </div>
        <div class="form-group">
          <label for="nickname" class="col-sm-3 control-label">昵称</label>
          <div class="col-sm-6">
            <input id="nickname" class="form-control" name="nickname" type="type" value="<?php echo $data['nickname'] ?>" placeholder="昵称">
            <p class="help-block">限制在 2-16 个字符</p>
          </div>
        </div>
        <div class="form-group">
          <label for="bio" class="col-sm-3 control-label">简介</label>
          <div class="col-sm-6">
            <textarea id="bio" name="textarea" class="form-control" placeholder="Bio" cols="30" rows="6">MAKE IT BETTER!</textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-6">
            <button type="submit" class="btn btn-primary">更新</button>
            <a class="btn btn-link" href="password-reset.php">修改密码</a>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  <?php $current_index = 'profile';?>
  <?php include 'ins/sidebar.php';?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  
  <script>
    $('#avatar').on('change',function () {
      var $this = $(this);
      var files = $this.prop('files');
      if (!files.length) return ;
      var file = files[0];
      //H5新增配合ajax上传二进制文件使用
      var data = new FormData();
      data.append('avatar',file);
      var xhr = new XMLHttpRequest();
      xhr.open('POST','/admin/api/upload.php');
      xhr.send(data);
      xhr.onload = function () {
        $this.siblings('img').attr('src',this.responseText);
        $this.siblings('input').val(this.responseText);
      }
    })
  </script>

  <script>NProgress.done()</script>
</body>
</html>
