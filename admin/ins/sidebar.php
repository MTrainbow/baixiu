<!-- <?php echo  $_SERVER['PHP_SELF']; ?> -->
<?php 
//这个模块在admin/被载入,所有这个相当路径相对于index.php而言,
//要根本解决可以采用物理路api见config.php
require_once '../functions.php';
$current_index = isset($current_index) ? $current_index : '' ;
$user = xiu_get_current_user();
$id= $user['id'];
$data = xiu_fetch_all("select * from users where id = {$id};")[0];
 ?>
<div class="aside">
    <div class="profile">
      <img class="avatar" src="<?php echo $data['avatar']?>">
      <h3 class="name"><?php echo $data['nickname']; ?></h3>
    </div>
    <ul class="nav">
      <li<?php echo $current_index == 'index' ? ' class = "active"' : ''; ?>>
        <a href="index.php"><i class="fa fa-dashboard"></i>仪表盘</a>
      </li>
      <li<?php echo in_array($current_index, array('posts', 'post-add', 'categories')) ? ' class = "active"' : ''; ?>>
        <a href="#menu-posts" data-toggle="collapse"<?php echo in_array($current_index, array('posts', 'post-add', 'categories')) ? ' class = "collapsed"' : ''; ?>>
          <i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
        </a>
        <ul id="menu-posts" class="collapse<?php echo in_array($current_index, array('posts', 'post-add', 'categories')) ? ' in' : ''; ?>">
          <li<?php echo $current_index == 'posts' ? ' class = "active"' : ''; ?>><a href="posts.php">所有文章</a></li>
          <li <?php echo $current_index == 'post-add' ? ' class = "active"' : ''; ?>><a href="post-add.php">写文章</a></li>
          <li <?php echo $current_index == 'categories' ? ' class = "active"' : ''; ?>><a href="categories.php">分类目录</a></li>
        </ul>
      </li>
      <li<?php echo $current_index == 'comments' ? ' class = "active"' : ''; ?>>
        <a href="comments.php"><i class="fa fa-comments"></i>评论</a>
      </li>
      <li<?php echo $current_index == 'users' ? ' class = "active"' : ''; ?>>
        <a href="users.php"><i class="fa fa-users"></i>用户</a>
      </li>
      <li<?php echo in_array($current_index, array('nav-menus', 'slides', 'settings')) ? ' class = "active"' : ''; ?>>
        <a href="#menu-settings" data-toggle="collapse"<?php echo in_array($current_index, array('nav-menus', 'slides', 'settings')) ? ' class = "collapsed"' : ''; ?>>
          <i class="fa fa-cogs"></i>设置<i class="fa fa-angle-right"></i>
        </a>
        <ul id="menu-settings" class="collapse<?php echo in_array($current_index, array('nav-menus', 'slides', 'settings')) ? ' in' : ''; ?>">
          <li<?php echo $current_index == 'nav-menus' ? ' class = "active"' : ''; ?>><a href="nav-menus.php">导航菜单</a></li>
          <li<?php echo $current_index == 'slides' ? ' class = "active"' : ''; ?>><a href="slides.php">图片轮播</a></li>
          <li<?php echo $current_index == 'settings' ? ' class = "active"' : '';  ?>><a href="settings.php">网站设置</a></li>
        </ul>
      </li>
    </ul>
  </div>