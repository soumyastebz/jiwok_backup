<?php
session_start();
ob_start();
include_once('includeconfig.php');
include_once("includes/classes/class.programs.php");
if($_SESSION['language']['langId']=="") { $lanId=1;  } else { $lanId = $_SESSION['language']['langId']; }
$_SESSION['folder'] = 2;
$flag = 0;
$errorMsg = '';	 
$userid = $_SESSION['user']['userId'];	
$objGen     	= new General();
//dynamic timezone changing
if($tz	=	$objGen->getTomeZonePHP($userid))	date_default_timezone_set($tz);
$objPgm     	= new Programs($lanId);
$parObj 		= new Contents('historical.php');
$headingData	= $parObj->_getTagcontents($xmlPath,'trainingprogram','pageHeading');
$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
$arrayData		= $returnData['general'];
$todayDate =date('Y-m-d');
$error = "";
$today = date('Y-m-d');
//$pgm_expdate='';
//$program_id  = base64_decode(trim($_REQUEST['program_id']));
$redirect_url = base64_encode('historical.php');
if(!($objPgm->_checkLogin())){
	header('location:login_failed.php?returnUrl='.$redirect_url);
}
//===============static start=============
      $_REQUEST['ccess'] = 'Y29tc2Vzcw==';
      ////======static ends===////
if(trim($_REQUEST['ccess']) != ""){
  //Dileep  
  $_REQUEST['workoutFlexId'] = str_replace(' ','+',trim($_REQUEST['workoutFlexId']));
  //$work_flex = explode('@',trim($_REQUEST['workoutFlexId']));  
  //$_REQUEST['workoutFlexId'] = base64_decode(trim($work_flex[0]))."@".$work_flex[1];
  //Dileep
  $work_Flex 	= trim($_REQUEST['workoutFlexId']);
  $workoutF 	= explode('@',trim($_REQUEST['workoutFlexId']));
  $workFlex 	= $workoutF[0]."@@".$workoutF[1];
  $_REQUEST['workoutFlexId'] = $workoutF[0];
  $work_flex 	= $workoutF[0];
  $workoutDatesArray = $objPgm->_getTrainingCalWorkoutDates($userid);
  $pgmUser 	= $objPgm->_getUserTrainingProgram($userid);
  $flexid 	= stripslashes(trim($pgmUser['flex_id']));
  $pgm_id 	= stripslashes(trim($pgmUser['program_id']));
  $workoutOrderArray = $objPgm->_getWorkoutOrders($flexid);
  $newWorkFlex = $workoutF[0]."@@".($workoutF[1]-1);
  $dat = $workoutDatesArray[$newWorkFlex];
  $mon = date('n',strtotime($dat));
  $yr = date('Y',strtotime($dat));
  $day = date('j',strtotime($dat));
  $workoutOrder_nav =$workoutOrderArray[$workoutF[1]-1]; 
  if($dat<$todayDate)
	  $a = 'a';
  elseif($dat==$todayDate)
     $a = 'b';
  elseif($dat>$todayDate)
     $a = 'c';	 
      //===============static start=============
      $_REQUEST['ccess'] = 'Y29tc2Vzcw==';
	 $work_Flex 	= 'AD_NAT_2X(4F-3JB-2M-1R)+2X(4F-1R-3M-2R)(40mn)@19';   
  $workoutF 	= explode('@',trim($_REQUEST['workoutFlexId']));
  $workFlex 	= "AD_NAT_2X(4F-3JB-2M-1R)+2X(4F-1R-3M-2R)(40mn)@@19";
  $_REQUEST['workoutFlexId'] = "AD_NAT_2X(4F-3JB-2M-1R)+2X(4F-1R-3M-2R)(40mn)";
  $work_flex 	= "AD_NAT_2X(4F-3JB-2M-1R)+2X(4F-1R-3M-2R)(40mn)";
  $workoutDatesArray = $objPgm->_getTrainingCalWorkoutDates($userid);
  $pgmUser 	= $objPgm->_getUserTrainingProgram($userid);
  $flexid 	= stripslashes(trim($pgmUser['flex_id']));
  $pgm_id 	= stripslashes(trim($pgmUser['program_id']));
  $workoutOrderArray = $objPgm->_getWorkoutOrders($flexid);
  $newWorkFlex = "AD_NAT_2X(4F-3JB-2M-1R)+2X(4F-1R-3M-2R)(40mn)@@18";
  $dat = $workoutDatesArray[$newWorkFlex];
  $mon = date('n',strtotime($dat));
  $yr = date('Y',strtotime($dat));
  $day = date('j',strtotime($dat));
  $workoutOrder_nav =19; 
  if($dat<$todayDate)
	  $a = 'a';
  elseif($dat==$todayDate)
     $a = 'b';
  elseif($dat>$todayDate)
     $a = 'c';	
	 //================static end================ 
}

