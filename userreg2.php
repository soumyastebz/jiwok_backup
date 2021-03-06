<?php include("session_local.php"); 
if(session_id() == '') {
    session_start();
}
include_once('includeconfig.php');
include_once('regDetail.php');
include_once('./includes/classes/class.member.php');
include_once("includes/classes/class.discount.php");
include_once('includes/classes/class.Languages.php');
include_once('includes/classes/class.massmail.php');
include_once('includes/classes/class.giftcode.php');
include_once('./includes/classes/class.registration.php');
include_once('includes/classes/class.fbLogin.php');
include_once "includes/classes/class.sendgrid.php";
$sendg          =   new sendgrid();
$parObj 		=   new Contents('userreg2.php');
$objGen   		=	new General();
$dbObj     		=   new DbAction();	
$objMember		= 	new Member($lanId);
$objMassmail	=   new Massmail($lanId);
$objDisc		= 	new Discount($lanId);
$lanObj 		= 	new Language();
$objgift        =   new gift();
$registration	=	new registration();
$fbLoginObj		=	new fbLogin();

//~ ini_set('display_errors',1);
//~ error_reporting(E_ALL|E_STRICT);
unset($_SESSION['giftregcheck']);
unset($_SESSION['registration']);

//for redirection page1 if not completed
if(!isset($_SESSION['login'])){
	header("Location:userreg1.php",true,301);
}
$elmts_ins=array();
$elmts_ins=$_SESSION['elmts1'];	
if(isset($_SESSION['user']['userId'])){
	header("location:myprofile.php",true,301);
	exit;
}

