<?php 
ob_start();
session_start();
include_once('includeconfig.php');
include_once("includes/classes/class.programs.php");
include_once('includes/classes/class.CMS.php');

if($lanId=="") $lanId=1;

$objGen     	= new General();
$objTraining	= new Programs($lanId);
$parObj 		= new Contents('contents.php');
$objCMS			= new CMS($lanId);

//collecting data from the xml for the static contents
$returnData		= $parObj->_getTagcontents($xmlPath,'contents','label');
$arrayData		= $returnData['general'];

//collecting All training program category for displaying
$getAllTrainCats= $objTraining->_getAllGenItem('category',$lanId);
$contentTitle	= mysql_real_escape_string(urldecode($_REQUEST['title']));
$contents 		= $objCMS->_getContent($contentTitle, $lanId);

include("header.php");
// Redirecting with the page title for seperate design.
switch($contentTitle)
	{
		case 'aboutus':
			include_once('aboutus.php');
		break;
		case 'press':
			include_once('press.php');
		break;
		/*case 'CONTACTUS':
			//include_once('contact.php');
			include_once('ticket.php');
		break;*/
		default:
			include_once('contents_common.php');
		break;
	}

	
include("footer.php"); 
?>
