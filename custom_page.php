<?php 
ob_start();
session_start();
include_once('includeconfig.php');
include_once("includes/classes/class.programs.php");
include_once('includes/classes/class.custompage.php');

if($lanId=="") $lanId=1;

$objGen     	= new General();
$objTraining	= new Programs($lanId);
$parObj 		= new Contents('contents.php');
$objCustom			= new Custompage($lanId);

//collecting data from the xml for the static contents
$returnData		= $parObj->_getTagcontents($xmlPath,'contents','label');
$arrayData		= $returnData['general'];

//collecting All training program category for displaying
$getAllTrainCats= $objTraining->_getAllGenItem('category',$lanId);
$contentTitle	= urldecode($_REQUEST['title']);
$customContents 		= $objCustom->_getCustomByTitle($contentTitle, $lanId);

include("header.php");
include("menu.php");

// Redirecting with the page title for seperate design.
switch($contentTitle){
	case 'aide-video':
		$searchPath	=	$parObj->_getLabenames($arrayData,"searchPath","name");
		$homeName	=	$parObj->_getLabenames($arrayData,"homeName","name");
		$newPgeTxt	=	$parObj->_getLabenames($arrayData,"videoAide","name");//$contents['content_display_title'];
		$newBackTxt	=	$parObj->_getLabenames($arrayData,"newBackTxt","name");
		$homeUrl	=	ROOT_JWPATH.'index.php';
	break;
	
}

$header='<div class="breadcrumbs">
		  <ul>
			<li>'.$searchPath.' :</li>
			<li><a href="'.$homeUrl.'">'.$homeName.'</a></li>
			<li>></li>
			<li><a href="#" class="select">'.$newPgeTxt.'</a></li>
		  </ul>
		</div>';
?>

<div id="container">
  <div id="wraper_inner">
    <?=$header?>
	<div class="heading"><span class="name"><?php echo $newHeadTxt; ?></span> <span class="date"><strong>&gt; </strong><a href="<?=$backButtonLink?>" class="white"><?php echo $newBackTxt; ?></a></span></div>
	<?php echo stripslashes($customContents['manager_body']); ?>
  </div>
</div>
<?php
include("footer.php"); 
?>