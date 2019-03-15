<?php

// 载入配置文件
require_once '../config.php';
//开始session
session_start();

function login(){
  global $mess_err;

  // 1. 接收并校验
  // 2. 持久化
  // 3. 响应 
   if (empty($_POST['email'])) {
    $mess_err = '请填写邮箱';
    return;
  }
  if (empty($_POST['password'])) {
    $mess_err = '请填写密码';
    return;
  }
  $email = $_POST['email'];
  $password = $_POST['password'];
// 当客户端提交过来的完整的表单信息就应该开始对其进行数据校验
  $conn = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
  mysqli_set_charset($conn,'utf8');
  if (!$conn) {
    exit('<h1>数据库连接失败</h1>');
  }
  $query = mysqli_query($conn,"select * from users where email = '{$email}' limit 1;");
  if (!$query) {
    $mess_err = "登录失败,请重试";
    return;
  }
  //获取用户名
  $user = mysqli_fetch_assoc($query);
  if (!$user) {
    $mess_err = '用户名不存在';
    return;
  }
   if ($user['password'] !== md5($password)) {
    $mess_err = '邮箱或者密码不匹配';
    return;
  }
  $_SESSION['login-info'] = $user;
  // $_SESSION['expiretime'] = time() + 3600; 
  header('Location: /admin/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  login();
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout' ) {
  unset($_SESSION['login-info']);
}
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
</head>
<body>
  <div class="login">
    <form class="login-wrap<?php echo isset($mess_err) ? ' shake animated' : '' ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off" novalidate> 
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong> 用户名或密码错误！
      </div> -->
      <?php if (isset($mess_err)): ?>
        <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $mess_err; ?>
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" type="email" name="email" class="form-control" placeholder="邮箱" autofocus>
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" type="password" name="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block" type="submit">登 录</button>
    </form>
  </div>
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script>
    $(function($){
      //单独作用域,确保页面加载后执行
        var reg = /^[0-9a-zA-Z]+[@][0-9a-zA-Z]+([.][a-zA-Z]+){1,2}$/;
        $("#email").on('blur',function(){
          var value = $(this).val();
          //空字符和不是邮箱直接返回
          if (!value || !reg.test(value)) return;
          $.get('/admin/api/avatar.php',{email:value,password:"matao",userId:5},function(res){
            if (!res) {
              $(".alert").html("<strong>错误！</strong>用户名不存在");
              $(".avatar").attr("src","/static/assets/img/default.png");
             return;
           }
            $(".alert").html("<strong>正确！</strong>用户名存在");
          // $('.avatar').fadeOut().attr('src', res).fadeIn()
            $('.avatar').fadeOut(function () {
            // 等到 淡出完成
            $(this).on('load', function () {
              // 图片完全加载成功过后
              $(this).fadeIn();
            }).attr('src', res);
          })
          })
        })
    })
  </script>
</body>
</html>