$totalDiscPer=0;
$today				=	date('m/d/Y');
//for discount code
if($lanId=="") $lanId=1;
$langName=strtolower($lanObj->_getLanguagename($lanId));
$optionMenus	= $objMember->_getOptionalMenus($siteMasterMenuConfig['USER_OPTIONAL_FIELDS'],$lanId);
/* Take voice preference from label_manager */
$voicePrefer	= $objMember->_getOptionalMenus($siteMasterMenuConfig['VOICE'],$lanId);
$weightUnits    = $objMember->_getOptionalMenus($siteMasterMenuConfig['WEIGHT'],$lanId);
$heightUnits    = $objMember->_getOptionalMenus($siteMasterMenuConfig['HEIGHT'],$lanId);
$returnData		= $parObj->_getTagcontents($xmlPath,'registrationUser','label');
$arrayData		= $returnData['general'];
$GetmailData		= $parObj->_getTagcontents($xmlPath,'forgotpassword','label');
$mailData			= $GetmailData['general'];
$returnError	=	$parObj->_getTagcontents($xmlPath,'registrationUser','messages');
$dataError		=	$returnError['errorMessage'];
if(isset($_POST['submit']))
{   


	$registration->validateRegistrationStep2($parObj,$xmlPath); 
	if($registration->errorMsg == 0)
	{
		$resqry1 = $GLOBALS['db']->query("SELECT MAX(user_id) as maximum FROM user_master");
		while ($resqry1->fetchInto($row)) 
		{
			$elmts_ins['user_id'] = $row[0]+1;
		}
		$chkins=$dbObj->_insertRecord("user_master",$elmts_ins);
		$resqry2 = $GLOBALS['db']->query("SELECT MAX(user_id) as maximum FROM user_master where user_email='".$elmts_ins['user_email']."'");
		while ($resqry2->fetchInto($row)) 
		{
			$userId=$_SESSION['login']['userId']=$nextId = $row[0];
		}
		statusRecord($_SESSION['login']['user_email'],'inserted user-no codes','userreg2.php',$nextId,$payFee,'7');
        unset($_SESSION['elmts1']);
		$_SESSION['payment']['user_email']			=	$_SESSION['login']['user_email'];
		$selectSettings		=	"select * from settings";
		$result				=	$GLOBALS['db']->getAll($selectSettings,DB_FETCHMODE_ASSOC);
		foreach($result as $key=>$data)
		{
			$defaultFreePeriod  =   $objGen->_output($data['free_days']);
		}
		$selectSettings		=	"select * from settings";
		$result				=	$GLOBALS['db']->getAll($selectSettings,DB_FETCHMODE_ASSOC);
		foreach($result as $key=>$data)
		{
			$memShipFee			=   $objGen->_output($data['membership_fee']);
		}
		$payFee				=	$memShipFee;
		$payFee				= 	round($payFee,2);
		//for subscription month fee calculation
		if(isset($_SESSION['payment']['NoMonthDisc']))
			$discMonthPeriod	=	$_SESSION['payment']['NoMonthDisc'];
		else
			$discMonthPeriod	=	0;

		$subscription		=	$objDisc->_subscriptionMonthFees($memShipFee,$payFee,$discMonthPeriod,'');
		$_SESSION['payment']['payFee']	=	$payFee;
		//if the discount code contain free period
		if($freeDaysMore == ''){ $freeDaysMore	=	0; 	}
			$_SESSION['payment']['freedays']	=	$freeDaysMore;
		//insert discount_user
		if($payFee != '')
		{
			//insert payment page
			$payElmts['payment_userid']				=	$userId;
			$payElmts['payment_amount']				=	$payFee;
			//$payElmts['payment_date']				=	date('Y-m-d');
			$payElmts['payment_status']				=	0;
			$objDb->_insertRecord("payment",$payElmts);
			statusRecord($_SESSION['login']['user_email'],'payment insertion','userreg2.php',$userId,$payFee,'8');
			$payReffId=mysql_insert_id();
			$_SESSION['payment']['pay_id']	= $payReffId;
		}			
		//calculation for discount ends here
		//We are going to upload the user photo.
		$_POST['user_dob']				=	$_POST['user_day'].'/'.$_POST['user_month'].'/'.$_POST['user_year'];
		unset($_POST['user_day']);
		unset($_POST['user_month']);
		unset($_POST['user_year']);
		//unset($_POST['user_discount']);
		if($_SESSION["RegMode"]=="fbLogin"){
			$fbLoginObj->setUserTocken($nextId,$_SESSION["fbaccesTkn"],$_SESSION["fbUserId"]);
			$fbPrlfImg	=	$fbLoginObj->saveFacebookImage($_SESSION["fbUserId"]);
			unset($_SESSION["RegMode"]);
		}
		$_SESSION['registration'] 		=	$_POST;	
		$reg 	= $objGen->_clearElmtsWithoutTrim($_SESSION['registration']);
		$elmts	= array_slice($reg,0,3);
		$nextId	= $_SESSION['login']['userId'];
		 
		//$totalFreePeriods = $reg['disc_period'];
		/**************Assign the dtat to the corresponding array*****************/
		$elmts['user_language'] 			= $reg['user_language'];
		$elmts['user_country'] 				= $reg['user_country'];
        $elmts['user_timezone'] 			= $reg['user_timezone'];
		$elmts['user_voice'] 				= $reg['user_voice'];
		$elmts['user_dob'] 					= $reg['user_dob'];
		$elmts['user_type']				    = $siteUsersConfig['MEMBER'];
		$elmts['user_doj']					= date('Y-m-d H:i:s');
		$elmts['user_email'] 		    	= addslashes($_SESSION['login']['user_email']);
		$elmts['user_alt_email']   		    = $elmts['user_email'];
		$elmts['paybox_email']   		    = $elmts['user_email'];
		$elmts['user_password'] 		    = addslashes(base64_encode($_SESSION['login']['password']));
		$elmts['user_weight_value']	        = $reg['user_weight_value'];
		$elmts['user_weight_unit']		    = $reg['user_weight_unit'];
		$elmts['user_height_value']	        = $reg['user_height_value'];
		$elmts['user_height_unit']		    = $reg['user_height_unit'];
		if($fbPrlfImg){
			$elmts['user_photo']		    = $fbPrlfImg;
		}
		$elmts['user_free_period']		    = $totalFreePeriods;
		$elmts['user_username']				= addslashes($_SESSION['login']['user_email']);
		//$elmts['user_reff_id']				= "REFF".uniqid(); // client kept it as pending
		$elmts['user_discount_status']		= 0;	
		//$elmts['user_refferal_status']		= 0; // 1 for refferal code activation
		$elmts['user_status']				= 1;
		$elmts['user_newsletter']		    = trim($_POST['user_newsletter']);
		statusRecord($_SESSION['login']['user_email'],'2nd step updation of master table over','userreg2.php','0',$payFee,'6');
		$chkNewEntry = $dbObj->_updateRecord("user_master",$elmts,"user_id = {$nextId}");	
		
		$elmts1		= array();
		$elmts1['usermaster_id']	= $nextId;
		// For tracking user activities using Trak.io
		/*include_once("includes/trak_analysis.php");
		$trakObj	=	new trakAnalysis();
		$response 	= 	$trakObj->trakRegistration($_SESSION['login']['user_email']); */
		//For mix panel tracking
		$_SESSION['mixRegStat']	=	1;		
		//insert referral datas		
		//For referral system starts
		/*****************************************************************************
			* @author 	: 	Dileep E
			* Date 		:	29-10-2011
			* Insert referral details
		* ***************************************************************************/					
	   if(isset($_COOKIE["jiwok_medium"])){
			$refr_data = array();
			$refr_data['user_id'] = $nextId; 
			$refr_data['referrer_user_id'] = 0;
			$refr_data['referred_medium'] = addslashes(strtolower($_COOKIE["jiwok_medium"]));			
			$dbObj->_insertRecord("jiwok_referrals",$refr_data);
			setcookie("jiwok_medium", "", time()-3600);
		}else if(isset($_SESSION['referrarId']) && isset($_SESSION['medium']))
		{
			$refr_data = array();
			$refr_data['user_id'] = $nextId; 
			$refr_data['referrer_user_id'] = $_SESSION['referrarId'];
			$refr_data['referred_medium'] = $_SESSION['medium'];			
			$dbObj->_insertRecord("jiwok_referrals",$refr_data);
			unset($_SESSION['referrarId']);
			unset($_SESSION['medium']);
		}		
		//Mail after the registration with the user details
		/////mail
		if($chkNewEntry > 0)
		{
			$userName = $_SESSION['login']['user_email'];
			$userNameNew = eregi_replace("\[".$att_name."\]",$att_value,$userName);
			$password = $_SESSION['login']['password'];
			$password =  mb_convert_encoding($password, 'UTF-8', 'ISO-8859-1');
			$emailTo		=  $_SESSION['login']['user_email'];
			$mailArray      =  $objMassmail->_fetchSettingsEmail();
			$return_email 	= $mailArray['RETURN_MAIL'];
			$bounce_email 	= $mailArray['BOUNCE_MAIL'];
			$subject = $parObj->_getLabenames($arrayData,'reg_mail_subject','name');			
			if($lanId	==	5)
			{
				$siteUrl 	= 'http://www.jiwok.pl';
				$extra_link2	= 'http://'.$_SERVER['HTTP_HOST'].'/pl/faq?mode=autologin&authid='.base64_encode($nextId);
				$sitelink	= 'http://'.$_SERVER['HTTP_HOST'].'/pl/?mode=autologin&authid='.base64_encode($nextId);
			}
			else
			{
				$siteUrl 	= 'http://'.$_SERVER['HTTP_HOST'].'/';
				$extra_link2	= 'http://'.$_SERVER['HTTP_HOST'].'/faq?mode=autologin&authid='.base64_encode($nextId);
				$sitelink	= 'http://'.$_SERVER['HTTP_HOST'].'/?mode=autologin&authid='.base64_encode($nextId);
			}
			$extra_link1	= 'http://'.$_SERVER['HTTP_HOST'].'/custom_page.php?title=aide-video&mode=autologin&authid='.base64_encode($nextId);
			$msg = "\n".$parObj->_getLabenames($arrayData,'reg_mail_to','name')."\n\n";
			$msg .= $parObj->_getLabenames($arrayData,'reg_mail_1','name')."\n\n";
			$msg .= $parObj->_getLabenames($arrayData,'reg_mail_2','name')."\n\n";
			$msg .= $parObj->_getLabenames($arrayData,'reg_mail_3','name').": \n\n";
			$msg .= $parObj->_getLabenames($arrayData,'reg_mail_username','name').": ".$userNameNew."\n";
			$msg .= $parObj->_getLabenames($arrayData,'reg_mail_password','name').": ".$password."\n\n";
			$msg .= $parObj->_getLabenames($arrayData,'reg_mail_link','name')." "."<a href=".$sitelink." style='text-decoration:none;' >".$siteUrl."</a>\n\n";
			$msg .= "<b>".$parObj->_getLabenames($arrayData,'reg_mail_bot1','name')."</b>"."\n\n\n";
			if($reg['user_language'] == 5)
			{
				$msg .= $parObj->_getLabenames($arrayData,'reg_mail_bot2','name')." kontakt@jiwok.pl ";
			}
			else
			{
				$msg .= $parObj->_getLabenames($arrayData,'reg_mail_bot2','name')." coach@jiwok.com ";
			}
			$msg .= $parObj->_getLabenames($arrayData,'reg_mail_bot3','name')."\n\n";
			/*New addition in welcome email*/
			$msg .= "\n<b>".$parObj->_getLabenames($arrayData,'reg_mail_extra_1','name')."</b>\n\n";
			$msg .= $parObj->_getLabenames($arrayData,'reg_mail_extra_2','name')."\n\n";
			if($lanId	!=	5)	{			
				$msg .= "<a href='http://itunes.apple.com/us/app/jiwok/id377378756?mt=8' style='text-decoration:none;'>".$parObj->_getLabenames($arrayData,'reg_mail_extra_5','name')."</a>\n\n";	
				$msg .= "<a href='https://play.google.com/store/apps/details?id=com.jiwok.jiwok&hl=fr' style='text-decoration:none;'>".$parObj->_getLabenames($arrayData,'reg_mail_extra_6','name')."</a>\n\n";
				$msg .= "<a href='$extra_link1' style='text-decoration:none;'>".$parObj->_getLabenames($arrayData,'reg_mail_extra_3','name')."</a>\n\n";	
			}
			$msg .= "<a href='$extra_link2' style='text-decoration:none;'>".$parObj->_getLabenames($arrayData,'reg_mail_extra_4','name')."</a>\n\n";
			$msg			= 	str_replace("#BR#",'<br/>',$msg);
			$msg 			= nl2br($msg);
			if($lanId	==	5)
			{
				$mailHeaderImage	=	"http://www.jiwok.com/images/hearder_newsletter_pl.png";	
			}
			else
			{
				$mailHeaderImage	=	"http://gallery.mailchimp.com/a5cb65461711684694f82bfce/images/hearder_newsletter.png";		
			}
			$mailTPL		=	'<body  style="color:#000" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
				<table width="100%" cellspacing="0" style="background-color: #07ADDF;">
				<tr>
				<td valign="top" align="center">
				<table id="contentTable" cellspacing="0" cellpadding="0" width="600"><tr><td>
				<table width="600" cellpadding="0" cellspacing="0">
				<tr>
				<td style="background-color: #48D1CC;border-bottom: 0 none #FFFFFF; border-top: 0 none #000000;padding: 0;text-align: center;" align="right"><div style="color: #663300;font-family: Verdana;font-size: 10px;line-height: 200%;text-decoration: none;" mc:edit="header"><span style="color: rgb(51, 102, 255);"><b> </b></span><a href="*|ARCHIVE|*" class="adminText"><span style="color: rgb(51, 102, 255);"></span></a></div></td>
				</tr>
				<tr>
				<td style="background-color: #FFFFFF; border-bottom: 0 none #FFFFFF;border-top: 0 none #333333;padding: 0;"><div class="headerBarText"><img mc:edit="header_image" style="width: 600px;" mc:allowdesigner mc:allowtext src="'.$mailHeaderImage.'"></div></td>
				</tr>
				</table>
				<table width="600" cellpadding="20" cellspacing="0" class="bodyTable">
				<tr>
				<td align="left" valign="top" style="background-color: #FFF;border-top: 0 none #FFFFFF;padding: 20px;">#MESSAGECNT#
				</td>
				</tr>
				<tr><td style="background-color: #48D1CC;border-top: 0 none #FFFFFF;padding: 20px;">#SLOGAN#</td></tr>
				</table>
				</td></tr></table>
				</td>
				</tr>
				</table>
				<span style="padding: 0px;"></span>';
				$slogn_footer	=	$parObj->_getLabenames($arrayData,'slogan','name');	
				$msg  			= 	str_replace("#FNAME#",$_SESSION['registration']['user_fname'],$msg);	
				$msg  			=	str_replace("#MESSAGECNT#",$msg,$mailTPL);
				$msg  			= 	str_replace("#SLOGAN#",$slogn_footer,$msg);	
            	/*New addition in welcome email ends here*/		
			$msg 	= $msg;
			if($reg['user_language'] == 5)
			{
				$from = array('kontakt@jiwok.pl' => 'Trener Jiwok');
			}
			else
			{
				$from = array('coach@jiwok.com' => 'Coach Jiwok');
			}
			if($reg['user_language'] == 1)
			{
				$path= './uploads/reg_attachment/english/';
			}
			if($reg['user_language'] == 2)
			{
				$path= './uploads/reg_attachment/french/';
			}
			if($reg['user_language'] == 3)
			{
				$path= './uploads/reg_attachment/spanish/';
			}
			if($reg['user_language'] == 4)
			{
				$path= './uploads/reg_attachment/italian/';
			}
			if($reg['user_language'] == 5)
			{
				$path= './uploads/reg_attachment/polish/';
			}
			$files_dir = scandir($path); // scan filed in particular dir
			foreach($files_dir as $fil)
			{
				if(is_file($path.$fil))
				{
					$tmp_file[] = $fil; 
				}
			}
			$emailTo = array($emailTo => '');
			$file_cnt = count($tmp_file);
			if($file_cnt >0)
			{
						$unid = md5(uniqid(time()));
						foreach($tmp_file as $filename)
						{
							$file = $path.$filename;
							$file_size = filesize($file);
							$handle = fopen($file, "r");
							$content = fread($handle, $file_size);
							fclose($handle);
							$content = chunk_split(base64_encode($content));
						}
						 $recipients = $sendg->send($subject,$from,$emailTo,$msg,$text='',$marathon='',$iso='',$file);
			}
			else
			{
				         $recipients = $sendg->send($subject,$from,$emailTo,$msg);
			}
	  	}
	  	///mail
		//array for login
		
		$insertData['login_date'] = date("Y-m-d H:i:s");
	 	$insertData['user_id']    = $nextId;
	 	$insertData['login_ip']   = $REMOTE_ADDR;
	 	$dbObj->_insertRecord("member_login",$insertData);
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
		$ticketEntry = $dbObj->_insertRecord("hdp_clients",$Ticket);
		$paymentTemp = array();
		$paymentTemp['user_id']		 		= $nextId;
		$paymentTemp['pay_id'] 				= $_SESSION['payment']['pay_id'];
		$paymentTemp['discUser_id'] 		= $_SESSION['payment']['discUser_id'];
		$paymentTemp['ActReffId'] 			= $_SESSION['payment']['ActReffId'];
		$paymentTemp['freedays'] 			= $_SESSION['payment']['freedays'];
		$paymentTemp['payFee'] 				= $_SESSION['payment']['payFee'];
		$paymentTemp['user_email'] 			= $_SESSION['login']['user_email'];
		$paymentTemp['pay_date']			= date('Y-m-d');
		statusRecord($_SESSION['login']['user_email'],'2nd step user_payment_temp insertion over','userreg2.php','0',$payFee,'7');
		$payEntry = $dbObj->_insertRecord("user_payment_temp",$paymentTemp);
		$_SESSION['user']= array(
								"userId"       => $nextId,
								"userType"     => $siteUsersConfig['MEMBER'],
								"user_email"    => $_SESSION['login']['user_email']
								);
		unset($_SESSION['admin_user']);
		unset($_SESSION['giftregcheck']);
		if(($chkins!="") && ($chkNewEntry!=""))
		{
			if($_SESSION['temp_user_id']!=""){
				$return_msg = $dbObj->_deleteData('user_temp','user_temp_id ='.$_SESSION['temp_user_id']);
			}
			statusRecord($_SESSION['login']['user_email'],'just before forum re direct','userreg2.php',$nextId,$payFee,'9');
            $_SESSION['login']['step2']='success';
          
			$addForum	=	register_forum($elmts); 
			if($_SESSION["regRedUrl"]!="")
			{  // Check whether any redirect url is set. This is for the user who clicks on the subscribe program button and then register		
				$redUrl	=	base64_decode($_SESSION["regRedUrl"]);
				$_SESSION["regRedUrl"]	=	"";
				//header("location:".$redUrl);
				header("location:userArea.php?origin=registration",true,301);
			}
			else
			{
				header("location:userArea.php?origin=registration",true,301);
			}
			exit;	
		}
		else
		{ 
			$errorMsginsert=1; 
		}
		exit;		
		//header("Location:payment1.php");
	}
}
else{
	if($_SESSION["RegMode"]=="fbLogin"){ //echo "here";print_r($_SESSION['fb_details']);exit; 
		$fbEMail	=	$_SESSION['fb_details']['email'];
		$fbGenere	=	$_SESSION['fb_details']['gender'];
		$fbDOB		=	$_SESSION['fb_details']['birthday'];
		$fbTimeZ	=	$_SESSION['fb_details']['timezone'];
		$dobArray	=	split("/",$fbDOB);
		$dobDay	=	$dobArray[1];
		$dobMnth	=	$dobArray[0];
		$dobYear	=	$dobArray[2];
		// Set Data to POST arry to fill the fields
		$_POST['user_fname']	=	$_SESSION['fb_details']['first_name'];
		$_POST['user_lname']	=	$_SESSION['fb_details']['last_name'];
		$_POST['user_day']	=	$dobDay;
		$_POST['user_month']	=	$dobMnth;
		$_POST['user_year']	=	$dobYear;
			if($fbGenere=="male"){
			$_POST['user_gender']	=	0;
		}else{
			$_POST['user_gender']	=	1;
		}
		$_POST['user_timezone']	=	$fbTimeZ;
		//var_dump($_POST);exit;
	}
}  
		$country_list	= $objMember->getCountryList($langName);
		$time_zones	= $objMember->getTimezoneList();
