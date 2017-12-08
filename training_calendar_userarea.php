<?php
session_start();
include_once('includeconfig.php');
include_once("includes/classes/class.programs.php");
include_once("includes/classes/class.Languages.php");

if($lanId=="")   $lanId = 1;
$errorMsg 	= '';	 
$userId 	= $_SESSION['user']['userId'];	

$objGen    	= new General();
$objPgm    	= new Programs($lanId);
$objLan    	= new Language();

//dynamic timezone changing
if($tz = $objGen->getTomeZonePHP($userId))	
	{
		date_default_timezone_set($tz);
	}

$workoutArray 	= $objPgm->_getTrainingCalWorkoutDates($userId);
$userPgm 		= $objPgm->_getUserTrainingProgram($userId);
$lanName 		= strtolower($objLan->_getLanguagename($lanId));
$flexid 		= trim($userPgm['flex_id']);
$pgmid 			= trim($userPgm['program_id']);
$workoutFlexIds = array();
$workoutDates 	= array();
$rpath = ROOT_FOLDER."calander_images/arw_lft.png";
$lpath = ROOT_FOLDER."calander_images/arw_rit.png";
foreach( $workoutArray as $key => $value)
	{
    	$workOut 			= explode("@@",$key);
		$workoutFlexIds[] 	= trim($workOut[0]); // store workout flex ids
		$workoutDates[]	  	= $value;  // store workout dates	
	}
//$workoutDates =array('2009-02-20','2009-02-26','2009-02-27','2009-02-28'); 
$output = '';
$month 	= $_GET['month'];
$year 	= $_GET['year'];
	
if($month == '' && $year == '') 
	{ 
		$time 	= time();
		$month 	= date('n',$time);
    	$year 	= date('Y',$time);
	}
$todayDate 	= date('Y-m-d');
$date 		= getdate(mktime(0,0,0,$month,1,$year));
$today 		= getdate();
$hours 		= $today['hours'];
$mins 		= $today['minutes'];
$secs 		= $today['seconds'];

if(strlen($hours)<2) $hours	= "0".$hours;
if(strlen($mins)<2) $mins	= "0".$mins;
if(strlen($secs)<2) $secs	= "0".$secs;

$days	=	date("t",mktime(0,0,0,$month,1,$year));
if($lanName=="english")
	{
		$start = $date['wday']+1;
	}
else
	{
		$start = $date['wday'];
		if($start ==0)
			{
				$start =7;
			}
	}
$name 		= $date['mon'];
$name1 		= $date['month'];
if($lanName	=="english")
{
	$name1 		= $date['month'];
}
elseif($lanName	==	'polish')
{
	$name1 = $monthArray_PL[$date['month']];
}
else 
{
	$name1 = utf8_encode($monthArray[$date['month']]);
}
$year2 	= $date['year'];
$offset = $days + $start - 1;
 
if($month==12) 
	{ 
		$next	= 1; 
		$nexty	= $year + 1; 
	}
else 
	{ 
		$next	= $month + 1; 
		$nexty	= $year; 
	}

if($month==1) 
	{ 
		$prev	=	12; 
		$prevy	=	$year - 1; 
	}
else 
	{ 
		$prev	=	$month - 1; 
		$prevy	=	$year; 
	}

if($offset <= 28)
{
	 $weeks=28; 
}
elseif($offset > 35)
{
	 $weeks = 42; 
}
else $weeks = 35; 

$output .= "
<table class='cal' cellpadding='0' cellspacing='0'>
<tr class='clnd-hed'>
	<td colspan='7'>
		<table class='calhead' cellpadding='0' cellspacing='0'>
		<tbody><tr>
			<td style='padding:2px 0 0 10px;'>
				<a href='javascript:navigate($prev,$prevy)'><img src='$rpath'></a></td>
			<td align='center'>
				<div class='calheadMonth'>
				  $name1 $year2
				</div>
			</td>
		    <td style='padding:2px 10px 0 0;'><a href='javascript:navigate($next,$nexty)'><img src='$lpath' /></a></td>
		</tr>
		</tbody></table>
	</td>
</tr>";
if($lanName=="english"){
	$session="SÉANCE";
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
else if($lanName=="polish"){
	$session="treningi";
$output .= "<tr class='dayhead'>
	<td>P</td>
	<td>W</td>
	<td>&#346;</td>
	<td>C</td>
	<td>P</td>
	<td>S</td>
	<td>N</td>
</tr>";
}
else{$session="SÉANCE";
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
$var=0;//for displaying 'seans' text in the cells
for($i=1;$i<=$weeks;$i++) { 

	if($next==3) $next=0;
	if($col==1) $output.="<tr class='dayrow'>"; 
	$output.="<td valign='top' onMouseOver=\"this.className='dayover'\" onMouseOut=\"this.className='dayout'\">";

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
		
		if(in_array($curdate,$workoutDates))
		{
		  
		  $key = array_search($curdate,$workoutDates);
		  //$workoutFlexId = base64_encode($workoutFlexIds[$key]);
		  $workoutFlexId 	= $workoutFlexIds[$key];
		  $orderArray 		= $objPgm->_getWorkoutOrders($flexid);
		  $workoutOrder 	= $orderArray[$key]; 
		   //$workoutOrder = $objPgm->_getWorkoutOrder($flexid,$workoutFlexId,$lanId);
		  
		    $c1 = 'a'.$cur;
			$c2 = 'b'.$cur;
			$c3 = 'c'.$cur;
		  if($curdate<$todayDate)
		    //$output.="<div class='day_Tick' onclick=\"this.className='day_Orange1'\" id='$c1' ><b>";
			$output.="<div class='day_Tick' onclick=\"selectWorkout('a','$cur','$workoutFlexId','$workoutOrder');\" id='$c1' ><b>";
		  elseif($curdate==$todayDate){
		  	//$output.="<div class='day_Today' onclick=\"this.className='day_Orange1'\" id='$c2'><b>";
			$output.="<div class='day_Today' onclick=\"selectWorkout('b','$cur','$workoutFlexId','$workoutOrder');\" id='$c2' ><b>";
		    $var=1;
		  }elseif($curdate>$todayDate){	
		   	//$output.="<div class='day_Orange2' onclick=\"this.className='day_Orange1'\" id='$c3'><b>";
			$output.="<div class='day_Orange2' onclick=\"selectWorkout('c','$cur','$workoutFlexId','$workoutOrder');\" id='$c3' ><b>";
		  $var=1;}else
			$output.="<div class='day'><b>";
         }
		 elseif($curdate==$todayDate)
		 	$output.="<div class='today_day'><b>";
		 else
		    $output.="<div class='day'><b>";
		    $output.="$cur";
		if($var==1){
			$output.="<br><span class='cal_font'>$session&nbsp".$workoutOrder."</span>";
            unset($var);
		}
		$output.="</b></div></td>";
		$cur++; 
		$col++; 
		
	} else { 
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
