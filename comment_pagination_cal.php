<?php
session_start();
ob_start();
include_once('includeconfig.php');
include_once("includes/classes/class.programs.php");

if($lanId == "")    $lanId=1;

$errorMsg 		= '';	 
$userid 		= $_SESSION['user']['userId'];	 

$objGen     	= new General();
$objPgm     	= new Programs($lanId);
$parObj 		= new Contents();
$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
$arrayData		= $returnData['general'];

$link_separator = "";
$userPhotoPath 	= "uploads/users/";
$imgPath 		= "uploads/programs/";

// Number of records to show per page
$recordsPerPage = 3;
// //default startup page
$opt_links_count = 5; 	
$max = $opt_links_count;

if(isset($_GET['pg']))
	{
		$pageNum = $_GET['pg'];
		settype($pageNum, 'integer');
	}
else 
 	$pageNum = 1;
 
$pgmid 			= trim($_REQUEST['pgmid']);
$workoutflex 	= trim($_REQUEST['workoutflex']);
$workoutFlex 	= str_replace(' ','+',$workoutFlex);
$offset 		= ($pageNum - 1) * $recordsPerPage;

/*$query = "SELECT t1.feedback_desc as description,t2.user_fname as firstname,t2.user_photo as user_photo FROM feedback as t1,user_master as t2 WHERE t1.program_id = {$pgmid} AND t1.user_id  = t2.user_id  AND t1.public_status ='0' AND t2.user_status  = 1 AND t1.lang_id =".$lanId." ORDER BY t1.feedback_datetime DESC LIMIT $offset, $recordsPerPage";*/

/*$query	=	"SELECT SQL_SMALL_RESULT `t1`.`feedback_desc` as `description`,`t2`.`user_fname` as `firstname`,
			`t2`.`user_photo` as `user_photo` FROM 
			`feedback` as `t1` JOIN `user_master` as `t2` USING(`user_id`) 
			WHERE
		 `t1`.`program_id` = {$pgmid} AND `t1`.`public_status` ='0' AND `t2`.`user_status`  = '1' AND `t1`.`lang_id`= '".$lanId."' 
			ORDER BY `t1`.`feedback_datetime` LIMIT $offset, $recordsPerPage";
*/

$query	=	"SELECT SQL_SMALL_RESULT `t1`.`feedback_desc` as `description`,`t2`.`user_fname` as `firstname`,
			`t2`.`user_photo` as `user_photo` FROM 
			`feedback` as `t1` JOIN `user_master` as `t2` USING(`user_id`) 
			WHERE
		 `t1`.`program_id` = {$pgmid} AND `t1`.`public_status` ='0' AND `t2`.`user_status`  = '1' AND `t1`.`lang_id`= '".$lanId."' 
			ORDER BY `t1`.`feedback_datetime` desc LIMIT $offset, $recordsPerPage" ;


# 1. Main query
$result = mysql_query($query) or die('Mysql Err. 1');
$datatext = '';
		//------------------------------
$datatext 		.= '<section class="jw_comments mid-wrapper">';
 $datatext 		.= '<h2>'.$parObj->_getLabenames($arrayData,'jiwokcomments','name').'</h2>';
 $datatext 		.= '<div class="clear"></div>';

//------------------------------

# 2 change/add columns name
$totResult	=	mysql_num_rows($result);
$totCount	=	1;
$style		=	'';	
while($row = mysql_fetch_assoc($result))
{
	if($totResult	==	$totCount)
			
	$userImage = $objGen->_output(trim($row['user_photo']));	
	if($userImage != "")
	{
			$user_img = $userPhotoPath.$objGen->_output(trim($userImage));
	}
	if((file_exists($user_img)) && (trim($userImage)!= ""))
	{			
			$userimg = $userPhotoPath.$objGen->_output(trim($userImage));
	}
	else
		{
			$userimg = 'images/profile-dummy.png';	
		}
	//========================================
			 $datatext 		.= '<article class="colums">';
          	 $datatext 		.= '<figure> <img src="'.ROOT_FOLDER.$userimg.'" alt="user"> </figure>';
			 $datatext 		.= '<h2>'.$objGen->_output(trim($row['firstname'])).'</h2>';
			 $datatext 		.= '<p>'.$objGen->_output(trim($row['description'])).'</p>';
			  $datatext 	.= '</article>';
			  
			//=========================================
	$totCount++;	
}
$datatext 		.=   '<div class="clear"></div>';