?>
<?php include("header.php"); ?>

<style>
	
.has-js .label_check, .has-js .label_radio ,.has-js label.r_on{  display:inline-block;background-position: 0 0px;}
label.error {
    float: none;
    color: #F00;
     font-size: 12px;
}
</style>
<script type="text/javascript" src="<?=ROOT_FOLDER?>resources/accordion/jquery-1.5.1.js"></script>
<script type="text/javascript" src="<?=ROOT_FOLDER?>includes/js/jquery.validate.js"></script>

<script type="text/javascript">
$(document).ready(function(){ 
$("#timelineModalBody").hide();
$("#timelineModalBody1").hide();
//~ $("#timelineModalBodyw").hide();
//~ $("#timelineModalBodywp").hide();
//~ $("#timelineModalBodyh").hide();
//~ $("#timelineModalBodyhp").hide();
$("#timelineModalBodyt").hide();
$("#timelineModalBodytp").hide();
    $("#regFrm2").validate(
	{  
		messages: {     	
		user_fname: {
       required: "<br /><?=$parObj->_getLabenames($dataError,'fname','name');?>",
      
     },
	 user_lname: {
       required: "<br /><?=$parObj->_getLabenames($dataError,'lname','name');?>",
     
     },
     user_day: {
       required: "<br /><?=$parObj->_getLabenames($dataError,'dob','name');?>",
     
     },
       user_month: {
       required: "<br /><?=$parObj->_getLabenames($dataError,'dob','name');?>",
     
     },
       user_year: {
       required: "<br /><?=$parObj->_getLabenames($dataError,'dob','name');?>",
     
     },
     user_weight_value: {
       required: "<br /><?=$parObj->_getLabenames($dataError,'weighterr','name');?>",
     
     },
     user_height_value: {
       required: "<br /><?=$parObj->_getLabenames($dataError,'heighterr','name');?>",
     
     },
    user_terms: {
       required: "<br /><?=$parObj->_getLabenames($dataError,'terms','name');?>",
     
     }
   
    
   }
	}
	);
	
  });
  	function validate1()
      {//alert(document.regFrm2.user_day.value);alert(document.regFrm2.user_month.value);alert(document.regFrm2.user_year.value);
		  var chk =0;
      var tt='<?php echo $_SESSION['language']['langId']; ?>';
      
      
      
      
      
			 //~ if( document.regFrm2.user_weight_value.value =='')
			 //~ {
				 //~ if(tt =='2')
				 //~ { $("#timelineModalBodyw").show();return false;
           //~ 
				   //~ 
				 //~ }
				//~ else if(tt =='5')
				 //~ { $("#timelineModalBodywp").show();return false;
           //~ 
				   //~ 
				 //~ } 
					//~ 
			 //~ }
			 //~ else if( document.regFrm2.user_weight_value.value != '')
			//~ { 	 
				 //~ if(tt =='2')
				//~ { 
					//~ $("#timelineModalBodyw").hide();
				 //~ }
				//~ else if(tt =='5')
				//~ { 
					//~ $("#timelineModalBodywp").hide();
				//~ } 	 
			 //~ }
			 
			 
			 
			 
			 
	 //~ if( document.regFrm2.user_height_value.value =='')
     //~ {
		 //~ if(tt =='2')
         //~ { $("#timelineModalBodyh").show();
           //~ 
           //~ 
         //~ }
        //~ else if(tt =='5')
         //~ { $("#timelineModalBodyhp").show();
           //~ 
           //~ 
         //~ } 
	 //~ }
	 //~ 
	 //~ 
	     //~ if( document.regFrm2.user_height_value.value !='')
      //~ {
		 //~ if(tt =='2')
         //~ { $("#timelineModalBodyh").hide();
           //~ 
         //~ }
        //~ else if(tt =='5')
         //~ { $("#timelineModalBodyhp").hide();
           //~ 
         //~ }  
	 //~ }
         if( (document.regFrm2.user_day.value == "0" )  || (document.regFrm2.user_month.value == "0" ) ||(document.regFrm2.user_year.value == "0" ) )
         { 
         if(tt =='2')
         { var chk =1;
			 $("#timelineModalBody").show();  
         }
         else if((tt =='2') && (document.regFrm2.user_terms.checked))
         { $("#timelineModalBody").show();return false;
          
         }
        else if(tt =='5')
         { $("#timelineModalBody1").show();
           // return false;
         }
	 }  
	  if( (document.regFrm2.user_day.value != "0" )  && (document.regFrm2.user_month.value != "0" ) && (document.regFrm2.user_year.value != "0" ) )
         {
         if(tt =='2')
         { $("#timelineModalBody").hide();
           // return false;
         }
        else if(tt =='5')
         { $("#timelineModalBody1").hide();
            //return false;
         }
	 }
	  if( !document.regFrm2.user_terms.checked)
     { 
		 if(tt =='2')
         { var chk =2;
			 $("#timelineModalBodyt").show();
           
         }
        else if(tt =='5')
         { $("#timelineModalBodytp").show();return false;
           
         } 
	 }  
	 if( document.regFrm2.user_terms.checked)
     { 
		 if(tt =='2')
         { $("#timelineModalBodyt").hide();
           
         }
        else if(tt =='5')
         { $("#timelineModalBodytp").hide();
           
         } 
	 }  
if( (chk ==1)||(chk ==2))
{
	return false;
}
	 }
  </script>
