<?php 

//判断是否已经登录了
require_once '../function.php';
xiu_get_current_user();

//判断地址中是否传有参数
if (isset($_GET['id'])) {
  //根据这个 ID 拿到需要修改的数据
  
  $categories_edit = xiu_fetch_once("select * from categories where id={$_GET['id']} limit 1 ;");
  
}

//添加
function xiu_insert(){


  if (empty($_POST['name']) || empty($_POST['slug'])) {
    $GLOBALS['notice'] = '请正确填写表单';

    return;
  }
  $add_name = $_POST['name'];
  $add_slug = $_POST['slug'];


  $add_categories = xiu_excute("insert into categories value(null,'{$add_name}','{$add_slug}');");


  // $add_categories != 1 ? $GLOBALS['notice'] = '添加失败' : $GLOBALS['notice'] ='添加成功';
  if ($add_categories <= 0 ) {
    $GLOBALS['notice'] = '添加失败';


  }else{
    $GLOBALS['notice'] ='添加成功';
    $GLOBALS['success'] = true;
  }

}

//编辑
function xiu_categories_edit(){
  // var_dump($GLOBALS[$categories_edit]);
  // return;

  global $categories_edit;

   if (empty($_POST['name']) || empty($_POST['slug'])) {
    $GLOBALS['notice'] = '请正确填写表单';

    return;
  }
  $id = $categories_edit['id'];
  $edit_name = empty($_POST['name']) ? $categories_edit['name'] : $_POST['name'];
  $edit_slug = empty($_POST['slug']) ? $categories_edit['slug'] : $_POST['slug'];
  $categories_edit['name'] = empty($_POST['name']) ? $categories_edit['name'] : $_POST['name'];
  $categories_edit['slug'] = empty($_POST['slug']) ? $categories_edit['slug'] : $_POST['slug'];


  $edit_categories_rows = xiu_excute("update categories set `name`='{$edit_name}',slug = '{$edit_slug}' where id={$id};");

  if ($edit_categories_rows <= 0 ) {
    $GLOBALS['notice'] = '保存失败';
  }else{
    $GLOBALS['notice'] ='保存成功';
    $GLOBALS['success'] = true;
  }

}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (empty($_GET['id'])) {
    xiu_insert();
  }else{
     xiu_categories_edit();
  }

  
}





$categories_cont = xiu_fetch_all('select * from categories;');



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

    <?php include 'inc/navbar.php'; ?>
    
    <div class="container-fluid">
      <div class="page-title">
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if(isset($notice) && isset($success)): ?>

      <div class="alert alert-success">
        <strong><?php echo $notice; ?></strong>
      </div>
      <?php endif ?>
      <?php if(isset($notice) && empty($success)): ?>
         <div class="alert alert-danger">
        <strong><?php echo $notice; ?></strong>
      </div>
      <?php endif ?>

      <div class="row">
        <div class="col-md-4">
          <?php if (isset($categories_edit)) : ?>
            <!-- //更新表单 -->
              <form action="<?php echo $_SERVER['PHP_SELF'].'?id='.$categories_edit['id'] ?>" method = "post">
            <h2>编辑《<?php echo $categories_edit['name'] ?>》</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo $categories_edit['name'] ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $categories_edit['slug'] ?>">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">保存</button>
            </div>
          </form>
            <?php else : ?>
            <!-- //添加表单 -->
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method = "post">
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
            <a id="btn_delet" class="btn btn-danger btn-sm" href="/admin/categories_delet.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories_cont as $value) :?>
              <tr>
                <td class="text-center"><input type="checkbox"  data-id="<?php echo $value['id'] ?>"></td>
                <td><?php echo $value['slug']; ?></td>
                <td><?php echo $value['name']; ?></td>
                <td class="text-center">
                  <a href="/admin/categories.php?id=<?php  echo $value['id'] ?>" class="btn btn-info btn-xs">编辑</a>
                  <a href="categories_delet.php?id=<?php echo $value['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>

            <?php endforeach ?>
              
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

   <?php $current_page = 'categories'?>
    <?php include 'inc/aside.php';?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script type="text/javascript">
    $(function($){


      //矮子表格中任意一个CheckBox 选中状态变化时
      //变量本地化，减少无意义的选择操作
      var $tbodyCheckbox = $('tbody input');
      var $btnDelet = $('#btn_delet');
      // var $theadCheckbox = $('thead input');


      //====version1====
      // $tbodyCheckbox.on('change',function(){
      //   var $flag = false;
      //   //有任意一个选中就显示反之就隐藏
      //   $tbodyCheckbox.each(function(i,item){
      //     //attr 和 prop 的区别 ：
      //     // attr 访问的是元素属性
      //     // prop 访问的是DOM对象的属性
      //     // console.dir(item);
      //     // console.log($(item).prop('checked'));
      //     // console.log($(item).attr('checked'));
      //     if ($(item).prop('checked')) {
      //        $flag = true;
      //     }
      //   })

      //   $flag ? $btnDelet.fadeIn() : $btnDelet.fadeOut();

      // })
      //将选中的id 存放到数组中
      var $allCheckeds = [];
      $tbodyCheckbox.on('change',function(){
        // console.log($(this).data('id'));
        // console.log($(this).attr('data-id'))

        var $id = $(this).data('id');
        //根据当前是否选中决定添加还是移除
        if ($(this).prop('checked')) {
          $allCheckeds.includes($id) || $allCheckeds.push($id);
        }else{
          $allCheckeds.splice($allCheckeds.indexOf($id),1);
        }
        // console.log($allCheckeds);
        //根据当前数组长度判断是否显示删除按钮
        $allCheckeds.length ? $btnDelet.fadeIn() : $btnDelet.fadeOut();
        //search ==>设置地址 ？ 部分的值
        $btnDelet.prop('search','?id='+ $allCheckeds);
      })
      $('thead input').on('change',function(){
        //获取当前选中按钮的状态
        var chcked = $(this).prop('checked'); 
        //根据当前按钮的状态 设置 tbody 
        $tbodyCheckbox.prop('checked',chcked).trigger('change');
      })


    })
  </script>
</body>
</html>
