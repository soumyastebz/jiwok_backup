<?phpsession_start();ob_start();include_once('includeconfig.php');include_once("includes/classes/class.Languages.php");include_once("includes/classes/class.programs_eng_beta.php");if($lanId == "") $lanId=1;$errorMsg = '';	 $userid 	= $_SESSION['user']['userId'];	 $objGen     = new General();$objPgm     = new Programs($lanId);$parObj 	= new Contents();$objLan    	= new Language();$returnData	= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');$arrayData	= $returnData['general'];$lanName 	= strtolower($objLan->_getLanguagename($lanId));$todayDate 	= date('Y-m-d');// Number of records to show per page$recordsPerPage = 1;if(isset($_GET['p']))	{		$pageNum = $_GET['p'];		settype($pageNum, 'integer');	}else  	$pageNum 	= 1;$pgm_flexid = trim($_REQUEST['pgm_flexid']);$pgmid 		= trim($_REQUEST['pgmid']);print_r($pgm_flexid);echo "PPPP";print_r($objGen->_output(trim($pgm_flexid)));$subscribeDetails 	= 	$objPgm->_getSubscriptionDetails($userid,$pgmid);$workOuts 			=  	$objPgm->_getTrainingCalWorkoutDates($userid);$workOutDays 		=  	$objPgm->_getTrainingCalWorkoutDays($userid);$orderArray 		= 	$objPgm->_getWorkoutOrders($pgm_flexid);print_r($orderArray );$freedays 			= 	$objPgm->_getFreeDays($userid);$workout_cnt 		= 	$objPgm->_getWorkoutCount($objGen->_output(trim($pgm_flexid)),$lanId);$workout_flex_ids 	= 	array();$coachqry	=	"SELECT program_author FROM program_master WHERE program_id =".$pgmid;$coach_result 	= mysql_query($coachqry);$coach_result 	= mysql_fetch_row($coach_result);if($coach_result[0] == 'Stephanie')		{						$coachImage	=	"stephanie_detail.jpg";		}		else		{						$coachImage	=	"pascal.png";		}$j			= 1;$offset 	= ($pageNum - 1) * $recordsPerPage;$query1 	= "	SELECT workout_flex_id FROM program_workout 				WHERE training_flex_id='".addslashes(trim($pgm_flexid))."' and lang_id={$lanId} 				ORDER BY workout_order ASC";$result1 	= mysql_query($query1);while($row1 = mysql_fetch_assoc($result1))	{		$workout_flex_ids[$j] = $objGen->_output(trim($row1['workout_flex_id']));		$j++;		}	$offset = ($pageNum - 1) * $recordsPerPage;$query 	= "	SELECT workout_flex_id,workout_date 			FROM program_workout WHERE training_flex_id='".addslashes(trim($pgm_flexid))."' and lang_id={$lanId} 			ORDER BY workout_order ASC LIMIT $offset, $recordsPerPage";# 1. Main query$result = mysql_query($query) or die('Mysql Err. 1');// print table$data 		= '';$provides 	= '';# 2 change/add columns namewhile($row = mysql_fetch_assoc($result))	{		$workout_flex_id = $objGen->_output(trim($row['workout_flex_id']));			$workout_date = $objGen->_output(trim($row['workout_date']));		if(count($workOuts)>0 && trim($subscribeDetails['program_type'])=="program")			{				$work_date 		= $workOuts[$workout_flex_id."@@".($pageNum - 1)];				$tmpWorkOutDate	= date('Y-m-d',strtotime($work_date));				$w_day 			= date('d',strtotime($work_date));				$w_month 		= date('F',strtotime($work_date));				if($lanName=="english")				{					$w_month = $w_month;				}				elseif($lanName	==	'polish')				{					//For polish					$monthArray_pl	=	array('January'=>'Stycznia','February'=>'Lutego','March'=>'Marca','April'=>'Kwietnia','May'=>'Maja','June'=>'Czerwca','July'=>'Lipca','August'=>'Sierpnia','September'=>'Wrzesnia','October'=>'Pazdziernika','November'=>'Listopada','December'=>'Grudnia');					$w_month = utf8_encode($monthArray_pl[$w_month]);				}									else								{					$w_month = $monthArray[$w_month];				}							}			$workoutDetail 		= $objPgm->_getWorkoutDetailAll(trim(str_replace(' ','',$workout_flex_id)),$lanId);		////				$workoutDetail['workout_desc']	=	str_replace("letter-spacing:0px;color:#0B333C;font-size:10px;font-family:'Verdana';","letter-spacing:0px;color: #2A80B9 !important;font-family: Montserrat,Arial,Helvetica,sans-serif;",$workoutDetail['workout_desc']);		$workoutDetail['workout_title']	=	str_replace("letter-spacing:0px;color:#0B333C;font-size:10px;font-family:'Verdana';","letter-spacing:0px;color: #2A80B9 !important;font-family: Montserrat,Arial,Helvetica,sans-serif;",$workoutDetail['workout_title']);		$workoutDetail['workout_provide']	=	str_replace("letter-spacing:0px;color:#0B333C;font-size:10px;font-family:'Verdana';","letter-spacing:0px;color: #2A80B9 !important;font-family: Montserrat,Arial,Helvetica,sans-serif;",$workoutDetail['workout_provide']);		/////		//~ $data = str_replace("<font style=\'letter-spacing:0px;color:#0B333C;font-size:10px;font-family:'Verdana';\'>","<font style=\"letter-spacing:0px;color: #2A80B9 !important;\">",$data);		//~ ////		///		$workout_detail 	= trim(stripslashes($workoutDetail['workout_desc']));		$workout_title 		= trim(stripslashes($workoutDetail['workout_title']));		$workout_provide 	= trim(stripslashes($workoutDetail['workout_provide']));				$data 		.= "$workout_detail";				$provides 	.= $workout_provide;	}	# Update this query with same where clause you are using above.$query 	= "	SELECT COUNT(workout_flex_id) AS dt FROM program_workout 			WHERE training_flex_id='".addslashes(trim($pgm_flexid))."' and lang_id={$lanId} 			ORDER BY workout_order ASC";$result 	= mysql_query($query) or die('Mysql Err. 2');if($result){	$row 		= mysql_fetch_assoc($result);	$numrows 	= $row['dt'];}# 4$maxPage = ceil($numrows/$recordsPerPage);/*if($opt_links_count) 	{		$start_from = $pageNum - round($max/2) + 1; 			// = 4 - round(5/2) + 1 = 4-3+1 = 2		$start_from = ($maxPage - $start_from < $max) ? $maxPage - $max + 1 : $start_from ; //(9-2) < 9 ? If yes, 9-5+1. | If no, no change.		$start_from = ($start_from > 1) ? $start_from : 1;	// If it is lesser than 1, make it 1(all paging must start at the '1' page as it is the first page) : = 2	} else 	{ // If $opt_links_count is 0, show all pages		$start_from = 1;		$max = $maxPage;	}*/$pgm_flexid = str_replace('+','%2B',$pgm_flexid);$i 		= 1;$count = 0;$nav = '';//Display '$opt_links_count' number of linkswhile($count++ < $maxPage)	{ 		if($i == $pageNum)			{				  $nav .= " <li class='active'><a>".$i."</a></li> ";				  			}		else			{				  $workid 		= $workout_flex_ids[$i];				  $work_date 	= $workOuts[$workid."@@".($i-1)];				  $mon 			= date('n',strtotime($work_date));				  $yr 			= date('Y',strtotime($work_date));				  $day 			= date('j',strtotime($work_date));				  $workoutOrder = $orderArray[$i-1]; 				  				  if($work_date<$todayDate)						$a = 'a';				  elseif($work_date==$todayDate)						$a = 'b';				  elseif($work_date>$todayDate)						$a = 'c';					  $nav .= "<li><a href=\"#f\" onclick=\"javascript:				  htmlData('workout_pagination_cal_eng.php','p=$i','$pgm_flexid','$pgmid');				  htmlComment('comment_pagination_cal.php','pg=1','$pgmid','$workid');				  topWorkOutButtonOnclick('workout_onclick.php','p=$i','$pgm_flexid','$pgmid');				  navigate_1('$mon','$yr','$a','$day','$workid','$workoutOrder');\">$i</a></li>";				  			}		$i++;		if($i > $maxPage) break; //If the current page exceeds the total number of pages, get out.	}if ($pageNum > 1)	{		$page 			= $pageNum - 1;		$workoutprev 	= $workout_flex_ids[$page];		$work_date1 	= $workOuts[$workoutprev."@@".($page-1)];		$mon1 			= date('n',strtotime($work_date1));		$yr1 			= date('Y',strtotime($work_date1));		$day1 			= date('j',strtotime($work_date1));		$workoutOrder1 	= $orderArray[$page-1]; 				if($work_date1<$todayDate)			$b = 'a';		elseif($work_date1==$todayDate)			$b = 'b';		elseif($work_date1>$todayDate)			$b = 'c';							$prev = "<input type=\"button\" class=\"btn2 lft\" value=\"< ".$parObj->_getLabenames($arrayData,'prevsession','name')."\" onclick=\"javascript:			htmlData('workout_pagination_cal_eng.php','p=$page','$pgm_flexid','$pgmid');			htmlComment('comment_pagination_cal.php','pg=1','$pgmid','$workoutprev');			topWorkOutButtonOnclick('workout_onclick.php','p=$page','$pgm_flexid','$pgmid');			navigate_1('$mon1','$yr1','$b','$day1','$workoutprev','$workoutOrder1');\"  >";											$workoutfirst 	= $workout_flex_ids[1];		$k 				= 1;		$work_datef 	= $workOuts[$workoutfirst."@@".($k-1)];		$monf 			= date('n',strtotime($work_datef));		$yrf 			= date('Y',strtotime($work_datef));		$dayf 			= date('j',strtotime($work_datef));		$workoutOrderf 	= $orderArray[$k-1]; 				if($work_datef<$todayDate)			$bf = 'a';		elseif($work_datef==$todayDate)			$bf = 'b';		elseif($work_datef>$todayDate)			$bf = 'c';						$first = "<li><a href=\"#f\" onclick=\"javascript:			htmlData('workout_pagination_cal_eng.php','p=1','$pgm_flexid','$pgmid');			htmlComment('comment_pagination_cal.php','pg=1','$pgmid','$workoutfirst');			topWorkOutButtonOnclick('workout_onclick.php','p=$page','$pgm_flexid','$pgmid');			navigate_1('$monf','$yrf','$bf','$dayf','$workoutfirst','$workoutOrderf');\">&lt;</a></li> ";	}else	{		$prev = '';		$first = '';	}if($pageNum < $maxPage)	{		$page 			= $pageNum + 1;		$workoutnext 	= $workout_flex_ids[$page];		$work_date2 	= $workOuts[$workoutnext."@@".($page-1)];				$mon2 			= date('n',strtotime($work_date2));				$yr2 			= date('Y',strtotime($work_date2));				$day2 			= date('j',strtotime($work_date2));				$workoutOrder2 	= $orderArray[$page-1]; 						if($work_date2<$todayDate)					$c = 'a';				elseif($work_date2==$todayDate)					$c = 'b';				elseif($work_date2 > $todayDate)					$c = 'c';								$next = "<input type=\"button\" class=\"btn2 rit\" value=\"".$parObj->_getLabenames($arrayData,'nextsession','name')." >\" onclick=\"javascript:			htmlData('workout_pagination_cal_eng.php','p=$page','$pgm_flexid','$pgmid');			htmlComment('comment_pagination_cal.php','pg=1','$pgmid','$workoutnext');			topWorkOutButtonOnclick('workout_onclick.php','p=$page','$pgm_flexid','$pgmid');			navigate_1('$mon2','$yr2','$c','$day2','$workoutnext','$workoutOrder2');\" >";				$workoutlast 		= $workout_flex_ids[$j-1];				$klast 				= $j-1;				$work_dateLast  	= $workOuts[$workoutlast."@@".($klast-1)];				$monLast 			= date('n',strtotime($work_dateLast));				$yrLast 			= date('Y',strtotime($work_dateLast));				$dayLast 			= date('j',strtotime($work_dateLast));				$workoutOrderLast 	= $orderArray[$klast-1]; 						if($work_dateLast<$todayDate)					$bLast = 'a';				elseif($work_dateLast==$todayDate)					$bLast = 'b';				elseif($work_dateLast>$todayDate)					$bLast = 'c';					$last = "<li><a href=\"#f\" onclick=\"javascript:			htmlData('workout_pagination_cal_eng.php','p=$maxPage','$pgm_flexid','$pgmid');			htmlComment('comment_pagination_cal.php','pg=1','$pgmid','$workoutlast');			topWorkOutButtonOnclick('workout_onclick.php','p=$page','$pgm_flexid','$pgmid');			navigate_1('$monLast','$yrLast','$bLast','$dayLast','$workoutlast','$workoutOrderLast');\">&gt;</a></li> ";		}else	{		$next = '';		$last = '';	}if($workout_cnt>0)	{					//MHR text										$qstnpop  = "<input type=\"button\" class=\"btn2 lft\" onclick=\"questionMHRText();\" value=\"".$parObj->_getLabenames($arrayData,'questionMHR','name')."\" \" >";						$result1  = '<div style="display: none;color: #2A80B9 !important;"><div id="inline3" class="popupstyle" style="color: #2A80B9 !important;" >';			$result1 .= nl2br($parObj->_getLabenames($arrayData,'questionMHRText','name'));			$result1 .= 	"<strong>";			$result1 .= "<a href='".ROOT_JWPATH."ticket.php' >".$parObj->_getLabenames($arrayData,'questionMHRTextLink','name')."</a>";			$result1 .= "</strong>";			$result1 .= '</div></div>';					//MHR text 				//-----popup    	$result1	 .= '<section class="pop_search" style="overflow: auto;height:85%"><div class="popbox_search"><div align="center">';        $result1  .= nl2br($parObj->_getLabenames($arrayData,'questionMHRText','name'));		$result1  .= "<a href='".ROOT_JWPATH."ticket.php' >".$parObj->_getLabenames($arrayData,'questionMHRTextLink','name')."</a>";		$result1	 .= '</div></div></section>';						include_once("workout_generation.php"); // To get the workout MP3 generation onclick		// work out pagination		 $result ='<article class="content height_equal" style="color: #2A80B9 !important;">';		$result .='<div class="hed"><span class="text">'.$parObj->_getLabenames($arrayData,'session','name')." ".$pageNum.'</span> <input type="submit"  class="btn_orng" value="'.$parObj->_getLabenames($arrayData,'generateMP3','name').'" '.$workoutOnclick.'></div>';					//				$result.="<h3>".$workout_title."</h3>                <p >".$data."</p><h3>".$parObj->_getLabenames($arrayData,'provide_w','name')."</h3><div class='blok_01'><figure><img src='".ROOT_FOLDER."images/corner.png' class='corner'><img src='".ROOT_FOLDER."images/".$coachImage."' alt='jiwok'></figure><article><strong>".$provides."<strong></article></div><div class='controls2'><div>".$qstnpop .$prev.$next."</div><input type='submit'  value='".$parObj->_getLabenames($arrayData,'generateMP3','name')."' class='btn_orng' ".$workoutOnclick."></div>  </article>     <div class=\"clear\"></div>";		$result	.=	"<div class=\"seane_paging\">        <div class=\"title\">".$parObj->_getLabenames($arrayData,'session','name')."</div>        <ul  id=\"paging2\">".$nav."</ul>        </div>";	}else	{		$result.="<p class=\"produiMidBoxRightText\">No workouts found</p><span class=\"produiMidBoxRightLink\">&nbsp;</span></p>";	}				echo trim($result).$result1;?>