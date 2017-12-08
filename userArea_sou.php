<?php
ob_start();
session_start();
/*ini_set('display_errors',1);
  error_reporting(E_ALL|E_STRICT);*/
/*--------------------------------------------------*/
// Project 		: Jiwok
// Purpose		: New Design Integration - Dash-board
// Created on	: 05-10-2015
// Created by	: soumya
/*--------------------------------------------------*/

include_once('includeconfig.php');
include_once('regDetail.php');
include_once("includes/classes/class.programs.php");
include_once("includes/classes/class.sort.php");
include_once("includes/classes/class.Languages.php");
include_once('includes/classes/class.Payment.php');
include_once('includes/classes/class.Documents_Download.php');
include_once('includes/classes/class.giftcode.php');
include_once("includes/classes/class.discount.php");

unset($_SESSION['registration']);
unset($_SESSION['login']);
unset($_SESSION['payment']);
unset($_SESSION['subscription']);
$userid		= $_SESSION['user']['userId'];
if($lanId	==	"")  $lanId=1;
$flag 		= 0;
$errorMsg 	= '';	 
$wtext 		= '';

$objGen     			=	 	new General();
$objPgm     			=		new Programs($lanId);
$objSort				=		new Sort($lanId);
$objLan    				= 		new Language();
$paymentObj				= 		new Payment();
$objDisc				= 		new Discount($lanId);
$objgift        		=   	new gift();
$parObj 				= 		new Contents('userArea.php');
$dbObj     				=   	new DbAction();	

//dynamic timezone changing
if($tz	=	$objGen->getTomeZonePHP($userid))	date_default_timezone_set($tz);

$returnDataProfile		= $parObj->_getTagcontents($xmlPath,'myprofile','label');
$arrayDataProfile		= $returnDataProfile['general'];
$returnData				= $parObj->_getTagcontents($xmlPath,'popup','label');
$arrayDataPopup			= $returnData['general'];
$returnErrorData		= $parObj->_getTagcontents($xmlPath,'popup','messages');
$arrayErrorData			= $returnErrorData['errorMessage'];
$headingData			= $parObj->_getTagcontents($xmlPath,'trainingprogram','pageHeading');
$returnData				= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
$arrayData				= $returnData['general'];
$returnErrorData		= $parObj->_getTagcontents($xmlPath,'payment','messages');
$arrayErrorDataDiscount	= $returnErrorData['errorMessage'];
$returnDataGift			= $parObj->_getTagcontents($xmlPath,'registrationComplete','label');
$arrayDataGift			= $returnDataGift['general'];
$returnPayData			= $parObj->_getTagcontents($xmlPath,'payment','label');
$arrayPayData			= $returnPayData['general'];
$lanName 				= strtolower($objLan->_getLanguagename($lanId));
if(!isset($lanName ))
{
	$lanName 			=	"french";
}
$docDownloadObj			= new Documents_Download($lanId, $lanName);
$error 			= "";
$today 			= date('Y-m-d');
$redirect_url 	= base64_encode('userArea.php');
//$parObj->_getLabenames($arrayData,'invoice','name');
if(!($objPgm->_checkLogin()))
{   //print_r($redirect_url);exit;
	
	header('location:login_failed.php?returnUrl='.$redirect_url,true,301);
}

if($lanId==1)
	{
		$search_link	=	"search.php";//"training";
	} 
else 
	{
		$search_link	=	"search.php";//"entrainement";
	}
	
if(isset($_SESSION['successPayment']))
{
	$_REQUEST["actn"]	=	$_SESSION['successPayment'];
	unset($_SESSION['successPayment']);	
}
if(isset($_POST['user_discount']) !="" ):

	$errorMsg = 0;
	include('userdiscount.php');	
	if($errorMsg == 0):
		header("Location:payment1_reg.php",true,301);
		exit;
	endif;		
	elseif($_POST['user_discount'] =="" && $_POST['renewSubscriptionIdBtn']):
		header("Location:payment1_reg.php",true,301);
		exit;
		
endif;

$freedays 			= $objPgm->_getFreeDays($userid);
$freeBalanceDays 	= $objPgm->_findBalanceFreeDays($userid,$freedays);

$cur_day 			= date('l');
$cur_month 			= date('F');
$cur_date 			= date('j.S');
$cur_year 			= date('Y');
$date_subscript 	= explode('.',$cur_date);

if($lanName=="polish")
{
	$dayArray = array('Sunday'=>'Niedziela','Monday'=>'Poniedzia&#322;ek','Tuesday'=>'Wtorek','Wednesday'=>'&#346;roda','Thursday'=>'Czwartek ','Friday'=>'Pi&#261;tek','Saturday'=>'Sobota');
	
	$monthArray  = array('January'=>'Stycznia','February'=>'Lutego','March'=>'Marca','April'=>'Kwietnia','May'=>'Maja','June'=>'Czerwca','July'=>'Lipca','August'=>'Sierpnia','September'=>'Wrzesnia','October'=>'Pazdziernika','November'=>'Listopada','December'=>'Grudnia');	
}
if($lanName=="english")
	{
		$cur_day = $cur_day;
	}
else
	{ 
		$cur_day = $dayArray[$cur_day];
	}
if($lanName=="english")
	{
		$cur_month = $cur_month;
	 	$subscrpt = trim($date_subscript[1]);
	}
else
	{
		$cur_month = $monthArray[$cur_month];
	}
	
$user 				= $objPgm->_getUserDetails($userid);
$nikeUserDetails 	= $objPgm->_getNikeDetails($userid);
$user_name 			= trim(stripslashes($user['user_fname'].' '.$user['user_lname']));
$nikeUserName 		= trim(stripslashes($nikeUserDetails['nike_login']));
$nikeUserPass 		= base64_decode(trim(stripslashes($nikeUserDetails['nike_password'])));
$usrPhotoPath 		= "uploads/users/";
$user_photo 		= trim(stripslashes($user['user_photo']));

if($user_photo != "")
	{
		$iParams = $objPgm->_imageResize(trim($user_photo),$usrPhotoPath,87,106);
		$iWidth  = $iParams[0];
		$iHeight  = $iParams[1];
	}
$program = $objPgm->_getUserTrainingProgram($userid);

