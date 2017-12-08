<?php
require_once 'includes/classes/Referrel/class.referal.php';
require_once 'includes/classes/Referrel/Twitter/twitteroauth.php';
require_once 'includes/classes/Referrel/Facebook/facebook.php';
require_once 'includes/classes/Referrel/OpenInviter/openinviter.php';
include_once "includes/classes/class.discount.php";

//$parObj 		=  new Contents('userreg1.php');
$referal		=	new referal();
$objDisc		= 	new Discount($lanId);

$returnData		= $parObj->_getTagcontents($xmlPath,'referralSectionNew','label');
$arrayData		= $returnData['general'];



$referal->debugQuery	=	false;
//////////////////////////// Google Shortner API////////////////////////////////////////////
$userToken		=	$referal->getUserToken();
if($lanId!=5)
{
$makeUrl		=	"www.jiwok.com/index.php?utm_source=referal&utm_medium=referal&utm_term=referal&utm_campaign=referal&referrer=".$userToken."&media=fb";
}
else
{
$makeUrl		=	"www.jiwok.com/pl/index.php?utm_source=referal&utm_medium=referal&utm_term=referal&utm_campaign=referal&referrer=".$userToken."&media=fb";

}
	$snippedUrl		=	$referal->googleApiCall($makeUrl);

////////////////////////////Google Shortner API END////////////////////////////////////////

///////////////////////////FACEBOOK CONNECT////////////////////////////////////////

	$fbDetails	=	$referal->fbConnect();
	if(defined('USER_STATUS')){
		$facebook 	= new Facebooks($fbDetails);
		$session 	= $facebook->getSession();
		if($session){
			if(USER_STATUS	==	"Need Access" || USER_STATUS == "Revoked"){
				$result["appId"]	=	FACEBOOK_APP_ID;
			}
		}
		else{
			if(USER_STATUS	==	"Need Access" || USER_STATUS == "Revoked"){
				$result	=	array("result"=>USER_STATUS);
				$result["appId"]	=	FACEBOOK_APP_ID;
			}
		}

	}
////////////////////////////////FACEBOOK CONNECT ENDS/////////////////////////////

//////////////////////TWITTER TWEETS///////////////////////////////

	$tweetDetails	=	$referal->twConnect();
	if(is_array($tweetDetails)){//Check for user Approval
		$tweetFlag	=	true;
	}
	else{
		$tweetFlag	=	false;
	}
	$fbSts	=	0;
	$twtSts	= 	0;
	
	$qry	=	"select * from jiwok_share_social where user_id=".$userId;
	$dataRefAry	=	$referal->dbSelectAll($qry);
	

	$selQry	=	"select * from jiwok_refferel_wo where user_id=".$userId;
	$dataArray		=	$referal->dbSelectAll($selQry);
	if(count($dataArray) > 0){
		if($dataArray[0]["isfb"]=="1"){
			$fbSts	=	1;	
		}
		if($dataArray[0]["istw"]=="1"){
			$twtSts	=	1;	
		}
	}else if(count($dataRefAry) > 0){
		$fbSts	=	1;
		$twtSts	=	1;
		
		if($dataRefAry[0]["facebook_access_token"]!=""){
			//$fbSts	=	1;	
		}
		if($dataRefAry[0]["twitter_oauth_token"]!=""){
			//$twtSts	=	1;	
		}
	}
	
	
	$isFbPosted	=	1;
	$isFbPostedWO	=	1;
	$isFbPostedComment	=	1;
	$isFbPostedHistory	=	1;
	if($fbPUParent=="WOPopup"){
		if((count($dataRefAry)==0)&&(count($dataArray)==0)){
			$isFbPosted	=	0;
			$isFbPostedWO	=	0;
		}
	}else{
		$isFbPosted	=	0;
		$isFbPostedWO	=	0;
		$isFbPostedComment	=	0;
		$isFbPostedHistory	=	0;
	}
	
	
	$tmpColName	=	"showHistory";
	$tmpColShared	=	"historypost";
	if($fbPUParent=="WOPopup"){
		$tmpColName	=	"showWo";
		$tmpColShared	=	"wopost";
	}else if($fbPUParent=="showComment"){
		$tmpColName	=	"showComment";
		$tmpColShared	=	"commentpost";
	}
	
	$showStsWO	=	1;
	$showStsComment	=	1;
	$showStsHistory	=	1;
	
	$showSts	=	1;
	$isFbShared	=	0;
	$isFBSharedWO	=	0;
	$isFBSharedComment	=	0;
	$isFBSharedHistory	=	0;
	
	$chkMsg	=	"partager ce commentaire sur Facebook";
	
	$selQry	=	"select * from jiwok_fbshare_popup where user_id=".$userId;
	$dataArray		=	$referal->dbSelectAll($selQry);
	if(count($dataArray) > 0){
		if($isFbPosted==0){
			if($dataArray[0][$tmpColName]==0){
				$isFbPosted	=	1;
				$showSts	=	$dataArray[0][$tmpColName];
			}
				
		
		}
		
		if($dataArray[0]["WOPopup"]==0){
			$isFbPostedWO	=	1;
			$showStsWO	=	$dataArray[0]["showWo"];
		}
		if($dataArray[0]["showComment"]==0){
			$isFbPostedComment	=	1;
			$showStsComment	=	$dataArray[0]["showComment"];
		}
		if($dataArray[0]["showHistory"]==0){
			$isFbPostedHistory	=	1;
			$showStsHistory	=	$dataArray[0]["showHistory"];
		}
		
		$isFbShared	=	$dataArray[0][$tmpColShared];
		
		$isFBSharedWO	=	$dataArray[0]["wopost"];
		$isFBSharedComment	=	$dataArray[0]["commentpost"];
		$isFBSharedHistory	=	$dataArray[0]["historypost"];

	}

	
	$visDataTpl	=	'<div class="refChkCntr"><input type="checkbox" name="postFB" value="1" id="postFB" /></div><div>'.$chkMsg.'</div>';
	$inVisDataTpl	=	'<input type="hidden" name="postFB" id="postFB" value="1" />';
	
	
	if($showSts==1){
		$dataTpl	=	$visDataTpl;
	}else{
		$dataTpl	=	$inVisDataTpl;
	}
	
	
	if($_SESSION["fbLoginTest"]==1){
	}else{
		$isFbPosted	=	1;
	}
