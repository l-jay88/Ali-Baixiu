<?php 


include '../../config.php';
// echo DB_HOST;
//TODO: 根据接收客户端上传的邮箱并返回给客户端头像地址

if (empty($_GET['email'])) {
	exit('邮箱上传错误');
}

$email = $_GET['email'];


//建立数据库连接
$conn = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);

if (!$conn) {
	exit('数据库连接不成功');
}
$query = mysqli_query($conn,"select avatar from users where email = '{$email}' limit 1;");

if (!$query) {
	exit('没有找到该邮箱');
}

$fetch = mysqli_fetch_assoc($query);
// array(1) { ["avatar"]=> string(26) "/static/uploads/avatar.jpg" }

if (!$fetch) {
	exit('数据获取失败');
}

echo $fetch['avatar'];




 ?>