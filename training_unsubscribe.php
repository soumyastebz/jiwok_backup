<?php

session_start();

include_once('includeconfig.php');

include_once("includes/classes/class.programs.php");

if($lanId=="")

     $lanId=1;



$errorMsg = '';	 

$userid = $_SESSION['user']['userId'];	 

$subscribeId = trim($_REQUEST['subscribeId']);

$flexid = trim($_REQUEST['flexid']);

//$subscribeId = 5;

$objGen     	= new General();

$objPgm     	= new Programs($lanId);

$parObj 		= new Contents();



$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');

$arrayData		= $returnData['general'];



$unsubscribe	= $objPgm->_unsubscribeTraining($subscribeId,$userid);

$delete = $objPgm->_deleteFromQueue("program_queue",$flexid,$userid);

echo "success";
?>

