<?php
ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);
echo "LL";exit;
/////from old page jiwok-paybox-new.php
require_once('includes/config.php');
require("sign.php"); // Contains function to verify sign returned in query string
include_once('includes/classes/class.discount.php');
include_once('includes/classes/class.programs.php');
/*require_once('papGlobal.php');*/
include_once('mail_gift.php');
include_once('pdf/examples/attachment.php');
include_once('./includes/classes/class.giftcode.php');
include('./admin/giftcodegen.php');
ob_end_clean();
global $gift_image;
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
$headers .= 'From: <jiwok@example.com>' . "\r\n";
$headers .= 'Cc: jiwok@example.com' . "\r\n";
//$status   = mail('dileepe.reubro@gmail.com', 'hai', $_REQUEST, $headers); 
$objDisc		=	new Discount($lanId);
$objPgm     	= 	new Programs($lanId);
$obj= new gift();
/////from old page jiwok-paybox-new.php ends
require_once ('stripe_code/config.php');
include_once("includes/classes/class.DbAction.php");
// $data = $_REQUEST;

 //=====================================
$from_host = $_SERVER['HTTP_HOST'];
$data['token']['id'] = 'tok_1AhdZfGOehFu6bSUgdtq30gp';
$data['token']['email'] = 'rohit.reubro@gmail.com';
$data['amount'] = '5990';
$data['language_name'] = 'french';
$data['order_reference'] = 'MCwxMiwwLDIwOTIxNyxEYW5pZWwsZGFuaWVsLnZhaW1hbkBpbnNlcm0uZnIsQW5uZSBWYWltYW4sYW5uZS52YWltYW5AaW5yYS5mciwwLGdpZnQsMixWYWltYW4sRXVybyww';

//======================================
$from_host = $_SERVER['HTTP_HOST'];
$token = $data['token']['id'];
$email = $data['token']['email'];
$amount = $data['amount'];
$language_name = $data['language_name'];
$order_reference = $data['order_reference'];

