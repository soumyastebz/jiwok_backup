<?php
//Fb images and hover images based on the languages
if($lanId	==	1)
{	
	$fblancode			=	"en/";
	
}
elseif($lanId	==	2)
{
	$fblancode			=	"";
	
}
elseif($lanId	==	3)
{
	$fblancode			=	"es/";
	
}
elseif($lanId	==	4)
{
	$fblancode			=	"it/";
	
}
elseif($lanId	==	5)
{
	$fblancode			=	"pl/";
	
}
else
{
	$fblancode			=	"";
	
}
?>
<style>
<!--
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
.

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


<?php } ?>
.fblpopup{
	font-size: 11px;
	padding-top:5px;
	padding-bottom:22px;
-->
<!--
}
-->
.refCptn{
	font-size: 13px;
    font-weight: bold;
    padding-bottom: 20px;
    width: 326px;
}
</style>
<?php if($fbLogin->fbMode=="registration"){ ?>
    <div class="refCptn"><?php echo $contentObj->_getLabenames($fbLoginAry,'newTooltipTxt','name'); ?></div>
    <div style="display:none;"></div>
<?php }else if($fbLogin->fbMode=="loginFailed"){ ?>

    <div style="display:none;"></div>


<?php }else{ ?>
	<div style="display:none;"></div>
<?php } ?>
<?php
if ($showBtn) { // Check whether the user fb user set and user tried to login 
	if($user_profile["email"]!=""){?>
<!--
		<span class="altFBLogin" <?php if($fbLogin->fbMode=="registration"){ ?> title="<?php echo $contentObj->_getLabenames($fbLoginAry,'newTooltipTxt','name'); ?>" <?php } ?>></span>
-->
	<span class="fb-btn" id="altFBLogin"><?php if($fbLogin->fbMode=="registration"){ ?> <?php echo $contentObj->_getLabenames($fbLoginAry,'fb_label','name'); ?> <?php } ?></span>
	<?php }else{?> 
    	<span class="fb-btn"><?php if($fbLogin->fbMode=="registration"){ ?> <?php echo $contentObj->_getLabenames($fbLoginAry,'fb_label','name'); ?> <?php } ?></span>
		<!--<fb:login-button scope="email,user_birthday,publish_stream" autologoutlink="true"></fb:login-button>-->
	<?php }
}
?>
    <div id="fb-root" ></div>
    
    


         <!-----pop up--->
       
       <section class="pop" id="fbLoginPopup"> <img src="images/close.png" alt="close" class="close b-modal __b-popup1__">
            <div class="popbox">

        <table width="100%" border="0" cellspacing="2" cellpadding="0" >
		  <tr>
			<td align="center"> <div ><?php echo $popupMsg; ?></div></td>
		  </tr>
		  <tr>
			<td align="center">

           <input type="button"  onClick="getokid();" id="okid" name="renewSubscriptionIdBtn" value="<?php echo $popupYes; ?>">
          <input type="button" onClick="getcancelid();" id="cancelid" name="renewSubscriptionIdBtn" value="<?php echo $popupNo; ?>"></td>
		  </tr>
		</table>
         </div> </section>
       <!--pop up ends-->
       <script src="http://code.jquery.com/jquery-2.1.0.min.js"></script>
    <script type="text/javascript" src="<?php echo JIWOK_URL; ?>js/jquery.bpopup.min.js"></script>
    <script type="text/javascript" src="<?php echo JIWOK_URL; ?>js/jquery.easing.1.3.js"></script>  
    <script type="text/javascript">
		var isClicked	=	0;
		//var ajaxUrl	=	"<?php echo JIWOK_URL; ?>/tools/fbconnect/examples/ajaxGetFbUserData.php";
			var ajaxUrl	=	"http://beta.jiwok.com/tools/fbconnect/examples/ajaxGetFbUserData.php";
		jQuery.noConflict();
		jQuery(document).ready(function(){
			//alert(1);
			<?php if($userRegConfirm){ ?>
		 jQuery('.pop').bPopup({
	        easing: 'easeOutBounce', 
            speed: 2000,
            transition: 'slideDown'
        });
           
			<?php } ?>
			//alert(2);
			//~ jQuery("#okid").click(function(){ 			alert(3);return false;
			//~ //commonAjax(ajaxUrl,"action=updateLoginSts&user_id=<?php echo $userId; ?>&sts=1",1);
			//~ });
			//~ jQuery("#cancelid").click(function(){  alert(4);
				//~ //disablePopupGeneral("fbLoginPopup","");
				//~ //commonAjax(ajaxUrl,"action=updateLoginSts&user_id=<?php echo $userId; ?>&sts=0",2);
			//~ });
			jQuery('.fbSecond #altFBLogin').click(function() {
			  	window.location =	"<?php echo JIWOK_URL; ?><?php echo $fblancode;?>userreg1.php?fbLogin=1"
			});
			jQuery('#altFBLogin').click(function() {
			  	window.location =	"<?php echo JIWOK_URL; ?><?php echo $fblancode. $targt; ?>?fbLogin=1"
			});
			jQuery('.fbSecond .fb-btn').unbind('click').click(function() {
			  	getLoginFBReg();
			});
			jQuery('.fb-btn').unbind('click').click(function() { 
			  	getLoginFB();
			});
			
			function getLoginFBReg(){ 
				FB.login(function(response){
					if (response.authResponse){
						if (response.authResponse){
							var access_token = response.authResponse.accessToken;
							var uid = response.authResponse.userID;
							window.location =	"<?php echo JIWOK_URL; ?><?php echo $fblancode. $targt; ?>?fbLogin=1";
						}
					}
				},{scope:'email,user_birthday'});
			}
			function getLoginFB(){ //
				FB.login(function(response){
					if (response.authResponse){ 
						if (response.authResponse){
							var access_token = response.authResponse.accessToken;
							var uid = response.authResponse.userID;
							window.location =	"<?php echo JIWOK_URL; ?><?php echo $fblancode.$targt; ?>?fbLogin=1";
						}
					}
				},{scope:'email,user_birthday'});
			}
		 
		
		});
		
		 function getokid()
		  {
			  
			  commonAjax(ajaxUrl,"action=updateLoginSts&user_id=<?php echo $userId; ?>&sts=1",1);
		  }
		function getcancelid()
		  {
			  
			 disablePopupGeneral("fbLoginPopup","");
			commonAjax(ajaxUrl,"action=updateLoginSts&user_id=<?php echo $userId; ?>&sts=0",2);
		  }
	              
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
    				//alert('ra:'+resultAction);
					if((resultAction==1)||(resultAction==2)){
						//alert('executed');
						 window.location = "<?php echo JIWOK_URL; ?><?php echo $fblancode.$targt; ?>";
					}
				}
  			} 
			xmlhttp.open("GET",url+"?"+params,true);
			xmlhttp.send();	
	  }
  	</script>
  	<!--pop up-->
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

  	<!--pop up-->
