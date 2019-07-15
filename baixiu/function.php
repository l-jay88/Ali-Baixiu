<?php 

include 'config.php';

//放置公共代码函数

//定义函数时注意：函数名与内置函数名冲突的问题
// js判断方式： typeof fn === 'function'
// php判断方式： function_exist('fn')

session_start();

function xiu_get_current_user(){
	if (empty($_SESSION['current_login_user'])) {

		header('Location: login.php');

		exit();//后续代码没有必要执行
	}

	return $_SESSION['current_login_user'];
}

//封装数据库连接函数

function xiu_connet($slet){

	$conn = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);

	if (!$conn) {
		exit('数据库连接失败');
	}

	$query = mysqli_query($conn,$slet);
	if (!$query) {
		return false;
	}

	$xiu_connet_arr = ['conn' => $conn,'query' => $query];
	return $xiu_connet_arr;

}



//获取多条数据

function xiu_fetch_all($slet){

	// xiu_connet($slet);
	$query= xiu_connet($slet)['query'];
	// $conn = xiu_connet($slet)['conn'];

	while ($row = mysqli_fetch_assoc($query)) {
		$result[] = $row;
	}

	// mysqli_free_result($query);
	// mysqli_close($conn);

	return $result;


}

//测试函数
// $fect = xiu_fetch_all("select count(1) from users;");
// var_dump($fect);

//获取单条数据

function xiu_fetch_once($selt){
	$ret = xiu_fetch_all($selt);
	return isset($ret[0]) ? $ret[0] : "";
}

// var_dump(xiu_fetch_once("select * from categories where id=147 limit 1 ;"));

//增删改数据
function xiu_excute($slet){
	// $query= xiu_connet($slet)['query'];
	$conn = xiu_connet($slet)['conn'];

	//传入的一定是连接对象
	$rows = mysqli_affected_rows($conn);

	//判断是否删除成功
	if ($rows <= 0) {
		var_dump('删除失败');
		return false;
	}

	//炸掉连接桥（切断与数据库的联系，因为数据库的连接数量有限）
	// mysqli_close($conn);
	return $rows;

}

//测试
// $add_categories = xiu_excute("update categories set `name`='庄周',slug = '561126' where id=152;");
// var_dump($add_categories);





