<?php
session_start();
if($_SESSION['user']['userId']==''){
	header('location:index.php');
}
else{
	$userId	=	$_SESSION['user']['userId'];
}

$lanId	=	2;

/*ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);*/

require_once 'includeconfig.php';
require_once 'includes/classes/Referrel/class.referal.php';
//require_once 'includes/classes/Referrel/TwitterV1.1/TwitterAPIExchange.php';
require_once 'includes/classes/Referrel/Facebook/facebook.php';
require_once 'includes/classes/Referrel/OpenInviter/openinviter.php';
include_once("includes/classes/class.programs.php");
include_once("includes/classes/class.discount.php");

//$parObj 		=  new Contents('userreg1.php');
$referal		=	new referal();
$parObj 		= new Contents('referrel_system.php');
$objPgm     	= 	new Programs($lanId);
$objDisc		= 	new Discount($lanId);



$returnData		= $parObj->_getTagcontents($xmlPath,'referralSectionNew','label');
$arrayData		= $returnData['general'];

$referal->debugQuery	=	false;

//////////////////////////// Google Shortner API////////////////////////////////////////////

	$userToken		=	$referal->getUserToken();
	$makeUrl		=	"www.jiwok.com/index.php?utm_source=referal&utm_medium=referal&utm_term=referal&utm_campaign=referal&referrer=".$userToken."&media=fb";
     $snippedUrl		=	$referal->googleApiCall($makeUrl);
$fbMessagenewtype	=	$referal->getFbMessage($mesFb);
$fbMessagenewtype1 = $fbMessagenewtype['link'];

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

	$tweetDetails	=	$referal->twConnect();//echo "mm";print_r($tweetDetails);exit;
	if(is_array($tweetDetails)){//Check for user Approval
		$tweetFlag	=	"1";
	}
	else{ 
		$tweetFlag	=	"0";
	}

//////////////////////TWITTER TWEETS END///////////////////////////////

/////////////////////Open Inviter DropDown////////////////////////////////

	$inviter	=	new openinviter();			
	$pluginList	=	$inviter->getPlugins();
	$dropDown	=	$referal->getDropDownInviter($pluginList);	
		
/////////////////////Open Inviter DropDown End///////////////////////////

if($objPgm->_checkUserPaymentPeriod($userId)){
  	$finalFormat	=	$objPgm->_getPaymentExpDate($userId);
	$Payed			=	1;
}else{
	$Payed			=	0;
}

$unSub					=	$objDisc->_isUnSubscribed($userId);
//user's payment version 
$paymentVersion 		=	usersPaymentVersion($userId);
$userNewPaymentStatus	=	$objPgm->checkUserPaymentStausCount($userId,$paymentVersion);
$newPaymentRegisterCount	=	$objPgm->newPaymentRegisterCount($userId,$paymentVersion);


$isNewPaybox	=	true;
$qry	=	"select * from payment_paybox where user_id=".$userId." order by id desc";
$payboxData  = mysql_query($qry);


$qry_stripe	=	"select * from stripe_payment where user_id=".$userId." order by id desc";
$payboxData_stripe = mysql_query($qry_stripe);
//echo $Payed."----".$unSub."-----".$userNewPaymentStatus."---------".$newPaymentRegisterCount;


if(mysql_num_rows($payboxData)>0){
	   $isNewPaybox	=	true;
	   $payboxUserId	=	$payboxRow["id"];
}else if($Payed==1 && $unSub == 0 && $userNewPaymentStatus== 0 && $newPaymentRegisterCount==0){
	$isNewPaybox	=	false;
}
else if(mysql_num_rows($payboxData)>0){
$isNewPaybox	=	true;
}
$fbSts	=	'checked="checked"';
$twtSts	= 	'checked="checked"';

$selQry	=	"select * from jiwok_refferel_wo where user_id=".$userId;
$dataArray		=	$referal->dbSelectAll($selQry);
if(count($dataArray) > 0){
	if($dataArray[0]["isfb"]=="0"){
		$fbSts	=	"";	
	}
	if($dataArray[0]["istw"]=="0"){
		$twtSts	=	"";	
	}
}

