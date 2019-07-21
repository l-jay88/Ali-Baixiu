<?php 


 //TODO:接收客户端 AJAX 请求，返回评论数据
require_once '../../function.php';

//获取客户端传过来的分页页码
$page = empty($_GET['page']) ? 1 : intval($_GET['page']) ;

$length = 30;
//根据页码计算越过多少条
$offset = ($page - 1) * $length;
$sql = sprintf('select 
comments.*,
posts.title
from posts
inner join comments on comments.post_id = posts.id
order by comments.created desc
limit %d,%d;',$offset,$length);


//查询所有的评论数据
$comments = xiu_fetch_all($sql);
//查询总条数
$total_comments= xiu_fetch_once("select 
count(1) as num
from comments
inner join posts on comments.post_id = posts.id;")['num'];
$total_pages = ceil($total_comments /$length);


//因为网络之间传递数据只能是字符串
// 所以我们先将数据转成字符串（序列化）
$json = json_encode(array(
	'total_pages' => $total_pages,
	'comments' => $comments
));
//告诉客户端响应体数据类型为 json
header('Content-Type: application/json');
//响应给客户端
echo $json;
