<?php
session_start();
ob_start();
//echo "<pre/>";//print_r($_SESSION);
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
require_once 'includes/classes/Payment/dbFunctions.php';
include_once('includes/classes/Payment/class.newpayment_paybox.php');
include_once('./includes/classes/class.Languages.php');
include_once('includes/classes/class.CMS.php');
include_once('includes/classes/class.giftcode.php');
include_once("includes/classes/class.discount.php");
include_once("paybox_formpage.php");
include_once("includes/classes/Payment/class.stripePayment.php");

$languageid	=	$_SESSION['language']['langId'];
//Oject creation
//$paymentClass	=	new paymentClass();
$parObj 	    =   new Contents('payment_new.php');
$objCMS			= 	new CMS($lanId);
$objgift        =   new gift();
$objDisc		= 	new Discount();
$dbObj     		=   new DbAction();
$newpaymentClass	=	new newpayment_payboxClass();
$dbfun			=	new dbFunctions();
$stripe			=	new stripePayment(); 
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
if((isset($_POST['oxysubmit'])))
{
	include_once("includes/classes/class.programs.php");
	include_once("oxylane_funct.php");
}
else
{
	include_once("includes/classes/class.programs_eng_beta.php");
}
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

//User details query
$sqlUserQry		=	"SELECT user_language FROM `user_master` where user_id='".$userId."'";
$resUserQry		=	$newpaymentClass->dbSelectOne($sqlUserQry);

$selectQuery1	=  "select  * from payment_paybox where user_id ='".$userId."' and status='ACTIVE'";
$result1		= 	$GLOBALS['db']->getAll($selectQuery1,DB_FETCHMODE_ASSOC);
//stripe
$selectQuery1stripe	=  "select  * from stripe_payment where user_id ='".$userId."' and status='ACTIVE'";
$striperesult1		= 	$GLOBALS['db']->getAll($selectQuery1stripe,DB_FETCHMODE_ASSOC);

	if(sizeof($result1)>0)
	{
	$count_result     = sizeof($result1) ;// paybox active user count 
	}
	else if(sizeof($striperesult1)>0)
	{ 
	$count_result     = sizeof($striperesult1) ; // stripe active user count 
	$selectQuerystripe	=	"SELECT payment_expdate FROM `payment` WHERE `payment_userid` = '".$userId."' AND `payment_expdate` > CURDATE() AND `payment_status` = 1 AND version='stripe'";
	$result_stripe		= 	$GLOBALS['db']->getAll($selectQuerystripe,DB_FETCHMODE_ASSOC); 
	$plan_next_paydate	=	$result_stripe[0]['payment_expdate'];// to extend payment only at expiry date of current plan end 
	}
//For oldpayment subcription perion user popup message
$selectQuery2	=	"SELECT payment_expdate FROM `payment` WHERE `payment_userid` = '".$userId."' AND `payment_expdate` > CURDATE() AND `payment_status` = 1 AND version='New'";
$result2		= 	$GLOBALS['db']->getAll($selectQuery2,DB_FETCHMODE_ASSOC); 
	
$selectQuery3	=  "select  * from payment where payment_userid ='".$userId."' AND payment_status = '1'";
$result3		= 	$GLOBALS['db']->getAll($selectQuery3,DB_FETCHMODE_ASSOC);
$selectQuery4	=	"SELECT COUNT(*) AS count FROM `payment` WHERE `payment_userid` = '".$userId."' AND `payment_expdate` > CURDATE() AND `payment_status` = 1 ";
$result4		= 	$GLOBALS['db']->getAll($selectQuery4,DB_FETCHMODE_ASSOC);

if((sizeof($count_result) > 0)	&&	($resUserQry['user_language'] !=	5) && ($striperesult1[0]['attempt_count']  == 0))
{ 
	$name_tag         = $parObj->_getLabenames($arrayData,'planChange','name');
}
else
{
		$name_tag         = $parObj->_getLabenames($arrayDataPay,'pay','name');
}
if($_POST['updatePaymentPlan'] == 1){ // after choosing  a plan id 

	$changedplanIddis 	= $_POST['payment_plan_id'];	
	$changedplanId 		= $_POST['payment_plan_id'];	
	if($changedplanIddis ==1)
	{
	if($_SESSION['payment']['discCode'])
	{
		  if($resUserQry['user_language'] ==2)
		  {
		   $changedplanIddis = 'euro1';
	      }
	}
   }
	$_SESSION['user']['payment_plan_id'] = $changedplanId;	
	
	$userlang	=	$resUserQry['user_language'];
	
	$button_payment	=	getPaymentButton($changedplanId,$count_result,$userlang,$name_tag,$striperesult1[0]['attempt_count']); //to see pay button 
	
	echo $button_payment;
	exit;
}
if($_POST['stripePayment'] == 1){ //stripe pop up after action

	if((($count_result > 0 )) && ($striperesult1[0]['attempt_count']  != 0))
	{
		$unsubscribe_retry_period			=	$stripe	->unsubsriptionFromstripe($striperesult1[0]['id']);
	}
	
	$token 				= $_POST['token'];	
	$stripe_email 		= $_POST['stripe_email'];
	$changedplanIddis 	= $_POST['payment_plan_id'];
	$changedplanId 		= $_POST['payment_plan_id'];	
	if($changedplanIddis ==1)
	{
	if($_SESSION['payment']['discCode'])
	{
		  if($resUserQry['user_language'] ==2)
		  {
		   $changedplanIddis = 'euro1';
	      }
	}
	}
	//////try catch code
	$errors = array();
	if (isset($_SESSION['token']) && ($_SESSION['token'] == $token))
		{
			$errors['token'] = 'You have apparently resubmitted the form. Please do not do that.';
		 } 
		 else
		  { // New submission.
			$_SESSION['token'] = $token;
		  }
		  if($token == "")// if no token from stripe 
		  {
			  $errors['token'] = "No token get from stripe account ";
		  }
		
	///try catch ends
	if(empty($errors))
	{
		$plansubscibe			=	$stripe	->plan_subscribe_stripe($changedplanIddis,$userId,$token,$stripe_email);
	
		if($plansubscibe !=0)
		{   
			 $plansubscibe['stripe_email']	=	$stripe_email;
			 $plansubscibe['token']	=	$token;
			 $plansubscibe['plansubscibe_id']	=	$changedplanId;
		    $dbQuery	 =  "select user_language from user_master where user_id=".$userId;
		    $res		 =	$GLOBALS['db']->getAll($dbQuery,DB_FETCHMODE_ASSOC); 
		     if($res[0]['user_language']==5)//user language
			{
				if($result4[0]['count'] > 0)
				{					
					//Make payment and extend the expiry date.
					$plansubscibe['userType']	=	"polishstripe";
					$valstripe = $newpaymentClass->afterPayment($plansubscibe,$type = '1');
				}
				else
				{
					//Make payment.
					$plansubscibe['userType']	=	"polishstripe";				
					$valstripe = $newpaymentClass->afterPayment($plansubscibe,$type = '2');
				}
			}
			else
			{				
				if($result4[0]['count'] > 0)
				{					
					//Make payment and extend the expiry date.	
					$plansubscibe['userType']	=	"stripe";				
					$valstripe = $newpaymentClass->afterPayment($plansubscibe,$type = '1');
				}
				else
				{ //Make payment.
					
					$plansubscibe['userType']	=	"stripe";				
					$valstripe = $newpaymentClass->afterPayment($plansubscibe,$type = '2');//print_r($valstripe);exit;
				}			
			} 	
	}
	else if($plansubscibe ==0)
	{ echo "00";exit;
		//message popn up
		}
}
	
//stripe
}
/* from strip old code 

if($_POST['updatePaymentPlanchange'] == 1)
{
	$changedplanId = $_POST['payment_plan_id'];	
	$_SESSION['user']['payment_plan_id'] = $changedplanId;
	echo "success";
	exit;
}*/