?>

		<?php include('header.php');?>
	       <!----body style starts-->
	
	<style type="text/css">
#disBtn{
	background: none repeat-x scroll 0 0 #CCCCCC !important;
    border: 0px solid #D67413 !important;
    display: block;
    height: 23px;
    line-height: 23px;
    padding: 0 5px;
    text-align: center;
    width: 170px;
}
#disSharing{
    background: none repeat scroll 0 0 #CCCCCC;
    border: 1px solid #CCCCCC;
    float: left;
    height: 23px;
    line-height: 23px;
    margin-right: 2px;
    padding: 0 5px;
    text-align: center;
    width: 150px;
}
.OldUserTxt{
	
}
.oldUserUrlTxt,.oldUserUrlTxt a{
    color: #FF4D09 !important;
    font-size: 14px !important;
    font-weight: bold !important;
    padding-bottom: 20px !important;
    padding-left: 20px !important;
    text-decoration: underline !important;
}
.cbkCntr{
    float: left;
    padding-top: 10px;
}
.fbChkCntr{
	float: left;
    width: 174px;
}
.twtChkCntr{
	float: left;
    margin-left: 80px;
    width: 174px;	
}
#loaderCnt{
	display:none;
	padding-bottom: 10px;
    text-align: center;	
}
/*.errMess{
	background-color: #F9ECB7;
	border:1px solid #DDCEB9;
	height:20px;
	width:70%;
	text-align:center;
	line-height:2;
	margin: 2px 150px 2px;
}*/
.msgTxt{
	width:60%;	
}
#clsImage{
	float:right;
	cursor:pointer;	
}
#popup{
	margin: 10px 0 50px 100px !important;
	margin: 50px 0 0px 0px !important;
}
#popupInner{
    border: 0px solid #D1E5F5 !important;
    margin: 0 auto 0px !important;
    padding-bottom: 0px !important;
    width: 579px !important;
	max-height:473px !important;
	overflow:auto;
	padding: 0px 0px 0 !important;
	padding-left: 10px !important;
}
.pop{
	background-color:#FFF !important;
	margin-left: 35px !important;	
}
.closeBtn{
    background: none repeat scroll 0 0 #22B3D2;
    padding-right: 25px;
    text-align: right;
    width: 565px;
}
.bu_norImg{
	cursor:pointer;
}
.blockOverlay{
	opacity : 0 !important;
}
.selClass{
	float: left;
    padding-top: 12px;
    width: 200px;
	color:#000 !important;
}
.selClass a{
	color:#000 !important;
}
.btnCntr{
	float: left;
    padding-left: 15px;
    padding-top: 5px;
}
.twtShr{
	margin-left:52px !important;
}
.indiBanner{
    color: #FF9B36;
    font-size: 56px;
    font-weight: bold;
	margin-left:307px;
    margin-top: 116px;
    position: absolute;
    z-index: 1000;
}
</style>
	
		
		<div class="frame-ref"><span id="twt"></span>
         <div class="row-1" style="padding-bottom:0"><div class="return">
<!--
            <a href="#" class="small">Retour</a>
