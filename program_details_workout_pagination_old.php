<section class="seance_1" id="seansWrapper">
         <div class="left height_equal"><img src="images/corner.png" alt="image" class="corner">
           <div class="title"><?php echo  mb_strtoupper($parObj->_getLabenames($arrayData,'session','name','UTF-8'))." ".$pageNum?> </div>
           <figure><img src="<?=ROOT_FOLDER?>images/img-jiwok_02.jpg" alt="image"></figure>
         </div>
        <article class="content height_equal">
            <span class="line"></span>
            <h3>Séance d'endurance fondamentale en aisance respiratoire.</h3>
            <p>Lors de cette séance, vous effectuerez :</p>
            <p>30 minutes courues autour de 70% de FCM en prenant du plaisir + 2 
minutes de marche de récupération.
Puis, 6 fois : 1 ligne droite de 15 secondes en courant vite (autour de 90% 
FCM) + 45 secondes de récupération en marchant.
Enfin, vous finirez par 2 minutes de marche.
Cette séance durera 40 minutes.</p>
<p>Lors de la réalisation de votre séance vous pourrez prendre une marge de 
+/- 3% par rapport au pourcentage de FCM indiqué.</p>

<h3>Les conseils du coach pour cette séance
</h3> <p> Séance facile avec quelques accélérations en fin de séance.
Ce ne sont pas des sprints, mais des accélérations progressives où vous 
chercherez à allonger votre foulée et maintenir un rythme assez soutenu
</p>
<div>
    <a href="#" class="btn lft">QU'EST-CE QUE LE FCM?</a>
    <a href="#" class="btn rit">SÉANCE SUIVANTE</a>
</div>
        </article>
      </section>
<div class="seane_paging">
        <div class="title"><?php echo mb_strtoupper($parObj->_getLabenames($arrayData,'session','name','UTF-8'))?></div>
        <ul>
          <li><a href="#">1</a></li>
          <li class="active"><a href="#">2</a></li>
          <li><a href="#">3</a></li>
          <li><a href="#">4</a></li>
          <li><a href="#">5</a></li>
          <li><a href="#">6</a></li>
          <li><a href="#">7</a></li>
          <li><a href="#">8</a></li>
          <li><a href="#">9</a></li>
          <li><a href="#">11</a></li>
          <li><a href="#">12</a></li>
          <li><a href="#">13</a></li>
          <li><a href="#">14</a></li>
          <li><a href="#">15</a></li>
          <li><a href="#">16</a></li>
          <li><a href="#">17</a></li>
          <li><a href="#">18</a></li>
          <li><a href="#">19</a></li>
          <li><a href="#">20</a></li>
          <li><a href="#">21</a></li>
          <li><a href="#">22</a></li>
          <li><a href="#">23</a></li>
          <li><a href="#">24</a></li>
        </ul>
     
     </div>

<?php
session_start();
ob_start();
include_once('includeconfig.php');
include_once("includes/classes/class.programs.php");

if($lanId=="")  $lanId=1;
$errorMsg = '';	 
$userid 		= $_SESSION['user']['userId'];	 
$objGen     	= new General();
$objPgm     	= new Programs($lanId);
$parObj 		= new Contents();
$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
$arrayData		= $returnData['general'];

// Number of records to show per page
$recordsPerPage = 1;
# 0
// //default startup page

if(isset($_GET['p']))
	{
		$pageNum = $_GET['p'];
		settype($pageNum, 'integer');
	}
else 
 		$pageNum = 1;
 
$pgm_flexid = trim($_REQUEST['pgm_flexid']);
$pgmid 		= trim($_REQUEST['pgmid']);

$workout_cnt 		= $objPgm->_getWorkoutCount($objGen->_output(trim($pgm_flexid)),$lanId);
$workout_flex_ids 	= array();
$j			=	1;
$offset 	= ($pageNum - 1) * $recordsPerPage;
$query1 	= "SELECT workout_flex_id FROM program_workout WHERE training_flex_id='".addslashes(trim($pgm_flexid))."' and lang_id={$lanId} ORDER BY workout_order ASC";
/*if($pgmid==348)
{
	echo $query1;
	
}*/
$result1 	= mysql_query($query1);

