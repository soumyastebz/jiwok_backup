<?php
  $newPaymentUrl = 'payment_new.php';
  if($_SERVER['QUERY_STRING']) {
	  $newPaymentUrl .= '?' . $_SERVER['QUERY_STRING'];
  }
  header("Location: $newPaymentUrl"); exit;

session_start();
ob_start();
//For secure url(https)
if ($_SERVER['SERVER_PORT']!=443)
{
	$url = "https://". $_SERVER['SERVER_NAME'] . ":443".$_SERVER['REQUEST_URI'];
	header("Location: $url");
}
//Includes files and classes
include_once('includeconfig.php');
require_once('includes/classes/class.member.php');
include_once 'includes/classes/class.DbAction.php';
require_once 'includes/classes/Payment/class.payment.php';
include_once('./includes/classes/class.Languages.php');
include_once('includes/classes/class.CMS.php');
include_once('includes/classes/class.giftcode.php');
include_once("includes/classes/class.discount.php");
//ini_set('display_errors',1);
//error_reporting(E_ALL|E_STRICT);
//Checking the oxylane payment submision
if((isset($_POST['oxysubmit']))||(isset($_POST['oxysubmit'])))
{
	include_once("includes/classes/class.programs.php");
	include_once("oxylane_funct.php");
}
else
{
	include_once("includes/classes/class.programs_eng_beta.php");
}
//Oject creation
$paymentClass	=	new paymentClass();
$parObj 	    =   new Contents('payment_renew.php');
$objCMS			= 	new CMS($lanId);
$objgift        =   new gift();
$objDisc		= 	new Discount();
$dbObj     		=   new DbAction();

//Getting the xml elements according to the language
$returnData				=	$parObj->_getTagcontents($xmlPath,'new_payment','label');
$arrayData				= 	$returnData['general'];

$returnDataPay			= 	$parObj->_getTagcontents($xmlPath,'payment','label');
$arrayDataPay			= 	$returnDataPay['general'];

$returnDataProfile		=	$parObj->_getTagcontents($xmlPath,'myprofile','label');
$arrayDataProfile		=	$returnDataProfile['general'];
	
$returnErrorData1		=	$parObj->_getTagcontents($xmlPath,'payment','messages');
$arrayErrorData1		=	$returnErrorData1['errorMessage'];
$arrayErrorDataDiscount	=	$returnErrorData1['errorMessage'];	


$languageid	=	$_SESSION['language']['langId'];
if($lanId	==	"") 
	$lanId	=	1;
//For redirection after the gift code payment
if($_SESSION['language']['langId']	==	'1')
{
	$lanVar	=	"en/";
}
elseif($_SESSION['language']['langId']	==	'3')
{
	$lanVar	=	"es/";
}
elseif($_SESSION['language']['langId']	==	'4')
{
	$lanVar	=	"it/";
}
elseif($_SESSION['language']['langId']	==	'5')
{
	$lanVar	=	"pl/";
}
else
{
	$lanVar	=	"";
}
//Redirect after gift card payment	
if(isset($_SESSION['giftregcheck'])) 
{
	if($_SESSION['giftregcheck']	==	1)  
	{
		unset($_SESSION['giftregcheck']);
		header("Location:".$lanVar."userArea.php?t=ac");
		exit;
  	}
}
//For discount code for all plans
//Currently the discount code is applicable only for the 1 month plan. Now for polish site need to change it for all plans 
$discountForAllPlans	=	false;
//Executes after the valid discount code entered correctly
if($_SESSION['payment']['discCode'])
{
	$getDiscCodeDetails	=	$objDisc->_getDiscDetails($_SESSION['payment']['discCode']);
	if(($_SESSION['language']['langId']	==	'5')	&&	($getDiscCodeDetails['all_plan_status']	==	1))
	{
		$discountForAllPlans	=	true;
	}
	$discUser			=	true;
	$price_tmp 			=	$_SESSION['payment']['payFee'];	
	$selectSettings		=	"select * from settings";
	$setResult			=	$GLOBALS['db']->getAll($selectSettings,DB_FETCHMODE_ASSOC);
	foreach($setResult as $key=>$data)
	{
		$framt			=   $objGen->_output($data['membership_fee']);
		$enamt			=   $objGen->_output($data['membership_feedollar']);
		$polskiAmt		=	35;
	}
	//For dollar calculation
	$enoneamt	=	$enamt * (1/$framt);
	$dolamt		=	$enoneamt	 * $_SESSION['payment']['payFee'];	
	if($languageid	==	1)
	{
		$discountAmount	=	number_format($dolamt,2);
	}
	else
	{
		$discountAmount	=	number_format($price_tmp,2);
	}
	$_SESSION['discountAmount']	=	$discountAmount;	
}
	
if(isset($_SESSION['login']['userId']))
{
	$userId		=	$_SESSION['login']['userId']; //if user is coming from the registration page.
}
elseif(isset($_SESSION['user']['userId']))
{
	$userId		=	$_SESSION['user']['userId']; //get user id from the session variable/if user is coming from the UserArea Page.
}
else
{
	header('location:login_failed.php?msg='.base64_encode(4));
}
if((isset($_POST['oxysubmit']))||(isset($_POST['oxysubmit'])))
{
	include_once("oxySubmit.php");
}

//Code for unsubscribing the user manually	====>	Starts
//This is used only when some critical errors occured during registration, unsubscription or crone payment
//To run this code we need to append the payBoxMail and dil parameter with the payment url
//The payBoxMail is the email id used to register in the paybox of the coresponding user and
//The parameter dil always put as yes  
if(($_REQUEST[payBoxMail]	!=	"")	&& ($_REQUEST[dil]	==	'yes'))
{
	//$paymentClass->test(48561);die('exit');
	$paymentClass->autorizations(urldecode($_REQUEST[payBoxMail]));
	die('hi');
}
//====>	Ends
//User details query
$sqlUserQry		=	"SELECT user_language FROM `user_master` where user_id='".$userId."'";
$resUserQry		=	$paymentClass->dbSelectOne($sqlUserQry);

$selectQuery1	=  "select  * from payment_paybox where user_id ='".$userId."' and status='ACTIVE'";
$result1		= 	$GLOBALS['db']->getAll($selectQuery1,DB_FETCHMODE_ASSOC);
//For oldpayment subcription perion user popup message
$selectQuery2	=	"SELECT payment_expdate FROM `payment` WHERE `payment_userid` = '".$userId."' AND `payment_expdate` > CURDATE() AND `payment_status` = 1 AND version='New'";
$result2		= 	$GLOBALS['db']->getAll($selectQuery2,DB_FETCHMODE_ASSOC);