if(count($program)>0) 
	{ 
	echo "step 1";
		$program_stat	= 'p';
		$flexid 		= stripslashes(trim($program['flex_id']));
		$program_id		= stripslashes(trim($program['program_id']));
		$pgmSubscribe	  	 = $objPgm->_getSubscriptionDetails($userid,$program_id);
		$imgPath = "admin/crop/assets/img/";
		$image = $objGen->_output(trim($program['program_image']));
		$image_new	=	$imgPath.$image;
		if(file_exists($image_new) && (!empty($image)))
		{
			$image_new	=	$imgPath.$image;
		}
		else
		{
			$image_new	= "images/slide_03.jpg";
		} 
				
	
			
		$workout_cnt 		= $objPgm->_getWorkoutCount($flexid,$lanId);
		$data 				= $objPgm->_displayTrainingProgram($program_id,$lanId);
		/*Limit description text*/
		$pgmDesc 	= 	substr(strip_tags($data['program_desc']),0, 300);
		$pos	 	= 	strrpos($pgmDesc, " ");
		if($pos>0) 
			{
				$pgmDesc = substr($pgmDesc, 0, $pos);
			}

		$workoutDatesArray 	= $objPgm->_getTrainingCalWorkoutDates($userid);
		//print_r($workoutDatesArray);
		$workoutOrderArray 	= $objPgm->_getWorkoutOrders($flexid);
		$workOrder 			= 0;
		foreach($workoutDatesArray as $key => $value)
		{
			$work_out = explode("@@",$key);
			$workOrder++;
			if($value>=$today)
			{
				$workDate	   = $value;  // store workout dates
				$workDateH	   = $value; // store workout date for historical page
				$workoutFlexId = trim($work_out[0]);
				break;
			}
		}

		if($workDate==$today)
			{
	    		$workText = $parObj->_getLabenames($arrayData,'workoutday','name'); 
			}	
		else
	  		{ 
	    		$noWorkText 	= $parObj->_getLabenames($arrayData,'noworkout','name');
	    		$workText 		= $parObj->_getLabenames($arrayData,'nextworkout','name'); 
	    		$wtext 			= "nextwork";
			}


		  /* for passing to historical page to show todays workout, if no workout for today then previous workout */
		  if($workDateH==$today)
			{
				$workTodayPresent = 'yes';
				$workOrderHistory = $workOrder; 
			}
		  else
			{
				$workTodayPresent = '';
				$workOrderHistory = $workOrder-1; 
			}
		if($workOrderHistory > 0)
			{
				echo "step 2";
				$workoutFlexHistory = $objPgm->_getWorkoutTodayLast($flexid,$workOrderHistory,$lanId);
				$workoutFlexHistory = $workoutFlexHistory."@".$workOrderHistory;
			}
		/* historical page parameters ends */
		
		$workout_details 			= $objPgm->_getWorkoutDetailAll($workoutFlexId,$lanId);
		//For Document Downloads
		$workouts_subscribed		= $objPgm->getWorkoutsOfProgram($flexid, $lanId);
		$program_document_download	= $docDownloadObj->getTrainingProgramPDFFiles($data['flex_id'], $data['program_title']);
		$workouts_document_download	= $docDownloadObj->getWorkoutPDFFiles($workouts_subscribed);

	} 