echo "<pre/>";print_r($data['order_reference']);exit;
if (base64_decode($data['order_reference'], true))
			{
				
				$sessiondata	=	base64_decode($data['order_reference']);
				
			}
			else
			{
				
				$sessiondata	=	$_REQUEST['order_reference'];
			}
			 $testdata=explode(",",$sessiondata);
		
			
		 if($testdata[9]=='gift') {
		   $_REQUEST['order_reference']=$sessiondata;
		 }

	
	if($language_name == 'french')
	{
		$currency ='EUR';
	}
	else if($language_name == 'polish')
	{
		$currency ='PLN';
	}
	
	//===================================================
	
	// Need a payment token:
    if ($token)
	 {      

		
		// Check for a duplicate submission, just in case:
		// Uses sessions, you could use a cookie instead.
		if (isset($_SESSION['token']) && ($_SESSION['token'] == $token))
		{
			$errors['token'] = 'You have apparently resubmitted the form. Please do not do that.';
		 } 
		 else
		  { // New submission.
			$_SESSION['token'] = $token;
		  }

    }
	else
	 {
		$errors['token'] = 'The order cannot be processed. Please make sure you have JavaScript enabled and try again.';
	  }
	
	if(empty($errors)) {
		

		// create the charge on Stripe's servers - this will charge the user's card
		try {
				
/*$customer = \Stripe\Customer::create(array(
      'email' => $email,
      'card'  => $token
  ));

  $charge = \Stripe\Charge::create(array(
      'customer' => $customer->id,
      'amount'   => $amount,
      'currency' => $currency
  ));
  */
  $charge->paid = true;

  if($charge->paid == true)
 {
	 
   
     ///table codes starts
     
     $updateKeys		=	$sessiondata;
	 $amt			=	round(($amount/100),2);
	 $updateKeysArr	=	explode(",",$updateKeys);
	 if($amount == 3490 && ($updateKeysArr[12] == 'parismarathon' || $updateKeysArr[12] == 'semideparis')):
				$nextMonth		=	strtotime('+ 6 month');
			elseif($amount == 1990 && ($updateKeysArr[12] == 'parismarathon' || $updateKeysArr[12] == 'semideparis')):
				$nextMonth		=	strtotime('+ 3 month');
			else:
				$nextMonth		=	strtotime('+ 1 month');
			endif;
			$date			=	date('m/d/y',$nextMonth);
			$extendDatetime	=	strtotime($date.' +'.$updateKeysArr[6].' Days');
			$extendDate		=	date('Y-m-d',$extendDatetime); 
			
			
			
			
			
			/*  if($testdata[9]=='gift') { 
			$filename = 'cron_gift.txt';
			$fp = fopen($filename, "a+");
			fputs($fp, ":--------------------------");			
			fputs($fp, $message);	
			fputs($fp, ":------------------------------");
			fputs($fp, "\r\n");
			fclose($fp);*/
		 
		 //added by dijo ends
			  
			  echo "<pre/>";print_r($testdata);
			  
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
				
				echo  $qry 			= 	mysql_query("select * from jiwok_payment_plan where plan_status=1 and  plan_currency=".$language." AND plan_id=".$period)or die(mysql_error());
				  $data			=	mysql_fetch_array($qry,MYSQL_BOTH);
				   //$qry			=	"select * from jiwok_payment_plan where plan_status=1 and  plan_currency=".$language." AND plan_id=".$period;
				   // $data			=	$GLOBALS['db']->getRow($qry,DB_FETCHMODE_ASSOC);
					
					print_r($data);exit;
				  $git_amount	=	$data['plan_amount'];		  
				  $subject  	= 'Admin Email For Gift Payment' ;
				  
				  if($language ==	5)
				  {
					  
					  $email_from	=	'kontakt@jiwok.pl';
					  $email_to		=	'kontakt@jiwok.pl';
					 // $email_to		=	'neethu.reubro@gmail.com';
					  $headers  	= 	"From:".$email_from. "\r\n"."CC: info@jiwok.com"; 
				  }
				  else
				  {
					  $email_from	=	'info@jiwok.com';
					$email_to		=	'admin@jiwok.com';
					  // $email_to		=	'neethu.reubro@gmail.com';
					  $headers  	= 	"From:".$email_from. "\r\n";
				  }				  
				  //$email 	= 'dijo.reubro@gmail.com' ;
				 
  				  $admin_msg	=	"
This user(".$user_name." ".$user_lname.") : with emailid :".$user_email." doing a payment gift code from ".$from_host.".The amount is:".$git_amount.$cur_lan." and the customer id is: ".$customer->id;
 
				 //mail( $email_to, $subject, $admin_msg, $headers);   
				 echo  $admin_msg; 
				 mail( 'dijo.reubro@gmail.com', $subject, $admin_msg, $headers);exit;
				
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
				echo $code = $GLOBALS['db']->query("SELECT max(code) as code FROM `gift_code` WHERE `codestatus` = 'unused' and codetype='$period'")or die(mysql_error());exit;
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
				
			}
			
			
			
			
			///else part starts
			
			
				else { 

				$cur_lan=$updateKeysArr[13];//
				if($updateKeysArr[0] != 0 ){

				$query			= "SELECT * FROM `payment` WHERE `payment_id` = '".(int) trim($updateKeysArr[0])."'"; 
				$payment_row	= $GLOBALS['db']->getRow($query,DB_FETCHMODE_ASSOC);

				if($updateKeysArr[7] == "reg")
				{
					$payDate = date('Y-m-d');
				} else { // For renewal the payment start date should be the one already set in the database

					$payDate = $payment_row['payment_date'];					
					if($amount == 3490 && ($updateKeysArr[12] == 'parismarathon' || $updateKeysArr[12] == 'semideparis')):

						$nextMonth		=	strtotime($payDate.' + 6 month');

					elseif($amount== 1990 && ($updateKeysArr[12] == 'parismarathon' || $updateKeysArr[12] == 'semideparis')):

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

					$query	= "UPDATE `payment` SET `payment_amount` =  ".$amt.", `payment_status` = 1, `payment_date` = '".$payDate."', `payment_firstdate` = '".$payDate."', `payment_expdate` = '" .$extendDate."',`payment_currency` = '".$cur_lan."' WHERE `payment_id` = {$payment_row['payment_id']}";
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
				if($amount == 3490 && ($updateKeysArr[12] == 'parismarathon' || $updateKeysArr[12] == 'semideparis')):

					$nextMonth		=	strtotime('+ 6 months');

				elseif($amount == 1990 && ($updateKeysArr[12] == 'parismarathon' || $updateKeysArr[12] == 'semideparis')):

					$nextMonth		=	strtotime('+ 3 months');

				else:

					$nextMonth		=	strtotime('+ 1 month');

				endif;



				$date			=	date('Y-m-d',$nextMonth);
				$userId			=	$updateKeysArr[3];
				$amountPay		=	$amt;
                $query_user="update user_master set user_unsubscribed='0',user_req_unsubscribe='0000-00-00',unsubscribe_date='' where user_id='".(int) trim($userId)."'";
				$res_user	= $GLOBALS['db']->query($query_user);

				$query	= "INSERT INTO `payment` ( `payment_id` , `payment_userid` , `payment_date` , `payment_status` ,`payment_packageid` , `payment_amount` , `payment_expdate` , `payment_no_times`,`payment_currency`) VALUES ('', '$userId', '$today', '1', '0', '$amountPay', '$date', '1','$cur_lan')";
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

			}	echo "1";exit;	

		  }
			
			
			
			///else part ends
			
			
			
			
			
     ///table code ends
     
     
     echo "1";exit;
     
     
 
 }
 else 
{
	///
							$paymentDetails	=	base64_encode(serialize($charge));
							$temparray = array();
							$temparray['user_id'] = $data['user_id'];
							$temparray['data'] = $paymentDetails;
							$chkNewEntry = $this->_insertRecord("stripe_transaction_errors",$temparray);
	
	
    echo "0";exit;
}
	}
		catch(Exception $e){
   $e->getMessage();
    echo "0";exit;
      exit;
	}
	}
	
	
