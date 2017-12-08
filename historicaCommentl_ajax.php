<? 

session_start();

include_once('includeconfig.php');

include_once("includes/classes/class.programs.php");

include_once("includes/classes/class.Languages.php");



if($lanId=="")

     $lanId=1;



$errorMsg = '';	 

$userId = $_SESSION['user']['userId'];	

$objGen     	= new General();

$objPgm     	= new Programs($lanId);

$objLan    		= new Language();

$parObj 		= new Contents('showProgram.php');

$headingData	= $parObj->_getTagcontents($xmlPath,'trainingprogram','pageHeading');

$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');

$arrayData		= $returnData['general'];



if($_SESSION['folder'] == 1) { $folder = "../"; } elseif(!isset($_SESSION['folder']) || $_SESSION['folder'] == 2) { $folder = "";}



$lanName =  strtolower($objLan->_getLanguagename($lanId));

$workoutArray = $objPgm->_getTrainingCalWorkoutDates($userId);



$_REQUEST['workFlex'] = str_replace(' ','+',trim($_REQUEST['workFlex']));

$workoutFlex = trim($_REQUEST['workFlex']);

$pgmid = trim($_REQUEST['pgmid']);

$workoutOrder = trim($_REQUEST['workOrder']);

$work_date = trim($_REQUEST['workDate']);

$_REQUEST['workoutFlexId_cal'] = str_replace(' ','+',trim($_REQUEST['workoutFlexId_cal']));

$work_Flex = trim($_REQUEST['workoutFlexId_cal']);

$workDate = date('Y-m-d',strtotime($work_date));

$data 	  	 = $objPgm->_displayTrainingProgram($pgmid,$lanId);

$flexid = stripslashes(trim($data['flex_id']));

$workDetails = $objPgm->_getWorkoutDetailAll($workoutFlex,$lanId);

$work_title = trim(stripslashes($workDetails['workout_title']));

$work_desc = trim(stripslashes(strip_tags($workDetails['workout_desc'])));

$work_advice = trim(stripslashes(strip_tags($workDetails['workout_provide'])));



$cday = date('d',strtotime($workDate));

$cmonth = date('F',strtotime($workDate));



if(strtolower(trim($lanName))=="english")

	{$cday = $cday; $cmonth = $cmonth;}

else

	{$cday = $cday; $cmonth = utf8_encode($monthArray[$cmonth]);}



/*$workout_cnt = $objPgm->_getWorkoutCount($flexid,$lanId);

$pgmType  	 = $objPgm->_getName(trim($data['training_type_flex_id']),$lanId);

$programType = $trainingTypeFlexId[trim($data['training_type_flex_id'])]; // set in global variable

$pgmFor = $objPgm->_getGroups(trim($data['program_for']),$lanId,'group');

$schedule = $objPgm->_getName1(trim($data['schedule_type']),$lanId,'schedule_type');

$pgmCategory = $objPgm->_getName1(trim($data['program_category_flex_id']),$lanId,'category');

$pgmLevel 	 = $objPgm->_getName1(trim($data['program_level_flex_id']),$lanId,'levels');*/



$userPhotoPath = $folder."uploads/users/";

$imgPath = $folder."uploads/programs/";

$image = $objGen->_output(trim($data['program_image']));

if($image != "")

{

$imageParams = $objPgm->_imageResize(trim($image),$imgPath,119,147);

$imageWidth  = $imageParams[0];

$imageHeight  = $imageParams[1];

}

if(($userId==93740)&&($pgmid==451)&&($workoutOrder==8)){
	$query = "SELECT t1.feedback_id as feedback_id,t1.feedback_desc as description FROM feedback as t1,user_master as t2 WHERE t1.user_id  = t2.user_id AND t1.feedback_id = 15064 ORDER BY feedback_datetime DESC";
	$feedbacks	 = $GLOBALS['db']->getAll($query, DB_FETCHMODE_ASSOC);
}else{
	$feedbacks = $objPgm->_getWorkoutUserFeedbacks($userId,$pgmid,$workoutFlex,$lanId);
}


if(count($feedbacks)<=0)

{

$display.='<form name="comment" action="historical.php" id="comment" method="post">

     <section class="comments">
	  <h3>'.$parObj->_getLabenames($arrayData,"newMyCmtTxt","name").$parObj->_getLabenames($arrayData,"newMyCmtEgTxt","name").'</h3>
		
<textarea class="HistoryCommetTextbx_his" name="comment_text1" id="comment_text1" cols="25" rows="4"></textarea>

	 </section >
	
	
	<input class="btn-orng" name="add" type="submit" value="'.$parObj->_getLabenames($arrayData,"newOkTxt","name").'" onclick="return addComment1();" />


					<input type="hidden" name="program_id" id="program_id" value="'.trim($_REQUEST['pgmid']).'" />

					<input type="hidden" name="workout_flexid" id="workout_flexid" value="'.trim($_REQUEST['workFlex']).'"/>

					<input type="hidden" name="workout_flexid_cal" id="workout_flexid_cal" value="'.trim($_REQUEST['workoutFlexId_cal']).'"/>	

					<input type="hidden" name="workOrder" id="workOrder" value="'.trim($_REQUEST['workOrder']).'"/>

					<input type="hidden" name="workDate" id="workDate" value="'.trim($_REQUEST['workDate']).'"/>

					<input type="hidden" name="lang_cid" id="lang_cid" value="'.$lanId.'"/>

				</form>';

}