else 
	{
		// GET SUGGESTED PROGRAMS 
		$image_new	= "images/slide_03.jpg";
		define('PROGRAMS_NUM', 10);
		$last_used_program		= $objSort->getLastUsedProgram($userid);
		$completed_data 		= $program_ids = $program_ids_arr = $selected_programs = $program_titles = array();
		if($last_used_program != '') 
			{
				// Get programs from DB maching user's profile
				$matched_programs_from_db	= $objSort->_getWizardIds($userid,4,'',$lanId);
				// change format of array
				foreach($matched_programs_from_db as $matched_program_from_db) 
					{
						$matched_programs[]	= $matched_program_from_db['pgmId'];
					}
				// Get programs already completed by user
				$completed_data_from_db	= $objSort->getCompletedPrograms($userid);
				// change format of array
				foreach($completed_data_from_db as $one_completed_data_from_db) 
					{
						$completed_data[]	= $one_completed_data_from_db['program_id'];
					}
				// remove completed programs from matched programs
				$unique_matched_programs	= array_diff($matched_programs, $completed_data);
				// if not, get the flex_ids as a comma separated string 
				$programs_after				= $objSort->getSuggestedProgramsAfterThis($last_used_program);
				// get program_ids from the comma separated string of flex_ids
		
				if($programs_after != '') 
					{
						$program_ids_arr	= $objSort->getProgramIdsFromWizardAfter($programs_after);
					}
				// change format of array
				foreach($program_ids_arr as $program_id_arr) 
					{
						$program_ids[]	= $program_id_arr['program_id'];
					}
				//get 4 programs in order of matched
				foreach($unique_matched_programs as $matched_program_id) 
					{
						if(in_array($matched_program_id, $program_ids)) 
							{
								$selected_programs[]	= 	$matched_program_id;
								if(sizeof($selected_programs)==PROGRAMS_NUM) 
								break;
							} 
					}
				// get size of selected programs
				$size_of_selected_programs	= sizeof($selected_programs);
				// if size of selected programs < 4 get more programs from suggested programs to make the size 4
				if($size_of_selected_programs<PROGRAMS_NUM)
					{
						// calculate count of needed programs to make count of selected programs 4
						$needed_programs_count	= PROGRAMS_NUM-$size_of_selected_programs;
						// for each program in suggested programs, if program not in selected programs, add to it. decrement needed programs count by 1. If needed programs count equals 0, break out of loop
						foreach($program_ids as $suggested_program_id ) 
							{
								if(!in_array($suggested_program_id, $selected_programs))
									{
										$selected_programs[]	= 	$suggested_program_id;
										$needed_programs_count--;
									}
								if($needed_programs_count == 0) break;
							}
					}

			}
		else 
			{ 
			 	if(count($program)==0)
			 		{
						 // if no programds subscribed yet
						 $otherPrograms = $objSort->_getpgmid($lanId);
						 foreach($otherPrograms as $otherProgram)
						 {
							$selected_programs[]	= $otherProgram['program_id'];
						 }
						 $pgrmcountflag=1;
			 		}
				 else
				 	{
						 //Not subscribed  a program yet
						 $otherPrograms = $objSort->_getWizardIds($userid, PROGRAMS_NUM,'',$lanId);
						 foreach($otherPrograms as $otherProgram)
						 {
							$selected_programs[]	= $otherProgram['pgmId'];
						 }
			 		}
			}
		// get the titles from 4 of them
		if(sizeof($selected_programs) != 0) 
			{
				$program_titles	= $objSort->getProgramTitleFromId($selected_programs, $lanId);
			}
		$cnt = count($program_titles);	
		unset($matched_programs_from_db, $matched_programs, $completed_data_from_db, $completed_data, $unique_matched_programs, $programs_after, $program_ids_arr, $program_ids, $selected_programs);

	}
	$static_documents_download	= $docDownloadObj->getStaticPDFFiles();
	if(isset($_POST['shift']))
 		{
			$workoutcal_flexid_form 	= stripslashes(trim($_REQUEST['workoutcal_flexid']));
			$workoutcal_flexid_explode 	= explode("@",$workoutcal_flexid_form);
			$workoutcal_flexid 			= trim($workoutcal_flexid_explode[0]);
			$workoutcal_order 			= trim($workoutcal_flexid_explode[1]); 
			$shiftSession 				= stripslashes(trim( $_REQUEST['shiftSession']));
			 $shiftAll 					= stripslashes(trim($_REQUEST['shiftAll']));
		
			$shiftSessionValue 			= explode('.',$shiftSession);
			$workoutArray 				= $objPgm->_getTrainingCalWorkoutDates($userid);
			$workoutDates 				= array();
			$workoutFlexIds 			= array();
			$j							= 0;
			foreach( $workoutArray as $key1 => $value1)
				{
					$work_out1 		  = explode("@@",$key1);
					$workDates[]	  = $value1;  // store workout dates
					$workoutFlexIds[] = trim($work_out1[0]);
				}
			//$position = array_search(trim($workoutcal_flexid),$workoutFlexIds);
			$position = array_search(trim($workoutcal_order),$workoutOrderArray);
			if(trim(stripslashes($workoutcal_flexid))== "")
				{
					
	  				$error .= $parObj->_getLabenames($arrayData,'shifterror1','name');
				}
			else
				{
					if($shiftAll==0) //not shift all=0
						{
							foreach( $workoutArray as $key => $value)
							{
								if($j<$position)
								{
									$workoutDates[]	  = $value;
								}
								else
								{
									$workoutDay = trim($shiftSessionValue[0]);
									if($shiftSessionValue[1] == 'a')
										$postdate = $objPgm->_findDateNext($value,$workoutDay);
									else
										$postdate = $objPgm->_findDatePrev($value,$workoutDay);
									if($j==$position)
									{
										if($postdate < $pgmSubscribe['subscribed_date'])
											{
												$error .= $parObj->_getLabenames($arrayData,'shifterror2','name'); 
											}
										if(in_array($postdate,$workDates))
			 								{
												$error .= $parObj->_getLabenames($arrayData,'shifterror3','name'); 
											}
										if($shiftSessionValue[1] == 'b' && $postdate < $workDates[$j-1] && $workDates[$j-1]!="")
			 								{
												$error .= $parObj->_getLabenames($arrayData,'shifterror4','name'); 
											} //cannot change workout order  
										if($shiftSessionValue[1] == 'a' && $postdate > $workDates[$j+1] && $workDates[$j+1]!="")
			 								{
												$error .= $parObj->_getLabenames($arrayData,'shifterror4','name'); 
											}//cannot change workout order

									}		
									if($j>$position)
									{ 
										$postdate = $value;  
									}	
									if(trim($error)=="") 
									{
										$workoutDates[]	= $postdate; 
									}
								}
			 					$j++;  
							}
							if(count($workoutDates) >0)
								{
									if($workoutDates[0] < $pgmSubscribe['subscribed_date'])
										{ 
											$error .= $parObj->_getLabenames($arrayData,'shifterror2','name');
										}
									$lastWorkoutKey = count($workoutDates)-1;
									if($workoutDates[$lastWorkoutKey] > $pgmSubscribe['program_expdate'])
										{
											$pgm_expdate = $objPgm->_findDateNext($workoutDates[$lastWorkoutKey],1);
										}		
								}
							if($error=="" && count($workoutDates) >0)
								{
									$postpondedDates = implode(',',$workoutDates);
									if(trim($pgm_expdate) != "")
										{
	 										$setExpDate = ",program_expdate='".addslashes($pgm_expdate)."'";
										}
									$query = "	UPDATE programs_subscribed set 
												posponded_date ='".addslashes($postpondedDates)."'".$setExpDate." 
												WHERE 	programs_subscribed_id=".$pgmSubscribe['programs_subscribed_id'];
									$upres = $GLOBALS['db']->query($query);
									$messg = "1";
									$_REQUEST['workoutcal_flexid'] = "";
									header("location:userArea.php?messg=".$messg,true,301);
								}	
						}
						if($shiftAll==1)//shift all =1
							{
									
								foreach( $workoutArray as $key => $value)
									{
										if($j<$position)
										{
											$workoutDates[]	  = $value;
										}
										else
										{
											$workoutDay = trim($shiftSessionValue[0]);
											
											if($shiftSessionValue[1] == 'a')
												$postdate = $objPgm->_findDateNext($value,$workoutDay);
											else
												$postdate = $objPgm->_findDatePrev($value,$workoutDay);
												
											if($j==$position)
											{
												if($postdate < $pgmSubscribe['subscribed_date'])
												{
													$error .= $parObj->_getLabenames($arrayData,'shifterror2','name'); 
												}
												elseif(in_array($postdate,$workoutDates))
												{
													$error .= $parObj->_getLabenames($arrayData,'shifterror3','name'); 
												}
												elseif($shiftSessionValue[1] == 'b' && $postdate < $workDates[$j-1] && $workDates[$j-1]!="")
												{
													$error .= $parObj->_getLabenames($arrayData,'shifterror4','name');
												}	
												else 
												{ 
													$workoutDates[]	= $postdate; 
												}	
											}		
											if($j>$position)
											{ 
											   if(in_array($postdate,$workoutDates))
												{
													$error .= $parObj->_getLabenames($arrayData,'shifterror4','name');
												}
												else 
												{ 
													$workoutDates[]	= $postdate; 
												}	
											}	
			   							}
										$j++; 	
									}
									if(count($workoutDates) >0)
									{
										if($workoutDates[0] < $pgmSubscribe['subscribed_date'])
										{ 
											$error .= $parObj->_getLabenames($arrayData,'shifterror2','name');
										}
										$lastWorkoutKey = count($workoutDates)-1;	
										if($workoutDates[$lastWorkoutKey] > $pgmSubscribe['program_expdate'])
										{
												$pgm_expdate = $objPgm->_findDateNext($workoutDates[$lastWorkoutKey],1);
										}		
									}
									
									if($error=="" && count($workoutDates) >0)
									{
										$postpondedDates = implode(',',$workoutDates);
										if(trim($pgm_expdate) != "")
										{
											$setExpDate = ",program_expdate='".addslashes($pgm_expdate)."'";
										}
									 	$query = "	UPDATE programs_subscribed set 
													posponded_date ='".addslashes($postpondedDates)."'".$setExpDate." 
													WHERE 	programs_subscribed_id=".$pgmSubscribe['programs_subscribed_id'];
										
										$upres = $GLOBALS['db']->query($query);
										$messg = "1";
										$_REQUEST['workoutcal_flexid'] = "";
										header("location:userArea.php?messg=".$messg,true,301);	
									}	
							}
				}
 		}
