<?php
/**************************************************************************** 

   Project Name	::> Jiwok 

   Module 		::> Admin-Newsletter Management

   Programmer	::> Sreejith E C

   Date			::> 2/2/2007

   

   DESCRIPTION::::>>>>

   This  code userd to add/edit newsletter.

   

*****************************************************************************/

	include_once('includeconfig.php');

	/*ini_set('display_errors',1);

	error_reporting(E_ERROR | E_WARNING);

	assert_options(ASSERT_ACTIVE, 1);

	assert_options(ASSERT_WARNING, 1);

	assert_options(ASSERT_BAIL, 0);*/

	include_once('../includes/classes/class.member.php');
	include_once('../includes/classes/class.newpayment.php');
	include_once('../includes/classes/class.Contents.php');
	include_once("../includes/classes/class.programs.php");
	 include_once ("../stripe_code/config.php");	
	//include_once('../includes/classes/class.DbAction.php');	
	if(isset($_REQUEST['langId'])){

	  $lanId	= (int) $_REQUEST['langId'];

	} else {

	  $lanId=1; 

	}

	//$dbObj	 =	new DbAction();

	$memObj			= 	new Member($lanId);
	$objGen			=	new General();
	$paymentClass	=	new newPayment();
	$parObj 		=   new Contents('update_subscription.php');
	$objPgm     	=	new Programs($lanId);
		

	$heading	= "User Details";

	$errorMsg	=	array();
	$langArray = $siteLanguagesConfig;


	if(isset($_REQUEST['userId'])) {

		$userId	= (int) trim($_REQUEST['userId']);

	}

	if(isset($_REQUEST['exp_date'])){

		if(preg_match('/^(20)((09)|([1-9]{1}[0-9]{1}))-([0-9]{2})-([0-9]{2})$/', $_REQUEST['exp_date'])!=1){

			$errorMsg[]  = 'Expiry date format is wrong: '.$_REQUEST['exp_date'];

		}

		$expiry_date	= trim($_REQUEST['exp_date']);

	}

	$payboxStatus		=	$memObj->getNewPayBoxStatus($userId,'ACTIVE');	
	//stripe
	$payboxStatusstripe		=	$memObj->getStatusstripe($userId,'ACTIVE');
	//stripe
	
	
	$result 			    = 	$memObj->_getOneUser($userId);
	$pay_result			    = 	$memObj->getPaymentStatus($userId);
	$newPaymentReport	    =	$memObj->getNewPaymentReport($userId);
	$newPaymentReportstripe	=	$memObj->getNewPaymentReportstripe($userId);
	$paymentReportIap		=	$memObj->getPaymentReportIap($userId);	
	if(sizeof($errorMsg)==0){

		if(isset($_REQUEST['update_payment'])) 
		{ //echo "here";echo "<pre>";print_r($pay_result);exit;
		  assert(preg_match('/^(20)([0-9]{2})-([0-9]{2})-([0-9]{2})$/', $expiry_date)==1);
			
			if(($pay_result ['version']=='stripe') || ($pay_result ['version']=='mobile_stripe'))
			{ 
				if(!$memObj->updatePaymentstripe($pay_result,$expiry_date))
								{
									$errorMsg[]  = 'Changing payment failed';
								}
  //echo "<pre/>";echo "pp";print_r($_SERVER);exit;
								$redirect =$_SERVER['HTTP_REFERER'];
								header('Location:'.$redirect);exit;
			}
			else if($pay_result ['version']=='New')
			{
				if(!$memObj->updatePayment($_REQUEST['paymentId'], $userId, $expiry_date))
								{
									$errorMsg[]  = 'Changing payment failed';
								}
								$redirect =$_SERVER['HTTP_REFERER'];
								header('Location:'.$redirect);exit;
			}
			else if($pay_result ['version']=='polishstripe')
			{				
				$payid 				= $pay_result['payment_id'];	
				$dbValues1			=  "payment_expdate 	=	'".$expiry_date."'";
				$dbCond1			=	"payment_id 	=	'".$payid."'";
				$paymentClass->dbUpdate('payment',$dbValues1,$dbCond1);	
				
								$redirect =$_SERVER['HTTP_REFERER'];
								header('Location:'.$redirect);exit;
			}
			else
			{
				if(!$memObj->updatePayment($_REQUEST['paymentId'], $userId, $expiry_date))
								{
									$errorMsg[]  = 'Changing payment failed';
								}
								$redirect =$_SERVER['HTTP_REFERER'];
								header('Location:'.$redirect);exit;
			}
			
								
		} 
		elseif(isset($_REQUEST['subscribe'])) 
		{
			$memObj->_sentReqstMemSubscribe($userId);
		} 
		elseif (isset($_REQUEST['unsubscribe']))
		{
			$memObj->_unsubscribeUserMemebership($userId);
		}
		elseif (isset($_REQUEST['newPayboxUnsubscribe']))
		{
			$res	=	$paymentClass->unsubsriptionFromPaybox($payboxStatus[0][id]);			
		   	if($res == '00000')
		   	{
			  	//Update the paybox table entry as unsubscribed				
				$dbValues	=	"status	=	'UNSUBSCRIBED'";
				$dbCond		=	"id='".$payboxStatus[0][id]."'";
				$dbTable	=	"payment_paybox";
				$paymentClass->dbUpdate($dbTable,$dbValues,$dbCond); 	
				
				$dbValues		=  "status	=	'UNSUBSCRIBED'";
				$dbCond			=	"pp_id 	=	'".$payboxStatus[0][id]."'";
				$paymentClass->dbUpdate('payment_cronjob',$dbValues,$dbCond);	
				//---------------------------------------------------		         

				$userdetails=$memObj->_getOneUser($userId);

				if($userdetails)

				{$userlangid=$userdetails['user_language'];}

				if($userlangid!='')

				{ 

					$langname=strtolower($langArray[$userlangid]);

				}

				if($langname){$xmlPath="../xml/".$langname."/page.xml";}

				

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
				//---------------------------------------------------						
				$confMsg = "Successfully Unsubscribed";	
							 
			}
		}
		elseif (isset($_REQUEST['stripeUnsubscribe']))
		{
						$bool=0;
					$sqlQry		    	=	"SELECT customer_id,subsciption_id FROM stripe_payment where id=".$userId." order by id desc";
					
				$res		 =	$GLOBALS['db']->getAll($sqlQry,DB_FETCHMODE_ASSOC);
				$cu = \Stripe\Customer::retrieve($res[0]['customer_id']);
				$resq = $cu->subscriptions->retrieve($res[0]['subsciption_id'])->cancel();//print_r($resq->status);exit;
				
				 if($resq->status =='canceled')
				 {
					
					$elmts = array();
					$elmts['status']		=  'UNSUBSCRIBED';
					$elmts['unsubscribed_date'] = date('Y-m-d');
					$rslt=$this->_updateRecord("stripe_payment",$elmts,"id IN($userid)");
					
					if($rslt === 1)
					{
						//echo "entered 1";
					$elmts_new = array();
					$elmts_new['status'] =  'UNSUBSCRIBED';
					$rslt_new=$this->_updateRecord("stripe_auto_renewal",$elmts_new,"pp_id IN($userid)");
					
					}
				 }
				$userdetails=$memObj->_getOneUser($userId);

				if($userdetails)

				{$userlangid=$userdetails['user_language'];}

				if($userlangid!='')

				{ 

					$langname=strtolower($langArray[$userlangid]);

				}

				if($langname){$xmlPath="../xml/".$langname."/page.xml";}

				

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
				//---------------------------------------------------						
				$confMsg = "Successfully Unsubscribed";		
		}
	}

	

		 
	 
	$qry	=	"select a.payment_amount,a.status,a.payment_currency,a.plan_id,a.discount_amount,b.plan_name,b.plan_amount from payment_cronjob a 
				LEFT JOIN jiwok_payment_plan  b ON a.plan_id=b.plan_id 
				WHERE a.user_id=".$userId." and IF(a.payment_currency = 'Euro', 2, 1) = b.plan_currency order by a.id desc";
	$resultData	=	mysql_query($qry);
	//stripe
  $qrycron	=	"select a.payment_amount,a.status,a.payment_currency,a.plan_id,a.discount_amount,b.plan_name,b.plan_amount from stripe_auto_renewal a 
				LEFT JOIN jiwok_payment_plan  b ON a.plan_id=b.plan_id 
				WHERE a.user_id=".$userId." and IF(a.payment_currency = 'Euro', 2, 1) = b.plan_currency order by a.id desc";	
	$resultDatastripe	=	mysql_query($qrycron);
		//IAP app 
		
	$qryIap 	=	"SELECT p .`payment_date`,p.`payment_amount`,p.`payment_expdate`,`payment_currency` , i.`payment_duration`
					FROM `payment` AS p
					JOIN `payment_iap` AS i ON i.`payment_id` = p.`payment_id`
					JOIN `member_iap` AS m ON m.`user_id` = i.`payment_userid`
					WHERE p.`payment_status` =1
					AND p.`version` = 'IAP'
					AND i.`payment_duration` !=0
					AND m.`receipt` <> ''
					AND p.`payment_expdate` >= CURDATE( ) 
					AND i.`payment_userid` =".$userId;
	$resultDataIap	=	mysql_query($qryIap);	
	$qry	=	"select * from payment_paybox where user_id=".$userId." order by id desc";
	$resultUnSub	=	mysql_query($qry);
	//stripe
	$qrystripe	=	"select * from stripe_payment where user_id=".$userId." order by id desc";
	$resultUnSubstripe	=	mysql_query($qrystripe);
	//stripe ends
	
	
	
	if(count($newPaymentReport) > 0) 
	{
		$payments	=	$memObj->getNewPaymentReport($userId,'PAID');
		$refunds	=	$memObj->getNewPaymentReport($userId,'REFUND');
		$cancels	=	$memObj->getNewPaymentReport($userId,'CANCELLED');
	}
	if(count($newPaymentReportstripe) > 0)
	{
		$paymentstripe	=	$memObj->getNewPaymentReportstripe($userId,'PAID');
		$refundstripe	    =	$memObj->getNewPaymentReportstripe($userId,'REFUND');
		$cancelstripe	=	$memObj->getNewPaymentReportstripe($userId,'CANCELLED');
	}
	$qryOld		=	"select * from payment where payment_userid=".$userId." and payment_status='1' AND version='Old' order by payment_id desc";
	$payboxOld  = mysql_query($qryOld);	
	if(mysql_num_rows($payboxOld)	>0	)
		$oldPaid	=	true;	
		
	//Find the expiry date of user to show the unsubscription link from old paybox
	$PaymentExpDateqry 	= 	"SELECT payment_expdate FROM payment WHERE payment_userid=".addslashes($userId)." AND payment_status='1' && payment_date!='0000-00-00' AND version	=	'Old' ORDER BY payment_date DESC";
	$PaymentExpDateresult = $GLOBALS['db']->getRow($PaymentExpDateqry, DB_FETCHMODE_ASSOC);
							
	$PaymentExpDate		=	$PaymentExpDateresult[payment_expdate];
	$today				=	date('Y-m-d');
	$qrydayCount 		=	"SELECT DATEDIFF('$PaymentExpDate', '$today')";
	$resultdayCount		= 	$GLOBALS['db']->getRow($qrydayCount, DB_FETCHMODE_ARRAY);
	$dayCount 			= 	$resultdayCount[0];	
	//Find single program subscribed or not
	$sngleProgramSbscbd	=	$objPgm->checkSingleProgramSubscribed($userId);
	if($sngleProgramSbscbd)
	{
		$singleProgramArrays	=	$objPgm->_getAllSingleTrainingPrograms($userId,0,1000);
	}
	
	
	
	//For program details
	$program = $objPgm->_getUserTrainingProgram($userId);	
	if(count($program)>0) 	
	{
		$programSbscbd	=	true;	
		$program_id		= 	stripslashes(trim($program['program_id']));
		$data			=	$objPgm->_displayTrainingProgram($program_id,'2');
		$levelQuery		=	"SELECT item_name FROM general WHERE table_name	=	'level'	AND		language_id	=	'2'	AND	flex_id	=	'".$data[program_level_flex_id]."'";
		$levelResult 	= 	$GLOBALS['db']->getRow($levelQuery, DB_FETCHMODE_ASSOC);
		//Find user brand
		$sql	= "SELECT *
						FROM `brand_master`
						INNER JOIN brand_user ON brand_master.`brand_master_id` = brand_user.`brand_master_id`
						WHERE user_id ='".$userId."'";			
		$arrRow		=	$GLOBALS['db']->getRow($sql,DB_FETCHMODE_ASSOC);
		if(is_array($arrRow))
		{
			$linkUrl	="http://".$arrRow['brand_name'].".jiwok.com/";
			$pro_url 	= 	$objPgm->makeProgramTitleUrl($data['program_title']);
		}
		else
		{
			$linkUrl	="http://www.jiwok.com/";	
			$pro_url 		= 	strtolower($objPgm->makeProgramTitleUrl($data['program_title']));	
		}		
		$normal_url		= 	$linkUrl.$objPgm->normal_url($pro_url).'-'.trim($program['program_id']);				
	}
