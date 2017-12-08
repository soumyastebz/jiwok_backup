<?php
session_start();
ob_start();

include_once('includeconfig.php');
include_once("includes/classes/class.Languages.php");
include_once("includes/classes/class.programs_eng_beta.php");

if($lanId == "") $lanId=1;
$errorMsg = '';	 

$userid 	= $_SESSION['user']['userId'];	 
$objGen     = new General();
$objPgm     = new Programs($lanId);
$parObj 	= new Contents();
$objLan    	= new Language();

$returnData	= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
$arrayData	= $returnData['general'];
$lanName 	= strtolower($objLan->_getLanguagename($lanId));
$todayDate 	= date('Y-m-d');
$link_separator = "<li>|</li>";

// Number of records to show per page
$recordsPerPage = 1;

// //default startup page
$opt_links_count = 5; 	

$max = $opt_links_count;
if(isset($_GET['p']))
	{
		$pageNum = $_GET['p'];
		settype($pageNum, 'integer');
	}
else 
 	$pageNum 	= 1;

$pgm_flexid = trim($_REQUEST['pgm_flexid']);
$pgmid 		= trim($_REQUEST['pgmid']);

$subscribeDetails 	= 	$objPgm->_getSubscriptionDetails($userid,$pgmid);
$workOuts 			=  	$objPgm->_getTrainingCalWorkoutDates($userid);
$workOutDays 		=  	$objPgm->_getTrainingCalWorkoutDays($userid);
$orderArray 		= 	$objPgm->_getWorkoutOrders($pgm_flexid);
$freedays 			= 	$objPgm->_getFreeDays($userid);
$workout_cnt 		= 	$objPgm->_getWorkoutCount($objGen->_output(trim($pgm_flexid)),$lanId);
$workout_flex_ids 	= 	array();
$j			= 1;
$offset 	= ($pageNum - 1) * $recordsPerPage;
$query1 	= "	SELECT workout_flex_id FROM program_workout 
				WHERE training_flex_id='".addslashes(trim($pgm_flexid))."' and lang_id={$lanId} 
				ORDER BY workout_order ASC";
$result1 	= mysql_query($query1);
while($row1 = mysql_fetch_assoc($result1))
	{
		$workout_flex_ids[$j] = $objGen->_output(trim($row1['workout_flex_id']));
		$j++;	
	}
	
$offset = ($pageNum - 1) * $recordsPerPage;
$query 	= "	SELECT workout_flex_id,workout_date 
			FROM program_workout WHERE training_flex_id='".addslashes(trim($pgm_flexid))."' and lang_id={$lanId} 
			ORDER BY workout_order ASC LIMIT $offset, $recordsPerPage";

# 1. Main query
$result = mysql_query($query) or die('Mysql Err. 1');

// print table
$data 		= '';
$provides 	= '';

# 2 change/add columns name
while($row = mysql_fetch_assoc($result))
	{
		$workout_flex_id = $objGen->_output(trim($row['workout_flex_id']));	
		$workout_date = $objGen->_output(trim($row['workout_date']));
		if(count($workOuts)>0 && trim($subscribeDetails['program_type'])=="program")
			{
				$work_date 		= $workOuts[$workout_flex_id."@@".($pageNum - 1)];
				$tmpWorkOutDate	= date('Y-m-d',strtotime($work_date));
				$w_day 			= date('d',strtotime($work_date));
				$w_month 		= date('F',strtotime($work_date));
				if($lanName=="english")
					{
						$w_month = $w_month;
					}				
				else				
					{
						$w_month = $monthArray[$w_month];
					}				
			}	

		$workoutDetail 		= $objPgm->_getWorkoutDetailAll(trim($workout_flex_id),$lanId);
		$workout_detail 	= trim(stripslashes($workoutDetail['workout_desc']));
		$workout_title 		= trim(stripslashes($workoutDetail['workout_title']));
		$workout_provide 	= trim(stripslashes($workoutDetail['workout_provide']));
		$data 		.= "$workout_detail";
		$provides 	.= $workout_provide;
	}
	# Update this query with same where clause you are using above.

