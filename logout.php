<?php
session_start();
include_once('includeconfig.php');
$objGen     	= 	new General();
//$logoutForum	=	logout(); uncomment this line while uploading time

//session unregister for logout
//session_unregister('user');
unset($_SESSION['user']);
unset($_SESSION['jiwokforum']['user']);
unset($_SESSION['jiwokforum']['email']);
unset($_SESSION['forum_userId']);
//to avoid session issue with variables on running heroes 
unset($_SESSION['callback_success']);
unset($_SESSION['callback_error']);
unset($_SESSION['running_id']);
unset($_SESSION['token']);
if(isset($_SESSION['facebook_access_token'])){
	unset($_SESSION['facebook_access_token']);	
}
setcookie('blogUSer',$_SESSION['user']['userId'],time()-600);
//return to last visted page.
if($_REQUEST['returnUrl'] != "")
	{		
		$returnURL = base64_decode($_REQUEST['returnUrl']);
	}
elseif($_REQUEST['blogUrl'] != "")
	{					
		$lastPage		=	$objGen->_lastVistedPage('Default');
		
		if($lastPage != 'Default')
			$returnURL	=	$lastPage;
		else
		$returnURL 	=	"index.php";
		
		header('location:'.$returnURL);exit;
			//$returnURL 	=	SITENAME."index.php";
	}
	if($returnURL	==	'rh-jiwok.php')
	{
	header('location:'.ROOT_JWPATH.'RunningHero/'.$returnURL);exit;
	}
	
header('location:'.ROOT_JWPATH.$returnURL);
?>
