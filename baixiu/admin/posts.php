<?php 

//判断是否已经登录了
require_once '../function.php';
xiu_get_current_user();

//当前页数
$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$size = 20;


//越过多少页
$offset = ($page - 1) * $size;


//接收筛选参数
//=============================
$where = '1 = 1';
$serch = '';
//分类筛选
if (isset($_GET['category']) && $_GET['category'] != 'all') 
{
  $where .= ' and posts.category_id = '. $_GET['category'];
  $serch .= '&category='.$_GET['category'];
}
//状态筛选
if (isset($_GET['status']) && $_GET['status'] != 'all') {
  $where .= " and posts.status = '{$_GET['status']}'";
  $serch .= '&status='.$_GET['status'];
}

if ($page < 1) {
  //跳转到第一页
  header('Location: /admin/posts.php?page=1'.$serch);
}

//获取全部数据遍历到列表
//使用数据库联合查询
//================================
$posts_cont = xiu_fetch_all("select 
posts.id,
posts.title,
users.nickname as user_name,
categories.`name` as category_name,
posts.created,
posts.`status`
from posts 
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id
where {$where}
order by posts.created desc
limit {$offset},{$size}");

//分类查询
$categories = xiu_fetch_all("select * from categories");



//处理分页页码====================
//求出最大页码
$total_count = (int)xiu_fetch_once("select 
count(1) as num
from posts
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id
where {$where}
")['num'];
$total_page = (int)ceil($total_count/$size);

if ($page > $total_page) {
  header("Location: /admin/posts.php?page={$total_page}".$serch);
}



//当页数小于5页的时候
// $total_page = 3;

$visiables = 5;
$region = ($visiables-1) / 2;
$begin = $page - $region;//开始页码
$end = $begin + $visiables;//结束页码


//确保begin 最小为 1
if ($begin <= 0) {
  $begin = 1;
  $end = $begin + $visiables;
}


//确保$end 不超过最大值
if ($end > $total_page + 1) {
  //$end 超出范围
  $end = $total_page + 1;
  $begin = $end - $visiables;
  if ($begin <=0) {
    $begin = 1;
  }
  
}

//处理上一页下一页
//==================
function privious($page){
  return $page-1;

}






//处理数据格式转换==============
//转换状态显示
function convert_status($status){
  $dict = array(
    'published' => '已发布',
    'drafted' => '草稿',
    'trashed' => '回收站'
   );
  return isset($status) ? $dict[$status] : '未知';
}

function convert_data($created){
  //将接收到的时间数据转化为一个时间戳
  $timestamp = strtotime($created);

  return date('Y年m月d日<b\r>H:i:s',$timestamp);
}


//===versioin1====
// function get_category_id($category_id){
//   return xiu_fetch_once("select `name` from categories where id={$category_id};")['name'];
// }
// function get_users_id($users_id);
//   return xiu_fetch_once("select nickname from users where id={$users_id};")['nickname'];
// }

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

    <?php include 'inc/navbar.php'; ?>
    
    </nav>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>" method='get'>
          <select name="category" class="form-control input-sm" >
            <option value="all">所有分类</option>
            <?php foreach ($categories as $value) : ?>
            <option  <?php echo isset($_GET['category']) && $_GET['category'] === $value['id'] ? 'selected' : ''; ?> value="<?php echo $value['id'] ?>"><?php echo $value['name']; ?></option>
          <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all" <?php echo isset($_GET['status']) && $_GET['status'] === 'all' ? ' selected' : '' ?>>所有状态</option>
            <option value="drafted"<?php echo isset($_GET['status']) && $_GET['status'] === 'drafted' ? ' selected' : '' ?>>草稿</option>
            <option value="published"<?php echo isset($_GET['status']) && $_GET['status'] === 'published' ? ' selected' : '' ?>>已发布</option>
             <option value="trashed"<?php echo isset($_GET['status']) && $_GET['status'] === 'trashed' ? ' selected' : '' ?>>回收站</option>

          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <?php if ($page != 1) : ?>
          <li><a href="?page=<?php echo privious($page).$serch; ?>">上一页</a></li>
        <?php endif ?>
          <?php for ($i=$begin; $i < $end ; $i++) :?>
          <li <?php echo $i === $page ?'class="active"' : '' ?>><a href="?page=<?php echo $i.$serch ?>" ><?php echo $i; ?></a></li>
        <?php endfor ?>
        <?php if ($page != $total_page) :?>
          <li><a href="?page=<?php echo $page+1 . $serch; ?>">下一页</a></li>
        <?php endif ?>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts_cont as $value) :?>
          <tr>
            <td class="text-center"><input type="checkbox"></td>
            <td><?php echo $value['title']; ?></td>
            <td><?php echo $value['user_name']; ?></td>
            <td><?php echo $value['category_name']; ?></td>
            <td class="text-center"><?php echo convert_data($value['created'] );?></td>
            <td class="text-center"><?php echo convert_status($value['status']);?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="posts_delet.php?id=<?php echo $value['id'] ;?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
        <?php endforeach ?>
          
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'posts'?>
 <?php include 'inc/aside.php';?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
