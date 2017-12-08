<?php
session_start();

include_once('includeconfig.php');
//include_once('user_logged.php');
include_once('./includes/classes/class.member.php');
include_once('includes/classes/class.massmail.php');


$parObj 		=   new Contents('giftreg.php');
$objGen   		=	new General();
$dbObj     		=   new DbAction();
$objMember		= 	new Member($lanId);
$objMassmail	=   new Massmail($lanId);


unset($_SESSION['registration']);
unset($_SESSION['login']);

$membqry="SELECT membership_fee,membership_feedollar FROM settings";
$membreslt = $GLOBALS['db']->getAll($membqry, DB_FETCHMODE_ASSOC);

if($lanId ==1){
	$fee= $membreslt[0]['membership_feedollar'];
}else{
	$fee= $membreslt[0]['membership_fee'];
}

$_SESSION['memberfee']=$membreslt[0]['membership_fee'];



if($lanId=="") $lanId=1;


$returnData		= $parObj->_getTagcontents($xmlPath,'registrationGift','label');
$arrayData		= $returnData['general'];


$errorReturn=$parObj->_getTagcontents($xmlPath,'registrationGift','messages');
$errorData=$errorReturn['errorMessage'];

//Find all payment plan for displaying plans
$planQuery  	=	"select * from jiwok_payment_plan where plan_status=1 and  plan_currency='".$lanId."' AND plan_id	!=1	ORDER BY plan_id ASC";
$planResult		= 	$GLOBALS['db']->getAll($planQuery,DB_FETCHMODE_ASSOC);

