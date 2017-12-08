<?php
	error_reporting(0);  
	include_once("includes/config.php");
	$ticket = strpos($_SERVER['SCRIPT_FILENAME'], 'ticket');
	if((basename($_SERVER['PHP_SELF'])!= 'payment_new.php') && (basename($_SERVER['PHP_SELF'])!= 'gift_payment.php') && ($ticket == false))
	{ 
		include_once("includes/filterValidation.php");
	}
	include_once("includes/globals.php"); 	
    include_once("test_BB.php"); 	
	include('includes/classes/CurrencyConverter.php');
	include_once('includes/classes/class.General.php');
	include_once('includes/classes/class.DbAction.php');
    include_once('includes/classes/class.Settings.php');
	include_once('includes/classes/class.Contents.php');	
	include_once('includes/classes/class.footerLinks.php');	
	include_once('includes/classes/class.newParser.php');
    //include_once('set_location.php');  // uncomment this for version setting based on the user ip
	include_once('generalSession.php');
	include_once('login_check.php');
	include_once('adwordManage.php');
	header("Connection: keep-alive");
?>
