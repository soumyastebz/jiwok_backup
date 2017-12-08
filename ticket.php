<?php
session_start();
ob_start();

/*ini_set('display_errors',1);
error_reporting(E_ALL);*/

/* modifications done by vinitha on july 29  for multilanguage support*/
include_once('includeconfig.php');
include_once("includes/classes/class.ticketConnect.php");
$objTicket     	= new Ticketconnect();
$redirect_url 	= base64_encode('ticket.php');
//define('ROOT_DIR','http://www.jiwok.com/');
if($_SERVER['HTTP_HOST'] == "www.jiwok.com.jiwok-wbdd2.najman.lbn.fr")
{
	define('ROOT_DIR','www.jiwok.com.jiwok-wbdd2.najman.lbn.fr/');
}
else if($_SERVER['HTTP_HOST'] == "www.jiwok.com")
{ 
	define('ROOT_DIR','https://www.jiwok.com/');
}
else if($_SERVER['HTTP_HOST'] == "10.0.0.8")
{ 
	define('ROOT_DIR','http://10.0.0.8/jiwokv3/');
}
if(!isset($_SESSION['user']['userId']))
	{
		if($lanId == 1)
			{			
				header('location:'.ROOT_DIR.'en/login_failed.php?returnUrl='.$redirect_url);exit;
			}
		
		else if($lanId == 3)
			{			
				header('location:'.ROOT_DIR.'es/login_failed.php?returnUrl='.$redirect_url);exit;
			}
		else if($lanId == 4)
			{			
				header('location:'.ROOT_DIR.'it/login_failed.php?returnUrl='.$redirect_url);exit;
			}
			else if($lanId == 5)
			{			
				header('location:'.ROOT_DIR.'pl/login_failed.php?returnUrl='.$redirect_url);exit;
			}
		else 
		
			{ 
				header('location:'.ROOT_DIR.'login_failed.php?returnUrl='.$redirect_url);exit;
			}
	}
else
{
//echo '<pre>',print_r($_SESSION);exit;
/*commented and  modified by  vinitha on july 29  for multilanguage support and to change the queries in main page using class*/
/*$dbConn	=	@mysql_pconnect("localhost","root","");
$dbLink	=	@mysql_select_db("jiwok_ver2",$dbConn);

$sql_pass = @mysql_query("select user_password, user_email,user_language from user_master where user_id=".$_SESSION['user']['userId']);*/



$res_pass =$objTicket ->_getuserbySession();


//= @mysql_fetch_assoc($sql_pass);
$password = $res_pass['user_password'];

/*if(session_is_registered("language"))	{*/
if(isset($_SESSION["language"]))	{
	$value			=	$objTicket ->_getlanguagebySession();
	$ticket_lang	=	$value['lang_folder'];	}
	else	{	if($res_pass['user_language'] == 1) { $ticket_lang = 'en'; } else { $ticket_lang = 'fr';} 	}		

/* -----------------------------------------------------*/
}

unset($_SESSION['brand_ticket']);

$objTicket->compareTckUser($_SESSION["user"]["user_email"]);

header("Location:".ROOT_JWPATH."ticket/panel.php?act=aml3b2tMb2dpbg==&userEmail=".base64_encode($_SESSION["user"]["user_email"]));

?>