while($row1 = mysql_fetch_assoc($result1))
	{
		$workout_flex_ids[$j] = $objGen->_output(trim($row1['workout_flex_id']));
		$j++;	
	}
$query = "SELECT workout_flex_id FROM program_workout WHERE training_flex_id='".addslashes(trim($pgm_flexid))."' and lang_id={$lanId} ORDER BY workout_order ASC LIMIT $offset, $recordsPerPage";

# 1. Main query
$result = mysql_query($query) or die('Mysql Err. 1');

// print table
$data 		= '';
$provides 	= '';



# 2 change/add columns name
while($row = mysql_fetch_assoc($result))
	{
		$workout_flex_id 	= $objGen->_output(trim($row['workout_flex_id']));	
		$workoutDetail 		= $objPgm->_getWorkoutDetailAll(trim(str_replace(' ','',$workout_flex_id)),$lanId);
		
		//For removing br tag form workout details starts
		$workoutDetail['workout_desc']		=	strip_tags($workoutDetail['workout_desc']);
		$workoutDetail['workout_title']		=	strip_tags($workoutDetail['workout_title']);
		$workoutDetail['workout_provide']	=	strip_tags($workoutDetail['workout_provide']);
		//For removing br tag form workout details ends		
		$workout_detail 	= trim(stripslashes($workoutDetail['workout_desc']));
		$workout_title 		= trim(stripslashes($workoutDetail['workout_title']));
		$workout_provide 	= trim(stripslashes($workoutDetail['workout_provide']));
		$data 		.= "$workout_detail";
		$provides 	.= $workout_provide;
		
		
	
	}
if(!isset($_SESSION['user']['userId'])) 
	{
		$data		= "<a href='userreg1.php' style='color:#5E6A6E;'>".$parObj->_getLabenames($arrayData,'anon_workout_details','name')."</a>";
		$provides	= "<a href='userreg1.php' style='color:#5E6A6E;'>".$parObj->_getLabenames($arrayData,'anon_workout_details','name')."</a>";
	}

# Update this query with same where clause you are using above.
$query 		= "	SELECT COUNT(workout_flex_id) AS dt FROM program_workout 
				WHERE training_flex_id='".addslashes(trim($pgm_flexid))."' and lang_id={$lanId} 
				ORDER BY workout_order ASC";
$result 	= mysql_query($query) or die('Mysql Err. 2');
$row 		= mysql_fetch_assoc($result);
$numrows 	= $row['dt'];
# 4
$maxPage = ceil($numrows/$recordsPerPage);

$i 		= 1;
$count 	= 0;
$nav 	= '';