-->
         </div>
         <div class="title">
         
           <h3><?=$parObj->_getLabenames($arrayData,'reffHeadTop','name');?> </h3>
      </div>
         
         </div>
         <div class="invite">
            <h3><?=$parObj->_getLabenames($arrayData,'reffHeadTop_1','name');?></h3>
            <?=$parObj->_getLabenames($arrayData,'reffHeadTopDesc','name');?> 
         
         </div>
         <!--- need to check-->
          <?php if(!$isNewPaybox){ ?> 
                <div class="oldUserUrlTxt">
                    <a href="payment_renew.php"> <?=$parObj->_getLabenames($arrayData,'refOldUserurlTxt','name');?> </a>
                </div>
            <?php } ?>

            <div id="loaderCnt"><span id="closeImage"></span></div>

			<div class="errMess" id="errMessId"></div>
			<div id="fb-root"></div>
			 <div class="frame-5">
            	<?php if(!$isNewPaybox){ ?> 
            		<div class="indiBanner"><?php if($lanId==5) { echo "Nie aktywne"; } else { echo "Non Activé";} ?></div>
                <?php } ?>
            <!--- need to check-->
        
            <div class="left">
                <h3><?=$parObj->_getLabenames($arrayData,'reffHead_1','name');?></h3>
                <p><?=$parObj->_getLabenames($arrayData,'reffInvTxt','name');?></p>
                <div class="row">
                  <span class="label"><?=$parObj->_getLabenames($arrayData,'reffEmail','name');?></span>
                  <span class="field"><input  name="" type="text" class="txt-fld"  id="mail" <?php if(!$isNewPaybox){?>disabled="disabled" <?php } ?>></span>
  
                </div>
               <div class="row">
                  <span class="label"><?=$parObj->_getLabenames($arrayData,'reffMailPwd','name');?></span>
                  <span class="field"><input name="input" type="password" class="txt-fld" id="mailPass" <?php if(!$isNewPaybox){?>disabled="disabled" <?php } ?> /></span>
                  
                </div>
                <!--for assigning mail provider-->
                <input type="hidden" name="mailProvider" id="mailProvider" value="">
      <!--- need to check-->
<!--
				<div>Provider</div>
				<div id="provider"><?=$dropDown?></div>
-->
      <!--- need to check-->
            <div class="control" >


				<span <?php if($isNewPaybox){?>class="hotbutton-content"<?php }else{?>id="disBtn"<?php } ?>>
                                	<?php if($isNewPaybox){?><a href="javascript:;" id="retrieve"><?php } ?>
										
                                     <?php if($isNewPaybox){?><input type="button" class="btn_orng4" value="<?=$parObj->_getLabenames($arrayData,'reffSubBtn_1','name');?>"></a><?php } ?>
                                 </span>


<!--
<span <?php if($isNewPaybox){?>class="hotbutton-content"<?php }else{?>id="disBtn"<?php } ?>>
                                	<?php if($isNewPaybox){?><a href="javascript:;" id="retrieve"><?php } ?>
										<?=$parObj->_getLabenames($arrayData,'reffSubBtn_1','name');?>
                                     <?php if($isNewPaybox){?></a><?php } ?>
                                 </span>
-->
                                 </div>
            
            
            
            
            
            <p><?=$parObj->_getLabenames($arrayData,'reffInvTxt_1','name');?></p>
            <div class="row"><textarea name="email_type" id="email_type" class="txt-area" <?php if(!$isNewPaybox){?>disabled="disabled" <?php } ?>></textarea></div>
           
             <div class="control"><span <?php if($isNewPaybox){?>class="hotbutton-content"<?php }else{?>id="disBtn"<?php } ?>>
							<?php if($isNewPaybox){?><a href="javascript:;" id="sendType"> <?php } ?> 
                               
                           <?php if($isNewPaybox){?><input type="button" class="btn_orng4" value=" <?=$parObj->_getLabenames($arrayData,'reffSubBtn_2','name');?>"></a> <?php } ?>
                    	</span></div>
            </div>
           <div class="right">
              <h3><?=$parObj->_getLabenames($arrayData,'reffHead_2','name');?></h3>
              <textarea name="fbt_share" id="fbt_share" class="txt-area" <?php if(!$isNewPaybox){?>disabled="disabled" <?php } ?>><?=$parObj->_getLabenames($arrayData,'reffFbTxt','name');?> <?=$snippedUrl?></textarea>
             
              <p align="right"><span id="charLeft">140</span> <?=$parObj->_getLabenames($arrayData,'reffRemChar','name');?></p>
              <div class="share">
                <div class="lft" style="width: 55%;">
                 <span <?php if($isNewPaybox){?>class="sharing"<?php }else{?>id="disSharing"<?php } ?>>
							<?php if($isNewPaybox){?> <a href="javascript:fb_login();"> <?php } ?>
								<img src="images/facebook.png" /> 
							<?php if($isNewPaybox){?><input type="button" class="btn" value="<?=$parObj->_getLabenames($arrayData,'reffSubBtn_FB','name');?>" ></a> <?php } ?>
						
						  <input type="hidden" name="new_fb_link" id="new_fb_link" value="<? echo $fbMessagenewtype1; ?>"  />
						</span>
                 
                  <div class="chcks2">