else{

for($i=0;$i<count($feedbacks);$i++)

	{

		$feedback_id = trim(stripslashes($feedbacks[$i]['feedback_id']));

		$desc = trim(stripslashes($feedbacks[$i]['description']));

	}
	
	
	$display .='<div  id="mycommentsEdt">
       <section class="comments">
	  <h3>'.$parObj->_getLabenames($arrayData,"newMyCmtTxt","name").$parObj->_getLabenames($arrayData,"newMyCmtEgTxt","name").'</h3>
		   <a  href="#com" onclick=\'showEditComment("'.$workoutFlex.'","'.$pgmid.'","'.$workoutOrder.'","'.$work_date.'","'.$work_Flex.'","'.$feedback_id.'")\' style="cursor:pointer; text-decoration:none;font-weight:bold">'.$parObj->_getLabenames($arrayData,"editcommt","name").'</a>
		   
		   
	<textarea class="HistoryCommetTextbx_his" name="comment_text1" id="comment_text1" cols="25" rows="4">'.$desc.'</textarea></section>
	</div>
	';
	
	

	//$display .= "<div id=\"produiRight3\"><img src=\"".$folder."images/produi-right-box2-top2.jpg\" alt=\"top\" /><span class=\"produiRight2HdNew\"><label class=\"produiRight2Hd1_hisNew\" style=\"padding:0 0 0px 0px;\">".$parObj->_getLabenames($arrayData,'historycomment','name')."</label><label class=\"produiRight2Hd2_hisNew\"><a  href=\"#com\" onclick=\"showEditComment('$workoutFlex','$pgmid','$workoutOrder','$work_date','$work_Flex','$feedback_id')\" style=\"cursor:pointer; text-decoration:none;font-weight:bold\">".$parObj->_getLabenames($arrayData,'editcommt','name')."</a></label></span><ul style=\"margin: 5px 0px 0px 10px;\"><li>".$desc."</li></ul><img src=\"".$folder."images/produi-right-box2-btm2.jpg\" alt=\"top\" /></div>";

/*$display.="<li>&nbsp;</li><li>&nbsp;</li><li>&nbsp;</li><li  style=\"text-aligns:left\"><h1>".$parObj->_getLabenames($arrayData,'mycomments','name')."</h1></li><li>&nbsp;</li><li>&nbsp;</li>";

	     $display.="<li>".$desc."&nbsp;[<a  href=\"#com\" onclick=\"showEditComment('$workoutFlex','$pgmid','$workoutOrder','$work_date','$work_Flex','$feedback_id')\" style=\"cursor:pointer; text-decoration:none;\">".$parObj->_getLabenames($arrayData,'edit','name')."</a>]</li>";*/

}



if(count($feedbacks)>0)

{			


$display.="<div id=\"updateComment\" ";

if($_REQUEST['feed']!="")

$display.="style=\"display:block;\">";

else

$display.="style=\"display:none;\">";

$display.='<form name="upcomment" action="historical.php" id="upcomment" method="post">
<section class="comments" >
	  <h3>'.$parObj->_getLabenames($arrayData,"newMyCmtTxt","name").$parObj->_getLabenames($arrayData,"newMyCmtEgTxt","name").'</h3>

<textarea class="HistoryCommetTextbx_his" name="comment_text2" id="comment_text2" cols="25" rows="4" >'.trim($desc).'</textarea>
		
	</section>
	
	<input class="btn-orng" name="update" type="submit" value="'.$parObj->_getLabenames($arrayData,"editcommt","name").'" onclick="return addComment2();" />
	
<div id="chkContainer"></div>

<input type="hidden" name="program_id" id="program_id" value="'.trim($_REQUEST['pgmid']).'" />

					<input type="hidden" name="workout_flexid" id="workout_flexid" value="'.trim($_REQUEST['workFlex']).'"/>

					<input type="hidden" name="workout_flexid_cal" id="workout_flexid_cal" value="'.trim($_REQUEST['workoutFlexId_cal']).'"/>	

					<input type="hidden" name="workOrder" id="workOrder" value="'.trim($_REQUEST['workOrder']).'"/>

					<input type="hidden" name="workDate" id="workDate" value="'.trim($_REQUEST['workDate']).'"/>

					<input type="hidden" name="feedback_id" id="feedback_id" value="'.trim($feedback_id).'"/>

					<input type="hidden" name="lang_cid" id="lang_cid" value="'.$lanId.'"/>

				</form></div>';

}
echo $display;



?>
