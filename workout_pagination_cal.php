<?php
session_start();
ob_start();
include_once('includeconfig.php');
include_once("includes/classes/class.Languages.php");
include_once("includes/classes/class.programs.php");
//echo "<pre>";
//print_r($_REQUEST);
//$dy = strtotime("2009-11-20"." + 1 month");
//echo date("Y-m-d",$dy);
//exit();

if($lanId=="")
     $lanId=1;

$errorMsg = '';	 
$userid = $_SESSION['user']['userId'];	 
$objGen     	= new General();
$objPgm     	= new Programs($lanId);
$parObj 		= new Contents();
$objLan    		= new Language();
$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
$arrayData		= $returnData['general'];
$lanName =  strtolower($objLan->_getLanguagename($lanId));
$todayDate =date('Y-m-d');
$link_separator = "|";

// Number of records to show per page
$recordsPerPage = 1;
# 0
// //default startup page

$opt_links_count = 5; 	
$max = $opt_links_count;
if(isset($_GET['p']))
{
$pageNum = $_GET['p'];
settype($pageNum, 'integer');
}
else 
 $pageNum = 1;

 $pgm_flexid = trim($_REQUEST['pgm_flexid']);
 $pgmid = trim($_REQUEST['pgmid']);
$subscribeDetails = $objPgm->_getSubscriptionDetails($userid,$pgmid);
$workOuts =  $objPgm->_getTrainingCalWorkoutDates($userid);
$workOutDays =  $objPgm->_getTrainingCalWorkoutDays($userid);
//echo "<pre>";
//print_r($workOutDays);

/*foreach($workOuts as $valdt){
  $first_workout_date = $valdt;
  break;
}*/

$orderArray = $objPgm->_getWorkoutOrders($pgm_flexid);
$freedays = $objPgm->_getFreeDays($userid);
$workout_cnt = $objPgm->_getWorkoutCount($objGen->_output(trim($pgm_flexid)),$lanId);
$workout_flex_ids = array();
$j=1;
$offset = ($pageNum - 1) * $recordsPerPage;
$query1 = "SELECT workout_flex_id FROM program_workout WHERE training_flex_id='".addslashes(trim($pgm_flexid))."' and lang_id={$lanId} ORDER BY workout_order ASC";
$result1 = mysql_query($query1);
while($row1 = mysql_fetch_assoc($result1))
{
$workout_flex_ids[$j] = $objGen->_output(trim($row1['workout_flex_id']));
$j++;	
}

$offset = ($pageNum - 1) * $recordsPerPage;
$query = "SELECT workout_flex_id,workout_date FROM program_workout WHERE training_flex_id='".addslashes(trim($pgm_flexid))."' and lang_id={$lanId} ORDER BY workout_order ASC LIMIT $offset, $recordsPerPage";

# 1. Main query
$result = mysql_query($query) or die('Mysql Err. 1');

// print table
$data = '';
$provides = '';

# 2 change/add columns name
while($row = mysql_fetch_assoc($result))
{
$workout_flex_id = $objGen->_output(trim($row['workout_flex_id']));	
$workout_date = $objGen->_output(trim($row['workout_date']));
if(count($workOuts)>0 && trim($subscribeDetails['program_type'])=="program"){
$work_date = $workOuts[$workout_flex_id."@@".($pageNum - 1)];



//if($userid==54){
$tmpWorkOutDate	= date('Y-m-d',strtotime($work_date));
//mail('webtesters@gmail.com','Test Work out',"Workoutdate---$tmpWorkOutDate----$work_date--");
//}

$w_day = date('d',strtotime($work_date));
$w_month = date('F',strtotime($work_date));
if($lanName=="english")
	{$w_month = $w_month;}
else
	{$w_month = $monthArray[$w_month];}
}	
$workoutDetail = $objPgm->_getWorkoutDetailAll(trim($workout_flex_id),$lanId);
$workout_detail = trim(stripslashes($workoutDetail['workout_desc']));
$workout_title = trim(stripslashes($workoutDetail['workout_title']));
$workout_provide = trim(stripslashes($workoutDetail['workout_provide']));
$data .= "$workout_detail";
$provides .= $workout_provide;
}


# Update this query with same where clause you are using above.
$query = "SELECT COUNT(workout_flex_id) AS dt FROM program_workout WHERE training_flex_id='".addslashes(trim($pgm_flexid))."' and lang_id={$lanId} ORDER BY workout_order ASC";

$result = mysql_query($query) or die('Mysql Err. 2');
$row = mysql_fetch_assoc($result);
$numrows = $row['dt'];
# 4
$maxPage = ceil($numrows/$recordsPerPage);

