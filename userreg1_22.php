<?php 
//~ include("session_local.php"); 
//~ if(session_id() == '') {
    //~ session_start();
//}
session_start();
/*
ini_set('display_errors',1);
error_reporting(E_ERROR | E_PARSE);
*/
include_once('includeconfig.php');
include_once('regDetail.php');
include_once('includes/classes/class.member.php');
include_once('includes/classes/class.registration.php');
$parObj 		=   new Contents('userreg1.php');
$objGen   		=	new General();
$dbObj     		=   new DbAction();
$objMember		= 	new Member($lanId);
$registration	= 	new registration();

unset($_SESSION['registration']);
unset($_SESSION['login']);
unset($_SESSION['giftregcheck']);

if(isset($_SESSION['user']['userId'])){
	header("location:myprofile.php",true,301);
	exit;
}
if($_REQUEST['returnUrl']){
	$urlParams			    =	"?returnUrl=".$_REQUEST['returnUrl'];
	$_SESSION["regRedUrl"]	=	$_REQUEST['returnUrl'];
}else{
	$_SESSION["regRedUrl"]	=	"";
}
if(isset($_REQUEST["referrer"]) && isset($_REQUEST["media"])){//Checking if a referrel
	//Yes it is a referrel
	$referalCheck 	=	$_REQUEST["referrer"];
	$media 			=	$_REQUEST["media"];
	if(($media == 'fb') || ($media == 'tw')){
			$field = "jiwok_share_social";
	}else{
		if($media != 'mail')
			header("Location:userreg1.php",true,301);
		else
			$field = "jiwok_share_email";
	}
	$selectQry		=	"SELECT * FROM ".$field." WHERE referral_secret_token = '".$referalCheck."'";
	$secret_tocken	= $GLOBALS['db']->getRow($selectQry,DB_FETCHMODE_ASSOC);
	if(count($secret_tocken) != 0){			
		$_SESSION['referrarId'] = $secret_tocken['user_id'];
		$_SESSION['medium'] = $media;
	}else
		header("Location:userreg1.php",true,301);

}
//For referral system starts
/*****************************************************************************
* Check whether the user is came through a referral
* ***************************************************************************/	
//Referral system ends
if($lanId=="") $lanId=1;
$_SESSION["usrTimeZone"]	=	$_REQUEST['t'];
$returnData		= 	$parObj->_getTagcontents($xmlPath,'registrationUser','label');   // Get content from XML
$arrayData		= 	$returnData['general'];
$returnError	=	$parObj->_getTagcontents($xmlPath,'registrationUser','messages');
$dataError		=	$returnError['errorMessage'];
if(isset($_POST['submit'])){
	
	$registration->validateRegistration($parObj,$xmlPath);//echo"kk";echo ($registration->errorMsg);exit;
	if($registration->errorMsg==0){
		$registration->processRegistration();
		header("Location:userreg2.php",true,301);
		statusRecord($registration->elmts['user_email'],'1st step over','userreg1.php','0','0','1');
		exit;	
	}
}
 //need to change
if(1){
	$fbMode	=	"registration";
	include("fbLoginInc.php"); // For Facebook Login
}
?>
<?php include("header.php"); ?>
<?php /*?><script type="text/javascript" src="<?=ROOT_FOLDER?>includes/js/jquery.validate.js"></script><?php */?>
<script type="text/javascript" src="<?=ROOT_FOLDER?>includes/js/jquery.validate.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $("#regForm").validate( 
	{
		messages: {     	
		user_email: {
       required: "<br /><?=$parObj->_getLabenames($dataError,'noemail','name');?>",
       email: "<br /><?=$parObj->_getLabenames($dataError,'emailerr','name');?>"
     },
	 c_user_email: {
       required: "<br /><?=$parObj->_getLabenames($dataError,'noemail','name');?>",
       email: "<br /><?=$parObj->_getLabenames($dataError,'emailerr','name');?>"
     }
   }
	}
	);
  });
