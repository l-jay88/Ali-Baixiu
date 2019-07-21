<?php 

//判断是否已经登录了
require_once '../function.php';
xiu_get_current_user();

 ?>
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
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">

    <?php include 'inc/navbar.php'; ?>
    
    </nav>
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
          <li><a href="#">上一页</a></li>
          <li><a href="#">1</a></li>
          <li><a href="#">2</a></li>
          <li><a href="#">3</a></li>
          <li><a href="#">下一页</a></li>
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
          <!-- <tr class="danger">
            <td class="text-center"><input type="checkbox"></td>
            <td>大大</td>
            <td>楼主好人，顶一个</td>
            <td>《Hello world》</td>
            <td>2016/10/07</td>
            <td>未批准</td>
            <td class="text-center">
              <a href="post-add.html" class="btn btn-info btn-xs">批准</a>
              <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr> -->
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page ='comments'?>
  <?php include 'inc/aside.php';?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.min.js"></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <script id="comments_tmpl" type="text/x-jsrender">
    {{for comments}}
    <tr {{if status == 'held'}} class = "warning" {{else}} class = "danger" {{/if}} data-id = {{:id}}>
            <td class="text-center"><input type="checkbox"></td>
            <td>{{:author}}</td>
            <td>{{:content}}</td>
            <td>{{:title}}</td>
            <td>{{:created}}</td>
            <td>{{:status}}</td>
            <td class="text-center">
              {{if status == 'held'}}
              <a href="post-add.html" class="btn btn-info btn-xs">批准</a>
              <a href="post-add.html" class="btn btn-info btn-xs">拒绝</a>
              {{/if}}
              <a href="javascript:;" class="btn btn-danger btn-xs btn-delete ">删除</a>
            </td>
          </tr>
    {{/for}}
    
  </script>
  <script type="text/javascript">

    // // nprogress
$(document)
 .ajaxStart(function () {
   NProgress.start()
 })
 .ajaxStop(function () {
   NProgress.done()
 })


  var pagecount = 1;
   function loadpage(page){
    $('tbody').fadeOut();
     //向服务端发送 AJAX 请求获取 评论数据
    $.getJSON('/admin/api/comment.php',{page:page},function(res){
      if (page > res.total_pages) {
        loadpage(res.total_pages);
        return false;
      }
      //必须 destroy 后才能重新更新分页列表
      $('.pagination').twbsPagination('destroy');
      $('.pagination').twbsPagination({
        first:'第一页',
        last:'最后一页',
        prev:'上一页',
        next:'下一页',
        totalPages:res['total_pages'],
        visiablePages:5,
        startPage:page,
        initiateStartPageClick:false,//不初始化
        onPageClick:function(e,page){
          loadpage(page);
        }
      });
      //请求得到响应后自动执行
      var html = $("#comments_tmpl").render(res);
      $('tbody').html(html).fadeIn();
      pagecount = page;

    });
   };

   $('.pagination').twbsPagination({
        first:'第一页',
        last:'最后一页',
        prev:'上一页',
        next:'下一页',
        totalPages:100,
        visiablePages:5,
        onPageClick:function(e,page){
          //第一次初始化使就会触发一次
          loadpage(page);
        }
      });

   //TODO:删除评论功能代码
   //====================
   $('tbody').on('click','.btn-delete',function(){
    //采用事件委托
    //1. 拿到需要删除数据的ID
    var id = $(this).parent().parent().data('id');
    //2. 发送 ajax 请求 告诉服务端要删除那一条具体数据
    $.get('/admin/api/comments_delete.php',{id:id},function(res){
      //3. 根据服务端返回的数据判断是否删除成功并移除这个元素
      //重新载入这一页的数据
      if (!res) return;
      // console.log(res);
      loadpage(pagecount);

    })
    
   })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
