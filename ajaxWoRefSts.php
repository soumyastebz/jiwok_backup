<?php
session_start();
require_once 'includeconfig.php';
require_once 'includes/classes/Referrel/class.referal.php';

/*ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);*/

$referal		=	new referal();


$woFlexid	=	0;
if($_REQUEST["woFlexid"]!=""){
	$woFlexid	=	$_REQUEST["woFlexid"];	
}

$action	=	"getSts";
if($_REQUEST["action"]!=""){
	$action	=	$_REQUEST["action"];	
}

$user_id	=	0;
if($_SESSION['user']['userId']!=""){
	$user_id	=	$_SESSION['user']['userId'];
}

if($action=="getSts"){
	$isFb	=	1;
	$isTw	=	1;
	
	//$selQry	=	"select * from jiwok_refferel_wo_share where user_id=".$user_id." and wo_flexid='".$woFlexid."'";
	$selQry	=	"select * from jiwok_refferel_wo_share where user_id=".$user_id." order by id desc";
	$dataArray		=	$referal->dbSelectAll($selQry);
	if(count($dataArray) > 0){
		
		$timeDiff	=	strtotime(date('Y/m/d H:i:s'))-strtotime($dataArray[0]['wo_time']);
		
		if(($timeDiff/3600)<24){
			$isFb	=	0;
			$isTw	=	0;
		}
		
		/*if($dataArray[0]['isfb']==1){
			$isFb	=	0;
		}
		if($dataArray[0]['istw']==1){
			$isTw	=	0;
		}*/
	}
	//$result	=	array("isFb"=>$isFb,"isTw"=>$isTw,"timeDiff"=>($timeDiff/3600));
	echo $isFb."#".$isTw;
}else if($action=="setShared"){
	$isFb	=	0;
	if($_REQUEST["isFb"]!=""){
		$isFb	=	$_GET["isFb"];	
	}
	
	$isTw	=	0;
	if($_REQUEST["isTw"]!=""){
		$isTw	=	$_GET["isTw"];	
	}
	
	$selQry	=	"select * from jiwok_refferel_wo_share where user_id=".$user_id."";
	$dataArray		=	$referal->dbSelectAll($selQry);
	if(count($dataArray) > 0){
		if($isFb!="1"){
			$updateArray	=	array("istw"=>$isTw,"wo_time"=>date('Y/m/d H:i:s'));
		}else if($isTw!="1"){
			$updateArray	=	array("isfb"=>$isFb,"wo_time"=>date('Y/m/d H:i:s'));
		}else{
			$updateArray	=	array("isfb"=>$isFb,"istw"=>$isTw,"wo_time"=>date('Y/m/d H:i:s'));
		}
		$updateCond	=	$referal->arrayDbUpdateOne($updateArray);
		$referal->dbUpdate("jiwok_refferel_wo_share",$updateCond,"`user_id`=".$user_id."");
	}else{
		$insertDbFields	=	"`user_id`,`wo_flexid`,`isfb`,`istw`,`wo_time`";
		$insertDbValues	=	$user_id.",'".$woFlexid."',".$isFb.",".$isTw.",'".date('Y/m/d H:i:s')."'";
		$referal->dbInsertSingle("jiwok_refferel_wo_share",$insertDbFields,$insertDbValues);
	}
	$result	=	array("status"=>"updated successfully");
	echo "updated successfully";
	//echo json_encode($result);
}


exit;	
?>