if(isset($_POST['submit'])){	

	$errorMsg = 0;
	if(trim($_POST['user_fname']) == ''){ 
		$errorMsg = 1; 
		$fname = $parObj->_getLabenames($errorData,'fname','name'); 
		$err1 = 1; 
	}
	if(trim($_POST['user_fname']) != ''){
	if($lanId!=5)
	{
	$pattern 	= 	'/^[A-Za-z\xC0-\xFF]([A-Za-z\xC0-\xFF\s]*[A-Za-z\xC0-\xFF])*$/u';
	}
	else
	{
	$pattern 	= 	'/^\p{L}[\p{L} _.-]+$/u';
	}
		$okay 	= 	preg_match($pattern,trim($_POST['user_fname']));
		if(!$okay){	
			$errorMsg = 1; 
			$fname = $parObj->_getLabenames($errorData,'fnameerr','name'); 
			$err1 = 1; 
		}
	}

	if(trim($_POST['user_lname']) == ''){ 
		$errorMsg = 1; 
		$lname = $parObj->_getLabenames($errorData,'lname','name'); 
		$err2 = 1; 
	}
    if(trim($_POST['user_lname']) != ''){
	
	if($lanId!=5){
		$pattern 	= 	'/^[A-Za-z\xC0-\xFF]([A-Za-z\xC0-\xFF\s]*[A-Za-z\xC0-\xFF])*$/u';
	}
	else
	{
	$pattern 	= 	'/^\p{L}[\p{L} _.-]+$/u';
	}	
		$okay 	= 	preg_match($pattern,trim($_POST['user_lname']));
		if(!$okay)	{	
			$errorMsg = 1; 
			$lname = $parObj->_getLabenames($errorData,'lnameerr','name'); 
			$err2 = 1; 
		}
	}
	if(trim($_POST['user_email']) == ''){ 
		$email1 = $parObj->_getLabenames($errorData,'noemail','name'); 
		$errorMsg = 1; 
		$err3 = 1; 
	}
	if(trim($_POST['user_email']) != ''){
		if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", trim($_POST['user_email']))){
			$email1 = $parObj->_getLabenames($errorData,'emailerr','name'); 
			$errorMsg = 1; 
			$err3 = 1; 
		}
	}
	if(trim($_POST['c_user_email']) == '') { 
		$email2 = $parObj->_getLabenames($errorData,'noemail','name'); 
		$errorMsg = 1; 
		$text1 = 0; 
		$err4 = 1; 
	}
	if(trim($_POST['c_user_email']) != ''){
		if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", trim($_POST['c_user_email']))){
			$email2 = $parObj->_getLabenames($errorData,'emailerr','name'); 
			$errorMsg = 1; 
			$err4 = 1; 
		}
	}
	if($errorMsg == 0 && trim($_POST['user_email']) != '' && trim($_POST['c_user_email']) != ''){
		if(0 != strcmp($_POST['user_email'],$_POST['c_user_email'])){
			$errorMsg = 1; 
			$email2 = $parObj->_getLabenames($errorData,'ckemail','name'); 
			$err4 = 1;
		}
	}
	if($_POST['gift_type'] == '') { 
		$errorMsg = 1; 
		$gifttype = $parObj->_getLabenames($errorData,'gifttype','name'); 
		$errtype = 1; 
	}
	if($_POST['gift_friend'] == '') { 
		$errorMsg = 1; 
		$gifttype = $parObj->_getLabenames($errorData,'giftfriend','name'); 
		$err5 = 1; 
	}

	if($_POST['gift_friend'] == 0){
		if(trim($_POST['first_name']) == ''){ 
			$errorMsg = 1; 
			$friendnameb = $parObj->_getLabenames($errorData,'fname','name'); 
			$err6 = 1; 
		}
		if(trim($_POST['last_name']) == ''){ 
			$errorMsg = 1; 
			$friendnamel = $parObj->_getLabenames($errorData,'lname','name'); 
			$err61 = 1; 
		}
		if(trim($_POST['first_name']) != '' && trim($_POST['last_name'])){
			// "/^[A-Za-z]([A-Za-z\s]*[A-Za-z])*$/"    OLD -   '/^[a-zA-Z\']+$/'
			if($lanId!=5){
				$pattern 	= 	'/^[A-Za-z\xC0-\xFF]([A-Za-z\xC0-\xFF\s]*[A-Za-z\xC0-\xFF])*$/u';	
			}
			else
			{
				$pattern 	= 	'/^\p{L}[\p{L} _.-]+$/u';
			}
			$okay 	= 	preg_match($pattern,trim($_POST['first_name']));
			$yes = 0;
			if(!$okay)	{	
				$errorMsg = 1; 
				$friendnameb= $parObj->_getLabenames($errorData,'fnameerr','name'); 
				$err6 = 1; 
				$yes = 1;
			}
			$okay1 	= 	preg_match($pattern,trim($_POST['last_name']));
			if(!$okay1)	{	
				$errorMsg = 1; 
				$friendnamel= $parObj->_getLabenames($errorData,'lnameerr','name'); 
				$err61 = 1; 
				$yes = 1;
			}
			if($yes == 0){
				$giftfriendname = $_POST['first_name'].' '.$_POST['last_name'];

			}
		}
		if(trim($_POST['friend_email']) == ''){ 
			$email1b = $parObj->_getLabenames($errorData,'noemail','name'); 
			$errorMsg = 1; 
			$err7 = 1; 
		}
		if(trim($_POST['friend_email']) != ''){
			if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", trim($_POST['friend_email']))){
				$email1b = $parObj->_getLabenames($errorData,'emailerr','name'); 
				$errorMsg = 1; 
				$err7 = 1; 
			}
		}
		if(trim($_POST['c_friend_email']) == '') { 
			$email2b = $parObj->_getLabenames($errorData,'noemail','name'); 
			$errorMsg = 1; 
			$text1 = 0; 
			$err8 = 1; 
		}
		if(trim($_POST['c_friend_email']) != ''){
			if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", trim($_POST['c_friend_email']))){
				$email2b = $parObj->_getLabenames($errorData,'emailerr','name'); 
				$errorMsg = 1; 
				$err8 = 1; 
			}
		}
		if($errorMsg == 0 && trim($_POST['friend_email']) != '' && trim($_POST['c_friend_email']) != ''){
			if(0 != strcmp($_POST['friend_email'],$_POST['c_friend_email'])){
				$errorMsg = 1; 
				$email2b = $parObj->_getLabenames($errorData,'ckemail','name'); 
				$err8 = 1;
			}else{
				$giftfriendemail = $_POST['friend_email'];
			}
		}
	}else{
		$giftfriendname = 'empty';
		$giftfriendemail = 'empty';
	}
	// If the reg1 is success then go to step2
	if($errorMsg == 0){
		
		$planQuery  	=	"select * from jiwok_payment_plan where plan_status=1 and  plan_currency='".$lanId."' AND plan_amount=".$_POST['gift_type'];
		$planResult		= 	$GLOBALS['db']->getRow($planQuery,DB_FETCHMODE_ASSOC);		
		
		$_SESSION['giftsubscription'] = 'TRUE';
		$_SESSION['user_name'] 		=	$_POST['user_fname'];
		$_SESSION['user_lname'] 	=	$_POST['user_lname'];
		$_SESSION['user_email'] 	=	$_POST['user_email'];
		$_SESSION['gift_type'] 		=	$planResult[plan_duration];
		$_SESSION['friend_name'] 	=	$giftfriendname ;
		$_SESSION['friend_email'] 	=	$giftfriendemail;
		$_SESSION['gift_friend']    =   $_POST['gift_friend'];
		$_SESSION['frend_msg']      =   $_POST['friend_msg'];
		$_SESSION['user_doj'] 		=	date('Y-m-d');
		$_SESSION['giftregistration'] 		=	$_POST;
		$reg = $objGen->_clearElmtsWithoutTrim($_SESSION['giftregistration']);
		//insert member to the gift_member table
		//Getting the value of next id to put it in user_ptions as usermaster_id.
		header("Location:gift_payment.php");
		exit;
	}
}
?>
<?php include("header.php"); ?>
<?php include("menu.php"); ?>
<style>
.gift_col .right {
    width: 411px !important;
}
</style>
<div id="container">
  <div id="wraper_inner">
    <div class="breadcrumbs">
      <ul>
        <li><?=$parObj->_getLabenames($arrayData,'newPageTxt','name');?> :</li>
        <li><a href="<?=ROOT_JWPATH?>index.php"><?=$parObj->_getLabenames($arrayData,'newHmeTxt','name');?></a></li>
        <li>></li>
        <li><a class="select"><?=$parObj->_getLabenames($arrayData,'newHeadTxt','name');?></a></li>
      </ul>
    </div>
    <div class="heading"><span class="name"><?=$parObj->_getLabenames($arrayData,'newHeadTxt','name');?> </span> <span class="date"><strong>&gt; </strong><a href="<?=$backButtonLink?>" class="white"><?=$parObj->_getLabenames($arrayData,'newBckTxt','name');?></a></span></div>
    <div class="container_common">
	<?php if($lanId!=5 ){?>
      <h2 class="hed-2"><?=$parObj->_getLabenames($arrayData,'newDescTxt','name');?>.</h2><?php }?>
      <div class="gift_col">
        <div class="left"><?php if($lanId!=5 ){?><img src="images/voucher-2.jpg" alt="voucher" /><?php } else{?><img src="images/voucher_pl.jpg" alt="voucher" /><?php }?></div>
        <div class="right">
          <div class="hedings"> <strong><?=$parObj->_getLabenames($arrayData,'newYrDtlsTxt','name');?> </strong> <span>* <?=$parObj->_getLabenames($arrayData,'newMtrFldsTxt','name');?></span> </div>
          <div class="clear"></div>
		  <form name="giftregForm" action="giftreg.php" method="post" >
          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table3">
		  <? if($errorMsg == 1){ ?>
		  <tr class="instructions" >
		  <td colspan="4" ><div><?=$parObj->_getLabenames($errorData,'errtitle','name');?></div></td>
		  </tr>
		  <? }?>
		  <?php if($nomessage==1){?>
		  <tr>
		  <td colspan="2"><center><span style="color:#FF0000"><?=$parObj->_getLabenames($errorData,'nogift','name');?></span></center></td>
		  </tr>
		  <?php }?>
		  
		  
		  
            <tr <? if($err1 == 1){ ?> class="errorMessageBg"<? }?> >
              <td align="right"><?=$parObj->_getLabenames($arrayData,'newFNameTxt','name');?><span>*</span></td>
              <td colspan="2" align="left"><input type="text" name="user_fname" id="textfield" class="tfl-7" value="<?=stripslashes($_POST['user_fname'])?>" />
              <br /><? if($err1 == 1){ ?><span class="errorMessageCommon" onMouseOver="tooltip('<?=$fname?>');" onMouseOut="exit();">[?]</span><? }?></td>
            </tr>
            <tr <? if($err2 == 1){ ?> class="errorMessageBg"<? }?>>
              <td align="right"><?=$parObj->_getLabenames($arrayData,'newLNameTxt','name');?><span>*</span></td>
              <td colspan="2" align="left"><input type="text" name="user_lname" id="textfield" class="tfl-7" value="<?=stripslashes($_POST['user_lname'])?>" />
              <br /><? if($err2 == 1){ ?><span class="errorMessageCommon" onMouseOver="tooltip('<?=$lname?>');" onMouseOut="exit();">[?]</span><? }?></td>
            </tr>
            <tr <? if($err3 == 1){ ?> class="errorMessageBg"<? }?>>
              <td align="right"><?=$parObj->_getLabenames($arrayData,'newEMailTxt','name');?><span>*</span></td>
              <td colspan="2" align="left"><input type="text" name="user_email" id="textfield" class="tfl-7" value="<?=stripslashes($_POST['user_email'])?>" />
              <br /><? if($err3 == 1){ ?><span class="errorMessageCommon" onMouseOver="tooltip('<?=$email1?>');" onMouseOut="exit();">[?]</span><? }?></td>
            </tr>
            <tr <? if($err4 == 1){ ?> class="errorMessageBg"<? }?>>
              <td align="right" ><?=$parObj->_getLabenames($arrayData,'newConfirmTxt','name');?><span>*</span></td>
              <td colspan="2" align="left"><input type="text" name="c_user_email" id="textfield" class="tfl-7" value="<?=stripslashes($_POST['c_user_email'])?>" />
              <br />
			  <? if($err4 == 1){ ?><span class="errorMessageCommon" onMouseOver="tooltip('<?=$email2?>');" onMouseOut="exit();">[?]</span><? }?>
			  </td>
            </tr>
            <tr <? if($errtype == 1){ ?> class="errorMessageBg"<? }?>>
              <td align="right"><?=$parObj->_getLabenames($arrayData,'newTheTxt','name');?><span>*</span></td>
              <td align="left" class="wdth">
			   <select name="gift_type" class="list-box-5" onChange="getGiftAmount(gift_type[gift_type.selectedIndex].value);">

					 <option value="">Select</option>
                     <?php
					 foreach($planResult as $planResults)
					 {
					 ?>
                     	<option value="<?=$planResults[plan_amount]?>" <?php if($planResults[plan_amount]==$_POST['gift_type']){?>selected="selected" <?php }?>><?=$planResults[plan_id]?></option>                        
                     <?php }?>  
                        
						<!--<option value="3" <?php if(3==$_POST['gift_type']){?>selected="selected" <?php }?>>3</option>
                        <option value="6" <?php if(6==$_POST['gift_type']){?>selected="selected" <?php }?>>6</option>
                        <option value="12" <?php if(12==$_POST['gift_type']){?>selected="selected" <?php }?>>12</option>-->
                        
