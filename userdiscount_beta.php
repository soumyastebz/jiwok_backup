<?php
if(trim($_POST['user_discount'])!= '' )
{	
	$reffId						=	trim($_POST['user_discount']);	
 	$discCnt	=	$objDisc->_isExistsDisc($reffId);		
	/*if(isset($_SESSION['login']['userId'])){
	$userId		=	$_SESSION['login']['userId']; //if user is coming from the registration page.}*/
	if(isset($_SESSION['user']['userId']))
	{
		$userId		=	$_SESSION['user']['userId']; //if user is coming from the registration page.
	}
	$selectSettings		=	"select * from settings";
	$result				=	$GLOBALS['db']->getAll($selectSettings,DB_FETCHMODE_ASSOC);	
	foreach($result as $key=>$data)
	{
	/*	if($lanId == 1) 
		{
			$memShipFee			=   $objGen->_output($data['membership_feedollar']);
		}
		else
		{*/
	$memShipFee			=   $objGen->_output($data['membership_fee']);
	//	}
	}
	//For Polish language
	if($lanId == 5) 
		$memShipFee			=	35;
	// gift chxeking
	if(strstr(trim($reffId),GC))
	{
		$reffId=trim($reffId);
		$giftcheck	=$objgift->_isExistsGift($reffId,$userId);
        if($giftcheck > 0)
		{    
			$giftdata=$objgift->_getdetailsgift($reffId);
			$period= $giftdata[0]['codetype'];						
			$nextId	= $_SESSION['user']['userId'];
            $no_usage = $giftdata[0]['no_usage'];		
			if(strlen($period) < 3)
				$period	=	$period.' months';			 
			/**************Assign the dtat to the corresponding array*****************/

			//$elmts['user_id'] 			= $nextId;
			/*$elmts['user_language'] 			= $reg['user_language'];
			$elmts['user_country'] 				= $reg['user_country'];
            $elmts['user_timezone'] 			= $reg['user_timezone'];
			$elmts['user_voice'] 				= $reg['user_voice'];
			$elmts['user_dob'] 					= $reg['user_dob'];
			$elmts['user_type']				    = 1;
			$elmts['user_doj']					= date('Y-m-d');
			$elmts['user_email'] 		    	= addslashes($_SESSION['login']['user_email']);
			$elmts['user_alt_email']   		    = $elmts['user_email'];
			$elmts['user_password'] 		    = addslashes(base64_encode($_SESSION['login']['password']));
			$elmts['user_weight_value']	        = $reg['user_weight_value'];
			$elmts['user_weight_unit']		    = $reg['user_weight_unit'];
			$elmts['user_height_value']	        = $reg['user_height_value'];
			$elmts['user_height_unit']		    = $reg['user_height_unit'];
			$elmts['user_free_period']		    = $totalFreePeriods;
			$elmts['user_username']				= addslashes($_SESSION['login']['user_email']);
			//$elmts['user_reff_id']				= "REFF".uniqid(); // client kept it as pending
			$elmts['user_discount_status']		= 1;	
			//$elmts['user_refferal_status']		= 0; // 1 for refferal code activation
			$elmts['user_status']				= 1;
			$elmts['user_newsletter']		    = trim($_POST['user_newsletter']);
			statusRecord($_SESSION['login']['user_email'],'just before user insertion-gift','userreg2.php','0',$payFee,'2');
			//////////////////
			$resqry1 = $GLOBALS['db']->query("SELECT MAX(user_id) as maximum FROM user_master");
			while ($resqry1->fetchInto($row)) 
			{
				$elmts_ins['user_id'] = $row[0]+1;
			}
			//////////////////
			$chkins=$dbObj->_insertRecord("user_master",$elmts_ins);
			//////////////////
			$resqry1 = $GLOBALS['db']->query("SELECT MAX(user_id) as maximum FROM user_master where user_email='".$elmts['user_email']."'");
			while ($resqry1->fetchInto($row)) 
			{
				$userId=$_SESSION['login']['userId']=$nextId = $row[0];
			}
			//////////////////
			statusRecord($_SESSION['login']['user_email'],'inserted user-gift','userreg2.php',$nextId,$payFee,'3');
			unset($_SESSION['elmts1']);	
			$chk = $dbObj->_updateRecord("user_master",$elmts,"user_id = {$nextId}");*/
			//$elmts1		= array();
			//$elmts1['usermaster_id']	= $nextId;
			//$update=array();
			//echo $reffId;
			$query="update gift_code set codestatus='used' where code='$reffId'";
			$result = $GLOBALS['db']->query($query);
            $query="update gift_code set increase_no_usage=increase_no_usage+1 where code='$reffId' and no_usage > 0";
			$result = $GLOBALS['db']->query($query);
			//$chk=$dbObj->_updateRecord("gift_code",$update,"code = {$reffId}");
			$payFee=$giftdata[0]['codeamount'];
			$period_accurate=str_replace(' months','',$period);
            $expdate=date('Y-m-d',strtotime(date('Y-m-d').' + '.$period));
			switch($period)
			{
				case '3 months':{$expdate=date('Y-m-d',strtotime(date('Y-m-d').' + 3 months'));}break;
				case '4 months':{$expdate=date('Y-m-d',strtotime(date('Y-m-d').' + 4 months'));}break;
				case '6 months':{$expdate=date('Y-m-d',strtotime(date('Y-m-d').' + 6 months'));}break;
				case '12 months':{$expdate=date('Y-m-d',strtotime(date('Y-m-d').' + 12 months'));}break;
			}
			//////////////payment
			$payment_last_id=NULL;
			if($payFee != '')
			{
				//Finding the subscription status of the user
				$PaymentExpDateqry 	= 	"SELECT payment_expdate,version,payment_id FROM payment WHERE payment_userid=".addslashes($userId)." AND payment_status='1' && payment_date!='0000-00-00' ORDER BY payment_id DESC";
				$PaymentExpDateresult = $GLOBALS['db']->getRow($PaymentExpDateqry, DB_FETCHMODE_ASSOC);
				$PaymentExpDate		=	$PaymentExpDateresult[payment_expdate];
				$today				=	date('Y-m-d');
		  		$qrydayCount 		=	"SELECT DATEDIFF('$PaymentExpDate', '$today')";
		  		$resultdayCount		= 	$GLOBALS['db']->getRow($qrydayCount, DB_FETCHMODE_ARRAY);
		  		$dayCount 			= 	$resultdayCount[0];				
				if($dayCount	>	0)
				{
					$expdate=date('Y-m-d',strtotime(date($PaymentExpDate).' + '.$period));
					$query="update payment set payment_expdate='".$expdate."' where payment_id='".$PaymentExpDateresult[payment_id]."'";
					$resultUp = $GLOBALS['db']->query($query);
					//check whether the user having any crone payment for auto renewal
					$cronqry 	= 	"SELECT id,payment_expiry_date FROM stripe_auto_renewal WHERE user_id=".addslashes($userId)." AND status='VALID' ORDER BY id DESC";
					$cronResult = $GLOBALS['db']->getRow($cronqry, DB_FETCHMODE_ASSOC);
					if(count($cronResult)	>	0)//Update the cron
					{
						$query="update stripe_auto_renewal set payment_expiry_date='".$expdate."' where id='".$cronResult[id]."'";
						$resultCr = $GLOBALS['db']->query($query);
					}
					$payment_last_id	=	$PaymentExpDateresult[payment_id];
										
				}
				else
				{							
					//insert payment page
					$payElmts['payment_userid']				=	$userId;
					$payElmts['payment_amount']				=	$payFee;
					$payElmts['payment_date']				=	date('Y-m-d');
					$payElmts['payment_status']				=	1;
					$payElmts['payment_expdate']			=	$expdate;
					if($giftdata[0]['purchase_currency']=='')
					{
						$giftdata[0]['purchase_currency']='Euro';
					}
					$payElmts['payment_currency']			=	$giftdata[0]['purchase_currency'];
					$paychck=$objDb->_insertRecord("payment",$payElmts);
					$payment_last_id=mysql_insert_id();
					//statusRecord($_SESSION['login']['user_email'],'payment insertion over-gift','userreg2.php',$nextId,$payFee,'4');
				}
			}
			if($no_usage==0)
			{	 
				$query=mysql_query("update gift_userdetails set user_id=".$userId.",usedate=CURDATE(),payment_id=".$payment_last_id." where code='$reffId'")or die(mysql_error());	 
				//$r = mysql_query($query2)or die(mysql_error());
			}
			else
			{
				$insert="insert into gift_userdetails(id,code,purchaseid,purchasedate,purchase_currency,user_id,usedate,payment_id) values('','$reffId','0',CURDATE(),'Euro','$userId',CURDATE(),'$payment_last_id')";
				$r = mysql_query($insert)or die(mysql_error());
			}
			$_SESSION['user']['userId']=$userId;
						
			///////////////////////
			/*$insertData=array();	
			$insertData['login_date'] = date("Y-m-d H:i:s");
			$insertData['user_id']    = $nextId;
			$insertData['login_ip']   = $REMOTE_ADDR;
			$dbObj->_insertRecord("member_login",$insertData);
		    $insertData=array();
			$fullname = $_SESSION['registration']['user_fname']." ".$_SESSION['registration']['user_lname'];
			$ticket_pass = md5($_SESSION['login']['password']); 
			$Ticket = array();
			$Ticket['client_name']		 	= $fullname;
			$Ticket['email'] 				= $_SESSION['login']['user_email'];
			$Ticket['registered_on'] 		= date("Y-m-d H:i:s");
			$Ticket['default_lang'] 		= 'en';
			$Ticket['preferred_zone'] 		= 0;
			$Ticket['pass_word'] 			= $ticket_pass;
			$Ticket['client_status'] 		= 'a';
			$ticketEntry = $dbObj->_insertRecord("ticket_clients",$Ticket);
	        $Ticket=array();
			////////////////////////
			//print_r($_SESSION);*/
			/*if($chk > 0)
			{
				$userName = $_SESSION['login']['user_email'];
				$userNameNew = eregi_replace("\[".$att_name."\]",$att_value,$userName);
				$password = $_SESSION['login']['password'];
				$emailTo		=  $_SESSION['login']['user_email'];
				$mailArray      =  $objMassmail->_fetchSettingsEmail();
				$return_email 	= $mailArray['RETURN_MAIL'];
				$bounce_email 	= $mailArray['BOUNCE_MAIL'];
				$subject = $parObj->_getLabenames($arrayData,'reg_mail_subject','name');
				$siteUrl = 'http://'.$_SERVER['HTTP_HOST'].'/index.php';
				$msg = "\n".$parObj->_getLabenames($arrayData,'reg_mail_to','name').",\n\n";
				$msg .= $parObj->_getLabenames($arrayData,'reg_mail_1','name')."\n";
				$msg .= $parObj->_getLabenames($arrayData,'reg_mail_2','name')."\n";
				$msg .= $parObj->_getLabenames($arrayData,'reg_mail_3','name').": \n";
				$msg .= $parObj->_getLabenames($arrayData,'reg_mail_username','name').": ".$userNameNew."\n";
				$msg .= $parObj->_getLabenames($arrayData,'reg_mail_password','name').": ".$password."\n\n";
				$msg .= $parObj->_getLabenames($arrayData,'reg_mail_link','name')." ".$siteUrl."\n\n";
				$msg .= $parObj->_getLabenames($arrayData,'reg_mail_bot1','name')."\n";
				$msg .= $parObj->_getLabenames($arrayData,'reg_mail_bot2','name')."coach@jiwok.com ";
				$msg .= $parObj->_getLabenames($arrayData,'reg_mail_bot3','name')."\n";
				$msg = utf8_decode($msg);
				$fromAddress = 'Coach Jiwok <coach@jiwok.com>';
				if($reg['user_language'] == 1)
				{
					$path= './uploads/reg_attachment/english/';
				}
				if($reg['user_language'] == 2)
				{
					$path= './uploads/reg_attachment/french/';
				}
				$files_dir = scandir($path); // scan filed in particular dir
				foreach($files_dir as $fil)
				{
					if(is_file($path.$fil))
					{
						$tmp_file[] = $fil; 
					}
				}
				$file_cnt = count($tmp_file);
				if($file_cnt >0)
				{
					$unid = md5(uniqid(time()));
					$headers = "From: Coach Jiwok <coach@jiwok.com>\r\n";
					$headers .= "Return-Path: $return_email\n";
					$headers .= "Return-Receipt-To: $bounce_email\n";
					$headers .= "Reply-To: coach@jiwok.com \r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: multipart/mixed; boundary=\"".$unid."\"\r\n\r\n";
					$headers .= "This is a multi-part message in MIME format.\r\n";
					$headers .= "--".$unid."\r\n";
					$headers .= "Content-type:text/plain; charset=iso-8859-1\r\n";
					$headers .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
					$headers .= $msg."\r\n\r\n";
					foreach($tmp_file as $filename)
					{
						$file = $path.$filename;
						$file_size = filesize($file);
						$handle = fopen($file, "r");
						$content = fread($handle, $file_size);
						fclose($handle);
						$content = chunk_split(base64_encode($content));
						$headers .= "--".$unid."\r\n";
						$headers .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use diff. tyoes here
						$headers .= "Content-Transfer-Encoding: base64\r\n";
						$headers .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
						$headers .= $content."\r\n\r\n";
					}
					@mail($emailTo,$subject,"",$headers);
				}
				else
				{
					$headers  = 'Mime-version: 1.0' . "\n"; 
					$headers .= 'Content-type: text/plain' . "\n"; 
					$headers .= 'Content-transfer-encoding: quoted-printable' . "\n"; 
					$headers .= "From: ".$fromAddress."\n";
					$headers .= "Return-Path: $return_email\n";
					$headers .= "Return-Receipt-To: $bounce_email\n";	
					@mail($emailTo,$subject,$msg,$headers);
				}
		  	}*/			
                     
			//session_register('user');
			//assigning the user data to a session named user 
			//session data should be the followings
			//1-user id
			//2-user type(trainer or user )
			/*$_SESSION['user']= array(
				"userId"       => $userId,
				"userType"     => $siteUsersConfig['MEMBER'],
				user_email"    => $_SESSION['login']['user_email']);*/
			$_SESSION['giftregcheck']=1;				
			/*unset($_SESSION['admin_user']);
			if(($chkins!="") && ($chk!=""))
			{
            	statusRecord($_SESSION['login']['user_email'],'just before forum redirection','userreg2.php',$nextId,$payFee,'5');
				header("location:register.php");
			}
			else{$errorMsginsert=1; }
			exit;		  //header("location:myprofile.php");
			//exit;	*/ 
		}
		else
		{
			$errorMsg = 1;
			$err16 = 1;
			$discountMsg	=$parObj->_getLabenames($arrayErrorData1,'err8','name'); //Referral id/gift code not valid
		}
	}
	else 
	{ 	
		$_REQUEST[payment_plan]				=	$_REQUEST[selectedPlan];		
		if( $discCnt['cnt'] > 0 )
		{
			$discCode		=	$reffId;
			$Type			=	'DISC';
			//get any active reff id for this user
			$activeDisc	=	$objDisc->_getLastCode($userId,$Type);
			///chk whether the user is already entered a disc/reff id 
			$discApplyed			=	$objDisc->_isAlreadyApply($userId,$discCode,$Type);
			if(count($activeDisc) > 0)
			{		
				$activeDiscCode			=	$activeDisc['discount_code'];
				//chage date format 
				$enteredDiscDate		=	$objGen->_dateTomdY($activeDisc['start_date']);
				$getDiscCodeDetails		=	$objDisc->_getDiscDetails($activeDiscCode);
				$discMonth				=	$getDiscCodeDetails['discount_month'];
				$discExpiryDate			=	$getDiscCodeDetails['end_date'];   //yyyy-mm-dd
				//chage date format 
				$splittedDate			=	$objGen->_dateTomdY($discExpiryDate);				
				//check whether discount period ended after apply discount
				//ADD month to date
				$userDiscExpiryDate		=	$objGen->_addMonthToDate($enteredDiscDate,$discMonth);
				$dateDiff1				=	$objGen->date_difference($today,$userDiscExpiryDate);
				$dateDiff2				=	$objGen->date_difference($today,$splittedDate);	
				/*if($userId	==	60378)
				{
					//unset($_SESSION['payment']['discCode']);
					echo "<pre/>";
					print_r();
					 	
				}	*/			
				if($dateDiff1 > 0 )
				{
					if($discMonth > 0)
					{
						if($dateDiff2 > 0)
						{
							$errorMsg = 1;
							$err16 = 1;
							$discountMsg	= $parObj->_getLabenames($arrayErrorDataDiscount,'err5','name'); ///You have already one active discount code			
						}
					}
				}		
			}
			if($discApplyed['cnt'] == 0)
			{
				$getDiscCodeDetails		=	$objDisc->_getDiscDetails($discCode);	
				$discMonth				=	$getDiscCodeDetails['discount_month'];
				$AffdiscPer				=	$getDiscCodeDetails['discount_percentage'];
				$discExpiryDate			=	$getDiscCodeDetails['end_date'];   //yyyy-mm-dd
				//chage date format 
				$discExpiryDate			=	$objGen->_dateTomdY($discExpiryDate);
				$dateDiff2				=	$objGen->date_difference($today,$discExpiryDate);//
				if($discMonth > 0)
				{
					if($dateDiff2 > 0)
					{
						$totalDiscPer	+=	$AffdiscPer;
						//for database updation
						$_SESSION['payment']['discCode']		=	$activeDiscCode;
						$freeDaysMore							=	$getDiscCodeDetails['free_days'];
						$_SESSION['payment']['NoMonthDisc']	=	$discMonth;
					}
					else
					{ 
						$errorMsg = 1;
						$err16 = 1;
						$discountMsg		= 	$parObj->_getLabenames($arrayErrorDataDiscount,'err6','name'); //Discount code already expired
					}
				}
				else
				{
					//if discount code  have only free period
					$totalDiscPer		+=	$AffdiscPer;
					$freeDaysMore		=	$getDiscCodeDetails['free_days'];
				}
			}
			else
			{
				$errorMsg = 1;
				$err16 = 1;
				$discountMsg	= $parObj->_getLabenames($arrayErrorDataDiscount,'err7','name'); //You had entered this discount code before
			}
		}
		else
		{
			$errorMsg = 1;
			$err16 = 1;
			$discountMsg=$parObj->_getLabenames($arrayErrorDataDiscount,'err8','name'); //Referral id/discount code not valid
		}
	}	  
	if($errorMsg != 1)
	{
		$discDetail		= $objDisc->_getDiscDetails(trim($_POST['user_discount'])); //for findout free trail period of this discount code
		$freePeriofFrmDiscDetails = $discDetail['free_days'];
		if($freePeriofFrmDiscDetails != '')
		{
			$_POST['disc_period']					= $defaultFreePeriod + $freePeriofFrmDiscDetails;
		}
		else
		{
			$_POST['disc_period']					= $defaultFreePeriod ;
		}
		$Type	=	"DISC";
		$activeDisc	=	$objDisc->_getLastCode($userId,$Type);
		if(count($activeDisc) > 0)
		{
			//echo "<br>dis=".
			$activeDiscCode			=	$activeDisc['discount_code'];
			$enteredDiscDate		=	$activeDisc['start_date'];
			//chage date format 
			$enteredDiscDate		=	$objGen->_dateTomdY($enteredDiscDate);
			$activeDiscId			=	$activeDisc['id'];
			$getDiscCodeDetails		=	$objDisc->_getDiscDetails($activeDiscCode);
			$discMonth				=	$getDiscCodeDetails['discount_month'];
			$AffdiscPer				=	$getDiscCodeDetails['discount_percentage'];
			$discExpiryDate			=	$getDiscCodeDetails['end_date'];   //yyyy-mm-dd
			//chage date format 
			$discExpiryDate			=	$objGen->_dateTomdY($discExpiryDate);
			//check whether discount period ended after apply discount
			//ADD month to date
			$expiryDate			=	$objGen->_addMonthToDate($enteredDiscDate,$discMonth);
			$dateDiff1				=	$objGen->date_difference($today,$discExpiryDate);
			$dateDiff2				=	$objGen->date_difference($today,$expiryDate);
			if($dateDiff1 > 0 )
			{
				if($discMonth > 0 )
				{
					if($dateDiff2 > 0)
					{
						$totalDiscPer		+=	$AffdiscPer;
						//for database updation
						$_SESSION['payment']['discId']			=	$activeDiscId;
						$_SESSION['payment']['discCode']		=	$activeDiscCode;
						$_SESSION['payment']['NoMonthDisc']		=	$discMonth;
					}
				}
				else
				{
					//if discount code only have free period
					$totalDiscPer		+=	$AffdiscPer;
					//for database updation
					$_SESSION['payment']['discId']			=	$activeDiscId;
					$_SESSION['payment']['discCode']		=	$activeDiscCode;
				}	
			}	
		}
		$_SESSION['payment']['percentage']	= $totalDiscPer;
		$discount			=	$_SESSION['payment']['percentage'];	
		if($discount > 0)
		{
			$discAmt			=	($memShipFee*$discount)/100;
			$payFee				=	$memShipFee-$discAmt;
		}
		else
		{
			$payFee				=	$memShipFee;
		}
		$payFee				= 	round($payFee,2);
		if(isset($_SESSION['payment']['NoMonthDisc']))
			$discMonthPeriod	=	$_SESSION['payment']['NoMonthDisc'];
		else
			$discMonthPeriod	=	0;
		$subscription		=	$objDisc->_subscriptionMonthFees($memShipFee,$payFee,$discMonthPeriod,'');
		$_SESSION['payment']['payFee']	=	$payFee;
		if($freeDaysMore == '')
		{ 
			$freeDaysMore	=	0; 	
		}		
		$_SESSION['payment']['freedays']	=	$freeDaysMore;
		if($reffId != '')
		{
			/*if($reffCnt['cnt'] > 0){
			$reffType			=	'REFF';
			}else*/
			if( $discCnt['cnt'] > 0 )
			{
				$reffType			=	'DISC';
				if(!isset($_SESSION['payment']['discCode']))
				{
					$_SESSION['payment']['discCode']	=	$reffId;
				}
			}
			$elmts['discount_code']				=	$reffId;
			$elmts['discount_type']				=	$reffType;
			$elmts['user_id']					=	$userId;
			$elmts['start_date']				=	date('Y-m-d');
			$elmts['payment_status']			=	'failed';										
			$dbObj->_insertRecord("discount_users",$elmts);
			$disUserId=mysql_insert_id();			
			//$disUserId=123456;
			$_SESSION['payment']['discUser_id']	= $disUserId;
		}
		if($payFee != '')
		{
		
			$payElmts['payment_userid']				=	$userId;
			$payElmts['payment_amount']				=	$payFee;
			$payElmts['payment_date']				=	date('Y-m-d');
			$payElmts['payment_status']				=	0;
			$dbObj->_insertRecord("payment",$payElmts);
			//statusRecord($_SESSION['login']['user_email'],'payment insertion','userreg2.php',$userId,$payFee,'8');
			$payReffId=mysql_insert_id();
			//$payReffId=1234567;
			$_SESSION['payment']['pay_id']	= $payReffId;
			$paymentTemp = array();
			$paymentTemp['user_id']		 		= $userId;
			$paymentTemp['pay_id'] 				= $_SESSION['payment']['pay_id'];
			$paymentTemp['discUser_id'] 		= $_SESSION['payment']['discUser_id'];
			$paymentTemp['ActReffId'] 			= $_SESSION['payment']['ActReffId'];
			$paymentTemp['freedays'] 			= $_SESSION['payment']['freedays'];
			$paymentTemp['payFee'] 				= $_SESSION['payment']['payFee'];
			$paymentTemp['user_email'] 			= $_SESSION['login']['user_email'];
			$paymentTemp['pay_date']			= date('Y-m-d');
			$paymentTemp['discCode'] 			= $_SESSION['payment']['discCode'];
			$paymentTemp['discId'] 				= $_SESSION['payment']['discId'];	
			$payEntry = $dbObj->_insertRecord("user_payment_temp",$paymentTemp);
			echo $payEntry;
			echo "test";
		}
	}
}
else
{
//echo "aji";
	header("Location:payment1_reg.php");
	exit;
}		
?>