<div class="frame2">
<ul class="steps">
   <li>1</li>
   <li class="active">2</li>
</ul>
    <div class="heading">
    <?=$parObj->_getLabenames($arrayData,'identify','name');?>
    </div>
    <p align="center" class="txt-ylew"><?=$parObj->_getLabenames($arrayData,'signupdesc','name');?></p>
<form name="regFrm2" action="userreg2.php" method="post" enctype="multipart/form-data" id="regFrm2" onsubmit="return(validate1());">
<section class="reg-form contact">
       <div class="colum ">
             <div class="fields">
                  <div <? if($registration->err1 == 1){ ?> class="rows error"<? }?>>
					<div class="label"><span>*</span> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'fname','name'),'UTF-8');?></div>
                     <div class="colums"><input name="user_fname" type="text" value="<?=stripslashes($_POST['user_fname'])?>" class="tfl required "></div>
                     <? if($registration->err1 == 1){ ?>
                        <img src="<?=ROOT_FOLDER?>images/help1.jpg" class="Q-mark"  alt="help" onMouseOver="tooltip('<?=$registration->fname?>');" onMouseOut="exit();">
                      <? }?>
<!--
                     nn <img class="Q-mark" onmouseout="exit();" onmouseover="tooltip('Prénom nécessaire');" alt="help" src="images/help1.jpg">
