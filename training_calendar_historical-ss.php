<? 
session_start();

include_once('includeconfig.php');

include_once("includes/classes/class.programs.php");

include_once("includes/classes/class.Languages.php");

if($_SESSION['language']['langId']=="") { $lanId=1;  } else { $lanId = $_SESSION['language']['langId']; }

$errorMsg = '';	 

$userId = $_SESSION['user']['userId'];	

$objGen     	= new General();
//dynamic timezone changing

if($tz	=	$objGen->getTomeZonePHP($userId))	date_default_timezone_set($tz);



$objPgm     	= new Programs($lanId);

$objLan    		= new Language();

$parObj 		= new Contents('training_calendar_historical.php');

$headingData	= $parObj->_getTagcontents($xmlPath,'trainingprogram','pageHeading');

$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');

$arrayData		= $returnData['general'];

$workoutArray = $objPgm->_getTrainingCalWorkoutDatesPgm($userId);
//echo "<pre/>";print_r($workoutArray);exit;


$lanName =  strtolower($objLan->_getLanguagename($lanId));

$workoutFlexIds = array();

$workoutDates = array();

$pgmIds = array();
$workoutOrders	=array();
for($i=0;$i<count($workoutArray);$i++)

{

 $values = explode('|',$workoutArray[$i]);

 $pgmIds[] = $values[0];

 $workoutFlexIds[] = $values[1];

 $workoutDates[] = $values[2];
 $workoutOrders[]	= $values[3];

}
$output = '';

$month = $_GET['month'];

$year = $_GET['year'];
	
if($month == '' && $year == '') { 

	$time = time();

	$month = date('n',$time);

    $year = date('Y',$time);

}

$todayDate =date('Y-m-d');

$date = getdate(mktime(0,0,0,$month,1,$year));

$today = getdate();

$hours = $today['hours'];

$mins = $today['minutes'];

$secs = $today['seconds'];



if(strlen($hours)<2) $hours="0".$hours;

if(strlen($mins)<2) $mins="0".$mins;

if(strlen($secs)<2) $secs="0".$secs;


$days=date("t",mktime(0,0,0,$month,1,$year));

if($lanName=="english"){$start = $date['wday']+1;}else{

$start = $date['wday'];

if($start ==0){$start =7;}

}

$name = $date['mon'];

if($lanName=="english"){

$name1 = $date['month'];

}
elseif($lanName	==	'polish')
{
	$name1 = $monthArray_PL[$date['month']];
}

else {

$name1 = utf8_encode($monthArray[$date['month']]);



}

$year2 = $date['year'];

$offset = $days + $start - 1;

 

if($month==12) { 

	$next=1; 

	$nexty=$year + 1; 

} else { 

	$next=$month + 1; 

	$nexty=$year; 

}



if($month==1) { 

	$prev=12; 

	$prevy=$year - 1; 

} else { 

	$prev=$month - 1; 

	$prevy=$year; 

}

if($offset <= 28) $weeks=28; 

elseif($offset > 35) $weeks = 42; 

else $weeks = 35; 

$arw_lft=ROOT_FOLDER.'calander_images/arw_lft.png';
$arw_rit=ROOT_FOLDER.'calander_images/arw_rit.png';
$output .= "

<table class='cal' cellpadding='0' cellspacing='0'>

<tr class='clnd-hed'>

	<td colspan='7'>

		<table class='calhead' cellpadding='0' cellspacing='0'>

		<tbody><tr>

			<td style='padding:2px 0 0 10px;'>

				<a href='javascript:navigate($prev,$prevy)'><img src='$arw_lft'></a></td>

			<td align='center'>

				<div class='calheadMonth'>

				  $name1 $year2

				</div>

			</td>

		    <td style='padding:2px 10px 0 0;'><a href='javascript:navigate($next,$nexty)'><img src='$arw_rit' /></a></td>

		</tr>

		</tbody></table>

	</td>

</tr>";