<?php /*?>					 <?php for($i=3;$i<=12;$i+3)

					 {?><option value="<?=$i?>" <?php if($i==$_POST['gift_type']){?>selected="selected" <?php }?>><?=$i?></option>

					 <?php }?>
<?php */?>
					 </select>
					 <br />
					 <? if($errtype== 1){ ?><span class="errorMessageCommon" onMouseOver="tooltip('<?=$gifttype?>');" onMouseOut="exit();">[?]</span><? }?>
		</td>
              <td align="left" class="gry"><?=$parObj->_getLabenames($arrayData,'newMonthTxt','name');?> = <label id="price" >0<?=$parObj->_getLabenames($arrayData,'curr','name');?></label> </td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table3">
            <tr>
              <td><?=$parObj->_getLabenames($arrayData,'newQstnTxt','name');?> </td>
              <td class="gry"><?=$parObj->_getLabenames($arrayData,'newYesTxt','name');?>
                <input type="radio" name="gift_friend" id="radio" <? if(trim($_POST['gift_friend']) == "0") { echo 'checked="checked"'; } ?> value="0" onClick="showdiv('<?php echo 0 ;?>')" />
                <?=$parObj->_getLabenames($arrayData,'newNoTxt','name');?>
                <input type="radio" name="gift_friend" id="radio2" <? if(trim($_POST['gift_friend']) != "0"   || $_POST['gift_friend'] == "") { echo 'checked="checked"'; } ?> value="1" onClick="showdiv('<?php echo 1 ;?>')" /><br /><? if($err5 == 1){ ?><span class="errorMessageCommon" onMouseOver="tooltip('<?=$giftfriend?>');" onMouseOut="exit();">[?]</span><? }?></td>
            </tr>
          </table>
		  <input type="hidden" id="feeElement" value="<?php echo $fee;?>"  />
		  <input type="hidden" id="lanidsElement" value="<?php echo $lanId;?>"  />
		
