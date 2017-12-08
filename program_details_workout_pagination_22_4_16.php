<?php
session_start();
ob_start();
include_once('includeconfig.php');
include_once("includes/classes/class.programs.php");
?>
<style>
.new_sub_but1 a
{
	width:340px; 
   background: none repeat scroll 0 0 #F1C40F;
    border: 2px solid #FFFFFF;
    border-radius: 3px;
    display: block;
    padding: 15px 16px;
    float: right;
    margin-bottom: 20px; font-size: 100%;
    color: #fff;
        font-size: 16px;
}
   
    .new_sub_but1 a:hover {
        background:rgba(241, 196, 15, 0.43);
        color: #408dc1;
    }    
</style>
<?php
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
		
		/*//For removing br tag form workout details starts
		$workoutDetail['workout_desc']		=	strip_tags($workoutDetail['workout_desc']);
		$workoutDetail['workout_title']		=	strip_tags($workoutDetail['workout_title']);
		$workoutDetail['workout_provide']	=	strip_tags($workoutDetail['workout_provide']);*/
		
		//============
		//For removing br tag form workout details starts
		$workoutDetail['workout_desc']	=	str_replace("<BR STYLE=\"letter-spacing:0px;color:#0B333C;font-size:10px;font-family:'Verdana';\"/>"," ",$workoutDetail['workout_desc']);
		$workoutDetail['workout_title']	=	str_replace("<BR STYLE=\"letter-spacing:0px;color:#0B333C;font-size:10px;font-family:'Verdana';\"/>"," ",$workoutDetail['workout_title']);
		$workoutDetail['workout_provide']	=	str_replace("<BR STYLE=\"letter-spacing:0px;color:#0B333C;font-size:10px;font-family:'Verdana';\"/>"," ",$workoutDetail['workout_provide']);
		
		
		//For removing br tag form workout details starts
		$workoutDetail['workout_desc']	=	str_replace("letter-spacing:0px;color:#0B333C;font-size:10px;font-family:'Verdana';","letter-spacing:0px;color: #2A80B9 !important;font-family: Montserrat, Arial, Helvetica, sans-serif !important;",$workoutDetail['workout_desc']);
		$workoutDetail['workout_title']	=	str_replace("letter-spacing:0px;color:#0B333C;font-size:10px;font-family:'Verdana';","letter-spacing:0px;color: #2A80B9 !important;font-family: Montserrat, Arial, Helvetica, sans-serif !important;",$workoutDetail['workout_title']);
		$workoutDetail['workout_provide']	=	str_replace("letter-spacing:0px;color:#0B333C;font-size:10px;font-family:'Verdana'","letter-spacing:0px;color: #2A80B9 !important;font-family: Montserrat, Arial, Helvetica, sans-serif !important;",$workoutDetail['workout_provide']);
		//==============
		
		//For removing br tag form workout details ends		
		$workout_detail 	= trim(stripslashes($workoutDetail['workout_desc']));
		$workout_title 		= trim(stripslashes($workoutDetail['workout_title']));
		$workout_provide 	= trim(stripslashes($workoutDetail['workout_provide']));
		$data 		.= "$workout_detail";
		$provides 	.= $workout_provide;
		
		//echo "<pre/>";print_r($data );exit;
	
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
$maxPage    = ceil($numrows/$recordsPerPage);
$i 		    = 1;
$count 	    = 0;
$nav 	    = '';

//Display '$opt_links_count' number of links
while($count++ < $maxPage)
{
	if($i == $pageNum)
	{	  
		  $nav .= " <li class='active'><a>".$i."</a></li> ";
	}
	else
	{
	   	$workid = $workout_flex_ids[$i];
		// Display format - <li class="blu_bg"><a href="#" class="select">1</a></li>
	 	$nav .= "<li>
					<a href=\"javascript:;\"  onclick=\"javascript:htmlData('program_details_workout_pagination.php','p=$i','".urlencode($pgm_flexid)."','$pgmid');
					htmlComment('comment_pagination.php','pg=1','$pgmid','$workid');\">$i</a>
				</li>";
	 		 
	}
	$i++;
	if($i > $maxPage) break; //If the current page exceeds the total number of pages, get out.
}