?>
<script type="text/javascript">
	var isFb	=	<?php echo $fbSts; ?>;
	var isTwt	=	<?php echo $twtSts; ?>;
	var isFbPosted	=	<?php echo $isFbPosted; ?>;
	var fbPUParent	=	"<?php echo $fbPUParent; ?>";
	var isFbShared	=	<?php echo $isFbShared; ?>;
	var dataTpl	=	'<?php echo $dataTpl; ?>';
	var visNot	=	'<?php echo '<input type="checkbox" name="postFB" value="1" id="postFB" />'.$chkMsg; ?>';
	var inVisNot	=	'<?php echo '<input type="hidden" name="postFB" id="postFB" value="1" />'; ?>';
</script>

<div style="display:none;">
	<div class="errMess"></div>
	<div id="fb-root"></div>
    <?php
	$ranRefMesNo	=	rand(1,5);
	$refTxt	=	$parObj->_getLabenames($arrayData,'refWORefTxt_'.$ranRefMesNo,'name');
	if($refTxt==""){
		$refTxt	=	$parObj->_getLabenames($arrayData,'refWORefTxt','name');
	}
	$refTxt	=	str_replace("#REFURL#",$snippedUrl,$refTxt);
	?>
    
    <input type="hidden" name="refWoId" id="refWoId" value="1" />
    <input type="hidden" name="repSniperUrl" id="repSniperUrl" value="<?=$snippedUrl?>"  />
    <textarea name="fbt_share" id="fbt_share" class="tar"><?=$refTxt?></textarea>
    <input type="hidden" name="fbt_backup" id="fbt_backup" value="<?=$refTxt?>" />
    <span class="sharing">
        <a id="facebkLnk" href="javascript:<?if(USER_STATUS	!= "Allowed"){?>fb_login()<?}?>;">
            <img src="images/facebook.png" /> <?=$parObj->_getLabenames($arrayData,'reffSubBtn_FB','name');?>
        </a>
    </span>
    <span class="sharing">
        <a id="twtLnk" href="javascript:;">
            <img src="images/twitter.png" /> <?=$parObj->_getLabenames($arrayData,'reffSubBtn_TW','name');?>
         </a> 
    </span>
    <?php include("reffCommon.php"); ?>
    <div id="loaderCnt"></div>
</div>
<style>
.sharing {
   
    border: 1px solid #D67413;
    float: left;
    height: 23px;
    line-height: 23px;
    margin-left: 74px;
    margin-right: 2px;
    margin-top: 5px;
    padding: 6px;
    text-align: center;
    width: 196px;
}
.sharing a {
    color: #FFFFFF !important;
}
.popupTextArea{
    background: url("images/inner_topbg_old.gif") repeat-x scroll 0 0 #FFFFFF;
    border: 1px solid #CCCCCC;
    height: 60px;
    margin-left: 9px;
    margin-top: 7px;
    overflow: auto;
    width: 332px;
}
.refChkCntr{
    float: left;
    margin-right: 6px;
    margin-top: 1px;
}