?><HTML><HEAD><TITLE><?=$admin_title?></TITLE>

<? include_once('metadata.php');?>

<script language="javascript" src="js/mask.js"></script>

<link href="./js/jscalendar/calendar-blue.css" rel="stylesheet" type="text/css" media="all">

<script language="javascript" src="./js/jscalendar/calendar.js"></script>

<script language="javascript" src="./js/jscalendar/calendar-en.js"></script>

<script language="javascript" src="./js/jscalendar/calendar-setup.js"></script>

</HEAD>

<BODY  class="bodyStyle">

<TABLE cellSpacing=0 cellPadding=0 width="779" align="center" border="1px" bordercolor="#E6E6E6">

  <TR>

    <TD valign="top" align=left bgColor=#ffffff><? include("header.php");?></TD>

  </TR>

  <TR height="5">

    <TD valign="top" align=left class="topBarColor">&nbsp;</TD>

  </TR>

  

  <TR>

    <TD align="left" valign="top"> 

      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 class="middleTableBg">

        <TR> 

          <TD  valign="top" align=left width="175" rowSpan="2" > 

            <TABLE cellSpacing="0" cellPadding="0" width="175" border=0>

              <TR> 

                <TD valign="top">

				 <TABLE cellSpacing=0 cellPadding=2 width=175  border=0>

                    <TBODY> 

                    <TR valign="top"> 

                      <TD valign="top"><? include ('leftmenu.php');?></TD>

                    </TR>

                    

                    </TBODY> 

                  </TABLE>

				</TD>

              </TR>

            </TABLE>

          </TD>

          <TD valign="top" align=left width=0></TD>

         

        </TR>

        <TR> 

          <TD valign="top" width="1067"><!---Contents Start Here----->

		  

		  

            <TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>

              <TR> 

                <TD  width="98%" valign="top">

				

				  <table width="75%" border="0" align="center" cellpadding="0" cellspacing="0">

