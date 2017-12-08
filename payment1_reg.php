<?php 
ini_set("session.gc_maxlifetime", "86400");
ini_set("session.gc_divisor", "1");
ini_set("session.gc_probability", "1");
ini_set("session.cookie_lifetime", "86400");
ini_set("session.save_path", "/home/sites_web/client/www.jiwok.com/session_dir");
session_set_cookie_params(86400 , '/', '.jiwok.com'); 

session_start();

//for chrome session issue
if($_SESSION["chrome_language_return"])
	{
		$_SESSION['language']	=	$_SESSION["chrome_language_return"];
		unset($_SESSION["chrome_language_return"]);
		if($_SESSION['language']['langId']	==	"1")
			{
				header("location:http://www.jiwok.com/en/payment1_reg.php");
				exit;
			}
		if($_SESSION['language']['langId']	==	"2")
			{
				header("location:http://www.jiwok.com/payment1_reg.php?msg=1");
				exit;
			}
	}
	
if(($_SESSION['brand'] == "parismarathon" || $_SESSION['brand'] == "semideparis") && $_SESSION['language']['langId'] ==	"1" && $_SESSION['userreg']==1):			
			header("Location:http://".$_SESSION['brand'].".jiwok.com/en/payment1_reg_beta.php?orig=ys");
			exit;
endif;

include_once('includeconfig.php');

if($_SESSION['language']['langId']=='1')
	{
		$lanVar="en/";
	}
else
	{
		$lanVar="";
	}
if(isset($_SESSION['brand']) && $_SESSION['userreg']==1) {		
		header("Location:http://".$_SESSION['brand'].".jiwok.com/payment1_reg.php?orig=ys");
		exit;		
}
if(isset($_SESSION['giftregcheck']))
{
  if($_SESSION['giftregcheck']==1)
  {
     header("Location:".$lanVar."userreg_complete.php");
	exit;
  }
}
if($lanVar!='')
{
     $pattern = "/(\/)(en)(\/)/";
	 $url_execute=$_SERVER['REQUEST_URI'];
	 $check_val=preg_match($pattern,$url_execute);
	 if(!$check_val)
	 {
	  header("Location:".$lanVar."payment1_reg.php");exit;  
	 }
}
if(isset($_SESSION['login']['step2']) && $_SESSION['login']['step2'] == 'success'):		
		//$_SESSION['userregcheck'] = $_SESSION['login']['step2'] ;
		unset($_SESSION['login']['step2']);
		header("Location:userArea.php");
		exit;
endif;
include_once('includeconfig.php');
include_once('regDetail.php');
include_once("includes/classes/class.member.php");
include_once("includes/classes/class.discount.php");
include_once('includes/classes/class.CMS.php');
include_once("includes/classes/class.programs.php");
include_once("paybox_reg.php");


$objGen   	=	new General();

$parObj 	=   new Contents('payment.php');

$objMem		= 	new Member();

$objDisc	= 	new Discount();

$objCMS		= 	new CMS($lanId);

$objPgm     = 	new Programs($lanId);


//============ For Navigating Brand ================//






	// for reproducing into orginal alphabets

	function normalze($string)

	{

	$fReplace2 	 = array('"','"','');

	$trans 	 = array('�','�','�');

	$trans2 = array_combine($trans, $fReplace2);

	$string = utf8_decode($string);

	return utf8_encode(strtr($string,$trans2));

	}


if(isset($_SESSION['login']['user_email'])){

$useremail	=	$_SESSION['login']['user_email']; //if user is coming from the registration page.

}
elseif(isset($_SESSION['user']['user_email'])){

$useremail	=	$_SESSION['user']['user_email']; //get user id from the session variable/if user is coming from the UserArea Page.
}



if(isset($_SESSION['login']['userId'])){

$userId		=	$_SESSION['login']['userId']; //if user is coming from the registration page.

}

elseif(isset($_SESSION['user']['userId'])){

$userId		=	$_SESSION['user']['userId']; //get user id from the session variable/if user is coming from the UserArea Page.

}else{

header('location:login_failed.php?msg='.base64_encode(4));

}

//print_r($_SESSION);

