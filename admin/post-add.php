<?php 
require_once '../functions.php';
$user = xiu_get_current_user();

/**
 * [add_posts 添加文章]
 * title content slug (feature-文件) category created status
 */
function add_posts () {
  global $user;
  global $mess_err ;

  if (empty($_POST['title']) || empty($_POST['content']) || empty($_POST['slug']) ||
   empty($_POST['category']) || empty($_POST['created']) || empty($_POST['status'])) {
      $mess_err = '请正确填写表单数据' ;
      return ;
  }
  if (xiu_fetch_one("select count(1) as count from posts where slug in ('{$_POST['slug']}');")['count']) {
    $mess_err = "别名已存在,请重新输入" ;
    return ;
  }
  //校验文件大小、格式、是否有错误等
  $source = $_FILES['feature'];
  if ($source['error']!== UPLOAD_ERR_OK ) {
      $mess_err = "文件上传失败";
      return;
  }
  if (strpos($source['type'],'image/') !== 0) {
    $mess_err = "文件格式不符";
    return;
  }
  if ($source['size'] > 5*1024*1024) {
    $mess_err = "文件过大，请重新选择";
    return;
  }
  $ext = pathinfo($source['name'], PATHINFO_EXTENSION); 
  if (file_exists('../static/uploads')) {
    var_dump($source);
    $target = '../static/uploads/avatar-' . uniqid() . '.' . $ext;
  }else{
    mkdir('../static/uploads');
   $target = '../static/uploads/avatar-' . uniqid() . '.' . $ext;
  }
  if (!move_uploaded_file($source['tmp_name'],$target)) {
    $mess_err = "文件上传失败";
    return;
  }
  $image_file = '/static/uploads/avatar-' . uniqid() . '.' . $ext;
 
 //接收数据title content slug (feature-文件) category created status
 $title = $_POST['title']; 
 $content = $_POST['content'];
 $slug = $_POST['slug'];
 $feature = isset($image_file) ? $image_file : '';
 $status = $_POST['status'];
 $created = $_POST['created'];
 $category_id = $_POST['category'];
 $user_id = $user['id'];

 $sql = sprintf(
      "insert into posts values (null, '%s', '%s', '%s', '%s', '%s', 0, 0, '%s', %d, %d)",
      $slug,
      $title,
      $feature,
      $created,
      $content,
      $status,
      $user_id,
      $category_id
    );
 // $result = xiu_execute("insert into posts values (null,'{$slug}','{$title}','{$feature}','{$created}','{$content}',null,null,'{$status}',{$user_id},{$category_id});");
 if (xiu_execute($sql) > 0) {
    header('Location: /admin/posts.php'); 
    exit;
 }else {
  $mess_err = "保存失败,请重试";
 }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  add_posts();
}


$category = xiu_fetch_all('select * from categories');
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/vendors/simplemde/simplemde.min.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'ins/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>写文章</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <?php if (isset($mess_err)): ?>
      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $mess_err; ?>
      </div>
      <?php endif ?>
      <form class="row" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype = "multipart/form-data">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" placeholder="文章标题" autocomplete="off" value="<?php echo isset($_POST['title']) ? $_POST['title'] : '' ; ?>">
          </div>
          <div class="form-group">
            <label for="content">标题</label>
            <textarea id="content" class="form-control input-lg" name="content" cols="30" rows="10" placeholder="内容" value="<?php echo isset($_POST['content']) ? $_POST['content'] : '' ; ?>"></textarea>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value = "<?php echo isset($_POST['slug']) ? $_POST['slug'] : '' ; ?>">
            <p class="help-block">https://zce.me/post/<strong>slug</strong></p>
          </div>
          <div class="form-group">
            <label for=" ">特色图像</label>
            <!-- show when image chose -->
            <img class="help-block thumbnail" style="display: none">
            <input id="feature" class="form-control" name="feature" type="file" accept="image/*">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
              <?php foreach ($category as $items): ?>
                <option value="<?php echo $items['id']?>" <?php echo isset($_POST['category']) && $_POST['category'] == $items['id'] ? ' selected' : ''; ?>><?php echo $items['name']; ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local" value="<?php echo isset($_POST['created']) ? $_POST['created'] : '' ; ?>">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted" <?php echo isset($_POST['status']) && $_POST['status'] == 'drafted' ? ' selected' : '' ; ?>>草稿</option>
              <option value="published" <?php echo isset($_POST['status']) && $_POST['status'] == 'published' ? ' selected' : '' ; ?>>已发布</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  <?php $current_index = 'post-add';?>
  <?php include 'ins/sidebar.php';?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/moment/moment.js"></script>
  <script src="/static/assets/vendors/simplemde/simplemde.min.js"></script>
    <script>
    $(function () {
      // 当文件域文件选择发生改变过后，本地预览选择的图片
      $('#feature').on('change', function () {
        var file = $(this).prop('files')[0]
        // 为这个文件对象创建一个 Object URL
        var url = URL.createObjectURL(file)
        // url => blob:http://zce.me/65a03a19-3e3a-446a-9956-e91cb2b76e1f
        // 不用奇怪 BLOB: binary large object block
        // 将图片元素显示到界面上（预览）
        $(this).siblings('.thumbnail').attr('src', url).fadeIn()
      })

      // slug 预览
      $('#slug').on('input', function () {
        $(this).next().children().text($(this).val())
      })

      // Markdown 编辑器
      new SimpleMDE({
        element: $("#content")[0],
        autoDownloadFontAwesome: false
      })

      // 发布时间初始值
      $('#created').val(moment().format('YYYY-MM-DDTHH:mm'))
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
