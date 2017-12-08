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

//$work_desc = trim(stripslashes(strip_tags($workDetails['workout_desc'])));

//$work_advice = trim(stripslashes(strip_tags($workDetails['workout_provide'])));

$work_desc = $workDetails['workout_desc'];
$work_desc = goodtext ($work_desc);
$work_advice = goodtext($workDetails['workout_provide']);

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
//-------------for dynamic images

	    $image1	=	$objGen->processProgramImage($data['flex_id'],$data['program_title'],$data['new_program_image']);
        $image1 		    	= $objGen->_output(trim($image1));
	    if($image1 != '')
		{
			$imgPath1 = "uploads/programsNew/";
			$image_new1	=	$imgPath1.$image1;
			if(file_exists($image_new1) && (!empty($image1)))
			{
			     $HistoricalImage	=	$imgPath1.$image1;
			}
			else
			{
				$HistoricalImage	= "images/dummy-3.jpg";
			} 
		}
		else
		{
			$HistoricalImage	= "images/dummy-3.jpg";
		}
						 
//-------------
$im1=ROOT_FOLDER.'images/corner-blu.png';
$im2=ROOT_FOLDER.'images/swim.jpg';
$im3=ROOT_FOLDER.'images/swim.jpg';
$display.='<div class="title">'.$objGen->_output(trim($data['program_title'])).'
       </div>';

// VENU $display.="<h3>".$objGen->_output(trim($data['program_title']))."</h3><span class=\"ProdStep1image\">";
$display .=' <div class="content"><div class="row">';

if($image1!= "") { 
//~ need to uncommand it $display.='<figure><img src="images/corner-blu.png" class="corner" ><img src="'.$imgPath.$objGen->_output(trim($data['program_image'])).'" alt="FaceBook"  ></figure>';
$display.='<figure><img src="'.$im1.'" class="corner"><img src="'.ROOT_FOLDER.$HistoricalImage.'" alt="'.$objGen->_output(trim($data['program_title'])).'"></figure>';
                      
 } else {
	$display.='<figure><img class="corner" src="'.$im1.'"><img alt="ProgramImage" src="'.ROOT_FOLDER.'images/dummy-3.jpg"></figure>';
	 }

 
         $display.='<article><h3>'.$parObj->_getLabenames($arrayData,"workout","name").' '.$workoutOrder.'  I '.$cday.' '.$cmonth.'</h3>'; 
         if($work_desc)
         {
 		$display .= '<div  style="text-align:justify;" class="text">'.$work_desc.'</div>';
	     }
 		$display .= '</article>
        </div>
        <div class="clear"></div>';
        $display .='<span class="sub_title" ><h3>'.$parObj->_getLabenames($arrayData,"newCoachAdvTxt","name").'</h3></span>';
        $display .='<p style="text-align:justify;">'.$work_advice.'</p></div>';

// VENU $display.="</span><ul><li><ul><li><h1>".$parObj->_getLabenames($arrayData,'workout','name')." ".$workoutOrder."<font color='#000000'> | </font> ".$cday." ".$cmonth." </h1></li><li><strong>$work_title </strong></li><li><h2>".$work_desc."</h2></li></ul></li>";




echo $display;

function goodtext($text)
{
	$text = str_ireplace("text-align:LEFT","text-align:justify",$text);
	$text = str_ireplace("text-align:RIGHT","text-align:justify",$text);
	$text = str_ireplace("letter-spacing:0px","",$text);
	$text = str_ireplace("font-family:'Verdana'","",$text);
	$text = str_ireplace("font-size:10px","",$text);
	$text = str_ireplace("color:#0B333C","",$text);
	
	return $text;
}

?>

