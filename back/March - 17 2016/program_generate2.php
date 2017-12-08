<?php 
ob_start();
session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);
/*--------------------------------------------------*/
// Project 		: Jiwok
// Created on	: 21-09-2015
// Created by	: soumya
// Purpose		: New Design Integration - Program generate page 
/*--------------------------------------------------*/
include_once('includeconfig.php');
include_once("includes/classes/class.programs_eng_beta.php");
require_once('includes/classes/class.member.php');
include_once("includes/classes/class.coach_master.php");
 
if($lanId=="")
     $lanId=1;

$flag = 0;
$errorMsg = '';	
 
$userid = $_SESSION['user']['userId'];	$objGen     	= new General();

$objCoach     	= new CoachMaster($lanId);
$objPgm     	= new Programs($lanId);
$objMember		= new Member($lanId);
$parObj 		= new Contents('program_generate2.php');

$headingData	= $parObj->_getTagcontents($xmlPath,'trainingprogram','pageHeading');
$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
$arrayData		= $returnData['general'];



//~ $returnData		= $parObj->_getTagcontents($xmlPath,'myprofile','label');
//~ $arrayData		= $returnData['general'];

$error = "";
$pgm_expdate='';

$program_id  = base64_decode(trim($_REQUEST['program_id']));

$returnDataProfile		= $parObj->_getTagcontents($xmlPath,'myprofile','label');
$arrayDataProfile		= $returnDataProfile['general'];$redirect_url = base64_encode('search.php');

if(!($objPgm->_checkLogin()))
	{
		header('location:login_failed.php?returnUrl='.$redirect_url,true,301);
	}
elseif(!isset($_REQUEST['program_id']))
	{
		header('location:search.php',true,301);
	}
elseif(!($objPgm->_checkUserSubscribed($userid,$program_id)))
	{
		header('location:search.php',true,301);
	}

/*echo "<br />".$enco = base64_encode("364");

echo "<br />".$deco = base64_decode("MTg4");*/

//echo "<br />"."3";
$todayDate =date('Y-m-d');
//$genreMusicArray = array('Pop ro' => 1,'Rnb'=>2,'House-electro'=>3,'Rap' => 4,
//				         'Techno-rave'=>5,'Funk-disco-soul'=>6,'World-music (Salsa, reggae, br?sil)'=>7);

$genreMusicArray	= $objMember->_getAllGenre();//to get all the genre
$genreVocalArray	= $objMember->_getAllVocal($lanId);//to get all the vocals
$genreMenus	= $objMember->_getGenreMenus($siteMasterMenuConfig['GENRE_ID'],$lanId);
$freedays = $objPgm->_getFreeDays($userid);
$pgmSubscribe	  	 = $objPgm->_getSubscriptionDetails($userid,$program_id); //  get user program subscription Id 
$data 	  	 = $objPgm->_displayTrainingProgram($program_id,$lanId);
$flexid = stripslashes(trim($data['flex_id']));
$flexid = str_replace('+','%2B',$flexid);

$workout_cnt = $objPgm->_getWorkoutCount($flexid,$lanId);
$workoutflex = $objPgm->_getFirstWorkoutId($flexid,$lanId);
$pgmType  	 = $objPgm->_getName(trim($data['training_type_flex_id']),$lanId);
$programType = $trainingTypeFlexId[trim($data['training_type_flex_id'])]; // set in global variable
$pgmFor = $objPgm->_getGroups(trim($data['program_for']),$lanId,'group');
$schedule = $objPgm->_getName1(trim($data['schedule_type']),$lanId,'schedule_type');
$pgmCategory = $objPgm->_getCatName(trim($data['program_category_flex_id']),$lanId);
$pgmLevel 	 = $objPgm->_getName1(trim($data['program_level_flex_id']),$lanId,'levels');
$pgmLevel		=	explode("(",$pgmLevel);
$pgmLevel		=	$pgmLevel[0];
$userPhotoPath = "uploads/users/";
$imgPath = "uploads/programs/";
$image = $objGen->_output(trim($data['program_image']));
if($image != ""){
	$imageParams = $objPgm->_imageResize(trim($image),$imgPath,118,134);
	$imageWidth  = $imageParams[0];
	$imageHeight  = $imageParams[1];
}

$loginUrl = base64_encode("program_details.php?program_id=".$program_id);
$workOuts =  $objPgm->_getTrainingCalWorkoutDates($userid);
$workoutOrderArray = $objPgm->_getWorkoutOrders($flexid);
$genres = $objPgm->_getUserGenres($userid);//if($userid==7475){//For implementing additional options without disturbing other users

//Added on 19 Dec 09 starts
$userRegLan				=	$objPgm->_getUserLang($userid);
$memorizedGenres		= 	$objPgm->_getUserMemoryGenres($userid); // generes remembered.
$langVocal				=	$objPgm->_getUserLangVocal($userid);// language and voice grade remembered.
//echo "hello<br>";
//print_r($langVocal);
//Added on 19 Dec 09 Ends
//}

