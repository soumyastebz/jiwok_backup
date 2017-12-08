<?php
 $showpop1	=	false;
 $showpop1 	= 	$objPgm->_getUserSubscribedProgram($userid);
 $showpop2	=	false;
 $invoke_time = true;

 if(($showRefUrl == true) && ($showRefUrlstatus == false ))	{ // not in paid ,conditions satisfief 
 
 	$showpop2	=	true;
	
	 $web_workout_genrated	=  $pressApi ->checkWorkoutGeneratedWeb($userId,$flexid );
	 $app_workout_generated	=	$pressApi ->checkWorkoutGeneratedFrmApp($userId,$program_id);
		if($web_workout_genrated==false){ //exist in program queue& generated atleast once	from website	
		$invoke_time = $pressApi ->checkWebInvokeTime($userId,$flexid ); 
			
		}
		elseif($app_workout_generated==false) { //check whether generate from app 
		$invoke_time = $pressApi ->checkAppInvokeTime($userId,$program_id ); 
		
		}
		if($invoke_time  == false){ // workout generation not < today 
				$showpop2	=	false;//popup 2 shouldnt see
			}
 }
 
 //$showpop2	= 	$objPgm->_getUserGeneratePaid($userid);
 
?>
<script>
	$(document).ready(function(){
		centerPopup2();
	});
</script>
<?php if(($_REQUEST["actn"]==base64_encode("paymentSuccess"))||($_REQUEST["actnMode"]!="")){ ?>
<script>
	$(document).ready(function(){
		//load popup
		showPopup("paymentSuccessMsg","");
		
		$("#okIdPayment").click(function(){
			disablePopupGeneral("paymentSuccessMsg","");
		});
		//Press Escape event!
		$(document).keypress(function(e){
			if(e.keyCode==27 && popupStatus==1){
				disablePopupGeneral("paymentSuccessMsg","");
			}
		});
	});
</script>



<div class="pop_paymentSuccessMsg" id="paymentSuccessMsg" style="display:none;position:fixed;z-index:100000;">
 <div class="popbox_paymentSuccessMsg">
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center" style="text-align:left">
        <?php if($_REQUEST["actn"]==base64_encode("paymentSuccess")){ ?>
		<?=$parObj->_getLabenames($arrayData,'paymentSucessMsg','name');?>
        <?php }else{
			echo base64_decode($_REQUEST["actnMode"]);}
		?>
        <br/><br/></td>
      </tr>
      <tr>
        <td align="center"><a id="okIdPayment"><input class="btn_pop ease"  name="renewSubscriptionIdBtn" type="button" value="Ok" /></a></td>
      </tr>
    </table>
    <div class="clear"></div>
  </div>
</div>
<?php } 
else if($showpop1 == true){ // pop up if no training program subscribed yet
?>
	<script>
	$(document).ready(function(){
	    //centering with css
		centerPopup();
		//load popup
		loadPopup();	//CLOSING POPUP
		//Click the x event!
		$("#popupContactClose").click(function(){
			disablePopup();
		});
		//Click out event!
		$("#backgroundPopup").click(function(){
			$("#backgroundPopup").fadeOut("slow");
			$("#AfterRegistrationmsg").fadeOut("slow");
		});
		$("#OkReg").click(function(){
			//disablePopup();
			$("#AfterRegistrationmsg").bPopup().close();
			
		});
		//Press Escape event!
		$(document).keypress(function(e){
			if(e.keyCode==27){
			    $("#backgroundPopup").fadeOut("slow");
				$("#AfterRegistrationmsg").fadeOut("slow");
			}
		});
	});
</script>

<!--Congratulations! You are now a Jiwoker message display in popup-->

<div class="pop_Registrationmsg" id="AfterRegistrationmsg" style="display:none;position:fixed;z-index:100000;">
  
  <div class="popbox_Registrationmsg">
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center" style="text-align:left"><?=$parObj->_getLabenames($arrayDataPopup,'mes1','name'); ?><br/><br/></td>
      </tr>
      <tr>
        <td style="text-align:left"><?=$parObj->_getLabenames($arrayDataPopup,'mes3','name'); ?><br/><br/></td>
      </tr>
      <tr>
        <td style="text-align:left">N'hésitez à pas contacter notre équipe <a href="https://www.jiwok.com/ticket/tickets_view.php">si vous avez des questions &gt;&gt;&gt;&gt;  </a><br/><br/></td>

      </tr>      
      <tr>
        <td align="center"><input class="btn_pop ease"  name="afterRegBtn" type="button"  value="Ok" id="OkReg" /></td>
      </tr>

    </table>
    <div class="clear"></div>
  </div>
  
</div>
<?php 	
}else if( $showpop2 == true): ?>

