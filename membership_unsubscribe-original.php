<?php
session_start();
include_once('includeconfig.php');
include_once("includes/classes/class.member.php");

$errorMsg = '';	 
$userid = $_SESSION['user']['userId'];	 

$objMem     		= new Member($lanId);
$memshipUnsubscribe	= $objMem->_sentReqstMemUnSubscribe($userid);
    
	//session unregister for logout
	//session_unregister('user');
	//unset($_SESSION['jiwokforum']['user']);
	//unset($_SESSION['jiwokforum']['email']);
echo "1";
?>
