<?php 



	session_start();



	include_once('includeconfig.php');



	include_once("includes/classes/class.programs.php");



	include_once('includes/classes/class.CMS.php');



	if($lanId=="")



		 $lanId=1;



$gift_card_free_period	=	$_REQUEST['gift'];

$type=trim($_REQUEST['type']);



	$objGen     	= new General();



	$objTraining	= new Programs($lanId);



	$parObj 		= new Contents('oxylane_payment.php');



	$objCMS			= new CMS($lanId);



	//collecting data from the xml for the static contents



	$returnData		= $parObj->_getTagcontents($xmlPath,'contents','label');



	$arrayData		= $returnData['general'];



	//collecting All training program category for displaying



	$getAllTrainCats	= $objTraining->_getAllGenItem('category',$lanId);



	



	$contentTitle	= urldecode($_REQUEST['title']);



	$contents 			= $objCMS->_getContent($contentTitle, $lanId);







	if ($contents=='') {



	    // get title form the request



		$contentTitle		=	base64_decode($_REQUEST['title']);



		//get contents according to the title



		$contents 			= $objCMS->_getContent($contentTitle,$lanId);



	}



	//print_r($contents);



?>

<?php /*?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">



<html xmlns="http://www.w3.org/1999/xhtml">



<head>



<meta http-equiv="Content-Type" content="text/html; charset=UTF-8;" />



<?php if($contents['content_keywords']!=''){ ?><meta name="keywords" content="<?php echo($objGen->_output($contents['content_keywords']));?>" /><?php } ?>



<title><?php echo($objGen->_output($contents['content_browser_title'])); ?></title>



<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />



<link href="resources/style.css" rel="stylesheet" type="text/css" />







</head>



<body">

<?php */?>
<style>
#contentAreaInnerContents {
    background:#fff;
    clear: both;
    margin: 0 auto 25px;
    min-height: 400px;
    *width:993px;
	#width:993px;
	_width:993px;
	width: 1006px;
	float:left;
}
.left_col_results {
   
    float: left;
    margin: 0;
    padding: 0;
    width: 570px;
  margin-left:210px;
}
.Giftcert_Top_Bg {
    background-image: url("../images/gift_cert_top_BG.jpg");
    background-repeat: no-repeat;
    float: left;
    height: 143px;
    padding-top: 7px;
    width: 527px;
}
.Giftcert_Btm_Bg {
    background-image: url("../images/gift_cert_btm_BG.jpg");
    background-repeat: no-repeat;
    float: left;
    height: 13px;
    width: 527px;
}
.Giftcert_Top_Bg{width:497px;text-align:center; font-family:Arial, Helvetica, sans-serif; font-size:13px; font-weight:bold; color:#6699CC; height:98px;  padding:45px 15px 0 15px;}
</style>

<?php
include("header.php");
//~ include("menu.php");

?>
<div id="container">
<div id="wraper_inner">
<div id="contentAreaInnerContents">



  <div id="pageHead">



  	<h1><?=$objGen->_output($contents['content_display_title']);?></h1>



  </div>



  <div class="left_col_results" >



	<div class="Giftcert_Main_Container" style="margin:30px 0 30px 20px;">

	</div>

	<span class="Giftcert_Top_Bg"><?php if($type!=1){?><?=$parObj->_getLabenames($arrayData,'oxysuccessone','name').$gift_card_free_period." ".$parObj->_getLabenames($arrayData,'oxysuccesstwo','name');}

	else {?><?=$parObj->_getLabenames($arrayData,'giftsuccess','name').$gift_card_free_period." ".$parObj->_getLabenames($arrayData,'giftsuccesstwo','name');}?><br /><br/>

	<a href="userArea.php" style="font-size:13px; font-weight:bold; color:#19A602; text-decoration:underline;">Cliquez ici pour commencer >> </a>

	</span>

	<span class="Giftcert_Btm_Bg"></span>

  </div>



  	


</div></div>
<div class="clear"></div>
</div>



<?php include('footer.php');?>



</body>



</html>