if($lanName=="english"){

$output .= "<tr class='dayhead'>

	<td>S</td>

	<td>M</td>

	<td>T</td>

	<td>W</td>

	<td>T</td>

	<td>F</td>

	<td>S</td>

</tr>";

}

else{

$output .= "<tr class='dayhead'>

	<td>L</td>

	<td>M</td>

	<td>M</td>

	<td>J</td>

	<td>V</td>

	<td>S</td>

	<td>D</td>

</tr>";



}



$col=1;

$cur=1;

$next=0;

$workOrderPrev	=	"";
	
for($i=1;$i<=$weeks;$i++) { 



	if($next==3) $next=0;

	if($col==1) $output.="<tr class='dayrow'>"; 

  	

	$output.="<td valign='middle' onMouseOver=\"this.className='dayover'\" onMouseOut=\"this.className='dayout'\">";

    

	if($i <= ($days+($start-1)) && $i >= $start) {

	

	    $curLen = strlen($cur);

		if($curLen < 2)

		   $new_cur = '0'.$cur;

		 else

		   $new_cur = $cur;

		$monthLen = strlen($name);

		if($monthLen < 2)

		   $new_name = '0'.$name; 

		else

		   $new_name = $name;    

	    $curdate = $year2."-".$new_name."-".$new_cur;

		

		$dat = $parObj->_getLabenames($arrayData,'session','name')."   ".date("j F Y",strtotime($curdate));	

		
	
		if(in_array($curdate,$workoutDates))

		{
			

		  	$key = array_search($curdate,$workoutDates);

		  	$workoutFlexId = $workoutFlexIds[$key];
			$workoutOrder	=$workoutOrders[$key];
			

			$pgm_id = $pgmIds[$key];

			$pgm_flexid = $objPgm->_getProgramFlexId($pgm_id);

		  	$orderArray = $objPgm->_getWorkoutOrders($pgm_flexid);
			//$workoutOrder =$orderArray[$key]; 
			//$keys = array_search($workoutOrder,$orderArray);
		  
		//print_r($keys);
			//print_r($orderArray );
			//print_r($workoutFlexIds);
			//print_r($workoutDates );
			//print_r($workoutOrder);
		  	//$workoutOrder = $objPgm->_getWorkoutOrder($pgm_flexid,$workoutFlexId,$lanId,$workOrderPrev);
			
			
			
			$workOrderPrev	=	$workoutOrder;

			$workoutflexid_cal = $workoutFlexId."@".$workoutOrder;	  

		    $c1 = 'a'.$cur;

			$c2 = 'b'.$cur;

			$c3 = 'c'.$cur;

			if($curdate<$todayDate)

		    {

			$output.="<div class='day_Tick' onclick=\"selectWorkout('a','$cur','$workoutFlexId','$dat');showProgram('$workoutFlexId','$pgm_id','$workoutOrder','$curdate','$workoutflexid_cal');\" id='$c1' ><b>";

			}

		  elseif($curdate==$todayDate)

		  {

		  	$output.="<div class='day_Today' onclick=\"selectWorkout('b','$cur','$workoutFlexId','$dat');showProgram('$workoutFlexId','$pgm_id','$workoutOrder','$curdate','$workoutflexid_cal');\" id='$c2' ><b>";

			}

		  elseif($curdate>$todayDate)

		  {	

		   	$output.="<div class='day_Orange2' onclick=\"selectWorkout('c','$cur','$workoutFlexId','$dat');showProgram('$workoutFlexId','$pgm_id','$workoutOrder','$curdate','$workoutflexid_cal');\" id='$c3' ><b>";

			}

		  

         }

		elseif($curdate==$todayDate)

		 	$output.="<div class='today_day'><b>";

		else

		  { $output.="<div class='day'><b>";}

				

		$output.="$cur";

		

        $output.="</b></div></td>";

		

		$cur++; 

		$col++; 

		

	 }else { 

		$output.="&nbsp;</td>"; 

		$col++; 

	} 

	    

    if($col==8) { 

	    $output.="</tr>"; 

	    $col=1; 

    }



} 

$output.="</table>";

 

echo $output;



?>