if($lanId=="") $lanId=1;

		

		 // get title form the request

		$contentTitle		=	'PAYMENTCONDITION';

		//get contents according to the title

		$contents 			= $objCMS->_getContent($contentTitle,$lanId);

		//collecting data from the xml for the static contents

		$returnData			= 	$parObj->_getTagcontents($xmlPath,'payment','label');

		$arrayData			= 	$returnData['general'];

		//get language name

		$lanName			=	$objMem->_getLanName($lanId);

		

		//get user free period remaining

		$freedays 			= 	$objPgm->_getFreeDays($userId);

		$freeBalanceDays 	= 	$objPgm->_findBalanceFreeDays($userId,$freedays);

		if($objPgm->_checkUserPaymentPeriod($userId))

			 {

			 $balanceDays	=	$objPgm->_getUserPaymentBalanceDays($userId);

		}

		elseif($freeBalanceDays>0){

			$balanceDays	=	$freeBalanceDays;

		}else{

			$balanceDays	=	0;

		}


		$payDetails = $objPgm->_getUserPaymentTemp($userId);


		///for backend page updation
		/*if($lanId == 1)
			{
				$selectSettings		=	"select * from settings";
				
				$result				=	$GLOBALS['db']->getAll($selectSettings,DB_FETCHMODE_ASSOC);
				
				foreach($result as $key=>$data){
				
				$reffPara			=   $objGen->_output($data['membership_fee']);
				}
			}
		else
			{*/
				$reffPara			= trim(stripslashes($payDetails['pay_id'])); 
			//}

		if(trim(stripslashes($payDetails['discUser_id'])) != "" && (trim(stripslashes($payDetails['discUser_id']))) != 0){

		$reffPara			=	$reffPara.','.trim(stripslashes($payDetails['discUser_id'])); 

		}else{

		$reffPara			=	$reffPara.','.'0';

		}

		if(trim(stripslashes($payDetails['discCode'])) != ""){

		$reffPara			=	$reffPara.','.trim(stripslashes($payDetails['discCode']));

		}else{

		$reffPara			=	$reffPara.','.'0';

		}

		if($userId != ''){

		$reffPara			=	$reffPara.','.$userId;

		}else{

		$reffPara			=	$reffPara.','.'0';

		}

		if(trim(stripslashes($payDetails['discId'])) != ""){

		$reffPara			=	$reffPara.','.trim(stripslashes($payDetails['discId']));

		}else{

		$reffPara			=	$reffPara.','.'0';

		}

		if(trim(stripslashes($payDetails['ActReffId'])) != ""){

		$reffPara			=	$reffPara.','.trim(stripslashes($payDetails['ActReffId']));

		}else{

		$reffPara			=	$reffPara.','.'0';

		}

		if(trim(stripslashes($payDetails['freedays'])) != ""){

		$reffPara			=	$reffPara.','.trim(stripslashes($payDetails['freedays']));

		}else{

		$reffPara			=	$reffPara.','.'0';

		}

		$reffPara			= $reffPara.','."reg";

		$_SESSION['payment']['payFee'] = trim(stripslashes($payDetails['payFee']));

        $selectSettings		=	"select * from settings";

		$result				=	$GLOBALS['db']->getAll($selectSettings,DB_FETCHMODE_ASSOC);
		foreach($result as $key=>$data)
			{
				$framt			=   $objGen->_output($data['membership_fee']);
				$enamt			=   $objGen->_output($data['membership_feedollar']);
			}
		$enoneamt	=	$enamt * (1/$framt);
		$dolamt		=	$enoneamt	 * $_SESSION['payment']['payFee']; 

		if (isset($_SESSION['user']['user_email'])) {

			$_SESSION['payment']['user_email'] = $_SESSION['user']['user_email'];

		} else {

			$_SESSION['payment']['user_email'] = trim(stripslashes($payDetails['user_email']));

		}


statusRecord($useremail,'3rd step over','payment1_reg.php',$userId,'0','10');	

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8;" />

<title>jiwok</title>

<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />

<link href="resources/style.css" rel="stylesheet" type="text/css" />

<link href="resources/style2.css" rel="stylesheet" type="text/css" />

<script language="javascript" type="text/javascript">

function unsubscribe()
{
  document.getElementById('unsubscribePgm').style.display='block';
}

function hideUnsubscribe(styleid)

{

document.getElementById(styleid).style.display='none';

}

</script>

<!--[if lte IE 6]>

<style type="text/css">

#downloadList2 {width: 495px; margin: 15px 0px 0px 15px;}

.bottomContent {width: 930px; margin: 25px 0px 0px 15px;}

