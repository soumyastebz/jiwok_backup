<?php
session_start();

/*
ini_set('display_errors',1);
error_reporting(E_ERROR | E_PARSE);
*/
include_once('includeconfig.php');
include_once('regDetail.php');
include_once('includes/classes/class.member.php');
include_once('includes/classes/class.registration.php');

$parObj 		=   new Contents('userreg1_jiwok.php');
$objGen   		=	new General();
$dbObj     		=   new DbAction();
$objMember		= 	new Member($lanId);
$registration		= 	new registration();


unset($_SESSION['registration']);
unset($_SESSION['login']);
unset($_SESSION['giftregcheck']);


if(isset($_SESSION['user']['userId'])){
	header("location:myprofile.php",true,301);
	exit;
}

if($_REQUEST['returnUrl']){
	$urlParams			=	"?returnUrl=".$_REQUEST['returnUrl'];
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
			header("Location:userreg1_jiwok.php",true,301);
		else
			$field = "jiwok_share_email";
	}
	$selectQry		=	"SELECT * FROM ".$field." WHERE referral_secret_token = '".$referalCheck."'";
	$secret_tocken	= $GLOBALS['db']->getRow($selectQry,DB_FETCHMODE_ASSOC);
	if(count($secret_tocken) != 0){			
		$_SESSION['referrarId'] = $secret_tocken['user_id'];
		$_SESSION['medium'] = $media;
	}else
		header("Location:userreg1_jiwok.php",true,301);

}