if(isset($_POST['update']) && trim($_REQUEST['feedback_id'])!=""){
	 $desc = trim($_REQUEST['comment_text2']);
	 $feedback_id = trim($_REQUEST['feedback_id']);
	 $res = $objPgm->_updateFeedback("feedback",$feedback_id,$desc);
	 $workOrder = trim($_REQUEST['workOrder']);
	 $workDate = trim($_REQUEST['workDate']);
	 //Dileep
	 $workout_flexid_cal = str_replace(' ','+',trim($_REQUEST['workout_flexid_cal']));
	 //$workout_flexid_cal = trim($_REQUEST['workout_flexid_cal']);
	 //Dileep
	 
	 $commantTxt	=	$_REQUEST['comment_text2'];
	 $_SESSION["refComment"]	=	base64_encode($commantTxt);
	 
	 if($_REQUEST["postFB"]==1){
		$_SESSION["refCommentPost"]	=	"1";
	}else{
		$_SESSION["refCommentPost"]	=	"0";
	}
	 
	 
     $msg = $parObj->_getLabenames($arrayData,'msgupcomment','name');
	 //Dileep
	 header("Location:historical.php?action=commented&pgm_id=".base64_encode(trim($_REQUEST['program_id']))."&workoutFlexId=".$workout_flexid_cal."&ccess=Y29tc2Vzcw==&msg=".$msg);
//header("Location:historical.php?pgm_id=".base64_encode(trim($_REQUEST['program_id']))."&workoutFlexId=".base64_encode($workout_flexid_cal)."&ccess=Y29tc2Vzcw==&msg=".$msg);
//Dileep
}

if(isset($_POST['add'])){
	$workOrder = trim($_REQUEST['workOrder']);
	$workDate = trim($_REQUEST['workDate']);
	//Dileep
	$workout_flexid_cal = str_replace(' ','+',trim($_REQUEST['workout_flexid_cal']));
	//$workout_flexid_cal = trim($_REQUEST['workout_flexid_cal']);
	//Dileep
	$insArray = array();
	$insArray['feedback_id']			= '';
    $insArray['feedback_subject']		= '';
	$insArray['feedback_desc'] 		= addslashes(trim($_REQUEST['comment_text1']));
	$insArray['feedback_datetime'] 	= date('Y-m-d H:i:s');
	$insArray['program_id'] 			= addslashes(trim($_REQUEST['program_id']));
	//Dileep
	$wrkFlx  = str_replace(' ','+',trim($_REQUEST['workout_flexid']));
	//$wrkFlx  =trim($_REQUEST['workout_flexid']);
	//Dileep
	$insArray['workout_flex_id'] 		= addslashes(trim($wrkFlx));
	$insArray['user_id'] 				= $userid;
	$insArray['public_status'] 		= '2';
	$insArray['lang_id'] 		= $_REQUEST['lang_cid'];
	
	$commantTxt	=	$_REQUEST['comment_text1'];
	$_SESSION["refComment"]	=	base64_encode($commantTxt);
	
	if($_REQUEST["postFB"]==1){
		$_SESSION["refCommentPost"]	=	"1";
	}else{
		$_SESSION["refCommentPost"]	=	"0";
	}
	
	$res = $objPgm->_insertDetails($insArray,"feedback");
	$msg = $parObj->_getLabenames($arrayData,'msgaddcomment','name');;
	//Dileep Add base64_encode for workout_flexid_cal
	header("Location:historical.php?action=commented&pgm_id=".base64_encode(trim($_REQUEST['program_id']))."&workoutFlexId=".$workout_flexid_cal."&ccess=Y29tc2Vzcw==&msg=".$msg);
//header("Location:historical.php?pgm_id=".base64_encode(trim($_REQUEST['program_id']))."&workoutFlexId=".base64_encode($workout_flexid_cal)."&ccess=Y29tc2Vzcw==&msg=".$msg);
	//Dileep
}
$feedbacks = $objPgm->_getUserFeedbacks($userid);
?>
<?php include("header.php"); ?>
<?php include("menu.php"); ?>
   <div class="frame_inner">
    <div class="row-1"><div class="return">
            <a href="<?=$backButtonLink?>"><?=$parObj->_getLabenames($arrayData,'newBckTxt','name');?></a>
         </div>
         <div class="title">
           <h3>Les bons cadeaux Jiwok </h3>
           iwok s'est associé à de nombreux partenaires afin de vous offrir le meilleur du meilleur.
         </div>
         
         </div>
       <div class="clear"></div>
       
       <div class="panel-left">
          <section class="calender">
             <h3>mon calendrier</h3>
             space for calander plug
          </section>
           
          <section  id="commentSecDiv">
            
          </section>
          
        
       </div>
       <div class="panel-rite">
           <section class="session">