$selectQuery3	=  "select  * from payment where payment_userid ='".$userId."' AND payment_status = '1'";
$result3		= 	$GLOBALS['db']->getAll($selectQuery3,DB_FETCHMODE_ASSOC);
$selectQuery4	=	"SELECT COUNT(*) AS count FROM `payment` WHERE `payment_userid` = '".$userId."' AND `payment_expdate` > CURDATE() AND `payment_status` = 1 ";
//$res	=& $GLOBALS['db']->getOne($select_query, $userId);
$result4		= 	$GLOBALS['db']->getAll($selectQuery4,DB_FETCHMODE_ASSOC);	
if((sizeof($result1) == 0) &&	(sizeof($result3) > 0) &&	($result4[0]['count'] > 0) && !isset($_POST['make_payment']) && (sizeof($result2) == 0)&& !$discUser	&&	!isset($_POST['oxysubmit']))
{
	$msg			=	$parObj->_getLabenames($arrayData,'oldPaiUser','name');
	$expireDateQry	=  "select max(payment_expdate) as payment_expdate FROM payment where payment_userid='".$userId."' AND 	payment_status = '1'";
	$userExpireDate	=	$paymentClass->dbSelectOne($expireDateQry);	 
	$msg			=	str_replace("##",date("d-m-Y", strtotime($userExpireDate[payment_expdate])),$msg);	
}
else
{
	$msg	=	"";
}
//For multi language implimentation for plan
if($languageid == 3 || $languageid	==	4)
{
	$planLanguage	=	2;	
}
else
{
	$planLanguage	=	$languageid;
}
//Check whether the discount code is applicable for all plans. 
if($discountForAllPlans	&&	$_POST['make_payment'])
{
	//Getting the plan amount
	$planQuery		=	"SELECT plan_amount,plan_discount,plan_month_amount,plan_currency FROM jiwok_payment_plan where plan_status=1 AND  plan_currency='".$planLanguage."'	AND	plan_id	=	'".$_POST['payment_plan']."'";
	$planResult		=	$paymentClass->dbSelectOne($planQuery);		
	//Find the discount code details
	$getDiscCodeDetails	=	$objDisc->_getDiscDetails($_SESSION['payment']['discCode']);	
	$payAfterDiscount	=	number_format($planResult['plan_amount']	-	(($planResult['plan_amount']	*	$getDiscCodeDetails['discount_percentage'])/100),2);	
	$_SESSION['discountAmount']	=	$payAfterDiscount;
}
//Check payment button clicked or not
if(isset($_POST['make_payment']))
{	
	//Check for polish user
	if($resUserQry['user_language']	==	5)
	{
		//Check whether the user is in payment period
		if($result4[0]['count'] > 0)
		{					
			//Make payment and extend the expiry date.								
			$_POST['userType']	=	"newPolishUser";				
			$paymentClass->makePayment($_POST,$type = '1');
		}
		else
		{
			//Make payment.
			$_POST['userType']	=	"newPolishUser";				
			$paymentClass->makePayment($_POST,$type = '2');
		}
	}
	//= 0 Not Registred & > 0  ALREDY REGISTRED
	else if(sizeof($result1) > 0) 
	{			
		//echo "1";die();
		//Just update the cron table.Because the user is existing and we assume that he came here for plan change		
		$plan 		= $_POST['payment_plan'];
		$discStatus	=	0;
		if((($_SESSION['payment']['discCode']) &&  ($plan	==	1))	||	(($_SESSION['payment']['discCode']) &&  ($discountForAllPlans)))
		{			
			$discStatus	=	$_SESSION['discountAmount']."##".$_SESSION['payment']['discUser_id'];
		}			
		$dbQuery	=  "select * from jiwok_payment_plan where plan_id='".$plan."' and plan_currency='".$lanId."'";
		$res		 =	$paymentClass->dbSelectOne($dbQuery);
		$price		 =	$res['plan_amount'];				
		
		$cronUpdateQuery = "UPDATE payment_cronjob SET plan_id={$plan},payment_amount={$price},payment_currency={$lanId},discount_amount='{$discStatus}' where user_id={$userId} AND pp_id = {$result1[0]['id']}";
		
		$res = $GLOBALS['db']->query($cronUpdateQuery);
		//Message to client for the plan change alert starts
		$sqlUserQry		    =	"SELECT * FROM `user_master` where user_id='".$userId."'";
		$resUserQry			=	$paymentClass->dbSelectOne($sqlUserQry);
		$sqlUserPlanQry		=  "select max(payment_expiry_date) as payment_expiry_date,plan_id from payment_cronjob where user_id='".$userId."' AND status = 'VALID'";
		$resPlanQry		=	$paymentClass->dbSelectOne($sqlUserPlanQry);		
		
		if($discStatus	!=	0)
		{
			unset($_SESSION['discountAmount']);
			unset($_SESSION['payment']['discCode']);
		}
		$message	=	base64_encode($parObj->_getLabenames($arrayData,'54','name'));		
		header("location:userArea.php?actnMode=".$message);
		exit;			
	}
	else
	{								
		if(sizeof($result2) > 0)
		{			
			$_POST['userType']	=	"newUser";//echo "2";die;
			$paymentClass->makePayment($_POST,$type = '2');
		}
		else if(sizeof($result3) > 0)//Check whether the user used old payment or not
		{						
			//Make authentication and payment(calculate the date)													
														
			if($result4[0]['count'] > 0)//Check whether the user is in payment period
			{					
				//Make authentication and payment								
				$_POST['userType']	=	"oldPaymentUser";	//echo "3";die;						
				$paymentClass->makePayment($_POST,$type = '1');
			}
			else
			{					
				$_POST['userType']	=	"oldPaymentUser";	//echo "4";die;					
				$paymentClass->makePayment($_POST,$type = '2');
			}	
		}
		else//User is a new user
		{			
			$_POST['userType']	=	"newUser";//echo "5";die;
			$paymentClass->makePayment($_POST,$type = '1');
		}				
	}					
}
$todayDate	=	"Y-m-d";
$userPlan 	= 	0;
if(sizeof($result1) > 0)//Find users plan if the user came here for plan change
{
	$dbUserPlanQuery	=  "select max(payment_expiry_date) as payment_expiry_date,plan_id from payment_cronjob where user_id='".$userId."' AND status = 'VALID'";
	$resUserPlan		=	$paymentClass->dbSelectOne($dbUserPlanQuery);
	$userPlan   		= 	$resUserPlan['plan_id'];
	$alertMsg			=	$parObj->_getLabenames($arrayData,'planChangeAlertMsg','name');
	$alertMsg			=	str_replace("#",$userPlan,$alertMsg);
	$alertMsg2			=	str_replace("*",date("d-m-Y", strtotime($resUserPlan['payment_expiry_date'])),$parObj->_getLabenames($arrayData,'planChangeAlertMsg2','name'));
	//$alertMsg			=	str_replace("*",$resUserPlan['payment_expiry_date'],$alertMsg);
			
}	 		
$selectQuery	=	"select * from jiwok_payment_plan where plan_status=1 and  plan_currency='".$planLanguage."'";
$result 		= 	$GLOBALS['db']->getAll($selectQuery,DB_FETCHMODE_ASSOC);
$i				=	0;
foreach($result as $key=>$value)
{
	if($value['plan_id']==1)
	{	
		if($discountForAllPlans)
		{
			//Find the amount for 1 month based on the plan selected.
			if($value['plan_currency']	==	1)
				$perMonthAmount	=	9.9;
			elseif($value['plan_currency']	==	2)	
				$perMonthAmount	=	7.9;
			elseif($value['plan_currency']	==	5)	
				$perMonthAmount	=	35;
			//Find the actual payment amount
			$actualPayAmount	=	$perMonthAmount*$value['plan_id'];
			//Find the discount code details
			$getDiscCodeDetails	=	$objDisc->_getDiscDetails($_SESSION['payment']['discCode']);	
			$payAfterDiscount	=	number_format($value['plan_amount']	-	(($value['plan_amount']	*	$getDiscCodeDetails['discount_percentage'])/100),2);
			$currentDiscount	=	100-floor(($payAfterDiscount	*	100)	/	$actualPayAmount);
			$currentMonthAmount	=	number_format($payAfterDiscount	/	$value['plan_id'],2);	
			$plan1['month_amount']	=	$currentMonthAmount;
			$plan1['plan_amount']	=	$payAfterDiscount;
			$plan1['plan_discount']	=	$currentDiscount;
		}
		else
		{
			$plan1['month_amount']	=	$value['plan_month_amount'];
			$plan1['plan_amount']	=	$value['plan_amount'];
			$plan1['plan_discount']	=	$value['plan_discount'];	
		}
	}
	if($value['plan_id']==2)
	{	
		if($discountForAllPlans)
		{
			//Find the amount for 1 month based on the plan selected.
			if($value['plan_currency']	==	1)
				$perMonthAmount	=	9.9;
			elseif($value['plan_currency']	==	2)	
				$perMonthAmount	=	7.9;
			elseif($value['plan_currency']	==	5)	
				$perMonthAmount	=	35;
			//Find the actual payment amount
			$actualPayAmount	=	$perMonthAmount*$value['plan_id'];
			//Find the discount code details
			$getDiscCodeDetails	=	$objDisc->_getDiscDetails($_SESSION['payment']['discCode']);	
			$payAfterDiscount	=	number_format($value['plan_amount']	-	(($value['plan_amount']	*	$getDiscCodeDetails['discount_percentage'])/100),2);
			$currentDiscount	=	100-floor(($payAfterDiscount	*	100)	/	$actualPayAmount);
			$currentMonthAmount	=	number_format($payAfterDiscount	/	$value['plan_id'],2);	
			$plan2['month_amount']	=	$currentMonthAmount;
			$plan2['plan_amount']	=	$payAfterDiscount;
			$plan2['plan_discount']	=	$currentDiscount;
		}
		else
		{
			$plan2['month_amount']	=	$value['plan_month_amount'];
			$plan2['plan_amount']	=	$value['plan_amount'];
			$plan2['plan_discount']	=	$value['plan_discount'];
		}
	}
	if($value['plan_id']==3)
	{	
		if($discountForAllPlans)
		{
			//Find the amount for 1 month based on the plan selected.
			if($value['plan_currency']	==	1)
				$perMonthAmount	=	9.9;
			elseif($value['plan_currency']	==	2)	
				$perMonthAmount	=	7.9;
			elseif($value['plan_currency']	==	5)	
				$perMonthAmount	=	35;
			//Find the actual payment amount
			$actualPayAmount	=	$perMonthAmount*$value['plan_id'];
			//Find the discount code details
			$getDiscCodeDetails	=	$objDisc->_getDiscDetails($_SESSION['payment']['discCode']);	
			$payAfterDiscount	=	number_format($value['plan_amount']	-	(($value['plan_amount']	*	$getDiscCodeDetails['discount_percentage'])/100),2);
			$currentDiscount	=	100-floor(($payAfterDiscount	*	100)	/	$actualPayAmount);
			$currentMonthAmount	=	number_format($payAfterDiscount	/	$value['plan_id'],2);	
			$plan3['month_amount']	=	$currentMonthAmount;
			$plan3['plan_amount']	=	$payAfterDiscount;
			$plan3['plan_discount']	=	$currentDiscount;
		}
		else
		{
			$plan3['month_amount']	=	$value['plan_month_amount'];
			$plan3['plan_amount']	=	$value['plan_amount'];
			$plan3['plan_discount']	=	$value['plan_discount'];
		}
	}
	if($value['plan_id']==6)
	{	
		if($discountForAllPlans)
		{
			//Find the amount for 1 month based on the plan selected.
			if($value['plan_currency']	==	1)
				$perMonthAmount	=	9.9;
			elseif($value['plan_currency']	==	2)	
				$perMonthAmount	=	7.9;
			elseif($value['plan_currency']	==	5)	
				$perMonthAmount	=	35;
			//Find the actual payment amount
			$actualPayAmount	=	$perMonthAmount*$value['plan_id'];
			//Find the discount code details
			$getDiscCodeDetails	=	$objDisc->_getDiscDetails($_SESSION['payment']['discCode']);	
			$payAfterDiscount	=	number_format($value['plan_amount']	-	(($value['plan_amount']	*	$getDiscCodeDetails['discount_percentage'])/100),2);
			$currentDiscount	=	100-floor(($payAfterDiscount	*	100)	/	$actualPayAmount);
			$currentMonthAmount	=	number_format($payAfterDiscount	/	$value['plan_id'],2);	
			$plan6['month_amount']	=	$currentMonthAmount;
			$plan6['plan_amount']	=	$payAfterDiscount;
			$plan6['plan_discount']	=	$currentDiscount;
		}
		else
		{
			$plan6['month_amount']	=	$value['plan_month_amount'];
			$plan6['plan_amount']	=	$value['plan_amount'];
			$plan6['plan_discount']	=	$value['plan_discount'];
		}
	}
	if($value['plan_id']==12)
	{	
		if($discountForAllPlans)
		{
			//Find the amount for 1 month based on the plan selected.
			if($value['plan_currency']	==	1)
				$perMonthAmount	=	9.9;
			elseif($value['plan_currency']	==	2)	
				$perMonthAmount	=	7.9;
			elseif($value['plan_currency']	==	5)	
				$perMonthAmount	=	35;
			//Find the actual payment amount
			$actualPayAmount	=	$perMonthAmount*$value['plan_id'];
			//Find the discount code details
			$getDiscCodeDetails	=	$objDisc->_getDiscDetails($_SESSION['payment']['discCode']);
			$payAfterDiscount	=	number_format($value['plan_amount']	-	(($value['plan_amount']	*	$getDiscCodeDetails['discount_percentage'])/100),2);
			$currentDiscount	=	100-floor(($payAfterDiscount	*	100)	/	$actualPayAmount);			
			$currentMonthAmount	=	number_format($payAfterDiscount	/	$value['plan_id'],2);	
			$plan12['month_amount']	=	$currentMonthAmount;
			$plan12['plan_amount']	=	$payAfterDiscount;
			$plan12['plan_discount']	=	$currentDiscount;
		}
		else
		{
			$plan12['month_amount']	=	$value['plan_month_amount'];
			$plan12['plan_amount']	=	$value['plan_amount'];
			$plan12['plan_discount']	=	$value['plan_discount'];
		}
	}
}

