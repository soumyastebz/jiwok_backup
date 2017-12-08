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



$parObj 		= new Contents('sw_downloads_beta_NewDesign.php');

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
 <div class="frame_inner">
    <div class="row-1"><div class="return">
            
            <a href="<?=$backButtonLink?>" class="small"><?=mb_strtoupper($parObj->_getLabenames($Downloads,'newBckTxt','name'),'UTF-8')?></a>
         </div>
         <div class="title">
          <ul class="bredcrumbs">
                <li><?=mb_strtoupper($parObj->_getLabenames($Downloads,'newPgeNmeTxt','name'),'UTF-8')?> :</li>
				<li><a href="<?=ROOT_JWPATH?>index.php"><?=mb_strtoupper($parObj->_getLabenames($Downloads,'newPgeHmeTxt','name'),'UTF-8')?></a></li>
				<li>></li>
				<li><a class="select"><?=mb_strtoupper($parObj->_getLabenames($Downloads,'newDwndTxt','name'),'UTF-8')?></a></li>
           </ul>
           <p class="Q"><?=mb_strtoupper($parObj->_getLabenames($Downloads,'newDwndTxt','name'),'UTF-8')?></p>
      </div>
         
         </div>
       <div class="clear"></div>
       <div class="downloads">
            <h3><u><?=$parObj->_getLabenames($Downloads,'newDwnCaptionTxt','name')?></u></h3>
            <div class="row">
              <div class="content2">
               <span class="icon"><img src="<?=ROOT_FOLDER?>images/icon-windows.png" alt="icon-windows"></span>
               <span class="name"><?=$parObj->_getLabenames($Downloads,'newPcVerTxt','name')?></span>
              </div>
              
               <span class="button"><a href="Jiwok_Multilingual.exe" ><input type="button" value="<?=mb_strtoupper($parObj->_getLabenames($Downloads,'newDwnBtnTxt','name'),'UTF-8')?>"></a></span>
             
            </div>
            
             <div class="row">
              <div class="content2">
               <span class="icon"><img src="<?=ROOT_FOLDER?>images/icon-apple.png" alt="icon-apple"></span>
<!--
               <span class="name">Version Mac <small>(compatible à partir de OSX 10.6 et suivant) </small></span>
-->
               <span class="name"><?=$parObj->_getLabenames($Downloads,'mac','name')?></span>
              </div>
              <span class="button"><a href="Jiwok_Mac.pkg"><input type="button" value="<?=mb_strtoupper($parObj->_getLabenames($Downloads,'newDwnBtnTxt','name'),'UTF-8')?>"></a></span>
            </div>
            
            <div class="row">
              <div class="content2">
               <span class="icon"><img src="<?=ROOT_FOLDER?>images/icon-appleI.png" alt="icon-windows"></span>
               <span class="name"><?=$parObj->_getLabenames($Downloads,'newIPnAppTxt','name')?></span>
              </div>
              <span class="button"><a href="http://itunes.apple.com/us/app/jiwok/id377378756?mt=8" target="_blank"><input type="button" value="<?=mb_strtoupper($parObj->_getLabenames($Downloads,'newDwnBtnTxt','name'),'UTF-8')?>"></a></span>
            </div>
            
            <div class="row">
              <div class="content2">
               <span class="icon"><img src="<?=ROOT_FOLDER?>images/icon-android.png" alt="icon-windows"></span>
               <span class="name">Application Android</span>
              </div>
              <span class="button"><a href="https://play.google.com/store/apps/details?id=com.jiwok.jiwok&hl=fr" target="_blank"><input type="button" value="<?=mb_strtoupper($parObj->_getLabenames($Downloads,'newDwnBtnTxt','name'),'UTF-8')?>"></a></span>
            </div>
             
         
      
           
         </div>  
     </div>
<!--
      <script src="js/flaunt.js"></script>
-->
     <script>
			var _gaq=[['_setAccount','UA-20440416-10'],['_trackPageview']];
			(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
			g.src='//www.google-analytics.com/ga.js';
			s.parentNode.insertBefore(g,s)})(document,'script');
		</script>
     <?php include("footer.php"); ?>