-->
                  </div>
                  <div <? if($registration->err2 == 1){ ?>class="rows error"<? }?>>
						 <div class="label"><span>*</span><?=mb_strtoupper($parObj->_getLabenames($arrayData,'lname','name'),'UTF-8');?></div>
						 <div class="colums"><input name="user_lname" type="text" value="<?=stripslashes($_POST['user_lname'])?>" class="tfl required "></div>
					  <?if($registration->err2 == 1){ ?>
						   <img src="<?=ROOT_FOLDER?>images/help1.jpg" class="Q-mark" alt="help"  onMouseOver="tooltip('<?=$registration->lname?>');" onMouseOut="exit();">
						  <? }?>
                  </div>
                  
                   <div <? if($registration->err3 == 1){ ?>class="rows error"<? }?>>
                      <div class="label"><span>*</span><?=mb_strtoupper($parObj->_getLabenames($arrayData,'gender','name'),'UTF-8');?></div>
                      <div class="colums">
                      <label class="label_radio" for="male">
                      <input  id="male" type="radio" name="user_gender"  <?php if($_POST['user_gender'] == 0) { echo 'checked'; } ?> value="0" />
                      <?=mb_strtoupper($parObj->_getLabenames($arrayData,'man','name'),'UTF-8');?>
                      </label>
                      <label class="label_radio" for="female">
                      <input id="female" type="radio" name="user_gender" <?php if($_POST['user_gender'] == 1)
                      { echo 'checked'; } ?> value="1" /><?=mb_strtoupper($parObj->_getLabenames($arrayData,'woman','name'),'UTF-8');?>
                      </label>
                  </div>
                   <? if($registration->err3 == 1){ ?>
		               <img src="<?=ROOT_FOLDER?>images/help1.jpg" class="Q-mark" alt="help"  onMouseOver="tooltip('<?=$registration->gender?>');" onMouseOut="exit();">
                   <? }?>
                  </div>
                 <!--date section starts -->
                
                  <div <? if($registration->err4 == 1){ ?>  class="rows error" <? }?>>
                     <div class="label"><span>*</span><?=mb_strtoupper($parObj->_getLabenames($arrayData,'dob','name'),'UTF-8');?></div>
                     <!--day-->
                     
                     <div class="colums"> 
						 <div class="tfl_small4 required email">
                       <select name="user_day" class="">
                         <option value="0" selected="selected"><?=$parObj->_getLabenames($arrayData,'day','name');?></option>
							 <?
							for($i=1; $i<=31; $i++){
								$string = "<option value={$i}";
								if($i == $_POST['user_day']){
									$string .= " selected";
								}
								$string .= ">{$i}</option>";
								echo $string;
							}
							?>
                       </select></div>
                       <!--day-->
                    <!--month-->
                    <div class="tfl_small4 required email">
                       <select name="user_month" class="">
                         <option value="0" selected="selected"><?=$parObj->_getLabenames($arrayData,'month','name');?></option>
						   <?
								for($i=1; $i<=count($siteMonthList); $i++){
									$string = "<option value={$i}";
									if($i == $_POST['user_month']){
										$string .= " selected";
									}
									$string .= ">";
									if($lanId==1)
									 $string .=$siteMonthList[$i];
									elseif($lanId==2)
									{
										$fPattern2 	 = array('/�/','/�/','/�/','/�/','/�/','/�/','/�/','/�/','/�/','/�/','/�/','/�/','/�/','/�/','/�/','/�/','/�/','/�/','/�/','/�/','/�/','/�/','/�/', '/�/','/�/','/�/','/�/','/�/');
										$fReplace2 	 = array('a','e','A','E','e','E','a','e','i','o','u','A','E','I','O','U','a','e','i','o','u','A','E','I','O','U','c','C');
										$newMonth = preg_replace($fPattern2,$fReplace2,$monthArray[$siteMonthList[$i]]);
										$string .=$newMonth; 
									  }
									  elseif($lanId==5)
									  {
										 $string .=$monthArray_PL[$siteMonthList[$i]];
									  }
									$string .="</option>";
									echo $string;
								}
							?>
                       </select></div>
                      <!--month-->
                      <!--yr-->
                      <div class="tfl_small4 required email">
                        <select name="user_year" class="">
                        <option value="0" selected="selected"><?=$parObj->_getLabenames($arrayData,'year','name');?></option>
						   <?
								for($i=date('Y')-1; $i>=1900; $i--){
								$string = "<option value={$i}";
								if($i == $_POST['user_year']){
									$string .= " selected";
								}
								$string .= ">{$i}</option>";
								echo $string;
								}
							?>
                       </select></div>
                       <!--yr-->
                        <br><br><br><span id ="timelineModalBody" style = "color: #F00 ;font-size: 100%;text-rendering: optimizelegibility;" class="error">Votre date de naissance est nécessaire pour les entraînements</span>