<div  id="imperial" style="display: <?php if  (trim($_POST['gift_friend'])=="0") echo 'block'; else echo "none"; ?>;">
		  
<table width="100%" border="0" cellspacing="2" cellpadding="0" class="table3">
  <tr <? if($err6 == 1){ ?> class="errorMessageBg"<? }?>>
    <td align="right"><?=$parObj->_getLabenames($arrayData,'fname','name');?><span>*</span></td>
    <td colspan="2" align="left">
      <input type="text" name="first_name" id="textfield" class="tfl-7" value="<?=stripslashes($_POST['first_name'])?>" />
    <br /><? if($err6 == 1){ ?><span class="errorMessageCommon" onMouseOver="tooltip('<?=$friendnameb?>');" onMouseOut="exit();">[?]</span><? }?></td>
  </tr>
  <tr <? if($err61 == 1){ ?> class="errorMessageBg"<? }?>>
    <td align="right"><?=$parObj->_getLabenames($arrayData,'lname','name');?><span>*</span></td>
    <td colspan="2" align="left">
      <input type="text" name="last_name" id="textfield" class="tfl-7" value="<?=stripslashes($_POST['last_name'])?>" />
    <br /><? if($err61 == 1){ ?><span class="errorMessageCommon" onMouseOver="tooltip('<?=$friendnamel?>');" onMouseOut="exit();">[?]</span><? }?></td>
  </tr>
  <tr <? if($err7 == 1){ ?> class="errorMessageBg"<? }?>>
    <td align="right"><?=$parObj->_getLabenames($arrayData,'friendemail','name');?><span>*</span></td>
    <td colspan="2" align="left">
      <input type="text" name="friend_email" id="textfield" class="tfl-7" value="<?=stripslashes($_POST['friend_email'])?>" />
    <br />
	<? if($err7== 1){ ?><span class="errorMessageCommon" onMouseOver="tooltip('<?=$email1b?>');" onMouseOut="exit();">[?]</span><? }?>
	</td>
  </tr>
  <tr <? if($err8 == 1){ ?> class="errorMessageBg"<? }?>>
    <td align="right"><?=$parObj->_getLabenames($arrayData,'cfriendemail','name');?><span>*</span></td>
    <td colspan="2" align="left">
      <input type="text" name="c_friend_email" id="textfield" class="tfl-7" value="<?=stripslashes($_POST['c_friend_email'])?>" />
    <br /><? if($err8 == 1){ ?><span class="errorMessageCommon" onMouseOver="tooltip('<?=$email2b?>');" onMouseOut="exit();">[?]</span><? }?></td>
  </tr>
  <tr <? if($err9 == 1){ ?> class="errorMessageBg"<? }?>>
    <td align="right"><?=$parObj->_getLabenames($arrayData,'friendmsg','name');?><span>*</span></td>
    <td align="left" colspan="2"><textarea name="friend_msg" id="friend_msg" type="textarea" value="<?=stripslashes($_POST['friend_msg'])?>" class="tfl-7" rows="3" cols="20" style="height:100px;"  /><?=stripslashes($_POST['friend_msg'])?></textarea><br /><? if($err9 == 1){ ?><span class="errorMessageCommon" onMouseOver="tooltip('<?=$email2b?>');" onMouseOut="exit();">[?]</span><? }?>     </td>
  </tr>
  
</table>

</div>  
		  
		  <input type="submit" name="submit" value="<?=$parObj->_getLabenames($arrayData,'newNextStepTxt','name');?>" class="newGiftBtnTxt" />
		  
           </div>
        <div class="clear"></div>
	  </form>

      </div>
    </div>
  </div>
  <div class="clear"></div>
</div>
<?php include("footer.php"); ?>
