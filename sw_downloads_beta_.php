<?php 

ob_start();

session_start();

/*	ini_set('display_errors',1);

	error_reporting(E_ERROR | E_PARSE);
*/
include_once('includeconfig.php');

//ini_set('display_errors',1);

//error_reporting(E_ERROR | E_WARNING);


include_once("includes/classes/class.programs.php");

include_once("includes/classes/class.Languages.php");

include_once 'includes/classes/class.Documents_Download.php';



if($lanId=="")

     $lanId=1;

$flag = 0;

$errorMsg = '';	 

$wtext = '';

$userid = $_SESSION['user']['userId'];	

$objPgm     	= new Programs($lanId);

$objLan    		= new Language();



$parObj 		= new Contents('sw_downloads_beta.php');

//$headingData	= $parObj->_getTagcontents($xmlPath,'trainingprogram','pageHeading');

$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');

$arrayData		= $returnData['general'];

$docDataAll		= $parObj->_getTagcontents($xmlPath,'documentListing','label');

$docDataGen		= $docDataAll['general'];

$DownloadArray		= $parObj->_getTagcontents($xmlPath,'downloads','label');

$Downloads		= $DownloadArray['general'];


$lanName =  strtolower($objLan->_getLanguagename($lanId));

$docDownloadObj	= new Documents_Download($lanId, $lanName);

$error = "";

$today = date('Y-m-d');

//$pgm_expdate='';

//$program_id  = base64_decode(trim($_REQUEST['program_id']));

$redirect_url = base64_encode('document_listing.php');

if(!($objPgm->_checkLogin()))

{

header('location:login_failed.php?returnUrl='.$redirect_url);

}

if(isset($_POST['addnike']) && ($_POST['addnike']!=""))

{

  $nikeUser = stripslashes(trim($_POST['nikeUser']));

  $nikePass = stripslashes(trim($_POST['nikePass']));

  $nikeDetail2 = $objPgm->_getNikeDetails($userid);

  

  if(count($nikeDetail2)>0)

  {

	$sql = "UPDATE nike set nike_login='".addslashes($nikeUser)."',nike_password='".addslashes(base64_encode($nikePass))."' where nike_userid='$userid'";

	$res = $GLOBALS['db']->query($sql);

   }

   else

   {

    $sql = "INSERT INTO nike VALUES('','$userid','".addslashes($nikeUser)."','".addslashes(base64_encode($nikePass))."')";

	 $res = $GLOBALS['db']->query($sql); 

   }

 
	header('location:nike_plus.php'); 



}

$user = $objPgm->_getUserDetails($userid);

$nikeUserDetails = $objPgm->_getNikeDetails($userid);

$user_name = trim(stripslashes($user['user_fname']));

$nikeUserName =trim(stripslashes($nikeUserDetails['nike_login']));

$nikeUserPass = base64_decode(trim(stripslashes($nikeUserDetails['nike_password'])));

$usrPhotoPath = "uploads/users/";

$user_photo = trim(stripslashes($user['user_photo']));

if($user_photo != "")

	{

		$iParams = $objPgm->_imageResize(trim($user_photo),$usrPhotoPath,87,106);

		$iWidth  = $iParams[0];

		$iHeight  = $iParams[1];

	}

//subscribed program

$program = $objPgm->_getUserTrainingProgram($userid);

/*echo "<pre>";

print_r($program);

echo "</pre>";*/



if(count($program)>0) 

{ 

	$flexid 		= stripslashes(trim($program['flex_id']));

    $program_id		= stripslashes(trim($program['program_id']));

	$program_title	= $objPgm->_getOnePgmDetail($program_id, 'program_title', $lanId);

	$data = $objPgm->_displayTrainingProgram($program_id,$lanId);

	$workoutDatesArray = $objPgm->_getTrainingCalWorkoutDates($userid);

	//$workoutOrderArray = $objPgm->_getWorkoutOrders($flexid);

	$workOrder =0;

	foreach($workoutDatesArray as $key => $value)

	{

	    $work_out = explode("@@",$key);

		$workOrder++;

		if($value>=$today)

		{

			$workDate	   = $value;  // store workout dates

			$workoutFlexId = trim($work_out[0]);

			break;

		}

		

	} 

	//For Document Downloads

	$workouts_subscribed	= $objPgm->getWorkoutsOfProgram($flexid, $lanId);

	$program_document_download	= $docDownloadObj->getTrainingProgramPDFFiles($data['flex_id'], $data['program_title']);

	$workouts_document_download	= $docDownloadObj->getWorkoutPDFFiles($workouts_subscribed);

}







$single_program = $objPgm->_getAllSingleTrainingPrograms($userid,0,10000); //get all single traing programs

if(count($single_program)>0) 

