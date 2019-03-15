<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>

  <style>
  #loading {
    display: none;
    align-items: center;
    justify-content: center;
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    top: 0;
    background-color: rgba(0,0,0,.7);
    z-index: 999;
  } 

  .flip-txt-loading {
    font: 26px Monospace;
    letter-spacing: 5px;
    color: #AF3F3F;
  }

  .flip-txt-loading > span {
    animation: flip-txt  2s infinite;
    display: inline-block;
    transform-origin: 50% 50% -10px;
    transform-style: preserve-3d;
  }

  .flip-txt-loading > span:nth-child(1) {
    -webkit-animation-delay: 0.10s;
            animation-delay: 0.10s;
  }

  .flip-txt-loading > span:nth-child(2) {
    -webkit-animation-delay: 0.20s;
            animation-delay: 0.20s;
  }

  .flip-txt-loading > span:nth-child(3) {
    -webkit-animation-delay: 0.30s;
            animation-delay: 0.30s;
  }

  .flip-txt-loading > span:nth-child(4) {
    -webkit-animation-delay: 0.40s;
            animation-delay: 0.40s;
  }

  .flip-txt-loading > span:nth-child(5) {
    -webkit-animation-delay: 0.50s;
            animation-delay: 0.50s;
  }

  .flip-txt-loading > span:nth-child(6) {
    -webkit-animation-delay: 0.60s;
            animation-delay: 0.60s;
  }

  .flip-txt-loading > span:nth-child(7) {
    -webkit-animation-delay: 0.70s;
            animation-delay: 0.70s;
  }

  @keyframes flip-txt  {
    to {
      -webkit-transform: rotateX(1turn);
              transform: rotateX(1turn);
    }
  }
  
  </style>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'ins/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right">
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
  <!-- 遮挡层 loading.awesomes.cn/loading.io -->
  <div id="loading">
    <div class="flip-txt-loading">
      <span>L</span><span>o</span><span>a</span><span>d</span><span>i</span><span>n</span><span>g</span>
    </div>
  </div>
  

  <?php $current_index = 'comments';?>
  <?php include 'ins/sidebar.php';?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <script id="comment_tmpl" type="text/x-jsrender">
    {{for data}}
      <tr class="{{: status === 'held' ? 'warning' : status === 'rejected' ? 'danger' : '' }}" data-id="{{: id}}">
        <td class="text-center"><input type="checkbox"></td>
        <td>{{: author }}</td>
        <td>{{: content }}</td>
        <td>《{{: post_title }}》</td>
        <td>{{: created }}</td>
        <td>{{: status == 'held' ? '待审' : status == 'rejected' ? '拒绝' : '准许'}}</td>
        <td class="text-center">
          {{if status === 'held'}}
              <a class="btn btn-info btn-xs btn-edit" href="javascript:;" data-status="approved">批准</a>
              <a class="btn btn-warning btn-xs btn-edit" href="javascript:;" data-status="rejected">拒绝</a>
          {{/if}}
              <a class="btn btn-danger btn-xs btn-delete" href="javascript:;">删除</a>
        </td>
          </tr>
    {{/for}}
  </script>

  <script>
    var $tbody = $('tbody');
    var $btnbatch = $('.btn-batch');
    var checkItem = [] ;

    $(document)
      .ajaxStart(function () {
        NProgress.start();
        // $('#loading').fadeIn();-(display--block样式要求flex)
        $('#loading').css('display','flex'); 
    })
      .ajaxStop(function () {
        NProgress.done();
        $('#loading').css('display','none');
    });  
    
    var currentPage = 1;   
    function loadData (page) {
      $.getJSON('/admin/api/comments.php',{page:page},function(res){
      if (page > res.total_page) {
        loadData(res.total_page);
        return false;
      }
      //第一次没有初始化分页组件，第二次不会重新渲染页数
      $('.pagination').twbsPagination('destroy');  
      $('.pagination').twbsPagination({
        first: '首页',
        prev: '上一页',
        next: '下一页',
        last: '尾页',
        startPage: page,
        totalPages: res.total_page,
        visiblePages: 7,
        initiateStartPageClick: false,
        onPageClick: function (event, page) {
            loadData(page);
        }
        });
      var html = $('#comment_tmpl').render({data:res.data});
      $tbody.fadeOut(function () {
        $(this).html(html).fadeIn();  
      })
      currentPage = page;
    })
    }

    $('.pagination').twbsPagination({
        first: '首页',
        prev: '上一页',
        next: '下一页',
        last: '尾页',
        totalPages: 100,
        visiblePages: 7,
        onPageClick: function (event, page) {
            loadData(page);
        }
        });
    loadData(currentPage);  
    
