<?php
session_start();
require_once 'includeconfig.php';
require_once 'includes/classes/Referrel/class.referal.php';

$referal		=	new referal();

$user_id	=	0;
if($_SESSION['user']['userId']!=""){
	$user_id	=	$_SESSION['user']['userId'];
}

$fieldName	=	"commentpost";
if($_REQUEST['fieldName']!=""){
	$fieldName	=	$_REQUEST['fieldName'];
}


echo $selQry	=	"select * from jiwok_fbshare_popup where user_id=".$user_id;
$dataArray		=	$referal->dbSelectAll($selQry);
if(count($dataArray) > 0){
	echo $dataArray[0][$fieldName];
}else{
	echo "";	
}
exit;
?>