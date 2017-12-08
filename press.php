<?php
session_start();
require_once 'includeconfig.php';
include_once('includes/classes/class.CMS.php');
include_once("includes/classes/class.homepage.php");
if($_POST){
	$lanId          =   $_POST["lanId"];
	}else{
    $lanId          = 1;
    }
	$objCMS    		=   new CMS($lanId);
	$objGen     	=   new General();
	$objHome     	=	new Homepage($lanId);
	$parObj 		= 	new Contents('');
	$returnData		= 	$parObj->_getTagcontents($xmlPath,'homepage','label');
	$arrayDataHome	= 	$returnData['general'];
	$press 		    =   $objCMS->_getContent($a = 'PRESS_NEW', $lanId);
	echo $objGen->_output($press['content_body']);
?>      
<?php
exit;
?>