{$cc_cnt=0;

	for($cc=0;$cc<count($single_program);$cc++) {

		$flexid 		= stripslashes(trim($single_program[$cc]['flex_id']));



		$program_id		= stripslashes(trim($single_program[$cc]['program_id']));

		$single_program_title	= $objPgm->_getOnePgmDetail($program_id, 'program_title', $lanId);

		$data = array();

		//$workoutDatesArray = array();

		$data = $objPgm->_displayTrainingProgram($program_id,$lanId);

		//echo "<pre>";

		//print_r($data);

		//echo "</pre>";

		//$workoutDatesArray = $objPgm->_getTrainingCalWorkoutDates($userid);

		////$workoutOrderArray = $objPgm->_getWorkoutOrders($flexid);

		/*$workOrder =0;

		foreach($workoutDatesArray as $key => $value)

		{

			$work_out = array();

			$work_out = explode("@@",$key);

			$workOrder++;

			if($value>=$today)

			{

				$workDate	   = $value;  // store workout dates

				$workoutFlexId = trim($work_out[0]);

				break;

			}

			

		}*/

		//For Document Downloads

//		$workouts_subscribed	= $objPgm->getWorkoutsOfProgram($flexid, $lanId);

/*echo "<pre>";

print_r($data);

echo "</pre>";*/

		if(count($docDownloadObj->getTrainingProgramPDFFiles($data['flex_id']))>0 ) {

			$single_program_document_download[$cc_cnt]	= $docDownloadObj->getTrainingProgramPDFFiles($data['flex_id']);

			$single_program_document_download[$cc_cnt][0]['flex_id']=$data['flex_id'];

			$single_program_document_download[$cc_cnt][0]['program_title']=$data['program_title'];

			

			$cc_cnt++;

		}

		//$workouts_document_download	= $docDownloadObj->getWorkoutPDFFiles($workouts_subscribed);

	}

}

/*echo "<pre>";

print_r($single_program_document_download);

echo "</pre>";*/



	$static_documents_download	= $docDownloadObj->getStaticPDFFiles($lanId);
	
$sql_text = "select * from contents where content_title_id = 'JiwokSoftwareText' and language_id='$lanId'";

	 $res_text	=	$GLOBALS['db']->getRow($sql_text, DB_FETCHMODE_ASSOC);
	 
	 $softdownloadtext = strip_tags(trim(stripslashes($res_text['content_body'])));
	 

?>
<?php include("header.php"); ?>
<?php include("menu.php"); ?>
<div id="container">
  <div id="wraper_inner">
    <div class="breadcrumbs">
      <ul>
        <li><?=$parObj->_getLabenames($Downloads,'newPgeNmeTxt','name')?> :</li>
        <li><a href="<?=ROOT_JWPATH?>index.php"><?=$parObj->_getLabenames($Downloads,'newPgeHmeTxt','name')?></a></li>
        <li>></li>
        <li><a href="#" class="select"><?=$parObj->_getLabenames($Downloads,'newPgeMyDocTxt','name')?></a></li>
      </ul>
    </div>
    <div class="heading"><span class="name"><?=$parObj->_getLabenames($Downloads,'newDwndTxt','name')?></span> <span class="date"><strong>&gt; </strong><?=$parObj->_getLabenames($Downloads,'newBckTxt','name')?></span></div>
    <div class="clear"></div>
    <div class="container-2">
    <h2><?=$parObj->_getLabenames($Downloads,'newDwnCaptionTxt','name')?></h2>
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="table-4">
      <tr>
    <td width="11%"><img src="images/windows.png" alt="Windows" /></td>
    <td width="24%"><strong>&gt;</strong> <?=$parObj->_getLabenames($Downloads,'newPcVerTxt','name')?></td>
    <td width="65%"><a href="Jiwok_Multilingual.exe" class="button-7"><?=$parObj->_getLabenames($Downloads,'newDwnBtnTxt','name')?></a></td>
  </tr>
  
  <tr>
    <td colspan="3"><hr class="blu2" /></td>
    </tr>
  <tr>
    <td><img src="images/apple.png" alt="Mac" /></td>
    <td><strong>&gt;</strong> <?=$parObj->_getLabenames($Downloads,'newPcVerTxt','name')?></td>
    <td><a href="Jiwok_Mac.pkg" class="button-7"><?=$parObj->_getLabenames($Downloads,'newDwnBtnTxt','name')?></a></td>
  </tr>
   <tr>
    <td colspan="3"><hr class="blu2" /></td>
    </tr>
  <tr>
    <td rowspan="2"><img src="images/android.png" alt="Mac" /></td>
    <td rowspan="2"><strong>&gt;</strong> <?=$parObj->_getLabenames($Downloads,'newPcVerTxt','name')?></td>
    <td><a href="http://itunes.apple.com/us/app/jiwok/id377378756?mt=8" class="button-7"><?=$parObj->_getLabenames($Downloads,'newIPnAppTxt','name')?></a></td>
  </tr>
  <tr>
    <td><a href="#" class="button-8"><?=$parObj->_getLabenames($Downloads,'newMobAppTxt','name')?></a></td>
  </tr>
    </table>
    <div class="clear"></div>
    <div class="btm"></div>
    </div>
   </div>
</div>
<?php include("footer.php"); ?>