<span id ="timelineModalBody1" style = "color: #F00; 
font-size: 100%;

text-rendering: optimizelegibility;" class="error">Wpisz swoją datę urodzenia</span>
                     </div>
                      <? if($registration->err4 == 1){ ?>
		                <img src="<?=ROOT_FOLDER?>images/help1.jpg" class="Q-mark" alt="help"  onMouseOver="tooltip('<?=$registration->dob?>');" onMouseOut="exit();">
                      <? }?>
                  </div>
                
                   <!--date section ends -->
                   <!--pays -->
                  <div <? if($registration->err10 == 1){ ?> class="rows error"<? }?>>
                     <div class="label"><span>*</span><?=mb_strtoupper($parObj->_getLabenames($arrayData,'country','name'),'UTF-8');?></div>
                     <div class="colums">
						 <div class="selet3 required email">
                       <select name="user_country" id="user_country" >
						   <?php foreach($country_list as $country_data){ ?>
                         <option value="<?php echo $country_data['countries_id']; ?>" <? if($_POST['user_country']== $country_data['countries_id']) print 'selected'; if($_POST['user_country']=='' && strtolower($country_data['countries_name'])=='france') print 'selected';?>><?php echo $country_data['countries_name'];?></option><?php } ?>  

                       </select></div>
                     </div>
                   <? if($registration->err10 == 1){ ?>
		                <img src="<?=ROOT_FOLDER?>images/help1.jpg" class="Q-mark" alt="help"  onMouseOver="tooltip('<?=$registration->country_err_msg?>');" onMouseOut="exit();">
                      <? }?>
                      </div>
                  <!--pays -->
                  </div>
                 <!-- fields class ends here -->
              <div class="label ylw"> *<?=mb_strtoupper($parObj->_getLabenames($arrayData,'require','name'),'UTF-8');?></div>
             </div>
              <!-- colum class ends here -->
        <div class="colum">
             <div class="fields rit">
								  <div <? if($registration->err22 == 1){ ?> class="rows error" <? }?>>
									 <div class="label"><span>*</span><?=mb_strtoupper($parObj->_getLabenames($arrayData,'timezone','name'),'UTF-8');?></div>
									 <div class="colums"><div class="selet3"><select name="user_timezone" id="user_timezone">
									<?php foreach($time_zones as $time_zone){ ?>
					                <option value="<?php echo $time_zone['time_tz']; ?>" <? if($_POST['user_timezone']== $time_zone['time_tz']){ print 'selected';}elseif(trim($_SESSION["usrTimeZone"])!='' && trim($_SESSION["usrTimeZone"])==$time_zone['time_tz']){print 'selected';}?>><?php if($lanId==2){echo $time_zone['gmt_timezone'];}else{echo $time_zone['time_name'];}?></option><?php } ?>
			                        </select></div>
									 </div>
									<? if($registration->err22 == 1){ ?>
		        <img src="<?=ROOT_FOLDER?>images/help1.jpg" class="Q-mark" alt="help"  onMouseOver="tooltip('<?=$registration->country_err_msg?>');" onMouseOut="exit();">
                      <? }?> </div>
                  <div <?php if($registration->err11 == 1){ ?> class="rows error"<? }?>>
                     <div class="label"><span>*</span><?=mb_strtoupper($parObj->_getLabenames($arrayData,'language','name'),'UTF-8');?></div>
                     <div class="colums"><div class="selet3">
                      <select name="user_language" id="user_language">
						<?
						foreach($siteLanguagesConfig as $key=>$data){ 
						if($langName==strtolower($data)) { ?>
						<option value="<?=$key?>" <? if($_POST['user_language']==$key) print 'selected'; if($_POST['user_language']=='' && $langName==strtolower($data)) print 'selected';?>><?=$parObj->_getLabenames($arrayData,strtolower($data),'name');?>
						</option>
						 <? 
						}
						} ?> 
						</select></div>
                       </div>
                      <? if($registration->err11 == 1){ ?>
		 <img src="<?=ROOT_FOLDER?>images/help1.jpg" class="Q-mark" alt="help" onMouseOver="tooltip('<?=$registration->language?>');" onMouseOut="exit();">
                      <? }?>
                  </div>
                 <div <? if($registration->err14 == 1){ ?> class="rows error"<? }?>>
                     <div class="label"><span>*</span><?=mb_strtoupper($parObj->_getLabenames($arrayData,'weight','name'),'UTF-8');?></div>
                     <div class="colums "><div class="div-left " style="width: 50%;"><input name="user_weight_value" type="text" class=" required tfl_small  " value="<?=stripslashes($_POST['user_weight_value'])?>">