#downloadList2 h1 span {margin: -18px 0px 0px 0px;}

.right_col_holder {width: 453px;}

.flagLeft {margin: 7px 0px 0px 26px;}

</style>

<![endif]-->

<!--[if IE]>

<style type="text/css">

</style>

<![endif]-->

</head>

<body>

<?php include('header.php')?>

<div id="contentAreaInnerPayment2">

  <div id="pageHead">

  	<h1><?=$parObj->_getLabenames($arrayData,'payment','name');?></h1>

  </div>

  <div class="left_col_payment">

  		<!--<span class="contentHolderFontStyle"><?// echo $parObj->_getLabenames($arrayData,'tit1','name').$balanceDays.$parObj->_getLabenames($arrayData,'tit2','name');?><a href="#"><?//=$parObj->_getLabenames($arrayData,'tit3','name');?></a> <?//=$parObj->_getLabenames($arrayData,'tit4','name');?></span>-->

		<div id="downloadList2">

		<?php
	/*	if($lanId == 1)
			{
					$membqry="SELECT membership_fee,membership_feedollar FROM settings";
       				$membreslt = $GLOBALS['db']->getAll($membqry, DB_FETCHMODE_ASSOC);
	    			$fee_doll= $membreslt[0]['membership_feedollar'];					
			}	
		$price_tmp = str_replace('.', ',',$_SESSION['payment']['payFee']);*/?>

			<h1>
            <?php

$price_tmp = str_replace('.', ',',number_format($_SESSION['payment']['payFee'],2));

if($lanId == 1)
{
echo str_replace(".",".",  number_format($dolamt,2))." dollars";
 }
else
{
   echo $price_tmp;
//echo str_replace(".",",",  number_format($price_tmp,2))." euros (".str_replace(",",".",  number_format($price_tmp_doll,2))." dollars )";
}
				//if($lanId == 1){ echo $fee_doll."(".$price_tmp.")"; }
				//else  echo $price_tmp;
		//	echo $price_tmp ." Euros,";

				?> <?=$parObj->_getLabenames($arrayData,'permonthnew','name');?></h1>

			<span style="text-align:right"><a href="javascript:void(0)" onclick="return unsubscribe();"><?=$parObj->_getLabenames($arrayData,'condition1','name');?></a></span>

			<span class="serviceIncludes"><?=$parObj->_getLabenames($arrayData,'service','name');?> :</span>

			<ul>

				<li>

					<span class="downloadList2ArrowHolder"><img src="images/arrow_blue.jpg" alt="Jiwok" /></span>

					<span class="downloadList2Holder"><?=$parObj->_getLabenames($arrayData,'service1','name');?></span>

				</li>

				<li>

					<span class="downloadList2ArrowHolder"><img src="images/arrow_blue.jpg" alt="Jiwok" /></span>

					<span class="downloadList2Holder"><?=$parObj->_getLabenames($arrayData,'service2','name');?></span>

				</li>

				<li>

					<span class="downloadList2ArrowHolder"><img src="images/arrow_blue.jpg" alt="Jiwok" /></span>

					<span class="downloadList2Holder"><?=$parObj->_getLabenames($arrayData,'service3','name');?></span>

				</li>

				<li>

					<span class="downloadList2ArrowHolder"><img src="images/arrow_blue.jpg" alt="Jiwok" /></span>

					<span class="downloadList2Holder"><?=$parObj->_getLabenames($arrayData,'service4','name');?></span>

				</li>

				<li>

					<span class="downloadList2ArrowHolder"><img src="images/arrow_blue.jpg" alt="Jiwok" /></span>

					<span class="downloadList2Holder"><?=$parObj->_getLabenames($arrayData,'service5','name');?></span>

				</li>

			</ul>

		</div>

	<!--popup disply-->	

		<div class="wstyleCondition" id="unsubscribePgm" style="display:none;">

      <h2><img src="images/close-button.gif" onclick="hideUnsubscribe('unsubscribePgm');" alt="close" title="close" style="cursor:pointer;" border="0"/></h2>

      

      <h1><?=$parObj->_getLabenames($arrayData,'paymentPopupTitle','name');?></h1>

      <h3><p><span class="bottomContentHolder"><?=normalze(html_entity_decode($parObj->_getLabenames($arrayData,'paymentPopupPara1','name'), ENT_QUOTES, "utf-8"));?></span></p></h3>

	  <h3><p><span class="bottomContentHolder"><?=normalze(html_entity_decode($parObj->_getLabenames($arrayData,'paymentPopupPara2','name'), ENT_QUOTES, "utf-8"));?></span></p></h3>

	  <h3><p><span class="bottomContentHolder"><?=normalze(html_entity_decode($parObj->_getLabenames($arrayData,'paymentPopupPara3','name'), ENT_QUOTES, "utf-8"));?></span></p></h3>

    </div>

	<!--popup disply ends here-->	

  </div>