//For referral system starts
/*****************************************************************************
* @author 	: 	Dileep E
* Date 		:	29-10-2011
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
	$registration->validateRegistration($parObj,$xmlPath);
	if($registration->errorMsg==0){
		$registration->processRegistration();
		header("Location:userreg2.php",true,301);
		statusRecord($registration->elmts['user_email'],'1st step over','userreg1_jiwok.php','0','0','1');
		exit;	
	}
}
if(1){
	$fbMode	=	"registration";
	include("fbLoginInc.php"); // For Facebook Login
}

?>
<?php include("header.php"); ?>
<style type="text/css">
* { font-family: Verdana; font-size: 96%; }
label { width: 10em; float: left; }
label.error { float: none; color: red; padding-left: .5em; vertical-align: top; width:187px;}
p { clear: both; }
.submit { margin-left: 12em; }
em { font-weight: bold; padding-right: 1em; vertical-align: top; }
#newSubBtn{
	margin-left: 204px;
    margin-top: 10px;
	float:none !important;
}
.register_wraper .right_col {
    float: left !important;
    height: 124px !important;
    padding-left: 20px !important;
    padding-top: 6px !important;
    position: relative !important;
    width: 448px !important;
}
.description {
    color: #057498;
    font: bold 12px Arial,"Arial Narrow","Arial Rounded MT Bold";
 	padding-bottom: 20px;
}
.numberHolder {
    float: left;
    margin: 12px 0 12px 22px;
    width: 200px;
}
.numberActive {
    background-color: #FF9712;
    color: #FFFFFF;
    float: left;
    font-family: Georgia,"Times New Roman",Times,serif;
    font-size: 30px;
    font-weight: bold;
    height: 48px;
    margin: 0 1px 0 0;
    padding: 10px 0 0;
    text-align: center;
    width: 58px;
}
.numberDisable {
    background-color: #CCC;
    color: #FFFFFF;
    float: left;
    font-family: Georgia,"Times New Roman",Times,serif;
    font-size: 30px;
    font-weight: bold;
    height: 48px;
    margin: 0 1px 0 0;
    padding: 10px 0 0;
    text-align: center;
    width: 58px;
}
</style>
<script type="text/javascript" src="<?=ROOT_FOLDER?>includes/js/jquery.validate.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $("#regForm").validate( 
	{
		messages: {     	
		user_email: {
       required: "<br />",
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
<div class="register_heding">
<div class="heading"><?=$parObj->_getLabenames($arrayData,'signUpHead','name');?></div>

 <div class="clear"></div>
</div>
<div class="register_wraper">
<div class="numberHolder">
	<div class="numberActive">1</div>
    <div class="numberDisable">2</div>
</div>
<div class="clear"></div>
<div class="description"><?=$parObj->_getLabenames($arrayData,'signupdesc','name');?></div>
  <h2 class="hed-1"><?=$parObj->_getLabenames($arrayData,'identify','name');?></h2>
  <form name="regForm" action="userreg1_jiwok.php<?php echo $urlParams; ?>" method="post" id="regForm">
  <!--<input type="hidden" id="timeZoneId" name="t" />-->
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
  
  <table width="400" border="0" cellspacing="0" cellpadding="0" class="table-1">
  <?php if($registration->errorMsg == 1){ ?>
  <tr>
  <td colspan="3"><div class="instructions" ><?=$parObj->_getLabenames($errorData,'errtitle','name');?></div></td>
  </tr>
  <?php }?>
  <tr <?php if($registration->err1 == 1){ ?> class="errorMessageBg"<?php }?>>
    <td align="right" ><span>*</span><?=$parObj->_getLabenames($arrayData,'email','name');?></td>
    <td><input type="text" name="user_email" id="textfieldLtest1"  class="required email" value="<?=stripslashes($_POST['user_email'])?>" />
	</td>
	
  </tr>
  <tr <?php if($registration->err2 == 1){ ?> class="errorMessageBg"<?php }?>>
    <td align="right"><span>*</span><?=$parObj->_getLabenames($arrayData,'cemail','name');?></td>
    <td><input type="text" name="c_user_email" id="textfieldLtest2"  class="required email" value="<?=stripslashes($_POST['c_user_email'])?>" /></td>
	<?php if($registration->err2 == 1){ ?>
	<td>
		<span class="errorMessageCommon" onMouseOver="tooltip('<?=$registration->email2?>');" onMouseOut="exit();">[?]</span>
	</td>
	<?php }?>
  </tr>
  <tr <?php if($registration->pas1 == 1){ ?> class="errorMessageBg"<?php }?>>
    <td align="right" ><span>*</span><?=$parObj->_getLabenames($arrayData,'password','name');?></td>
    <td><input type="password" name="password" id="textfield3"  class="tfl-1" value="<?=$_POST['password']?>" /></td>
	<?php if($registration->pas1 == 1){ ?>
	<td>
		<span class="errorMessageCommon" onMouseOver="tooltip('<?=$registration->pass1?>');" onMouseOut="exit();">[?]</span>
	</td>
	<?php }?>
  </tr>
  <tr <?php if($registration->pas2 == 1){ ?> class="errorMessageBg"<?php }?> style="display:none;">
    <td align="right" ><span>*</span><?=$parObj->_getLabenames($arrayData,'cpassword','name');?></td>
    <td><input type="password" name="c_password" id="textfield4"  class="tfl-1" value="<?=$_POST['c_password']?>" /></td>
	<?php if($registration->pas2 == 1){ ?>
	<td>
		<span class="errorMessageCommon" onMouseOver="tooltip('<?=$registration->pass2?>');" onMouseOut="exit();">[?]</span>
	</td>
	<?php }?>
  </tr>
</table>
<div class="right_col">
	<?php
    if(1){
        include("fbLogin.php");
    }else{
    ?>
        <span class="orange">* <?=$parObj->_getLabenames($arrayData,'require','name');?></span><br/>
        <span class="orange">* <?=$parObj->_getLabenames($arrayData,'warningMsg','name');?></span><br />
        <input type="submit" name="submit" value="<?=$parObj->_getLabenames($arrayData,'newButtonName','name');?>" class="regSubButton"  />
    <?php } ?>
</div>

<div class="clear"></div>

<?php if(1){ ?>
<div>
<span class="orange">* <?=$parObj->_getLabenames($arrayData,'require','name');?></span><br/>
<span class="orange">* <?=$parObj->_getLabenames($arrayData,'warningMsg','name');?></span><br />
<input type="submit" name="submit" value="<?=$parObj->_getLabenames($arrayData,'newButtonName','name');?>" class="regSubButton" id="newSubBtn" <?php if($lanId	==	5) echo "style='background-position:10px -216px;'";?>/>

</div>
<?php } ?>
</form>
</div>
<div class="shade_btm_userReg"></div>
<script type="text/javascript">
	getOffset();
</script>
<?php include("footer.php"); ?>

<label class="error" for="textfieldLtest2" generated="true">

    <br></br>

    Entrez une adresse e-mail valide

</label>

<label class="error" for="textfieldLtest1" generated="true">

    <br></br>

    Une adresse e-mail est requise

</label>
