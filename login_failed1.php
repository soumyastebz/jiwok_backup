<?php 
	
	session_start();
	include_once('includeconfig.php');
	
	if($lanId=="") $lanId=1;
		 
	$objGen     	= new General();
	$parObj 		= new Contents('login_failed.php');
				
	//collecting data from the xml for the static contents
	
	$returnData		= 	$parObj->_getTagcontents($xmlPath,'registrationUser','label');   // Get content from XML
	$arrayData		= 	$returnData['general'];

	if(isset($_SESSION['user']['userId']))
	{
		//print_r($_SESSION['user']['userId']);exit;
	// 		$path = "/../runningheroes.php";
	// header('Location:'.$path);
		header('location:running_heroes/runningheroes.php');
	}
	$returnDataGen		= $parObj->_getTagcontents($xmlPath,'loginfailed','label');
	$arrayDataLogin			= $returnDataGen['general'];
	
	
	//collecting data from the xml for the static contents
	$returnData			= $parObj->_getTagcontents($xmlPath,'loginfailed','messages');
	$errorData			= $returnData['errorMessage'];
	
	$urlParams	=	"";
	
	 $_REQUEST['returnUrl'] 	=	 htmlspecialchars(strip_tags(trim($_REQUEST['returnUrl'])));
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
	

if(1){
	$fbMode	=	"login";
	include("fbLoginInc.php"); // For Facebook Login
}

?>
<?php include("header.php"); ?>

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
<div class="chk_JW" >
 <!-- <label class="label_check" for="checkbox-04" >
 <input type="checkbox" id="checkbox-04"  value="1" name="remember">
<?=$parObj->_getLabenames($arrayDataLogin,'remember','name');?> -->
                        
                      </label>


<a href="forgot_password.php" style="padding-left: 24px;"><?=$parObj->_getLabenames($arrayDataLogin,'forgot','name');?></a>

<div class="clear"></div>
 

</div>
<?php } ?><?php
$fbLogin->fbMode	=	"login";
include("fbLogin.php"); 
?>
             </div>
       <div class="right">
		   <?if($msg){?>
		   <p class="error_txt_JW"><?=$msg?></p><?}?>
          <p><a href="userreg1.php<?php echo $urlParams; ?>" >
	<?=mb_strtoupper($parObj->_getLabenames($arrayData,'regTxtNew','name'));?>
</a></p>
<p> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'orTxt','name'));?></p>

<?php 
$fbLogin->fbMode	=	"loginFailed"; 
include("fbLogin.php");
?>
<!--<p>
   <a href="#" class="fb-btn">
      CONNECTEZ VOUS AVEC FACEBOOK
   </a>
</p>-->
<br>
<input type="hidden" name="returnUrl" value="<?=$_REQUEST['returnUrl'];?>" />
<input type="submit" class="sub-btn" name="loginButton_running" value="<?=mb_strtoupper($parObj->_getLabenames($arrayDataLogin,'login','name'));?>"  id="newSubBtn" />
 
       </div>

</section>

</form> 


</div>
<!--===============================================-->

<script type="text/javascript">
	getOffset();
</script>
<?php include("footer.php"); ?>
<script type="text/javascript">
function RegNull(name)
	{
		if(name==1)
			document.regfail.user_email.value='';
		if(name==2)
			document.regfail.user_password.value='';
	}
