<?php 


 require_once '../../function.php';

 if (empty($_GET['id'])) {
 	exit('缺少必要参数');
 }

 $id = $_GET['id'];

 $rows = xiu_excute("delete from comments where id in({$id});");

 header('Content-Type: appliction/json');

 echo  json_encode($rows > 0);