//Display '$opt_links_count' number of links
while($count++ <= $maxPage)
{
	if($i == $pageNum)
	{	  
		  $nav .= " <li class=\"blu_bg\"><a class=\"select\">".$i."</a></li> ";
		  if($i<$maxPage)
		  $nav.= $link_separator;
	}
	else
	{
	   	$workid = $workout_flex_ids[$i];
		// Display format - <li class="blu_bg"><a href="#" class="select">1</a></li>
	 	$nav .= "<li class=\"blu_bg\">
					<a href=\"javascript:;\"  onclick=\"javascript:htmlData('program_details_workout_pagination.php','p=$i','".urlencode($pgm_flexid)."','$pgmid');
					htmlComment('comment_pagination.php','pg=1','$pgmid','$workid');\">$i</a>
				</li>";
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
		$workoutfirst = $workout_flex_ids[1];
		$prev = "<a href=\"javascript:;\" class=\"blu2\" onclick=\"javascript:htmlData('program_details_workout_pagination.php','p=$page','".urlencode($pgm_flexid)."','$pgmid');htmlComment('comment_pagination.php','pg=1','$pgmid','$workoutprev');\">&lt; ".$parObj->_getLabenames($arrayData,'prevsession','name')."</a> ";
		
		$first = "<li> <a href=\"javascript:;\"  onclick=\"javascript:htmlData('program_details_workout_pagination.php','p=1','".urlencode($pgm_flexid)."','$pgmid'); htmlComment('comment_pagination.php','pg=1','$pgmid','$workoutfirst');\">&lt;</a></li>";
	}
else
	{
		$prev = '';
		$first = '';
	}
if ($pageNum < $maxPage)
	{
		$page = $pageNum + 1;
		$workoutnext = $workout_flex_ids[$page];
		$workoutlast = $workout_flex_ids[$j-1];
		$next = "<a href=\"javascript:;\"  class=\"blu2\" onclick=\"javascript:htmlData('program_details_workout_pagination.php','p=$page','".urlencode($pgm_flexid)."','$pgmid');htmlComment('comment_pagination.php','pg=1','$pgmid','$workoutnext');\">".$parObj->_getLabenames($arrayData,'nextsession','name')." &gt;</a>";
		
		$last = " <li><a href=\"javascript:;\" onclick=\"javascript:htmlData('program_details_workout_pagination.php','p=$maxPage','".urlencode($pgm_flexid)."','$pgmid');htmlComment('comment_pagination.php','pg=1','$pgmid','$workoutlast');\">&gt;</a></li>";
	}
else
	{
		$next = '';
		$last = '';
	}


if($workout_cnt > 0)
	{
		/* Displaying No of sessions as heading*/
		$result	 = 	"<h2>".$workout_cnt." ".$parObj->_getLabenames($arrayData,'sessions','name')."</h2>";
		$result	.=	"<ul id=\"paging\">$first$nav$last</ul>"; /* Displaying pagination*/
		$result	.=	"<hr class=\"blu-line\" />";
		$result	.= 	"<div><span><strong>".$parObj->_getLabenames($arrayData,'session','name')." ".$pageNum."</strong></span></div>";
		
		
	//if($_SESSION['user']['userId'])
	//{
		$result	.="<br />";		
	//}
			
			$result	.= 	"<strong>";
			$result .= '<div style="display: none;"><div id="inline4" class="popupstyle" >';
			$result .= nl2br($parObj->_getLabenames($arrayData,'questionMHRText','name'));
			$result .= "<a href='".ROOT_JWPATH."ticket.php' >".$parObj->_getLabenames($arrayData,'questionMHRTextLink','name')."</a>";
			$result .= '</div></div>';

	 

		 
		/*if($pageNum == '1')
			{
				$result.=$parObj->_getLabenames($arrayData,'firstsession','name');
			}*/
		$result.=	"</strong>";
		
		//to change format of polish workout pagination
		if($lanId == 5)
		{
			$titleStart	="<DIV STYLE=\"text-align:LEFT;\"><FONT STYLE=\"letter-spacing:0px;color:#004665 !important;font-size:12px !important;font-family:Arial !important\">";
			$titleEnd	="</FONT></DIV>";
			$descStart	="<DIV class=\"desc\" >";
			
			$descEnd	="</DIV>";
			$adviseTitle	="<DIV STYLE=\"text-align:LEFT;\"><FONT STYLE=\"letter-spacing:0px;color:#004665 !important;font-size:12px !important;font-family:Arial !important\">";
		}
		else
		{
			$titleStart	="";
			$titleEnd	="";
			$descStart	="";
			$descEnd	="";
			$adviseTitle	="";
		}
		?><style>
		.desc span,.desc font,.desc p,.desc div,.desc p div
		{
			letter-spacing:0px;
			color:#000!important;
			font-size:10px!important;
			font-family:Verdana!important;
			
		}
		.desc p span
		{
			letter-spacing:0px;
			color:#000!important;
			font-size:10px!important;
			font-family:Verdana!important;
			
		}
        </style> <?php 
		$result.=	'<span>'.$titleStart.'<strong>'.$workout_title.'</strong>'.$titleEnd.'</span>';
		$result.=	'<hr class="blu-line" />';
		$result.=	$descStart.'<strong>'.$data.'</strong>'.$descEnd;
		$result.=	'<p></p>';
		$result.=	'<span>'.$adviseTitle.'<strong>'.$parObj->_getLabenames($arrayData,'provide_w','name').'</strong>'.$titleEnd.'</span><hr class="blu-line" />';
		$result.=	'<div>'.$descStart.'<strong>'.$provides.'</strong>'.$descEnd.'</div>';
		$result.=	'<p style="padding-top:20px;" align="right">'.$prev.' I '.$next.'</p>';
	}
else
	{
		$result.='<p><strong>No workouts found</strong></p>';
	}

echo $result;
?>