$pdf = 0;
$dbClass	=	new DbAction();
$dbQuery	=  "select count(payment_id) as count from payment where payment_userid='".$userid."' AND ( version='New' OR version='stripe' OR version='polishstripe') AND payment_status='1'";
$resDb		=	$dbClass->dbSelectOne($dbQuery,DB_FETCHMODE_ASSOC);
if($resDb['count']>0)
	$pdf = 1;


$showRefUrl	=	false;
$showRefUrl	=	getSubStsUser($userid,$dbClass,$objPgm);
function getSubStsUser($userId,$dbClass,$objPgm){
	$showRefUrl	=	false;
	$qry	=	"select * from programs_subscribed where user_id=".$userId." and subscribe_status=1";
	$pgrmData	=	mysql_query($qry);
	$dataCnt	=	mysql_num_rows($pgrmData);
	if($dataCnt>1){
		$showRefUrl	=	true;
	}else{
		if($dataCnt==1){
			$woDates	=	$objPgm->_getTrainingCalWorkoutDates($userId);
			$i=0;
			foreach($woDates as $woDte){
				if($i==1){
					$secondWoDate	=	$woDte;
					break;
				}
				$i++;
			}
			if(($secondWoDate<=date("Y-m-d"))&&($secondWoDate!="")){
				$showRefUrl	=	true;
			}else{
				$showRefUrl	=	false;
			}
		}
	}
	return $showRefUrl;
}
global $onloadglobal;
if(trim(stripslashes($program['program_expdate']))!= "" && $today >= trim(stripslashes($program['program_expdate'])) && trim($_REQUEST['conf']) != "") { 
	$onloadglobal	=	"displayMessageConfirmBox();";
 } 
 else
 { $onloadglobal    =   "GetListFromCrowdSound();";
 }
include("header.php");
?>
<!--======================need ?==============-->
       <div id="dashboard">
      <?php if(0){ ?>
			<!--<style>
                #paymentMsg{
					color: #FE8510;
					font-size: 15px;
					font-weight: bold;
					padding-bottom: 22px;
					text-align: center;
                }
            </style>
      		<script type="text/javascript">
				$(document).ready(function(){
					setTimeout(function() { $("#paymentMsg").slideUp("slow"); }, 1000);
				})
			</script>
            <div id="paymentMsg">
            	<?=$parObj->_getLabenames($arrayData,'paymentSucessMsg','name');?>
            </div>-->
      <?php } ?>
      <?php
			$userpaymentstatus 	= $objPgm->checkUserPaymentStaus($userid);			
			$AccountExpDate 	= $objPgm->_findAccountExpireDate($userid);			
			$paymentTemp 		= $objPgm->_getUserPaymentTemp($userid);
			$userOldPaymentStatus=	$objPgm->checkUserPaymentStausCount($userid,'Old');
			$userNewPaymentStatus=	$objPgm->checkUserPaymentStausCount($userid,'New');
			$newPaymentRegisterCount	=	$objPgm->newPaymentRegisterCount($userid,'');		
			
			
			//For displaying the refferal link only in denis@jiwok.com account
			/*if($userid	==	'82501')
			{?>
				
                	<h2><a href="referrel_system.php" class="red-1" style="color:#FF7625;"> 
					<?php echo 'Referrel system';?>
                     </a></h2>
              	<?php
			}*/
			//User is in new payment using or just registered. Message to change plan
			/*if(($userNewPaymentStatus > 0 || $newPaymentRegisterCount>0) && ($today < trim($AccountExpDate)))
			{
				?>
				<p><b>
            <?=$parObj->_getLabenames($arrayData,'planChangeMsg','name');?>
            , <a href="payment_renew.php" class="red-1">
            <?=$parObj->_getLabenames($arrayData,'transaction_failed_msg_02_new','name');?>
            </a></b></p>
            <?php
			}
			//User is in old payment not yet try the new payment. Message to invite to new payment
			else */
			$today				=	date('Y-m-d');
			$qry 				=	"SELECT DATEDIFF('$AccountExpDate', '$today')";
			$result				= 	$GLOBALS['db']->getRow($qry, DB_FETCHMODE_ARRAY);
			$dayCount 			= 	$result[0];	
			
			//New starts
		/*	if($userNewPaymentStatus	>	0)
			{
				if($dayCount	>	0)
				{
								
				}
				else
				{?>
					<p><b><a href="payment_renew.php" class="red-1"> 
					<?php 
					echo $parObj->_getLabenames($arrayData,'transaction_failed_msg_01_new','name').$parObj->_getLabenames($arrayData,'transaction_failed_msg_02_new','name');?>
                     </a></b></p><?php
				}
			}
			else
			{
				if($newPaymentRegisterCount	>	0)
				{
								
				}
				elseif(($userOldPaymentStatus	>	0)	&&	($dayCount	>	0))	
				{
					?>
                	<p><b><a href="payment_renew.php" class="red-1"><?php
					echo $parObj->_getLabenames($arrayData,'planChangeMsg','name').",".$parObj->_getLabenames($arrayData,'transaction_failed_msg_02_new','name'); 	?>
                    </a></b></p><?php	
				}
				elseif($userpaymentstatus	>	0)
				{
					?>
					<p><b><a href="payment_renew.php" class="red-1"> 
					<?php 
					echo $parObj->_getLabenames($arrayData,'transaction_failed_msg_01_new','name').$parObj->_getLabenames($arrayData,'transaction_failed_msg_02_new','name');?>
                     </a></b></p><?php
				}
			}*/ ?>
             <!--=======================================-->