if(trim($_REQUEST['p']) != ""){
	$workout_page = trim($_REQUEST['p']);
	$query1 = "SELECT workout_flex_id FROM program_workout WHERE training_flex_id='".addslashes(trim($flexid))."' AND workout_order='$workout_page' and lang_id ={$lanId}";
	$result = $GLOBALS['db']->getRow($query1,DB_FETCHMODE_ASSOC);
	$workoutflex = stripslashes(trim($result['workout_flex_id']));
}else{
	$workout_page = '1';
	$workoutflex = $workoutflex;
}

$qry	=	"select program_type from programs_subscribed where programs_subscribed_id=";

if($pgmSubscribe['program_type']=="single"){
	$work_date_nav	=	$pgmSubscribe['subscribed_date'];
}else{
	$work_date_nav = $workOuts[$workoutflex."@@".($workout_page-1)];
}

$mon = date('n',strtotime($work_date_nav));

$yr = date('Y',strtotime($work_date_nav));

$day = date('j',strtotime($work_date_nav));

$workoutOrder_nav =$workoutOrderArray[$workout_page-1]; 

if($work_date_nav<$todayDate)
	$a = 'a';
elseif($work_date_nav==$todayDate)
	$a = 'b';
elseif($work_date_nav>$todayDate)
 	$a = 'c';	 
if(isset($_POST['shift'])){
	
 	$workoutcal_flexid_form = stripslashes(trim($_REQUEST['workoutcal_flexid']));
	$workoutcal_flexid_explode = explode("@",$workoutcal_flexid_form);
	$workoutcal_flexid = trim($workoutcal_flexid_explode[0]);
	$workoutcal_order =trim($workoutcal_flexid_explode[1]); 
	$shiftSession =stripslashes(trim( $_REQUEST['shiftSession']));
 	$shiftAll = stripslashes(trim($_REQUEST['shiftAll']));
	$shiftSessionValue = explode('.',$shiftSession);
 	$workoutArray = $objPgm->_getTrainingCalWorkoutDates($userid);
 	$workoutDates = array();
	$workoutFlexIds = array();
	$j=0;
    foreach( $workoutArray as $key1 => $value1){
		$work_out1 = explode("@@",$key1);
		$workDates[]	  = $value1;  // store workout dates
		$workoutFlexIds[] = trim($work_out1[0]);
	}
	
		
	//$position = array_search(trim($workoutcal_flexid),$workoutFlexIds);
	$position = array_search(trim($workoutcal_order),$workoutOrderArray);
	if(trim(stripslashes($workoutcal_flexid))== ""){
		$error .= $parObj->_getLabenames($arrayData,'shifterror1','name');
	}else{
		if($shiftAll=="0"){
			foreach( $workoutArray as $key => $value){
				if($j<$position){
					$workoutDates[]	  = $value;
				}else{
					$workoutDay = trim($shiftSessionValue[0]);
					if($shiftSessionValue[1] == 'a')
						$postdate = $objPgm->_findDateNext($value,$workoutDay);
					else
						$postdate = $objPgm->_findDatePrev($value,$workoutDay);
					if($j==$position){
						if($postdate < $pgmSubscribe['subscribed_date']){
							$error .= $parObj->_getLabenames($arrayData,'shifterror2','name'); 
						}
						if(in_array($postdate,$workDates)){
							$error .= $parObj->_getLabenames($arrayData,'shifterror3','name'); 
						}
						if($shiftSessionValue[1] == 'b' && $postdate < $workDates[$j-1] && $workDates[$j-1]!=""){
							$error .= $parObj->_getLabenames($arrayData,'shifterror4','name'); 
						}
						if($shiftSessionValue[1] == 'a' && $postdate > $workDates[$j+1] && $workDates[$j+1]!=""){
							$error .= $parObj->_getLabenames($arrayData,'shifterror4','name'); 
						}
					}		
					if($j>$position){ 
						$postdate = $value;  
					}	
					if(trim($error)=="") {
						$workoutDates[]	= $postdate; 
					}
	
				}
				$j++;  
			}
			if(count($workoutDates) >0){
				if($workoutDates[0] < $pgmSubscribe['subscribed_date']){ 
					$error .= $parObj->_getLabenames($arrayData,'shifterror2','name');
				}
				$lastWorkoutKey = count($workoutDates)-1;
				if($workoutDates[$lastWorkoutKey] > $pgmSubscribe['program_expdate']){
					$pgm_expdate = $objPgm->_findDateNext($workoutDates[$lastWorkoutKey],1);
				}		
			}
		   
			if($error=="" && count($workoutDates) >0){
				$postpondedDates = implode(',',$workoutDates);
				if(trim($pgm_expdate) != ""){
					$setExpDate = ",program_expdate='".addslashes($pgm_expdate)."'";
				}
				$query = "UPDATE programs_subscribed set posponded_date ='".addslashes($postpondedDates)."'".$setExpDate." WHERE 	programs_subscribed_id=".$pgmSubscribe['programs_subscribed_id'];
				$upres = $GLOBALS['db']->query($query);
				$messg = "1";
				$_REQUEST['workoutcal_flexid'] = "";
				header("location:program_generate2.php?program_id=".base64_encode($program_id)."&messg=".$messg,true,301);
			}	
		}
		if($shiftAll =="1"){
			foreach( $workoutArray as $key => $value){
				if($j<$position){
					$workoutDates[]	  = $value;
				}else{
					$workoutDay = trim($shiftSessionValue[0]);
					if($shiftSessionValue[1] == 'a')
						$postdate = $objPgm->_findDateNext($value,$workoutDay);
					else
						$postdate = $objPgm->_findDatePrev($value,$workoutDay);
					if($j==$position){
						if($postdate < $pgmSubscribe['subscribed_date']){
							$error .= $parObj->_getLabenames($arrayData,'shifterror2','name'); 
						}elseif(in_array($postdate,$workoutDates)){
							$error .= $parObj->_getLabenames($arrayData,'shifterror3','name'); 
						}elseif($shiftSessionValue[1] == 'b' && $postdate < $workDates[$j-1] && $workDates[$j-1]!=""){
							$error .= $parObj->_getLabenames($arrayData,'shifterror4','name');
						}else { 
							$workoutDates[]	= $postdate; 
						}	
	
					}		
	
					if($j>$position){ 
					   if(in_array($postdate,$workoutDates)){
							$error .= $parObj->_getLabenames($arrayData,'shifterror4','name');; 
						}else { 
							$workoutDates[]	= $postdate; 
						}	
	
					}	
				}
				$j++; 	
			}
	
				 
			if(count($workoutDates) >0){
				if($workoutDates[0] < $pgmSubscribe['subscribed_date']){ 
					$error .= $parObj->_getLabenames($arrayData,'shifterror2','name');
				}
				$lastWorkoutKey = count($workoutDates)-1;	
				if($workoutDates[$lastWorkoutKey] > $pgmSubscribe['program_expdate']){
					$pgm_expdate = $objPgm->_findDateNext($workoutDates[$lastWorkoutKey],1);
				}		
			}
					   
			if($error=="" && count($workoutDates) >0){
				$postpondedDates = implode(',',$workoutDates);
				if(trim($pgm_expdate) != ""){
					$setExpDate = ",program_expdate='".addslashes($pgm_expdate)."'";
				}
				$query = "UPDATE programs_subscribed set posponded_date ='".addslashes($postpondedDates)."'".$setExpDate." WHERE 	programs_subscribed_id=".$pgmSubscribe['programs_subscribed_id'];
				$upres = $GLOBALS['db']->query($query);
				$messg = "1";
				$_REQUEST['workoutcal_flexid'] = "";
				header("location:program_generate2.php?program_id=".base64_encode($program_id)."&messg=".$messg,true,301);
			}	
		}
	}
}

 	

