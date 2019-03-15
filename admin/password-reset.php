<?php 
require_once '../functions.php';

//---第一次登录数据sessionID
$user = xiu_get_current_user();  
/**
 * [修改密码]
 * @return [boolean] [description]
 */
function change_psw () {
  global $mess_err;
  global $user;

  if (empty($_POST['password']) || empty($_POST['new_psw']) || empty($_POST['conf_psw'])) {
      $mess_err = '请正确填写表单数据';
      return;
  } 

  $password = $_POST['password'];
  $new_psw = $_POST['new_psw'];
  $conf_psw = $_POST['conf_psw'];
//*************************************************
  $data = xiu_fetch_one("select * from users where id = {$user['id']} ;");
//***************************************************
  if($data['password'] !== md5($password)){
    $mess_err = '密码错误,请重新输入';
    return;
  }

  if (md5($new_psw) !== md5($conf_psw)) {
    $mess_err = '密码不一致,请重新输入';
    return; 
  }

  $sql = sprintf("update users set password = '%s' where id = %d ",md5($new_psw),$user['id']);
  $row = xiu_execute($sql);

  $mess_err = $row < 0 ? "更新失败" : "更新成功";   
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    change_psw();
}

 ?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Password reset &laquo; Admin</title>
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
        <h1>修改密码</h1>
      </div>
      <?php if (isset($mess_err)): ?>
        <!-- 有错误信息时展示 -->
      <div class="alert alert-danger">
        <strong><?php echo $mess_err ?></strong>
      </div>
      <?php endif ?>
      <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
        <div class="form-group">
          <label for="old" class="col-sm-3 control-label">旧密码</label>
          <div class="col-sm-7">
            <input id="old" class="form-control" name="password" type="password" placeholder="旧密码">
          </div>
        </div>
        <div class="form-group">
          <label for="password" class="col-sm-3 control-label">新密码</label>
          <div class="col-sm-7">
            <input id="password" class="form-control" name="new_psw" type="password" placeholder="新密码">
          </div>
        </div>
        <div class="form-group">
          <label for="confirm" class="col-sm-3 control-label">确认新密码</label>
          <div class="col-sm-7">
            <input id="confirm" class="form-control" name="conf_psw" type="password" placeholder="确认新密码">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-7">
            <button type="submit" class="btn btn-primary">修改密码</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  <?php $current_index = 'password-reset';?>
  <?php include 'ins/sidebar.php';?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
