<?php 

ob_start();

session_start();

include_once('includeconfig.php');

ini_set('display_errors',0);

error_reporting(E_ERROR | E_WARNING);

//error_reporting(E_ERROR ^  E_NOTICE);

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



$parObj 		= new Contents('userArea.php');

//$headingData	= $parObj->_getTagcontents($xmlPath,'trainingprogram','pageHeading');

$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');

$arrayData		= $returnData['general'];

$docDataAll		= $parObj->_getTagcontents($xmlPath,'documentListing','label');

$docDataGen		= $docDataAll['general'];



$lanName =  strtolower($objLan->_getLanguagename($lanId));

$docDownloadObj	= new Documents_Download($lanId, $lanName);

$error = "";

$today = date('Y-m-d');

//$pgm_expdate='';

//$program_id  = base64_decode(trim($_REQUEST['program_id']));

$redirect_url = base64_encode('document_listing.php');

if(!($objPgm->_checkLogin()))

{

header('location:index.php?returnUrl='.$redirect_url);

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
$checkpay	=	$objPgm->_checkUserPaymentPeriod($userid);

?>
<?php include_once("header.php"); ?>
<style type="text/css">
.indiBanner{
    color: #FF9B36;
    font-size: 56px;
    font-weight: bold;
	margin-left:307px;
    margin-top: 116px;
    position: absolute;
    z-index: 1000;
}
#disableanchor{
	 pointer-events: none;
   	 cursor: default;
}
.nopayUserUrlTxt,.nopayUserUrlTxt a{
    color: #FF4D09 !important;
    font-size: 14px !important;
    font-weight: bold !important;
    padding-bottom: 20px !important;
    padding-left: 20px !important;
    text-decoration: underline !important;
}
</style>
<!--
 <script src="js/flaunt.js"></script>
-->
 <div class="frame_inner">
    <div class="row-1"><div class="return">
          
            <a class="small" href="javascript:history.go(-1);"><?=mb_strtoupper($parObj->_getLabenames($docDataGen,'newBackTxt','name'),'UTF-8')?></a>
         </div>
         <div class="title">
          <ul class="bredcrumbs">
              <li><?=mb_strtoupper($parObj->_getLabenames($docDataGen,'newPageTxt','name'),'UTF-8')?> :</li>
        <li><a href="<?=ROOT_JWPATH?>index.php"><?=mb_strtoupper($parObj->_getLabenames($docDataGen,'newHmeTxt','name'),'UTF-8')?></a></li>
        <li>></li>
        <li><a href="#" class="select"><?=mb_strtoupper($parObj->_getLabenames($docDataGen,'newMyDocTxt','name'),'UTF-8')?></a></li>
           </ul>
           <p class="Q"><?=mb_strtoupper($parObj->_getLabenames($docDataGen,'newHeadTxt','name'),'UTF-8')?></p>
      </div>
         
         </div>
       <div class="clear"></div>
       <div class="downloads">
		  
    <?php if($checkpay !=1){ ?>
     <div class="row">
          <a href="payment_new.php"> <?=$parObj->_getLabenames($docDataGen,'nopaylink','name');?> >>></a>
     </div>
    <div class="nnactv"><?php if($lanId==5) { echo "Nie aktywne"; } else { echo "Non Activé";} ?>
    </div>
   <?php } ?>
		   
		   
		   
		   <?php if(isset($static_documents_download) && sizeof($static_documents_download)>0){ ?>
	 <h3><?=$parObj->_getLabenames($docDataGen,'newMyBksTxt','name')?></h3>
   <?php foreach ($static_documents_download as $static_document) { ?>
   <div class="row">
    		 <p class="content"> <?php if($lanId	==	5)	echo ucwords($static_document['link']); else echo htmlentities(ucwords($static_document['link']), ENT_QUOTES, "ISO-8859-1");  ?></p>
    		 <span class="button"><a href="<?php echo utf8_decode(htmlentities($static_document['url'],ENT_QUOTES, "ISO-8859-1")); ?>"  <?php if($checkpay !=1){ ?> id="disableanchor" <?php } ?> ><input type="button" value="<?=mb_strtoupper($parObj->_getLabenames($docDataGen,'newDwnd','name'),'UTF-8')?>"></a></span>
  	 </div>
   <?php } ?>
    <div class="clear"></div>
	<?php } ?>
		   
		   
		   
		<h3><?=$parObj->_getLabenames($docDataGen,'newTrPDFTxt','name')?></h3>   
		   
				<?php if(count($program)>0) { ?>
		 <div class="row">
		<p class="content"> <?php echo htmlspecialchars($program_title['program_title']); ?></p>
		<span class="button"><a href="<?php echo ROOT_JWPATH; ?>pdf/programs/download.php" <?php if($checkpay !=1){ ?> id="disableanchor" <?php } ?>  ><input type="button" value="<?=mb_strtoupper($parObj->_getLabenames($docDataGen,'newDwnd','name'),'UTF-8')?>"></a></span>
		</div>
	<?php } 
		if (isset($program_document_download) && sizeof($program_document_download)>0){ 
		foreach ($program_document_download as $program_document) {
	?>
			<div class="row">
			<p class="content">  <?php echo htmlentities(ucwords($program_document['link'])); ?></p>
			<span class="button"><a href="<?php echo utf8_decode(htmlentities($program_document['url'])); ?>"  <?php if($checkpay !=1){ ?> id="disableanchor" <?php } ?> ><input type="button" value="<?=mb_strtoupper($parObj->_getLabenames($docDataGen,'newDwnd','name'),'UTF-8')?>"></a></span>
			</div>
  <?php 
  		}
	} 
  ?>   
	
  	<?php 
	if (isset($single_program_document_download) && sizeof($single_program_document_download)>0){ 
		foreach ($single_program_document_download as $single_program_document) {
	?>
			<div class="row">
			<p class="content"> <?php echo htmlentities(ucwords($single_program_document[0]['link'])); ?></p>
			<span class="button"><a href="download_pdf.php?type=singleprogram&amp;pgm=<?php echo utf8_encode($single_program_document[0]['flex_id']); ?>&amp;title=<?php echo htmlspecialchars(utf8_encode($single_program_document[0]['url_filename'])); ?>" <?php if($checkpay !=1){ ?> id="disableanchor" <?php } ?> ><input type="button" value="<?=mb_strtoupper($parObj->_getLabenames($docDataGen,'newDwnd','name'),'UTF-8')?>"></a></span>
			</div>		
  	<?php
		}
	}
	?>	   
	
	
	<?php if(isset($workouts_document_download) && sizeof($workouts_document_download)>0){ ?>
    
    <h3><?=htmlspecialchars($parObj->_getLabenames($docDataGen,'workout','name'))?></h3>
 
	<?php foreach ($workouts_document_download as $workouts_document) {	?>
    <div class="row">
    		<p class="content"><?php echo htmlentities(ucwords($workouts_document['link'])); ?></p>
    		<span class="button"><a href="<?php echo utf8_decode(htmlentities($workouts_document['url'])); ?>" <?php if($checkpay !=1){ ?> id="disableanchor" <?php } ?> ><input type="button" value="<?=mb_strtoupper($parObj->_getLabenames($docDataGen,'newDwnd','name'),'UTF-8')?>"></a></span>
  </div>		
 
   <?php } ?>

   
	<?php } ?>
	
		   
           
         </div>  
     </div>
     <?php include("footer.php"); ?>