<?php if(trim($_REQUEST['ccess']) != "") { ?>
          <script language="javascript" type="text/javascript">navigate2('<?=$mon?>','<?=$yr?>','<?=$a?>','<?=$day?>','<?=$work_flex?>','<?=$workoutOrder_nav?>');
          showProgram('<?=$work_flex?>','<?=$pgm_id?>','<?=$workoutOrder_nav?>','<?=$dat?>','<?=$work_Flex?>')</script>
          <?php } else { ?>
          <script language="javascript" type="text/javascript">navigate("","");</script>
          <?php } ?>
           <div class="training_detail" id="produitLeftnew" > </div>
           </section>
       </div>
     </div>

<?php include("footer.php"); ?>
<?php
$fbPUParent	=	"showHistory";
include("refInclude.php");

if($showStsHistory==1){
	$dataTpl	=	$visDataTpl;
}else{
	$dataTpl	=	$inVisDataTpl;
}

?>
<script type="text/javascript">

dataTpl	=	'<?php echo $dataTpl; ?>';
<?php
if($_SESSION["fbLoginTest"]=="1"){
	if($_REQUEST["action"]=="commented"){ 
		if($_SESSION["refComment"]!=""){
			if($_SESSION["refCommentPost"]=="1"){
				?>
				$(document).ready(function(){
					document.getElementById("refPopupTxtArea").value	=	"<?php echo base64_decode($_SESSION["refComment"])." . ".$snippedUrl; ?>";
					document.getElementById("fbt_share").value	=	document.getElementById("refPopupTxtArea").value;
					<?php if(USER_STATUS	!= "Allowed"){?>
						fb_login();
					<?php }else{ ?>
						fbPostNew();
					<?php } ?>
				});
				<?php
			}else if(($isFbPostedHistory==0)&&($isFBSharedHistory==0)){
				?>
					isPopUp	=	1;
					document.getElementById("refPopupTxtArea").value	=	"<?php echo base64_decode($_SESSION["refComment"])." . ".$snippedUrl; ?>";
					document.getElementById("fbt_share").value	=	document.getElementById("refPopupTxtArea").value;
					document.getElementById("fbPostPopup").style.display	=	"block";
					showPopup("fbPostPopup","");	 
				<?php
			}
			unset($_SESSION["refComment"]);
		}
	}
}
?>
</script>
<script>
			var _gaq=[['_setAccount','UA-20440416-10'],['_trackPageview']];
			(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
			g.src='//www.google-analytics.com/ga.js';
			s.parentNode.insertBefore(g,s)})(document,'script');
		</script>
