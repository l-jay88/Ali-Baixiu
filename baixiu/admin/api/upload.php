<?php  

//TODO: 接收客户端上传文件，并返回文件地址
if (empty($_FILES['avatar'])) {
	exit('请上传文件');
}

$avatar = $_FILES['avatar'];
if ($avatar['error'] != UPLOAD_ERR_OK) {
	exit('上传文件失败');
}

//效验类型 大小

//移动文件到网站范围内
$ext = pathinfo($avatar['name'],PATHINFO_EXTENSION);//获取扩展名
$target = '../../static/uploads/img-'.uniqid().'.'.$ext;
if (!move_uploaded_file($avatar['tmp_name'], $target)) {
	exit('上传失败');
}
//上传成功
echo substr($target, 5);