<tr> 

                <td width="10" height="9"><img src="images/top_left.jpg" width="9" height="9"></td>

                <td width="543" height="9" background="images/top_mdl.jpg"><img src="images/top_mdl.jpg" width="9" height="9"></td>

                <td width="11" height="9"><img src="images/top_right.jpg" width="9" height="9"></td>

              </tr>

              <tr> 

                <td background="images/side1.jpg"><img src="images/side1.jpg" width="9" height="9"></td>

                <td valign="top"> 

				

				

				

				<TABLE cellSpacing=0 cellPadding=0 border=0 align="center">

                    <TR> 

                      <TD valign="top" width=564 bgColor=white> 

						  <TABLE cellSpacing=0 cellPadding=4 width=561 border=0>

                          <TBODY> 

                          <TR> 

                            <TD>

								   <table class="paragraph2" cellspacing=0 cellpadding=0 width=553 border=0>

								  <tr>

										<td height="50" align="center" valign="bottom" class="sectionHeading"><?=$heading;?></td>

									</tr>

									<?php 

										if($errorMsg){ ?>

									<tr>

										<td align="center"><? print $objGen->_adminmessage_box($errorMsg,$mode='error',$url='normal') ?></td>

									</tr>

									<?php } 
									
									if($confMsg != ""){
									?>
									
									<tr>

										<td align="center" class="successAlert"><?=$confMsg?></td>

									</tr>
                                    <?php }?>
				

									<TR> 

									<TD align="left">

										

										<table width="98" height="50" class="topActions">

										<?php 

											//$return	= "list_subscriptions.php";

											$return	= "list_members.php";

										?>

										  <tr><td valign="middle" width="103"><a href="<?=$return;?>?pageNo=<?=$_REQUEST['pageNo']?>&maxrows=<?=$_REQUEST['maxrows']?>&keyword=<?=$_REQUEST['keyword']?>"><img src="images/list.gif" border="0" alt="Listing Record">&nbsp;Back to List</a></td>

										

										</tr>                                        
                                        </table>

									</TD>

									</TR>

									

								  </table>

                              

				  <TABLE class="paragraph2" cellSpacing=0 cellPadding=2 width="80%" align="center">

				   <TBODY> 				   

				    	<TR>

					    <td width="40%" align="left" style="padding-top:15px;"><strong>User Name </strong>&nbsp;</td>

					    <td style="padding-top:15px;">: <?=$objGen->_output($result['user_fname'])." ".$objGen->_output($result['user_lname'])?></td>

					</tr>
                    <TR>

					    <td width="40%" align="left"><strong>Password </strong>&nbsp;</td>                
					    <td>: <?php if((bool)preg_match('/^[a-f0-9]{32}$/', base64_decode($result['user_password'])))echo "Sorry Md5 password";else echo $objGen->_output(base64_decode($result['user_password']));?></td>

					</tr>

					<tr>

						<td width="40%" align="left"> <strong>E-mail </strong>&nbsp;</td>

						<td>: <?=$objGen->_output($result['user_alt_email'])?>						</td>

					</tr>
                    <?php
					if($objGen->_output($result['email_history'])	!=	"")
					{
						$result['email_history']	=	str_replace(",",", ",$result['email_history']);?>
                    <tr>

						<td width="40%" align="left" valign="top"> <strong>Used E-mails </strong>&nbsp;</td>

						<td>: <?=$objGen->_output($result['email_history'])?>						</td>

					</tr>
					<?php }?>
                    <tr>

                      <td align="left"><strong>Date of Joining</strong></td>

                      <td>: <?php echo strftime("%B %d, %Y", strtotime(substr($result['user_doj'], 0, 10))); ?></td>

                    </tr>

					<?php 
					$sts	=	false;					
					if((mysql_num_rows($resultUnSub)>0) || (mysql_num_rows($resultUnSubstripe)>0)){
						
						  if(mysql_num_rows($resultUnSub)>0)
						    {
							   $unSubData	=	mysql_fetch_array($resultUnSub);
							}
						  else if(mysql_num_rows($resultUnSubstripe)>0)
						   {
							   $unSubData	=	mysql_fetch_array($resultUnSubstripe);
						   }
						//print_r($unSubData);
						if(($unSubData['status']=='UNSUBSCRIBED'	||	$unSubData['status']=='EXPIRED')&&($unSubData['unsubscribed_date']!='0000-00-00')){
							$sts	=	true;
						}
					}
					if($sts){
					?> 
					   <tr>

						<td width="40%" align="left"><strong>Unsubscribed/Expired date</strong>&nbsp;</td>

						<td>: <?php echo $unSubData['unsubscribed_date']; ?></td>

					</tr>


                    <?php 
					}
					else{
					
					if($result['user_unsubscribed'] > 0){?>

                    <tr>

						<td width="40%" align="left"><strong> Request for Unsubscription</strong>&nbsp;</td>

						<td>: <?=$objGen->_modifier_date_format($result['user_req_unsubscribe'])?></td>

					</tr>

					<?php }
					

					if($result['user_unsubscribed'] == 2){?>

					 <tr>

					   <td align="left"><strong>Date Unsubscribed </strong>&nbsp;</td>

					   <td>: <?=strftime("%B %d, %Y", strtotime($result['unsubscribe_date']))?></td>

					   </tr>

                     <? }
					}
					 if(sizeof($pay_result)>0){?>

					 <tr>

					   <td align="left"><strong>Date of last Payment</strong></td>

					   <td>: <?php echo strftime("%B %d, %Y", strtotime($pay_result['payment_date'])); ?></td>

					   </tr>

					 <tr>

					   <td align="left"><strong>Date of  Payment Expiration</strong></td>

					   <td>: <?php echo strftime("%B %d, %Y", strtotime($pay_result['payment_expdate'])); ?></td>

					   </tr>

                     <?php } ?>
                     <?php if((mysql_num_rows($resultData)>0) || (mysql_num_rows($resultDatastripe)>0) || (mysql_num_rows($resultDataIap)>0) )
                     {
						  if (mysql_num_rows($resultDataIap)>0)
						 {
							 $nxtPaymentData	=	mysql_fetch_assoc($resultDataIap);	
						 }
						 else if (mysql_num_rows($resultDatastripe)>0)//need to edit
						 {
							 $nxtPaymentData	=	mysql_fetch_array($resultDatastripe);	//  echo "222";echo "<pre>";print_r($nxtPaymentData);exit;
							 //~ print_r($nxtPaymentData);exit;
							 $nxtPaymentData['via']	=	"stripe";
						 }
						 else if(mysql_num_rows($resultData)>0)
						 {
							 $nxtPaymentData	=	mysql_fetch_array($resultData);	 //echo "11";echo "<pre>";print_r($nxtPaymentData);exit;
							  $nxtPaymentData['via']	=	"paybox";
						 }
						
						 			
					?>
						 <tr>

					   <td align="left"><strong>Next Payment plan</strong></td>

					   <td>: <?php 
					   				if (mysql_num_rows($resultDataIap)>0)
									{
										echo  "Pass ".$nxtPaymentData["payment_duration"]." mois via Iphone";
									}
					   				else if($nxtPaymentData["status"]=='VALID')
									{
										if(($nxtPaymentData['discount_amount']	!=	0)	&&	($nxtPaymentData['plan_id']	==	1))
										{
											echo $nxtPaymentData["plan_name"]."(With discount code)"." via ".$nxtPaymentData['via'];
										}
										else
											echo $nxtPaymentData["plan_name"]." via ".$nxtPaymentData['via'];
									}
									else
									{
										echo $nxtPaymentData["status"] ." via ".$nxtPaymentData['via'];
									}
									 ?></td>

					   </tr>

					 <tr>

					   <td align="left"><strong>Next Payment Amount</strong></td>

					   <td>: <?php 
					   				if (mysql_num_rows($resultDataIap)>0)
									{
										echo  $nxtPaymentData["payment_amount"]." ".$nxtPaymentData["payment_currency"];
									}
					   				if($nxtPaymentData["status"]=='VALID')
									{
										if(($nxtPaymentData['discount_amount']	!=	0)	&&	($nxtPaymentData['plan_id']	==	1))
										{
											$pieces = explode("##", $nxtPaymentData['discount_amount']);																
											$nxtPaymentData["payment_amount"]	=	trim($pieces[0]);											
										}
										else
											$nxtPaymentData["payment_amount"]	=	$nxtPaymentData["plan_amount"];										
										echo $nxtPaymentData["payment_amount"]." ".$nxtPaymentData["payment_currency"];
									}
									else
									{
										echo $nxtPaymentData["status"];
									}
					  ?></td>

					   </tr>

						 
					<?php } ?>

					 <tr>

					   <td align="left">&nbsp;</td>

					   <td>&nbsp;</td>

					   </tr>

					 <tr>

					   <td align="left" colspan="1"></td>

                       <td align="right">
                       <?php 
					   if(mysql_num_rows($resultDataIap)== 0) {
					   ?><form name="subscription" action="" method="post">
                       <?php
					    if(count($payboxStatus)	>	0 )
					   { 
					   ?>
					   <input type="submit" name="newPayboxUnsubscribe" value="Unsubscribe User From new paybox" onClick="return confirm('Are you sure that you want to unsubscribe this member from new paybox? If yes click Ok, if not click Cancel.')">
					   <?php
					   }
					    else if(count($payboxStatusstripe) > 0 )
					   {
					   ?>
					   <input type="submit" name="stripeUnsubscribe" value="Unsubscribe User From stripe" onClick="return confirm('Are you sure that you want to unsubscribe this member from new paybox? If yes click Ok, if not click Cancel.')">
					   <?php
					   }
					 if((($result['user_unsubscribed'] == 2 || !$oldPaid)	&&	(count($payboxStatus)	==	0)) && (($result['user_unsubscribed'] == 2 || !$oldPaid)	&&	(count($payboxStatusstripe)	==	0))){ 
					  
					   ?><input type="submit" name="subscribe" value="Subscribe User" onClick="return confirm('Are you sure that you want to subscribe this member? If yes click Ok, if not click Cancel.')"><?php } 
					   elseif($dayCount	>	0){ ?><input type="submit" name="unsubscribe" value="Unsubscribe User" onClick="return confirm('Are you sure that you want to unsubscribe this member? If yes click Ok, if not click Cancel.')"><?php } ?>

                    <input type="hidden" name="userId"     value="<? echo $userId; ?>">

                    <input type="hidden" name="langId"     value="<?=$_REQUEST['langId']?>">   

                    <input type="hidden" name="pageNo"	   value="<?=$_REQUEST['pageNo']?>">

                    <input type="hidden" name="maxrows"    value="<?=$_REQUEST['maxrows']?>">

                    <input type="hidden" name="type"       value="<?=$_REQUEST['type']?>">

                    <input type="hidden" name="field" 	   value="<?=$_REQUEST['field']?>">

                    <input type="hidden" name="keyword"    value="<?=$_REQUEST['keyword']?>">

                    </form>
                    <?php
					   }
                    ?></td>

					   </tr>

                     <tr>

					   <td align="left" colspan="2"><fieldset>

					   <legend>Set Expiry Date</legend>

					   <form name="update_payment_frm" action="" method="post"><table width="90%" align="center" cellpadding="3" >

                       <tr>

                       	<td align="left"><strong>Last Payment</strong></td>

                        <td><?php echo $pay_result['payment_amount']; ?></td>

                       </tr>

                       <tr>

                         <td align="left"><strong>Expiry Date</strong></td>

                         <td><input type="text" name="exp_date" id="update_exp_date" value="<?php echo $pay_result['payment_expdate']; ?>"> <input type="button" id="update_exp_date_btn" value="Select"></td>

                       </tr>

                       <tr><td colspan="2">&nbsp;</td></tr>

                       <tr>

                        <td align="center" colspan="2">

                        <input type="submit" name="update_payment" value="Save">

                        </td>

                       </tr></table>

                    <input type="hidden" name="paymentId"  value="<? echo $pay_result['payment_id']; ?>">

                    <input type="hidden" name="userId"     value="<? echo $userId; ?>">

                    <input type="hidden" name="langId"     value="<?=$_REQUEST['langId']?>">   

                    <input type="hidden" name="pageNo"	   value="<?=$_REQUEST['pageNo']?>">

                    <input type="hidden" name="maxrows"    value="<?=$_REQUEST['maxrows']?>">

                    <input type="hidden" name="type"       value="<?=$_REQUEST['type']?>">

                    <input type="hidden" name="field" 	   value="<?=$_REQUEST['field']?>">

                    <input type="hidden" name="keyword"    value="<?=$_REQUEST['keyword']?>">

                    </form>

