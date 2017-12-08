<?php
	/*
	Function 	:	This code is used for ajax pagination for training listing
	Pgmer		: 	Ajith
	Date		:	24 feb,2009
	*/
	
	session_start();
	ob_start();
	include_once('includeconfig.php');
	include_once("includes/classes/class.programs.php");
	
	$userId	=	$_SESSION['user']['userId'];
	if($lanId=="")
		 $lanId=1;
	
	$objGen     	= new General();
	$objPgm     	= new Programs($lanId);
	$parObj 		= new Contents();
	
	//collecting data from the xml for the static contents
	$returnDataList		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
	$arrayData		= $returnDataList['general'];
		
	$link_separator = "|";
	$thisPage="training_pagination_single.php";
	// Number of records to show per page
	$recordsPerPage = 3;
	// //default startup page
	$opt_links_count = 4;
	$max = $opt_links_count;

	if(isset($_GET['pageNo']))
		{
			$pageNum = $_GET['pageNo'];
			settype($pageNum, 'integer');
		}
	else 
	 		$pageNum = 1;
			
	$offset = ($pageNum - 1) * $recordsPerPage;
	
	//collecting All training program for displaying
	$start=$offset;
	$len=$recordsPerPage;
	$getAllTraining = $objPgm->_getAllSingleTrainingPrograms($userId,$start,$len);
	$trn_cou = count($getAllTraining);
	if($trn_cou >0)
	{
	
	// HTML STARTS HERE
	$data .= "<li>".$parObj->_getLabenames($arrayData,'single','name')."</li>";
	# 2 change/add columns name
	for($i=0;$i<$trn_cou;$i++)
	{
	//HTML DATA TO BE DISPLAY ON PARENT PAGE
	 
		$target		=	$objGen->_output($getAllTraining[$i]['program_target']);
		$pgmFor 	= 	$objPgm->_getGroups(trim($getAllTraining[$i]['program_for']),$lanId,'group');
		$schedule 	= 	$objPgm->_getName1(trim($getAllTraining[$i]['schedule_type']),$lanId,'schedule_type');
		$pgmLevel 	= 	$objPgm->_getName1(trim($getAllTraining[$i]['program_level_flex_id']),$lanId,'levels');
		$cntTarget	=	strlen($target);
		if($cntTarget > 180)
			$target		=	$objGen->_outputLimit($getAllTraining[$i]['program_target'],0,180)." ...";
		$data	.=	"<li>";
		if($objGen->_output($getAllTraining[$i]['program_image'])!="")
		{
			$data.="<div class='image'><img src='uploads/programs/".$objGen->_output($getAllTraining[$i]['program_image'])."' alt='Jiwok' width='118' height='140' /></div>";
		}
		else
		{
			$data.="<div class='image'><img src='images/no_photo_pgm.jpg' alt='program' border='0' width='118' height='140' /></div>";
		}
		$data.="<div class='description'><form>";
		$data.="<h2><a href='".ROOT_JWPATH."program_generate2.php?program_id=".base64_encode($getAllTraining[$i]['program_id'])."'>".$objGen->_output($getAllTraining[$i]['program_title'])."</a></h2>";
			//$data.="<a href='".ROOT_JWPATH."program_details_single.php?program_id=".base64_encode($getAllTraining[$i]['program_id'])."'>".$objGen->_output($getAllTraining[$i]['program_title'])."</a>";
		
		
		$data.="<span>".$parObj->_getLabenames($arrayData,'duration','name').":<b>".$objGen->_output(trim($getAllTraining[$i]['program_schedule']))." ".$objGen->_output($schedule)."</b></span><span>".$parObj->_getLabenames($arrayData,'level','name').": <b>".$objGen->_output(trim($pgmLevel))."</b></span><span>".$parObj->_getLabenames($arrayData,'for','name').": <b>".$objGen->_output(trim($pgmFor))."</b></span>";
		$data.="</form></div>
		<a class='searchSingleSubmit' href='".ROOT_JWPATH."program_generate2.php?program_id=".base64_encode($getAllTraining[$i]['program_id'])."'>Découvrir cet entraînement</a>
		<div class='clear'></div>
		</li>";
	}
	$data.="<div class='pagination'>";
	
	# to get the total count of the record
	$numrows	= $objPgm->_getAllSingleTrainingProgramsCount($userId);
	//$numrows = $getAllTraining['cnt'];
	
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
		  $nav .= " ".$i." ".$link_separator;
		}
		else
		{
		 $nav .= " "."<a href=\"#\" onclick=\"javascript:loadPage('".$thisPage."','pageNo=$i')\">$i</a> $link_separator";
		}
		$i++;
		if($i > $maxPage) break; //If the current page exceeds the total number of pages, get out.
	}
	if ($pageNum > 1)
	{
		$page = $pageNum - 1;
		$prev = " "."<a href=\"#\" onclick=\"javascript:loadPage('".$thisPage."','pageNo=$page')\"><strong>".$parObj->_getLabenames($arrayData,'prev','name')."</strong></a> ";
		
		$first = "<a href=\"#\" onclick=\"javascript:loadPage('".$thisPage."','pageNo=1')\"><strong><<</strong></a>";
	}
	
	else
	{
	$prev = '<strong> </strong>';
	$first = '<strong> </strong>';
	}
	
	
	if ($pageNum < $maxPage)
	{
	$page = $pageNum + 1;
	$next = "<a href=\"#\" onclick=\"javascript:loadPage('".$thisPage."','pageNo=$page')\"><strong>".$parObj->_getLabenames($arrayData,'next','name')."</strong></a> ";
	$last = "<a href=\"#\" onclick=\"javascript:loadPage('".$thisPage."','pageNo=$maxPage')\"><strong>>></strong></a>";
	}
	
	else
	{
	$next = '<strong> </strong>';
	$last = '<strong> </strong>';
	}
	if($numrows<=0)
	{
	  	$result = $parObj->_getLabenames($arrayData,'noresult','name');
	}else{
		$result = $data."$first$prev$nav$next$last</div>";
	}
}
else
{
	//$programListUrl	=	"http://www.jiwok.com/entrainement/Seance+unique+%2830+min-+1+heure...%29-11";
	$programListUrl	=	"http://www.jiwok.com/entrainement/seance-unique-%2830-min--1-heure...%29-11";
	if($lanId==1){
		$programListUrl	=	"http://www.jiwok.com/en/training/Single+workout+%2830+min-+1+hour...%29-11";
	}
	
	if($lanId==5){
		$programListUrl	=	"http://www.jiwok.com/pl/entrainement/pojedyncze-treningi-%28np.-trening-30-minutowy%29-11";
	}
	$result = "<li>".$parObj->_getLabenames($arrayData,'nosingle','name')."";
	$result = $result."<br/><br/>".$parObj->_getLabenames($arrayData,'nosingleNew','name')." <a href='".$programListUrl."'><u>".$parObj->_getLabenames($arrayData,'nosingleNew1','name')."</u></a></li>";
}
	echo $result;
?>
