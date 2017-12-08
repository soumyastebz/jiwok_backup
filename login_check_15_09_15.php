<?php
include_once("includes/config.php");
include_once 'includes/classes/class.DbAction.php';
setcookie("PHPSESSID",$_COOKIE['PHPSESSID'],time()+86400, '/');
$objGen     	= 	new General();
$objDb          =  	new DbAction();

//~ echo "<pre>";
//~ ini_set('display_errors',1);
//~ error_reporting(E_ALL|E_STRICT);

//ini_set('display_errors',1);
//error_reporting(E_ALL);


//for returning to the last page


if($_REQUEST['returnUrl'] != "") {
	$returnUrl = base64_decode($_REQUEST['returnUrl']);
} elseif($_REQUEST['blogUrl'] != "") {
	$lastPage	=	$objGen->_lastVistedPage('Default'); 
	if($lastPage != 'Default')
		$returnUrl =	$lastPage;
	else
		$returnUrl =	SITENAME."index.php";
} elseif($_REQUEST['forumUrl'] != "") {
	$returnUrl =	FORUMURL."test.php";
} else	{
	if($_REQUEST["fbLoginRed"]!=""){
		$returnUrl =	base64_decode($_REQUEST["fbLoginRed"]);
		$_SESSION["forumFBUrl"]	=	$_REQUEST["fbLoginRed"];
		if($_REQUEST["isBlog"]=="1"){
			$_SESSION["trnsFBUrl"]	=	$_SESSION["forumFBUrl"];	
		}
	}else if($_SESSION["fbLoginRed"]!=""){
		$returnUrl	=	$_SESSION["fbLoginRed"];
	}else{
		$returnUrl =	"index.php";
	}
}	

//if($res['user_id']	==	'60378')
			//{
				//die($_SESSION["fbLoginRed"]);	
			//}

	/*Code added by dijo for autologin features*/
	if($_REQUEST['mode'] == 'autologin')
	{
		if($_REQUEST['authid']!='')
		{
			$_SESSION['LoginMode'] 			= $_REQUEST['mode'];//'autologin';
			$_SESSION['LoginModeUserId'] 	= base64_decode($_REQUEST['authid']);
			
			$currUrlautologin				=	(!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			/*It removes mode and uid in the urls*/
			$returnUrl 						=	explode('mode',$currUrlautologin);
			if($returnUrl[0] != '')
				$returnUrl 					=	$returnUrl[0];
				
			/*It removes last & or ? symbol from query string if it ends with these symbols*/
			if((substr($returnUrl, -1) == '?') || (substr($returnUrl, -1) == '&'))
				$returnUrl 					=	substr($returnUrl, 0, -1); /*Removes last char*/
				 
		}
	}
	/*Code added by dijo for autologin features*/



//searching for whether there any cokie set for the site
if($_COOKIE['jiwokCookie'])
{
	list($cookieId,$cookieEmail,$cookiePass) = explode('~',$_COOKIE['jiwokCookie']);
}
if((isset($_REQUEST['loginButton']))||($_SESSION["LoginMode"]!=""))
{ //echo"reached";exit;
	if(($_REQUEST['user_email']== "")&&($_SESSION["LoginMode"]==""))
	{
		header('Location:login_failed.php?returnUrl='.base64_encode($returnUrl).'&msg='.base64_encode(1));
	}	
	elseif(($_REQUEST['user_password']=="")&&($_SESSION["LoginMode"]==""))
	{
		header('Location:login_failed.php?returnUrl='.base64_encode($returnUrl).'&msg='.base64_encode(1));
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
				header('Location:login_failed.php?returnUrl='.base64_encode($returnUrl).'&msg='.base64_encode(1));
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
			header('Location:login_failed.php?returnUrl='.base64_encode($returnUrl).'&msg='.base64_encode('2'));
			}
			else
			{
			header('Location:login_failed.php?returnUrl='.base64_encode($returnUrl).'&msg='.base64_encode('1'));
			}
		}
	}
}
////////////////////////////////////////////////////
if((isset($_REQUEST['reloginButton'])))
{
	if($_REQUEST['reuser_email']== "")
	{
		header('Location:re_login_failed.php?returnUrl='.base64_encode($returnUrl).'&msg='.base64_encode(1));
	}	
	elseif($_REQUEST['reuser_password']=="")
	{
		header('Location:re_login_failed.php?returnUrl='.base64_encode($returnUrl).'&msg='.base64_encode(1));
	}	
	else
	{
		$password	= $_REQUEST['reuser_password'];
		if(get_magic_quotes_gpc())
		{
			$password	= stripslashes($password);
		}
		$password=addslashes(base64_encode($password));
		$sql="SELECT * FROM reseller_master WHERE   	
	reseller_id='".$_REQUEST['reuser_email']."' AND re_password='".$password."' AND   	
	re_status =1";
	//exit;
		$res = $GLOBALS['db']->getRow($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res))
		{
		//echo $res->getDebugInfo();
		}
		if(count($res)>1)
		{
			//insertng in to the member_login table to track the user login to the site to generate the 
			//report in the admin side
			$insertData['login_date'] = date("Y-m-d H:i:s");
			$insertData['user_id']    = $res['reseller_id'];
			$insertData['login_ip'] = $REMOTE_ADDR;
			$objDb->_insertRecord("member_login",$insertData);
			if($_REQUEST['remember'] == 1)
			{
				$value = implode('~',array($res['reseller_id'],$_REQUEST['reuser_email'],$_REQUEST['reuser_password']));
				setcookie("jiwokCookie", $value, time()+3600000, '/');
			}
		
		
		/*session_unregister('login');
		session_unregister('registration');
		session_register('reseller');*/
		unset($_SESSION['login']);
		unset($_SESSION['registration']);
		session_start();
		$_SESSION['reseller'];
			
		
		
		//assigning the user data to a session named user 
		//session data should be the followings
		//1-user id
		//2-user type(trainer or user )
		$_SESSION['reseller']= array(
		"r_master_id"       => $res['r_master_id']
		);
		$returnUrl = "reseller_index.php?conf=".base64_encode("confirm");
		header('Location:'.$returnUrl);
		}
		else
		{             
			$sql="SELECT * FROM reseller_master WHERE   	
			reseller_id='".$_REQUEST['reuser_email']."' AND re_password='".$password."' AND   	
			re_status =0";
			$res = $GLOBALS['db']->getRow($sql, DB_FETCHMODE_ASSOC);
				if(count($res) > 1)
				{
					header('Location:re_login_failed.php?returnUrl='.base64_encode($returnUrl).'&msg='.base64_encode('3'));//not active reseller
				}
				else
				{
					header('Location:re_login_failed.php?returnUrl='.base64_encode($returnUrl).'&msg='.base64_encode('1'));
				}
		}
	}
}
?>