if(isset($_POST['Comment'])){
	$insArray['feedback_id'] = '';
	$insArray['feedback_subject'] = '';
	$insArray['feedback_desc'] = addslashes(trim(stripslashes($_REQUEST['commentText'])));
	$commantTxt	=	$_REQUEST['commentText'];
	$_SESSION["refComment"]	=	base64_encode($commantTxt);
	$insArray['feedback_datetime'] = addslashes(date('Y-m-d H:i:s'));
	$insArray['program_id'] = $program_id;
	$insArray['workout_flex_id'] = addslashes(trim(stripslashes($_REQUEST['workoutFlex'])));
	$insArray['user_id'] = $userid;
	$insArray['lang_id'] = $lanId;
	$objPgm->_insertDetails($insArray,"feedback");
	
	if($_REQUEST["postFB"]==1){
		$sql	=	"update jiwok_fbshare_popup set showComment=0 where user_id='".$userid."'";
		$upres = $GLOBALS['db']->query($query);
		$_SESSION["refCommentPost"]	=	"1";
	}else{
		$_SESSION["refCommentPost"]	=	"0";
	}
	header("location:program_generate2.php?action=commented&program_id=".base64_encode($program_id),true,301);
}
?>
<?php include("header.php"); ?>

<!--For slider 
Added by	:	Dileep.E
Date		:	07.01.12
Description	:	This section used for including the training programs, which are 
				linked via wizard function in trainer tool, in the slider.-->
<?php
	
$selectQuery  	=	"select wizard_after,wizard_before from program_wizard where training_flex_id	=	'".$flexid."'";
$result			= 	$GLOBALS['db']->getRow($selectQuery, DB_FETCHMODE_ASSOC);	
//$slides = array(0=>'sa',1=>'ds',2=>'fd',3=>'hy',4=>'gh',5=>'ju');
$pgmFlexIds		=	explode(",",$result[wizard_after]);		
$nextArrayPosition=	count($pgmFlexIds)-1;
$pgmBeforFlexIds=	explode(",",$result[wizard_before]);
foreach($pgmBeforFlexIds as $pgmBeforFlexId)
{
	if(!in_array($pgmBeforFlexId, $pgmFlexIds))
	{
		$pgmFlexIds[$nextArrayPosition]	=	$pgmBeforFlexId;
		$nextArrayPosition++;	
	}		
}
//Find programs from the same categories or sub categories	
$pgmDetailsQuery=	"SELECT	program_category_flex_id FROM	program_master	WHERE	flex_id	= '".$flexid."'";
$pgmDetailResult= 	$GLOBALS['db']->getRow($pgmDetailsQuery, DB_FETCHMODE_ASSOC);
$programCategoryFlexId	=	explode(",",$pgmDetailResult[program_category_flex_id]);
$findInCdtn		=	"";	
foreach($programCategoryFlexId	as $key	=>	$programCategoryFlexIds)
{
	if($key	!=	0)
	{
		$findInCdtn		.=	"	OR	FIND_IN_SET('".trim($programCategoryFlexIds)."',program_category_flex_id)";
	}
	else
	{
		$findInCdtn		.=	"	WHERE	(FIND_IN_SET('".trim($programCategoryFlexIds)."',program_category_flex_id)";
	}
}
if($findInCdtn	==	"")
	$findInCdtn	.=	"	WHERE	program_status = '4'";