<script type="text/javascript">

  Calendar.setup(

    {

      inputField  : "update_exp_date",         // ID of the input field

      ifFormat    : "%Y-%m-%d",    // the date format

      button      : "update_exp_date_btn"       // ID of the button

    }

  );

</script>

                       </fieldset></td>

					   </tr>

					 <tr>

					   <td align="center" colspan="2">&nbsp;</td>

					   </tr>

					 <?php if((count($newPaymentReport)	> 0) || (count($newPaymentReportstripe)	> 0) ||(count($paymentReportIap) > 0))
					 {
						 $key	= 0;	?>
                         <tr>
    
                           <td align="left" colspan="2">
                           <fieldset>
								<legend>New payment report</legend>
                                <table width="90%" align="center" cellpadding="3" >

                       <tr>

                       	<td align="left"><strong>Payment</strong>
                        	<table width="100%" align="center" cellpadding="3">
                            <tr><td width="16%" bgcolor="#CCCCCC">&nbsp;#</td>
                              <td width="45%" bgcolor="#CCCCCC">&nbsp;Date</td>
                              <td width="39%" bgcolor="#CCCCCC">&nbsp;Amount</td>
                              <td width="39%" bgcolor="#CCCCCC">&nbsp;Payment</td>
                            </tr>
                            <?php 
                             $nums = 0;
                            if(count($payments)	>	0) 
							{  $nums = 1;
								foreach($payments as $key=>$payment)
								{ 
									$details	=	$paymentClass->unserializeArray(base64_decode($payment[details]));	
									$method   = 'paybox';																	
									if($details[DEVISE]== '978')
										$currency		=	"Euro";										
									else if($details[DEVISE]== '985')
										$currency		=	"Zloty";
									else
										$currency		=	"Dollar";
									$amount				=	$details[MONTANT]/100;		
									$amountDescription	=	$amount." ".$currency; 	
									$key	=	$key+1;
									
									?>
                                	<tr>
                                    	<td width="16%">&nbsp;<?=$key?></td>
                              			<td width="45%">&nbsp;<?=$payment['date']?></td>
                              			<td width="39%">&nbsp;<?=$amountDescription?></td>
                              			<td width="39%">&nbsp;<?=$method?></td>
                            		</tr>
                                <?php }
							}
							 if(count($paymentstripe)	>	0) 
							{  $nums = 1; 
								$skey	=$key;
								foreach($paymentstripe as $key=>$payment)
								{
								     			
	                                 $Paymentcur 	= 	"SELECT payment_amount,payment_currency,version FROM payment WHERE payment_id=".$payment['payment_id']." order by payment_id desc";
	                                 $Paymentcurresult = $GLOBALS['db']->getRow($Paymentcur, DB_FETCHMODE_ASSOC);
									 $amountDescription	=	$Paymentcurresult['payment_amount']." ".$Paymentcurresult['payment_currency'];
									 $method   = $Paymentcurresult['version'];
									 $key	=	$skey+1; 	
									?>
								<tr>
                                    	<td width="16%">&nbsp;<?=$key?></td>
                              			<td width="45%">&nbsp;<?=$payment['join_date']?></td>
                              			<td width="39%">&nbsp;<?=$amountDescription?></td>
                              			<td width="39%">&nbsp;<?=$method?></td>
                            		</tr>
								
							<?php }
							}
							 if(count($paymentReportIap) > 0)
							{  $nums = 1;
								$ikey	=$key;
								foreach($paymentReportIap as $key=>$payment)
								{
								     $method   = 'IAP';			
	                                 
									 $key	=	$ikey+1;
									?>
								<tr>
                                    	<td width="16%">&nbsp;<?=$key?></td>
                              			<td width="45%">&nbsp;<?=$payment['payment_date']?></td>
                              			<td width="39%">&nbsp;<?=$payment['payment_amount']." " .$payment['payment_currency']?></td>
                              			<td width="39%">&nbsp;<?=$method?></td>
                            		</tr>
								
							<?php }
								//echo "<pre/>";print_r($paymentReportIap);exit;
							}
							if( $nums == 0)
							{?>
								<tr>
                                	<td colspan="3" align="center">&nbsp;No payments</td>
                              		</tr>
							<?php }?>       	        
                            </table>
                        </td>

                        </tr>

                       <tr>

                         <td align="left"><strong>Refund</strong>
                         	<table width="100%" align="center" cellpadding="3">
                            <tr><td width="16%" bgcolor="#CCCCCC">&nbsp;#</td>
                              <td width="45%" bgcolor="#CCCCCC">&nbsp;Date</td>
                              <td width="39%" bgcolor="#CCCCCC">&nbsp;Amount</td>
                              <td width="39%" bgcolor="#CCCCCC">&nbsp;Payment</td>
                            </tr>
                            <?php if(count($refunds)	>	0)
							{
								foreach($refunds as $key=>$refund)
								{
									$details	=	$paymentClass->unserializeArray(base64_decode($refund[details]));	
									$method		= 'paybox';						
									if($details[DEVISE]== '978')
										$currency		=	"Euro";
									else if($details[DEVISE]== '985')
										$currency		=	"Zloty";	
									else
										$currency		=	"Dollar";										
									$amount				=	$details[MONTANT]/100;		
									$amountDescription	=	$amount." ".$currency;
									?>
                                	<tr>
                                    	<td width="16%">&nbsp;<?=$key+1?></td>
                              			<td width="45%">&nbsp;<?=$refund['date']?></td>
                              			<td width="39%">&nbsp;<?=$amountDescription?></td>
                              			<td width="39%">&nbsp;<?=$method?></td>
                            		</tr>
                                <?php }
							}
							else if(count($refundstripe)	>	0)
							{
								foreach($refundstripe as $key=>$refund)
								{
									
									$Paymentcur 	= 	"SELECT payment_amount,payment_currency,version FROM payment WHERE payment_id=".$refund['payment_id'];
	                                 $Paymentcurresult = $GLOBALS['db']->getRow($Paymentcur, DB_FETCHMODE_ASSOC);
									 $amountDescription	=	$Paymentcurresult['payment_amount']." ".$Paymentcurresult['payment_currency'];
									 $method		= $Paymentcurresult['version'];	
									?>
                                	<tr>
                                    	<td width="16%">&nbsp;<?=$key+1?></td>
                              			<td width="45%">&nbsp;<?=$refund['join_date']?></td>
                              			<td width="39%">&nbsp;<?=$amountDescription?></td>
                              			<td width="39%">&nbsp;<?=$method?></td>
                            		</tr>
                                <?php }
							}
							else
							{?>
								<tr>
                                	<td colspan="3" align="center">&nbsp;No refunds</td>
                              		</tr>
							<?php }?>  
                            </table>
                         </td>

                         </tr>

                       <tr><td><strong>Cancel</strong>
                       	<table width="100%" align="center" cellpadding="3">
                            <tr><td width="16%" bgcolor="#CCCCCC">&nbsp;#</td>
                              <td width="45%" bgcolor="#CCCCCC">&nbsp;Date</td>
                              <td width="39%" bgcolor="#CCCCCC">&nbsp;Amount</td>
                               <td width="39%" bgcolor="#CCCCCC">&nbsp;Payment</td>
                            </tr>
                            <?php if(count($cancels)	>	0)
							{
								foreach($cancels as $key=>$cancel)
								{   
									$method		= 'paybox';		
									$details	=	$paymentClass->unserializeArray(base64_decode($cancel[details]));									
									if($details[DEVISE]== '978')
										$currency		=	"Euro";
									else if($details[DEVISE]== '985')
										$currency		=	"Zloty";	
									else
										$currency		=	"Dollar";
									$amount				=	$details[MONTANT]/100;		
									$amountDescription	=	$amount." ".$currency;									
									?>
                                	<tr>
                                    	<td width="16%">&nbsp;<?=$key+1?></td>
                              			<td width="45%">&nbsp;<?=$cancel['date']?></td>
                              			<td width="39%">&nbsp;<?=$amountDescription?></td>
                              			<td width="39%">&nbsp;<?=$method?></td>
                            		</tr>
                                <?php }
							}
							else if(count($cancelstripe)	>	0)
							{
								foreach($cancelstripe as $key=>$cancel)
								{
										
									$Paymentcur 	= 	"SELECT payment_amount,payment_currency,version FROM payment WHERE payment_id=".$cancel['payment_id'];
	                                 $Paymentcurresult = $GLOBALS['db']->getRow($Paymentcur, DB_FETCHMODE_ASSOC);
									 $amountDescription	=	$Paymentcurresult['payment_amount']." ".$Paymentcurresult['payment_currency'];		
									 $method		= $Paymentcurresult['version'];							
									?>
                                	<tr>
                                    	<td width="16%">&nbsp;<?=$key+1?></td>
                              			<td width="45%">&nbsp;<?=$cancel['join_date']?></td>
                              			<td width="39%">&nbsp;<?=$amountDescription?></td>
                              			<td width="39%">&nbsp;<?=$method?></td>
                            		</tr>
                                <?php }
							}
							else
							{?>
								<tr>
                                	<td colspan="3" align="center">&nbsp;No Cancels</td>
                              		</tr>
							<?php }?>  
                            </table>
                       </td></tr>

                       <tr>

                        <td align="center">

                        

                        </td>

                       </tr></table>
    						</fieldset>
                            </td>
                           </tr>
                     <?php } ?>
                     
                  <tr>
    
                           <td align="left" colspan="2">
                           <fieldset>
								<legend>Subscribed program details </legend>
                                <table width="90%" align="center" cellpadding="3" >

                       <tr>

                       	<td width="25%" align="left"><strong>Title</strong></td>

                        <td width="75%"><?php if($programSbscbd) echo trim($data['program_title']); else echo 'No program subscribed';?></td>

                       </tr>

                       <tr>

                         <td align="left"><strong>Level</strong></td>

                         <td><?php if($programSbscbd) echo trim($levelResult[item_name]); else echo 'No program subscribed';?></td>

                       </tr>

                       <tr><td><strong>Link</strong></td>
                         <td><?php if($programSbscbd) echo "<a href='".trim($normal_url)."' target='_blank'> Goto program</a>"; else echo 'No program subscribed';?></td>
                       </tr>

                       <tr>

                        <td align="center" colspan="2">&nbsp;</td>

                       </tr></table>
    						</fieldset>
                            </td>
                           </tr>
						   <tr>
    
                           <td align="left" colspan="2">
                           <fieldset>
								<legend>Subscribed single program details </legend>
								<?php
								if($sngleProgramSbscbd)
								{?>
									<table width="90%" align="center" cellpadding="3" >
									<?php
									foreach($singleProgramArrays	as	$key	=>	$singleProgramArray)
									{?>
										<tr>			
											<td width="8%" align="left"><?=$key+1?></td>
											<td width="17%" align="left"><strong>Title</strong></td>			
											<td width="75%"><?=$singleProgramArray[program_title];?></td>
										</tr>
										<tr>
											<td align="center" colspan="3">&nbsp;</td>
										</tr>
									<?php
									}?>
									</table>
					   		<?php }
								else
								{?>
									<table width="90%" align="center" cellpadding="3" >
										<tr>
											<td align="center"><strong>No single program subscribed</strong></td>
										</tr>
										<tr>
											<td align="center">&nbsp;</td>
										</tr>
									</table>
								<?php
								}?>
    						</fieldset>
                            </td>
                           </tr>

				    </tbody>

			 	  </table>

				</TD>

                          </TR>

                          </TBODY>

                        </TABLE>

                      </TD>

                     </TR>

                  </TABLE>

				  

				  

				  </td>

                <td background="images/side2.jpg">&nbsp;</td>

              </tr>

              <tr> 

                <td width="10" height="9"><img src="images/btm_left.jpg" width="9" height="9"></td>

                <td height="9" background="images/btm_mdl.jpg"><img src="images/btm_mdl.jpg" width="9" height="9"></td>

                <td width="11" height="9"><img src="images/btm_right.jpg" width="9" height="9"></td>

              </tr>

            </table>



                </TD>

              </TR>

            </TABLE>



          </TD>

        </TR>

		 <TR height="2">

    <TD valign="top" align=left class="topBarColor" colspan="3">&nbsp;</TD>

  </TR>

      </TABLE>

        <?php include_once("footer.php");?>

</td></tr></table>		

</body>

</html>