//==============================================================
$response				=	array();
$response['payment_plan']	=	$_SESSION['user']['payment_plan_id'];
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
if((isset($_POST['oxysubmit'])))
{   
	include_once("oxySubmit.php");
}
//Code for unsubscribing the paybox user manually	,from old payment page code ====>	Starts
//This is used only when some critical errors occured during registration, unsubscription or crone payment
//To run this code we need to append the payBoxMail and dil parameter with the payment url
//The payBoxMail is the email id used to register in the paybox of the coresponding user and
//The parameter dil always put as yes  
if(($_REQUEST['payBoxMail']	!=	"")	&& ($_REQUEST[dil]	==	'yes'))
{
	//$paymentClass->test(48561);die('exit');
	$paymentClass->autorizations(urldecode($_REQUEST['payBoxMail']));
	die('hi');
}
//====>	Ends
// to give pop up to stripe retry period users
$retryMsg	=	$parObj->_getLabenames($arrayDataPay,'paymentretrypop','name');
if(isset($_REQUEST['confirm']) != "")
{
		$confirm	=	base64_decode($_REQUEST['confirm']);
}
if(($count_result > 0 ) && ($striperesult1[0]['attempt_count'] > 0) &&($confirm != "user"))
{
		$msg	=	$retryMsg;
	
}
elseif(((sizeof($result1) == 0) ||(sizeof($striperesult1) == 0)  ) &&	(sizeof($result3) > 0) &&	($result4[0]['count'] > 0) && !isset($_POST['make_payment']) && (sizeof($result2) == 0)&& !$discUser	&&	!isset($_POST['oxysubmit']) )
{
	
	$msg_Text			=	$parObj->_getLabenames($arrayData,'oldPaiUser','name');
	$expireDateQry	=  "select max(payment_expdate) as payment_expdate FROM payment where payment_userid='".$userId."' AND 	payment_status = '1'";
	$userExpireDate	=	$newpaymentClass->dbSelectOne($expireDateQry);	 
	$msg_Text			=	str_replace("##",date("d-m-Y", strtotime($userExpireDate['payment_expdate'])),$msg_Text);
	$msg		=	2;
		
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
	$planQuery		=	"SELECT plan_amount,plan_discount,plan_month_amount,plan_currency FROM jiwok_payment_plan where plan_status=1 AND  plan_currency='".$planLanguage."'	AND	plan_id	=	'".$_POST['payment_plan_id']."'";
	$planResult		=	$newpaymentClass->dbSelectOne($planQuery);		
	//Find the discount code details
	$getDiscCodeDetails	=	$objDisc->_getDiscDetails($_SESSION['payment']['discCode']);	
	$payAfterDiscount	=	number_format($planResult['plan_amount']	-	(($planResult['plan_amount']	*	$getDiscCodeDetails['discount_percentage'])/100),2);	
	$_SESSION['discountAmount']	=	$payAfterDiscount;
}
/* Plan Change in new payment system */
if(sizeof($result1) > 0 && isset($_POST['make_payment'])) 
	{	
		
		//Just update the cron table.Because the user is existing and we assume that he came here for plan change		
		$plan 		 = $response['payment_plan'];
		$discStatus  =	0;
		if((($_SESSION['payment']['discCode']) &&  ($plan	==	1))	||	(($_SESSION['payment']['discCode']) &&  ($discountForAllPlans)))
		{			
		$discStatus	 =	$_SESSION['discountAmount']."##".$_SESSION['payment']['discUser_id'];
		}			
		$dbQuery	 =  "select * from jiwok_payment_plan where plan_id='".$plan."' and plan_currency='".$lanId."'";
		$res		 =	$newpaymentClass->dbSelectOne($dbQuery);
		$price		 =	$res['plan_amount'];				
	    $cronUpdateQuery = "UPDATE payment_cronjob SET plan_id={$plan},payment_amount={$price},payment_currency={$lanId},discount_amount='{$discStatus}' where user_id={$userId} AND pp_id = {$result1[0]['id']}";
		$res = $GLOBALS['db']->query($cronUpdateQuery);
		//Message to client for the plan change alert starts
		$sqlUserQry		    =	"SELECT * FROM `user_master` where user_id='".$userId."'";
		$resUserQry			=	$newpaymentClass->dbSelectOne($sqlUserQry);
		$sqlUserPlanQry		=  "select max(payment_expiry_date) as payment_expiry_date,plan_id from payment_cronjob where user_id='".$userId."' AND status = 'VALID'";
		$resPlanQry		=	$newpaymentClass->dbSelectOne($sqlUserPlanQry);		
		
		if($discStatus	!=	0)
		{
			unset($_SESSION['discountAmount']);
			unset($_SESSION['payment']['discCode']);
		}
		$message	=	base64_encode($parObj->_getLabenames($arrayData,'54','name'));		
		header("location:userArea.php?actnMode=".$message);
		exit;			
	}
//****for stripe payment table checking
if(sizeof($striperesult1) > 0 && isset($_POST['make_payment'])) 
	{	
		
		$plan 		            =  $response['payment_plan'];
		$planResponse			=	$stripe	->plan_change_stripe($plan,$userId,$plan_next_paydate);
		if($planResponse)
		{
	    $stripeUpdateQuery = "UPDATE stripe_payment SET plan_id={$planResponse} where user_id={$userId} and status='ACTIVE'";
		$res = $GLOBALS['db']->query($stripeUpdateQuery);
		$sq ="select * from stripe_auto_renewal where user_id='".$userId."' and status='VALID'";
		$dq = end($GLOBALS['db']->getAll($sq,DB_FETCHMODE_ASSOC));
		if(sizeof($dq) >0)
		{
		 $dbQuery	 =  "select * from jiwok_payment_plan where plan_id='".$plan."' and plan_currency='".$lanId."'";
		 $res		 =	$newpaymentClass->dbSelectOne($dbQuery);
		 $price		 =	$res['plan_amount'];
		 $stripeauto = "UPDATE stripe_auto_renewal SET plan_id={$planResponse},payment_amount={$price} where user_id={$userId} AND status = 'VALID'";
		 $stripeautoupdate = $GLOBALS['db']->query($stripeauto);
		}
		}
		//Just update the cron table.Because the user is existing and we assume that he came here for plan change		
		
		$discStatus  =	0;
		if((($_SESSION['payment']['discCode']) &&  ($plan	==	1))	||	(($_SESSION['payment']['discCode']) &&  ($discountForAllPlans)))
		{			
		$discStatus	 =	$_SESSION['discountAmount']."##".$_SESSION['payment']['discUser_id'];
		}	
		//Message to client for the plan change alert starts
		$sqlUserQry		    =	"SELECT * FROM `user_master` where user_id='".$userId."'";
		$resUserQry			=	$newpaymentClass->dbSelectOne($sqlUserQry);
		$sqlUserPlanQry		=  "select max(payment_expiry_date) as payment_expiry_date,plan_id from stripe_auto_renewal where user_id='".$userId."' AND status = 'VALID'";
		$resPlanQry		=	$newpaymentClass->dbSelectOne($sqlUserPlanQry);		
		
		if($discStatus	!=	0)
		{
			unset($_SESSION['discountAmount']);
			unset($_SESSION['payment']['discCode']);
		}
		$message	=	base64_encode($parObj->_getLabenames($arrayData,'54','name'));		
		header("location:userArea.php?actnMode=".$message);
		exit;			
	}

