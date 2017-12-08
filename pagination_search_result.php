<?php
	/*
	Function 	:	This code is used for ajax pagination for training listing
	Pgmer		: 	Ajith
	Date		:	24 feb,2009
	*/
	
	session_start();
	ob_start();


	include_once('includeconfig.php');
	//init_set('display_errors', 1);
	//error_reporting(E_ALL);
	include_once("includes/classes/class.programs.php");
	
		 
	if($lanId=="")
		 $lanId=1;
	
	$objGen     	= new General();
	$objPgm     	= new Programs($lanId);
	$parObj 		= new Contents();
	
	//fetch the static content form the xml
	$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
	$arrayData		= $returnData['general'];
	//collecting data from the xml for the static contents
	$returnDataList		= $parObj->_getTagcontents($xmlPath,'listprograms','label');
	$arrayDataList		= $returnDataList['general'];
	
	$link_separator = "|";
	$thisPage=ROOT_JWPATH."pagination_search_result.php";
	// Number of records to show per page
	$recordsPerPage = 5;
	// //default startup page
	$opt_links_count = 4;
	$max = $opt_links_count;

	if($_GET['pageNo']!='')
		{
			$pageNum = $_GET['pageNo'];
			settype($pageNum, 'integer');
		}
	else 
	 		$pageNum = 1;
	// for getting the pgm ids		
	if($_GET['pgmIds']!="")	
	$pgmMasterIds = $_GET['pgmIds'];	
	//print_r($pgmMasterIds);
	$offset = ($pageNum - 1) * $recordsPerPage;
	
	//collecting All training program for displaying
	$field="program_title";
	$type="DESC";
	$start=$offset;
	$len=$recordsPerPage;
	
	// HTML STARTS HERE
	$data = "<h1>".$parObj->_getLabenames($arrayData,'ursearch','name')."</h1><ul>";
	
	if($pgmMasterIds!= ""){
		
		//split the pgm id into array
		$pgmMasterIdArr	=	explode(",",$pgmMasterIds);	
		//print(count($pgmMasterIdArr));
		//to get the end point of the sorted pgm array
		$ends		=	$start+$len;
		//$ends=count($pgmMasterIdArr);
		# 2 change/add columns name
		$j=0;
		for($i=$start;$i<$ends;$i++)
		{	
			$getTraining	=	$objPgm->_getOnePgm($pgmMasterIdArr[$i],$lanId);
			
			if(count($getTraining) > 0){
			    $pgm_status = $objGen->_output($getTraining['program_status']);
				// If program_status is Standby, do not display the program
				if ($pgm_status == '2') {
					$ends++;	// Take one more program
					$j++;
					continue;	// continue the loop
				}
				$target		=	$objGen->_output($getTraining['program_target']);
				$pgmFor = $objPgm->_getGroups(trim($getTraining['program_for']),$lanId,'group');
				$schedule = $objPgm->_getName1(trim($getTraining['schedule_type']),$lanId,'schedule_type');
				$pgmLevel 	 = $objPgm->_getName1(trim($getTraining['program_level_flex_id']),$lanId,'level');
				$cntTarget	=	strlen($target);
				if($cntTarget > 180)
				$target		=	$objGen->_outputLimit($getTraining['program_target'],0,180)." ...";
				
				//image disply
				if(!is_file($_SERVER["DOCUMENT_ROOT"].ROOT_FOLDER."uploads/programs/".$objGen->_output($getTraining['programImage']))){
				$imagePgm	=	ROOT_FOLDER."images/no_photo_pgm.jpg";
				}else{
				$imagePgm	=	ROOT_FOLDER."uploads/programs/".$objGen->_output($getTraining['programImage']);
				}
				//HTML DATA TO BE DISPLAY ON PARENT PAGE
				if($pgm_status=='4')
				  $buttonValue = $parObj->_getLabenames($arrayDataList,'discover','name');
				else
				  $buttonValue = $parObj->_getLabenames($arrayDataList,'releasesoon','name');  
				$data.="<li><span class='listThumbHolder'><img src='".$imagePgm."' width='118' height='140' alt='Jiwok' /></span><div id='listDetails'>";
				$pro_url = $objPgm->makeProgramTitleUrl($getTraining['program_title']);
				$normal_url= $objPgm->normal_url($pro_url);
				if($pgm_status=='4')
				{
					//$data.="<form method='post' action='".ROOT_FOLDER."program_details.php?program_id=".base64_encode($getTraining['program_master_id'])."'>";
					//$data.="<form method='post' action='".ROOT_FOLDER.$objPgm->makeProgramTitleUrl($getTraining['program_title'])."-".$getTraining['program_master_id']."'>";
					$data.='<form method="post" action="'.ROOT_JWPATH.$normal_url.'-'.$getTraining['program_master_id'].'">';
				}
				$data.="<h3>";
				if($pgm_status=='4')
				{
					//$data.="<a href='".ROOT_FOLDER."program_details.php?program_id=".base64_encode($getTraining['program_master_id'])."'>".$objGen->_output($getTraining['program_title'])."</a>";
				//	$data.="<a href='".ROOT_FOLDER.$objPgm->makeProgramTitleUrl($getTraining['program_title'])."-".$getTraining['program_master_id']."'>".$objGen->_output($getTraining['program_title'])."</a>";
				$data.='<a href="'.ROOT_JWPATH.$normal_url.'-'.$getTraining['program_master_id'].'">'.$objGen->_output($getTraining['program_title']).'</a>';
				}
				else
				{
					$data.=$objGen->_output($getTraining['program_title']);
				}
				$data.="</h3><span>".$parObj->_getLabenames($arrayData,'duration','name').":<b>".$objGen->_output(trim($getTraining['program_schedule']))." ".$objGen->_output(trim($schedule));
				if(strtolower($objGen->_output(trim($schedule)))!="mois" && ($objGen->_output(trim($getTraining['program_schedule']))>1)) { $data.='s';} 
				$data.="</b><b><font style='padding-left:55px;color:#006699'>".$parObj->_getLabenames($arrayData,'rythm','name').":</font>&nbsp;".$objGen->_output(trim($getTraining['program_rythm']))." ".$parObj->_getLabenames($arrayData,'times','name')."/".$objGen->_output($schedule);
				
				$data.="</b></span><span>".$parObj->_getLabenames($arrayData,'level','name').": <b>".$objGen->_output(trim($pgmLevel))."</b></span><span>".$parObj->_getLabenames($arrayData,'for','name').": <b>".$objGen->_output(trim($pgmFor))."</b></span><span class=\"listButtonHolder\"><input name='' type='submit' class='buttonList' value='".$buttonValue."' /></span>";
				if($pgm_status=='4')
				{
					$data.="</form>";
				}
				$data.="</div></li>";
			}
		}
			
		$data.="</ul><div class='pagination'>";
		
		# to get the total count of the record
		  $numrows=count($pgmMasterIdArr);
		 //print $j;
		 $numrows=$numrows-$j;
		//$numrows =$getPgmCount['cnt'];
		
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
			 $nav .= " "."<a href=\"#\" onclick=\"javascript:loadPage('".$thisPage."','pageNo=$i','pgmIds=$pgmMasterIds')\">$i</a> $link_separator";
			}
			$i++;
			if($i > $maxPage) break; //If the current page exceeds the total number of pages, get out.
		}
		if ($pageNum > 1)
		{
			$page = $pageNum - 1;
			$prev = " "."<a href=\"#\" onclick=\"javascript:loadPage('".$thisPage."','pageNo=$page','pgmIds=$pgmMasterIds')\"><strong>".$parObj->_getLabenames($arrayData,'prev','name')."</strong></a> ";
			
			$first = "<a href=\"#\" onclick=\"javascript:loadPage('".$thisPage."','pageNo=1','pgmIds=$pgmMasterIds')\"><strong><<</strong></a>";
		}
		
		else
		{
		$prev = '<strong> </strong>';
		$first = '<strong> </strong>';
		}
		
		
		if ($pageNum < $maxPage)
		{
		$page = $pageNum + 1;
		$next = "<a href=\"#\" onclick=\"javascript:loadPage('".$thisPage."','pageNo=$page','pgmIds=$pgmMasterIds')\"><strong>".$parObj->_getLabenames($arrayData,'next','name')."</strong></a> ";
		$last = "<a href=\"#\" onclick=\"javascript:loadPage('".$thisPage."','pageNo=$maxPage','pgmIds=$pgmMasterIds')\"><strong>>></strong></a>";
		}
		
		else
		{
		$next = '<strong> </strong>';
		$last = '<strong> </strong>';
		}
			
		$result = $data."$first$prev$nav$next$last</div>";
		}
	else
	{
	$result=$data."<li><span class='errorPrograms'>".$parObj->_getLabenames($arrayDataList,'noSearchResult','name')."</span></li></ul>";
	}	
	echo $result;
?>