<script>
	$(document).ready(function(){
	    //centering with css
		centerPopup();
		//load popup
		loadPopup();	//CLOSING POPUP
		//Click the x event!
		$("#popupContactClose").click(function(){
			disablePopup();
		});
		//Click out event!
		$("#backgroundPopup").click(function(){
			$("#backgroundPopup").fadeOut("slow");
			$("#Registrationmsg").fadeOut("slow");
		});
		$("#okid").click(function(){
			//disablePopup();
			$("#backgroundPopup").fadeOut("slow");
			$("#Registrationmsg").fadeOut("slow");
		});
		//Press Escape event!
		$(document).keypress(function(e){
			if(e.keyCode==27){
			    $("#backgroundPopup").fadeOut("slow");
				$("#Registrationmsg").fadeOut("slow");
			}
		});
	});
</script>

<!--Congratulations! You are now a Jiwoker message display in popup-->

<div class="pop_Registrationmsg" id="Registrationmsg" style="display:none;position:fixed;z-index:100000;">
  
  <div class="popbox_Registrationmsg">
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center" style="text-align:center">
J'espère que votre première séance s'est bien passé !<br/><br/></td>
      </tr>
      <tr>
        <td style="text-align:center">Pour continuer votre progression et atteindre rapidement votre objectif,<br/><a href="https://www.jiwok.com/payment_new.php">activez dès maintenant votre compte Jiwok &gt;&gt;&gt;&gt;</a><br/><br/></td>
      </tr>
      
      <tr>
        <td align="center"><input class="btn_pop ease" onclick="return renewsubscription()" name="renewSubscriptionIdBtn" type="button"  value="Ok" /></td>
      </tr>

    </table>
    <div class="clear"></div>
  </div>
  
</div>
<div id="backgroundPopup" style="display:block; position:fixed; height:100%; width:100%; top:0; left:0; background:#FFFFFF; border:1px solid #cecece;z-index:-1;"></div>
<?php
endif;

if(isset($_REQUEST['t']) && $userpaymentstatus > 0):
?>
<script>

	$(document).ready(function()
	{ 
		//centering with css
		centerPopup();
		
		//load popup
		loadPopup();	//CLOSING POPUP
		//Click the x event!
		$("#popupContactClose").click(function(){
			disablePopup();
		});
		//Click out event!
		$("#backgroundPopup").click(function(){
			disablePopup();
		});
		$("#okid").click(function(){
		    disablePopup();
			//showPopup("giftPaymentMsg","");
		});

		//Press Escape event!
		$(document).keypress(function(e){
			if(e.keyCode==27 && popupStatus==1){
				disablePopup();
				//showPopup("giftPaymentMsg","");
			}
		});
	});
 </script>
	<div class="pop_giftpayment" id="Registrationmsg" style="display:block;position:fixed;z-index:100000;">
      <div class="popbox_giftpayment">
		<table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
		  <tr>
			<td align="center"> <?=$parObj->_getLabenames($arrayDataGift,'lefttitle1','name'); ?></td>
		  </tr>
		  <tr>
			<td align="center"><a id="okid"><input class="btn_pop ease"  name="renewSubscriptionIdBtn" type="button" value="Ok" /></a></td>
		  </tr>
		</table>
	   </div>
	</div>
	
	<div id="backgroundPopup" style="display:none; position:fixed; height:100%; width:100%; top:0; left:0; background:#FFFFFF; z-index:-1;"></div>