else
	$findInCdtn	.=	"	)	AND	(program_status = '4')";			
$categoryQuery	=	"SELECT	DISTINCT flex_id FROM	program_master ".$findInCdtn;
$categoryResult	=	$GLOBALS['db']->getAll($categoryQuery, DB_FETCHMODE_ASSOC);	
foreach($categoryResult	as $categoryResults)
{
	if(!in_array($categoryResults[flex_id], $pgmFlexIds))
	{
		$pgmFlexIds[$nextArrayPosition]	=	$categoryResults[flex_id];
		$nextArrayPosition++;	
	}	
}	
shuffle($pgmFlexIds);
//***********to hide polish pgm id=172
if($lanId	==	5)
{
	$adquery	=	"AND  PM.flex_id!=	'D23' ";
}
else
{
	$adquery	=	"";
}
foreach($pgmFlexIds	as $key=>$pgmFlexId)
{
	$selectQuery  	=	"select PM.*,PD.program_title from program_master AS PM LEFT JOIN program_detail AS PD ON PM.program_id =PD.program_master_id where PM.flex_id 	=	'".$pgmFlexId."' AND PD.language_id	=	'".$lanId."'".$adquery;
	$slides[$key]		= 	$GLOBALS['db']->getRow($selectQuery, DB_FETCHMODE_ASSOC);					
	//uploads/programs/<?=$objGen->_output(trim($data['program_image']))		
}
//$slides	=	$pgmFlexIds;
$products='';
foreach($slides as $v)
{
	if($v['program_title']!=""){
	
		if($v['program_image']	!=	"")
		{		
			$imgSource			=	"uploads/programs/".$objGen->_output(trim($v['program_image']));
			$imageParamSlider	= $objPgm->_imageResize(trim($objGen->_output(trim($data['program_image']))),"uploads/programs/",118,128);
			$imageWidthSlider  	= $imageParamSlider[0];
			$imageHeightSlider 	= $imageParamSlider[1];	
		}
		else
		{
			$imgSource	=	"images/no_photo_pgm.jpg";	
			$imageWidthSlider  	= '118';
			$imageHeightSlider 	= '128';	
		}
	
		$pro_urlSlider 		= 	$objPgm->makeProgramTitleUrl($v['program_title']);	
		$pro_urlSlider		= 	strtolower($pro_urlSlider);		
		$normal_urlSlider	= 	$objPgm->normal_url($pro_urlSlider);		
		$formAction	=	$normal_urlSlider."-".$v['program_id'];
		$duration	=	$objGen->_output(trim($v['program_schedule']))." ".$objGen->_output(trim($objPgm->_getName1(trim($v['schedule_type']),$lanId,'schedule_type')));
		if(strtolower($objGen->_output(trim($objPgm->_getName1(trim($v['schedule_type']),$lanId,'schedule_type'))))!="mois" && ($objGen->_output(trim($v['program_schedule']))>1))
			$duration	.=	's';		
		$pgmForSlider	=	"";
		if($v['program_for']	!=	"")	
			$pgmForSlider	=	$objPgm->_getGroups(trim($v['program_for']),$lanId,'group');
		//echo "is: ".$objPgm->_getGroups(trim($v['program_for']),$lanId,'group');
		if(strlen($v['program_title'])	> 100)
			$title	=	substr($v['program_title'],0,70)."....";
		else
			$title	=	$v['program_title'];	
		if($lanId	==	5){ 
			$search_value	=	"Zobacz&nbsp;szczegóły&nbsp;planu";
		}else{
			$search_value	=	"Découvrir cet entraînement";
		}
	$products.='<div class="colums">
                <figure>
                  <img src="'.ROOT_FOLDER.'images/corner-ylw.png" alt="jiwok" class="corner">
                  <img src="'.ROOT_FOLDER.'images/dummy-3.jpg" alt="Jiwok">
                </figure>
                <article>
				<a href="'.$formAction.'"><h3>'.$title.'</h3></a> 
				<form action="'.$formAction.'" method="post">
				<div class="listing">
				   <div class="left"><span>'.$parObj->_getLabenames($arrayData,'duration','name').':</span></div>
				   <div class="right">'.$duration.'</div>
				   <div class="listing">
				   <div class="left"><span>'.$parObj->_getLabenames($arrayData,'rythm','name').':</span></div>
				   <div class="right">'.$objGen->_output(trim($v['program_rythm']))." ".$parObj->_getLabenames($arrayData,'times','name').$slash.$objGen->_output(trim($objPgm->_getName1(trim($v['schedule_type']),$lanId,'schedule_type'))).'</div>
				 </div>
     
				 <div class="listing">
				   <div class="left"><span>'.$parObj->_getLabenames($arrayData,'level','name').': </span></div>
				   <div class="right">'.$objGen->_output(trim($objPgm->_getName1(trim($v['program_level_flex_id']),$lanId,'level'))).'</div>
				 </div>
				 </div>	
				 <div class="rating">
				   <img src="'.ROOT_FOLDER.'images/rating-star_small.png" alt="rating">
				   <img src="'.ROOT_FOLDER.'images/rating-star_small.png" alt="rating">
				   <img src="'.ROOT_FOLDER.'images/rating-star_small.png" alt="rating">
				   <img src="'.ROOT_FOLDER.'images/rating-star_small.png" alt="rating">
				   <img src="'.ROOT_FOLDER.'images/rating-star_small.png" alt="rating">
        		 </div> 
				 <input type="submit" class="btn" value="'.mb_strtoupper($search_value,'UTF-8').'" name="">				  
				</form> 
				</article>
             </div>';
            
}	
}
?>
<div class="frame slider-first">
<div class="callbacks_container">
      <ul class="rslides" id="slider4">
        <li><img src="<?=ROOT_FOLDER?>images/slide_05.jpg" alt="Slide 01"></li>
      </ul>
    </div>
     <section class="entertain_grid">
       <nav  class="b_cumbs">
         <ul>
          <li><?=mb_strtoupper($parObj->_getLabenames($arrayData,'newPgeTxt','name'),'UTF-8');?>:</li>
          <li><a href="<?=ROOT_JWPATH?>"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'newHmeTxt','name'),'UTF-8');?></a></li> <li>&gt;</li>
          <li class="current-itom"><a class="select"><?php echo mb_strtoupper($data['program_title'],'UTF-8');?></a></li>
         </ul>
       </nav>
    <p class="title_01"><?=$data['program_title']?></p>
   <section class="chart">
       <div class="colums">
           <div><?=mb_strtoupper($parObj->_getLabenames($arrayData,'duration','name'),'UTF-8');?> </div>
           <div><span><?=$objGen->_output(trim($data['program_schedule']))?><?=$objGen->_output(trim($schedule))?><?php if(strtolower($objGen->_output(trim($schedule)))!="mois" && ($objGen->_output(trim($data['program_schedule']))>1)) { echo 's';} ?></span></div>
       </div>
       
       <div class="colums">
           <div> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'rythm','name'),'UTF-8');?></div>
           <div><span><?=$objGen->_output(trim($data['program_rythm']))." ".$parObj->_getLabenames($arrayData,'times','name')."/".$objGen->_output(trim($schedule))?></span></div>
       </div>
       
       <div class="colums">
           <div><?=mb_strtoupper($parObj->_getLabenames($arrayData,'workoutnum','name'),'UTF-8');?></div>
           <div><span><?=$workout_cnt?></span></div>
       </div>
       
       <div class="colums">
           <div><?=mb_strtoupper($parObj->_getLabenames($arrayData,'level','name'),'UTF-8');?></div>
           <div><span><?=$objGen->_output(trim($pgmLevel))?></span></div>
       </div>
       
       <div class="colums">
           <div>RATING</div>
           <div><img src="<?=ROOT_FOLDER?>images/star.png" alt="rating">&nbsp;<img src="<?=ROOT_FOLDER?>images/star.png" alt="rating">&nbsp;<img src="<?=ROOT_FOLDER?>images/star.png" alt="rating">
                      &nbsp;<img src="<?=ROOT_FOLDER?>images/star.png" alt="rating">&nbsp;<img src="<?=ROOT_FOLDER?>images/star.png" alt="rating"></div>
       </div>
   
   </section>
   
   
       <nav>
          <a href="<?=ROOT_JWPATH?>userArea.php" class="btn-return"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'newBckTxt','name'),'UTF-8');?></a>
           <!----checking with device code starts----------->
           <?php
           $user_agent     =   $_SERVER['HTTP_USER_AGENT'];
           function getOS() { 
            	global $user_agent;
              	$os_platform    =   "Unknown OS Platform";
            	$os_array       =   array(
										'/windows nt 10/i'     =>  'Windows 10',
										'/windows nt 6.3/i'     =>  'Windows 8.1',
										'/windows nt 6.2/i'     =>  'Windows 8',
										'/windows nt 6.1/i'     =>  'Windows 7',
										'/windows nt 6.0/i'     =>  'Windows Vista',
										'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
										'/windows nt 5.1/i'     =>  'Windows XP',
										'/windows xp/i'         =>  'Windows XP',
										'/windows nt 5.0/i'     =>  'Windows 2000',
										'/windows me/i'         =>  'Windows ME',
										'/win98/i'              =>  'Windows 98',
										'/win95/i'              =>  'Windows 95',
										'/win16/i'              =>  'Windows 3.11',
										'/macintosh|mac os x/i' =>  'Mac OS X',
										'/mac_powerpc/i'        =>  'Mac OS 9',
										'/linux/i'              =>  'Linux',
										'/ubuntu/i'             =>  'Ubuntu',
										'/iphone/i'             =>  'iPhone',
										'/ipod/i'               =>  'iPod',
										'/ipad/i'               =>  'iPad',
										'/android/i'            =>  'Android',
										'/blackberry/i'         =>  'BlackBerry',
										'/webos/i'              =>  'Mobile'
									);
             foreach ($os_array as $regex => $value) { 
                   	  if (preg_match($regex, $user_agent)) {
						$os_platform    =   $value;
					}
                   }   
                  return $os_platform;
               }
               $user_os        =   getOS();
               $device_details =   $user_os;
               //~ $device_details =  'iPod';// for testing
               if($device_details=='iPhone'||$device_details=='iPod'||$device_details=='iPad'){
			    ?><input class="btn-sign" type="button" value="<?php echo mb_strtoupper($parObj->_getLabenames($arrayData,'generateMP3','name'),'UTF-8');?>" onclick="show_device_popup(1)"/>
			   <?php
			   }else if($device_details=='Android'){?>
				   <input class="btn-sign" type="button" value="<?php echo mb_strtoupper($parObj->_getLabenames($arrayData,'generateMP3','name'),'UTF-8');?>" onclick="show_device_popup(2)"/>
			   <?php }else{
			   ?>  <div id="MP3GenerateButtonContainer" style="text-transform: uppercase;"></div>
			   <?php
			   }
               ?>
                
           <!----checking with device code ends------------>
       </nav>
       
    </section>
    </div>

    <section class="goal">
      <div class="frame">
         <p class="title"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'objective','name'),'UTF-8');?></p>
         <p class="description">
         <?php
				$goalarray = array();
				$data['program_target']	=	str_replace("<BR STYLE=\"letter-spacing:0px;color:#0B333C;font-size:10px;font-family:'Verdana';\"/>"," ",$data['program_target']);
				$goal =($objGen->_output(trim($data['program_target'])));
				//$goal = strip_tags($objGen->_output(trim($data['program_target'])));
				$goal = str_replace("\n"," ",$goal);
				$goal = str_replace("<br>"," ",$goal);
				$goal = str_replace("<BR>"," ",$goal);
				$goal = str_replace("<br/>"," ",$goal);
				$goal = str_replace("<BR/>"," ",$goal);
				$goaltext = $goal;
				$goals = explode("</DIV>", $goal);				
				if(end($goals)	==	"")
				{
					array_pop($goals);
				}	
				$goalnum = count($goals);
				if($goalnum > 2){
					//$goaltext = trim($goals[0])."</DIV>".trim($goals[1])."</DIV>";
					$goaltext = trim($goals[0]).".".trim($goals[1]);
					for($i=2;$i<=$goalnum;$i++){
						if($goals[$i] != "")
					 		$goalarray[]=$goals[$i];
					}
					$goal_rest = 	implode("</DIV>",$goalarray);
					$goal_rest.=	"</DIV>";					
				}
				echo trim(strip_tags($goaltext));
					

				?>
              <?php if($goal_rest!= "") {  ?>
            
               <span id="toggleText" style="display: none"><?php echo trim(strip_tags($goal_rest));?>           
              </span></p>
              <a href="javascript:toggle('displayText','toggleText');" id="displayText" class="read"><?=strtoupper($parObj->_getLabenames($arrayData,'readmore','name'));?></a>
               
              <?php } ?>
              </p>
      </div>
    </section>
    
    <section class="ent_description">
      <div class="frame">
         <p class="title"> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'description','name'),'UTF-8');?></p>
         <p class="description">
         <?php
				$descarray = array();
				$data['program_desc']	=	str_replace("<BR STYLE=\"letter-spacing:0px;color:#0B333C;font-size:10px;font-family:'Verdana';\"/>"," ",$data['program_desc']);
				$desc =$objGen->_output(trim($data['program_desc']));
				//$desc =strip_tags($objGen->_output(trim($data['program_desc'])));
				$desc = str_replace("\n"," ",$desc);
				$desc = str_replace("<br>"," ",$desc);
				$desc = str_replace("<BR>"," ",$desc);
				$desc = str_replace("<br/>"," ",$desc);
				$desc = str_replace("<BR/>"," ",$desc);
				$desctext = $desc; 
				$descs = explode("</DIV>", $desc);
				if(end($descs)	==	"")
				{
					array_pop($descs);
				}	
				$descnum = count($descs);
				if($descnum > 2){
					//$desctext = trim($descs[0])."</DIV>".trim($descs[1])."</DIV>";
					$desctext = trim($descs[0]).".".trim($descs[1]);
					for($i=2;$i<=$descnum;$i++){
						if(trim($descs[$i]) != "")
							$descarray[]=$descs[$i];
					}
					$desc_rest = 	implode("</DIV>",$descarray);
					$desc_rest.=	"</DIV>";
					
				}
				echo trim(strip_tags($desctext));
			
				?>
              <?php if($desc_rest!= "") {  ?>
              <span id="toggleText1" style="display: none"><?php echo trim(strip_tags($desc_rest));?></span>
            	</p>
              <a id="displayText" class="read" href="javascript:toggle('displayText1','toggleText1');">
                 <?=mb_strtoupper($parObj->_getLabenames($arrayData,'readmore','name'),'UTF-8');?>
                </a>
              <?php } ?>
        
         
         </p>
      </div>
      
    </section>

    <section class="advice">
      <div class="frame">
         <p class="title"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'provide_t','name'),'UTF-8');?></p>
         <p class="description">
         <!--===============================-->
         <?php
         // To get coach image from coach table.		 
			  /*  $coachName = $objGen->_output(trim($data['program_author']));
				if($coachName)	
				{
					$coachDetails = $objCoach->_getProgramCoachDetails($coachName);
					$coachImage	  = "uploads/coaches/".$coachDetails['coach_image'];
				}
				?><img src="<?=($coachDetails['coach_image']) ? $coachImage :'images/thump-1.jpg'?>" alt="<?=$coachName?>" title="<?=$coachName?>" />  */  
				
				$descarray = array();
				$data['program_provide']	=	str_replace("<BR STYLE=\"letter-spacing:0px;color:#0B333C;font-size:10px;font-family:'Verdana';\"/>"," ",$data['program_provide']);
				$desc = $objGen->_output(trim($data['program_provide']));
				//$desc = strip_tags($objGen->_output(trim($data['program_provide'])));
				$desc = str_replace("\n"," ",$desc);
				$desc = str_replace("<br>"," ",$desc);
				$desc = str_replace("<BR>"," ",$desc);
				$desc = str_replace("<br/>"," ",$desc);
				$desc = str_replace("<BR/>"," ",$desc);
				$desctext = $desc; 
				$descs = explode("</DIV>", $desc);
			
				if(end($descs)	==	"")
				{
					array_pop($descs);
				}	
				$descnum = count($descs);
				if($descnum > 2){
					//$desctext = trim($descs[0])."</DIV>".trim($descs[1])."</DIV>";
					$desctext = trim($descs[0]).".".trim($descs[1]);
					for($i=2;$i<=$descnum;$i++){
						if(trim($descs[$i]) != "")
							$descarray[]=$descs[$i];
					}
					$desc_rest = 	implode("</DIV>",$descarray);
					$desc_rest.=	"</DIV>";
					
	
				}
				echo trim(strip_tags($desctext));
			  ?>
              <?php if($desc_rest!= "") {  ?>
              <span id="toggleText2" style="display: none"><?php echo trim(strip_tags($desc_rest));?></span>            
              </p><a class="read" id="displayText2" href="javascript:toggle('displayText2','toggleText2');">
                 <?=mb_strtoupper($parObj->_getLabenames($arrayData,'readmore','name'),'UTF-8');?>
                </a>
              <?php } ?>  </p>        
         
         
      </div>
    </section>
     
      <section class="seance_1 prgrm_gen_frame">
      <div class="left height_equal" >      
        <?php
	/* display calendar for program training only . Calendar starts*/
	if($programType== "program") { ?>
 <?php /*?>         <script language="javascript" type="text/javascript">navigate("","");</script><?php */?>
           <section class="calender_02">
              <p class="hed"> <?=$parObj->_getLabenames($arrayData,'pgmcalendar','name');?></p>
              <div id="calendar"></div>
              <form action="program_generate2.php" id="shiftcal" name="shiftcal" method="post">
              <span class= "mymsg">
              <?php

							if(trim($error)!="") { echo $error; }

							if(trim($_REQUEST['messg'])!="") { echo "<font color='#FF7533'><strong>".$parObj->_getLabenames($arrayData,'shiftsuccess','name')."</strong></font>"; }?>
              </span>
              <div class="row">
                <div class="colum">
                 <p><?=mb_strtoupper($parObj->_getLabenames($arrayData,'shiftdate','name'),'UTF-8');?></p>
                 
                 <div class="selet3">
                       <select  name="shiftSession" id="shiftSession">
                        <option value="1.b">1
                  <?=$parObj->_getLabenames($arrayData,'day','name');?>
                  <?=$parObj->_getLabenames($arrayData,'before','name');?>
                  </option>
                  <?php for($i=2;$i<=7;$i++) { ?>
                  <option value="<?=$i?>.b">
                  <?=$i?>
                  <?=$parObj->_getLabenames($arrayData,'days','name');?>
                  <?=$parObj->_getLabenames($arrayData,'before','name');?>
                  </option>
                  <?php } ?>
                  <option value="1.a">1
                  <?=$parObj->_getLabenames($arrayData,'day','name');?>
                  <?=$parObj->_getLabenames($arrayData,'after','name');?>
                  </option>
                  <?php for($i=2;$i<=7;$i++) { ?>
                  <option value="<?=$i?>.a">
                  <?=$i?>
                  <?=$parObj->_getLabenames($arrayData,'days','name');?>
                  <?=$parObj->_getLabenames($arrayData,'after','name');?>
                  </option>
                  <?php } ?>
                       </select>
                 </div>
                 </div>                  
                <div class="colum">
                 <p><?=mb_strtoupper($parObj->_getLabenames($arrayData,'shiftotherdate','name'),'UTF-8');?></p>
                 <div class="selet3">
                       <select  name="shiftAll"  id="shiftAll">
                         <option value="0">NON</option>
                         <option  value="1">YES</option>
                       </select>
                 </div>
                 </div>
                </div>
                <input type="hidden" name="program_id" id="program_id" value="<?=$_REQUEST['program_id']?>"/>
                <input type="hidden" name="workoutcal_flexid" id="workoutcal_flexid" value="<?=$_REQUEST['workoutcal_flexid']?>"/>
                <input type="hidden" name="p" id="p" value="<?=$_REQUEST['p']?>"/>
                <div align="right"><input type="submit" class="btn" value="<?=mb_strtoupper($parObj->_getLabenames($arrayData,'shift','name'),'UTF-8');?>" name="shift" ></div>
                </form>
              </section>  
             
          <?php } 
		  else{  ?>
          <script type='text/javascript' src="<?=ROOT_FOLDER?>includes/js/training_calendar_single.js"></script>
<?php /*?>          <script language="javascript" type="text/javascript">navigate("","");</script>
<?php */?>           <section class="calender_02">
             <p class="hed"> <?=$parObj->_getLabenames($arrayData,'pgmcalendar','name');?></p>
             <div id="calendar">
              <div id="calback">
                <div id="calendarsingle"></div>
                </div>
              </div>
           </section>  
          
            
          
          <?php } ?> 
          </div>
             
      <!--=======================================-->
      
      
      
      
  
         
       <div id="produiMidBoxRight"></div>
       
       
       
       
       
       
       
       </section>
     
       <div id="produiRight2"></div>
 <?php if(count($slides)>0){ ?>
      <section class="jw_excercise prgrm_gen_frame">	
    	<h2> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'sliderHead','name'),'UTF-8');?></h2>
         <div class="clear"></div>
         <div class="clear"></div>
          <div class="content_6 content">
          	<div class="images_container" id="slider"><?=$products?></div>
          </div>
   		  </section>
	
    <?php } ?>    
   