$query 	= "	SELECT COUNT(workout_flex_id) AS dt FROM program_workout 
			WHERE training_flex_id='".addslashes(trim($pgm_flexid))."' and lang_id={$lanId} 
			ORDER BY workout_order ASC";
$result 	= mysql_query($query) or die('Mysql Err. 2');
$row 		= mysql_fetch_assoc($result);
$numrows 	= $row['dt'];

# 4
$maxPage = ceil($numrows/$recordsPerPage);
if($opt_links_count) 
	{
		$start_from = $pageNum - round($max/2) + 1; 			// = 4 - round(5/2) + 1 = 4-3+1 = 2
		$start_from = ($maxPage - $start_from < $max) ? $maxPage - $max + 1 : $start_from ; //(9-2) < 9 ? If yes, 9-5+1. | If no, no change.
		$start_from = ($start_from > 1) ? $start_from : 1;	// If it is lesser than 1, make it 1(all paging must start at the '1' page as it is the first page) : = 2
	} 
else 
	{ // If $opt_links_count is 0, show all pages
		$start_from = 1;
		$max = $maxPage;
	}

$i = $start_from;
$count = 0;
$nav = '';

# 2 change/add columns name
while($row = mysql_fetch_assoc($result))
	{
		$workout_flex_id = $objGen->_output(trim($row['workout_flex_id']));	
		$workout_date = $objGen->_output(trim($row['workout_date']));
		if(count($workOuts)>0 && trim($subscribeDetails['program_type'])=="program")
			{
				$work_date 		= $workOuts[$workout_flex_id."@@".($pageNum - 1)];
				$tmpWorkOutDate	= date('Y-m-d',strtotime($work_date));
				$w_day 			= date('d',strtotime($work_date));
				$w_month 		= date('F',strtotime($work_date));
				if($lanName=="english")
					{
						$w_month = $w_month;
					}				
				else				
					{
						$w_month = $monthArray[$w_month];
					}				
			}	

		$workoutDetail 		= $objPgm->_getWorkoutDetailAll(trim($workout_flex_id),$lanId);
		$workout_detail 	= trim(stripslashes($workoutDetail['workout_desc']));
		$workout_title 		= trim(stripslashes($workoutDetail['workout_title']));
		$workout_provide 	= trim(stripslashes($workoutDetail['workout_provide']));
		$data 		.= "$workout_detail";
		$provides 	.= $workout_provide;
	}
	# Update this query with same where clause you are using above.

$query 		= "	SELECT COUNT(workout_flex_id) AS dt FROM program_workout 
				WHERE training_flex_id='".addslashes(trim($pgm_flexid))."' and lang_id={$lanId} 
				ORDER BY workout_order ASC";
$result 	= mysql_query($query) or die('Mysql Err. 2');
$row 		= mysql_fetch_assoc($result);
if($row) $numrows 	= $row['dt'];


//Display '$opt_links_count' number of links

if($workout_cnt>0)
	{
		include_once("workout_generation.php"); // To get the workout MP3 generation onclick		
		
		 $MP3topButton ="<input style=\"font-size: 16px;\" type=\"button\" class=\"btn-sign\" value=\"".mb_strtoupper($parObj->_getLabenames($arrayData,'generateMP3','name'),'UTF-8')."\"".$workoutOnclick.">";
	
		// $MP3topButton ="<a href=\"javascript:;\" class=\"new_back_btn1\"".$workoutOnclick.">".$parObj->_getLabenames($arrayData,'generateMP3','name')."</a>";
		
	}
else
	{
		$MP3topButton ="";
		//<a href=\"javascript:;\" class=\"button-6\">".$parObj->_getLabenames($arrayData,'generateMP3','name')."</a>
	}

echo $MP3topButton;
?>
