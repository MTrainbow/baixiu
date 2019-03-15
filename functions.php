<?php 
require_once 'config.php';

/**
 * 封装大家公用的函数
 */
session_start() ;
/**
 * 获取当前登录用户信息，如果没有获取到则自动跳转到登录页面
 * @return object [description]
 */
function xiu_get_current_user(){
	if (empty($_SESSION['login-info'])) {
		header('Location: /admin/login.php') ;
		exit();
	}
	return $_SESSION['login-info'] ;
}



function xiu_query($sql){
	$conn = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME) ;
	if (!$conn) {
		exit('连接失败') ;
	}
	$data[] = $conn;
	mysqli_set_charset($conn,'utf8');
	$query = mysqli_query($conn,$sql);
	if (!$query) {
		//查询失败
		return false ;
	}
	$data[] = $query;
	return $data;
}

function xiu_connect () {
  $connection = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME) ;

  if (!$connection) {
    // 如果连接失败报错
    die('<h1>Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() . '</h1>');
  }

  // 设置数据库编码
  mysqli_set_charset($connection, 'utf8');

  return $connection;
}

/**
 * 获取数据库多条信息
 * @return [type] [description]
 */
function xiu_fetch_all($sql){

	$connection = xiu_connect();

 	$data = array();

 	if ($result = mysqli_query($connection, $sql)) {
    // 查询成功，则获取结果集中的数据
    while ($row = mysqli_fetch_assoc($result)) {
      $data[] = $row;
    }
    mysqli_free_result($result);
  }

  	mysqli_close($connection);

  	return $data;

} 

/**
 * 获取数据库单条数据
 * @return [type] [description]
 */
function xiu_fetch_one($sql) {
	
	$result = xiu_fetch_all($sql);	
	return isset($result[0]) ? $result[0] : null ; 
	
}

/**
 * 查询数据库--增删改
 * @return [type] [description]
 */

function xiu_execute($sql) {

	$conn = xiu_query($sql)[0];
	$affend_rows = mysqli_affected_rows($conn);
	return $affend_rows;
}

/**
 * 执行一个查询语句，查询数据
 * @param  [string] $sql [执行的查询语句]
 * @return [array]      [二维数组]
 */
function xiu_query_demo ($sql) {

  // 建立数据库连接

  $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

  if (!$connection) {

    die('<h1>Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() . '</h1>');

  }

  // 定义结果数据容器，用于装载查询到的数据

  $data = array();

  // 执行参数中指定的 SQL 语句

  if ($result = mysqli_query($connection, $sql)) {

    // 查询成功，则获取结果集中的数据

    // 遍历每一行的数据

    while ($row = mysqli_fetch_array($result)) {

      $data[] = $row;

    }

    // 释放结果集

    mysqli_free_result($result);

  }

  // 关闭数据库连接

  mysqli_close($connection);

  return $data;
}
