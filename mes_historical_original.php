<?php
session_start();

ini_set('display_errors',0);
error_reporting(E_ERROR | E_PARSE);

include_once('includeconfig.php');
include_once('./includes/classes/class.member.php');
include_once("includes/classes/class.sort.php");
include_once('./includes/classes/class.Languages.php');
include_once("includes/classes/class.discount.php");
include_once("includes/classes/class.programs.php");
include_once("includes/classes/class.Payment.php");
include_once('includes/classes/class.Documents_Download.php');
settype( $temptime, double ); // for date add function in general class

// create an object for manage the parsed content 
$parObj 		=   new Contents();
$objGen   		=	new General();
$dbObj     		=   new DbAction();	
$objMember		= 	new Member($lanId);
$objDisc		= 	new Discount($lanId);
$lanObj 		= 	new Language();	
$objPgm     	= 	new Programs($lanId);
$paymentObj		=	new Payment();
$objSort	    =		new Sort($lanId);
if($_SESSION['user']['userId']==''){
	header('location:login_failed.php?login=failed');
}else{
	$userId	=	$_SESSION['user']['userId'];
}
if($lanId=="") $lanId=1;
$lanName 				= strtolower($lanObj->_getLanguagename($lanId));
if(!isset($lanName ))
{
	$lanName 			=	"french";
}
$docDownloadObj			= new Documents_Download($lanId, $lanName);
$pdf 		= 0;
$dbClass	=	new DbAction();
//$dbQuery	=  "select max(payment_expiry_date) as expiry_date from payment_cronjob where user_id='".$userid."' and status='PAID' OR status='VALID'";
$dbQuery	=  "select count(payment_id) as count from payment where payment_userid='".$userId."' AND version='New' AND payment_status='1'";
$resDb		=	$dbClass->dbSelectOne($dbQuery,DB_FETCHMODE_ASSOC);
if($resDb['count']>0)
{
	$pdf = 1;
}

$program = $objPgm->_getUserTrainingProgram($userId);

if(count($program)>0) 
	{
		$program_stat	= 'p';
		$flexid 		= stripslashes(trim($program['flex_id']));
		$program_id		= stripslashes(trim($program['program_id']));
		$pgmSubscribe	  	 = $objPgm->_getSubscriptionDetails($userid,$program_id);
		$imgPath = "uploads/programs/";
		$image = $objGen->_output(trim($program['program_image']));
	
		if($image != "")
			{
				$imageParams = $objPgm->_imageResize(trim($image),$imgPath,119,147);
				$imageWidth  = $imageParams[0];
				$imageHeight  = $imageParams[1];
			}
			
		$workout_cnt 		= $objPgm->_getWorkoutCount($flexid,$lanId);
		$data 				= $objPgm->_displayTrainingProgram($program_id,$lanId);
		/*Limit description text*/
		$pgmDesc 	= 	substr(strip_tags($data['program_desc']),0, 300);
		$pos	 	= 	strrpos($pgmDesc, " ");
		if($pos>0) 
			{
				$pgmDesc = substr($pgmDesc, 0, $pos);
			}

		$workoutDatesArray 	= $objPgm->_getTrainingCalWorkoutDates($userId);
		$workoutOrderArray 	= $objPgm->_getWorkoutOrders($flexid);
		$workOrder 			= 0;
		foreach($workoutDatesArray as $key => $value)
		{
			$work_out = explode("@@",$key);
			$workOrder++;
			if($value>=$today)
			{
				$workDate	   = $value;  // store workout dates
				$workDateH	   = $value; // store workout date for historical page
				$workoutFlexId = trim($work_out[0]);
				break;
			}
		}

		if($workDate==$today)
			{
	    		$workText = $parObj->_getLabenames($arrayData,'workoutday','name'); 
			}	
		else
	  		{ 
	    		$noWorkText 	= $parObj->_getLabenames($arrayData,'noworkout','name');
	    		$workText 		= $parObj->_getLabenames($arrayData,'nextworkout','name'); 
	    		$wtext 			= "nextwork";
			}


		  /* for passing to historical page to show todays workout, if no workout for today then previous workout */
		  if($workDateH==$today)
			{
				$workTodayPresent = 'yes';
				$workOrderHistory = $workOrder; 
			}
		  else
			{
				$workTodayPresent = '';
				$workOrderHistory = $workOrder-1; 
			}
		if($workOrderHistory > 0)
			{
				$workoutFlexHistory = $objPgm->_getWorkoutTodayLast($flexid,$workOrderHistory,$lanId);
				$workoutFlexHistory = $workoutFlexHistory."@".$workOrderHistory;
			}
		/* historical page parameters ends */
		
		$workout_details 			= $objPgm->_getWorkoutDetailAll($workoutFlexId,$lanId);
		//For Document Downloads
		$workouts_subscribed		= $objPgm->getWorkoutsOfProgram($flexid, $lanId);
		$program_document_download	= $docDownloadObj->getTrainingProgramPDFFiles($data['flex_id'], $data['program_title']);
		$workouts_document_download	= $docDownloadObj->getWorkoutPDFFiles($workouts_subscribed);

	} 
