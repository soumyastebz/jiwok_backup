<?php
include_once('includeconfig.php');	
include_once('includes/classes/class.newpayment.php');	
include_once('includes/classes/class.Languages.php');
include_once('includes/classes/class.member.php');
include_once('includes/classes/class.Contents.php');
include_once("includes/classes/class.programs.php");
include_once("includes/classes/Payment/class.stripePayment_test.php");



if($_REQUEST['langId']!="")
  $lanId=$_REQUEST['langId'];
else
  $lanId=1;  
	
$payObj 	= 	new newPayment();
$genObj   	=	new General();
$lanObj 	= 	new Language();
$memObj		= 	new Member($lanId);
$objPgm     = 	new Programs($lanId);
$userId		=	$_SESSION['user']['userId'];
$langArray 	= 	$siteLanguagesConfig;
$parObj 	=   new Contents('unSubscibePayBox.php');
$stripe			=	new stripePayment(); 
$AccountExpDate 		= $objPgm->_findAccountExpireDate($userId);
//$strippayObj 	= 	new stripePayment();

$engSuccess	=	"Your Unsubscription was taken into account. Your subscription will end on :".date("d-m-Y", strtotime($AccountExpDate));
$frSuccess	=	"Votre désabonnement a été pris en compte. Votre abonnement se terminera le :".date("d-m-Y", strtotime($AccountExpDate));

$engErrMsg	=	"Error Please try later";
$frErrMsg	=	"S'il vous plaît essayez d'erreur plus tard,";



if($lanId==1){
	$successMsgTxt	=	$engSuccess;
	$errMsgTxt	=	$engErrMsg;
}else{
	$successMsgTxt	=	$frSuccess;
	$errMsgTxt	=	$frErrMsg;	
}


if($_REQUEST['action'] == "unsubscribe" && $_REQUEST['id']!=""){
		   //cancel code here
		  // echo "enter to unsubscribe.php";
		   //echo $_REQUEST['id'];die;
		   $res	=	$payObj->unsubsriptionFromPaybox($_REQUEST['id']);		   
		   if($res == '00000')
		   {
			   	//Update the paybox table entry as unsubscribed				
				$dbValues	=	"status	=	'UNSUBSCRIBED',unsubscribed_date='".date('Y-m-d')."'";
				$dbCond		=	"id='".$_REQUEST['id']."'";
				$dbTable	=	"payment_paybox";
				$payObj->dbUpdate($dbTable,$dbValues,$dbCond); 	
				
				$dbValues		=  "status	=	'UNSUBSCRIBED'";
				$dbCond			=	"pp_id 	=	'".$_REQUEST['id']."'";
				$payObj->dbUpdate('payment_cronjob',$dbValues,$dbCond);	
				
				
				//$userdetails	=	$memObj->_getOneUser($userId);
				
				if($_SESSION['language']['langId'])
					$userlangid	=	$_SESSION['language']['langId'];
				else
					$userlangid	=	1;
				
				if($lanId!='')
				{ 
					$langname=strtolower($langArray[$userlangid]);
				}
				if($langname){$xmlPath="xml/".$langname."/page.xml";}

				

				$returnData		= $parObj->_getTagcontents($xmlPath,'unscubscribe','label');

				$arrayData		= $returnData['general'];

				

				$unscubscribeMail[$userId] = "\n".$parObj->_getLabenames($arrayData,'reg_mail_to','name').",\n\n";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_part1','name')."\n";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_part2','name')."\n";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_part3','name')." \n\n";

				

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_part4','name')." \n";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_part5','name').": ";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_partUrl6','name')."\n\n";

				

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_footer1','name')."\n\n";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_footer2','name').": ";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_footer3Url','name')."\n\n ";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_footer4','name')."\n\n";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_footer5','name')."\n\n";

				$subject_mail[$userId]=$parObj->_getLabenames($arrayData,'subject','name')."\n\n";
				
				
				$mails = $memObj->_sendUnsubscribeMails($userId, $unscubscribeMail,$subject_mail);
						
				$confMsg = $successMsgTxt;	
							 
		   }
		   else
		   {
			   //Unsubscription failed gettng the error message
			   $errMsg	= $payObj->errorMessages($res);
			   $confMsg = $errMsgTxt;
		   }
		
}
else if($_REQUEST['action'] == "unsubscribestripe" && $_REQUEST['id']!=""){

	 $res	=	$stripe ->unsubsriptionFromstripe($_REQUEST['id']);
	  //echo $res;
	 //~ exit;
	if($res === 1)
	{
		//echo "entered 2";
		if($_SESSION['language']['langId'])
					$userlangid	=	$_SESSION['language']['langId'];
				else
					$userlangid	=	1;
				
				if($lanId!='')
				{ 
					$langname=strtolower($langArray[$userlangid]);
				}
				if($langname){$xmlPath="xml/".$langname."/page.xml";}

				

				$returnData		= $parObj->_getTagcontents($xmlPath,'unscubscribe','label');

				$arrayData		= $returnData['general'];

				

				$unscubscribeMail[$userId] = "\n".$parObj->_getLabenames($arrayData,'reg_mail_to','name').",\n\n";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_part1','name')."\n";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_part2','name')."\n";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_part3','name')." \n\n";

				

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_part4','name')." \n";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_part5','name').": ";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_partUrl6','name')."\n\n";

				

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_footer1','name')."\n\n";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_footer2','name').": ";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_footer3Url','name')."\n\n ";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_footer4','name')."\n\n";

				$unscubscribeMail[$userId] .= $parObj->_getLabenames($arrayData,'unscubscribe_mail_footer5','name')."\n\n";

				$subject_mail[$userId]=$parObj->_getLabenames($arrayData,'subject','name')."\n\n";
				$mails = $memObj->_sendUnsubscribeMails($userId, $unscubscribeMail,$subject_mail);
				//echo $userId;
				//print_r($unscubscribeMail);
				//echo($subject_mail);	
				$confMsg = $successMsgTxt;	
	}
	else
	{
		//echo "enteed error";
		$errMsg = "Subscription Cancellation Cannot Be Completed";
		$confMsg = $errMsgTxt;
	}
	
}

else if($_REQUEST['action'] == "unsubscribeOld" && $_REQUEST['id']!=""){
	$memObj->_unsubscribeUserMemebership($userId);
	$confMsg = $successMsgTxt;	
}


echo $confMsg;

?>