</script>
<?php
if((isset($_REQUEST['loginButton_running']))||($_SESSION["LoginMode"]!=""))
{ 
	if(($_REQUEST['user_email']== "")&&($_SESSION["LoginMode"]==""))
	{
		header('Location:login_failed1.php?returnUrl='.base64_encode($returnUrl).'&msg='.base64_encode(1));
	}	
	elseif(($_REQUEST['user_password']=="")&&($_SESSION["LoginMode"]==""))
	{
		header('Location:login_failed1.php?returnUrl='.base64_encode($returnUrl).'&msg='.base64_encode(1));
	}	
	else
	{	
		$isSess	=	false;
		if(($_SESSION["LoginMode"]=="")){	
			$password	= $_REQUEST['user_password'];	
			if(get_magic_quotes_gpc())
			{ 
			$password	= stripslashes($password);
			}
			$mdpassword	=	addslashes(base64_encode(md5(utf8_decode($password))));
			$password=addslashes(base64_encode(utf8_decode($password)));
		$sql	=	"SELECT SQL_NO_CACHE SQL_SMALL_RESULT
					`user_id`,`user_type`,`user_email`,`user_alt_email` 
			FROM `user_master` 
			WHERE `user_alt_email`= '".$_REQUEST['user_email']."' 
					 AND (`user_password`='".$password."' OR `user_password`='".$mdpassword."') AND 
					`user_status` IN(1,3) LIMIT 1";
		}else{		
			if($_SESSION["LoginModeUserId"]!=""){
				$isSess	=	true;
				$loginUserId	=	$_SESSION["LoginModeUserId"];
				$sql	=	"SELECT SQL_NO_CACHE SQL_SMALL_RESULT
					`user_id`,`user_type`,`user_email`,`user_alt_email` 
			FROM `user_master` 
			WHERE `user_id`= '".$loginUserId."' 
					AND	`user_status` IN(1,3) LIMIT 1";
					unset($_SESSION["LoginModeUserId"]);
					unset($_SESSION["LoginMode"]);
			}else{
				header('Location:login_failed1.php?returnUrl='.base64_encode($returnUrl).'&msg='.base64_encode(1));
				exit;
			}
		}
	
		$res = $GLOBALS['db']->getRow($sql, DB_FETCHMODE_ASSOC);	
		if(DB::isError($res)) 
		{
		//echo $res->getDebugInfo();
		}	
		if(count($res)>1)
		{ 
			//insertng in to the member_login table to track the user login to the site to generate the 
			//report in the admin side
			if($isSess){
				$_REQUEST['user_email']	=	stripslashes($res['user_alt_email']);
				$_REQUEST['user_password']	=	stripslashes(base64_decode($res['user_password']));
			}
			$insertData['login_date'] = date("Y-m-d H:i:s");
			$insertData['user_id']    = $res['user_id'];
			$insertData['login_ip'] = $REMOTE_ADDR;
			$objDb->_insertRecord("member_login",$insertData);
			if($_REQUEST['remember'] == 1)
			{
			$value = implode('~',array($res['user_id'], $_REQUEST['user_email'], $_REQUEST['user_password']));
			setcookie("jiwokCookie", $value, time()+3600000, '/');
			}
			
			
			/*session_unregister('login');
			session_unregister('registration');
			session_register('user');*/
			unset($_SESSION['login']);
			unset($_SESSION['registration']);
			session_start();
			$_SESSION['user'];
			
			
			
			//assigning the user data to a session named user 
			//session data should be the followings
			//1-user id
			//2-user type(trainer or user )
			unset($_SESSION['oauth_token']);
			unset($_SESSION['oauth_token_secret']);
			unset($_SESSION['oauth_verifier']);
				
			$_SESSION['user']= array(
			"userId"       => $res['user_id'],
			"userType"     => $res['user_type'],
			"user_email"    => $res['user_alt_email']
			);
			//echo $returnUrl;exit;	
			
			//echo $returnUrl;exit;
			if($returnUrl=="userArea.php")
			$returnUrl = "userArea.php?conf=".base64_encode("confirm");
			else
			$returnUrl = $returnUrl;			
			$brandname 		= explode(".", $_SERVER['HTTP_HOST']);
			$brand 			= $brandname[0]; 	
		   //Forum login --- added by Jitha
			if($brand=="beta")
			{
				define('IN_PHPBB', true);
				$phpEx = substr(strrchr(__FILE__, '.'), 1);
				$phpbb_root_path = "/home/sites_web/client/newdesign.back/forum/";
				//$phpbb_root_path = "forum/";
				/* includes all the libraries etc. required */
				require($phpbb_root_path ."common.php");
				require($phpbb_root_path ."includes/functions_user.php");
				include_once $phpbb_root_path ."includes/utf/utf_tools.php";	
				include_once $phpbb_root_path ."includes/functions.php";
				include_once $phpbb_root_path ."includes/auth.php";
				$auth	=	new user();
				$user->session_begin();
	
				$sqlForum		=	"SELECT * 
				FROM `forum_users` 
				WHERE `username`= '".$_REQUEST['user_email']."'"; 
				$resF		 	= mysql_query($sqlForum);
				$resForum		=	mysql_fetch_array($resF,MYSQL_ASSOC);
				$result = $user->session_create($resForum['user_id']);
				$_SESSION['forum_userId']			=	$resForum['user_id'];
				$_SESSION['jiwokforum']['email'] 	= $resForum['user_email'];
				$_SESSION['jiwokforum']['user'] 	= $resForum['user_email'];
            }
			else
			{
				define('IN_PHPBB', true);
				$phpEx = substr(strrchr(__FILE__, '.'), 1);
				//$phpbb_root_path = "/home/sites_web/client/newdesign/forum/";
				$phpbb_root_path  = "/var/www/html/jiwokv3/forum/";
				//$phpbb_root_path = "forum/";
				/* includes all the libraries etc. required */
				require($phpbb_root_path."common.php");
				require($phpbb_root_path."includes/functions_user.php");
				include_once $phpbb_root_path."includes/utf/utf_tools.php";	
				include_once $phpbb_root_path."includes/functions.php";
				include_once $phpbb_root_path."includes/auth.php";
				$auth	=	new user();
				$user->session_begin();				
				$sqlForum		=	"SELECT * 
				FROM `forum_users` 
				WHERE LOWER(username)= '".strtolower($_REQUEST['user_email'])."'"; 
				$resF		 	= mysql_query($sqlForum);
				$resForum		= mysql_fetch_array($resF,MYSQL_ASSOC);
			    $result         = $user->session_create($resForum['user_id']);				
				$_SESSION['forum_userId']			= $resForum['user_id'];
				$_SESSION['jiwokforum']['email'] 	= $resForum['user_email'];
				$_SESSION['jiwokforum']['user'] 	= $resForum['user_email'];
				unset($_SESSION['oauth_token']);
				unset($_SESSION['oauth_token_secret']);
				unset($_SESSION['oauth_verifier']);						
			}
				//Forum login --- added by Jitha
				
			$currloginUrl				=	(!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			
			if(isset($_SESSION["forumFBUrl"])){
				if($_SESSION["forumFBUrl"]!=""){					
					$returnUrl	=	base64_decode($_SESSION["forumFBUrl"]);
					unset($_SESSION["forumFBUrl"]);

				}
			}
			//For color workout
			//Added by Dileep.E						
			if(($_SERVER['HTTP_HOST'] != "www.jiwok.com")	&&	$_SESSION['brand']	==	'domyos')
			{					
				$_SESSION['user']['color_flag'] =  	$res['color_flag'];
				$_SESSION['user']['testlogin']	= 	1;
			}	
		//echo '<pre>';	print_r($_SESSION); echo $returnUrl.' Reached'; exit;		
			header('Location:'.$returnUrl);
			exit;
		}
		else
		{             
			$sql="SELECT user_id,user_type FROM user_master WHERE user_alt_email='".$_REQUEST['user_email']."' AND (user_password='".$password."' OR user_password='".$mdpassword."') AND user_status =0";
			$res = $GLOBALS['db']->getRow($sql, DB_FETCHMODE_ASSOC);
			if(count($res) > 1)
			{
			header('Location:login_failed1.php?returnUrl='.base64_encode($returnUrl).'&msg='.base64_encode('2'));
			}
			else
			{
			header('Location:login_failed1.php?returnUrl='.base64_encode($returnUrl).'&msg='.base64_encode('1'));
			}
		}
	}
}
?>