<section class="banner-static bnr-mrgn">
       <div class="bred-hovr second">
          <ul class="bredcrumbs">
               <li><?=mb_strtoupper($parObj->_getLabenames($arrayData,'searchPath','name'),'UTF-8');?></li>              
               <li><a href="<?=ROOT_JWPATH?>">
			  <?=mb_strtoupper($parObj->_getLabenames($arrayData,'homeName','name'),'UTF-8');?>
			  </a></li>
			   <li>&gt;</li>
			   <li><a href="#" class="select">
			  <?=mb_strtoupper($parObj->_getLabenames($arrayData,'myaccount','name'),'UTF-8');?>
			  </a></li>
            </ul>
       </div>
       <div class="bnr-content" style="position:relative;">
          <div class="frame slider-first">
<div class="callbacks_container">
      <ul class="rslides callbacks callbacks1" id="slider4">
       
		        <li><img data-lazy-src="<?=ROOT_FOLDER.$image_new?>" alt="jiwok">         
        </li>
		      
      </ul>
    </div>
         </div>
           
  
                  <div class="heading4"><p style='max-width: 980px;background: url("images/blu-transparent.png") repeat scroll 0 0 rgba(0, 0, 0, 0);'><?=$parObj->_getLabenames($arrayData,'hi','name') ?>&nbsp<?php if($lanId == 5){ echo ucfirst($user['user_fname']); }else{ echo  ucwords($user_name); } ?></p></div> 
                  <?php
				  if(count($program)>0) 
					{ 
						// ongoing user program check starts
						if($today >= trim(stripslashes($program['program_expdate']))) // if today date >= program expire date 
						{
							
						?>
						   <div class="heading5">
                           <span class="user-2">
                           <p>
						 <?=mb_strtoupper($parObj->_getLabenames($arrayData,'noworkout','name'),'UTF-8')?>&nbsp;&nbsp;&nbsp;
						 <?=$date_subscript[0]." ".utf8_encode($cur_month);?>
                           <br/>
                           <span class="underline">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                           </p>
                           <p class="des"><?php echo mb_strtoupper(trim($data['program_title']),'UTF-8');?></p>
                           </span>
                          </div> 
                          <a href="javascript:;" class="link linknew" onClick="gotoWorkout('<?=$workOrder?>','<?=base64_encode($program_id)?>');">
                        <?=mb_strtoupper($parObj->_getLabenames($arrayData,'seeRecentSession','name'),'UTF-8');?>
                        </a>
					<?php /*?><a href="entrainement" class="link" >
					<?=mb_strtoupper($parObj->_getLabenames($arrayData,'findNewTraining','name','UTF-8'));?>
					</a><?php */?>
					  
				   <?php } 
			   else
				 {
					?><div class="heading5"><span class="user-2"><p><?php
					 if($noWorkText!="")
					{
						echo mb_strtoupper($parObj->_getLabenames($arrayData,'noworkout','name'),'UTF-8'); 
						echo '&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;';
					} 
					if($workDate != "")
						{
						echo mb_strtoupper($workText,'UTF-8'); 
							if($wtext!="")
							{
								$w_day 		= date('d',strtotime($workDate));
								$w_month 	= date('F',strtotime($workDate));
								if($lanName	==	"english")
								{
									$w_month = $w_month;
								}
								else
								{
									$w_month = utf8_encode($monthArray[$w_month]);
								}
								echo " ".$w_day." ".$w_month;
							} 
						}
						if($wtext=="")
							{
								echo '&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;';
								echo mb_strtoupper($parObj->_getLabenames($arrayData,'workout','name'),'UTF-8');
								echo '&nbsp;&nbsp;';
								echo $workoutOrderArray[$workOrder-1];
							}
					
					?>
                    <br/>
                    <span class="underline">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    </p>
                    
                  
                    
                    <p class="des"><?php echo mb_strtoupper(trim($data['program_title']),'UTF-8');?></p></span></div> 
                    <a href="javascript:;" class="link linknew" onClick="gotoWorkout('<?=$workOrder?>','<?=base64_encode($program_id)?>');">
                <?=mb_strtoupper($parObj->_getLabenames($arrayData,'gotoworkout','name'),'UTF-8');?>
                </a>
					 <?php
				 	} 
				 }
				 else
				 {
					$sql	=	"SELECT * FROM programs_subscribed WHERE user_id=".$userid ;
					$res	=	mysql_query($sql);
					$count	=	mysql_num_rows($res);
						if(($lanId == 5)&& ($count <= 0))
						{ 
						?>
							
						<div class="heading5"><span class="user-2"><p><?=mb_strtoupper($parObj->_getLabenames($arrayData,'notrainingpgm','name'),'UTF-8');?>|
							  <?=$date_subscript[0]?>&nbsp;
							  <?php echo utf8_encode($cur_month);?></p></span></div> 
							
					  <?php
						}
						else
						{
						?>
					 <div class="heading5"><span class="user-2"><p>
						  <?=mb_strtoupper($parObj->_getLabenames($arrayData,'notrainingpgm','name'),'UTF-8');?>
						   |
						  <?=$date_subscript[0]?>&nbsp;
						  <?php echo utf8_encode($cur_month);?></p></span></div>
			          <?php
			} ?>
			<a href="<?php echo ROOT_JWPATH.$search_link; ?>" class="link linknew">
							<?=mb_strtoupper($parObj->_getLabenames($arrayData,'findmore','name'),'UTF-8');?>
							</a> 
		
<?php	}
				  ?>
           </div>
       </section>
         <!--======================calendar codes starts from here============-->
       <section class="my-colender mid-wrapper">
       <div class="frame">
		   <?php if(count($program) > 0) { ?>

              
          <div class="heading">
             <p class="date"><?php echo mb_strtoupper($cur_day,'UTF-8')." ".$date_subscript[0]." "  .$cur_month .", " .$cur_year ;?>
             </p>
             <p class="title"> <?=$parObj->_getLabenames($arrayData,'pgmcalendar','name');?></p>
          </div>
          <div class="CL">
          <script language="javascript" type="text/javascript">navigate("","");</script>
             <div id="calendar" class="clndr"></div>
             <?php
			 //===========================
		
			 if(count($program)>0) 
					{ 
						// ongoing user program check starts
						if($today >= trim(stripslashes($program['program_expdate']))) // if today date >= program expire date 
						{ ?> 
                        
                        <input type="button" class="btn userarea_btn ease" value="<?php echo mb_strtoupper($parObj->_getLabenames($arrayData,'seeRecentSession','name'),'UTF-8');?>" onClick="gotoWorkout('<?=$workOrder?>','<?=base64_encode($program_id)?>');">
                       <a href="<?=ROOT_JWPATH?>entrainement"> <input type="button" class="btn userarea_btn ease" value="<?php echo mb_strtoupper($parObj->_getLabenames($arrayData,'see_all','name'),'UTF-8');?>">
                        </a>
					  
				   <?php } 
			   else
				 { 
					 
					 
					?> 
					
                      <input type="button" class="btn ease" value="<?php echo mb_strtoupper($parObj->_getLabenames($arrayData,'gotoworkout','name'),'UTF-8');?>" onClick="gotoWorkout('<?=$workOrder?>','<?=base64_encode($program_id)?>');">
                    
					 <?php
				 	} 
				 }
				 else
				 {?>
					 <a href="<?=ROOT_JWPATH?>entrainement"> <input type="button" class="btn userarea_btn ease" value="<?php echo mb_strtoupper($parObj->_getLabenames($arrayData,'see_all','name'),'UTF-8');?>">
                        </a>
				<?php }
				 
			 //===========================
			 ?>             
          </div>
         
          <form action="userArea.php" id="shiftcal" name="shiftcal" method="post">
          <div class="options">
			  <div style="height: 337px;">
		  <!-----------messages code starts --------------->
          <?php 
				if(trim($error)!="" || trim($_REQUEST['messg'])!="") 
					{ 
						if(trim($error)!="") 
							{ 
								echo "<span class='error_message'>".$error."</span>"; 
							}
						if(trim($_REQUEST['messg'])!="") 
							{ 
								echo "<span class='error_message'>".$parObj->_getLabenames($arrayData,'shiftsuccess','name')."</span>"; 
							}
                	} 
				if(count($program)>0) 
					{
						if($today >= trim(stripslashes($program['program_expdate'])))  
							{
								echo "<span class='error_message'>".$parObj->_getLabenames($arrayData,'noworkout','name')."</span>";								
							}
						elseif($workDate == $today)
							{  
				?>
<!--displaying session details in old jiwok
                <div  id="showTodayWorkout">
                  <p><a href="<?=ROOT_JWPATH?>program_generate2.php?program_id=<?=base64_encode($program_id)?>&p=<?=$workOrder?>" class="blu">
                    <?=$parObj->_getLabenames($arrayData,'workout','name');?>
                    <?=$workoutOrderArray[$workOrder-1]?>
                    </a> | <a href="<?=ROOT_JWPATH?>program_generate2.php?program_id=<?=base64_encode($program_id)?>&p=<?=$workOrder?>" class="blu">
                    <?=$date_subscript[0]?>
                    <?=utf8_encode($cur_month)?>
                    </a></p>
                </div>
-->
                <?php 
							}
						else{
						?>
                <div  id="showTodayWorkout" ><span class='error_message'>
                     <?=$parObj->_getLabenames($arrayData,'noworkout','name')?>
                     </span>
                </div>
                <!-- no workouts today -->
                <?php
							}
					} 
				else{
					?>
				 <span class='error_message'>
                  <?=$parObj->_getLabenames($arrayData,'noprogrammsg1.0','name')?>
                  &nbsp;<a href="<?php echo ROOT_JWPATH.$search_link; ?>" >
                  <?=$parObj->_getLabenames($arrayData,'noprogrammsg1.1','name')?>
                  </a>
                 </span>
                <?php 
					} 
				?>
          <!-----------messages code ends--------------->
             <label><?=mb_strtoupper($parObj->_getLabenames($arrayData,'shiftdate','name'),'UTF-8');?></label>
             <div class="fields selet3">
              <select  name="shiftSession" id="shiftSession" <?php if(count($program)<=0){?>  disabled="disabled" <? } ?>>
                  <option value="1.b">1
                  <?=$parObj->_getLabenames($arrayData,'day','name');?>
                  <?=$parObj->_getLabenames($arrayData,'before','name');?>
                  </option>
                  <?php for($i=2;$i<=7;$i++) { ?>
                  <option value="<?=$i?>.b">
                  <?=$i?>
                  <?=$parObj->_getLabenames($arrayData,'days','name');?>
                  <?=$parObj->_getLabenames($arrayData,'before','name');?>
                  </option>
                  <?php } ?>
                  <option value="1.a">1
                  <?=$parObj->_getLabenames($arrayData,'day','name');?>
                  <?=$parObj->_getLabenames($arrayData,'after','name');?>
                  </option>
                  <?php for($i=2;$i<=7;$i++) { ?>
                  <option value="<?=$i?>.a">
                  <?=$i?>
                  <?=$parObj->_getLabenames($arrayData,'days','name');?>
                  <?=$parObj->_getLabenames($arrayData,'after','name');?>
                  </option>
                  <?php } ?>
               </select>
             </div>
             <label><?=mb_strtoupper($parObj->_getLabenames($arrayData,'shiftotherdate','name'),'UTF-8');?></label>
             <div class="fields selet3">
               <select  name="shiftAll"  id="shiftAll">
                         <option value="0">NON</option>
                         <option  value="1">YES</option>
               </select>
             </div>
			  <!-- value will assign to the hidden fields through calendar clicks----->
			 <input type="hidden" name="program_id" id="program_id" value="<?=$program_id?>"/>
			 <input type="hidden" name="workoutcal_flexid" id="workoutcal_flexid" value="<?=$_REQUEST['workoutcal_flexid']?>"/>
			 <input type="hidden" name="shift" value="<?=$parObj->_getLabenames($arrayData,'shift','name');?>" />
			
			
			  <?php 
				  if(count($program)<=0)
				  	{
				  ?>
				  <input type="button"  class="btn ease" value="<?=mb_strtoupper($parObj->_getLabenames($arrayData,'shift','name'),'UTF-8');?>" name="shift" > 

                  <?php 
				  	} 
				  else
				  	{
				  ?>
				    <input type="submit"  class="btn ease" value="<?=mb_strtoupper($parObj->_getLabenames($arrayData,'shift','name'),'UTF-8');?>" name="shift" > 
                  <?php 
				  	}
				  ?>
				  <label> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'exportcalendar','name'),'UTF-8');?></label>
                    <p>
					 <?php
					  if(count($program)>0) {?>
					 <a href="calendar/outlook.php"  target="_blank">
					     <input type="button" class="btn2" value="OUTLOOK">
					 </a> 
					 <a href="calendar/googlecalendar/index.php"  target="_blank">
						 <input type="button" class="btn2" value="GMAIL  <?=mb_strtoupper($parObj->_getLabenames($arrayData,'calendar','name'),'UTF-8');?>">
					 </a>
					<a href="calendar/ical.php"  target="_blank">
						 <input type="button" class="btn2" value="iCAL">
					</a>
					
						 <?php
					}else{
					 ?>
					 <input type="button" class="btn2" value="OUTLOOK">
                     <input type="button" class="btn2" value="GMAIL  <?=mb_strtoupper($parObj->_getLabenames($arrayData,'calendar','name'),'UTF-8');?>"> 
                     <input type="button" class="btn2" value="iCAL">
                     <?php
				      }?>
                     </p>
                    <ol class="listing_01">
                       <li> <?php echo $parObj->_getLabenames($arrayData,'exportCalendar1','name'); ?></li>
                       <li> <?php echo $parObj->_getLabenames($arrayData,'exportCalendar2','name'); ?></li>
                       <li><?php echo $parObj->_getLabenames($arrayData,'exportCalendar3','name'); ?> (*.vcs)</li>
                    </ol>
