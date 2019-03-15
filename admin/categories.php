<?php 
require_once '../functions.php';
$user = xiu_get_current_user();

function add_category () {
  global $mess_err;
  global $success;

  if (empty($_POST['name']) || empty($_POST['slug'])) {
      $mess_err = "请正确填写表单";
      $success = false;
      return;
    }
    $name = $_POST['name'];
    $slug = $_POST['slug'];

    var_dump($name);

   $rows = xiu_execute("insert into categories values(null,'".$name."','".$slug."');");
   $success = $rows > 0 ;
   $mess_err = $rows <= 0 ? '添加失败！' : '添加成功！';
}

function edit_category () {
    global $current_edit_category;
    global $mess_err;
    global $success;

    $id = $current_edit_category['id'];
    $name = empty($_POST['name']) ? $current_edit_category['name'] : $_POST['name'];
    $current_edit_category['name'] = $name;
    $slug = empty($_POST['slug']) ? $current_edit_category['slug'] : $_POST['slug'];
    $current_edit_category['slug'] = $slug;
    $sql = sprintf("update categories set slug ='%s', name ='%s' where id = %d",$slug,$name,$id);
    $rows = xiu_execute($sql);
    // $rows = xiu_execute("UPDATE categories SET slug = '{$slug}',name = '$name' where id = {$id};");
    $success = $rows > 0 ;
    $mess_err = $rows <= 0 ? '更新失败！' : '更新成功！';

}

//add or edit 
if (empty($_GET['id'])) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    add_category();
  }
}else {
  $current_edit_category = xiu_fetch_one("select * from categories where id in (' {$_GET['id']} ');");
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      edit_category();
  }
}

//查询全部分类数据
$cates_data = xiu_fetch_all('select * from categories;');

 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <?php if (isset($mess_err)): ?>
      <?php if ($success): ?>
      <div class="alert alert-success">
        <strong>成功！</strong> <?php echo $mess_err; ?>
      </div>
      <?php else: ?>
      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $mess_err; ?>
      </div>
      <?php endif ?>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <?php if (isset($current_edit_category)): ?>
            <form action="<?php echo $_SERVER['PHP_SELF'];?>?id=<?php echo $current_edit_category['id'] ?>" method="post">
            <h2>编辑《<?php echo $current_edit_category['name']; ?>》</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" value="<?php echo $current_edit_category['name']; ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text"  value="<?php echo $current_edit_category['slug']; ?>">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">保存</button>
              <button style="visibility:hidden;"></button>
              <a href="/admin/categories.php" class="btn btn-primary" type="submit">取消</a>
            </div>
          </form>
          <?php else: ?>
            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
            <h2>添加新分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
          <?php endif ?>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="/admin/categories-delete.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input id="allCheckBox" type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cates_data as $items): ?>
                <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $items['id']; ?>"></td>
                <td><?php echo $items['name']; ?></td>
                <td><?php echo $items['slug']; ?></td>
                <td class="text-center">
                  <a href="/admin/categories.php?id=<?php echo $items['id'];?> " class="btn btn-info btn-xs">编辑</a>
                  <a href="/admin/categories-delete.php?id=<?php echo $items['id'];?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_index = 'categories';?>
  <?php include 'ins/sidebar.php';?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function($){
      var $tbodyCheckInput = $('tbody input')
      var $btnDelete = $('#btn_delete')
      var $allCheckBox = $('#allCheckBox')
      var allCheck = []
      $tbodyCheckInput.on('change',function(){
          // var id = this.dataset['id'];
          // var id = $(this).attr('id');
          var id = $(this).data('id') //当前点击的data-id
          if ($(this).prop('checked')) {
             // allCheck.indexOf(id) !== -1 || allCheck.push(id)
             allCheck.includes(id) || allCheck.push(id) 
          }else{
            allCheck.splice(allCheck.indexOf(id),1)
          }
          allCheck.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut()
          $btnDelete.prop('search','?id='+allCheck)
      })
      $allCheckBox.on('change',function(){
        $tbodyCheckInput.prop('checked',$(this).prop('checked')).trigger('change');
      })
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