<ul>
<!--
                      <label class="label_check" for="checkbox-03" id="fbChkCntr"  >
 	<span class="fbChkCntr"><input name="fbChkBx"  id="fbChkBx"  value="fbchked" <?php echo $fbSts; ?><?php if(!$isNewPaybox){?>disabled="disabled" <?php } ?>  type="checkbox" ></span> </label><span><?=$parObj->_getLabenames($arrayData,'refChkBthTxt','name');?></span>
-->
                

       
<!--
                  <div class="fbChkCntr">
                    	
							 <label class="label_check" for="checkbox-03" id="fbChkCntr"  ><span id="fbChkCntr">
                    		<input type="checkbox" name="fbChkBx" id="fbChkBx" value="fbchked" <?php echo $fbSts; ?>  <?php if(!$isNewPaybox){?>disabled="disabled" <?php } ?> />
                    		
                        </span></label>
						<?=$parObj->_getLabenames($arrayData,'refChkBthTxt','name');?>
                    </div>    
-->
    
					 <!--   <span class="fbChkCntr"><input type="checkbox" id="terms" name="terms" value="fbchked" <?php echo $fbSts; ?>  <?php if(!$isNewPaybox){?>disabled="disabled" <?php } ?> />   
						</span> -->
                    <li class="checkboxFive">
<input type="checkbox" id="checkboxFiveInput" name="terms" value="fbchked" <?php echo $fbSts; ?>  <?php if(!$isNewPaybox){?>disabled="disabled" <?php } ?> />   
	  	<label for="checkboxFiveInput"></label></li>	<li class="fbChkCntrnew"><?=$parObj->_getLabenames($arrayData,'refChkBthTxt','name');?></li>
  	                    </ul>
                  </div>


 	      
                </div>  <input type="hidden" name="repSniperUrl" id="repSniperUrl" value="<?=$snippedUrl?>"  />
                <div class="rit">
                 <span <?php if($isNewPaybox){?>class="sharing twtShr"<?php }else{?>id="disSharing" class="twtShr"<?php } ?>>
							<?php if($isNewPaybox){?><a href="javascript:;"><?php } ?>
							
							<?php if($isNewPaybox){?> <input type="button" class="btn" value="<?=$parObj->_getLabenames($arrayData,'reffSubBtn_TW','name');?>"></a> <?php } ?>
						</span>
						
                  
                  <div class="chcks2">
<!--
                      <label class="label_check" for="checkbox-04" id="twtChkCntr">
	<span id="twtChkCntr"> 
 <input name="twtChkBx" id="twtChkBx" value="twtchked" type="checkbox" <?php echo $twtSts; ?> <?php if(!$isNewPaybox){?>disabled="disabled" <?php } ?>></span> </label><span><?=$parObj->_getLabenames($arrayData,'refChkBthTxt','name');?> </span>
-->
                  <ul>
                    <li class="checkboxFive">
