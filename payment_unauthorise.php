<?php
session_start();
include_once('regDetail.php');

unset($_SESSION['payment']);

unset($_SESSION['subscription']);
if(isset($_SESSION['login']['user_email'])){

$useremail	=	$_SESSION['login']['user_email']; //if user is coming from the registration page.

}
elseif(isset($_SESSION['user']['user_email'])){

$useremail	=	$_SESSION['user']['user_email']; //get user id from the session variable/if user is coming from the UserArea Page.
}



if(isset($_SESSION['login']['userId'])){

$userId		=	$_SESSION['login']['userId']; //if user is coming from the registration page.

}

elseif(isset($_SESSION['user']['userId'])){

$userId		=	$_SESSION['user']['userId']; //get user id from the session variable/if user is coming from the UserArea Page.

}

statusRecord($useremail,'payment refused','payment_error.php',$userId,'0','14');

if(isset($_SESSION['brand'])&& $_SESSION['brandpay']==1)
{
unset($_SESSION['brandpay']);
		if($_SESSION['brand'] == "semideparis" || $_SESSION['brand'] == "parismarathon"):
			if($_SESSION['language']['langId']==1){ $url.="en/";}
			header("location:http://".$_SESSION['brand'].".jiwok.com/".$url."payment_success.php?msg=".base64_encode(4));
			exit;
		else:
			header("location:http://".$_SESSION['brand'].".jiwok.com/payment_success.php?msg=".base64_encode(4));
			exit;
		endif;
}
else
{
$url="http://www.jiwok.com/";
if($_SESSION['language']['langId']==1){ $url.="en/";}///////////add language cases here
header('location:'.$url.'payment_success.php?msg='.base64_encode(4));
exit;
}
?>