/*if ($pageNum > 1)
	{
		$page = $pageNum - 1;
		$workoutprev = $workout_flex_ids[$page];
		$workoutfirst = $workout_flex_ids[1];
		$prev = "<a  class='btn lft' href=\"javascript:;\"  onclick=\"javascript:htmlData('program_details_workout_pagination.php','p=$page','".urlencode($pgm_flexid)."','$pgmid');htmlComment('comment_pagination.php','pg=1','$pgmid','$workoutprev');\">&lt; ".$parObj->_getLabenames($arrayData,'prevsession','name')."</a> ";
		
		$first = "<li> <a class='btn lft' href=\"javascript:;\"  onclick=\"javascript:htmlData('program_details_workout_pagination.php','p=1','".urlencode($pgm_flexid)."','$pgmid'); htmlComment('comment_pagination.php','pg=1','$pgmid','$workoutfirst');\">&lt;</a></li>";
	}
else
	{
		$prev = '';
		$first = '';
	}*/
if ($pageNum < $maxPage)
	{   $page        = $pageNum + 1;
		$workoutnext = $workout_flex_ids[$page];
		$workoutlast = $workout_flex_ids[$j-1];
		
		$next = "<div class='page_btn'><strong><a href=\"javascript:;\" class='btn rit' onclick=\"javascript:htmlData('program_details_workout_pagination.php','p=$page','".urlencode($pgm_flexid)."','$pgmid');htmlComment('comment_pagination.php','pg=1','$pgmid','$workoutnext');\">".$parObj->_getLabenames($arrayData,'nextsession','name')." &gt;</a></strong></div>";
	}
else
	{   $next = '';
	}
if(isset($_SESSION['user']['userId'])){
			$qstnpop .="<div class='page_btn'><strong><a href=\"javascript:;\" class='btn lft' onclick=\"javascript:questionMHRText();\">".$parObj->_getLabenames($arrayData,'questionMHR','name')."</a></strong></div>";
		}else{
			$qstnpop ="";
		}