//old code 
$payBoxIps	=	array('195.101.99.76','194.2.122.158','195.25.7.166');	
$requestedIp = $_SERVER['REMOTE_ADDR'];
//--
$todayDate	=	"Y-m-d";
$userPlan 	= 	0;
if($count_result > 0)//Find users plan if the user came here for plan change
{ 
	if(sizeof($result1) > 0)
	{
		$dbUserPlanQuery	=  "select max(payment_expiry_date) as payment_expiry_date,plan_id from payment_cronjob where user_id='".$userId."' AND status = 'VALID'";		
	}
	elseif(sizeof($striperesult1) > 0)
	{ 
	   //$dbUserPlanQuery	=  "select max(payment_expiry_date) as payment_expiry_date,plan_id from stripe_auto_renewal where user_id='".$userId."' AND status = 'VALID'";
	    $dbUserPlanQuery	=  "select payment_expiry_date,plan_id from stripe_auto_renewal where user_id='".$userId."' AND status = 'VALID'  order by payment_expiry_date desc ";
	  
	}
	$resUserPlan		=	$newpaymentClass->dbSelectOne($dbUserPlanQuery);
	$userPlan   		= 	$resUserPlan['plan_id'];
	$alertMsg			=	$parObj->_getLabenames($arrayData,'planChangeAlertMsg','name');
	$alertMsg			=	str_replace("#",$userPlan,$alertMsg);
	$alertMsg2			=	str_replace("*",date("d-m-Y", strtotime($resUserPlan['payment_expiry_date'])),$parObj->_getLabenames($arrayData,'planChangeAlertMsg2','name'));	
				
}	
//here need to write pop up message code for mobile paid users 

 		
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
	if($value['plan_id']==2) // currently not seen on db,so no need to check it 
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
			$plan12['month_amount']	 =	$currentMonthAmount;
			$plan12['plan_amount']	 =	$payAfterDiscount;
			$plan12['plan_discount'] =	$currentDiscount;
		}
		else
		{
			$plan12['month_amount']	 =	$value['plan_month_amount'];
			$plan12['plan_amount']	 =	$value['plan_amount'];
			$plan12['plan_discount'] =	$value['plan_discount'];
		}
	}
}
$_SESSION['payment_amount_new'] = $perMonthAmount;
if(trim($_POST['user_discount'])!="" ):
	$errorMsg = 0;
	include('userdiscount_beta.php');	
		
	if($errorMsg == 0):	
		$_SESSION['discount_verify'] = true;
		header("Location:payment_new.php?payment_plan=".$_REQUEST['payment_plan']);
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
/*=============================from old code ===========*/
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
    }
	?>
	<script type="text/javascript">
		/*$(document).ready(function()
		{
			if(<?=$resUserQry['user_language'];?>	==	5)
			{
				if(document.getElementById('payment_plan12').checked)
				{
					document.getElementById('payment_plan12').value='12';
				}
				else if(document.getElementById('payment_plan6').checked)
				{
					document.getElementById('planId_paypal').value='6';
				}
				else if(document.getElementById('payment_plan3').checked)
				{
					document.getElementById('planId_paypal').value='3';
				}
				else if(document.getElementById('payment_plan1').checked)
				{
					document.getElementById('planId_paypal').value='1';
				}
			}
			
		});*/

		function changePlanValPaypal(planId)
		{
			if(<?=$resUserQry['user_language'];?>	==	5)
			{
				
				document.getElementById('planId_paypal').value=planId;
				
			}
		}
		
		function checkBoxSts(checkId,buttonText)
		{
			var buttonText	=	buttonText;		
			//alert(buttonText)	;return false;
			document.getElementById("payment"+checkId).checked=true;
			planIdSelected = document.getElementById("payment"+checkId).value;	// payment_plan			
			$('#newpaymentPageFrom').html('<input type="submit" disabled="disabled" value="'+buttonText+'"  class="style1" >');
			$.ajax({
				url : 'payment_new.php',
				type: "POST",
				data: "updatePaymentPlan=1&payment_plan_id="+planIdSelected,
				success: function(response){
					$('#newpaymentPageFrom').html(response);
						
				}
					
			});
		}
		
		<?php if(isset($_POST['token']) == "") //if user not make any pay now,default 12 mnth selected 
				{ ?>
		$(document).ready(function(){
			//alert($( "input[name='payment_plan']:checked" ).val());	
			defaultPlanId = $( "input[name='payment_plan']:checked" ).val();			
			$.ajax({
				url : 'payment_new.php',
				type: "POST",
				data: "updatePaymentPlan=1&payment_plan_id="+defaultPlanId,
				success: function(response){
					
					$('#newpaymentPageFrom').html(response);
						
				}
					
			});
		});
		<?php } ?>
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
			}	//alert(selectedVal);return false;
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
		}var isSubmit = 0;
		
	</script> 
    <?php
/*=============================from old code ends ===========*/
$homeUrl	=	ROOT_JWPATH.'index.php'; 	
$userlang         = $resUserQry['user_language']	;

?>
 <section class="payment-new">         
         <div class="payment-wrapper">
         <div class="breadcrumbs">
    <ul>
        <li></li>
        </ul>

</div>
   <div class="heading2">
    <span class="name"><a href="#"><?=$parObj->_getLabenames($arrayData,'newHeadTxt','name');?></a></span>
</div>

  </div>
