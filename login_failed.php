<?php 
	
	session_start();
	include_once('includeconfig.php');
	if($lanId=="") $lanId=1;
		 
	$objGen     	= new General();
	$parObj 		= new Contents('login_failed.php');
				
	//collecting data from the xml for the static contents
	
	$returnData		= 	$parObj->_getTagcontents($xmlPath,'registrationUser','label');   // Get content from XML
	$arrayData		= 	$returnData['general'];
	if($_REQUEST['returnrh'] != "")// for running hero page after login
	{		
		$returnrh = base64_decode($_REQUEST['returnrh']);
	}
	if((isset($_SESSION['user']['userId'])) && ($returnrh == 'runninghero'))
	{
		header('location:RunningHero/rh-jiwok.php',true,301);exit;
	}
	elseif(isset($_SESSION['user']['userId']))
	{
		header('location:index.php',true,301);
	}
	$returnDataGen		= $parObj->_getTagcontents($xmlPath,'loginfailed','label');
	$arrayDataLogin			= $returnDataGen['general'];
	
	
	//collecting data from the xml for the static contents
	$returnData			= $parObj->_getTagcontents($xmlPath,'loginfailed','messages');
	$errorData			= $returnData['errorMessage'];
	
	$urlParams	=	"";
	
	 $_REQUEST['returnUrl'] 	=	 htmlspecialchars(strip_tags(trim($_REQUEST['returnUrl'])));//echo "here";print_r($_REQUEST['returnUrl']);
	  $_REQUEST['fromPgm'] 	=	 htmlspecialchars(strip_tags(trim($_REQUEST['fromPgm'])));
	   $_REQUEST['forum'] 	=	 htmlspecialchars(strip_tags(trim($_REQUEST['forum'])));
	if($_REQUEST['returnUrl'] && !$_REQUEST['forum']){
		$urlParams			=	"?returnUrl=".$_REQUEST['returnUrl'];		
	}
	
	if($_REQUEST['fromPgm']==1){
		$_SESSION["userRegProgram"]	=	$_SESSION["userViewProgram"];
		$_SESSION["userRegProgramId"]	=	$_SESSION["userViewProgramId"];		
	}

	if($_REQUEST['returnUrl'] && !$_REQUEST['forum']){
		$_REQUEST['returnUrl']			=	$_REQUEST['returnUrl'];		
	}
	elseif($_REQUEST['forum'] == 1 && $_REQUEST['returnUrl'])
	{
		$ForumUrl			=	"http://www.jiwok.com/forum/";
		$_REQUEST['returnUrl']			= 	base64_encode($ForumUrl);	
	
	$return_forum = mysql_query("select session_page from forum_sessions where session_id='".mysql_real_escape_string($_SESSION['jiwokforum']['sid'])."'");
	$bk_2_forum = mysql_fetch_assoc($return_forum);
		
		/*if(!session_is_registered('ForumPage'))
		{ session_register('ForumPage');	}
		else { unset($_SESSION['ForumPage']); }*/
		$_SESSION['ForumPage'] = $bk_2_forum['session_page'];	
		
		/*if(!session_is_registered('returnPath'))
		{ session_register('returnPath');	}
		else { unset($_SESSION['returnPath']); }*/
		$_SESSION['returnPath'] = $_REQUEST['returnUrl'];	
	}	

	if($_REQUEST['msg']){ 
		$errorMsg	=	base64_decode($_REQUEST['msg']);
		
		switch($errorMsg){ 
		//for the faulier in the login from the 
		case 1:
		      $msg =  $parObj->_getLabenames($errorData,'invalid','name');
			  break;
	    //for the my account section		  
		case 2:	 
			  $msg =  $parObj->_getLabenames($errorData,'notcomplete','name');
			  break; 
		//for the my account section		  
		case 3:	 
			  $msg = $parObj->_getLabenames($errorData,'subscription','name');
			  break; 
			  //for the my account section		  
		case 4:	 
			  $msg = $parObj->_getLabenames($errorData,'paymentLogin','name');
			  break; 			
		}
	}
	//print_r($urlParams);exit;

if(1){
	$fbMode	=	"login";
	include("fbLoginInc.php"); // For Facebook Login
}

?>
<?php include("header.php");?>


