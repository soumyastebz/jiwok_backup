<?php
session_start();
include_once('includeconfig.php');
include_once("includes/classes/class.programs.php");
if($lanId=="")
     $lanId=1;
$errorMsg = '';	 
$userid         = $_SESSION['user']['userId'];	 
$subscribeid    = trim($_REQUEST['subscribeid']);
$objGen     	= new General();
$objPgm     	= new Programs($lanId);
$parObj 		= new Contents();
$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
$arrayData		= $returnData['general'];
$unsubscribe	= $objPgm->_unsubscribeTrainingConfirm($subscribeid,$userid);
echo "success";
?>