if($workout_cnt > 0)
	{ 
		$result	 =	'<section itemscope itemtype="https://schema.org/ExercisePlan" class="seance_1" >';
		$result	 .=	"<div class='left height_equal'><img src='".ROOT_FOLDER."images/corner.png' alt='image' class='corner'>";
        $result	 .=	'<div class="title">'.mb_strtoupper($parObj->_getLabenames($arrayData,'session','name'),'UTF-8')." ".$pageNum.'</div>';
        $result	 .=	"<figure><img src='".ROOT_FOLDER."images/img-jiwok_02.jpg' alt='image'></figure>";
        $result	 .=	"</div>";
      	$result	 .='<article class="content height_equal">';
	  	$result	 .='<span class="line"></span>';	  
		$result.=	'<h2 itemprop="alternativeHeadline">'.$workout_title.'</h2>';	
		$data1 = mb_strlen($data,'UTF-8');			

		if($data1>2090)//1950
		{
		//$result.=	'<div class="page-div-height force-overflow" id="style-5" itemprop="description">';		
		$result.=	'<div class="page-div-height force-overflow" id="style-5" itemprop="description">';	
		}
		else
		{
		$result.=	'<div  class="force-overflownew" id="style-5" itemprop="description">';		
		}
		$result.=	$data.mb_strlen($data,'UTF-8');		 ;	
		$result.=	'</div>';
		$result.=	'<h2 itemprop="alternativeHeadline"><strong>'.$parObj->_getLabenames($arrayData,'provide_w','name').'</strong></h2>';
		$result.=	'<span itemprop="guideline"><strong>'.$provides.'</strong></span>';
		$result.=	$qstnpop.$next;
		$result	 .='</article>';
	   
		$result	 .='</section>';
		 //new generate button  starts
		$data 	  	 	= $objPgm->_displayTrainingProgram($pgmid,$lanId);
        $programType 	= $trainingTypeFlexId[trim($data['training_type_flex_id'])];
        $loginUrl = base64_encode("program_details.php?program_id=".base64_encode($pgmid));
		if($workout_cnt>0)
			{ 
         	if(!($objPgm->_checkLogin())) 
				{ 
                //~ <div style="width:340px; 
   //~ background: none repeat scroll 0 0 #F1C40F;
    //~ border: 2px solid #FFFFFF;
    //~ border-radius: 3px;
    //~ color: #FFFFFF !important;
    //~ display: block;
    //~ padding: 15px 53px; float: right;
    //~ margin-bottom: 20px; font-size: 100%;padding: 15px 53px;">
 $result	 .= 	'<div class="new_sub_but1"><a href="javascript:;" class="btn-sign" onclick=window.location.href="login_failed.php?fromPgm=1&returnUrl='.$loginUrl.'&msg='.base64_encode(3).'" class="btn-sign generate_new_but" style="padding:10px 10px; background: #f6de7a;" name="subscribe" id="subscribe" rel="nofollow">'.mb_strtoupper($parObj->_getLabenames($arrayData,'subscribe_unlog','name'),'UTF-8').'</a></div>';
 
				}
	 		elseif($programType=="program")
	 			{ 
	   			$programDt1 = $objPgm->_getUserTrainingProgramConfirm($userid);
	  			if(count($programDt1) > 0) 
						{ 
	   			         $result	 .= '<div class="new_sub_but1"><a href="javascript:;" class="btn-sign"  name="subscribe" id="subscribe" onclick="displayConfirm();" rel="nofollow">'.mb_strtoupper($parObj->_getLabenames($arrayData,'subscribe','name'),'UTF-8').'</a></div>';				
	  					} 
						else 
						{ 
				         $result	 .= '<div class="new_sub_but1"><a href="javascript:;" class="btn-sign"  name="subscribe" id="subscribe" onclick="displayDate();" rel="nofollow">'.mb_strtoupper($parObj->_getLabenames($arrayData,'subscribe','name'),'UTF-8').'</a></div>'; 
						}
	  			}		
	  			else
				{ 
	  			if($workout_cnt>0)
					{
	  				?>
					 <form name="tt" action="" method="get">
					 <?php 
					if($objPgm->checkUserPaymentStaus($userid) == 0 && !$objPgm->checkProgramSubscribed($userid)):
					$result	 .= '<a href="javascript:;" class="btn-sign generate_new_but"  name="subscribe" id="subscribe" onclick="showSessionMessage();" rel="nofollow">'.$parObj->_getLabenames($arrayData,'subscribe','name').'</a>';
					$result	 .= '<input name="subscribe" type="hidden" id="subscribe" value="'.mb_strtoupper($parObj->_getLabenames($arrayData,'subscribe','name'),'UTF-8').' />';
                    else:	
					$result	 .='<a href="javascript:;" class="btn-sign generate_new_but"  name="subscribe" id="subscribe" onclick="document.tt.submit();" rel="nofollow">'.$parObj->_getLabenames($arrayData,'subscribe','name').'</a>';
					$result	 .='<input name="subscribe" type="hidden" id="subscribe" value="'.mb_strtoupper($parObj->_getLabenames($arrayData,'subscribe','name'),'UTF-8').'/>';
					endif;
					$result	 .='<input type="hidden" name="program_id" id="program_id" value="'.base64_encode($pgmid).  '/>';
					$result	 .='<input type="hidden" name="todo" value="subscribe" id="todo" />';
					?>
					</form>
					<?php 
					}  
				} 
			}
				
		//new generate button  ends
		/* Displaying No of sessions as pagination*/
		$result	 .= "<div class='seane_paging'>";
		$result	 .= "<div class='title'>".mb_strtoupper($parObj->_getLabenames($arrayData,'session','name'),'UTF-8')."</div>";		
    	$result	 .= "<ul id=\"paging\">".$nav."</ul>";
    	$result	 .= "</div>";
    	//-----popup
    	$result	 .= '<section class="pop_search" style="overflow: auto;height:85%"><div class="popbox_search"><div align="center">';
        $result  .= nl2br($parObj->_getLabenames($arrayData,'questionMHRText','name'));
		$result  .= "<a href='".ROOT_JWPATH."ticket.php' >".$parObj->_getLabenames($arrayData,'questionMHRTextLink','name')."</a>";
		$result	 .= '</div></div></section>';
		

        //-----
    }
else
	{
		$result.='<p><strong>No workouts found</strong></p>';
	}
echo $result;
?>
