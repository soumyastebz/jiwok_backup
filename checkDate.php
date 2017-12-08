<?php
session_start();
include_once('includeconfig.php');
include_once("includes/classes/class.programs.php");
//include_once("includes/trak_analysis.php");

if($lanId=="")     $lanId=1;
$errorMsg = '';	 
$userid 		= $_SESSION['user']['userId'];	 
$startdate 		= trim($_REQUEST['startdate']);
$freedays 		= trim($_REQUEST['freedays']);
$programid 		= base64_decode(trim($_REQUEST['programid']));
$programType 	= trim($_REQUEST['programtype']);

$objGen     	= new General();
$objPgm     	= new Programs($lanId);
$parObj 		= new Contents();
//$trakObj		= new trakAnalysis();

$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
$arrayData		= $returnData['general'];
$startdate1 	= date('Y-m-d',strtotime(trim($startdate)));



if($objPgm->_checkValidDate($startdate))
   { 
   
	  	 $expdate = $objPgm->_getProgramExpireDate($programid,$startdate1);
		 
		 $userEmail	= $_SESSION['user']['user_email'];
		 $trakProperties = array('Program_Id'=>$programid,'Start_date'=>$startdate1,'Expiry_date'=>$expdate);
		 
		 if($objPgm->_checkFreePeriod($userid,$freedays,$startdate))
		 	{	
		  		$query = "INSERT INTO programs_subscribed 
				values('','$programid','$userid',CURDATE(),'','1','p','$startdate1','$expdate','$programType','','1')";
		  		$result =  $GLOBALS['db']->query($query);
				//$response = $trakObj->trakProgramSubscription($userEmail,$trakProperties);   
				//echo "success";
		  		echo 1;	
		 	}	
		 elseif($objPgm->_checkPaymentPeriod($userid,$startdate))
		 	{
		 	 	$query = "INSERT INTO programs_subscribed 
				values('','$programid','$userid',CURDATE(),'','1','p','$startdate1','$expdate','$programType','','1')";
		  		$result =  $GLOBALS['db']->query($query);
				//$response = $trakObj->trakProgramSubscription($userEmail,$trakProperties);
				//echo "success";
		  		echo 1;
		 	}
		 /*elseif($objPgm->checkWorkoutGenerated($userid))
		 {
			  $query = "INSERT INTO programs_subscribed values('','$programid','$userid',CURDATE(),'','1','p','$startdate1','$expdate','$programType','','1')";
			  $result =  $GLOBALS['db']->query($query);
			  echo "success";
		 } */
		elseif($objPgm->checkProgramSubscribed($userid))
	 		{		
		  		$query = "INSERT INTO programs_subscribed 
				values('','$programid','$userid',CURDATE(),'','1','p','$startdate1','$expdate','$programType','','1')";
		  		$result =  $GLOBALS['db']->query($query);
				//$response = $trakObj->trakProgramSubscription($userEmail,$trakProperties);
				//echo "success";
		  		echo 1;
	 		}
	 	else
	   		echo "<font color='red'>".$parObj->_getLabenames($arrayData,'validsubdate','name')."</font>";  
   }
   else
   {
     	$errorMsg = "<font color='red'>".$parObj->_getLabenames($arrayData,'validdate','name')."</font>";
	 	echo $errorMsg;
   }
?>