</section>
<section class="payment-boxcontent">
   <article>
       <div class="payment_left">
     <form method="post" action="payment_new.php" id="plan" onSubmit="return validatePaymentNew(<?php echo $languageid;?>);" name="plan">  
     <?php
	  if($lanId	==	5)
	  {
		   if(sizeof($plan12) > 0)
        	{  ?>
            <div class="twelve-pass" id="_plan12" onclick="checkBoxSts(this.id,'<?php echo $name_tag;?>');">
        <div class="pass12">
            <ul>
                <li>
                   
                        <input type="radio" name="payment_plan" id="payment_plan12" value="12" <?php if(($userPlan == 12) || sizeof($count_result) == 0 || $_REQUEST['payment_plan']	==	12) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='12';"/>
                </li>
                <li>
                    <h2><?=$parObj->_getLabenames($arrayData,'offer1','name');?>: <span class="d-price"><?php if($discountForAllPlans){echo $payAfterDiscount.$parObj->_getLabenames($arrayData,'currency','name');}else{ echo $plan12['plan_amount'].$parObj->_getLabenames($arrayData,'currency','name');}?></span>
                    <!--<span style="font-style: italic;"><?=$plan12['month_amount'].$parObj->_getLabenames($arrayData,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span>-->
                    </h2>
                    <?=$plan12['month_amount'].$parObj->_getLabenames($arrayData,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?>
                    <span style="text-decoration: line-through;"><?=$parObj->_getLabenames($arrayData,'actualPaymentAmount','name').$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?></span>
                     </li>
            </ul>
        </div>
        
        <div class="round-offer-red">
        <h2>-<?=$plan12['plan_discount']?>%</h2>
        <span><?=$parObj->_getLabenames($arrayData,'newDisCaptTxt','name');?></span>
    </div>
        
    </div>
    <?php	}
	if(sizeof($plan6) > 0)
    { ?>
    <div class="pass-blue" id="_plan6"  onclick="checkBoxSts(this.id,'<?php echo $name_tag;?>');">
        <div class="pass6">
            <ul>
                <li>
                   
                        <input type="radio" name="payment_plan" id="payment_plan6" value="6" <?php if($userPlan == 6	||	$_REQUEST['payment_plan']	==	6) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='6';"/>
                </li>
                	<li>
                    <h2> <?=$parObj->_getLabenames($arrayData,'offer2','name');?>: <span class="d-blue"><?=$plan6['plan_amount'].$parObj->_getLabenames($arrayData,'currency','name');?></span>
                   <!-- <span style="font-style: italic;"><?=$plan6['month_amount'].$parObj->_getLabenames($arrayData,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span>-->
                    </h2>
                    <?=$plan6['month_amount'].$parObj->_getLabenames($arrayData,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?>
                    <span style="text-decoration: line-through;"><?=$parObj->_getLabenames($arrayData,'actualPaymentAmount','name').$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?></span>
                    </li>
            </ul>
        </div>
        
        <div class="round-offer-blue">
            <h2>-<?=$plan6['plan_discount']?>%</h2>
            <span><?=$parObj->_getLabenames($arrayData,'newDisCaptTxt','name');?></span>
    	</div>        
   </div>
		
		
<?php	}
		if(sizeof($plan3) > 0)
        { ?> 
        <div class="pass-blue" id="_plan3" onclick="checkBoxSts(this.id,'<?php echo $name_tag;?>');">
        <div class="pass6">
            <ul>
                <li>
                    
                        <input type="radio" name="payment_plan" id="payment_plan3" value="3" <?php if($userPlan == 3	||	$_REQUEST['payment_plan']	==	3) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='3';"/>
                </li>
                <li>
                    <h2><?=$parObj->_getLabenames($arrayData,'offer3','name');?>: <span class="d-blue"><?=$plan3['plan_amount'].$parObj->_getLabenames($arrayData,'currency','name');?></span>
                    <!--<span style="font-style: italic;"><?=$plan3['month_amount'].$parObj->_getLabenames($arrayData,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span>-->
                    </h2> 
                    <?=$plan3['month_amount'].$parObj->_getLabenames($arrayData,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?>
                     <span style="text-decoration: line-through;"><?=$parObj->_getLabenames($arrayData,'actualPaymentAmount','name').$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?></span>
                    </li>
            </ul>
        </div>
        
        <div class="round-offer-blue">
        <h2>-<?=$plan3['plan_discount']?>%</h2>
        <span><?=$parObj->_getLabenames($arrayData,'newDisCaptTxt','name');?></span>
    </div>
        
    </div>
        
	    <?php }
		if(sizeof($plan1) > 0	&&	$discountForAllPlans) 
			{ ?>
            <div class="pass-blue-1" id="_plan1" onclick="checkBoxSts(this.id,'<?php echo $name_tag;?>');">
        <div class="pass1">
            <ul>
                <li>
                 
                        <input type="radio" name="payment_plan" id="payment_plan1" value="1" <?php if($userPlan == 1	||	$_REQUEST['payment_plan']	==	1) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='1';"/> 
                </li>
                <li>
                    <h2><?=$parObj->_getLabenames($arrayData,'offer4','name');?>:<span class="d-blue"><?=$plan1['plan_amount'].$parObj->_getLabenames($arrayData,'currency','name');?></span>
                    <!--<span style="font-style: italic;"> <?=$plan1['month_amount'].$parObj->_getLabenames($arrayData,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span>-->
                    </h2>
                     <?=$plan1['month_amount'].$parObj->_getLabenames($arrayData,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?>
                     <span style="text-decoration: line-through;"><?=$parObj->_getLabenames($arrayData,'actualPaymentAmount','name').$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?></span>
                     <!--need from old code ?-->
                   </li>
            </ul>
        </div> 
    </div>
            
		<?php }
		elseif((sizeof($plan1) > 0)	&&	($discUser))
			{
				$discText		=	$parObj->_getLabenames($arrayData,'validDiscUser','name');
				$currencyPart	=	$plan1['month_amount']." ".$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');
				$discText		=	str_replace("##",$currencyPart,$discText);?>
                
            <div class="pass-blue-1"  id="_plan1"  onclick="checkBoxSts(this.id,'<?php echo $name_tag;?>');">>
        <div class="pass1">
            <ul>
                <li>
                    
                        <input type="radio" name="payment_plan" id="payment_plan1" value="1" <?php if($userPlan == 1	||	$_REQUEST['payment_plan']	==	1) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='1';"/>
                </li>
                <li>
                    <h2><?=$parObj->_getLabenames($arrayData,'offer4','name');?> (<?=$parObj->_getLabenames($arrayData,'planNewTxt','name');?>):<span class="d-blue">  <?=$discountAmount;?></span>
<!--                    <span style="font-style: italic;"><?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?>/<?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span>
-->                    </h2>
?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?>/<?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?>
(<?=$discText;?>)
                   </li>
            </ul>
        </div>        
    </div>
		<?php	}
		elseif(sizeof($plan1) > 0) 
          	{   ?>
            <div class="pass-blue-1" id="_plan1"  onclick="checkBoxSts(this.id,'<?php echo $name_tag;?>');">
        <div class="pass1">
            <ul>
                <li>
                  
                        <input type="radio" name="payment_plan" id="payment_plan1" value="1" <?php if($userPlan == 1	||	$_REQUEST['payment_plan']	==	1) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='1';"/>
                </li>
                <li>
                    <h2><?=$parObj->_getLabenames($arrayData,'offer4','name');?> (<?=$parObj->_getLabenames($arrayData,'planNewTxt','name');?>):<span class="d-blue"> </span>
                    <!--<span style="font-style: italic;"> <?=$plan1['month_amount'].$parObj->_getLabenames($arrayData,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayData,'singleMonth','name');?></span>-->
                    </h2>
                     <?=$plan1['month_amount'].$parObj->_getLabenames($arrayData,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayData,'singleMonth','name');?>
                   </li>
            </ul>
        </div>
    </div>
 <?php }
 }
	  else
	  {
		  if(sizeof($plan12) > 0) 
		  {  ?>
			  <div class="twelve-pass" id="_plan12" onclick="checkBoxSts(this.id,'<?php echo $name_tag;?>');">
        <div class="pass12">
            <ul>
                <li>
                   
                        <input type="radio" name="payment_plan" id="payment_plan12" value="12" <?php if(($userPlan == 12) || sizeof($count_result) == 0 || $_REQUEST['payment_plan']	==	12) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='12'"/> 
                </li>
                <li>
                    <h2><?=$parObj->_getLabenames($arrayData,'offer1','name');?>: <span class="d-price"><?=$plan12['month_amount'];?></span><span style="font-style: italic;"><?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?>/<?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span></h2><?=$parObj->_getLabenames($arrayData,'actualPaymentTxt','name')." ";?> <span style="text-decoration: line-through;"><?=$parObj->_getLabenames($arrayData,'actualPaymentAmount','name').$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?></span>
                    <br>  <?=$parObj->_getLabenames($arrayData,'invoicedetails1','name')." ".$plan12['plan_amount']." ".$parObj->_getLabenames($arrayData,'currency','name');?></li>
            </ul>
        </div>
        
        <div class="round-offer-red">
        <h2>-<?=$plan12['plan_discount']?>%</h2>
        <span><?=$parObj->_getLabenames($arrayData,'newDisCaptTxt','name');?></span>
    </div>
        
    </div>
		 <?php }
		 if(sizeof($plan6) > 0)
		 { ?>
			 <div class="pass-blue" id="_plan6"  onclick="checkBoxSts(this.id,'<?php echo $name_tag;?>');">
        		<div class="pass6">
                <ul>
                    <li>
                   
                        <input type="radio" name="payment_plan" id="payment_plan6" value="6" <?php if($userPlan == 6 ||	$_REQUEST['payment_plan']	==	6) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='6'"/> 
                </li>
                <li>
                    <h2><?=$parObj->_getLabenames($arrayData,'offer2','name');?>: <span class="d-blue"><?=$plan6['month_amount'];?></span><span style="font-style: italic;"><?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?>/<?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span></h2> <?=$parObj->_getLabenames($arrayData,'actualPaymentTxt','name')." ";?> <span style="text-decoration: line-through;"><?=$parObj->_getLabenames($arrayData,'actualPaymentAmount','name').$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?></span><br> 
<?=$parObj->_getLabenames($arrayData,'invoicedetails1','name')." ".$plan6['plan_amount']." ".$parObj->_getLabenames($arrayData,'currency','name');?></li>
            </ul>
        </div>
        
        <div class="round-offer-blue">
        <h2>-<?=$plan6['plan_discount']?>%</h2>
        <span><?=$parObj->_getLabenames($arrayData,'newDisCaptTxt','name');?></span>
    </div>
        
    </div>
	<?php	 }
		 if(sizeof($plan3) > 0)
		 { ?>
		 <div class="pass-blue" id="_plan3" onclick="checkBoxSts(this.id,'<?php echo $name_tag;?>');">
        <div class="pass6">
            <ul>
                <li>
                  
                        <input type="radio"  name="payment_plan" id="payment_plan3" value="3" <?php if($userPlan == 3	||	$_REQUEST['payment_plan']	==	3) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='3'"/>
                </li>
                <li>
                    <h2><?=$parObj->_getLabenames($arrayData,'offer3','name');?>: <span class="d-blue"><?=$plan3['month_amount'];?></span><span style="font-style: italic;"><?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?>/<?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span></h2><?=$parObj->_getLabenames($arrayData,'actualPaymentTxt','name')." ";?> <span style="text-decoration: line-through;"><?=$parObj->_getLabenames($arrayData,'actualPaymentAmount','name').$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?></span>
                    <br> <?=$parObj->_getLabenames($arrayData,'invoicedetails1','name')." ".$plan3['plan_amount']." ".$parObj->_getLabenames($arrayData,'currency','name');?> </li>
           </ul>
        </div>
        
        <div class="round-offer-blue">
        <h2>-<?=$plan3['plan_discount']?>%</h2>
        <span><?=$parObj->_getLabenames($arrayData,'newDisCaptTxt','name');?></span>
    </div>
   </div>
   <?php  }
   //remove code from old page with if(sizeof($plan2) > 0),because no planid as 2
   if((sizeof($plan1) > 0)	&&	($discUser))
			{ 
				$discText		=	$parObj->_getLabenames($arrayData,'validDiscUser','name');
				$currencyPart	=	$plan1['month_amount']." ".$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');
				$discText		=	str_replace("##",$currencyPart,$discText);
				?>
                <div class="pass-blue-1" id="_plan1"  onclick="checkBoxSts(this.id,'<?php echo $name_tag;?>');">
        <div class="pass1">
            <ul>
                <li>
                  
                        <input type="radio" name="payment_plan" id="payment_plan1" value="1" checked="checked" onclick="javascript:document.getElementById('selectedPlan').value='1'"/>
                </li>
                <li>
                    <h2><?=$parObj->_getLabenames($arrayData,'offer4','name');?> (<?=$parObj->_getLabenames($arrayData,'planNewTxt','name');?>):<span class="d-blue"> <?=$discountAmount;?></span><span style="font-style: italic;"> <?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?>/<?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?> </span></h2>(<?=$discText;?>)
                   </li>
            </ul>
        </div>
    </div>
	<?php	}
	elseif(sizeof($plan1) > 0)
	 { ?>  
     <div class="pass-blue-1" id="_plan1"  onclick="checkBoxSts(this.id,'<?php echo $name_tag;?>');">
        <div class="pass1">
            <ul>
                <li>
                 
                        <input type="radio" name="payment_plan" id="payment_plan1" value="1" <?php if($userPlan == 1	||	$_REQUEST['payment_plan']	==	1) echo "checked='checked'"?> onclick="javascript:document.getElementById('selectedPlan').value='1'"/> 
                </li>
                <li>
                    <h2><?=$parObj->_getLabenames($arrayData,'offer4','name');?> (<?=$parObj->_getLabenames($arrayData,'planNewTxt','name');?>):<span class="d-blue">  <?=$plan1['month_amount'];?></span><span style="font-style: italic;">  <?=$parObj->_getLabenames($arrayData,'newCurrencyTxt','name');?>/<?=$parObj->_getLabenames($arrayData,'newMonthsTxt','name');?></span></h2>
                   </li>
            </ul>
        </div>
    </div>
<?php 	}
?>
</form>
<!--link for pop up disc-gift codes and TC-->
<div class="pre-next">
    <span class="prev"><a onclick="return showDiscountPopup();" href="javascript:void(0)"  id="disCouponTgr">&gt; <?=$parObj->_getLabenames($arrayData,'newCpnTxt','name');?></a></span>
        <span class="next"><a href="javascript:void(0)" onclick="return unsubscribe();" style="color: #1996c0;" ><strong>&gt; <?=$parObj->_getLabenames($arrayData,'newTermsLnkTxt','name');?></strong></a></span>
    </div>
    <script type="text/javascript">
					$(document).ready(function(){
						<?php if((($errorMsg == 1)||($errorMsgNull))	&&	(!isset($_POST['oxysubmit']))){?>
							showDiscountPopup();
						<?php }?>
					});
				</script>

<?php }  
	 ?>
       <!--services -->       
    <div class="service-inclus">
        <h2><?=$parObj->_getLabenames($arrayDataPay,'service','name');?></h2>
    <ul>
    		<li><?=$parObj->_getLabenames($arrayDataPay,'service1','name');?>  </li>
      		<li><?=$parObj->_getLabenames($arrayDataPay,'service2','name');?> </li>
      		<li><?=$parObj->_getLabenames($arrayDataPay,'service3','name');?> </li>
      		<li><?=$parObj->_getLabenames($arrayDataPay,'service4','name');?> </li>
          <?php if($lanId!=5){ ?> <li><?=$parObj->_getLabenames($arrayDataPay,'service5','name');?> </li> <?php } ?>
 
    </ul>
    </div>
    </div>
</article>
<!--------------pop up from old code-->
<div class="pop_unsubscribePgm" id="unsubscribePgm" style="top: 50px; left: 290px; position:fixed; display:none; z-index:10;">    	
          <div class="popbox_unsubscribePgm"> 
        
			<h3><?=$parObj->_getLabenames($arrayDataPay,'paymentPopupTitle','name');?></h3>
            <div id="scroll" style="height:250px; overflow:auto;">
				<table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
				<tr>
				<?php if($lanId!=5){ ?>
				  <td colspan="2" align="center"><?=normalze(nl2br(html_entity_decode($parObj->_getLabenames($arrayDataPay,'paymentPopupPara1','name'), ENT_QUOTES, "utf-8")));?></td><?php }else{?><td colspan="2" align="center"><?=html_entity_decode($parObj->_getLabenames($arrayDataPay,'paymentPopupPara1','name'), ENT_QUOTES, "utf-8");?></td><?php }?>
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
	</div>
    <!----------------pop up end -->
       <article> 
           <div class="payment_right">
           <!------payment via stripe -->
               <div class="payment-information">
               <?php
			   
			    if(sizeof($count_result) > 0	&&	$resUserQry['user_language']	!=	5)
				{ 
					?>
                     <div class="info-bg-head">
                   <h2><?=$parObj->_getLabenames($arrayData,'planChangeHead','name');?></h2>
                   </div>
                   <form method="post" action="payment_new.php" id="plan" onSubmit="return validatePaymentNew(<?php echo $languageid;?>);" name="plan">
                   <div class="account">
            <div class="lock-info">
                <img src="<?php echo ROOT_FOLDER; ?>images/lock-info.png" alt="" />
            </div>
            <div class="security-info">
                <h2><?=$parObj->_getLabenames($arrayData,'newPaymentTxt','name');?>  100%   <?=$parObj->_getLabenames($arrayData,'newSecureTxt','name');?></h2>
            </div>
<div class="payment-gateway">
    <img src="<?php echo ROOT_FOLDER; ?>images/transction.png" alt="" />
    <ul>
    <li><img src="<?php echo ROOT_FOLDER; ?>images/visa-payment.png" alt="" /></li>
        <li><img src="<?php echo ROOT_FOLDER; ?>images/ce-payment.png" alt="" /></li>
        <li><img src="<?php echo ROOT_FOLDER; ?>images/mastercard.png" alt="" /> </li>
    </ul>
            </div>
                       <div class="info-btn" id="newpaymentPageFrom">
                       
          <!-- <h2>Payez maintenant ></h2>-->
        </div>
        </div>
        </form>
			<?php	}
				else
				{ 
				 ?>
                <div class="info-bg-head">
                   <h2><?=$parObj->_getLabenames($arrayData,'newBnkInfoTxt','name');?></h2>
                   </div>
                   <div class="account">
            <div class="lock-info">
                <img src="<?php echo ROOT_FOLDER; ?>images/lock-info.png" alt="" />
            </div>
            <div class="security-info">
                <h2><?=$parObj->_getLabenames($arrayData,'newPaymentTxt','name');?>  100%   <?=$parObj->_getLabenames($arrayData,'newSecureTxt','name');?></h2>
            </div>
<div class="payment-gateway">
    <img src="<?php echo ROOT_FOLDER; ?>images/transction.png" alt="" />
    <ul>
    <li><img src="<?php echo ROOT_FOLDER; ?>images/visa-payment.png" alt="" /></li>
        <li><img src="<?php echo ROOT_FOLDER; ?>images/ce-payment.png" alt="" /></li>
        <li><img src="<?php echo ROOT_FOLDER; ?>images/mastercard.png" alt="" /> </li>
    </ul>
            </div>
                       <div class="info-btn" id="newpaymentPageFrom">
        <!--   <h2>Payez maintenant ></h2>-->
        </div>
        </div>
                
					
			<?php	}?>
              
               
               </div>
 <?php if($lanId==2 && 	$resUserQry['user_language']	!=	5)
    { 
        include("oxyPayment.php");  
    } 
	if($lanId	==	5)
	{
		?>
        <div class="voucher_section">   
        <h2>Kup bon upominkowy Jiwoka!</h2>
        <div class="right-acc-login">
            Jiwok oferuje także abonamenty w formie bonów upominkowych. Dzięki temu, za pomocą pojedynczego kliknięcia, możesz zrobić bliskiej Ci osobie oryginalny, praktyczny i zdrowy prezent: Treningi sportowe z osobistym trenerem na mp3!
To proste: przesyłasz bon e-mailem lub drukujesz i wkładasz do koperty.
<div class="right-login-btn">
<h2><a href="<?php echo ROOT_JWPATH?>giftreg.php" class="btn"><b>Spraw komuś prezent już teraz!</b></a></h2>
</div>

        </div>
 </div>
 <?php
	}
	//from old code ===========
	/*
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
	*/
	
	//================
      if($resUserQry['user_language']	!=	5)
	{?>         
               
           <div class="right_desc">              
              <h3><?=$parObj->_getLabenames($arrayData,'newBtmTxtHead','name');?>:</h3>
                  <p><?=$parObj->_getLabenames($arrayData,'newBtmTxt','name');?></p>
              </div>
   <?php }
   ?>
 </div> 
 <!--===========discount /gift pop up============-->
 <div class="pop_renewSubscriptionId" id="renewSubscriptionId" style="display:none; z-index:10">
	    <div class="popbox_renewSubscriptionId"> 
        <a id="fancybox-close1" onclick="renewSubscriptionDisplay();" title="close" style="display: inline;"></a>
		<h3> 
		  <?php if($userpaymentstatus == 0){ echo $parObj->_getLabenames($arrayDataProfile,'popuppayment','name'); } else { echo $parObj->_getLabenames($arrayDataProfile,'renewSubscription','name');} ?>
		</h3><hr style="color:#f4d03e;" />
		<form name="renewSubscriptionFrm" action="" method="post" >
		  <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
			<tr>
			  <td colspan="2" align="center" class="error_message"><?php if($errorMsg == 1){echo $discountMsg;} else {if($errorMsgNull) echo $errorMsgNull;}?></td>
			</tr>
			<tr>
			  <td width="50%" align="left" valign="middle"><?=$parObj->_getLabenames($arrayDataProfile,'renewDisc','name');?></td>
			  <td width="50%">
				  <span class="login"><input type="text" class="field" name="user_discount" value="" ></span>
			  </td>
			</tr>
			 <!--<?=$parObj->_getLabenames($arrayDataProfile,'gotonext','name');?>--><input type="hidden" name="selectedPlan" value="<?php if(sizeof($result1) > 0	&&	$resUserQry['user_language']	!=	5) echo $userPlan; else echo '12';?>" id="selectedPlan"/>
		
			<tr>
			  <td colspan="2" align="center"><input class="btn_pop ease" name="renewSubscriptionIdBtn" type="submit" value="<?=$parObj->_getLabenames($arrayDataProfile,'renew','name');?>" /></td>
			</tr>
		  </table>
		</form>
		<div class="clear"></div>
	    </div>
       </div>
        <!--=======================-->
<div class="pop_paymantCmnAlertMsg" id="paymantCmnAlertMsg" style="display:none;position:fixed;z-index:100000;">
  <a href="javascript:void(0)" title="close" id="closePaymantCmnAlertMsg" style="display: inline;"></a>
  <div class="popbox_paymantCmnAlertMsg">  
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center" class="pop-box" ><div id="alertMsgCmnPayment"></div></td>
      </tr>
      <tr>
      <?php
	  if(((sizeof($result1) == 0) ||(sizeof($striperesult1) == 0)  ) &&	(sizeof($result3) > 0) &&	($result4[0]['count'] > 0) && !isset($_POST['make_payment']) && (sizeof($result2) == 0)&& !$discUser	&&	!isset($_POST['oxysubmit']) )
{
	$expirydatemsg			= 1;
	/*$expirydatemsg			=	$parObj->_getLabenames($arrayData,'oldPaiUser','name');
	$expireDateQry	=  "select max(payment_expdate) as payment_expdate FROM payment where payment_userid='".$userId."' AND 	payment_status = '1'";
	$userExpireDate	=	$newpaymentClass->dbSelectOne($expireDateQry);	 
	$expirydatemsg			=	str_replace("##",date("d-m-Y", strtotime($userExpireDate['payment_expdate'])),$expirydatemsg);	*/
	}
	else
	{
		$expirydatemsg	=	"";
	}

	  ?>
        <td align="center"><a id="okIdCmnPaymentAlert"><input class="btn_pop ease" onclick="close_popup_confirm_alert('<?php echo $expirydatemsg;?>');"  name="renewSubscriptionIdBtn" type="button" value="Ok" /></a></td>
      </tr>
    </table>
    <div class="clear"></div>
  </div>
</div>
<!--==================-->
<!--====new popup checking after retry period poup on loading ==========-->
<div class="pop_paymantCmnAlertMsg" id="paymantExpiryMsg" style="display:none;position:fixed;z-index:100000;">
  <a href="javascript:void(0)" title="close" id="closePaymantCmnAlertMsg" style="display: inline;"></a>
  <div class="popbox_paymantCmnAlertMsg">  
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
       <?php
	  if(((sizeof($result1) == 0) ||(sizeof($striperesult1) == 0)  ) &&	(sizeof($result3) > 0) &&	($result4[0]['count'] > 0) && !isset($_POST['make_payment']) && (sizeof($result2) == 0)&& !$discUser	&&	!isset($_POST['oxysubmit']) )
{
	
	$expirydatemsg			=	$parObj->_getLabenames($arrayData,'oldPaiUser','name');
	$expireDateQry	=  "select max(payment_expdate) as payment_expdate FROM payment where payment_userid='".$userId."' AND 	payment_status = '1'";
	$userExpireDate	=	$newpaymentClass->dbSelectOne($expireDateQry);	 
	$expirydatemsg			=	str_replace("##",date("d-m-Y", strtotime($userExpireDate['payment_expdate'])),$expirydatemsg);	
	}
	else
	{
		$expirydatemsg	=	"";
	}

	  ?>
        <td align="center" class="pop-box" ><div id="alertMsgPaymentExpiry"><?php echo $expirydatemsg;?></div></td>
      </tr>
      <tr>
        <td align="center"><a id="okIdCmnPaymentAlert"><input class="btn_pop ease" onclick="close_popup_confirm_expiryAlert();"  name="renewSubscriptionIdBtn" type="button" value="Ok" /></a></td>
      </tr>
    </table>
    <div class="clear"></div>
  </div>
</div>
<!--==================-->
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
			if(msgTxt==2){
				showPopup("paymantExpiryMsg","");
				jpopup = $('#paymantExpiryMsg').bPopup({speed: 500,positionStyle: 'absolute', });
			}
			else{
			document.getElementById("alertMsgCmnPayment").innerHTML	=	msgTxt;
			showPopup("paymantCmnAlertMsg","");
			jpopup = $('#paymantCmnAlertMsg').bPopup({speed: 500,positionStyle: 'absolute', });
			}
		}
		
		var def = $.Deferred();
		setTimeout(def.resolve, 4000);
		$("#okIdCmnPaymentAlert").click(function(){
			disablePopupGeneral("paymantCmnAlertMsg","");
		});
		$("#closePaymantCmnAlertMsg").click(function(){
			disablePopupGeneral("paymantCmnAlertMsg","");
		});
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
<!--==============-->
<div class="pop_paymantAlertMsg" id="paymantAlertMsg" style="display:none;position:fixed;z-index:100000;">
  <div class="popbox_paymantAlertMsg">
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center" style="text-align:left"><div id="alertMsgPayment"></div><br/><br/></td>
      </tr>
      <tr>
        <td align="center"><a id="okIdPaymentAlert"><input class="btn_pop ease"  name="renewSubscriptionIdBtn" type="button" value="Ok" /></a></td>
      </tr>
    </table>
    <div class="clear"></div>
  </div>
</div>
<!--==============-->
<!-- Discount code pop up confrm msg -->
<div class="pop_DiscountCmnAlertMsg" id="DiscountCmnAlertMsg" style="display:none;position:fixed;z-index:100000;">
 <div class="popbox_DiscountCmnAlertMsg">
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center" style="text-align:left"><div id="alertMsgCmnDiscount"></div><br/><br/></td>
      </tr>
      <tr>
        <td align="center"><a id="okIdCmnDiscountAlert">
			<input class="btn_pop ease"  name="renewSubscriptionIdBtn" type="button" value="Ok" /></a></td>
      </tr>
    </table>
    <div class="clear"></div>
</div>
</div>
<!--Discount code pop up confrm msg  ends -->

<!-- from old code plan change pop up dijo-->
<div class="pop_planChangeAlertMsg" id="planChangeAlertMsg" style="display:none;position:fixed;z-index:10;width:431px;">
 <div class="popbox_planChangeAlertMsg">
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center" style="text-align:left"><div id="alertMsgPlanChange"></div><br/><br/></td>
      </tr>
      <tr>
        <td align="center">
		<a id="cancelPlanChange">
		&nbsp;&nbsp;<input class="btn_pop ease"  name="planChangeCancel" id="planChangeCancelid" type="button" value="<?=$parObj->_getLabenames($arrayData,'cancelbutton','name');?>" /></a>&nbsp;&nbsp;
        <a id="okPlanChange"><input class="btn_pop ease"  id="planChangeOK" name="planChangeOK" type="button" value="Ok" /></a></td>
      </tr>
    </table>
  </div>
 </div>
<!--plan change pop up dijo ends-->
<script>
	$(document).ready(function(){
        $("#okIdPaymentAlert").click(function(){
			disablePopupGeneral("paymantAlertMsg","");
			$("#paymantAlertMsg").bPopup().close();//gg
		});
		//Press Escape event!
		$(document).keypress(function(e){
			if(e.keyCode==27 && popupStatus==1){
				disablePopupGeneral("paymantAlertMsg","");
				$("#paymantAlertMsg").bPopup().close();//gg
			}
		});
	});
	function close_popup_confirm_alert(expirydatemsg){
		var msg	=	expirydatemsg;
		document.getElementById("paymantCmnAlertMsg").style.display="none";
		$("#paymantCmnAlertMsg").bPopup().close();
		if(msg	!='')
		{
			showPopup("paymantExpiryMsg","");
			jpopup = $('#paymantExpiryMsg').bPopup({speed: 500,positionStyle: 'absolute', });//gg
		}		
		}
		function close_popup_confirm_expiryAlert(){
			$("#paymantExpiryMsg").bPopup().close();
		}
</script>
<?php  if(($errorMsg == 1 && $_REQUEST['user_discount'] !="")):	?>
	<script>//showDiscountPopup();</script>	
<?php
endif;		
if(($errorMsg == 4) && (isset($_POST['renewSubscriptionIdBtn']))):		
?>
	<script>//showDiscountPopup();</script>
<?php endif; ?>
<script type="text/javascript">
					$(document).ready(function(){
						<?php if((($errorMsg == 1)||($errorMsgNull))	&&	(!isset($_POST['oxysubmit']))){?>
						showDiscountPopup();
						$("#paymantCmnAlertMsg").bPopup().close();
						<?php }?>
					});
</script>





<section class="pop">
         <div class="popbox">
         <center><strong>Nous n'avons pas pu débiter votre carte. Est ce que vous pourriez réessayer dans quelques minutes. Merci</strong></center>
         </div>
</section>

      </article>    
         
</section>
<?php

function getPaymentButton($planId,$count_result,$userlang,$name_tag,$attempt_count)
{
	
	$form_content	='<form method="post" action="payment_new.php" id="plan"  name="plan">';
	if(($count_result > 0)	&&	($resUserQry['user_language']	!=	5) && ($attempt_count == 0))
	{ 
		$form_content	.= '<input name="make_payment" id="make_payment" type="submit" class="style1" onclick="return planChange();"  value="'.$name_tag.'" />';
       		
	}
	else
	{		
	
	?>
        <!-------stripe payment  scripts------->
        <script src="https://checkout.stripe.com/checkout.js"></script>
		<style>
        .customButton{
            background: none repeat scroll 0 0 #FFE801 !important;
            border: medium none !important;
            color: #2A80B9; !important;
            display: block !important;
            font-size: 16.39px !important;
            padding: 8px 20px !important;
            text-align: center !important;
            linear-gradient(#FFE801, #FFE801);
            width:100%;
            cursor:pointer;
        }
        .customButton1{
            background: none repeat scroll 0 0 #FFE801 !important;
            border: medium none !important;
            color: #E67F23; !important;
            display: block !important;
            font-size: 16.39px !important;
            padding: 8px 20px !important;
            text-align: center !important;
            linear-gradient(#FFE801, #FFE801);
            width:100%;
            cursor:pointer;
        }
        </style>
        <?php
		$dbQuerynn	 =  "select * from jiwok_payment_plan where plan_id='".$planId."' and plan_currency='".$_SESSION['language']['langId']."'";
		$resnn		 =	$GLOBALS['db']->getRow($dbQuerynn, DB_FETCHMODE_ASSOC);
		$amountstripeform      = $resnn['plan_amount']*100;

		 if($_SESSION['language']['langId'] == 2)
		 {
			$stripeform   = 'eur';
			}
		else if($_SESSION['language']['langId'] == 5)
		{
			$stripeform   = 'pln';
		}

		//discount
		if($_SESSION['payment']['discCode'])
			{
				  if($lan==2)
				  {
					   if($planid == 1)
					   {
					   $amountstripeform = '100';
					   $stripeform = 'eur';
					  }
				  }
			}
			$form_content	= '<input type="hidden" name="amount_'.$planId.'" id="amount_'.$planId.'" value="'.$amountstripeform.'" >';
			$form_content	.= 	'<input name="paybutton" type="button" value="'.$name_tag.'" class="style1" id="'.$planId.'" onclick="myfunstripe('.$planId.');" />';
		?>	
			<script>
	function myfunstripe(planId)
	{ 
	
	var planidnew	=	planId;		
     var  amountstripe =	$('#amount_'+planidnew).val();	
	
	
  var handler = StripeCheckout.configure({
    key: 'pk_live_8j42smc327cbrhEl91rQXMRl',
    image: '<?=ROOT_FOLDER?>images/stripe.png',
    locale: 'auto',
    //alert("jj");return false;
    token: function(token) { 
		
		$.ajax({
				url : 'payment_new.php',
				type: "POST",
				data: "stripePayment=1&payment_plan_id="+planidnew+"&token="+token.id+"&stripe_email="+token.email,
				dataType :"text",
				success: function(response){							 
					if(response==1)
					{
						//alert(response);return false;					
						window.location ='userArea.php?origin=payment';
					}
					else 
					{
						//alert("000");return false;
						//document.getElementById('planChangeAlertMsg').style.display="block";
						//alert("payment failed");
						//~ window.location ='payment_new.php';
						//document.getElementById('stripeAlert').style.display="block";
						      $('.pop').bPopup({
	                             speed: 2000,
                                 transition: 'slideDown'
                                });
					}
					
					//~ alert(response);return false;
					//~ window.location ='userArea.php';
					
						//window.location ='payment_new.php';
					
				}
					
			});
      // Use the token to create the charge with a server-side script.
      // You can access the token ID with `token.id`
    }
  });

<?php
if($lanId==2)
{
	$stripecur = 'EUR';
	
}
else if($lanId==5)
{
	$stripecur = 'PLN';
}
?>

    handler.open({
      name: 'Carte Bancaire',
   
        currency: '<?php echo $stripeform ?>',
    // amount: amountstripe,
     panelLabel : "Valider",
   
			email:'<?php echo $_SESSION['user']['user_email'];?>',
			allowRememberMe:false,
			locale:'fr'
      
    });
   // e.preventDefault();
 // });
  // Close Checkout on page navigation
  $(window).on('popstate', function() {
    handler.close();
  });

  }

  
</script>
<!----------stripe payment scripts -------->
<?php

		
		
}
$form_content	.=	'</form>';
return $form_content;
}
?>
>>