else 
	{ 
		// GET SUGGESTED PROGRAMS 
		define('PROGRAMS_NUM', 10);
		$last_used_program		= $objSort->getLastUsedProgram($userId);
		$completed_data 		= $program_ids = $program_ids_arr = $selected_programs = $program_titles = array();
		if($last_used_program != '') 
			{ 
				// Get programs from DB maching user's profile
				$matched_programs_from_db	= $objSort->_getWizardIds($userid,4,'',$lanId);
				// change format of array
				foreach($matched_programs_from_db as $matched_program_from_db) 
					{
						$matched_programs[]	= $matched_program_from_db['pgmId'];
					}
				// Get programs already completed by user
				$completed_data_from_db	= $objSort->getCompletedPrograms($userid);
				// change format of array
				foreach($completed_data_from_db as $one_completed_data_from_db) 
					{
						$completed_data[]	= $one_completed_data_from_db['program_id'];
					}
				// remove completed programs from matched programs
				$unique_matched_programs	= array_diff($matched_programs, $completed_data);
				// if not, get the flex_ids as a comma separated string 
				$programs_after				= $objSort->getSuggestedProgramsAfterThis($last_used_program);
				// get program_ids from the comma separated string of flex_ids
		
				if($programs_after != '') 
					{
						$program_ids_arr	= $objSort->getProgramIdsFromWizardAfter($programs_after);
					}
				// change format of array
				foreach($program_ids_arr as $program_id_arr) 
					{
						$program_ids[]	= $program_id_arr['program_id'];
					}
				//get 4 programs in order of matched
				foreach($unique_matched_programs as $matched_program_id) 
					{
						if(in_array($matched_program_id, $program_ids)) 
							{
								$selected_programs[]	= 	$matched_program_id;
								if(sizeof($selected_programs)==PROGRAMS_NUM) 
								break;
							} 
					}
				// get size of selected programs
				$size_of_selected_programs	= sizeof($selected_programs);
				// if size of selected programs < 4 get more programs from suggested programs to make the size 4
				if($size_of_selected_programs<PROGRAMS_NUM)
					{
						// calculate count of needed programs to make count of selected programs 4
						$needed_programs_count	= PROGRAMS_NUM-$size_of_selected_programs;
						// for each program in suggested programs, if program not in selected programs, add to it. decrement needed programs count by 1. If needed programs count equals 0, break out of loop
						foreach($program_ids as $suggested_program_id ) 
							{
								if(!in_array($suggested_program_id, $selected_programs))
									{
										$selected_programs[]	= 	$suggested_program_id;
										$needed_programs_count--;
									}
								if($needed_programs_count == 0) break;
							}
					}

			}
		else 
			{ 
			 	if(count($program)==0)
			 		{
						 // if no programds subscribed yet
						 $otherPrograms = $objSort->_getpgmid($lanId);
						 foreach($otherPrograms as $otherProgram)
						 {
							$selected_programs[]	= $otherProgram['program_id'];
						 }
						 $pgrmcountflag=1;
			 		}
				 else
				 	{
						 //Not subscribed  a program yet
						 $otherPrograms = $objSort->_getWizardIds($userid, PROGRAMS_NUM,'',$lanId);
						 foreach($otherPrograms as $otherProgram)
						 {
							$selected_programs[]	= $otherProgram['pgmId'];
						 }
			 		}
			}
		// get the titles from 4 of them
		if(sizeof($selected_programs) != 0) 
			{
				$program_titles	= $objSort->getProgramTitleFromId($selected_programs, $lanId);
			}
		$cnt = count($program_titles);	
		unset($matched_programs_from_db, $matched_programs, $completed_data_from_db, $completed_data, $unique_matched_programs, $programs_after, $program_ids_arr, $program_ids, $selected_programs);
    }
    
?>