<!--- device popup code starts------------->
				<section class="pop_device_popup">
				   <div class="popbox_device_popup">
				     <h3> <?php echo $parObj->_getLabenames($arrayData,'device_msg','name');?></h3>
					  <br>
						  <div align="center" id="popup_link1" style="display:none">
							 <input type="button" class="btn_pop ease" onclick="location.href = 'http://itunes.apple.com/us/app/jiwok/id377378756?mt=8';" value="<?php echo mb_strtoupper($parObj->_getLabenames($arrayData,'clicklink','name')); ?>">
                          </div>
						  <div align="center" id="popup_link2" style="display:none">
							  	 <input type="button" class="btn_pop ease" onclick="location.href = 'https://play.google.com/store/apps/details?id=com.jiwok.jiwok&hl=fr';" value="<?php echo mb_strtoupper($parObj->_getLabenames($arrayData,'clicklink','name')); ?>">
                          </div>
				    </div>
				 </section>
 <!--- device popup code ends------------->
<input type="hidden" name="histFlexId" id="histFlexId"/>
<input type="hidden" name="pageNum" id="pageNum"/>
<script type="text/javascript">
		<?php if($workout_page!=""){ ?>
			$(document).ready(function(){
				document.getElementById("refWoId").value	=	"<?php echo $workout_page; ?>";
			});
		<?php } ?>
		htmlComment('comment_pagination_cal.php','pg=1','<?=$program_id?>','<?=$workoutflex?>');
		htmlData('workout_pagination_cal_eng.php','p=<?=$workout_page?>','<?=$flexid?>','<?=$program_id?>');
		topWorkOutButtonOnclick('workout_onclick.php','p=<?=$workout_page?>','<?=$flexid?>','<?=$program_id?>');
		navigate_1('<?=$mon?>','<?=$yr?>','<?=$a?>','<?=$day?>','<?=$workoutflex?>','<?=$workoutOrder_nav?>');
		