<!--==============================================-->
<?php 
 if($registration->err1 == 1){ 
 $rowcls	=	"rows error";
  }else{
	   $rowcls ="rows" ;
   }
   if($registration->pas1 == 1){ 
   $rowcls2	="rows error";
  }else{
	   $rowcls2 ="rows" ;
   }
   
   ?> 


<div class="frame2">
<form name="regfail" method="post" action="">
    <div class="heading">
   <?=$parObj->_getLabenames($arrayData,'loginHeadNew','name');?>
    </div>
    <section class="reg-form">
    <div class="colum">
<!--
		<?php if($msg){?>
		
      <div valign="top" style="color: #FBBA2B;">&iexcl;&nbsp;<?=$msg?></div>
   			<?php }?>
-->
             <div class="fields">
                  <div class="<?=$rowcls?>"> 
                     <div class="label"><span>* </span><?=mb_strtoupper($parObj->_getLabenames($arrayData,'loginTxtNew','name'));?></div>
                     <div class="colums"><input type="text" name="user_email" id="textfieldLtest1"  class="tfl required email" value="<?=$email;?>" onFocus="RegNull(1);" />
                  </div>
                  <?php if($registration->err1 == 1){ ?>
                     <img src="images/help1.jpg" class="Q-mark" alt="help" onMouseOver="tooltip('<?=$registration->email1?>');" onMouseOut="exit();">
                     <?php }?>
                  </div>
                  
                  <div class="<?=$rowcls2 ?>"> 
                     <div class="label"><span>*</span><?=mb_strtoupper($parObj->_getLabenames($arrayDataLogin,'password','name'));?></div>
                     <div class="colums">
                     <input type="password" name="user_password" id="textfield3"  class="tfl tfl-1" value="<?=$pass;?>" onFocus="RegNull(2);" /></div>
                     <?php if($registration->pas1 == 1){ ?>
                     <img src="images/help1.jpg" class="Q-mark" alt="help" onMouseOver="tooltip('<?=$registration->pass1?>');" onMouseOut="exit();">
                     <?php }?>
                  </div>                 
                  
                  </div>
              <?php if(1){ ?>
<div class="chk_JW newchkbx" >
 <!--<label class="label_check" for="checkbox-04" >-->
 <input type="checkbox" id="checkbox-04"  value="1" name="remember">
<label for="checkbox-04"><span></span><?=$parObj->_getLabenames($arrayDataLogin,'remember','name');?>
                        
                      </label>


<a href="forgot_password.php" style="padding-left: 24px;"><?=$parObj->_getLabenames($arrayDataLogin,'forgot','name');?></a>

<div class="clear"></div>
 

</div>
<?php } ?>
<?php 
$fbLogin->fbMode	=	"login";
include_once("fbLogin.php"); 
?>
</div>
<div class="right">
<?php if($msg){ ?>
<p class="error_txt_JW"><?=$msg?></p><?}?>
<p><a href="userreg1.php<?php echo $urlParams; ?>" >
<?=mb_strtoupper($parObj->_getLabenames($arrayData,'regTxtNew','name'));?>
</a></p>
<p> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'orTxt','name'));?></p>
<?php 
$fbLogin->fbMode	=	"loginFailed"; 
include_once("fbLogin.php");
?>
<p>
   <a href="#" class="fb-btn">
      CONNECTEZ VOUS AVEC FACEBOOK
   </a>
</p>
<br>
<input type="hidden" name="returnUrl" value="<?=$_REQUEST['returnUrl'];?>" />
<input type="submit" class="sub-btn" name="loginButton" value="<?=mb_strtoupper($parObj->_getLabenames($arrayDataLogin,'login','name'));?>"  id="newSubBtn" />
 
       </div>

</section>

</form> 


</div>
<!--===============================================-->

<script type="text/javascript">
	//getOffset();
</script>
<?php //include("footer.php"); ?>
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
function RegNull(name)
	{
		if(name==1)
			document.regfail.user_email.value='';
		if(name==2)
			document.regfail.user_password.value='';
	}
function getOffset(){ 
	var d = new Date()
    var gmtHours = -d.getTimezoneOffset()/60;
	document.getElementById("timeZoneId").value=gmtHours;
}
</script>