<input type="checkbox" id="checkboxFiveInput1" name="checkboxFiveInput1" value="fbchked" <?php echo $twtSts; ?>  <?php if(!$isNewPaybox){?>disabled="disabled" <?php } ?> />   
	  	<label for="checkboxFiveInput1"></label></li>	<li class="fbChkCntrnew"><?=$parObj->_getLabenames($arrayData,'refChkBthTxt','name');?></li>
  	                    </ul>
                  </div>
                  
                  
                  
                  
                </div>
               
              </div>
           </div>
           <div class="clear"></div>
           <p> <?=$parObj->_getLabenames($arrayData,'reffBtmTxt','name');?><input type="text" readonly value="<?=$snippedUrl?>"  class="blu-field" name="fbTweet" <?php if(!$isNewPaybox){?>disabled="disabled" <?php } ?>> </p>
          
           <div class="training_JI">
           <h3><?=$parObj->_getLabenames($arrayData,'refOfferDescTxt','name');?></h3>
           <p>
             <?php
				// Total Referance for the user
                $refCnt	=	$referal->getReferralCnt($userId);
			
				// Total Referance group/month for the user
                $refData	=	$referal->getReferralSts($userId);
				
                $i=0;
                $groupCnt	=	1;
                $groupGainCnt	=	1;
                $groupCntId	=	0;
                $monthsEarned	=	0; 
                
                $refgainTmp	=	'<img src="images/star-orang.png" alt="star">';    // Icon indicating the Month gain
                $refTmp		=	'<img src="images/refIconBlu.png" alt="star">';	   // Icon indicating the Referance gain
                $refRemTmp	=	'<img src="images/refIconBluRem.png" alt="star">';  // Dim icon
                
                $perRowCnt	=	34;  // Number of icon per row
                
				// Find the count of dom icon to fill one more row
                if(($refCnt%$perRowCnt)!=0){
                    $refRem	=	(2*$perRowCnt)-($refCnt%$perRowCnt);
                }else{
                    $refRem	=	$perRowCnt;
                }
					
                // Number of referance to Gain one months
                $refGainCnt	=	$referal->getRefCount();
                
								
                for($i=0;$i<($refCnt+$refRem);$i++){  //Loop for number of referance+count of dim icons
                    if($i<$refCnt){ //Referance or nor
                        if($groupCntId<=(count($refData)-1)){  // Check whether its alraedy gained or not
                            if($refData[$groupCntId]["cnt"]==$groupCnt){ // Gain icon or not
                                echo $refgainTmp;
                                $groupCnt	=	0;
                                $groupCntId++;
                                $monthsEarned++;
                            }else{
                                echo $refTmp;
                            }
                        }else{
                            if($refGainCnt==$groupGainCnt){
                                echo $refgainTmp;
                                $groupGainCnt	=	1;
								$monthsEarned++;
                            }else{
                                echo $refTmp;
                                $groupGainCnt++;
                            }
                        }
                    }else{
                        echo $refRemTmp;
                    }
                    $groupCnt++;
                }
                
                ?>  
            
           </p>
           <small> <?php
					$mnthTxt	=	$parObj->_getLabenames($arrayData,'refMonthErnTxt','name');
					if($monthsEarned>1){
						$mnthTxt	=	$parObj->_getLabenames($arrayData,'refMonthErnTxts','name');
					}
				?>
            	<div class="clear"></div>
            		<div>
                		<?php echo $monthsEarned." ".$mnthTxt; ?>
                	</div>  </small>
         </div>
           <!---pop up need to add new design-->
           
           <div class="popup" id="popup" style="z-index: 100000;display:none;" >
                  <div><img src="images/pop-top.png" alt="jiwok" width="590px"></div>
                  <div class="closeBtn"><img src="images/closeTiny.png" class="bu_norImg"></div>
                  <div class="inner" id="popupInner">
                  <h2><?=$parObj->_getLabenames($arrayData,'reffCntHead','name');?></h2>
                                <p><?=$parObj->_getLabenames($arrayData,'reffCntTxt','name');?></p>
                                <div class="selecton">
                                	<div class="selClass">
                                    <a href="javascript:;"><?=$parObj->_getLabenames($arrayData,'unSelect','name');?></a> |
                                    <a href="javascript:;"><?=$parObj->_getLabenames($arrayData,'selectAll','name');?></a>
                                    </div>
                                    <div class="btnCntr">
                                    	<input name="" type="button" value="Invite 0 friends" class="bu_blu" />
                                    </div>
                                    <div class="searchRefMail">
                                        <input name="srch"  id="srch" type="text" class="tfl" />
                                        <a href="javascript:;">
                                            <img src="../images/search_icon.jpg" />
                                        </a>
                                    </div>
                                </div>
                                <table width="400" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td></td>
                                        <td align="right" valign="top">
                                            <input name="" type="button" value="Invite 0 friends" class="bu_blu" />
                                            <input name="input" type="button" value="<?=$parObj->_getLabenames($arrayData,'reffCanlTxt','name');?>" class="bu_nor" />
                                        </td>
                                    </tr>
                                </table>
                    <div class="clear"></div>
                  </div>
                  <div><img src="images/pop-btm.png" alt="jiwok" width="590px"></div>
			</div>
           <!---pop up-->
           
        
      </div> </div>
      	<!----body style ends-->
      <!--- script from designer-->
     
		
		 <!--- script from designer-->
		
		
		    <?php include("reffCommon.php"); ?>
        <input type="hidden" id="chkCntTxt" value="<?=$parObj->_getLabenames($arrayData,'refInvBtnTxt','name');?>"  />
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/jquery-ui.min.js"></script>
		<script type="text/javascript" src="includes/js/jquery.oauthpopup.js"></script>

