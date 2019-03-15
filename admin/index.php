<?php 
require_once '../functions.php';
$user = xiu_get_current_user();
// -- 文章数
// select count(1) as count from posts

// -- 草稿数
// select count(1) as count from posts where status = 'drafted'

// -- 分类数
// select count(1) as count from categories

// -- 评论数
// select count(1) as count from comments where status = 'held'

// -- 带审核数
// select count(1) as count from 
$posts_count = xiu_fetch_one('select count(1) as count from posts')['count'];

$posts_drafted = xiu_fetch_one("select count(1) as count from posts where status = 'drafted';")['count'];

$cates_count = xiu_fetch_one('select count(1) as count from categories')['count'];

$coms_count = xiu_fetch_one('select count(1) as count from comments')['count'];

$coms_held = xiu_fetch_one("select count(1) as count from comments where status = 'held';")['count'];
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
      <div class="jumbotron text-center">
        <h1>One Belt, One Road</h1>
        <p>Thoughts, stories and ideas.</p>
        <p><a class="btn btn-primary btn-lg" href="post-add.php" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo $posts_count; ?></strong>篇文章（<strong><?php  echo $posts_drafted; ?></strong>篇草稿）</li>
              <li class="list-group-item"><strong><?php echo $cates_count; ?></strong>个分类</li>
              <li class="list-group-item"><strong><?php echo $coms_count; ?></strong>条评论（<strong><?php echo $coms_held; ?></strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4">
          <canvas id="chart-area"> </canvas>
        </div>
        <div class="col-md-4">
          <button id="randomizeData">Randomize Data</button>
          <button id="addDataset">Add Dataset</button>
          <button id="removeDataset">Remove Dataset</button>
        </div>
      </div>
    </div>
  </div>
  
  <?php $current_index = 'index';?>
  <?php include 'ins/sidebar.php';?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/chart/Chart.js"></script>
  <script src="/static/assets/vendors/chart/utils.js"></script>
  <script>NProgress.done()</script>

  <script>
     var randomScalingFactor = function() {
      return Math.round(Math.random() * 100);
    };

    var config = {
      type: 'pie',
      data: {
        datasets: [{
          data: [
            randomScalingFactor(),
            randomScalingFactor(),
            randomScalingFactor(),
            randomScalingFactor(),
            randomScalingFactor(),
          ],
          backgroundColor: [
            window.chartColors.red,
            window.chartColors.orange,
            window.chartColors.yellow,
            window.chartColors.green,
            window.chartColors.blue,
          ],
          label: 'Dataset 1'
        }],
        labels: [
          'Red',
          'Orange',
          'Yellow',
          'Green',
          'Blue'
        ]
      },
      options: {
        responsive: true
      }
    };

    window.onload = function() {
      var ctx = document.getElementById('chart-area').getContext('2d');
      window.myPie = new Chart(ctx, config);
    };

    document.getElementById('randomizeData').addEventListener('click', function() {
      config.data.datasets.forEach(function(dataset) {
        dataset.data = dataset.data.map(function() {
          return randomScalingFactor();
        });
      });

      window.myPie.update();
    });

    var colorNames = Object.keys(window.chartColors);
    document.getElementById('addDataset').addEventListener('click', function() {
      var newDataset = {
        backgroundColor: [],
        data: [],
        label: 'New dataset ' + config.data.datasets.length,
      };

      for (var index = 0; index < config.data.labels.length; ++index) {
        newDataset.data.push(randomScalingFactor());

        var colorName = colorNames[index % colorNames.length];
        var newColor = window.chartColors[colorName];
        newDataset.backgroundColor.push(newColor);
      }

      config.data.datasets.push(newDataset);
      window.myPie.update();
    });

    document.getElementById('removeDataset').addEventListener('click', function() {
      config.data.datasets.splice(0, 1);
      window.myPie.update();
    })
  </script>
</body>
</html>
