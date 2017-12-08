<?php
	session_start();
	include_once('includeconfig.php');
	include_once("includes/classes/class.Languages.php");
	include_once("includes/classes/class.programs.php");
	
	if($lanId=="")
     $lanId=1;
	$objLan    		= new Language();
	$objPgm     	= new Programs($lanId);
	$parObj 		= new Contents('downloadOriginMP3.php');
	$headingData	= $parObj->_getTagcontents($xmlPath,'trainingprogram','pageHeading');
	$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
	$arrayData		= $returnData['general'];
	$lanName =  strtolower($objLan->_getLanguagename($lanId));
	$session = $parObj->_getLabenames($arrayData,'session','name');
	if(trim($_REQUEST['work']) != "")
	{
		$workoutid	= base64_decode(trim($_REQUEST['work']));
		$workOutData = $objPgm->_getWorkoutOriginSourceFile($workoutid);
		$workoutFile = trim(stripslashes($workOutData['workout_origin_file']));
		$myFile1 = "./uploads/originforce/".$lanName."/".$workoutFile;
		$workoutName = $workoutFile;
		if(file_exists($myFile1))
		{
		 	header("Content-type: application/force-download");
        	header('Content-Disposition: inline; filename="' . $workoutName . '"');
       		header("Content-Transfer-Encoding: Binary");
       		header("Content-length: ".filesize($myFile1));
       		header('Content-Type: application/octet-stream');
       		header("Content-disposition: attachment; filename=\"".$workoutName."\""); 
       		readfile("$myFile1");
	   	}
		
	} 
	
	    
	?>
	
	