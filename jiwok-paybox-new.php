<?php
ob_start();
//For log
$myVal	=	"XXNEXTXX";
foreach($_REQUEST as $key=>$val)
{
	$myVal	=	$myVal.$key."->".$val." ";	
}
$myVal	=	$myVal."XXNEXTXX";
$myFile = "payboxNewLog.txt";
$fh = fopen($myFile, 'a');
fwrite($fh, $myVal);
fclose($fh);
//Log end
require_once('includes/config.php');
require("sign.php"); // Contains function to verify sign returned in query string
include_once('includes/classes/class.discount.php');
include_once('includes/classes/class.programs.php');
require_once('papGlobal.php');
include_once('mail_gift.php');
include_once('pdf/examples/attachment.php');
include_once('./includes/classes/class.giftcode.php');
include('./admin/giftcodegen.php');
global $gift_image;
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
$headers .= 'From: <jiwok@example.com>' . "\r\n";
$headers .= 'Cc: jiwok@example.com' . "\r\n";
$status   = mail('dileepe.reubro@gmail.com', 'hai', $_REQUEST, $headers); 
$objDisc		=	new Discount($lanId);
$objPgm     	= 	new Programs($lanId);
$obj= new gift();
/*echo "<br>au=".$_REQUEST['authorization_no'];
echo "<br>au=".$_REQUEST['amount'];
echo "<br>au=".$_REQUEST['order_reference'];
echo "<br>au=".$_REQUEST['transaction_id'];
echo "<br>au=".$_REQUEST['error_code'];*/
if(isset($_REQUEST['authorization_no'], $_REQUEST['amount'], $_REQUEST['order_reference'], $_REQUEST['transaction_id'], $_REQUEST['error_code'])){
	//$testsign = pbxtestsign($_SERVER["QUERY_STRING"],"pubkey.pem");
	$testsign	= PbxVerSign($_SERVER["QUERY_STRING"],"pubkey.pem"); // Test sign returned in query string using the file with public key of Paybox - pubkey.pem
	if($testsign==1) {
	     	if (base64_decode($_REQUEST['order_reference'], true))
			{
				$sessiondata	=	base64_decode($_REQUEST['order_reference']);
			}
			else
			{
				$sessiondata	=	$_REQUEST['order_reference'];
			}
		 $testdata=explode(",",$sessiondata);
		 if($testdata[9]=='gift') {
		   $_REQUEST['order_reference']=$sessiondata;
		 }
		if($_REQUEST['error_code']=='00000') {
		//echo "yes";

			$updateKeys		=	$sessiondata;
			$amt			=	round(($_REQUEST['amount']/100),2);
			$updateKeysArr	=	explode(",",$updateKeys);
			if($_REQUEST['amount'] == 3490 && ($updateKeysArr[12] == 'parismarathon' || $updateKeysArr[12] == 'semideparis')):
				$nextMonth		=	strtotime('+ 6 month');
			elseif($_REQUEST['amount'] == 1990 && ($updateKeysArr[12] == 'parismarathon' || $updateKeysArr[12] == 'semideparis')):
				$nextMonth		=	strtotime('+ 3 month');
			else:
				$nextMonth		=	strtotime('+ 1 month');
			endif;
			//$nextMonth		=	strtotime('+ 1 month');
			$date			=	date('m/d/y',$nextMonth);
			$extendDatetime	=	strtotime($date.' +'.$updateKeysArr[6].' Days');
			$extendDate		=	date('Y-m-d',$extendDatetime); 
		  if($testdata[9]=='gift') {
			  
			//added by dijo
		 	/*$ary	    =	$testdata;
			$to		    ='dijo.reubro@gmail.com';
			$subject    = "Test mail";
			$message	=	'';
			foreach($ary as $key=>$val)
			{
				$message .= $key.'<=>'.$val;
			}
			$from = "dijo.reubro@gmail.com";
			$headers = "From:" . $from;
			mail($to,$subject,$message,$headers);*/
					 
		 
			$filename = 'cron_gift.txt';
			$fp = fopen($filename, "a+");
			fputs($fp, ":--------------------------");			
			fputs($fp, $message);	
			fputs($fp, ":------------------------------");
			fputs($fp, "\r\n");
			fclose($fp);
		 
		 //added by dijo ends
			  
			  
			  
				$gift_type=$testdata[1];
				$gift_friend=$testdata[2];
				$purchase_id=$testdata[3];
				$user_name=$testdata[4];
				$user_email=$testdata[5];
				$friend_name=$testdata[6];
				if($friend_name==''){$friend_name=" ";}
				$friend_email=$testdata[7];
				//$frend_msg=$testdata[8];
				$language=$testdata[10];
				$user_lname=$testdata[11];
				$cur_lan=$testdata[12];//
				//$cur_sym	=	$testdata[14];//Euro
				//echo "yes";
                $period		=	$gift_type;
				
				
				
				/*Admin Email Of Gift Payment*/
				
				  $qry 			= 	mysql_query("select * from jiwok_payment_plan where plan_status=1 and  plan_currency=".$language." AND plan_id=".$period)or die(mysql_error());
				  $data			=	mysql_fetch_array($qry,MYSQL_BOTH);
				  $git_amount	=	$data['plan_amount'];			  
				  $subject  	= 'Admin Email For Gift Payment' ;
				  
				  if($language ==	5)
				  {
					  
					  $email_from	=	'kontakt@jiwok.pl';
					  $email_to		=	'kontakt@jiwok.pl';
					  $headers  	= 	"From:".$email_from. "\r\n"."CC: info@jiwok.com"; 
				  }
				  else
				  {
					  $email_from	=	'info@jiwok.com';
					  $email_to		=	'admin@jiwok.com';
					  $headers  	= 	"From:".$email_from. "\r\n";
				  }				  
				  //$email 	= 'dijo.reubro@gmail.com' ;
				 
  				  $admin_msg	=	"
This user(".$user_name." ".$user_lname.") : with emailid :".$user_email." doing a payment gift code.The amount is:".$git_amount.$cur_lan." and the invoice id is: ".$updateKeys." and refference id is : ".$_REQUEST['transaction_id'];

				 mail( $email_to, $subject, $admin_msg, $headers);
				 mail( 'dijo.reubro@gmail.com', $subject, $admin_msg, $headers);
				
				/*Admin Email Of Gift Payment Ends*/
				
				
				//$period		=	$gift_type." mois";
				$membqry="SELECT `membership_fee`,`membership_feedollar` FROM `settings`";
				$membreslt = $GLOBALS['db']->getAll($membqry, DB_FETCHMODE_ASSOC);
				if($cur_lan == 'Dollar') {
					$fee		= $membreslt[0]['membership_feedollar'];
					$amt		=	$gift_type*$fee;
				} else {
					
					$fee		= $membreslt[0]['membership_fee'];
					$amt		=	$gift_type*$fee;
				}
				  $pdf_lang		=	'';
				  switch($language)	  {

				   case 1: {$gift_image =   './images/Jiwok_Gift_Card_bg_en.jpeg';
						    $periodsend= $period." months";
							$subject="Your Gift Voucher Jiwok";
							$subjectfrnd="Your Gift Voucher Jiwok sent by ";
							$replyto="info@jiwok.com";
							
							}break;
							

				   case 2: {$gift_image =   './images/Jiwok_Gift_Card_bg.jpg';
						    $periodsend= $period." mois";
							$subject="Votre bon cadeau Jiwok";
							$subjectfrnd="Votre bon cadeau Jiwok offert par ";
							$replyto="info@jiwok.com";}break;
							
							
				   case 5: {$gift_image 	=   './images/giftbg_pl.jpg';
				   			
							if($period == 3)
							{
								$mnths	=	' miesiące';//3
							}
							else 
							{
								$mnths	=	' miesięcy';//6,9
							}
						    $periodsend		= 	$period.$mnths;
							$subject		=	"Twój bon upominkowy Jiwok";
							$subjectfrnd	=	"przesyła Ci bon upominkowy Jiwok";
							$replyto		=	"kontakt@jiwok.pl";
							$pdf_lang		=	5;
							}break;
							


				   default: {$gift_image =   './images/Jiwok_Gift_Card_bg.jpg';
						    $periodsend= $period." mois";
							$subject="Votre bon cadeau Jiwok";
							$subjectfrnd="Votre bon cadeau Jiwok offert par ";
							$replyto="info@jiwok.com";}break;

				  }
				$unique=false;
							do
							{
							  $cap_code = get_code();
							  //echo $numchk=$obj->_getchecked($cap_code);
							  $numchk=$obj->new1($cap_code);
							  if($numchk ==0)
								 {$unique=true;}	 
							} while(!$unique); 
						   $gcode=$cap_code;
						$result1=$obj->_insertgift($period,$amt,$gcode);
				$code = mysql_query("SELECT max(code) as code FROM `gift_code` WHERE `codestatus` = 'unused' and codetype='$period'")or die(mysql_error());
				if(mysql_num_rows($code)>0)
				{
				  $dat=mysql_fetch_array($code,MYSQL_BOTH);
				  $gift_code_paid=$dat['code'];/////////setting paid code in to session variable
				  $query="UPDATE `gift_code` SET `codestatus` = 'purchased' WHERE code='$dat[code]'";
				  $res	= $GLOBALS['db']->query($query);
				  if($purchase_id!=0)
				  {  	
				  $query_retrv=mysql_query("select * from gift_member where purchaseid='$purchase_id'")or die(mysql_error());
				  $dat_rtev=mysql_fetch_array($query_retrv,MYSQL_BOTH);
				  $frend_msg=nl2br($dat_rtev['friendmessage']);
				  $query1="update gift_member set code='$dat[code]' where purchaseid='$purchase_id'";
				 $res	= $GLOBALS['db']->query($query1);
					          $query3="insert into gift_userdetails (id,code,purchaseid,purchase_currency,purchasedate) values('','$dat[code]','$purchase_id','".$cur_lan."',CURDATE())";
							  $res	= $GLOBALS['db']->query($query3);
				  }

				  $from_name="Jiwok";
				  //$replyto="info@jiwok.com";
					 if($gift_friend==0)
					 {
						 if($language == 5)
						 { //for polish user name should come first
							 $subjectfrnd=	$user_name." ".$user_lname." ".$subjectfrnd;
						 }
						 else
						 {
							 $subjectfrnd=$subjectfrnd.$user_name." ".$user_lname;
						 }
					    
						$query4="SELECT *
									FROM `contents`
									WHERE `content_title_id` = 'gift_from_frnd'
									AND `language_id` = '$language'";
						 $content	= $GLOBALS['db']->getRow($query4,DB_FETCHMODE_ASSOC);
				         $messageattchmnt=$content['template'];
						 $message=nl2br($content['content_body'])."\n";
						 ///$message.=$frend_msg;
						 $message=str_replace('{buyer First name}',$user_name,$message);
						 $message=str_replace('{buyer last name}',$user_lname,$message);
						 $message=str_replace('{message from the customer}',$frend_msg,$message);
						 if($language == 5)
						 {// for polsih url
						 		$message=str_replace("http://www.jiwok.com","<a  href='http://www.jiwok.com/pl/index.php'>http://www.jiwok.com</a>",$message);
						 }
						else
						{
							$message=str_replace("http://www.jiwok.com","<a  href='http://www.jiwok.com'>http://www.jiwok.com</a>",$message);
						}
							
						 //$varname1=$friend_name.rand().".pdf";
						 $varname1=$friend_email."_".date("d-m-y_H:i:s", time()).".pdf";
						 $varname="./pdffiles/".$varname1;
						 $messageattchmntto=str_replace('{FROM_NAME}',$user_name." ".$user_lname,$messageattchmnt);
						 $messageattchmntto=str_replace('{TO_NAME}',$friend_name,$messageattchmntto);
						 $messageattchmntto=str_replace('{GIFT_TYPE}',$periodsend,$messageattchmntto);
						 $messageattchmntto=str_replace('{GIFT_CODE}',$dat['code'],$messageattchmntto);
						 $messageattchmntto=str_replace('"',"'",$messageattchmntto);
						 $messageattchmntto=str_replace("http://www.jiwok.com","<a href='http://www.jiwok.com'>http://www.jiwok.com</a>",$messageattchmntto);
						 getpdf($varname,$subtitle,$messageattchmntto,$gift_image,$pdf_lang);
						 $filename=$varname1;
						 $path="./pdffiles/";
						 mail_attachment($filename, $path,$from_name, $friend_email, $user_email, $replyto, $subjectfrnd, $message);
					 }

					 if($gift_friend==1)
					 {

						$query4="SELECT *
						FROM `contents`
						WHERE `content_title_id` = 'gift_self'
						AND `language_id` = '$language'";
					} else {
						$query4="SELECT *
						FROM `contents`
						WHERE `content_title_id` = 'gift_to_frnd'
						AND `language_id` = '$language'";
					}
					 $content	= $GLOBALS['db']->getRow($query4,DB_FETCHMODE_ASSOC);
					 $message=nl2br($content['content_body'])."\n";
					 $message=str_replace("http://www.jiwok.com","<a href='http://www.jiwok.com'>http://www.jiwok.com</a>",$message);
				     $messageattchmnt=$content['template'];
					 //$varname1=$user_name.rand().".pdf";
					 $varname1=$user_email."_".date("d-m-y_H:i:s", time()).".pdf";
					 $varname="./pdffiles/".$varname1;
					 $messageattchmntslf=str_replace('{FROM_NAME}',$user_name." ".$user_lname,$messageattchmnt);
					 $messageattchmntslf=str_replace('{TO_NAME}',$friend_name,$messageattchmntslf);
					 $messageattchmntslf=str_replace('{GIFT_TYPE}',$periodsend,$messageattchmntslf);
					 $messageattchmntslf=str_replace('{GIFT_CODE}',$dat['code'],$messageattchmntslf);
					 $messageattchmntslf=str_replace('"',"'",$messageattchmntslf);
					 $messageattchmntslf=str_replace("http://www.jiwok.com","<a href='http://www.jiwok.com'>http://www.jiwok.com</a>",$messageattchmntslf);
					 getpdf($varname,$subtitle,$messageattchmntslf,$gift_image,$pdf_lang);
					 $filename=$varname1;
					 $path="./pdffiles/";
					 mail_attachment($filename, $path,$from_name, $user_email, "admin@jiwok.com", $replyto, $subject, $message);	  

			    }
				//header("Location:giftafterpayment.php");
			} else {

				$cur_lan=$updateKeysArr[13];//
				if($updateKeysArr[0] != 0 ){

				$query			= "SELECT * FROM `payment` WHERE `payment_id` = '".(int) trim($updateKeysArr[0])."'"; 
				$payment_row	= $GLOBALS['db']->getRow($query,DB_FETCHMODE_ASSOC);

				if($updateKeysArr[7] == "reg")
				{
					$payDate = date('Y-m-d');
				} else { // For renewal the payment start date should be the one already set in the database

					$payDate = $payment_row['payment_date'];					
					if($_REQUEST['amount'] == 3490 && ($updateKeysArr[12] == 'parismarathon' || $updateKeysArr[12] == 'semideparis')):

						$nextMonth		=	strtotime($payDate.' + 6 month');

					elseif($_REQUEST['amount'] == 1990 && ($updateKeysArr[12] == 'parismarathon' || $updateKeysArr[12] == 'semideparis')):

						$nextMonth		=	strtotime($payDate.' + 3 month');

					else:

						$nextMonth		=	strtotime($payDate.' + 1 month');

					endif;
					//$nextMonth		=	strtotime($payDate.' + 1 month');

					$date			=	date('m/d/y',$nextMonth);
					$extendDatetime	=	strtotime($date.' +'.$updateKeysArr[6].' Days');

					$extendDate		=	date('Y-m-d',$extendDatetime); 


				}

				$newPaymentIdVar	= '';



/* Converting dollar amount into curresponding euro amount.*/

				if($cur_lan == 'Dollar'){


							$selectSettings		=	"select * from settings";				

							$result				=	$GLOBALS['db']->getAll($selectSettings,DB_FETCHMODE_ASSOC);

							foreach($result as $key=>$data) {

									$framt			=   $data['membership_fee'];
									$enamt			=   $data['membership_feedollar'];

								}
							$enoneamt	=	$framt * (1/$enamt);
							$amtdol		=	$enoneamt	 * $amt; 	

						if($payment_row['payment_amount']==(float) trim($amtdol))
							{ 								
								$cond	=	1;
							}	
					}
				else
					{
						if($payment_row['payment_amount']==(float) trim($amt))
							{ 
								$cond	=	1;

							}

					} 


				if($payment_row['payment_id']==(int) trim($updateKeysArr[0]) && $cond == 1 && $payment_row['payment_status']== 0){

					$user_id = $updateKeysArr[3];
					$newPaymentIdVar	=$payment_row['payment_id'];
				    $query_user="update user_master set user_unsubscribed='0',user_req_unsubscribe='0000-00-00',unsubscribe_date='' where user_id='".(int) trim($user_id)."'";
					$res_user	= $GLOBALS['db']->query($query_user);

					$query	= "UPDATE `payment` SET `payment_amount` =  ".$amt.", `payment_status` = 1, `payment_date` = '".$payDate."', `payment_firstdate` = '".$payDate."', `payment_expdate` = '" .$extendDate."',`payment_currency` = '".$cur_lan."',  `payment_error_code` = '".$_REQUEST['error_code']."' WHERE `payment_id` = {$payment_row['payment_id']}";
					$res	= $GLOBALS['db']->query($query);

					$paymentTemp = $objPgm->_getUserPaymentTemp($user_id);

					if(count($paymentTemp)>0)
					{

					  $queryPay = "DELETE FROM `user_payment_temp` WHERE `user_id` = '".(int) trim($user_id)."'";

					  $res	= $GLOBALS['db']->query($queryPay);


					}


				}else{

				//for automatic payment (subscription payment)

				$today			=	date('Y-m-d');
				//$nextMonth		=	strtotime('+ 1 month');
				if($_REQUEST['amount'] == 3490 && ($updateKeysArr[12] == 'parismarathon' || $updateKeysArr[12] == 'semideparis')):

					$nextMonth		=	strtotime('+ 6 months');

				elseif($_REQUEST['amount'] == 1990 && ($updateKeysArr[12] == 'parismarathon' || $updateKeysArr[12] == 'semideparis')):

					$nextMonth		=	strtotime('+ 3 months');

				else:

					$nextMonth		=	strtotime('+ 1 month');

				endif;



				$date			=	date('Y-m-d',$nextMonth);
				$userId			=	$updateKeysArr[3];
				$amountPay		=	$amt;
                $query_user="update user_master set user_unsubscribed='0',user_req_unsubscribe='0000-00-00',unsubscribe_date='' where user_id='".(int) trim($userId)."'";
				$res_user	= $GLOBALS['db']->query($query_user);

				$query	= "INSERT INTO `payment` ( `payment_id` , `payment_userid` , `payment_date` , `payment_status` ,`payment_packageid` , `payment_amount` , `payment_expdate` , `payment_no_times`,`payment_currency`, `payment_error_code` ) VALUES ('', '$userId', '$today', '1', '0', '$amountPay', '$date', '1','$cur_lan','".$_REQUEST['error_code']."')";
				$res	= $GLOBALS['db']->query($query);
				$newPaymentIdVar	=mysql_insert_id();
				}

			}

		//update the discoutn_user table.

		if($updateKeysArr[1] != 0 ){

			 $query			= "SELECT * FROM `discount_users` WHERE `id` = '".(int) trim($updateKeysArr[1])."'";

			$disc_row	= $GLOBALS['db']->getRow($query,DB_FETCHMODE_ASSOC);

			if($disc_row['id']==(int) trim($updateKeysArr[1]) ){

				$queryDisc	= "UPDATE `discount_users` SET `payment_status` = 'success' WHERE `id` = {$disc_row['id']}";

				$res	= $GLOBALS['db']->query($queryDisc);


			}



		 }	
		//update the discoutn_user table if user already submit discount.
		if($updateKeysArr[4] != 0 ){

				$query			= "SELECT * FROM `discount_users` WHERE `id` = '".(int) trim($updateKeysArr[4])."'";

				$disc_row	= $GLOBALS['db']->getRow($query,DB_FETCHMODE_ASSOC);
				if($disc_row['id']==(int) trim($updateKeysArr[4]) ){

					$queryDisc	= "UPDATE `discount_users` SET `payment_status` = 'success' WHERE `id` = {$disc_row['id']}";

					$res	= $GLOBALS['db']->query($queryDisc);

				}	



		  }	


		  //update the discoutn_user table if user already submit discount.(in the case of reff Id)


		if($updateKeysArr[5] != 0 ){


				$query			= "SELECT * FROM `discount_users` WHERE `id` = '".(int) trim($updateKeysArr[5])."'";

				$disc_row	= $GLOBALS['db']->getRow($query,DB_FETCHMODE_ASSOC);

				if($disc_row['id']==(int) trim($updateKeysArr[5]) ){

					$queryDisc	= "UPDATE `discount_users` SET `payment_status` = 'success' WHERE `id` = {$disc_row['id']}";

					$res	= $GLOBALS['db']->query($queryDisc);


				}	


		  }	

		 //for doscount code updation and discount affilate

		$userId			=	$updateKeysArr[3];
		$discountcode	=	$updateKeysArr[2];
		$amount			=	$amt;

		if($discountcode != '0' ){

		 //$objDisc->_insertDiscountCommission($userId,$discountcode,$amount); this is commented because it doen't add the payment id to affliate commession table

		 $objDisc->_insertDiscountCommission($userId,$discountcode,$amount, $newPaymentIdVar);

			} 

		//for sale tracking
		$userDat = $objPgm->_getUserDetails(trim($userId));	

		$aff_id = trim(stripslashes($userDat['aff_refid']));
		$aff_bannerid = trim(stripslashes($userDat['aff_bannerid']));
		if($aff_id!="" && $aff_bannerid!="")
		{

				if($discountcode != '0' ){
					$discountAffReffId = $objPgm->_getDiscountAffiliateRefferalId($discountcode);
					if($discountAffReffId!=$aff_id)
		 			{ $objPgm->_calculatePapCommissionPro($aff_id,$aff_bannerid,$amount); }

				}

				else
			    	{ $objPgm->_calculatePapCommissionPro($aff_id,$aff_bannerid,$amount); }
				/*$queryup	= "UPDATE `user_master` SET `aff_refid` = '', `aff_bannerid` = '' WHERE `user_id` = {$userId}";

					$resup	= $GLOBALS['db']->query($queryup);	*/

			}		

		  }

		} else {

		// Error code not equal to 00000. ie Payment failed 

			$order_reference	= explode(',', $_REQUEST['order_reference']);
			$payment_id			= trim($order_reference[0]);
			$userId				= trim($order_reference[3]);
			$today				= date('Y-m-d');

			$amount				= round(($_REQUEST['amount']/100),2);
			if ($payment_id > 0) {

				$query			= "SELECT *, DATEDIFF(CURDATE()-`payment_date`) as days_since_first_pay FROM `payment` WHERE `payment_id` = '".(int) $payment_id."'"; 

				$payment_row	= $GLOBALS['db']->getRow($query,DB_FETCHMODE_ASSOC);

				if ($payment_row['payment_id']== $payment_id && $payment_row['payment_status']== 0 && $payment_row['days_since_first_pay'] <= 2) { // Check if first subscription month.

					$query	= "UPDATE `payment` SET `payment_error_code` = '".$_REQUEST['error_code']."' WHERE `payment_id` = ".$payment_row['payment_id'];

					$res	= $GLOBALS['db']->query($query);

				} elseif ($userId > 0) {

					 $query	=	"INSERT INTO `payment` 

								(`payment_userid` ,`payment_date` ,`payment_amount`, `payment_error_code`)


								VALUES (?, ?, ?, ?)";

					$res	= $GLOBALS['db']->query($query, array($userId, $today, $amount, $_REQUEST['error_code']));

				}

			}

		}		

	  }


	}

/// Log all calls of this file 


$log_query	= "INSERT INTO `payment_log` 

				(`id`, `time`, `query_string`) 

				VALUES 

				(DEFAULT, DEFAULT, ?)";

$res		= $GLOBALS['db']->query($log_query, $_SERVER["QUERY_STRING"]);
?>