if($_POST['user_discount'] !="" ):
	$errorMsg = 0;
	include('userdiscount_beta.php');	
		
	if($errorMsg == 0):	
		$_SESSION['discount_verify'] = true;
		header("Location:payment_renew.php?payment_plan=".$_REQUEST[payment_plan]);
		exit;
	endif;		
endif;

if(($_POST['user_discount'] =="" ) && (isset($_POST['renewSubscriptionIdBtn']))):
	if($_SESSION['language']['langId']=='1')
		$errorMsgNull = "Field is empty!";
	else if($_SESSION['language']['langId']=='5')
		$errorMsgNull = "To pole jest puste!";	
	else
		$errorMsgNull = "Le champ est vide!";
	$errorMsg = 4;
endif;	


function normalze($string)	
{
	$fReplace2 	 = array('"','"','');
	$trans 	 = array('�','�','�');
	$trans2 = array_combine($trans, $fReplace2);
	$string = utf8_decode($string);
	return utf8_encode(strtr($string,$trans2));
}
include("header.php"); 
include("menu.php");?>

<div id="container">
	<div id="wraper_payment">
		<div class="breadcrumbs">
      <ul>
        <li>
          <?=$parObj->_getLabenames($arrayData,'searchPath','name');?>
        </li>
        <li>
          <?=$parObj->_getLabenames($arrayData,'homeName','name');?>
          </li>
        <li>></li>
        <li>
          <?=$parObj->_getLabenames($arrayData,'myaccount','name');?>
        </li>
      </ul>
    </div>
	<div class="heading pad-btm"><span class="name"><?=$parObj->_getLabenames($arrayData,'newHeadTxt','name');?></span> <span class="date"></span></div>
	<?php
    if(isset($_GET['errorcode']))
    {	
        $eror_code	=	$_GET['errorcode'];
        $err 		= 	"2";
        if($eror_code == '00000' || $eror_code == '50' ||	$eror_code == '51' || $eror_code == '52' || $eror_code == '53')
            $err 	= 	"1";
        $msg		=	$parObj->_getLabenames($arrayData,$eror_code,'name');
        if($msg	==	"")
            $msg		=	$parObj->_getLabenames($arrayData,'default','name');	
        //Special case
        if($eror_code	==	'00057')
        {	
            $message57	=	str_replace("##","<div style='color:#000000; float:left;padding-right:5px;'>info@jiwok.com</div>",$parObj->_getLabenames($arrayData,'57message','name'));
            $msg		.=	"<br/>".$message57;
        }
    }?>
	<script type="text/javascript">
		$(document).ready(function()
		{
			if(<?=$resUserQry['user_language'];?>	==	5)
			{
				if(document.getElementById('payment_plan1').checked)
				{
					document.getElementById('planId_paypal').value='12';
				}
				else if(document.getElementById('payment_plan2').checked)
				{
					document.getElementById('planId_paypal').value='6';
				}
				else if(document.getElementById('payment_plan3').checked)
				{
					document.getElementById('planId_paypal').value='3';
				}
				else if(document.getElementById('payment_plan4').checked)
				{
					document.getElementById('planId_paypal').value='1';
				}
			}
			
		});

		function changePlanValPaypal(planId)
		{
			if(<?=$resUserQry['user_language'];?>	==	5)
			{
				
				document.getElementById('planId_paypal').value=planId;
				
			}
		}
		function checkBoxSts(checkId)
		{
			document.getElementById("payment"+checkId).checked=true;	
		}
		var isSubmit = 0;
		function planChange()
		{
			var radioObj	= document.forms['plan'].elements['payment_plan'];
			var selectedVal	=	"";
			
			var radioLength = radioObj.length;
			if(radioLength == undefined)
				if(radioObj.checked)
					selectedVal	=	radioObj.value;		
			for(var i = 0; i < radioLength; i++) {
				if(radioObj[i].checked) {
					selectedVal	=	radioObj[i].value;
				}
			}
			if(isSubmit == 0)
			{
				document.getElementById('alertMsgPlanChange').innerHTML='<?=$alertMsg." ";?>'+selectedVal+'<?=" ".$alertMsg2?>';
				document.getElementById('planChangeAlertMsg').style.display="block";
				centerPopupDiscount("planChangeAlertMsg");
				//return confirm('<?=$alertMsg;?>'+selectedVal);	//commeneted by dijo	
				return false;
			}
			else
			{
				return true;	
			}
		}

	</script>
	<style>
	.paymentDesc,#paymentDescNew{
		color:#000000 !important;
		font-size: 13px !important;
	}
	ul#billing li .discount_1 {
   	    font-size: 10px !important;
	}
	.regulations {
    	color: #000000;
	}
	
	ul#billing li.blue table tr td #xAmt {
		font-size: 20px !important;
	}
	ul#billing li.ylow table tr td #xAmt {
		font-size: 20px !important;
	}
	.ornge {
    color: #FBBA2B;
	}
	
