<?php 
//Fb images and hover images based on the languages
if($lanId	==	1)
{	
	$fblancode			=	"en/";
	$fbImage			=	"fbLogin.png";
	$fbHoverImage		=	"fbLogin_hover.png";
	$fbRegImage			=	"fbLoginReg.png";
	$fbRegHoverImage	=	"fbLoginReg_hover.png";
}
elseif($lanId	==	2)
{
	$fblancode			=	"";
	$fbImage			=	"fbLogin_hover.png";
	$fbHoverImage		=	"fbLogin_hover.png";
	$fbRegImage			=	"fbLoginReg.png";
	$fbRegHoverImage	=	"fbLoginReg_hover.png";
}
elseif($lanId	==	3)
{
	$fblancode			=	"es/";
	$fbImage			=	"fbLogin.png";
	$fbHoverImage		=	"fbLogin_hover.png";
	$fbRegImage			=	"fbLoginReg.png";
	$fbRegHoverImage	=	"fbLoginReg_hover.png";
}
elseif($lanId	==	4)
{
	$fblancode			=	"it/";
	$fbImage			=	"fbLogin.png";
	$fbHoverImage		=	"fbLogin_hover.png";
	$fbRegImage			=	"fbLoginReg.png";
	$fbRegHoverImage	=	"fbLoginReg_hover.png";
}
elseif($lanId	==	5)
{
	$fblancode			=	"pl/";
	$fbImage			=	"fbLogin_pl.png";
	$fbHoverImage		=	"fbLogin_hover_pl.png";
	$fbRegImage			=	"fbLoginReg_pl.png";
	$fbRegHoverImage	=	"fbLoginReg_hover_pl.png";
}
else
{
	$fblancode			=	"";
	$fbImage			=	"fbLogin.png";
	$fbHoverImage		=	"fbLogin_hover.png";
	$fbRegImage			=	"fbLoginReg.png";
	$fbRegHoverImage	=	"fbLoginReg_hover.png";
}