if($opt_links_count) {
			$start_from = $pageNum - round($max/2) + 1; 			// = 4 - round(5/2) + 1 = 4-3+1 = 2
			$start_from = ($maxPage - $start_from < $max) ? $maxPage - $max + 1 : $start_from ; //(9-2) < 9 ? If yes, 9-5+1. | If no, no change.
			$start_from = ($start_from > 1) ? $start_from : 1;	// If it is lesser than 1, make it 1(all paging must start at the '1' page as it is the first page) : = 2
		} else { // If $opt_links_count is 0, show all pages
			$start_from = 1;
			$max = $maxPage;
		}
$i = $start_from;
$count = 0;
$nav = '';

//Display '$opt_links_count' number of links
while($count++ < $max)
{
	if($i == $pageNum)
	{
	  
	  //$nav .= "<div class=\"pNo\">$i</div>";
	  $nav .= " ".$i." ";
	  if($i<$maxPage)
	  $nav.=$link_separator;
	}
	else
	{
	  $workid = $workout_flex_ids[$i];
	  $work_date = $workOuts[$workid."@@".($i-1)];
	  $mon = date('n',strtotime($work_date));
	  $yr = date('Y',strtotime($work_date));
	  $day = date('j',strtotime($work_date));
	  $workoutOrder =$orderArray[$i-1]; 
	  if($work_date<$todayDate)
	  	$a = 'a';
	  elseif($work_date==$todayDate)
	  	$a = 'b';
	  elseif($work_date>$todayDate)
	  	$a = 'c';	
	  $nav .= " "."<a href=\"#f\" onclick=\"javascript:htmlData('workout_pagination_cal.php','p=$i','$pgm_flexid','$pgmid');htmlComment('comment_pagination_cal.php','pg=1','$pgmid','$workid');navigate_1('$mon','$yr','$a','$day','$workid','$workoutOrder');\">$i</a>";
	   if($i<$maxPage)
	   $nav.=" $link_separator";
	}
	$i++;
	if($i > $maxPage) break; //If the current page exceeds the total number of pages, get out.
}

if ($pageNum > 1)
{

$page = $pageNum - 1;
$workoutprev = $workout_flex_ids[$page];
$work_date1 = $workOuts[$workoutprev."@@".($page-1)];
$mon1 = date('n',strtotime($work_date1));
$yr1 = date('Y',strtotime($work_date1));
$day1 = date('j',strtotime($work_date1));
$workoutOrder1 =$orderArray[$page-1]; 
if($work_date1<$todayDate)
	$b = 'a';
elseif($work_date1==$todayDate)
	$b = 'b';
elseif($work_date1>$todayDate)
	$b = 'c';	
$prev = " "."<a href=\"#f\" onclick=\"javascript:htmlData('workout_pagination_cal.php','p=$page','$pgm_flexid','$pgmid');htmlComment('comment_pagination_cal.php','pg=1','$pgmid','$workoutprev');navigate_1('$mon1','$yr1','$b','$day1','$workoutprev','$workoutOrder1');\"><strong>".$parObj->_getLabenames($arrayData,'prevsession','name')."</strong></a> ";

$workoutfirst = $workout_flex_ids[1];
$k =1;
$work_datef = $workOuts[$workoutfirst."@@".($k-1)];
$monf = date('n',strtotime($work_datef));
$yrf = date('Y',strtotime($work_datef));
$dayf = date('j',strtotime($work_datef));
$workoutOrderf =$orderArray[$k-1]; 
if($work_datef<$todayDate)
	$bf = 'a';
elseif($work_datef==$todayDate)
	$bf = 'b';
elseif($work_datef>$todayDate)
	$bf = 'c';	
$first = " "."<a href=\"#f\" onclick=\"javascript:htmlData('workout_pagination_cal.php','p=1','$pgm_flexid','$pgmid');htmlComment('comment_pagination_cal.php','pg=1','$pgmid','$workoutfirst');navigate_1('$monf','$yrf','$bf','$dayf','$workoutfirst','$workoutOrderf');\"><strong><<</strong></a> ";
}

else
{
$prev = '<strong> </strong>';
$first = '<strong> </strong>';
}


