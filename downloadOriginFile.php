<?php
session_start();
ob_start();
include_once('includeconfig.php');
include_once("includes/classes/class.programs.php");
include_once("includes/classes/class.Languages.php");
if($lanId=="")
     $lanId=1;
$errorMsg = '';	 
$userid		= $_SESSION['user']['userId'];	
$objGen     	= new General();
$objPgm     	= new Programs($lanId);
$objLan    		= new Language();
$parObj 		= new Contents();
$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
$arrayData		= $returnData['general'];
$lanName =  strtolower($objLan->_getLanguagename($lanId));
$workoutid	= trim(stripslashes($_REQUEST['workoutid']));

$workOutData = $objPgm->_getWorkoutOriginSourceFile($workoutid);
$workoutFile = trim(stripslashes($workOutData['workout_origin_file']));
$myFile1 = "./uploads/originforce/".$lanName."/".$workoutFile;
if(file_exists($myFile1))
{
//echo "<p align=\"center\" ><strong>".$parObj->_getLabenames($arrayData,'staticdownloadmsg','name')."</strong></p><p align=\"center\" ><a href=\"downloadOriginMP3.php?work=".base64_encode($workoutid)."\" style=\"text-decoration:underline;\"><font color=\"#0000000\"><strong>".$parObj->_getLabenames($arrayData,'workoutdownload','name')."</strong></font></a></p>";
echo '<p align="center" id="dwnmsg"><strong>'.$parObj->_getLabenames($arrayData,'staticdownloadmsg','name').'</strong></p><p align="center" id="download_btn" ><input type="button"  value="'.$parObj->_getLabenames($arrayData,'workoutdownload','name').'" onclick="downloadOriginForceFile();"  class="btn_pop ease"/><noscript><a href="downloadOriginMP3.php?work='.base64_encode($workoutid).'" >'.$parObj->_getLabenames($arrayData,'workoutdownload','name').'</a></noscript><input type="hidden" id="download_id" value="'.base64_encode($workoutid).'" /></p><p align="center" id="loader_image" style="visibility: hidden; display: none;"><img align="center" src="images/ajax_loader.gif" style="float:none;"/></p><p align="center" id="alter_download" style="visibility: hidden; display: none;">'.$parObj->_getLabenames($arrayData,'alterdwd','name').'</p><p align="center" id="alter_download2" style="visibility: hidden; display: none;"><a class="btn_pop ease" href="downloadOriginMP3.php?work='.base64_encode($workoutid).'" target="_blank">'.$parObj->_getLabenames($arrayData,'downld','name').'</a></p>';
}
else
{
echo "<h1 align=\"center\" ><strong>".$parObj->_getLabenames($arrayData,'filenotfound','name')."</strong></h1>";
}

?>