<span class="required"></span>                    </div><div class="tfl_small4" style="width: 45%;"><select name="user_weight_unit" class=""> <? foreach($weightUnits as $key => $data)

				{	?>
                <option value="<?=$key?>" <? if($_POST['user_weight_unit'] == $key) echo 'selected';if(trim($_POST['user_weight_unit']) == '' && trim($data)=="kg") echo 'selected';?>><?=$data?></option>

                <?	} ?>
                      </select></div></div>
                <? if($registration->err14 == 1){ ?>
		              <img src="<?=ROOT_FOLDER?>images/help1.jpg" class="Q-mark" alt="help"  onMouseOver="tooltip('<?=$registration->weight?>');" onMouseOut="exit();">
                 <? }?>
                 </div>
<!--
                 <span id ="timelineModalBodyw" class="timelineModalBodywp-art">Entrez un poids valide</span>
<span id ="timelineModalBodywp" class="timelineModalBodywp-art">Wprowadź aktualną wagę</span>
-->
                 <div <? if($registration->err15 == 1){ ?> class="rows error"<? }?>>
                 <div class="label"><span>*</span><?=mb_strtoupper($parObj->_getLabenames($arrayData,'height','name'),'UTF-8');?></div>
                 <div class="colums"><div class="div-left"  style="width: 50%;"><input name="user_height_value" type="text" class=" required tfl_small " value="<?=stripslashes($_POST['user_height_value'])?>" > 
               </div> <div class="tfl_small4" style="width: 45%;"><select name="user_height_unit" class="">   <?	foreach($heightUnits as $key => $data)
			    	{  ?>
                    <option value="<?=$key?>" <? if($_POST['user_height_unit'] == $key) echo 'selected';if(trim($_POST['user_height_unit']) == '' && trim($data)=="cm") echo 'selected';?>><?=$data?></option>
                <?	}?>
				</select></div>
				 </div>
				    <? if($registration->err15 == 1){ ?>
		              <img src="<?=ROOT_FOLDER?>images/help1.jpg" class="Q-mark" alt="help" onMouseOver='tooltip("<?=$registration->height?>");' onMouseOut="exit();">
                    <? }?>