<?php endif;?>
<div class="popup" id="produiOverlayNike" style="display:none; position:fixed; z-index:100000; left:280px; top:250px;">

  <div class="inner">
  <a id="fancybox-close" onClick="hideUnsubscribe('produiOverlayNike');" title="close" style="display: inline;"></a>
    <h2><?=$parObj->_getLabenames($arrayData,'nikemsg','name')?></h2>
	<form action="userArea.php" id="addnike" method="post" name="addnike">
  <div class="red-1" id="nikeerror" style="display:none;"><br /><b><?=$parObj->_getLabenames($arrayData,'nikeerrormsg','name')?></b></div>
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td width="40%" align="left" valign="middle"><?=$parObj->_getLabenames($arrayData,'nikelogin','name')?></td>
        <td width="60%"><input type="text" class="textnike" name="nikeUser" id="nikeUser" style="width:200px;" /></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><?=$parObj->_getLabenames($arrayData,'nikepassword','name')?></td>
        <td><input type="password" class="textnike" name="nikePass" id="nikePass" style="width:200px;" /></td>
      </tr>
      <tr>
        <td colspan="2" align="center"><input class="bu_03" name="addnike" type="submit" value="<?=$parObj->_getLabenames($arrayData,'submitbtn','name')?>" onClick="return checkNikeValues();"/>
      &nbsp;&nbsp;
      <input class="bu_03" name="cancelnike" type="button" value="<?=$parObj->_getLabenames($arrayData,'cancelbutton','name')?>" onClick="hideUnsubscribe('produiOverlayNike');"/></td>
      </tr>
    </table>
	</form>
    <div class="clear"></div>&nbsp;
  </div>
 
</div>

<div id="produiOverlayBoxConfirmUserArea" class="pop_produiOverlayBoxConfirmUserArea" style="display:none;position:fixed; z-index:100000; left:280px; top:250px;">
<div class="popbox_produiOverlayBoxConfirmUserArea">
  		<a id="fancybox-close" onClick="hideUnsubscribe2('');" title="close" style="display: inline;"></a>
		<h3><?=$parObj->_getLabenames($arrayData,'confirmsub','name');?><?=$subscribed_program_title?>?</h3>
		<br><br>
		<?php
		$programDt1 = $objPgm->_getUserTrainingProgramConfirm($userid);
		$subscribed_program_title = trim(stripslashes($programDt1['program_title']));
		$subscribed_program_id = trim(stripslashes($programDt1['programs_subscribed_id']));?>
        <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
        <tr><td colspan="2" align="center">
		<input class="btn_pop ease" name="payment" id="payment" type="button" value="<?=$parObj->_getLabenames($arrayData,'yes','name');?>" onClick="confirmUnsubscribe(<?=$subscribed_program_id?>);"/>
         &nbsp;&nbsp;
        <input class="btn_pop ease" name="payment" id="cancelpayment" type="button" value="<?=$parObj->_getLabenames($arrayData,'no','name');?>" onClick="hideUnsubscribe2();"/></td>
      	</tr>
        </table>
  		<div class="clear"></div>&nbsp;
	</div>