<!--
<script type="text/javascript" src="includes/js/jquery-1.2.3.js"></script>
-->


		<script type="text/javascript" src="includes/js/jquery.blockUI.js"></script>




		<script type="text/javascript" src="includes/js/jquery.quicksearch.js"></script>
	<!--
	<script type="text/javascript" src="includes/js/jquery.limit-1.2.js"></script>
		-->
	
		<script type="text/javascript" src="includes/js/referrelSystem_nee.js"></script>
		 <script type="text/javascript">
		 	function fb_login(){ 
				referelSystem.fbLog();
			}
	

		$(document).ready(function(){
			
			var tweetflag = "<?=$tweetFlag?>";
			referelSystem.init();
			<? if(USER_STATUS	== "Allowed"){?>referelSystem.fbInit();<? } ?>
			
			referelSystem.chkTw(<?=$tweetFlag?>);
			//referelSystem.getTagged(); 
			//referelSystem.manageChBx(0);
		});
		
		
		
	</script>
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
            if (l.className.indexOf('label_') == -1) continue;
            var inp = gebtn(l,'input')[0];
            if (l.className == 'label_check') {
                l.className = (safari && inp.checked == true || inp.checked) ? 'label_check c_on' : 'label_check c_off';
                l.onclick = check_it;
            };
            if (l.className == 'label_radio') {
                l.className = (safari && inp.checked == true || inp.checked) ? 'label_radio r_on' : 'label_radio r_off';
                l.onclick = turn_radio;
            };
        };
    };
   var check_it = function() {

        var inp = gebtn(this,'input')[0];
        if (this.className == 'label_check c_off' || (!safari && inp.checked)) {
            this.className = 'label_check c_on';
            if (safari) inp.click();
        } else {
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
</script>

     <script src="js/flaunt.js"></script>
		<!---newly added js-->
		

<!--
		<script type="text/javascript" src="<?=ROOT_FOLDER?>resources/popup/fancybox/jquery.fancybox-1.3.4.pack.js" ></script>
        <script type="text/javascript" src="<?=ROOT_FOLDER?>resources/popup/fancybox/jquery.mousewheel-3.0.4.pack.js" ></script>
-->
		<!---newly added js-->
		<?php include('footer.php');
		//to get user's paid version 
		function usersPaymentVersion($userId)
		{
			$query 	= 	"SELECT `version` FROM payment WHERE payment_userid=".addslashes($userId)." AND payment_status = '1'  ORDER BY `payment_id` DESC LIMIT 0,1 ";
			$r = $GLOBALS['db']->getRow($query, DB_FETCHMODE_ASSOC);			
			return $r['version'];	
		}?></div>

