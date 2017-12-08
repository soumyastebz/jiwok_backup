<?php
session_start();
require_once 'includeconfig.php';
require_once 'includes/classes/Referrel/class.referal.php';

/*ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);*/

$referal		=	new referal();

$orgin	=	"isfb";
if($_REQUEST["orgin"]!=""){
	$orgin	=	$_REQUEST["orgin"];	
}

$sts	=	0;
if($_REQUEST["sts"]!=""){
	$sts	=	$_REQUEST["sts"];	
}

$isPosted	=	0;
if($_REQUEST["isPosted"]!=""){
	$isPosted	=	$_REQUEST["isPosted"];
}

$user_id	=	0;
if($_SESSION['user']['userId']!=""){
	$user_id	=	$_SESSION['user']['userId'];
}



$name	=	"twtChkBx";
$element	=	"twtChkCntr";
$isfb	=	1;
$istw	=	$sts;


if($orgin=="isfb"){
	$name	=	"fbChkBx";
	$element	=	"fbChkCntr";
	$isfb	=	$sts;
	$istw	=	1;
}else if(($orgin=="WOPopup")||($orgin=="showComment")||($orgin=="showHistory")){
	$name	=	"fbChkBxPopup";
	$element	=	"fbChkCntrPopup";
	$isfbPopup	=	$sts;
}

$stsTxt	=	'';
if($sts=="1"){
	$stsTxt	=	"checked='checked'";
}


if(($orgin=="WOPopup")||($orgin=="showComment")||($orgin=="showHistory")){
	
	if($isPosted==1){
		$sts	=	1;
	}else{
		if($sts==0){
			$sts	=	1;
		}else{
			$sts	=	0;	
		}
	}
	
	if($orgin=="WOPopup"){
		if($isPosted==0){
			$columnName	=	"showWo";
		}else{
			$columnName	=	"wopost";
		}
	}else if($orgin=="showComment"){
		if($isPosted==0){
			$columnName	=	"showComment";
		}else{
			$columnName	=	"commentpost";
		}
	}else if($orgin=="showHistory"){
		if($isPosted==0){
			$columnName	=	"showHistory";
		}else{
			$columnName	=	"historypost";
		}
	}
	
	
	$selQry	=	"select * from jiwok_fbshare_popup where user_id=".$user_id;
	$dataArray		=	$referal->dbSelectAll($selQry);
	if(count($dataArray) > 0){
		$updateArray	=	array($columnName=>$sts);
		$updateCond	=	$referal->arrayDbUpdateOne($updateArray);
		$referal->dbUpdate("jiwok_fbshare_popup",$updateCond,"`user_id`=".$user_id);
	}else{
		$insertDbFields	=	"`user_id`,`".$columnName."`";
		$insertDbValues	=	$user_id.",".$sts;
		$referal->dbInsertSingle("jiwok_fbshare_popup",$insertDbFields,$insertDbValues);
	}
	
}else{
	$selQry	=	"select * from jiwok_refferel_wo where user_id=".$user_id;
	$dataArray		=	$referal->dbSelectAll($selQry);
	if(count($dataArray) > 0){
		$updateArray	=	array($orgin=>$sts);
		$updateCond	=	$referal->arrayDbUpdateOne($updateArray);
		$referal->dbUpdate("jiwok_refferel_wo",$updateCond,"`user_id`=".$user_id);
	}else{
		$insertDbFields	=	"`user_id`,`isfb`,`istw`";
		$insertDbValues	=	$user_id.",".$isfb.",".$istw;
		$referal->dbInsertSingle("jiwok_refferel_wo",$insertDbFields,$insertDbValues);
	}
}

$chkTpl	=	"<input type='checkbox' name='#NAME#' id='#NAME#' value='fbchked' #CHKED# />";

$elemenetData	=	str_replace("#NAME#",$name,$chkTpl);
$elemenetData	=	str_replace("#CHKED#",$stsTxt,$elemenetData);

$result	=	array("element"=>$element,"elemenetData"=>$elemenetData);

echo json_encode($result);
exit;
?>