<?php
require_once '../functions.php';
$user = xiu_get_current_user();

$where = '1 = 1';
$search = '';

/**
 * 筛选判断
 */
if (isset($_GET['category']) && $_GET['category'] !== 'all') {
  $where .= ' and posts.category_id = ' . $_GET['category'];
  $search .='&category='.$_GET['category'];
}

if (isset($_GET['status']) && $_GET['status'] !== 'all') {
  $where .= " and posts.status = '{$_GET['status']}'";
  $search .='&status='.$_GET['status'];
}

// $where => "1 = 1 and posts.category_id = 1 and posts.status = 'published'"
// $search => "&category=1&status=published"

//处理分页参数
/**
 * $page :当前展示页数
 * $size :当前展示条数
 * $skipNum : 越过的条数
 * $total_page:最大的页数
 */
$size = 20;
$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$skipNum = ($page - 1) * $size;

if ($page < 1) {
  header('Location: /admin/posts.php?page=1');
  exit;
}

$total_count = (int)xiu_fetch_one("select count(1) as count from posts
 inner join categories on posts.category_id = categories.id  
 inner join users on posts.user_id  = users.id
 where {$where} 
  ")['count'];
$total_page = (int)ceil($total_count/$size);

//条件帅选total
if ($total_page == 0) {
    $total_page = $page ;
}

if ($page > $total_page) {
  header('Location: /admin/posts.php?page='.$total_page);
  exit;
}

/**
 * [$posts 数据库查询]
 * @var [type]
 */
$posts = xiu_fetch_all("select
 posts.id,
 posts.title,
 users.nickname as user_name,
 categories.name as category_name,
 posts.created,
 posts.status
from posts
inner join categories on posts.category_id = categories.id  
inner join users on posts.user_id  = users.id
where {$where}
ORDER BY posts.created DESC 
limit {$skipNum},{$size}");

$category = xiu_fetch_all('select * from categories');

$visiable = 5;
$begin = $page - ($visiable - 1) / 2;
$end = $begin + $visiable - 1;

$begin = $begin < 1 ? 1 : $begin;
$end = $begin + $visiable -1;
$end = $end > $total_page ? $total_page : $end;
$begin = $end - $visiable + 1;
$begin = $begin < 1 ? 1 : $begin;

/**
 * 格式化日期
 * @param  [string] $status 时间字符串
 * @return [string]         格式化后的时间字符串
 */
function convert_date ($created) {
  date_default_timezone_set('PRC');
  $strtime = strtotime($created);
  return date('Y年m月d日<b\r>H:i:s',$strtime); 
}

/**
 * 英文状态转换成中文状态
 * @param  [string] $status 英文状态
 * @return [string]         中文状态
 */
function convert_status ($status) {
 $dict = array( 
    'published' => '已发布',
    'drafted' => '草稿',
    'trashed' => '回收站' );
  return isset($dict[$status]) ? $dict[$status] : '未知'; 
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="post-add.php" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <a class="btn btn-danger btn-sm" id="btn_delete" href="/admin/post-delete.php" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($category as $item): ?>
              <option value="<?php echo $item['id'];?>"<?php echo isset($_GET['category'])&&$_GET['category'] === $item['id'] ? ' selected' : ''; ?>><?php echo $item['name']; ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="drafted" <?php echo isset($_GET['status']) && $_GET['status'] === 'drafted' ? ' selected' : '' ;?>>草稿</option>
            <option value="published" <?php echo isset($_GET['status']) && $_GET['status'] === 'published'? ' selected' : '' ;?>>已发布</option>
            <option value="trashed" <?php echo isset($_GET['status']) && $_GET['status'] === 'trashed'? ' selected' : '' ;?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="?page=<?php echo (($page-1)>0)? ($page-1) : '1';?>">上一页</a></li>
          <?php for($i = $begin; $i <= $end; $i ++):?>
          <li <?php echo $i === $page ? ' class="active"' : '' ?>><a href="?page=<?php echo $i . $search ;?>"><?php echo $i ;?></a></li>
          <?php endfor ?>
          <li><a href="?page=<?php echo ($total_page - $page - 1) > 0 ? $page + 1 : $total_page ;?>">下一页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
       <?php  if (empty($posts)):?>
          <div class="text-center alert alert-danger">暂无数据</div>
        <?php else: ?> 
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox" id="allCheckBox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody> 
        <?php foreach ($posts as $items): ?>
              <tr>
            <td class="text-center"><input type="checkbox" data-id="<?php echo $items['id']; ?>"></td>
            <td><?php echo $items['title']; ?></td>
            <td><?php echo $items['user_name']; ?></td>
            <td><?php echo $items['category_name']; ?></td>
            <td class="text-center"><?php echo convert_date($items['created']); ?></td>
            <td class="text-center"><?php echo convert_status($items['status']) ; ?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/post-delete.php?id=<?php echo $items['id']; ?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
        <?php endforeach ?>
        </tbody>
       <?php endif ?>
      </table>
    </div>
  </div>
  <?php $current_index = 'posts';?>
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
          var id = $(this).data('id') 
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
        
          