if ($pageNum < $maxPage)
{
$page = $pageNum + 1;
$workoutnext = $workout_flex_ids[$page];
$work_date2 = $workOuts[$workoutnext."@@".($page-1)];
$mon2 = date('n',strtotime($work_date2));
$yr2 = date('Y',strtotime($work_date2));
$day2 = date('j',strtotime($work_date2));
$workoutOrder2 =$orderArray[$page-1]; 
if($work_date2<$todayDate)
	$c = 'a';
elseif($work_date2==$todayDate)
	$c = 'b';
elseif($work_date2 > $todayDate)
	$c = 'c';	
$next = "|<a href=\"#f\" onclick=\"javascript:htmlData('workout_pagination_cal.php','p=$page','$pgm_flexid','$pgmid');htmlComment('comment_pagination_cal.php','pg=1','$pgmid','$workoutnext');navigate_1('$mon2','$yr2','$c','$day2','$workoutnext','$workoutOrder2');\"><strong>".$parObj->_getLabenames($arrayData,'nextsession','name')."</strong></a>";

$workoutlast = $workout_flex_ids[$j-1];
$klast =$j-1;
$work_dateLast = $workOuts[$workoutlast."@@".($klast-1)];
$monLast = date('n',strtotime($work_dateLast));
$yrLast = date('Y',strtotime($work_dateLast));
$dayLast = date('j',strtotime($work_dateLast));
$workoutOrderLast =$orderArray[$klast-1]; 
if($work_dateLast<$todayDate)
	$bLast = 'a';
elseif($work_dateLast==$todayDate)
	$bLast = 'b';
elseif($work_dateLast>$todayDate)
	$bLast = 'c';	
$last = " "."<a href=\"#f\" onclick=\"javascript:htmlData('workout_pagination_cal.php','p=$maxPage','$pgm_flexid','$pgmid');htmlComment('comment_pagination_cal.php','pg=1','$pgmid','$workoutlast');navigate_1('$monLast','$yrLast','$bLast','$dayLast','$workoutlast','$workoutOrderLast');\"><strong>>></strong></a> ";
}

else
{
$next = '<strong> </strong>';
$last = '<strong> </strong>';
}


// work out pagination