function getOffset(){ 
	var d = new Date()
    var gmtHours = -d.getTimezoneOffset()/60;
	document.getElementById("timeZoneId").value=gmtHours;
}
</script>
<div class="frame2">
<ul class="steps">
   <li class="active">1</li>
   <li>2</li>
</ul>
    <div class="heading">
   <?=$parObj->_getLabenames($arrayData,'identify','name');?>
    </div>
    <p align="center" class="txt-ylew subtitle"><?=$parObj->_getLabenames($arrayData,'signupdesc','name');?></p>

<section class="reg-form">
	 <form name="regForm" action="userreg1.php<?php echo $urlParams; ?>" method="post" id="regForm">
	
	<?php
    if($lanId	==	5)
    {?>
  	  <input type="hidden" name="t" value="1" />
     <?php
    }
    else
    {?>
  	 <input type="hidden" id="timeZoneId" name="t" />
     <?php
    }?>
  
    <div class="colum">
             <div class="fields">
						  <div <? if($registration->err1 == 1){ ?> class="rows error"<? }?>>
							 <div class="label"><span>*</span> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'email','name'),'UTF-8');?></div>
							 <div class="colums"><input type="text" name="user_email" id="textfieldLtest1" class="required email tfl "  value="<?=stripslashes($_POST['user_email'])?>"></div>
							   <? if($registration->err1 == 1){ ?>
								<img src="<?=ROOT_FOLDER?>images/help1.jpg" class="Q-mark"  alt="help" onMouseOver="tooltip('<?=$registration->email1?>');" onMouseOut="exit();">
							  <? }?>
						  </div>
                  
											  <div <? if($registration->err2 == 1){ ?> class="rows error"<? }?>>
												 <div class="label"><span>*</span><?=mb_strtoupper($parObj->_getLabenames($arrayData,'cemail','name'),'UTF-8');?></div>
												 <div class="colums"><input type="text" name="c_user_email" id="textfieldLtest2" class="tfl required email" value="<?=stripslashes($_POST['c_user_email'])?>"></div>
													<? if($registration->err2 == 1){ ?>
													<img src="<?=ROOT_FOLDER?>images/help1.jpg" class="Q-mark"  alt="help" onMouseOver="tooltip('<?=$registration->email2?>');" onMouseOut="exit();">
													<? }?>
											  </div>
                  
                  <div <? if($registration->pas1 == 1){ ?> class="rows error"<? }?>>
                     <div class="label"><span>**</span><?=mb_strtoupper($parObj->_getLabenames($arrayData,'password','name'),'UTF-8');?></div>
                     <div class="colums"><input type="password" name="password" id="textfield3"  class="tfl"  value="<?=$_POST['password']?>"></div>
                      <? if($registration->pas1 == 1){ ?>
                        <img src="<?=ROOT_FOLDER?>images/help1.jpg" class="Q-mark"  alt="help" onMouseOver="tooltip('<?=$registration->pass1?>');" onMouseOut="exit();">
                        <? }?>
                  </div>
                  </div>
									  <div class="label ylw"> *<?=mb_strtoupper($parObj->_getLabenames($arrayData,'require','name'),'UTF-8');?><br>
																   
															   ** <?=mb_strtoupper($parObj->_getLabenames($arrayData,'warningMsg','name'),'UTF-8');?><br>
																   
										  
											 
							</div>
             </div>
       <div class="right">
		   
		<?php if(1){
        include("fbLogin.php");
         }else{
         ?>
        <input type="submit" name="submit" value="<?=$parObj->_getLabenames($arrayData,'newButtonName','name');?>" class="sub-btn"  />
        <?php } ?>
    
<br/>
 <?php if(1){ ?>
		
		<input type="submit" name="submit" value="<?=mb_strtoupper($parObj->_getLabenames($arrayData,'newButtonName','name'),'UTF-8');?>" class="sub-btn" id="newSubBtn" <?php if($lanId	==	5) echo "style='background-position:10px -216px;'";?>/>
        
		<?php 
		} ?>

       </div>
       </form>
</section>
</div>
<script type="text/javascript">
	getOffset();
</script>
</html>
<?php //include("footer.php"); ?>