//删除评论
    $tbody.on('click','.btn-delete',function () {
     var tr = $(this).parent().parent();
     var id = parseInt(tr.data('id'));
     $.getJSON('/admin/api/comments-delete.php',{id:id},function (res) {
       if (!res) return;
        // tr.remove();
        loadData(currentPage);
     });
  })

//修改评论状态
  $tbody.on('click','.btn-edit',function () {
    var id = parseInt($(this).parent().parent().data('id'));
    var status = $(this).data('status');
    $.post('/admin/api/comments-status.php?id=' + id,{status:status},function (res) {
      if(!res) return;
      loadData(currentPage);
    })
  })

//批量删除
  $tbody.on('change', 'td > input[type=checkbox]', function () {
    var id = $(this).parent().parent().data('id');
    if ($(this).prop('checked')) {
        checkItem.includes(id) || checkItem.push(id);
    }else {
       checkItem.splice(checkItem.indexOf(id),1); 
    }
    checkItem.length ? $btnbatch.fadeIn() : $btnbatch.fadeOut();
    console.log(checkItem);
  })

//全选/全不选
  $('th > input[type=checkbox]').on('click',function () {
    var checked = $(this).prop('checked');
    $('td > input[type=checkbox]').prop('checked',checked).trigger('change');
  })

//批量操作
  $btnbatch
  .on('click', '.btn-info', function () {
    $.post('/admin/api/comments-status.php?id=' + checkItem.join(','), {status : 'approved'}, function (res) {
      if(!res) return;
      loadData(currentPage);
      $('th > input[type=checkbox]').prop('checked',false);
    })
  })
  .on('click', '.btn-warning', function () {
    $.post('/admin/api/comments-status.php?id=' + checkItem.join(','), {status : 'rejected'}, function (res) {
      if(!res) return;
      loadData(currentPage);
      $('th > input[type=checkbox]').prop('checked',false);
    })
  })
  .on('click', '.btn-danger', function () {       
    $.getJSON('/admin/api/comments-delete.php', {id : checkItem.join(',')}, function (res) {
      if(!res) return;
      loadData(currentPage);
       $('th > input[type=checkbox]').prop('checked',false);
    })
  })  
  var fun = function(options){
    var xhr = null
    var method = options.method.toLocaleUpperCase();
    var data = param(options.params)
    if (window.XMLHttpRequest){
            xhr = new XMLHttpRequest();
        } else {
            xhr = new ActiveXObject('Microsoft.XMLHTTP');
        }
    if (method == 'get') {
      xhr.open(method,options.url+"?"+data,optionas.async)
    }else if(method =="post"){
      xhr.open(method,optionas.url,optionas.async);
      xhr.setRequestHeader('Content-Type','appliaction/x-www-form-urlencoded');
      xhr.send(data);
    }
    xhr.addEventLinener('readystatechange',function(){
      if (this.responText!==4) return
        options.success(this.responText)
    })
    var param = function(data){
      var arr = []
      for(var key in data){
        arr.push(key+"="+data[key])
      }
      return arr.join('&')
    }   
  }

  </script>
  <script>NProgress.done()</script>
</body>
</html>
