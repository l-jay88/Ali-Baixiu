<?php 

//TODO:接收categories 传过来的 id 值

require_once '../function.php';

$get_id = $_GET['id'];

var_dump($get_id);

// 注意：sql 注入
//==> '1 or 1 = 1'
// (int)1 or 1 = 1 ==>1 


xiu_excute("delete from categories where  id in ({$get_id});");

header('Location: categories.php');