</style>
	<form method="post" action="payment_renew.php" id="plan" onSubmit="return validatePaymentNew(<?php echo $languageid;?>);" name="plan">
    <div class="clear"></div>
    <div class="left_col">
    <?php
	if($lanId	==	5)
	{?>
    	<ul id="billing" class="billingClick">
			<?php if(sizeof($plan12) > 0)
        	{ ?>
                <li class="ylow" id="_plan1" onclick="checkBoxSts(this.id);changePlanValPaypal('12');">
                    <div class="corner"></div>
                    <table width="327" border="0" cellspacing="2" cellpadding="0">
                    <tr>
                        <td width="20" align="left" valign="middle">
                            <input type="radio" name="payment_plan" id="payment_plan1" value="12" <?php if(($userPlan == 12) || sizeof($result1) == 0 || $_REQUEST[payment_plan]	==	12) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='12';changePlanValPaypal('12');"/>
                        </td>
                        <td width="301">
                            <span style="color:#FFFFFF;font:bold 22px Arial,Helvetica;">
                            	<?=$parObj->_getLabenames($arrayData,'offer1','name');?>:<?php if($discountForAllPlans){echo $payAfterDiscount.$parObj->_getLabenames($arrayData,'currency','name');}else{ echo $plan12['plan_amount'].$parObj->_getLabenames($arrayData,'currency','name');}?>
                            </span><br/> 
                            <div class="paymentDesc"> 
                                <span style="color:#000000;font:bold italic 20px 'Trebuchet MS';">
                                    <?=$plan12['month_amount'].$parObj->_getLabenames($arrayData,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?><span style="color:#000000;font:bold italic 20px 'Trebuchet MS'; text-decoration:line-through;"><?=$parObj->_getLabenames($arrayData,'actualPaymentAmount','name').$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?></span>
                                </span>
                            </div>
                        </td>    
                    </tr>
                    </table>
                    <div class="discount_1"><strong>-<?=$plan12['plan_discount']?>%</strong><br />
                        <span class="oferText" style="font-size:11px;">
                            <?=$parObj->_getLabenames($arrayData,'newDisCaptTxt','name');?>
                        </span>
                    </div>
                </li>
            <?php }    
        	if(sizeof($plan6) > 0)
            {?>
                <li class="blue" id="_plan2"  onclick="checkBoxSts(this.id);changePlanValPaypal('6');">
                    <div class="corner"></div>
                    <table width="327" border="0" cellspacing="2" cellpadding="0">
                    <tr>
                        <td width="20" align="left" valign="middle">
                        <input type="radio" name="payment_plan" id="payment_plan2" value="6" <?php if($userPlan == 6	||	$_REQUEST[payment_plan]	==	6) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='6';changePlanValPaypal('6');"/>
                        </td>
                        <td width="301">
                        	<span style="color:#FFFFFF;font:bold 22px Arial,Helvetica;">
                    <?=$parObj->_getLabenames($arrayData,'offer2','name');?>:<?=$plan6['plan_amount'].$parObj->_getLabenames($arrayData,'currency','name');?>
                			</span> 
               				<br/>
                			<div class="paymentDesc"> 
                                <span style="color:#000000;font:bold italic 20px 'Trebuchet MS';">
                                    <?=$plan6['month_amount'].$parObj->_getLabenames($arrayData,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?>
                                    <span style="color:#000000;font:bold italic 20px 'Trebuchet MS'; text-decoration:line-through;">
                                        <?=$parObj->_getLabenames($arrayData,'actualPaymentAmount','name').$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?>
                                    </span>
                                </span>
                     		</div>
                    	</td>			
                 	</tr>
                    </table>
                    <div class="discount_2"><strong>-<?=$plan6['plan_discount']?>%</strong><br /> <span class="oferText">	<?=$parObj->_getLabenames($arrayData,'newDisCaptTxt','name');?></span></div>
                </li>
           	<?php }  
            if(sizeof($plan3) > 0)
            { ?>
            	<li class="blue" id="_plan3" onclick="checkBoxSts(this.id);changePlanValPaypal('3');">
             		<div class="corner"></div>
             		<table width="327" border="0" cellspacing="2" cellpadding="0">
              		<tr>
                		<td width="20" align="left" valign="middle">
                   			<input type="radio" name="payment_plan" id="payment_plan3" value="3" <?php if($userPlan == 3	||	$_REQUEST[payment_plan]	==	3) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='3';changePlanValPaypal('3');"/>
                		</td>
                		<td width="301">
                        	<span style="color:#FFFFFF;font:bold 22px Arial,Helvetica;">
            					<?=$parObj->_getLabenames($arrayData,'offer3','name');?>:<?=$plan3['plan_amount'].$parObj->_getLabenames($arrayData,'currency','name');?>
        					</span> 
       						<br/>
        					<div class="paymentDesc"> 
        						<span style="color:#000000;font:bold italic 20px 'Trebuchet MS';">
             						<?=$plan3['month_amount'].$parObj->_getLabenames($arrayData,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?>
             						<span style="color:#000000;font:bold italic 20px 'Trebuchet MS'; text-decoration:line-through;">
                						<?=$parObj->_getLabenames($arrayData,'actualPaymentAmount','name').$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?>
            						</span>
        						</span>
             				</div>
                   		</td>			
             		</tr>
             		</table>
             		<div class="discount_2"><strong>-<?=$plan3['plan_discount']?>%</strong><br />	 <span class="oferText"><?=$parObj->_getLabenames($arrayData,'newDisCaptTxt','name');?></span></div>
        		</li>
    		<?php } 
			if(sizeof($plan1) > 0	&&	$discountForAllPlans) 
			{?>
				<li class="blue" id="_plan4" onclick="checkBoxSts(this.id);changePlanValPaypal('1');">
             		<div class="corner"></div>
             		<table width="327" border="0" cellspacing="2" cellpadding="0">
              		<tr>
                		<td width="20" align="left" valign="middle">
                   			<input type="radio" name="payment_plan" id="payment_plan4" value="1" <?php if($userPlan == 1	||	$_REQUEST[payment_plan]	==	1) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='1';changePlanValPaypal('1');"/>
                		</td>
                		<td width="301">
                        	<span style="color:#FFFFFF;font:bold 22px Arial,Helvetica;">
            					<?=$parObj->_getLabenames($arrayData,'offer4','name');?>:<?=$plan1['plan_amount'].$parObj->_getLabenames($arrayData,'currency','name');?>
        					</span> 
       						<br/>
        					<div class="paymentDesc"> 
        						<span style="color:#000000;font:bold italic 20px 'Trebuchet MS';">
             						<?=$plan1['month_amount'].$parObj->_getLabenames($arrayData,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?>
             						<span style="color:#000000;font:bold italic 20px 'Trebuchet MS'; text-decoration:line-through;">
                						<?=$parObj->_getLabenames($arrayData,'actualPaymentAmount','name').$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?>
            						</span>
        						</span>
             				</div>
                   		</td>			
             		</tr>
             		</table>
             		<div class="discount_2"><strong>-<?=$plan1['plan_discount']?>%</strong><br />	 <span class="oferText"><?=$parObj->_getLabenames($arrayData,'newDisCaptTxt','name');?></span></div>
        		</li>
			<?php }
			elseif((sizeof($plan1) > 0)	&&	($discUser))
			{
				$discText		=	$parObj->_getLabenames($arrayData,'validDiscUser','name');
				$currencyPart	=	$plan1['month_amount']." ".$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');
				$discText		=	str_replace("##",$currencyPart,$discText);?>
				<li class="blue" id="_plan4"  onclick="checkBoxSts(this.id);changePlanValPaypal('1');">
 					<div class="corner"></div>
 					<div class="corner-rit"></div>
 					<table width="378" cellspacing="2" cellpadding="0" border="0">
  					<tbody>
                    	<tr>
    						<td width="20" valign="middle" align="left">
      							<input type="radio" name="payment_plan" id="payment_plan4" value="1" <?php if($userPlan == 1	||	$_REQUEST[payment_plan]	==	1) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='1';changePlanValPaypal('1');"/>
      						</td>
      						<td width="342">
                            	<span class="xxxNew"><?=$parObj->_getLabenames($arrayData,'offer4','name');?> (<?=$parObj->_getLabenames($arrayData,'planNewTxt','name');?>):</span> 
                                <span class="x" id="xAmt"> <?=$discountAmount;?></span><span class="xxxxNew"> <?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?>/<?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span><br/> 
      							<div class="paymentDesc">(<?=$discText;?>)</div>
      						</td>    
  						</tr>
  					</tbody>
                    </table>
  				</li>
			<?php }
        	elseif(sizeof($plan1) > 0) 
          	{   ?>
    			<li class="blue" id="_plan4"  onclick="checkBoxSts(this.id);changePlanValPaypal('1');">
     				<div class="corner"></div>
     				<div class="corner-rit"></div>
     				<table width="378" cellspacing="2" cellpadding="0" border="0">
      				<tbody>
                    	<tr>
        					<td width="20" valign="middle" align="left">
          						<input type="radio" name="payment_plan" id="payment_plan4" value="1" <?php if($userPlan == 1	||	$_REQUEST[payment_plan]	==	1) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='1';changePlanValPaypal('11');"/>
          					</td>
          					<td width="342">
                            	<span style="color:#FFFFFF;font:bold 22px Arial,Helvetica;">
            <?=$parObj->_getLabenames($arrayData,'offer4','name');?> (<?=$parObj->_getLabenames($arrayData,'planNewTxt','name');?>):
        						</span> 
       							<br/>
        						<div class="paymentDesc"> 
        							<span style="color:#000000;font:bold italic 20px 'Trebuchet MS';">
             							<?=$plan1['month_amount'].$parObj->_getLabenames($arrayData,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayData,'singleMonth','name');?>
             						</span>
             					</div>   
          					</td>    
      					</tr>
      				</tbody>
                    </table>
      			</li>
    		<?php } ?>
            <li>
                <table width="100%" border="0" cellspacing="2" cellpadding="0">
                <tr>
                    <td align="left" valign="top" ><!--?php if(sizeof($result1) == 0){?>--><strong><a onclick="return showDiscountPopup();" href="javascript:void(0)" class="orange" id="disCouponTgr">&gt; <?=$parObj->_getLabenames($arrayData,'newCpnTxt','name');?></a></strong><!--?php }?>--></td>
                    <td align="right" valign="top"><a href="javascript:void(0)" onclick="return unsubscribe();" class="blu"><strong>&gt; <?=$parObj->_getLabenames($arrayData,'newTermsLnkTxt','name');?></strong></a></td>
                </tr>
                </table>
                <script type="text/javascript">
					$(document).ready(function(){
						<?php if((($errorMsg == 1)||($errorMsgNull))	&&	(!isset($_POST['oxysubmit']))){?>
							showDiscountPopup();
						<?php }?>
					});
				</script>
            </li>
       </ul>
     <?php
	}
	else
	{?>
		<ul id="billing" class="billingClick">	   		
	   		<?php if(sizeof($plan12) > 0){ ?>
         <li class="ylow" id="_plan1" onclick="checkBoxSts(this.id);">
 			<div class="corner"></div>
			<table width="327" border="0" cellspacing="2" cellpadding="0">
			<tr>
				<td width="20" align="left" valign="middle">
					<input type="radio" name="payment_plan" id="payment_plan1" value="12" <?php if(($userPlan == 12) || sizeof($result1) == 0 || $_REQUEST[payment_plan]	==	12) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='12'"/>
				</td>
                <td width="301"><span class="xxxNew"><?=$parObj->_getLabenames($arrayData,'offer1','name');?>:</span> <span class="x" id="xAmt"> <?=$plan12['month_amount'];?></span><span class="xxxxNew"> <?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?>/<?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span><br/>
                
    <div class="paymentDesc">            
   <span class="x" id="paymentDescNew"><?=$parObj->_getLabenames($arrayData,'actualPaymentTxt','name')." ";?></span><span class="xstrike" id="paymentDescNew"><?=$parObj->_getLabenames($arrayData,'actualPaymentAmount','name').$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?></span> <br/>              
  <?=$parObj->_getLabenames($arrayData,'invoicedetails1','name')." ".$plan12['plan_amount']." ".$parObj->_getLabenames($arrayData,'currency','name');?>    </div> 
				</td>
                
				<!--<td width="301"><span class="xxxNew"><?=$plan12['month_amount']?></span><span class="xx"><?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?></span> <span class="x"> <?=$parObj->_getLabenames($arrayData,'newForTxt','name');?></span> <span class="xxxxNew"> 12 <?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span><br />
  <?=$parObj->_getLabenames($arrayData,'invoicedetails1','name')." ".$plan12['plan_amount']." ".$parObj->_getLabenames($arrayData,'currency','name');?>    
				</td>-->
			</tr>
			</table>
			<div class="discount_1"><strong>-<?=$plan12['plan_discount']?>%</strong><br />

<span class="oferText">
	<?=$parObj->_getLabenames($arrayData,'newDisCaptTxt','name');?>
</span>
			</div>
        </li>
       <?php }  
	   		if(sizeof($plan6) > 0){ ?>
	    <li class="blue" id="_plan2"  onclick="checkBoxSts(this.id);">
		 <div class="corner"></div>
		 <table width="327" border="0" cellspacing="2" cellpadding="0">
		  <tr>
			<td width="20" align="left" valign="middle">
			   <input type="radio" name="payment_plan" id="payment_plan2" value="6" <?php if($userPlan == 6	||	$_REQUEST[payment_plan]	==	6) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='6'"/>
			</td>
            <td width="301"><span class="xxxNew"><?=$parObj->_getLabenames($arrayData,'offer2','name');?>:</span> <span class="x" id="xAmt"> <?=$plan6['month_amount'];?></span><span class="xxxxNew"> <?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?>/<?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span><br/>
            <div class="paymentDesc"> 
   <span class="x" id="paymentDescNew"><?=$parObj->_getLabenames($arrayData,'actualPaymentTxt','name')." ";?></span><span class="xstrike" id="paymentDescNew"><?=$parObj->_getLabenames($arrayData,'actualPaymentAmount','name').$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?></span> <br/>              
  <?=$parObj->_getLabenames($arrayData,'invoicedetails1','name')." ".$plan6['plan_amount']." ".$parObj->_getLabenames($arrayData,'currency','name');?>     
  </div>
				</td>
			<!--<td width="301"><span class="xxxNew"><?=$plan6['month_amount']?></span><span class="xx"><?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?></span> <span class="x"> <?=$parObj->_getLabenames($arrayData,'newForTxt','name');?></span> <span class="xxxxNew"> 6 <?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span><br />
			  <?=$parObj->_getLabenames($arrayData,'invoicedetails2','name')." ".$plan6['plan_amount']." ".$parObj->_getLabenames($arrayData,'currency','name');?>   
			</td>-->
		 </tr>
		 </table>
		 <div class="discount_2"><strong>-<?=$plan6['plan_discount']?>%</strong><br /> <span class="oferText">	<?=$parObj->_getLabenames($arrayData,'newDisCaptTxt','name');?></span>
		 </div>
	</li>
    <?php }  
			if(sizeof($plan3) > 0){ ?>
          <li class="blue" id="_plan3" onclick="checkBoxSts(this.id);">
		 <div class="corner"></div>
		 <table width="327" border="0" cellspacing="2" cellpadding="0">
		  <tr>
			<td width="20" align="left" valign="middle">
			   <input type="radio" name="payment_plan" id="payment_plan3" value="3" <?php if($userPlan == 3	||	$_REQUEST[payment_plan]	==	3) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='3'"/>
			</td>
            <td width="301"><span class="xxxNew"><?=$parObj->_getLabenames($arrayData,'offer3','name');?>:</span> <span class="x" id="xAmt"> <?=$plan3['month_amount'];?></span><span class="xxxxNew"> <?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?>/<?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span><br/>
            <div class="paymentDesc"> 
   <span class="x" id="paymentDescNew"><?=$parObj->_getLabenames($arrayData,'actualPaymentTxt','name')." ";?></span><span class="xstrike" id="paymentDescNew"><?=$parObj->_getLabenames($arrayData,'actualPaymentAmount','name').$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?></span> <br/>              
  <?=$parObj->_getLabenames($arrayData,'invoicedetails1','name')." ".$plan3['plan_amount']." ".$parObj->_getLabenames($arrayData,'currency','name');?> </div>    
				</td>
			<!--<td width="301"><span class="xxxNew"><?=$plan3['month_amount']?></span><span class="xx"><?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?></span> <span class="x"> <?=$parObj->_getLabenames($arrayData,'newForTxt','name');?></span> <span class="xxxxNew"> 3 <?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span><br />
			  <?=$parObj->_getLabenames($arrayData,'invoicedetails3','name')." ".$plan3['plan_amount']." ".$parObj->_getLabenames($arrayData,'currency','name');?>   
			</td>-->
		 </tr>
		 </table>
		 <div class="discount_2"><strong>-<?=$plan3['plan_discount']?>%</strong><br />	 <span class="oferText"><?=$parObj->_getLabenames($arrayData,'newDisCaptTxt','name');?></span>
		 </div>
	</li>
<?php } 
			if(sizeof($plan2) > 0){ ?>
          <li class="blue" id="_plan5"  onclick="checkBoxSts(this.id);">
		 <div class="corner"></div>
		 <table width="327" border="0" cellspacing="2" cellpadding="0">
		  <tr>
			<td width="20" align="left" valign="middle">
			   <input type="radio" name="payment_plan" id="payment_plan5" value="2" <?php if($userPlan == 2	||	$_REQUEST[payment_plan]	==	2) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='2'"/>
			</td>
			<td width="301"><span class="xxxNew"><?=$plan2['month_amount']?></span><span class="xx"><?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?></span> <span class="x" id="paymentDescNew"> <?=$parObj->_getLabenames($arrayData,'newForTxt','name');?></span> <span class="xxxxNew" id="paymentDescNew"> 2 <?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span><br /><div class="paymentDesc">
			  <?=$parObj->_getLabenames($arrayData,'invoicedetails3','name')." ".$plan2['plan_amount']." ".$parObj->_getLabenames($arrayData,'currency','name');?>   
              </div>
			</td>
		 </tr>
		 </table>
		 <div class="discount_2"><strong>-<?=$plan2['plan_discount']?>%</strong><br /> <span class="oferText">	<?=$parObj->_getLabenames($arrayData,'newDisCaptTxt','name');?> </span>
		 </div>
	</li>
<?php } 
			if((sizeof($plan1) > 0)	&&	($discUser))
			{
	$discText		=	$parObj->_getLabenames($arrayData,'validDiscUser','name');
	$currencyPart	=	$plan1['month_amount']." ".$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');
	$discText		=	str_replace("##",$currencyPart,$discText);
	?>
<li class="blue" id="_plan4"  onclick="checkBoxSts(this.id);">
 <div class="corner"></div>
 <div class="corner-rit"></div>
 <table width="378" cellspacing="2" cellpadding="0" border="0">
  <tbody><tr>
    <td width="20" valign="middle" align="left">
      <input type="radio" name="payment_plan" id="payment_plan4" value="1" checked="checked" onclick="javascript:document.getElementById('selectedPlan').value='1'"/>
      </td>
      <td width="342"><span class="xxxNew"><?=$parObj->_getLabenames($arrayData,'offer4','name');?> (<?=$parObj->_getLabenames($arrayData,'planNewTxt','name');?>):</span> <span class="x" id="xAmt"> <?=$discountAmount;?></span><span class="xxxxNew"> <?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?>/<?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span><br/> 
      <div class="paymentDesc">(<?=$discText;?>)</div>
      </td>    
  </tr>
  </tbody></table>
  
</li>
	
<?php }
			elseif(sizeof($plan1) > 0) {   ?>
<li class="blue" id="_plan4"  onclick="checkBoxSts(this.id);">
 <div class="corner"></div>
 <div class="corner-rit"></div>
 <table width="378" cellspacing="2" cellpadding="0" border="0">
  <tbody><tr>
    <td width="20" valign="middle" align="left">
      <input type="radio" name="payment_plan" id="payment_plan4" value="1" <?php if($userPlan == 1	||	$_REQUEST[payment_plan]	==	1) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='1'"/>
      </td>
      <td width="342"><span class="xxxNew"><?=$parObj->_getLabenames($arrayData,'offer4','name');?> (<?=$parObj->_getLabenames($arrayData,'planNewTxt','name');?>):</span> <span class="x" id="xAmt"> <?=$plan1['month_amount'];?></span><span class="xxxxNew"> <?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?>/<?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span><br/>      
      
				</td>
    <!--<td width="301"><span class="xxxNew"><?=$plan1['month_amount']?></span><span class="xx"><?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?></span><span class="x"> <?=$parObj->_getLabenames($arrayData,'newForTxt','name');?></span> <span class="xxxxNew"> 1 <?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span><br>
      <?=$parObj->_getLabenames($arrayData,'invoicedetails4','name')." ".$plan1['plan_amount']." ".$parObj->_getLabenames($arrayData,'currency','name');?>   </td>-->
  </tr>
  </tbody></table>
  
</li>
<?php } ?>
          	<li>
            	<table width="100%" border="0" cellspacing="2" cellpadding="0">
  				<tr>
    				<td align="left" valign="top" ><!--?php if(sizeof($result1) == 0){?>--><strong><a onclick="return showDiscountPopup();" href="javascript:void(0)" class="orange" id="disCouponTgr">&gt; <?=$parObj->_getLabenames($arrayData,'newCpnTxt','name');?></a></strong><!--?php }?>--></td>
    				<td align="right" valign="top"><a href="javascript:void(0)" onclick="return unsubscribe();" class="blu"><strong>&gt; <?=$parObj->_getLabenames($arrayData,'newTermsLnkTxt','name');?></strong></a></td>
  				</tr>
				</table>
				<script type="text/javascript">
					$(document).ready(function(){
						<?php if((($errorMsg == 1)||($errorMsgNull))	&&	(!isset($_POST['oxysubmit']))){?>
							showDiscountPopup();
						<?php }?>
					});
				</script>
			</li>
		</ul>
     <?php
	}?>
	<div id="services_col">
     	<h2><?=$parObj->_getLabenames($arrayDataPay,'service','name');?></h2>
     	<ul id="service_list">
      		<li><?=$parObj->_getLabenames($arrayDataPay,'service1','name');?>  </li>
      		<li><?=$parObj->_getLabenames($arrayDataPay,'service2','name');?> </li>
      		<li><?=$parObj->_getLabenames($arrayDataPay,'service3','name');?> </li>
      		<li><?=$parObj->_getLabenames($arrayDataPay,'service4','name');?> </li>
      		<?php 
       		if($lanId	!=	5)
      		{?>
      			<li><?=$parObj->_getLabenames($arrayDataPay,'service5','name');?></li>
     		<?php }?>
     	</ul>
   	</div>
	<div class="services_btm-shade">&nbsp;</div>  
	   
	<div class="popup" id="unsubscribePgm" style="top: 50px; left: 290px; position:fixed; display:none; z-index:10;">    	
		<div><img src="images/pop-top.png" alt="jiwok" /></div>
		<div class="inner"> 
        	<a id="fancybox-close" onclick="hideUnsubscribe('unsubscribePgm');" title="close" style="display: inline;"></a>
			<h2><?=$parObj->_getLabenames($arrayDataPay,'paymentPopupTitle','name');?></h2>
            <div id="scroll" style="height:250px; overflow:auto;">
				<table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
				<tr>
				<?php if($lanId!=5){ ?>
				  <td colspan="2" align="center"><?=normalze(html_entity_decode($parObj->_getLabenames($arrayDataPay,'paymentPopupPara1','name'), ENT_QUOTES, "utf-8"));?></td><?php }else{?><td colspan="2" align="center"><?=html_entity_decode($parObj->_getLabenames($arrayDataPay,'paymentPopupPara1','name'), ENT_QUOTES, "utf-8");?></td><?php }?>
				</tr>
				<tr>
				  <td colspan="2" align="center"><?=normalze(html_entity_decode($parObj->_getLabenames($arrayDataPay,'paymentPopupPara2','name'), ENT_QUOTES, "utf-8"));?></td>
				</tr>
				<tr>
				  <td colspan="2" align="center"> <?=normalze(html_entity_decode($parObj->_getLabenames($arrayDataPay,'paymentPopupPara3','name'), ENT_QUOTES, "utf-8"));?></td>
				</tr>
				</table>              
         	</div>  
			<div class="clear"></div>
        	&nbsp;
		</div>
		<div><img src="images/pop-btm.png" alt="jiwok" /></div>          
	</div>	   
</div> 	 
<div class="right_col">
	<div id="banking">        
    	<?php if(sizeof($result1) > 0	&&	$resUserQry['user_language']	!=	5)
		{?>
        	<div class="heading"><?=$parObj->_getLabenames($arrayData,'planChangeHead','name');?></div>
          	<input type="hidden" name="switch" id="switch" value="1" />
	  		<input type="submit" class="buttonNew" id="make_payment" name="make_payment" value="<?=$parObj->_getLabenames($arrayData,'planChange','name');?>" onclick="return planChange()"/>          
       	<?php } 
		else
		{?> 
    		<div class="heading"><?=$parObj->_getLabenames($arrayData,'newBnkInfoTxt','name');?></div>
         	<table width="100%" border="0" cellspacing="4" cellpadding="0" class="bank">
            <tr>
            	<td width="31%" align="left" valign="bottom" class="pad-btm"><img src="images/lock.png" alt="jiwok" /></td>
                <td width="25%" align="left" valign="bottom" class="pad-btm"><span class="xxx"><?=$parObj->_getLabenames($arrayData,'newPaymentTxt','name');?> <br />
                  100% <br />
                  <?=$parObj->_getLabenames($arrayData,'newSecureTxt','name');?></span></td>
                <td width="44%" align="left" valign="bottom" class="pad-btm"><img src="images/cards.png" alt="jiwok" /></td>
          	</tr>  
         	<tr>
           		<td align="right" valign="top" class="pad-rit"><?=$parObj->_getLabenames($arrayData,'newCardNoTxt','name');?></td>
            	<td colspan="2"> <input type="text"  name="cardno" id="cardno"  class="votre_menu"/></td>
          	</tr>
   	 		<tr>
    			<td align="right" valign="top" class="pad-rit"><?=$parObj->_getLabenames($arrayData,'newExpDteTxt','name');?></td>
    			<td colspan="2"> 
                	<select name="cc_mm" id="cc_mm" class="list-box-auto">
                    	<option value="01">01</option>
                        <option value="02">02</option>
                        <option value="03">03</option>
                        <option value="04">04</option>
                        <option value="05">05</option>
                        <option value="06">06</option>
                        <option value="07">07</option>
                        <option value="08">08</option>
                        <option value="09">09</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                 	</select>
      				<select name="cc_yy" id="cc_yy" class="list-box-auto">
                        <option value="11">11</option>
                        <option value="12" >12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                        <option value="17">17</option>
                        <option value="18">18</option>
                        <option value="19">19</option>
                        <option value="20">20</option>
          			</select>
             	</td>
    		</tr>
    		<tr>
    			<td align="right" valign="top" class="pad-rit"><?=$parObj->_getLabenames($arrayData,'newCardPinTxt','name');?></td>
    			<td colspan="2" align="left" valign="top">
					<input  type="text" name="cvv" id="cvv" class="tfl-2" />     
      				<a href="javascript:void(0)" onMouseOver="tooltip('<html><br> <img src=\'/images/crypto.jpg\' /></html>');" onMouseOut="exit();"><img src="images/help.png" alt="Help" /></a></td>
    		</tr>
			</table>
			<input type="hidden" name="switch" id="switch" value="2" />
 			<input type="submit" class="buttonNew" name="make_payment" value="<?=$parObj->_getLabenames($arrayData,'newCOutBtnTxt','name');?>" />
		<?php }?>
       	<div class="clear"></div>
	</div>
</form>
	<?php if($lanId==2 && 	$resUserQry['user_language']	!=	5)
    { 
        include("oxyPayment.php");  
    } 
	if($resUserQry['user_language']	==	5)
	{?>	
		<form action="paypal_payment.php" name="paypalform" method="post">     
			<div id="voucher_section" style="height:30px; padding-top:0px;">
				 <input type="hidden" name="userId_paypal" value="<?=$userId;?>" />
				 <input type="hidden" name="planId_paypal" id="planId_paypal"/>             
				 <input type="hidden" name="discCode" id="discCode" value="<?=$_SESSION['payment']['discCode'];?>"/>
				 <input type="hidden" name="discountAmount" id="discountAmount" value="<?=$_SESSION['discountAmount'];?>"/>
				 <input type="hidden" name="discUser_id" id="discUser_id" value="<?=$_SESSION['payment']['discUser_id'];?>"/>             
				 <input name="paypalsubmit" type="submit" value="<?php if($lanId	==	5) echo "Płatność Paypal";else echo "paiement Paypal";?>" class="buttonNew"/>
			</div>
		 </form>
	<?php }
	if($lanId	==	5)
	{
		include('polish_gift_msg.php');
	}
	if($resUserQry['user_language']	!=	5)
	{?>
		<div class="regulations">
			<b><?=$parObj->_getLabenames($arrayData,'newBtmTxtHead','name');?>: </b><br  /><br />
			<?=$parObj->_getLabenames($arrayData,'newBtmTxt','name');?>
		</div>
	<?php }?>
</div>
</div>
<div class="clear"></div>
</div>
</div>
<!--top: 172px; left: 290px; position:fixed;-->
<div class="popup" id="renewSubscriptionId" style="display:none; z-index:10">
  <div><img src="images/pop-top.png" alt="jiwok" /></div>
  <div class="inner"> <a id="fancybox-close" onclick="renewSubscriptionDisplay();" title="close" style="display: inline;"></a>
    <h2>
      <?php if($userpaymentstatus == 0){ echo $parObj->_getLabenames($arrayDataProfile,'popuppayment','name'); } else { echo $parObj->_getLabenames($arrayDataProfile,'renewSubscription','name');} ?>
    </h2>
    <form name="renewSubscriptionFrm" action="" method="post" >
      <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
        <tr>
          <td colspan="2" align="center" class="ornge"><?php if($errorMsg == 1){echo $discountMsg;} else {if($errorMsgNull) echo $errorMsgNull;}?></td>
        </tr>

        <tr>
          <td width="50%" align="left" valign="middle"><?=$parObj->_getLabenames($arrayDataProfile,'renewDisc','name');?></td>
          <td width="50%"><input type="text" name="user_discount" value="" ></td>
        </tr>
        <tr>
          <td align="left" valign="middle" colspan="2"><!--<?=$parObj->_getLabenames($arrayDataProfile,'gotonext','name');?>--><input type="hidden" name="selectedPlan" value="<?php if($_REQUEST[payment_plan]){if($discUser	&&	!$discountForAllPlans){echo '1';}else{echo $_REQUEST[payment_plan];}}elseif(sizeof($result1) > 0	&&	$resUserQry['user_language']	!=	5) echo $userPlan; else echo '12';?>" id="selectedPlan"/></td>
        </tr>
        <tr>
          <td colspan="2" align="center"><input class="bu_03" name="renewSubscriptionIdBtn" type="submit" value="<?=$parObj->_getLabenames($arrayDataProfile,'renew','name');?>" /></td>
        </tr>
      </table>
    </form>
    <div class="clear"></div>
  </div>
  <div><img src="images/pop-btm.png" alt="jiwok" /></div>
</div>

<div class="popup" id="paymantCmnAlertMsg" style="display:none;position:fixed;z-index:100000;">
  <div><img src="images/pop-top.png" alt="jiwok" /></div>
  <div id="close">
  	<a href="javascript:void(0)" title="close" id="closePaymantCmnAlertMsg">
    	<img src="images/close.PNG" alt="close" title="close"/>
  	</a>  
  </div>
  <div class="inner">  
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center" style="text-align:left"><div id="alertMsgCmnPayment"></div><br/><br/></td>
      </tr>
      <tr>
        <td align="center"><a id="okIdCmnPaymentAlert"><input class="bu_03"  name="renewSubscriptionIdBtn" type="button" value="Ok" /></a></td>
      </tr>
    </table>
    <div class="clear"></div>
  </div>
  <div><img src="images/pop-btm.png" alt="jiwok" /></div>
</div>
<?php
if($discUser	&&	$errorMsg == 0	&&	!$discountForAllPlans)
{
	$msgDiscount	 =  $parObj->_getLabenames($arrayData,'validDiscountCodeMsg','name');
}
else
{
	$msgDiscount	 = "";
}

?>
<script>
	
	var msgTxt		=	"<?php echo $msg; ?>";
	var msgDiscount =	"<?php echo $msgDiscount; ?>";
	
	$(document).ready(function(){
		if(msgTxt!="")
		{
			document.getElementById("alertMsgCmnPayment").innerHTML	=	msgTxt;
			showPopup("paymantCmnAlertMsg","");
		}
		
		var def = $.Deferred();
		setTimeout(def.resolve, 4000);
		$("#okIdCmnPaymentAlert").click(function(){
			disablePopupGeneral("paymantCmnAlertMsg","");
		});
		$("#closePaymantCmnAlertMsg").click(function(){
			disablePopupGeneral("paymantCmnAlertMsg","");
		});
		/*def.done(function() 
		{
			$("#paymantCmnAlertMsg").fadeOut(3000);
		});*/
		
		
				
		
		//$('#paymantCmnAlertMsg').delay(5000).fadeOut(4000);
		//Press Escape event!
		$(document).keypress(function(e){
			if(e.keyCode==27 && popupStatus==1){
				disablePopupGeneral("paymantCmnAlertMsg","");
			}
		});
		// Discount code confirmation pop up
		if(msgDiscount!=""){
				document.getElementById("alertMsgCmnDiscount").innerHTML	=	msgDiscount;
				showPopup("DiscountCmnAlertMsg","");
		}
		$("#okIdCmnDiscountAlert").click(function(){
			disablePopupGeneral("DiscountCmnAlertMsg","");
		});
		//Press Escape event!
		$(document).keypress(function(e){
			if(e.keyCode==27 && popupStatus==1){
				disablePopupGeneral("DiscountCmnAlertMsg","");
			}
		});
		//dis code pop up ends
		//plan change pop 

			$("#okPlanChange").click(function(){
				isSubmit = 1;
				$('#make_payment').trigger('click');
				
			});
			$("#cancelPlanChange").click(function(){
				$('#planChangeAlertMsg').hide();;
			});

		//plan change pop 
	});
</script>

<div class="popup" id="paymantAlertMsg" style="display:none;position:fixed;z-index:100000;">
  <div><img src="images/pop-top.png" alt="jiwok" /></div>
  <div class="inner">
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center" style="text-align:left"><div id="alertMsgPayment"></div><br/><br/></td>
      </tr>
      <tr>
        <td align="center"><a id="okIdPaymentAlert"><input class="bu_03"  name="renewSubscriptionIdBtn" type="button" value="Ok" /></a></td>
      </tr>
    </table>
    <div class="clear"></div>
  </div>
  <div><img src="images/pop-btm.png" alt="jiwok" /></div>
</div>

<!-- Discount code pop up confrm msg -->
<div class="popup" id="DiscountCmnAlertMsg" style="display:none;position:fixed;z-index:100000;">
  <div><img src="images/pop-top.png" alt="jiwok" /></div>
  <div class="inner">
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center" style="text-align:left"><div id="alertMsgCmnDiscount"></div><br/><br/></td>
      </tr>
      <tr>
        <td align="center"><a id="okIdCmnDiscountAlert"><input class="bu_03"  name="renewSubscriptionIdBtn" type="button" value="Ok" /></a></td>
      </tr>
    </table>
    <div class="clear"></div>
  </div>
  <div><img src="images/pop-btm.png" alt="jiwok" /></div>
</div>
<!--Discount code pop up confrm msg  ends -->

<!--plan change pop up dijo-->
<div class="popup" id="planChangeAlertMsg" style="display: none;
    position: fixed;
    z-index: 10;width: 431px;">
  <div><img src="images/pop-top.png" alt="jiwok" /></div>
  <div class="inner">
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center" style="text-align:left"><div id="alertMsgPlanChange"></div><br/><br/></td>
      </tr>
      <tr>
        <td align="center"><a id="cancelPlanChange">&nbsp;&nbsp;<input class="bu_03"  name="planChangeCancel" type="button" value="<?=$parObj->_getLabenames($arrayData,'cancelbutton','name');?>" style="background:grey;border-radius: 7px;"/></a>&nbsp;&nbsp;<a id="okPlanChange"><input class="bu_03"  name="planChangeOK" type="button" value="Ok" /></a></td>
      </tr>
    </table>
    <div class="clear"></div>
  </div>
  <div><img src="images/pop-btm.png" alt="jiwok" /></div>
</div>

<!--plan change pop up dijo ends-->

<script>

	

	$(document).ready(function(){

		$("#okIdPaymentAlert").click(function(){
			disablePopupGeneral("paymantAlertMsg","");
		});
		//Press Escape event!
		$(document).keypress(function(e){
			if(e.keyCode==27 && popupStatus==1){
				disablePopupGeneral("paymantAlertMsg","");
			}
		});
		
	});
</script>

<?php  if(($errorMsg == 1 && $_REQUEST['user_discount'] !="")):	?>
	<script>//showDiscountPopup();</script>	
<?php
endif;		
if(($errorMsg == 4) && (isset($_POST['renewSubscriptionIdBtn']))):		
?>
	<script>//showDiscountPopup();</script>
<?php endif; ?>

<?php //include("footer.php"); ?>