</div> 
<div id="paymentRetry" class="pop_produiOverlayBoxConfirmUserArea" style="display:none;position:fixed; z-index:100000; left:280px; top:250px;">
<div class="popbox_produiOverlayBoxConfirmUserArea">
  		<a id="fancybox-close" onClick="noNeedofNewPayment('');" title="close" style="display: inline;"></a>
		<h4><?=$retryMsg?> &gt;&gt;&gt;</h4>
		<br>
        <?php
		$pgm_expired	=	"false";
		if(trim(stripslashes($program['program_expdate']))!= "" && $today >= trim(stripslashes($program['program_expdate'])) && trim($_REQUEST['conf']) != "")
		{
			$pgm_expired	=	"true";
		}
		?>
		<table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
        <tr><td colspan="2" align="center">
		<input class="btn_pop ease" name="topayment" id="topaymentpage" type="button" value="<?=$parObj->_getLabenames($arrayData,'yes','name');?>" onClick="confirmPaymentPage();"/>
         &nbsp;&nbsp;
        <input class="btn_pop ease" name="nopayment" id="Nopaymentpage" type="button" value="<?=$parObj->_getLabenames($arrayData,'no','name');?>" onClick="noNeedofNewPayment('<?php echo $pgm_expired ?>');"/></td>
      	</tr>
        </table>
  		<div class="clear"></div>&nbsp;
	</div>
</div>
<?php
/*--------------------------------new gift code pop up for campaign payment-------------*/
$userlanId	=	$user['user_language'];

$testUserIds = array(162584,193208 ,190976,190801); // ,135084 User ids For initial testing