/*ggg**/
.pop_fb_popup {
    background: #408dc1;
    width: 100%;
    margin: 50px auto 0;
    display: none;
    max-width: 738px;
    z-index: 500;
    color: #f4d03e;
    font-family: 'Montserrat(OTT)', sans-serif;
    border-radius: 15px
}
.popbox_fb_popup {
    padding: 50px 28px 15px;
    font-size: 18.03px
}
.pop_fb_popup {
    width: auto;
    margin: 50px 10px
}
.popbox_fb_popup {
    font-size: 14.03px
}    
/*ggg**/
    
</style>


<!--FB Posting Popup Msg -->
<!--
<div class="pop_fb_popup" id="fbPostPopup" style="display:none;position:fixed;z-index:100000;">
-->
<div class="pop_fb_popup" id="fbPostPopup" style=" display: block;    left: 230px;    position: fixed;    top: 192.5px;    z-index: 100000;">
    <div class="popbox_fb_popup">
       <a style="display:inline;" title="Fermer" onclick="javascript:disablePopupGeneral('fbPostPopup','');" id="fancybox-close" style="background: url(../images/fancy_close.png);"></a>
      <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
        <tr>
          <td align="center"> <div class="fblpopup"><?=$parObj->_getLabenames($arrayData,'refFbPostPopupTxt','name');?><a href="referrel_system.php" target="_blank" title="<?php echo $parObj->_getLabenames($arrayData,'refPopupUrlCaptn','name'); ?>">(<?=$parObj->_getLabenames($arrayData,'refFbPostMoreInfo','name');?>)</a></div></td>
        </tr>
        <tr>
        	<td><textarea readonly="readonly" class="popupTextArea" id="refPopupTxtArea"><?php echo $refTxt; ?></textarea></td>
        </tr>
        <tr>
          <td align="center">
          	<div class="sharing">
                <a id="fbPostBtnId" href="javascript:<?if(USER_STATUS	!= "Allowed"){?>fb_login()<?}else{?>fbPostNew()<?}?>;"> 
                 
                    	<img src="<?=ROOT_FOLDER?>images/facebook.png" style="float:left;">
                    
                   
                    	<?=$parObj->_getLabenames($arrayData,'reffSubBtn_FB','name');?>
                    
                </a>
			</div>
         </td>
        </tr>
        <tr>
        	<td>
                <span id="fbChkCntrPopup" style="float:left;">
                    <input type="checkbox" name="dontShow" id="fbChkBxPopup" />
                </span>
                <div style="padding-left: 19px;">
                    <?=$parObj->_getLabenames($arrayData,'refFbDontAskTxt','name');?>
                </div>
            </td>
        </tr>
      </table>
      <div class="clear"></div>
    </div>
</div>

<!--FB Posting Popup Msg Ends -->


<script type="text/javascript" src="<?=ROOT_FOLDER?>includes/js/jquery.oauthpopup.js"></script>
<script type="text/javascript" src="<?=ROOT_FOLDER?>includes/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="<?=ROOT_FOLDER?>includes/js/jquery.quicksearch.js"></script>
<script type="text/javascript" src="<?=ROOT_FOLDER?>includes/js/jquery.limit-1.2.js"></script>
<script type="text/javascript" src="<?=ROOT_FOLDER?>includes/js/referrelSystem.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		referelSystem.init();
		<?if(USER_STATUS	== "Allowed"){?>referelSystem.fbInit();<?}?>
		referelSystem.chkTw(<?=$tweetFlag?>);
		referelSystem.getTagged();
		
		$('#fbChkBxPopup').change(function () {
			orgin	=	fbPUParent;
			orginCnt	=	"fbChkCntrPopup";
			if ($(this).attr("checked")) {
				sts	=	1;
				isFbPosted	= 	1;
			}else{
				sts	=	0;
				isFbPosted	=	0;
			}
			referelSystem.setDataTpl(sts);
			referelSystem.updateChkSts(orgin,sts,orginCnt);
		});
		
	});
	
	function fbPostNew(){
		referelSystem.newFBPost();
	}
	
	function fb_login(){
		referelSystem.fbLog();
	}
	
	function setRefSts(fieldName){
		var xmlhttp;
		var resTxt;
		if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}else{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.open("GET","http://www.jiwok.com/ajaxGetRefSts.php?fieldName="+fieldName,false);
		xmlhttp.send();
		resTxt=xmlhttp.responseText;
		return resTxt;
	}
	
	
</script>
