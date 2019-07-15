<?php 
// 载入配置文件

require_once '../config.php';
// echo DB_HOST;
function login(){
  // 接收效验
// var_dump($_POST);
// array(2) { ["email"]=> string(2) "23" ["password"]=> string(4) "sdfd" }
// return;
  if (empty($_POST['email'])) {
    # code...
    $GLOBALS['notice'] = '请填写邮箱';
    return;
  }
  if (empty($_POST['password'])) {
    # code...
    $GLOBALS['notice'] = '请填写密码';
    return;
  }
  $email = $_POST['email'];
  $password = $_POST['password'];

  // 业务效验

  //连接数据库

  $conn = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
  // $conn = mysqli_connect('127.0.0.2','root','456789','baixiu');
  // var_dump($conn);
  if (!$conn) {
    $GLOBALS['notice'] = '数据库连接失败';
    return;
  }

  $query = mysqli_query($conn,"select * from users WHERE email = '{$email}';");

  if (!$query) {
    $GLOBALS['notice'] = '登录失败，请重试';
    return;
  }

  $user = mysqli_fetch_assoc($query);

  if (!$user) {
    $GLOBALS['notice'] = '邮箱和用户名错误';
    return;
  }

  if ($password !== $user['password']) {
    $GLOBALS['notice'] = '邮箱和用户名错误';
    return;
  }

// 登录标识
  session_start();
  $_SESSION['current_login_user'] = $user;
  header('Location: index.php');


  // $GLOBALS['notice'] = '登录成功';


  // 持久化
  // 响应


}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  # 判断是否有post提交
  login();

}

//退出
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
  session_start();
  //删除session
  unset($_SESSION['current_login_user']);
}


 ?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" type="text/css" href="/static/assets/vendors/animates/animate.css">
</head>
<body>
  <div class="login">

    <!-- 可以通过在 form 上添加 novalidate 取消浏览器自带效验功能 -->
    <!-- autocomplete= 'off' 关闭浏览器自动完成功能 -->
    <form class="login-wrap  <?php echo isset($notice) ? ' shake animated' : '' ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method = "post" novalidate autocomplete = "off">
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($notice)) : ?>

      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $notice; ?>
      </div>
    <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus 
        value="<?php 
        //状态保持
        echo empty($_POST['email']) ? '' : $_POST['email']; ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password"  name="password" type="password" class="form-control" placeholder="密码" >
      </div>
      <button class="btn btn-primary btn-block" href="index.html">登 录</button> 
    </form>
  </div>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script type="text/javascript">
    
    $(function($){
      //确保页面加载完后再执行
      //TODO:根据用户输入的邮箱显示头像

      //实现：
      //时机: 邮箱失去焦点，并且能拿到正确的邮箱时==>使用正则
      //事情：获取这个邮箱对应的头像地址，并展示到img元素上

      var emailts =  /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/;
      $('#email').on('blur',function(){

        var valu = $(this).val();
        //忽略文本框为空或者不是一个邮箱
        if (!valu || !emailts.test(valu)) return;

        //用户输入了一个合理的邮箱
        //通过ajax 请求服务端接口，让这个服务端接口连接数据库获取头像地址

        $.get('/admin/api/avatar.php',{email:valu},function(res){

          //res ==> 邮箱对应的头像地址
          if (!res) return;

          //展示到img 元素上
          // $('.avatar').attr('scr',res);
          //添加过度效果
          $('.avatar').fadeOut(function(){

            $(this).on('load',function(){
              $(this).fadeIn();
            }).attr('src',res)

          });

        })

      })


    })

    
  </script>
</body>
</html>