# Update this query with same where clause you are using above.
/*$query = "SELECT COUNT(t1.program_id) as dt FROM feedback as t1,user_master as t2 WHERE t1.program_id = {$pgmid} AND t1.user_id  = t2.user_id  AND t2.user_status  = 1 AND t1.public_status ='0' ORDER BY t1.feedback_datetime DESC";*/

$query =   "SELECT COUNT(`fdbk`.`program_id`) as `dt` FROM 
			`feedback` as `fdbk` JOIN `user_master` as `um` USING(`user_id`)
			WHERE `fdbk`.`program_id` = '$pgmid' AND `um`.`user_status`  = '1'
			AND `fdbk`.`public_status` ='0'  AND `fdbk`.`lang_id` ='".$lanId."' 
			ORDER BY `fdbk`.`feedback_id` DESC";

$result 	= mysql_query($query);
$row 		= mysql_fetch_assoc($result);
$numrows 	= $row['dt'];
$maxPage 	= ceil($numrows/$recordsPerPage);# 4

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
	
$i 		= $start_from;
$count 	= 0;
$nav 	= '';

//Display '$opt_links_count' number of links
while($count++ < $max)
	{
		if($i == $pageNum)
			{
		  		if($numrows>3)
					$nav .= "<li class=\"active\">".$i."</li>";
			}
		else
			{		
		 		$nav .= "<li><a href=\"#q\" onclick=\"javascript:htmlComment('comment_pagination_cal.php','pg=$i','$pgmid','$workoutflex')\">".$i."</a></li>";
			}
		$i++;
		if($i > $maxPage) break; //If the current page exceeds the total number of pages, get out.
	}

if ($pageNum > 1)
	{	
		$page = $pageNum - 1;
		/*$prev = "<li class='horizontal'><a href=\"#q\" onclick=\"javascript:htmlComment('comment_pagination_cal.php','pg=$page','$pgmid','$workoutflex')\">&lt;</a></li>";		*/
		$prev = "<li class='prev'><a href=\"#q\" onclick=\"javascript:htmlComment('comment_pagination_cal.php','pg=$page','$pgmid','$workoutflex')\"><img src='".ROOT_FOLDER."images/paging-prev.png' alt=\"previous\"></a></li>";		

/*		$first = "<li class='horizontal'><a href=\"#q\" onclick=\"javascript:htmlComment('comment_pagination_cal.php','pg=1','$pgmid','$workoutflex')\">&lt;</a></li>";
*/	}

else
	{
		$prev = '';
		$first = '';
	}

if ($pageNum < $maxPage)
	{
		$page = $pageNum + 1;
/*		$next = "<li class='horizontal'><a href=\"#q\" onclick=\"javascript:htmlComment('comment_pagination_cal.php','pg=$page','$pgmid','$workoutflex')\">&gt;</a></li>";
*/		
		$next = "<li class='next'><a href=\"#q\" onclick=\"javascript:htmlComment('comment_pagination_cal.php','pg=$page','$pgmid','$workoutflex')\"><img src='".ROOT_FOLDER."images/paging-next.png' alt=\"next\"></a></li>";

/*$last = "<li class='horizontal'><a href=\"#q\" onclick=\"javascript:htmlComment('comment_pagination_cal.php','pg=$maxPage','$pgmid','$workoutflex')\">&gt;</a></li>";
*/	}

else
	{
		$next = '';
		$last = '';
	}
		$datatext 		.= 	'<div class="bottom">'; 
if($numrows>0)
	{
	  	if($numrows>3)
		$datatext 		.= '<nav class="paging"><ul id=\"pagination\">'.$prev.$nav.$next.'</ul></nav>';
		
	}
		
$page_name_called 		= end(explode("/",$_SERVER['HTTP_REFERER']));
$page_name_called 		= explode("?",$page_name_called);
if($page_name_called[0] == 'program_generate2.php')
{
$datatext .= "<a  class=\"btn_orng2\" onclick=\"javascript:setWorkoutFlexId('$workoutflex');\">".$parObj->_getLabenames($arrayData,'addcomment','name')."</a>";
}
$datatext 		.=  '</div>';

		$datatext 		.=  '</section>';
echo $datatext ;

?>