</script>

<?php include("program_generate2_popup.php"); ?>
<?php
$fbPUParent	=	"WOPopup";
if($_REQUEST["action"]=="commented"){
	$fbPUParent	=	"showComment";
}
include("refInclude.php"); 
if($showStsComment==1){
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
			}else if(($isFbPostedComment==0)&&($isFBSharedComment==0)){
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
function callFbWindow(){
	hideUnsubscribe1('produiOverlayBox3');
	var shareTxt	=	document.getElementById("fbt_backup").value;
 	shareTxt	    =	shareTxt.replace("#WOID#",document.getElementById("refWoId").value);
	document.getElementById("refPopupTxtArea").value	=	shareTxt;
	document.getElementById("fbt_share").value	=	shareTxt;
	isPopUp	        =	1;
	<?php if($isFbPostedWO==0){ ?>
		var woStsTmp	=	setRefSts("showWo");
		if(String(woStsTmp)!=String("0")){
			document.getElementById("fbPostPopup").style.display	=	"block";
			showPopup("fbPostPopup","");
		}
	<?php } ?>
}
$(document).ready(function(){
		navigate("","");
});
/***********device popup code starts*****************/
function show_device_popup(id){
	
	jpopup = $('.pop_device_popup').bPopup({	     
              speed: 200,
              positionStyle: 'fixed',});
            if(id==1){
				 $("#popup_link1").show();
			}else if(id==2){
				$("#popup_link2").show();
			 } 
}
/***********device popup ends starts*****************/
</script>
<?php include("footer.php");?>