<?php if($lanId == 1) { ?>

<div class="right_col_holder" style="padding-top:100px;">

		<div class="right_col_payment" >

			<span class="rightColInnerTopBig" style="padding-top:0!important;" ><img src="images/right_box_btm_n4_reverse.jpg" alt="Jiwok" /></span>

		  <span class="rightColPaymentContentHolder"><img src="images/e_transactions.png" alt="e-Transactions" class="eTransactionsLogo" /> <br />

<img src="images/var_logos.jpg" alt="Jiwok" class="varLogosPsn" /></span>

<span class="rightColPaymentButtonHolder">

<?php
 	$amount				=	$_SESSION['payment']['payFee']*100;	

	$client_email		=	$_SESSION['payment']['user_email'];

 	$reffPara1			=	$reffPara.",0,0,0,0,0,".'Dollar'; // order reference for euro payment

 	$order_reference	=	$reffPara1;

	$language_name		=	"english";

 	$buttonValue		=	$parObj->_getLabenames($arrayData,'paynowdol','name');

	echo getPayBoxForm($amount, $client_email, $order_reference, $language_name,$buttonValue);

?>



</span><span class="rightColInnerBtmBig"><img src="images/right_box_btm_n4.jpg" alt="Jiwok" /></span>

		</div>
		
	</div>
	<?php } else { ?>
<div class="right_col_holder">

		<div class="right_col_payment">

			<span class="rightColInnerTopBig"><img src="images/right_box_btm_n4_reverse.jpg" alt="Jiwok" /></span>

		  <span class="rightColPaymentContentHolder"><img src="images/e_transactions.png" alt="e-Transactions" class="eTransactionsLogo" /> <br />

<img src="images/var_logos.jpg " alt="Jiwok" class="varLogosPsn" /></span>
<span class="rightColPaymentButtonHolder">

<?php
 	$amount=$_SESSION['payment']['payFee']*100;

	$client_email		=	$_SESSION['payment']['user_email'];

 	$reffPara1			=	$reffPara.",0,0,0,0,0,".'Euro'; // order reference for euro payment

 	$order_reference	=	$reffPara1;

	$language_name		=	"french";

 	$buttonValue		=	$parObj->_getLabenames($arrayData,'pay','name');

	echo getPayBoxForm($amount, $client_email, $order_reference, $language_name,$buttonValue);

?>



</span>

<span class="rightColInnerBtmBig"><img src="images/right_box_btm_n4.jpg" alt="Jiwok" /></span>

		</div>
		
		<div class="right_col_payment" style="width:438px;"> 
<span class="rightColInnerTopBigNew"><img alt="Jiwok" src="images/right_box_btm_n4_reverse.jpg">
</span><br clear="all" /> 
<span class="rightColPaymentContentHolder">
  <img class="varLogosPsn" style="margin: 10px 0 0px 30px;" alt="ckdo1" src="images/ckdo1.png" />
  <img class="varLogosPsn" style="margin: 10px 0 0px 27px;" alt="ckdo3" src="images/ckdo3.png" />
  <img class="varLogosPsn" style="margin: 10px 0 0px 27px;" alt="jiwokkdo4" src="images/jiwokkdo4.jpg" />
  </span>
  <span class="rightColPaymentButtonHolder">
  <form name="payment" action="oxylane_payment.php" method="post">
  <input type="submit" class="buttonOrangeLargeNew2" value="<?=$parObj->_getLabenames($arrayData,'paymentoxylanebutton','name');?>">
  </form>
  
  </span>
  <span class="rightColInnerBtmBig"><img alt="Jiwok" src="images/right_box_btm_n4.jpg"></span> 
  </div> 
  
  <?php } ?>
	</div>

	<?php include('payment_bottom.php');?>

</div>

<?php include('footer.php');?>

</body>

</html>

