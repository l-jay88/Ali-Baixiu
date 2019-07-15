<?php 

//TODO:接收posts.php 传过来的 id 值

require_once '../function.php';

$get_id = $_GET['id'];

if (empty($get_id)){
	
	exit('缺少必要参数');
}

var_dump($get_id);

// 注意：sql 注入
//==> '1 or 1 = 1'
// (int)1 or 1 = 1 ==>1 


xiu_excute("delete from posts where  id in ({$get_id});");
//http 中的 referer 用来标识当前请求来源
header('Location:'.$_SERVER['HTTP_REFERER']);