<!--
                    <span id ="timelineModalBodyh" class="timelineModalBodywp-art">Hauteur nécessaire</span><span id ="timelineModalBodyhp" 
class="timelineModalBodywp-art">Wpisz swoją wysokość ciała</span>
-->
                  </div>
                  <div class="chcks newchkbx">
                     <!--<label class="label_check" for="user_newsletter1" >-->
                         <input name="user_newsletter" id ="user_newsletter1" value="1" type="checkbox" /> 
                         <label for="user_newsletter1"><span></span></label>
                     <!--</label>-->
                    <span class="content-span">
						 <?=mb_strtoupper($parObj->_getLabenames($arrayData,'newsletterNotify','name'),'UTF-8');?>
                     </span>
                  </div>

                  <div   <?php if($registration->err20 == 1){ ?> class="chcks rows error newchkbx " style="padding-left:8%;" <? }else{ ?> class="chcks newchkbx" <?php } ?>>
                      <!--<label class="label_check" for="user_terms1"   class="required">-->

                        <input name="user_terms"  id="user_terms1" value="1"  type="checkbox" <? if($_POST['user_terms'] == 1){ echo 'checked'; } ?> /> 

                      <!--</label>-->
                 		<label for="user_terms1"><span></span></label>
                      <span>
						       <?=mb_strtoupper($parObj->_getLabenames($arrayData,'terms1','name'),'UTF-8');?>
                        <?php if($lanId !=5){ ?>
                               <?=mb_strtoupper($parObj->_getLabenames($arrayData,'terms','name'),'UTF-8');?>
                        <?php }
                          else{?> 
							   <?= $parObj->_getLabenames($arrayData,'terms1','name');?>
							   <a href="<?=ROOT_JWPATH?>terms+and+conditions+general"> 
							   <?php echo $parObj->_getLabenames($arrayData,'terms2','name');?></a>, <a href="<?=ROOT_JWPATH?>terms+and+conditions+web"> <?php echo $parObj->_getLabenames($arrayData,'terms3','name');?></a> <a href="<?=ROOT_JWPATH?>terms+and+conditions+services"><?= $parObj->_getLabenames($arrayData,'terms4','name');?>
							   </a>
	                           <br/><br/>
	                           <?= $parObj->_getLabenames($arrayData,'terms6','name');?><?php 
	                     }?>
                      </span>
                       <? if($registration->err20 == 1){  ?>
		               <img src="<?=ROOT_FOLDER?>images/help1.jpg" class="Q-mark" alt="help" onMouseOver="tooltip('<?php echo addslashes($registration->terms); ?>');" onMouseOut="exit();">
                      <? }?>
                   </div>
                  <span id ="timelineModalBodyt" style="color:#F00; font-size: 100%; text-align:left; width:100% !important;">Veuillez s'il vous plaît accepter les CGU de Jiwok</span><span id ="timelineModalBodytp" 
style="color:#F00; font-size: 100%; text-align:left; width:100% !important;">Zaakceptuj proszę Warunki Użytkowania serwisu Jiwok</span>
                   <div>
                     <div class="label">&nbsp;</div>
                     <div class="colums"><input type="submit" name="submit" value="<?=$parObj->_getLabenames($arrayData,'submitButtonSecond','name');?>" class="sub-btn"  />
                     <input type="hidden" name="t" value="<?php echo $_REQUEST['t']; ?>" />
                    
                  </div>
                  </div>
                  </div>
               </div>
</section>
</form>
</div>
<script>
    var d = document;
    var safari = (navigator.userAgent.toLowerCase().indexOf('safari') != -1) ? true : false;
    var gebtn = function(parEl,child) { return parEl.getElementsByTagName(child); };
    onload = function() {
        var body = gebtn(d,'body')[0];
        body.className = body.className && body.className != '' ? body.className + ' has-js' : 'has-js';
        if (!d.getElementById || !d.createTextNode) return;
        var ls = gebtn(d,'label');
        for (var i = 0; i < ls.length; i++) {
            var l = ls[i];
            if(l.className.indexOf('label_') == -1) continue;
            var inp = gebtn(l,'input')[0];
            if(l.className == 'label_check'){
				l.className = (safari && inp.checked == true || inp.checked) ? 'label_check c_on' : 'label_check c_off';
                l.onclick   = check_it;
            };
            if (l.className == 'label_radio') {
                l.className = (safari && inp.checked == true || inp.checked) ? 'label_radio r_on' : 'label_radio r_off';
                l.onclick = turn_radio;
            };
        };
    };
    var check_it = function(){
        var inp  = gebtn(this,'input')[0];
        if(this.className == 'label_check c_off' || (!safari && inp.checked)) {
           this.className =  'label_check c_on';
            if (safari) inp.click();
        }else{
            this.className = 'label_check c_off';
            if (safari) inp.click();
        };
    };
    var turn_radio = function() {
		
        var inp = gebtn(this,'input')[0];
       
        if (this.className == 'label_radio r_off' || inp.checked) {
            var ls = gebtn(this.parentNode,'label');
            for (var i = 0; i < ls.length; i++) {
                var l = ls[i];
                if (l.className.indexOf('label_radio') == -1)  continue;
                l.className = 'label_radio r_off';
            };
            this.className = 'label_radio r_on';
            if (safari) inp.click();
        } else {
            this.className = 'label_radio r_off';
            if (safari) inp.click();
        };
    };

	//~ if(jQuery('#user_terms1').is(':checked') || (jQuery('div.chcks label').hasClass('c_on'))){
	//~ this.className =  'label_check c_on';
	//~ }else{  this.className = 'label_check c_off';
	//~ }
	//~ if(jQuery('#user_newsletter1').is(':checked') || (jQuery('div.chcks label').hasClass('c_on'))){
	//~ this.className =  'label_check c_on';
	//~ }else{  this.className = 'label_check c_off';
	//~ }
</script>
<?php include("footer.php"); ?>
