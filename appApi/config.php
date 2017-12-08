<?php

   if($_SERVER['HTTP_HOST']	==	'10.0.0.8')
   { 
	$host      = "localhost";
	$user      = "reubromail";
	$password  = "reubromail";
	$database1 = "jiwok_com";
	$database2 = "jiwok_app";
   }else{
	$host      = "localhost";
	$user      = "jiwok_com_new";
	$password  = "mHXqAm1l";
	$database1 = "jiwok_com";
	$database2 = "jiwok_app";
   }
   $database3 = "jiwok_appLogEvent";// for benoit to  logUserEvent
  try{
	$GLOBALS['db_com']= new PDO("mysql:host=$host;dbname=$database1", $user, $password,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
	}catch(PDOException $e){
		die('Could not connect to the database:' . $e);
	}
	try{
	$GLOBALS['db_app']= new PDO("mysql:host=$host;dbname=$database2", $user, $password,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
	}catch(PDOException $e){
		die('Could not connect to the database:' . $e);
	}
	try{
	$GLOBALS['db_logtype']= new PDO("mysql:host=$host;dbname=$database3", $user, $password,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
	}catch(PDOException $e){
		die('Could not connect to the database:' . $e);
	}
?>