/*if($userlanId == 2  && in_array($userid,$testUserIds) && $_SESSION['gift']['workout3or8'])*/
if($userlanId == 2  && in_array($userid,$testUserIds) )
{
/*include_once("includes/classes/class.GiftCodeCampaign.php");
$objGiftCampaign	=	new GiftCodeCampaign();*/

$gift_userSql	=	"SELECT c.code,c.payment_id FROM gift_userdetails AS c 
					LEFT JOIN payment AS p ON p.payment_id = c.payment_id 
					LEFT JOIN payment_paybox AS b ON b.user_id = p.payment_userid  
					WHERE p.payment_expdate >=  '".$today."' AND c.user_id=".$userid . " 
					AND p.`payment_status` = 1 AND (b.user_id IS NULL OR (b.status != 'ACTIVE')) GROUP BY c.payment_id  ORDER BY c.id DESC"; 	
$gift_user 		= 	$dbObj->_getList($gift_userSql);
$payment_id		=	$gift_user [0]['payment_id']; 
$giftCodePopUPFlag = false;
if(count($gift_user) > 0)//Giftcode Popup Condition 1(User in gift_userdetails with payment_expdate >=today and not a member of paybox) satisfied
{
	$sql_usercampaign		=	"SELECT id,paid_status,camp_id FROM gift_user_campaign WHERE user_id=".$userid." AND payment_id = ".$payment_id ;
	$gift_usercampaign		=	$dbObj->_getList($sql_usercampaign);
	if(count($gift_usercampaign)>0)
	{
		if($gift_usercampaign[0]['paid_status'] == 0)//check whether this user not done any payment thrugh gift pop up-condition2
		{ 
			$giftCodePopUPFlag  = true;
			$primary_camp_id	=	$gift_usercampaign[0]['id'];
			$camp_ID		    =	$gift_usercampaign[0]['camp_id'] ;
			$camp_details	    =   "SELECT id,camp_discount,camp_price,valid_months,amount_month FROM gift_pay_campaign WHERE id =".$camp_ID;
			$next_campaign  	=	$dbObj->_getList($camp_details);
		}
	}
	else
	{ 
				$giftCodePopUPFlag  = true;
				$sql_campaign_max		=	"SELECT camp_id FROM gift_user_campaign ORDER BY id DESC LIMIT 0, 1";
				$max_campaign			=	 $dbObj->_getList($sql_campaign_max);
				$campID					=	$max_campaign[0]['camp_id'];				
				$sql_next_campaignid	=	"SELECT id,camp_discount,camp_price,valid_months,amount_month FROM gift_pay_campaign WHERE id > ".$campID	." AND status = 1 ORDER BY id ASC LIMIT 1";
				$next_campaign			=	 $dbObj->_getList($sql_next_campaignid);
				if(count($next_campaign) ==  0)
				{
					
					$sql_next_campaignId	=	"SELECT id,camp_discount,camp_price,valid_months,amount_month FROM gift_pay_campaign WHERE id < ".$campID	." AND status = 1 ORDER BY id  LIMIT 1";
					$next_campaign			=	 $dbObj->_getList($sql_next_campaignId);
					if(count($next_campaign) ==  0)
					{
						$sql_next_campaignId	=	"SELECT id,camp_discount,camp_price,valid_months,amount_month FROM gift_pay_campaign WHERE id = ".$campID	." AND status = 1 ORDER BY id  LIMIT 1";
						$next_campaign			=	 $dbObj->_getList($sql_next_campaignId);
					}
				}
				$next_campaign_id		=	trim($next_campaign[0]['id']);
				
				$insArr	=	array('user_id'=>$userid,'camp_id'=>$next_campaign_id,'paid_status'=>0,'payment_id'=>$payment_id,'date'=>$today);
				$ins_usercampaign = $dbObj->_insertRecord('gift_user_campaign',$insArr);
				
				/*$ins_usercampaign		=	mysql_query("INSERT INTO `gift_user_campaign` (`id` ,`user_id`,`camp_id`,               							 											`paid_status`,`payment_id`,`date`,`camp_payment_id`) VALUES ( NULL ,  '$userid',  '$next_campaign_id', '0','$payment_id','$today',0)") or die('Failed to execute');
				
				die("INSERT INTO `gift_user_campaign` (`id` ,`user_id`,`camp_id`,               							 											`paid_status`,`payment_id`,`date`) VALUES ( NULL ,  '$userid',  '$next_campaign_id', '0','$payment_id','$today') ".$ins_usercampaign); exit;*/
				
				/*$insQry	= "INSERT INTO `gift_user_campaign` (`id` ,`user_id`,`camp_id`,               							 											`paid_status`,`payment_id`,`date`,`camp_payment_id`) VALUES ( NULL ,  '$userid',  '$next_campaign_id', '0','$payment_id','$today',0)";
				$ins_usercampaign		=	mysql_query($insQry) or die(mysql_error());*/
				
				if($ins_usercampaign)//die($ins_usercampaign);
				{
					$sql_lastIns_campaignId	=	"SELECT max(id) as lastID FROM gift_user_campaign";
					$sql_lastIns_campaign	=	 $dbObj->_getList($sql_lastIns_campaignId);
					$primary_camp_id		=	 $sql_lastIns_campaign[0]['lastID'];//primary key 
					//echo 'id->'.$ins_usercampaign.'->'.$primary_camp_id; exit;
				}
				else{
					die('Erro');	
				}

	}
				$next_campaign_id				=	trim($next_campaign[0]['id']);
				$next_campaign_discount			=	trim($next_campaign[0]['camp_discount']);
				$next_campaign_price			=   trim($next_campaign[0]['camp_price']);
				$next_campaign_months			=	trim($next_campaign[0]['valid_months']);
				$next_campaign_price_per_month	=	trim($next_campaign[0]['amount_month']);
				
				
}				
}
?>
 <?php 
 
 
 
 if( $giftCodePopUPFlag ){ 
 $_SESSION['gift']['PopUPFlag']			=	$giftCodePopUPFlag;
 $_SESSION['gift']['campId']			=	$next_campaign_id;
 $_SESSION['gift']['camp_discount']		=	$next_campaign_discount	;
 $_SESSION['gift']['camp_price']		=	$next_campaign_price;
 $_SESSION['gift']['valid_months']		=	$next_campaign_months;
 $_SESSION['gift']['primary_camp_id']	=	$primary_camp_id;
 $_SESSION['gift']['amount_month']		=	$next_campaign_price_per_month;

 ?>
<script>
	var next_campaign_months		=	"<?php echo $next_campaign_months; ?>";
	var next_campaign_price			=	"<?php echo $next_campaign_price; ?>";
	
	$(document).ready(function(){ 
	    //load popup
		  showPopup("giftPaymentMsg","");
		  setTimeout function(){
          jpopup = $('.pop_paymentSuccessMsg').bPopup({speed: 200,positionStyle: 'fixed',});
          },10000); // 5000 to load it after 10 seconds from page load
          
		$("#okIdgiftPayment").click(function(){ 
			document.location.href='payment_giftpopup_bis.php';
		});
			//Click the x event!
		$(".closepop").click(function(){
			
			$("#giftPaymentMsg").fadeOut("slow");
		});
		//Press Escape event!
		 $(document).bind('keydown', function(e) { 
        if (e.which == 27) {
            $("#giftPaymentMsg").fadeOut("slow");
        }
    }); 
	});