?>
<style>
.fb_button, .fb_button_rtl {
    background: none repeat scroll 0 0 transparent !important;
}
<?php if($fbLogin->fbMode=="loginFailed"){ ?>
.fbSecond .fb_button .fb_button_text, .fbSecond .fb_button_rtl .fb_button_text {
	background: url("http://www.jiwok.com/images/<?=$fbRegImage;?>") no-repeat scroll 0 0 transparent !important;
	display: block !important;
	font-family: "lucida grande",tahoma,verdana,arial,sans-serif !important;
	font-weight: bold !important;
	margin: 1px 1px 0 21px !important;
	padding: 0 141px 34px !important;
	text-shadow: none !important;
	border-bottom: 0px solid #1A356E !important;
	border-top: 0px solid #879AC0 !important;
	color: transparent !important;
	font-size: 0px !important;
}
.fbSecond .altFBLogin {
    background: url("http://www.jiwok.com/images/<?=$fbRegImage;?>") no-repeat scroll 0 0 transparent !important;
    color: transparent !important;
    cursor: pointer;
    padding: 0 140px 36px !important;
}

.fbSecond .altFBLogin:hover {
    background: url("http://www.jiwok.com/images/<?=$fbRegHoverImage;?>") no-repeat scroll 0 0 transparent !important;
    color: transparent !important;
    cursor: pointer;
    padding: 0 140px 36px !important;
}
.fbSecond .fBLoginNew{
	background: url("http://www.jiwok.com/images/<?=$fbRegImage;?>") no-repeat scroll 0 0 transparent !important;
    color: transparent !important;
    cursor: pointer;
    padding: 0 140px 36px !important;
}
.fbSecond .fBLoginNew:hover{
	background: url("http://www.jiwok.com/images/<?=$fbRegHoverImage;?>") no-repeat scroll 0 0 transparent !important;
    color: transparent !important;
    cursor: pointer;
    padding: 0 140px 36px !important;
}
<?php }else if($fbLogin->fbMode=="registration"){ ?>
.fb_button .fb_button_text, .fb_button_rtl .fb_button_text {
	background: url("http://www.jiwok.com/images/<?=$fbRegImage;?>") no-repeat scroll 0 0 transparent !important;
	display: block !important;
	font-family: "lucida grande",tahoma,verdana,arial,sans-serif !important;
	font-weight: bold !important;
	margin: 1px 1px 0 21px !important;
	padding: 0 141px 34px !important;
	text-shadow: none !important;
	border-bottom: 0px solid #1A356E !important;
	border-top: 0px solid #879AC0 !important;
	color: transparent !important;
	font-size: 0px !important;
}
.altFBLogin {
    background: url("http://www.jiwok.com/images/<?=$fbRegImage;?>") no-repeat scroll 0 0 transparent !important;
    color: transparent !important;
    cursor: pointer;
    padding: 0 140px 36px !important;
}

.altFBLogin {
    background: url("http://www.jiwok.com/images/<?=$fbRegImage;?>") no-repeat scroll 0 0 transparent !important;
    color: transparent !important;
    cursor: pointer;
    padding: 0 140px 36px !important;
}

.altFBLogin:hover {
    background: url("http://www.jiwok.com/images/<?=$fbRegHoverImage;?>") no-repeat scroll 0 0 transparent !important;
    color: transparent !important;
    cursor: pointer;
    padding: 0 140px 36px !important;
}
.fBLoginNew{
	background: url("http://www.jiwok.com/images/<?=$fbRegImage;?>") no-repeat scroll 0 0 transparent !important;
    color: transparent !important;
    cursor: pointer;
    padding: 0 140px 36px !important;
}
.fBLoginNew:hover{
	background: url("http://www.jiwok.com/images/<?=$fbRegHoverImage;?>") no-repeat scroll 0 0 transparent !important;
    color: transparent !important;
    cursor: pointer;
    padding: 0 140px 36px !important;
}

<?php }else{ ?>

.fb_button .fb_button_text, .fb_button_rtl .fb_button_text {
    background: url("http://www.jiwok.com/images/<?=$fbImage;?>") no-repeat scroll 0 0 transparent  !important;
	display: block !important;
    font-family: "lucida grande",tahoma,verdana,arial,sans-serif !important;
    font-weight: bold !important;
    margin: 1px 1px 0 21px !important;
    padding: 0 70px 12px !important;
    text-shadow: none !important;
	border-bottom: 0px solid #1A356E !important;
    border-top: 0px solid #879AC0 !important;
	color: transparent !important;
	font-size:0px !important;
}
.altFBLogin{
    background: url("http://www.jiwok.com/images/<?=$fbImage;?>") no-repeat scroll 0 0 transparent !important;
    color: transparent !important;
    cursor: pointer;
    padding: 0 70px 12px !important;
}
.altFBLogin:hover{
    background: url("http://www.jiwok.com/images/<?=$fbHoverImage;?>") no-repeat scroll 0 0 transparent !important;
    color: transparent !important;
    cursor: pointer;
    padding: 0 70px 12px !important;
}
.fBLoginNew{
	background: url("http://www.jiwok.com/images/<?=$fbImage;?>") no-repeat scroll 0 0 transparent !important;
    color: transparent !important;
    cursor: pointer;
    padding: 0 70px 12px !important;
	float: left;
    height: 12px;
}
.fBLoginNew:hover{
	background: url("http://www.jiwok.com/images/<?=$fbHoverImage;?>") no-repeat scroll 0 0 transparent !important;
    color: transparent !important;
    cursor: pointer;
    padding: 0 70px 12px !important;
	float: left;
    height: 12px;
}
<?php } ?>
.fblpopup{
	font-size: 11px;
	padding-top:5px;
	padding-bottom:22px;
}
.refCptn{
	font-size: 13px;
    font-weight: bold;
    padding-bottom: 20px;
    width: 326px;
}
</style>
<?php

 if($fbLogin->fbMode=="registration"){ ?>
    <div class="refCptn"><?php echo $contentObj->_getLabenames($fbLoginAry,'newTooltipTxt','name'); ?></div>
    <div style="display:none;"><img src="http://www.jiwok.com/images/<?=$fbRegHoverImage;?>" /></div>
<?php }else if($fbLogin->fbMode=="loginFailed"){ ?>
    <div style="display:none;"><img src="http://www.jiwok.com/images/<?=$fbRegHoverImage;?>" /></div>
<?php }else{ ?>
	<div style="display:none;"><img src="http://www.jiwok.com/images/<?=$fbHoverImage;?>" /></div>
<?php } ?>
<?php
if ($showBtn) { // Check whether the user fb user set and user tried to login 
	if($user_profile["email"]!=""){?>
		<span class="altFBLogin" <?php if($fbLogin->fbMode=="registration"){ ?> title="<?php echo $contentObj->_getLabenames($fbLoginAry,'newTooltipTxt','name'); ?>" <?php } ?>></span>
	<?php }else{ ?>
    	<span class="fBLoginNew" <?php if($fbLogin->fbMode=="registration"){ ?> title="<?php echo $contentObj->_getLabenames($fbLoginAry,'newTooltipTxt','name'); ?>" <?php } ?>></span>
    	<span class="fb-btn" <?php if($fbLogin->fbMode=="registration"){ ?> title="<?php echo $contentObj->_getLabenames($fbLoginAry,'fb_label','name'); ?>" <?php } ?>></span>
		<!--<fb:login-button scope="email,user_birthday,publish_stream" autologoutlink="true"></fb:login-button>-->
	<?php }
}
?>
    <div id="fb-root" ></div>
    
    
    <div class="popup" id="fbLoginPopup" style="display:none;position:fixed;z-index:100000;">
	  <div><img src="http://www.jiwok.com/images/pop-top.png" alt="jiwok" /></div>
	  <div class="inner">
		<table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
		  <tr>
			<td align="center"> <div class="fblpopup"><?php echo $popupMsg; ?></div></td>
		  </tr>
		  <tr>
			<td align="center">
            <a id="okid"><input type="button" class="bu_03" name="renewSubscriptionIdBtn" value="<?php echo $popupYes; ?>"></a>
            <a id="cancelid" style="margin-left: 45px;"><input type="button" class="bu_03" name="renewSubscriptionIdBtn" value="<?php echo $popupNo; ?>"></a></td>
		  </tr>
		</table>
		<div class="clear"></div>
	  </div>
	  <div><img src="http://www.jiwok.com/images/pop-btm.png" alt="jiwok" /></div>
	</div>
    
    <script type="text/javascript">
		var isClicked	=	0;
		var ajaxUrl	=	"http://www.jiwok.com/tools/fbconnect/examples/ajaxGetFbUserData.php";
		$(document).ready(function(){
			<?php if($userRegConfirm){ ?>
				showPopup("fbLoginPopup","");
			<?php } ?>
			$('.fbSecond .altFBLogin').click(function() {
			  	window.location =	"http://www.jiwok.com/<?php echo $fblancode;?>userreg1.php?fbLogin=1"
			});
			$('.altFBLogin').click(function() {
			  	window.location =	"http://www.jiwok.com/<?php echo $fblancode. $targt; ?>?fbLogin=1"
			});
			$('.fbSecond .fBLoginNew').unbind('click').click(function() {
			  	getLoginFBReg();
			});
			$('.fBLoginNew').unbind('click').click(function() {
			  	getLoginFB();
			});
			
			function getLoginFBReg(){
				FB.login(function(response){
					if (response.authResponse){
						if (response.authResponse){
							var access_token = response.authResponse.accessToken;
							var uid = response.authResponse.userID;
							window.location =	"http://www.jiwok.com/<?php echo $fblancode. $targt; ?>?fbLogin=1";
						}
					}
				},{scope:'email,user_birthday,publish_stream'});
			}
			function getLoginFB(){
				FB.login(function(response){
					if (response.authResponse){
						if (response.authResponse){
							var access_token = response.authResponse.accessToken;
							var uid = response.authResponse.userID;
							window.location =	"http://www.jiwok.com/<?php echo $fblancode.$targt; ?>?fbLogin=1";
						}
					}
				},{scope:'email,user_birthday,publish_stream'});
			}

		});
		$("#cancelid").click(function(){
			disablePopupGeneral("fbLoginPopup","");
			commonAjax(ajaxUrl,"action=updateLoginSts&user_id=<?php echo $userId; ?>&sts=0",2);
		});
		$("#okid").click(function(){
			commonAjax(ajaxUrl,"action=updateLoginSts&user_id=<?php echo $userId; ?>&sts=1",1);
		});
		
	</script>
    
    
    <script>               
	  window.fbAsyncInit = function() {
        	FB.init({
          		appId: '<?php echo $facebook->getAppID(); ?>', 
          		cookie: true, 
         		xfbml: true,
          		oauth: true
        	});
        	FB.Event.subscribe('auth.login', function(response) {
		  		//window.location =	"http://www.jiwok.com/<?php echo $targt; ?>?fbLogin=1"
        	});
        	FB.Event.subscribe('auth.logout', function(response) {
          		
        	});
      };
      (function() {
       		var e = document.createElement('script'); e.async = true;
       		e.src = document.location.protocol +
          	'//connect.facebook.net/en_US/all.js';
        	document.getElementById('fb-root').appendChild(e);
      }());

  	  function commonAjax(url,params,resultAction){
			var xmlhttp;
			var resTxt;
			if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
  				xmlhttp=new XMLHttpRequest();
  			}else{// code for IE6, IE5
  				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  			}
			xmlhttp.onreadystatechange=function(){
  				if (xmlhttp.readyState==4 && xmlhttp.status==200){
    				resTxt=xmlhttp.responseText;
					if((resultAction==1)||(resultAction==2)){
						 window.location = "http://www.jiwok.com/<?php echo $fblancode.$targt; ?>";
					}
				}
  			}
			xmlhttp.open("GET",url+"?"+params,true);
			xmlhttp.send();	
	  }
  	</script>
  </body>
</html>