$result="<span class=\"produiMidBoxRightHd\"><label class=\"produiMidBoxRightHd2\">".$workout_cnt." ".$parObj->_getLabenames($arrayData,'sessions','name')."</label>";
if($workout_cnt>0)
{
$result.="<label class=\"produiMidBoxRightSeance\">".$parObj->_getLabenames($arrayData,'session','name')." $first$nav$last</label></span><span class=\"produiMidBoxRightHd3\">".$parObj->_getLabenames($arrayData,'session','name')." ".$pageNum;
/*if($pageNum==1)
{

$result.="|".$parObj->_getLabenames($arrayData,'firstsession','name');
}*/
if(trim($subscribeDetails['program_type'])=="program")
{$result.="| ".$w_day." ".utf8_encode($w_month);}
$result.="</span>";
}
if($workout_cnt>0)
{
$result.="<p class=\"produiMidBoxRightText\"><strong>$workout_title</strong><br/><br/>".$data."</p><p class=\"produiMidBoxRightTextB\">".$parObj->_getLabenames($arrayData,'provide_w','name')."</p><div class=\"produiMidBoxRightProvide\">".$provides."</div><span class=\"produiMidBoxRightLink\">$prev$next</span><span class=\"produiMidBoxRightText2\"><input type=\"button\" class=\"ProduiBtn2\" value=\"".$parObj->_getLabenames($arrayData,'generateMP3','name')."\" ";
if(trim($subscribeDetails['program_type'])=="program")
{$workoutStartDate = $workOuts[$workout_flex_id."@@".($pageNum-1)];}
else {$workoutStartDate = trim($subscribeDetails['subscribed_date']);}

$originFource = $objPgm->_getWorkoutOriginSource($workout_flex_id);
$originFourceStatus = trim(stripslashes($originFource['workout_origin_force']));
$originFourceFile = trim(stripslashes($originFource['workout_origin_file']));
$originForceWorkoutId =trim(stripslashes($originFource['workout_id'])); 
if($originFourceStatus==1)
{ 
	if($objPgm->_checkFreePeriod($userid,$freedays,$workoutStartDate))
	{ 
		$result.=" onclick=\"showOriginForceDownload('$originForceWorkoutId');\"/>";
	}
       elseif ($objPgm->_checkProgramTypeSubscribed($userid,$pgmid)=="single")
        { 
         $result.=" onclick=\"showOriginForceDownload('$originForceWorkoutId');\"/>";
        }
       elseif (!$objPgm->_checkUserPaymentPeriod_evenIfPreponed($userid,$workOutDays[$_REQUEST['p']],$subscribeDetails['subscribed_date']))
        { 
         //$result.=" onclick=\"showGenerateType('$workout_flex_id');\"/>";
         $result.=" onclick=\"showGenerateOverlay1('$workout_flex_id');\"/>";
        }
	elseif($objPgm->_checkPaymentPeriod($userid,$workoutStartDate))
	{
		$result.=" onclick=\"showOriginForceDownload('$originForceWorkoutId');\"/>";
	}
	else
	{
 		$result.=" onclick=\"showGenerateOverlay1('$workout_flex_id');\"/>";
	} 
}
else
	{  
/// bypass the tag offline condition ($objPgm->_getUserTagOfflineLogin($userid))
if($tmp = false)
{
   if($objPgm->_checkoxylaneUser($userid))///check oxy lane user
   {
  	 if($objPgm->_validateOxylaneUser($userid,$tmpWorkOutDate))
		{	
				$result .= " onclick=\"showGenerateType('$workout_flex_id');\"/>";
		}
		else
		{
			$result .= " onclick=\"thrityDayValidationGiftCodeUsers();\"/>";
		}
   }
   else
   {
   //$result.=" onclick=\"showGenerateOverlayTag();\"/>";
		if($objPgm->_validateMp3GenarationPeriod($userid,$tmpWorkOutDate))
		{	
				$result .= " onclick=\"showGenerateType('$workout_flex_id');\"/>";
		}
		else
		{
			$result .= " onclick=\"thrityDayValidationGiftCodeUsers();\"/>";
		}
   }
		
}
/*elseif($objPgm->_checkProgramSubscription($userid,"single")){
    
}*/
/*elseif($objPgm->_checkWorkoutExistsInQueue($userid,$pgm_flexid,$workout_flex_id))
{
$result.=" onclick=\"showGenerateOverlay2();\"/>";
}*/
elseif($objPgm->_checkFreePeriod($userid,$freedays,$workoutStartDate))
{
			//$result.=" onclick=\"showGenerateOverlay('$workout_flex_id');\"/>";
	 if($objPgm->_checkoxylaneUser($userid))///check oxy lane user
	   {
		 if($objPgm->_validateOxylaneUser($userid,$tmpWorkOutDate))
			{	
					$result .= " onclick=\"showGenerateType('$workout_flex_id');\"/>";
			}
			else
			{
				$result .= " onclick=\"thrityDayValidationGiftCodeUsers();\"/>";
			}
	   }
	   else
	   {
			if($objPgm->_validateMp3GenarationPeriod($userid,$tmpWorkOutDate))
				{
							$result .= " onclick=\"showGenerateType('$workout_flex_id');\"/>";
					
			}else{
				$result .= " onclick=\"thrityDayValidationGiftCodeUsers();\"/>";
			}
		}
}
elseif (!$objPgm->_checkUserPaymentPeriod_evenIfPreponed($userid,$workOutDays[$_REQUEST['p']],$subscribeDetails['subscribed_date']))
{
 //$result.=" onclick=\"showGenerateType('$workout_flex_id');\"/>";
 $result.=" onclick=\"showGenerateOverlay1('$workout_flex_id');\"/>";
}
elseif($objPgm->_checkPaymentPeriod($userid,$workoutStartDate))
{
			
			//$result.=" onclick=\"showGenerateOverlay('$workout_flex_id');\"/>";
	if($objPgm->_checkoxylaneUser($userid))///check oxy lane user
	   {
		 if($objPgm->_validateOxylaneUser($userid,$tmpWorkOutDate))
			{	
					$result .= " onclick=\"showGenerateType('$workout_flex_id');\"/>";
			}
			else
			{
				$result .= " onclick=\"thrityDayValidationGiftCodeUsers();\"/>";
			}
	   }
	   else
	   {
			if($objPgm->_validateMp3GenarationPeriod($userid,$tmpWorkOutDate))
				{
							$result .= " onclick=\"showGenerateType('$workout_flex_id');\"/>";
				}
					else
					{
					
						$result .= " onclick=\"thrityDayValidationGiftCodeUsers('$workout_flex_id');\"/>";
					}
			
			}
}
else
{

 $result.=" onclick=\"showGenerateOverlay1('$workout_flex_id');\"/>";
 /*if($objPgm->_checkUserPaymentPeriod($userid))
		$result.=" onclick=\"showGenerateType1('$workout_flex_id');\"/>";
	else
 		$result.=" onclick=\"showGenerateOverlay1('$workout_flex_id');\"/>";*/

}
}
	

$result.="</span><p class=\"produiMidBoxRightText2\">".$parObj->_getLabenames($arrayData,'generaltext','name')."</p>";
}
else
{
$result.="<p class=\"produiMidBoxRightText\">No workouts found</p><span class=\"produiMidBoxRightLink\">&nbsp;</span></p>";
}
echo $result;
?>