</script>

<div class="popup" id="giftPaymentMsg" style="display:none;position:fixed;z-index:100000;">
  
  <div class="inner">
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center" style="text-align:left">
        
        <a class="closepop" style="display: inline;" title="close"  id="fancybox-close"></a>
        
        <!--<a id="popupContactClose1"><img border="0" style="margin:10px 10px 0px 5px; float: right; cursor:pointer" title="close" alt="close" src="images/close-button.gif" /></a>-->
        <?php
			$campainPopMsg = $parObj->_getLabenames($arrayDataPopup,'campainGiftCodeMsg','name');
			$campainPopMsg = str_replace('###AMOUNT###',$next_campaign_price,$campainPopMsg);
			$campainPopMsg = str_replace('###MONTH###',$next_campaign_months,$campainPopMsg);
		 ?>
       <div><?=$campainPopMsg;?></div>
		<?php /* echo $parObj->_getLabenames($arrayData,'paymentSucessMsg','name');*/?>
        
		
        <br/><br/></td>
      </tr>
      <tr>
        <td align="center"><a id="okIdgiftPayment"><input class="bu_03"  name="giftPayBtn" type="button" value="Ok" /></a></td>
      </tr>
    </table>
    <div class="clear"></div>
  </div>
 
</div>
<?php } ?>

<!-- For shwing success message after ddoing gift campaign payment successfully -->
<?php  if($_GET['giftPaymentReturn'] == 'success' && 0 ){ 
 
 ?>
<script>
	
	$(document).ready(function(){
		//load popup
		showPopup("giftPaymentMsgSuccess","");
		
		$("#okIdgiftPaymentSuccess").click(function(){ 
			$("#giftPaymentMsgSuccess").fadeOut("slow");
		});
			//Click the x event!
		$(".closepop").click(function(){
			
			$("#giftPaymentMsgSuccess").fadeOut("slow");
		});
		//Press Escape event!
		 $(document).bind('keydown', function(e) { 
        if (e.which == 27) {
            $("#giftPaymentMsgSuccess").fadeOut("slow");
        }
    }); 
	});
</script>

<div class="popup" id="giftPaymentMsgSuccess" style="display:none;position:fixed;z-index:100000;">
  
  <div class="inner">
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center" style="text-align:left">
        
        <a class="closepop" style="display: inline;" title="close"  id="fancybox-close"></a>
        
        <div>Your payment process successfully completed.</div>
		<?php /* echo $parObj->_getLabenames($arrayData,'paymentSucessMsg','name');*/?>
        
		
        <br/><br/></td>
      </tr>
      <tr>
        <td align="center"><a id="okIdgiftPaymentSuccess"><input class="bu_03"  name="giftPayBtn" type="button" value="Ok" /></a></td>
      </tr>
    </table>
    <div class="clear"></div>
  </div>
  <div><img src="images/pop-btm.png" alt="jiwok" /></div>
</div>
<?php } ?>