</div><!---gg-->
                  
                  <div class="block">
                   

					<?php 
						if(count($program)>0)
						{ 
						?>
					  <a href="javascript:;" class="class_btn_orng" onClick="return goHistoricalPage('<?=base64_encode($program_id)?>','<?=base64_encode("comsess")?>','<?=addslashes($parObj->_getLabenames($arrayData,'selectworkouterror','name'));?>');" >
					  <?=mb_strtoupper($parObj->_getLabenames($arrayData,'commentsession','name'),'UTF-8');?>
					  </a>
					  <?php 
						} 
						else 
						{
						?>
					  <a class="class_btn_orng">
					  <?=mb_strtoupper($parObj->_getLabenames($arrayData,'commentsession','name'),'UTF-8');?>
					  </a>
					  <?php 
						} 
					?>


                 <?php if($showRefUrl){ ?>
                     <a class="class_btn_blu" href="referrel_system.php"><?=mb_strtoupper($parObj->_getLabenames($arrayDataProfile,'refUrlTxt','name'),'UTF-8')?></a>
                           
                  <?php } ?>
                    
                    </div>
				
			</div>
            
             </form>  
                    
         
        <?php } 
		else { 
			/*if(($lanId == 5)&& ($count <= 0))
			{ 
			
			}
			else
			{ */
			 ?>
				<div class="heading" style="background:none; padding: 0 0 0px; margin-bottom:0;">
             <p class="date"><?php echo mb_strtoupper($cur_day,'UTF-8')." ".$date_subscript[0]." "  .$cur_month .", " .$cur_year ;?>
             </p>
             <p class="title1"><?=$parObj->_getLabenames($arrayData,'notrainingpgm','name');?></p>
              <hr style="margin: 1em 6em;">
          </div>
		
        <div class="new-user">
         <p><?php if($pgrmcountflag){echo $parObj->_getLabenames($arrayData,'tryotherfirst','name');}else{echo $parObj->_getLabenames($arrayData,'tryother','name');}?></p>
            <?php
				if($_SESSION["userRegProgram"]!=""){
					$_SESSION[$userid]["userRegProgram"]	=	$_SESSION["userRegProgram"];
					$_SESSION[$userid]["userRegProgramId"]	=	$_SESSION["userRegProgramId"];
					unset($_SESSION["userRegProgram"]);
					unset($_SESSION["userRegProgramId"]);
				}
			
			  if($_SESSION[$userid]["userRegProgram"]!=""){
			  		$pgmTitle	= $_SESSION[$userid]["userRegProgram"];
					
					$pro_url 	= $objPgm->makeProgramTitleUrl($_SESSION[$userid]["userRegProgram"]);
					$normal_url	= $objPgm->normal_url($pro_url);
					$pgmUrl		= ROOT_JWPATH.$normal_url;
					$pgmUrl    .= "-".$_SESSION[$userid]["userRegProgramId"];
			?>
             <!-- <p><a href="<?php //echo $pgmUrl;?>" style="color:#9C1800;">
                > <?php //echo $pgmTitle?>
                </a></p>-->
                <?php
			  }else if($cnt>0 && sizeof($program_titles)>0) 
				{ 
				?>
                <ul>
                <?php
				for($i=0;$i<$cnt;$i++) 
				{
					$pgmTitle	= $program_titles[$i]['program_title'];
					$pgmId		= $program_titles[$i]['program_master_id'];
					$pro_url 	= $objPgm->makeProgramTitleUrl($program_titles[$i]['program_title']);
					$normal_url	= $objPgm->normal_url($pro_url);
					$pgmUrl		= ROOT_JWPATH.$normal_url;
					$pgmUrl    .= "-".$pgmId;
			?>
           <a href="<?php echo $pgmUrl;?>"><li><?=$pgmTitle?></li>
                </a>
              <?php 
		  		 } 
				 ?>
                 </ul>
                 <?php
			}
		  ?>
              
            <div class="button">
        <p><a href="<?php echo ROOT_JWPATH.$search_link; ?>" class="btn"><?=$parObj->_getLabenames($arrayData,'findmore','name');?></a></p>
            </div>
            
           </div>
			 <?php   } ?>
         </div>
     </section>
         <!--=========================calendar codes ends here================-->
     <div class="frame-5 DS-b mid-wrapper" style="padding:0">
     <div id="tab-container" class="tab-container">
     <ul class="etabs">
     <?php if($lanId!=5){?>
 
   <li class="tab evidence active">
       <span class="left">&nbsp;</span> 
       <span class="right">&nbsp;</span> 
       <a href="#LES-TÉMOIGNAGES" class="active"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'myblog','name'),'UTF-8')?></a>
   </li>
   <li class="tab press2">
        <span class="left">&nbsp;</span> 
           <span class="right">&nbsp;</span>
       <a href="#LA-PRESSE"> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'mytools','name'),'UTF-8')?></a>
   </li>
  
   <li class="tab press3">
        <span class="left">&nbsp;</span> 
           <span class="right">&nbsp;</span>
       <a href="#LE-FORUM"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'myforum','name'),'UTF-8')?></a>
   </li>

 <?php } else{?>
 <li class="tab evidence active">
       <span class="left">&nbsp;</span> 
       <span class="right">&nbsp;</span> 
       <a href="#LA-PRESSE" class="active"> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'mytraining','name'),'UTF-8')?></a>
   </li>
  <?php }?>
   </ul>
 <div class="panel-container">
  <div id="LES-TÉMOIGNAGES" style="">
 <?php if($lanId!=5){?>
     
          <?php include_once('blog.php');?>
       
        <?php }?>
          </div>
       <div id="LA-PRESSE" class="tab_02" style="display: none;">
           <div class="frame-4">
               <div class="chanels chanels_new" id="press_det">
                <ul class="listing_02_tab_02">
                <?php if($lanId!=5){?>
                <a href="<?=ROOT_JWPATH?>software"><li><?=mb_strtoupper($parObj->_getLabenames($arrayData,'downloadsoft','name'),'UTF-8')?></li></a>
                <a href="<?=ROOT_JWPATH?>document_listing.php"><li> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'mydocs','name'),'UTF-8')?></li></a>
                <a href="<?=ROOT_JWPATH?>singlePrograms.php"><li> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'single','name'),'UTF-8')?></li></a>                  <?php } else{ ?>
                 <!--for polish no blog and forum data only tool like traning software ,doc etc -->
                   <a href="<?=ROOT_JWPATH?>document_listing.php"> <li>  <?=mb_strtoupper($parObj->_getLabenames($arrayData,'pdffile','name'),'UTF-8')?></li></a>
                    <a href="<?=ROOT_JWPATH?>document_listing.php"><li><?=mb_strtoupper($parObj->_getLabenames($arrayData,'nutritionaltips','name'),'UTF-8')?></li></a>
                   <a href="<?=ROOT_JWPATH?>software"><li> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'installsoftware1','name'),'UTF-8')?><br/>
                <?=mb_strtoupper($parObj->_getLabenames($arrayData,'installsoftware2','name'),'UTF-8')?></li></a>
                 
				<?php }?>
             </ul>        
               </div>
           </div>
       </div>
       <div id="LE-FORUM" class="tab_03" style="display: none;">
            <div class="frame-4">
               <div class="chanels chanels_new" id="press_det">
               <?php include_once('forum.php');?>
               </div>
           </div>
       </div>
       
       
  </div>
  
 </div>
 </div>
 <div class="frame_inner">
       <section class="profile">
       <?php   $image_user 	= 'uploads/users/'.$user_photo;
				if(!is_file($image_user)) 
				{  if(file_exists('./uploads/users/'.$user_photo) && (!empty($user_photo)))
					{
					$image_user = 'http://en.beta.jiwok.com/uploads/users/'.$user_photo;
				    }
				else{
					$image_user = './images/profile-dummy.png';
					} 
				} 
				else
				{   $imageParam = $objPgm->_imageResize(trim($user_photo),'uploads/users/',166,145);
					$widtht  	= $imageParam[0];
					$heightt  	= $imageParam[1];
				}
		?>
	   <div class="left">
            <figure class="profile-image image_02"><img data-lazy-src="<?=ROOT_FOLDER?><?=$image_user?>" alt="profile image"></figure>
       </div>
       <div class="profile-edit profile-1">
         <h2 class="name"><?php echo mb_strtoupper($user_name,'UTF-8');  ?></h2>
         <h3 class="title2"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'myinfo','name'),'UTF-8')?></h3>
          <ul class="listing_03">
            <a href="<?=ROOT_JWPATH?>myprofile.php"><li>
                <?=mb_strtoupper($parObj->_getLabenames($arrayData,'myprofile','name'),'UTF-8')?></li>
                </a> 
            <?php if($pdf == 1)
				{
				?>
                 <a href="<?=ROOT_JWPATH?>myInvoices.php"><li><?=mb_strtoupper($parObj->_getLabenames($arrayData,'invoice','name'),'UTF-8')?></li> </a>
           <?php }
				if($lanId==2) { ?>
                 <a href="<?=ROOT_JWPATH?>payment_new.php"><li> <?=mb_strtoupper($parObj->_getLabenames($arrayPayData,'userpaylink','name'),'UTF-8')?></li></a>
                
           		<? }?>
           
          </ul>
          <?php if(count($program)>0 && $workOrderHistory > 0) 
			   { echo "hhhi";
          ?>
          <h3 class="title2"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'myhistory','name'),'UTF-8')?></h3>
          <ul class="listing_03">
			    <a href="<?=ROOT_JWPATH?>historical.php?pgm_id=<?=base64_encode($program_id)?>&workoutFlexId=<?=trim($workoutFlexHistory)?>&ccess=Y29tc2Vzcw==">
                 <li><?=mb_strtoupper($parObj->_getLabenames($arrayData,'myhistorytext','name'),'UTF-8')?></li>
                </a>
          </ul>
           <?php 
			   }
			  ?> 
         </div>
        </section>
       </div>
<?php
include("userAreaPopup.php");
?><script>
	$(document).ready(function(){
		//navigate("","");
		});
</script>
<?php
include("footer.php"); 
?>
