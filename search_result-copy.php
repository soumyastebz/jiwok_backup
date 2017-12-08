<?php	//print_r($_REQUEST);exit;
	session_start();
	//session_destroy();
	include_once('includeconfig.php');
	include_once("includes/globals.php"); 	
	ini_set('display_errors',1);
	error_reporting(E_ERROR | E_PARSE);
	include_once("includes/classes/class.programs.php");
	include_once("includes/classes/class.sort.php");
    include_once("includes/classes/class.Search.php");
	if($lanId=="")

		 $lanId=1;

	 $objPgm     	= new Programs($lanId);

	$objGen     	= new General();

	$objTraining	= new Programs($lanId);

	$objSort     	= new Sort($lanId);

    $searchObj      = new Search();

	$parObj 		= new Contents();	
	
	$wizard_goals	= $searchObj->getWizardGoals($lanId);
	
	$wizard_levels	= $searchObj->getLevels($lanId);
	array_walk_recursive($wizard_levels, array($searchObj, '_utf8encode'));
	

    $wizard_rythms   = $searchObj->getWizardRythms($lanId);
	array_walk_recursive($wizard_rythms, array($searchObj, '_utf8encode'));
    // get all sports

	$wizard_sports   = $searchObj->getWizardSports($lanId);
	array_walk_recursive($wizard_rythms, array($searchObj, '_utf8encode'));	/* New code for SEO */			
	
			$returnData		= $parObj->_getTagcontents($xmlPath,'searchWizard','label');
			$arrayDataWiz	= $returnData['general'];			$returnDataPG		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
			$arrayDataPG		= $returnDataPG['general'];

			//collecting data from the xml for the static contents

			$returnDataList		= $parObj->_getTagcontents($xmlPath,'listprograms','label');
			$arrayDataList		= $returnDataList['general'];		



			$link_separator = "|";

			//if($lanId==1){$search_link="training";} else {$search_link="entrainement";}

			if($lanId==1){$search_link="training";} else {$search_link="entrainement";}

			$reg_exp	= '/^(.+)%2F$/';

			$category_url_name	= preg_replace($reg_exp, '${1}/', $_REQUEST['categoryName']);
			//echo $_REQUEST['categoryName'];exit;
			
?>

<?php 
//Create SEO friendly urls instead of search_result.php 
	include("search_redirect.php");//print_r($_SESSION);exit;
	
?>

<?php
			if($_SESSION['search_val'])
			{
				$_REQUEST['search']				=	$_SESSION['search_val'];
				$_REQUEST['user_goal']			=	$_SESSION['val_goal'];
				$_REQUEST['user_level']			=	$_SESSION['val_level'];
				$_REQUEST['user_sport']			=	$_SESSION['val_sport'];
				$_REQUEST['user_no_session']	=	$_SESSION['val_session'];
			   /*unset($_SESSION['search_val']);
				unset($_SESSION['val_goal']);
				unset($_SESSION['val_level']);
				unset($_SESSION['val_sport']);
				unset($_SESSION['val_session']);*/
			}
			

			
			if($_REQUEST['search']	=='aa')

				{

					$_REQUEST['search']	 = '';	
					
					if(base64_decode($_REQUEST['categoryName'],true))
					{
						//die('error');
						$_REQUEST['searchKeyButton']	=	'ok';				
						$_REQUEST['searchKey']			=	base64_decode($_REQUEST['categoryName']);
						$_REQUEST['categoryName']		= 	"";
					}

				}

			if($_REQUEST['search']!='') 
			{ 
				$_REQUEST['categoryName']='p'; 

				if($_REQUEST['user_goal']=='a') $_REQUEST['user_goal']='';

				if($_REQUEST['user_level']=='b') $_REQUEST['user_level']='';

				if($_REQUEST['user_sport']=='c') $_REQUEST['user_sport']='';

			} 

			else { }
			

			$thisPage=ROOT_JWPATH.$search_link.'/'.htmlentities($_REQUEST['categoryName'], ENT_COMPAT, 'UTF-8');
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

			

	/* End of new code */



	$goal=$_REQUEST['goal'];

	$sport=$_REQUEST['sport'];

	if($_REQUEST['langChange']!='' && $_REQUEST['categoryName']!=''){	

		$categoryName	= str_replace('%252F', '%2F', $_REQUEST['categoryName']);

		$categoryName	= str_replace('52F', '%2F', $categoryName);

		$categoryName	= urldecode($categoryName);

		$url	=	$objTraining->getCategoryListingUrlOnLangChange($categoryName, $_REQUEST['langChange']);

		header("location:$url");

		exit;

	}

	

	

	

	//collecting data from the xml for the static contents

	$parObj 		= new Contents('search_result.php');

	$returnData		= $parObj->_getTagcontents($xmlPath,'listprograms','label');

	$arrayData		= $returnData['general'];

	//collecting All training program category for displaying

	//$getAllTrainCats	= $objTraining->getCategories($lanId);
	//for displaying the search result
	
	

	if($_REQUEST['search']){ 

		$user_goal			=	$_REQUEST['user_goal'];

		$user_level			=	 $_REQUEST['user_level'];

		$user_sport		 	=    $_REQUEST['user_sport'];
		
		$user_no_session	=	(int) $_REQUEST['user_no_session'];

		if($goal==''){

		$goal=$user_goal;}

		else{$goal=$_REQUEST['user_goal'];}

		if($user_sport!="")

		{$sport=$user_sport;

		}else{$sport=$_REQUEST['user_sport'];}

		if(isset($_SESSION['user']['userId'])) {

            $userOptFCM=41;

            $userOptFCR=42;

            $userOptVMA=44;

            $fcm	=	(int) $objSort->_getUserOption($userId,$userOptFCM,$userOptFields); //"32";

            $fcr	=	(int) $objSort->_getUserOption($userId,$userOptFCR,$userOptFields); //"34";

            $vma	=	(int) $objSort->_getUserOption($userId,$userOptVMA,$userOptFields); //"34";

            $user_details	= $objSort->_userDetail($_SESSION['user']['userId']);

            $age	= $objSort->_getCurrentAge($user_details['user_dob']);

			$imc    = round($user_details['bmi'], 2);

            $gender = $user_details['user_gender'];

			unset($user_details);

        }

		$search_fields	= array (

			'user_goal'	=> $user_goal,

			'user_level'	=> $user_level,

			'user_rythm'	=> $user_no_session,

			'user_fcr'		=> $fcr, 

			'user_imc'		=> $imc,

			'user_fcm'		=> $fcm, 

			'user_age'	=> $age, 

			'user_gender'	=> $gender,

			'user_vma'	=> $vma

		);

		

		$program_csv_list=array();

if($lanId == 1) 

	{

		if($user_goal!=""){

			$program_csv_list	= $searchObj->getProgramsFromGoal($user_goal, $lanId);

			if (empty($program_csv_list)) {

				$flex_ids_all_arr    = $searchObj->getAllPrograms($lanId);

				$flex_ids_all_arr_mod    = $objGen->_changeArrayStruct($flex_ids_all_arr);

				$flex_ids_all_csv   = implode(',', $flex_ids_all_arr_mod['flex_id']);

				$program_csv_list   = $flex_ids_all_csv;

				unset($flex_ids_all_arr, $flex_ids_all_arr_mod, $flex_ids_all_csv);

			}

        }

		else{

            $flex_ids_all_arr    = $searchObj->getAllPrograms($lanId);

            $flex_ids_all_arr_mod    = $objGen->_changeArrayStruct($flex_ids_all_arr);

            $flex_ids_all_csv   = implode(',', $flex_ids_all_arr_mod['flex_id']);

            $program_csv_list   = $flex_ids_all_csv;

			unset($flex_ids_all_arr, $flex_ids_all_arr_mod, $flex_ids_all_csv);

		}

/////////for new field

	}

else

	{

if($user_sport!="" && $user_goal!="")

		{

					if($user_goal!="")

					{

						$program_csv_list	= $searchObj->getProgramsFromGoal($user_goal, $lanId);
						
						

						 if (empty($program_csv_list)) 

						{

							$flex_ids_all_arr    = $searchObj->getAllPrograms($lanId,$getBid);

							$flex_ids_all_arr_mod    = $objGen->_changeArrayStruct($flex_ids_all_arr);

							$flex_ids_all_csv   = implode(',', $flex_ids_all_arr_mod['flex_id']);

							$program_csv_list   = $flex_ids_all_csv;

							unset($flex_ids_all_arr, $flex_ids_all_arr_mod, $flex_ids_all_csv);

						}

					}

				/*else if($user_sport!=""){

				$program_csv_list.=$searchObj->getAllPgmsBySports($user_sport);}

				//if($user_goal == ""  && $user_sport == "")*/

			else

				{

					$flex_ids_all_arr    = $searchObj->getAllPrograms($lanId);

					$flex_ids_all_arr_mod    = $objGen->_changeArrayStruct($flex_ids_all_arr);

					$flex_ids_all_csv   = implode(',', $flex_ids_all_arr_mod['flex_id']);

					$program_csv_list   = $flex_ids_all_csv;

					unset($flex_ids_all_arr, $flex_ids_all_arr_mod, $flex_ids_all_csv);

				}	

			//	if(	$user_goal ==""			

				$program_csv_list .=$searchObj->getAllPgmsBySports($user_sport);

		}	

		else if($user_sport!="" && $user_goal =="")

		{
				
				$program_csv_list =$searchObj->getAllPgmsBySports($user_sport);

		}

		else

		{

				if($user_goal!="")

					{

						$program_csv_list	= $searchObj->getProgramsFromGoal($user_goal, $lanId);

						 if (empty($program_csv_list)) 

						{

							$flex_ids_all_arr    = $searchObj->getAllPrograms($lanId);

							$flex_ids_all_arr_mod    = $objGen->_changeArrayStruct($flex_ids_all_arr);

							$flex_ids_all_csv   = implode(',', $flex_ids_all_arr_mod['flex_id']);

							$program_csv_list   = $flex_ids_all_csv;

							unset($flex_ids_all_arr, $flex_ids_all_arr_mod, $flex_ids_all_csv);

						}

					}

				/*else if($user_sport!=""){

				$program_csv_list.=$searchObj->getAllPgmsBySports($user_sport);}

				//if($user_goal == ""  && $user_sport == "")*/

				else

				{

					$flex_ids_all_arr    = $searchObj->getAllPrograms($lanId);

					$flex_ids_all_arr_mod    = $objGen->_changeArrayStruct($flex_ids_all_arr);

					$flex_ids_all_csv   = implode(',', $flex_ids_all_arr_mod['flex_id']);

					$program_csv_list   = $flex_ids_all_csv;

					unset($flex_ids_all_arr, $flex_ids_all_arr_mod, $flex_ids_all_csv);

				}

		}

}

///////////////////

        

		$program_arr_list	= $searchObj->getProgramsListing($search_fields ,$program_csv_list, $lanId,'',$goal,$sport);
	
		foreach($program_arr_list as $program_arr_list_det){

			if($program_arr_list_det['program_master_id']!=""){	

			 	$sportListValues = $searchObj->GetSportLsitValues($program_arr_list_det['program_master_id']);

				$sportLstIds .=$sportListValues.",";

			}	

		}
		
		$sportLstIds=substr($sportLstIds,0,-1);

		$arrySport=explode(",",$sportLstIds);

		$val= array();

		if(count($arrySport)>0){

			for($i=0;$i<count($arrySport);$i++){

			//echo array_search($arrySport[$i],$val);

					if(in_array($arrySport[$i],$val)!== true)

					{

					 $sportIdList.="'".$arrySport[$i]."',";

					 $val[]=$arrySport[$i];

					 }

					 

			}
		}

	 	$sportIdList=substr($sportIdList,0,-1);
		foreach($program_arr_list as $program_arr_list_det2){

			if($program_arr_list_det2['flex_id']!=""){	

				$goalLists=$searchObj->GetGoalFromFlex($program_arr_list_det2['flex_id']);

				 $goalListsIDs .=$goalLists;

			}	

		}
		$goalListsIDsV=substr($goalListsIDs,0,-1);

		$arrygoal=explode(",",$goalListsIDsV);
		//$arrySport1=array_unique($arrySport);

		//print_r($$arrygoal);
		//$val2[]=$arrygoal[0];

		$val2= array();

		if(count($arrygoal)>0){

			for($i=1;$i<count($arrygoal);$i++){

			//echo array_search($arrySport[$i],$val);

					if(in_array($arrygoal[$i],$val2)!== true)

					{

					 $goalIdList.="'".$arrygoal[$i]."',";

					 $val2[]=$arrygoal[$i];

					 }					 

			}

		}

			//print_r($val2);

		$goalIdList=substr($goalIdList,0,-1);
		$prgm_flag=end($program_arr_list);

		$getPgmIds			=	$objGen->_changeArrayStruct($program_arr_list);
		
		//print_r($getPgmIds);

        //echo implode(',',$getPgmIds['flex_id']);

		unset($search_fields, $program_csv_list, $program_arr_list);

		

				

	}elseif(trim($_REQUEST['categoryName'])!=""){ 
	
		$_REQUEST['categoryName']	= str_replace('FF', '/', $_REQUEST['categoryName']);

	 	//print $_REQUEST['categoryName'];exit;
		//$_REQUEST['goal']	=	$_SESSION['val_goal'];	
		//$_REQUEST['sport']	=	$_SESSION['val_sport'];

		$sport=$_REQUEST['user_sport'];

		$goal=$_REQUEST['user_goal'];

	// for displaying sub-categories or programs of the categoryName

		$getAllTrainSubCats	= array();

		

		$cat_names				=	explode("-",$_REQUEST['categoryName']);

	 	$cat_flex_id			=  array_pop($cat_names);
		//echo "<br>c=".

		$cat_normal_url   		=	implode("-",$cat_names);
		
		$cat_normal_url			=	str_replace("--","[  ",$cat_normal_url);
		$cat_normal_url			=	str_replace("-"," ",$cat_normal_url);
		$cat_normal_url			=	str_replace("[ ","-",$cat_normal_url);

		//to find out the cat name 
	
		$cat_name1				=	$objTraining->findCatName($cat_flex_id,$lanId);
		
		$cat_name				=	$objTraining->makeCategoryTitle($cat_name1);			
		
		//echo "<br>ch=".

		$check_url				=	$objTraining->normal_url($cat_name);
		
		
		//echo $check_url."hhhh".$cat_normal_url;exit;
		if(strtolower($check_url)==strtolower($cat_normal_url)) 

		{		

				
					// issue with '/' even after encoding, So it is double encoded.

					$categoryName	= str_replace('%252F', '%2F', $cat_name1);

					$categoryName = str_replace('52F', '%2F', $categoryName);

					//$categoryName	= str_replace('52F', '%2F', $_REQUEST['categoryName']);

					$categoryName	= urldecode($categoryName);

					if(get_magic_quotes_gpc()){

						$categoryName	=	stripslashes($categoryName);

					}

//echo "<br>f=".$categoryName;

		}

		else

		{  
			
				//$categoryName	= str_replace('%252F', '%2F', $_REQUEST['categoryName']);
				$categoryName	= str_replace('%252F', '%2F',$cat_name1);

					$categoryName = str_replace('52F', '%2F', $categoryName);

					//$categoryName	= str_replace('52F', '%2F', $_REQUEST['categoryName']);

					$categoryName	= urldecode($categoryName);

					if(get_magic_quotes_gpc()){

						$categoryName	=	stripslashes($categoryName);

					}	

				//echo "<br>s=".$categoryName;

		}

        //echo $lanId;

			
		$categoryDetails	= $objTraining->getRowFromSubCategory($lanId, $categoryName,'','',$goal,$sport,$cat_flex_id);
		
		if(sizeof($categoryDetails)==0){
			redirectToSearchPage();

		}

		else

		{


			$subcatPageDetails=$objTraining->getRowFromSubCategoryPage($categoryDetails['flex_id'],$lanId,'',$goal,$sport);
		}

		

		

		//print_r($subcatPageDetails);

		//echo $subcatPageDetails['keywords'];

		if($categoryDetails['parent_id']==0){

			if($categoryDetails['flex_id'] == ''):

				$showall = 0;

			else:

				$showall = 1;

			endif;

			$getAllTrainSubCats	= $objTraining->getCategories($lanId, $categoryDetails['flex_id']);
			
		}
		
		//print_r($getAllTrainSubCats);

		//print_r($getAllTrainSubCats);

		if($categoryDetails['parent_id']!=0 || sizeof($getAllTrainSubCats)==0){

			//print $categoryDetails['flex_id'];

			// for displaying programs of the category if given categoryName is a parent category or there are no sub-categories for category
			$getAllByCate		=	$objTraining->_getAllPgmByCate($categoryDetails['flex_id'],$lanId,'',$goal,$sport);

			foreach($getAllByCate as $getAllByCate_det){

				if($getAllByCate_det['program_master_id']!=""){	

					$sportListValues = $searchObj->GetSportLsitValues($getAllByCate_det['program_master_id']);

					$sportLstIds .=$sportListValues.",";

				}	

			}

			$sportLstIds=substr($sportLstIds,0,-1);

			$arrySport=explode(",",$sportLstIds);

			$arrySport1=array_unique($arrySport);

			if(count($arrySport1)>0){

				for($i=0;$i<count($arrySport1);$i++){

						if($arrySport1[$i]!="")

						 $sportIdList.="'".$arrySport1[$i]."',";

				}

			}

			$sportIdList=substr($sportIdList,0,-1);
			$getPgmIds			=	$objGen->_changeArrayStruct($getAllByCate);	

		}

	

	}elseif($_REQUEST['searchKeyButton']!=""){

	// Display key search result

		$searchKeyWord		=	$_REQUEST['searchKey'];

		$cleanSearchKey  	=	$objGen->prepareSearchKeyword($searchKeyWord);

		$key				=	trim($cleanSearchKey);

		if($key	!= ''){	

		$getAllByKey		=	$objTraining->_getAllPgmByKey($key,$lanId,'',$goal,$sport);

		//print_r($getAllByKey);

				foreach($getAllByKey as $getAllByKey_det){

					if($getAllByKey_det['program_master_id']!=""){

					//print $getAllByKey_det['program_master_id'];	

						$sportListValues = $searchObj->GetSportLsitValues($getAllByKey_det['program_master_id']);

						$sportLstIds .=$sportListValues.",";

					}	

				}

				$sportLstIds=substr($sportLstIds,0,-1);

				$arrySport=explode(",",$sportLstIds);

				$arrySport1=array_unique($arrySport);

				if(count($arrySport1)>0){

					for($i=0;$i<count($arrySport1);$i++){

						if($arrySport1[$i]!="")

						 $sportIdList.="'".$arrySport1[$i]."',";

					}

				}

				$sportIdList=substr($sportIdList,0,-1);
		$getPgmIds			=	$objGen->_changeArrayStruct($getAllByKey);	

		}else{

		$getPgmIds			=	0;

		}

		if(strlen($_REQUEST['searchKey'])>7){

			$show_searchKey	=	substr($_REQUEST['searchKey'], 0, 6);

			$show_searchKey	=	htmlspecialchars($show_searchKey);

			$show_searchKey	.=	'...';

		}else{

			$show_searchKey	=	htmlspecialchars($_REQUEST['searchKey']);

		}

	}

	else

	{

		redirectToSearchPage();

	} 

	

	//display pgm detail form given ids

if(count($getPgmIds) > 0){ 

		/*if(isset($_SESSION['user']['userId'])){

			$userOptList	= 	$objSort->_getWizardIds($_SESSION['user']['userId']);

			if(count($userOptList) > 0){

				$listOptPgms	=	$objGen->_changeArrayStruct($userOptList);

				$sortPgms		=	$objSort->_getSortListPgms($getPgmIds['program_master_id'],$listOptPgms['pgmId']);

			}else{

			$sortPgms			=	$getPgmIds['program_master_id'];

			}

		 	$pgmMasterIds		=	implode(',',$sortPgms);	

		}else{

		$pgmMasterIds			=	implode(',',$getPgmIds['program_master_id']);

		}*/

		 $pgmMasterIds			=	implode(',',$getPgmIds['program_master_id']);

		$pgmMasterIds	=str_replace('167','',$pgmMasterIds); 

	}

	//echo $pgmMasterIds;	

	function redirectToSearchPage(){

		header('location:'.ROOT_FOLDER.'entrainement/');

		exit;

	}

	//print_r($categoryDetails);

if($goalIdList!=""){

//$wizard_goals	= $searchObj->getAllGoalSearch_list($lanId,$goalIdList);
$wizard_goals	= $searchObj->getWizardGoals($lanId);

}

else{	

$wizard_goals	= $searchObj->getWizardGoals($lanId);

}

$sport_list		= $searchObj->getAllSportSearch_list($lanId,$sportIdList);
//========================================================================================

$data = "";
 $data = "<ul id=\"workout\">";


if($categoryDetails['description']!="")
	{

		 $data.='<li>'.$categoryDetails['description'].'</li>';

	}
?>

<? if(count($getPgmIds) > 0){ if($prgm_flag==1){ 
 $data.='<li>'.$parObj->_getLabenames($arrayData,'SearchMessage','name').'</li>';
 } }?>
<?php

	

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

							

					$getTrainingGoal	=	$objPgm->_getOnePgmGoal($getTraining['flex_id'],$lanId);

				

					if($getTrainingGoal['item_name'] == ''):

						$altname = 'jiwok';

					else:

						$altname = addslashes($getTrainingGoal['item_name']);

					endif;				

					$altname = str_replace("\'","&quot;",$getTraining['program_title']);

					$altname = str_replace('\"','&quot;',$getTraining['program_title']);

				

					$target		=	$objGen->_output($getTraining['program_target']);

					$pgmFor = $objPgm->_getGroups(trim($getTraining['program_for']),$lanId,'group');

					$schedule = $objPgm->_getName1(trim($getTraining['schedule_type']),$lanId,'schedule_type');

					$pgmLevel 	 = $objPgm->_getName1(trim($getTraining['program_level_flex_id']),$lanId,'level');

					$cntTarget	=	strlen($target);

					if($cntTarget > 180)

						$target		=	$objGen->_outputLimit($getTraining['program_target'],0,180)." ...";

				

				//image disply

					if(!is_file($_SERVER["DOCUMENT_ROOT"].ROOT_FOLDER."uploads/programs/".$objGen->_output($getTraining['programImage']))){

						$imagePgm		=	ROOT_FOLDER."images/no_photo_pgm.jpg";
						$imgDimension 	= 	"";

					}else{

						$imagePgm		=	ROOT_FOLDER."uploads/programs/".$objGen->_output($getTraining['programImage']);
						
						list($width,$height) = getimagesize($_SERVER["DOCUMENT_ROOT"].$imagePgm);
						if($width > 120 && $height > 175)
						{
							$imgDimension = ' width="120" height="135"';
						}
						else	
							$imgDimension 	= 	"";
				}
				//HTML DATA TO BE DISPLAY ON PARENT PAGE

					if($pgm_status=='4')

				  		$buttonValue = $parObj->_getLabenames($arrayDataList,'discover','name');

					else

				  		$buttonValue = $parObj->_getLabenames($arrayDataList,'releasesoon','name'); 
				  
				  	$data.='<li><div class="image"><img class="brderwhite" src="'.$imagePgm.'" alt="'.htmlentities($altname).'" '.$imgDimension.' /></div><div class="description">';
				  
				  
				 	$pro_url 	= $objPgm->makeProgramTitleUrl($getTraining['program_title']);
					
				 	$normal_url	= strtolower($objPgm->normal_url($pro_url));
				  	if($pgm_status=='4')

					{
						$data.='<form method="post" action="'.ROOT_JWPATH.$normal_url.'-'.$getTraining['program_master_id'].'">';

					}
					
					$data.="<h2>";
					if($pgm_status=='4')

					{
					$data.='<a href="'.ROOT_JWPATH.$normal_url.'-'.$getTraining['program_master_id'].'">'.$objGen->_output($getTraining['program_title']).'</a>';

					}

					else

					{
						
						$data.=$objGen->_output($getTraining['program_title']);

					}
					$data.="</h2>
							<table width='100%' cellspacing='0' cellpadding='2' border='0'>
  							<tbody><tr>
    						<td>
							<span>".$parObj->_getLabenames($arrayDataPG,'duration','name').":</span>".$objGen->_output(trim($getTraining['program_schedule']))." ".$objGen->_output(trim($schedule));
	//if(strtolower($objGen->_output(trim($schedule)))!="mois" && ($objGen->_output(trim($getTraining['program_schedule']))>1)) { $data.='s';}
	if(($lanId	==	1)&& ($objGen->_output(trim($getTraining['program_schedule']))>1)) { $data.='s';} 
	$slash	=	"/";
	if($lanId	==	5) { $slash	=	' w ';}
	$data.="</td>
    		<td><span>".$parObj->_getLabenames($arrayDataPG,'rythm','name').":</span>".$objGen->_output(trim($getTraining['program_rythm']))." ".$parObj->_getLabenames($arrayDataPG,'times','name').$slash.$objGen->_output($schedule);
	
	$data.="</td> </tr> <tr> <td colspan='2'><span>".$parObj->_getLabenames($arrayDataPG,'level','name').": </span>".$objGen->_output(trim($pgmLevel))."</td>  </tr> <tr> <td colspan='2'><span>".$parObj->_getLabenames($arrayDataPG,'for','name').": </span>".$objGen->_output(trim($pgmFor))."</td></tr></tbody></table>
	<input type='submit' name='' value='".$buttonValue."' class='searchResultSubmit' />";
		if($pgm_status=='4')

				{

					$data.="</form>";

				}
		$data.="</div> <div class='clear'></div></li>";
		
		}

		}
		
		
		
		
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

		

		if($lanId==1) $endtag	=	"/";

		

		//Display '$opt_links_count' number of links
		/*$_REQUEST['user_goal']	=	$_SESSION['val_goal'];
		$_REQUEST['user_level']	=	$_SESSION['val_level'];
		$_REQUEST['search']		=	$_SESSION['search_val'];
		if($lanId	==	1)
				{
					$_REQUEST['user_no_session']	=	$_SESSION['val_session'];
				}
				else
				{
					$_REQUEST['user_sport']	=	$_SESSION['val_sport'];
				}*/
		
		while($count++ < $max)

		{

			if($_REQUEST['user_goal']=='') $_REQUEST['user_goal']='a';

			if($_REQUEST['user_level']=='') $_REQUEST['user_level']='b';

			if($_REQUEST['user_sport']=='') $_REQUEST['user_sport']='c';

			if($i == $pageNum)

			{

			  //$nav .= "<div class=\"pNo\">$i</div>";

			  $nav .= " ".$i." ".$link_separator;

			}

			else

			{

				if($_REQUEST['search']=="")

				 	$nav .= ' '.'<a  href="'.$thisPage.base64_encode($searchKeyWord).'/'.$i.'/aa/'.$_REQUEST['user_goal'].'/'.$_REQUEST['user_level'].'/'.$_REQUEST['user_sport'].$endtag.'">'.$i.'</a>'. $link_separator;
				else

				 	$nav .= ' '.'<a  class="blu"  href="'.$thisPage.'/'.$i.'/1/'.$_REQUEST['user_goal'].'/'.$_REQUEST['user_level'].'/'.$_REQUEST['user_sport'].$endtag.'">'.$i.'</a>'. $link_separator;
			}

			$i++;

			if($i > $maxPage) break; //If the current page exceeds the total number of pages, get out.

		}

		if ($pageNum > 1)

		{

			if($_REQUEST['user_goal']=='') $_REQUEST['user_goal']='a';

			if($_REQUEST['user_level']=='') $_REQUEST['user_level']='b';

			if($_REQUEST['user_sport']=='') $_REQUEST['user_sport']='c';

			$page = $pageNum - 1;

			if($_REQUEST['search']=="")
			{
				$href_prev	=	$thisPage.base64_encode($searchKeyWord).'/'.$page.'/aa/'.$_REQUEST['user_goal'].'/'.$_REQUEST['user_level'].'/'.$_REQUEST['user_sport'].$endtag;
				$prev = ' '.'<a  class="blu" href="'.$href_prev.'" ><strong>'.$parObj->_getLabenames($arrayDataPG,'prev','name').'</strong></a> ';
			}
			else
			{
				$href_prev	=	$thisPage.'/'.$page.'/1/'.$_REQUEST['user_goal'].'/'.$_REQUEST['user_level'].'/'.$_REQUEST['user_sport'].$endtag;
				$prev = ' '.'<a  class="blu" href="'.$href_prev.'"><strong>'.$parObj->_getLabenames($arrayDataPG,'prev','name').'</strong></a> ';
			}
			
			if($_REQUEST['search']=="")
			{
				$href_first	=	$thisPage.base64_encode($searchKeyWord).'/1/aa/'.$_REQUEST['user_goal'].'/'.$_REQUEST['user_level'].'/'.$_REQUEST['user_sport'].$endtag;
				$first = '<a  class="blu" href="'.$href_first.'" \><strong><<</strong></a>';
			}
			else
			{	
				$href_first	=	$thisPage.'/1/'.$_REQUEST['user_goal'].'/'.$_REQUEST['user_level'].'/'.$_REQUEST['user_sport'].
				$first = '<a  class="blu" href="'.$href_first.'"><strong><<</strong></a>';
			}
		}

		

		else

		{

		$prev = '<strong> </strong>';

		$first = '<strong> </strong>';

		}

		

		

		if ($pageNum < $maxPage)

		{

			if($_REQUEST['user_goal']=='') $_REQUEST['user_goal']='a';

			if($_REQUEST['user_level']=='') $_REQUEST['user_level']='b';

			if($_REQUEST['user_sport']=='') $_REQUEST['user_sport']='c';

		$page = $pageNum + 1;

		if($_REQUEST['search']=="" )
		{
			$href_next	= $thisPage.base64_encode($searchKeyWord).'/'.$page.'/aa/'.$_REQUEST['user_goal'].'/'.$_REQUEST['user_level'].'/'.$_REQUEST['user_sport'].$endtag;
				
			$next = '<a  class="blu" href="'.$href_next.'"><strong>'.$parObj->_getLabenames($arrayDataPG,'next','name').'</strong></a> ';
		}
		else
		{
			$href_next  = $thisPage.'/'.$page.'/1/'.$_REQUEST['user_goal'].'/'.$_REQUEST['user_level'].'/'.$_REQUEST['user_sport'].$endtag;
			$next  = '<a  class="blu" href="'.$href_next.'"><strong>'.$parObj->_getLabenames($arrayDataPG,'next','name').'</strong></a> ';
		}

		if($_REQUEST['search']=="")
		{
			$href_last  = $thisPage.base64_encode($searchKeyWord).'/'.$maxPage.'/aa/'.$_REQUEST['user_goal'].'/'.$_REQUEST['user_level'].'/'.$_REQUEST['user_sport'].$endtag;
			$last  = '<a  class="blu" href="'.$href_last.'"><strong>>></strong></a>';
		}
		else
		{
			$href_last  = $thisPage.'/'.$maxPage.'/1/'.$_REQUEST['user_goal'].'/'.$_REQUEST['user_level'].'/'.$_REQUEST['user_sport'].$endtag;
			$last = '<a  class="blu" href="'.$href_last.'"><strong>>></strong></a>';
		}

	}

		

		else

		{

		$next = '';

		$last = '';

		}

		$result = $data."<li><div class='paginationNew'>$first$prev$nav$next$last</div></li>";

		$result.="</ul>";
			
		
		
		}

	else if(count($getAllTrainSubCats)>0)

	{
		//DIJO
        //$data = $data."<ul id=\"workout\">";
		
		
        
		
		foreach($getAllTrainSubCats as $catData){
			
			$subCatUrl	=	"";
			
			if($catData['url']!=''){
	       		$subCatUrl	=	ROOT_JWPATH.$catData['url'];
			} else {
				//$url	= str_replace('%2F', '%252F', urlencode($getAllTrainSubCats[$i]['category_name']));
				$url = $objTraining->makeCategoryTitle($catData['category_name']);
				$url = $objTraining->normal_url($url);
				$url	= urlencode($url);
				//----for seo--------
				$url		=strtolower($url);
				$url		=str_replace("+","-",$url);
				$url		=	str_replace("--","[  ",$url);	
				$url		=	str_replace("[ ","-",$url);
				$url	= str_replace('%2F', 'FF', $url);// comment by anu
				//	$url	= str_replace('%2F', ' 52F', $url);
				$subCatUrl	=	ROOT_JWPATH.$search_link.'/'.$url."-".$catData['flex_id'];

			}
	
		
	        $tempDeta = $tempDeta."<li><div style=\"text-align:LEFT;\"><font style=\"letter-spacing:0px;color:#0B333C;font-size:14px;font-family:'Verdana';\"><a href=".$subCatUrl.">".$catData['category_name']."</a></font></div></li>";


		}
		$result =$data.$tempDeta;
        $result = $result."</ul>";
      


	}else{
		$result=$data."<li><span class='errorPrograms'>".$parObj->_getLabenames($arrayDataList,'noSearchResult','name')."</span></li></ul>";
	}

	
		
//=====================================================================================

?>

<?php include("header_aug20.php"); ?>
<script type="text/javascript">
$(document).ready(function(){

	window.jpopup = '';
	
	$('#mypop').click(function(){
		jpopup.close();
		});
	
	});

 function showhide(ctrl)
 	{	
		$("#"+ctrl).slideToggle();
	}

equalheight = function(container){

var currentTallest = 0,
     currentRowStart = 0,
     rowDivs = new Array(),
     $el,
     topPosition = 0;
 $(container).each(function() {

   $el = $(this);
   $($el).height('auto')
   topPostion = $el.position().top;

   if (currentRowStart != topPostion) {
     for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
       rowDivs[currentDiv].height(currentTallest);
     }
     rowDivs.length = 0; // empty the array
     currentRowStart = topPostion;
     currentTallest = $el.height();
     rowDivs.push($el);
   } else {
     rowDivs.push($el);
     currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
  }
   for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
     rowDivs[currentDiv].height(currentTallest);
   }
 });
}

$(window).load(function() {
	
  equalheight('.JW_ents .colums');
});
$(window).resize(function(){
  equalheight('.JW_ents .colums');
});

function validateSearch()
{
	var goal			=	document.getElementById('user_goal').value;
	var level			=	document.getElementById('user_level').value;
	var lang			=	document.getElementById('langfield').value;
	var cont_goal		=	document.getElementById('cont_goal').value;
	var cont_level		=	document.getElementById('cont_level').value;
	
	if(lang	==	1)
	{
		var cont_session	=	document.getElementById('cont_session').value;
		var session	=	document.getElementById('user_no_session').value;
	}
	else
	{
		var cont_sport		=	document.getElementById('cont_sport').value;
		var sport	=	document.getElementById('user_sport').value;
	}
	if(goal	==	""	&&	level	==	""	&&	sport	==	"")
	{
		//var $j = jQuery.noConflict();
		
		document.getElementById('alertMsgSearch').innerHTML="<?=$parObj->_getLabenames($arrayDataWiz,'search_empty','name');?>";
			
             jpopup =  $('.pop').bPopup({
	        easing: 'easeOutBounce', 
            speed: 2000,
            transition: 'slideDown'
        });
            
		return false;
	}
	else if(goal	==	""	&&	level	==	""	&&	session	==	"")
	{
		document.getElementById('alertMsgSearch').innerHTML="<?=$parObj->_getLabenames($arrayDataWiz,'search_empty','name');?>";
		
              jpopup = $('.pop').bPopup({
	        easing: 'easeOutBounce', 
            speed: 2000,
            transition: 'slideDown'
        });
            
		return false;
	}
}
function assignchoice()
{
	
			var lang	=	document.getElementById('langfield').value;
			if(document.getElementById('user_goal').value != ""){
			var text_goal			=	document.getElementById('user_goal').options[document.getElementById('user_goal').selectedIndex].text;
			text_goal				=	text_goal.split(' ').join('-');
			$('#cont_goal').attr("value",text_goal);
		}
		else{
			var text_goal			=	"";
			$('#cont_goal').attr("value",text_goal);
		}
		if(document.getElementById('user_level').value != ""){
			var text_level			=	document.getElementById('user_level').options[document.getElementById('user_level').selectedIndex].text;
			text_level				=	text_level.split(' ').join('-');
			$('#cont_level').attr("value",text_level);
		}
		else{
			var text_level			=	"";
			$('#cont_level').attr("value",text_level);
		}
		
		if(lang	==	1){
			if(document.getElementById('user_no_session').value != ""){
				var text_session	=	document.getElementById('user_no_session').options[document.getElementById('user_no_session').selectedIndex].text;
				$('#cont_session').attr("value",text_session);
			}
			else{
				var text_session	=	"";
				text_session		=	text_session.split(' ').join('-');
				$('#cont_session').attr("value",text_session);
			}
		}
		else{
			if(document.getElementById('user_sport').value != ""){
				var text_sport		=	document.getElementById('user_sport').options[document.getElementById('user_sport').selectedIndex].text;
				//alert(text_sport);exit;
				$('#cont_sport').attr("value",text_sport);
			}
			else{
				var text_sport		=	"";
				text_sport			=	text_sport.split(' ').join('-');
				$('#cont_sport').attr("value",text_sport);
			}
		}
}


    var d = document;
    var safari = (navigator.userAgent.toLowerCase().indexOf('safari') != -1) ? true : false;
    var gebtn = function(parEl,child) { return parEl.getElementsByTagName(child); };
    onload = function() {
        
        var body = gebtn(d,'body')[0];
        body.className = body.className && body.className != '' ? body.className + ' has-js' : 'has-js';
        
        if (!d.getElementById || !d.createTextNode) return;
        var ls = gebtn(d,'label');
        for (var i = 0; i < ls.length; i++) {
            var l = ls[i];
            if (l.className.indexOf('label_') == -1) continue;
            var inp = gebtn(l,'input')[0];
            if (l.className == 'label_check') {
                l.className = (safari && inp.checked == true || inp.checked) ? 'label_check c_on' : 'label_check c_off';
                l.onclick = check_it;
            };
            if (l.className == 'label_radio') {
                l.className = (safari && inp.checked == true || inp.checked) ? 'label_radio r_on' : 'label_radio r_off';
                l.onclick = turn_radio;
            };
        };
    };
    var check_it = function() {
        var inp = gebtn(this,'input')[0];
        if (this.className == 'label_check c_off' || (!safari && inp.checked)) {
            this.className = 'label_check c_on';
            if (safari) inp.click();
        } else {
            this.className = 'label_check c_off';
            if (safari) inp.click();
        };
    };
    var turn_radio = function() {
        var inp = gebtn(this,'input')[0];
        if (this.className == 'label_radio r_off' || inp.checked) {
            var ls = gebtn(this.parentNode,'label');
            for (var i = 0; i < ls.length; i++) {
                var l = ls[i];
                if (l.className.indexOf('label_radio') == -1)  continue;
                l.className = 'label_radio r_off';
            };
            this.className = 'label_radio r_on';
            if (safari) inp.click();
        } else {
            this.className = 'label_radio r_off';
            if (safari) inp.click();
        };
    };
	
	
   
</script>
<style>
.searchResultSubmit {
	cursor:pointer;
}
</style>


 <?php 

	if(isset($_REQUEST['searchKey']) || $_REQUEST['search'] != ""):

	?>

  <?php $titlecat = $parObj->_getLabenames($arrayData,'mainhead','name')." :";?>

  <?php if(isset($_REQUEST['searchKey'])) { $titlecat .= $show_searchKey; } ?>

  <?php

	elseif($categoryName != ""):

		if($categoryName == "10 km"):

				$titlecat= $parObj->_getLabenames($arrayDataList,'10km','name');

		elseif($categoryName == "20 km"):

				$titlecat= $parObj->_getLabenames($arrayDataList,'20km','name');

		elseif($categoryName == "6 km"):

				$titlecat= $parObj->_getLabenames($arrayDataList,'6km','name');

		else:

				$titlecat= $categoryName;

		endif;

	else:
	$ttlSrch	=	$parObj->_getLabenames($arrayData,'serchDftTxt','name')." :";
	$titlecat = $parObj->_getLabenames($arrayData,'mainhead','name')." :";
    if(isset($_REQUEST['searchKey'])) { $titlecat .=$show_searchKey; } 
	endif;

	?>
    <section class="banner-static">
           <div class="bnr-image"><img src="images/banner_02.jpg" alt="jiwok"></div>
           <div class="bnr-content">
                <div class="inner">
                  <div class="heading3"><p>à chacun sa façon de trouver un entraînement</p></div> 
                  <div class="line2"><p>&nbsp;</p></div>
                  
                    
                </div>
           </div>
       
       
       </section>
     
     <section class="JW_ents">
             <div class="colums">
                <figure>
                  <img src="images/corner-4.png" alt="jiwok" class="corner">
                  <img src="images/dummy-2.jpg" alt="Jiwok">
                </figure>
                <article>
                  <h3>TRIATHLON</h3>
                  <p>Préparer un triathlon 
de format sprint ou courte 
distance, 3 fois par semaine 
pendant 8 semaines.</p>

     <div class="listing">
       <div class="left"><span>Durée</span></div>
       <div class="right">8 semaine</div>
     </div>
     
     <div class="listing">
       <div class="left"><span>Rythme</span></div>
       <div class="right">3 fois/semaine</div>
     </div>
     
     <div class="listing">
       <div class="left"><span>Niveau</span></div>
       <div class="right">Pratique une activité sportive régulière</div>
     </div>
         <div class="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
         </div>   
         <!--<input type="button" class="btn" value="DÉCOUVRIR L'ENTRAÎNEMENT">-->
         <a href="#" class="btn">DÉCOUVRIR L'ENTRAÎNEMENT</a>    
                </article>
                
             </div>
             <div class="colums">
                <figure>
                  <img src="images/corner-4.png" alt="jiwok" class="corner">
                  <img src="images/dummy-2.jpg" alt="Jiwok">
                </figure>
                <article>
                  <h3>TRIATHLON</h3>
                  <p>Préparer un triathlon 
de format sprint ou courte 
distance, 3 fois par semaine 
pendant 8 semaines.</p>

     <div class="listing">
       <div class="left"><span>Durée</span></div>
       <div class="right">8 semaine</div>
     </div>
     
     <div class="listing">
       <div class="left"><span>Rythme</span></div>
       <div class="right">3 fois/semaine</div>
     </div>
     
     <div class="listing">
       <div class="left"><span>Niveau</span></div>
       <div class="right">Pratique une activité sportive régulière</div>
     </div>
         <div class="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
         </div>   
         <!--<input type="button" class="btn" value="DÉCOUVRIR L'ENTRAÎNEMENT">-->
         <a href="#" class="btn">DÉCOUVRIR L'ENTRAÎNEMENT</a>    
                </article>
                
             </div>
             
              <div class="colums">
                <figure>
                  <img src="images/corner-4.png" alt="jiwok" class="corner">
                  <img src="images/dummy-2.jpg" alt="Jiwok">
                </figure>
                <article>
                  <h3>TRIATHLON</h3>
                  <p>Préparer un triathlon 
de format sprint ou courte 
distance, 3 fois par semaine 
pendant 8 semaines.</p>

     <div class="listing">
       <div class="left"><span>Durée</span></div>
       <div class="right">8 semaine</div>
     </div>
     
     <div class="listing">
       <div class="left"><span>Rythme</span></div>
       <div class="right">3 fois/semaine</div>
     </div>
     
     <div class="listing">
       <div class="left"><span>Niveau</span></div>
       <div class="right">Pratique une activité sportive régulière</div>
     </div>
         <div class="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
         </div>   
         <!--<input type="button" class="btn" value="DÉCOUVRIR L'ENTRAÎNEMENT">-->
         <a href="#" class="btn">DÉCOUVRIR L'ENTRAÎNEMENT</a>    
                </article>
                
             </div>
             <div class="colums">
                <figure>
                  <img src="images/corner-4.png" alt="jiwok" class="corner">
                  <img src="images/dummy-2.jpg" alt="Jiwok">
                </figure>
                <article>
                  <h3>TRIATHLON</h3>
                  <p>Préparer un triathlon 
de format sprint ou courte 
distance, 3 fois par semaine 
pendant 8 semaines.</p>

     <div class="listing">
       <div class="left"><span>Durée</span></div>
       <div class="right">8 semaine</div>
     </div>
     
     <div class="listing">
       <div class="left"><span>Rythme</span></div>
       <div class="right">3 fois/semaine</div>
     </div>
     
     <div class="listing">
       <div class="left"><span>Niveau</span></div>
       <div class="right">Pratique une activité sportive régulière</div>
     </div>
         <div class="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
         </div>   
         <!--<input type="button" class="btn" value="DÉCOUVRIR L'ENTRAÎNEMENT">-->
         <a href="#" class="btn">DÉCOUVRIR L'ENTRAÎNEMENT</a>    
                </article>
                
             </div>
             
              <div class="colums">
                <figure>
                  <img src="images/corner-4.png" alt="jiwok" class="corner">
                  <img src="images/dummy-2.jpg" alt="Jiwok">
                </figure>
                <article>
                  <h3>TRIATHLON</h3>
                  <p>Préparer un triathlon 
de format sprint ou courte 
distance, 3 fois par semaine 
pendant 8 semaines.</p>

     <div class="listing">
       <div class="left"><span>Durée</span></div>
       <div class="right">8 semaine</div>
     </div>
     
     <div class="listing">
       <div class="left"><span>Rythme</span></div>
       <div class="right">3 fois/semaine</div>
     </div>
     
     <div class="listing">
       <div class="left"><span>Niveau</span></div>
       <div class="right">Pratique une activité sportive régulière</div>
     </div>
         <div class="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
         </div>   
         <!--<input type="button" class="btn" value="DÉCOUVRIR L'ENTRAÎNEMENT">-->
         <a href="#" class="btn">DÉCOUVRIR L'ENTRAÎNEMENT</a>    
                </article>
                
             </div>
        <div class="colums">
                <figure>
                  <img src="images/corner-4.png" alt="jiwok" class="corner">
                  <img src="images/dummy-2.jpg" alt="Jiwok">
                </figure>
                <article>
                  <h3>TRIATHLON</h3>
                  <p>Préparer un triathlon 
de format sprint ou courte 
distance, 3 fois par semaine 
pendant 8 semaines.</p>

     <div class="listing">
       <div class="left"><span>Durée</span></div>
       <div class="right">8 semaine</div>
     </div>
     
     <div class="listing">
       <div class="left"><span>Rythme</span></div>
       <div class="right">3 fois/semaine</div>
     </div>
     
     <div class="listing">
       <div class="left"><span>Niveau</span></div>
       <div class="right">Pratique une activité sportive régulière</div>
     </div>
         <div class="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
           <img src="images/rating-star_small.png" alt="rating">
         </div>   
         <!--<input type="button" class="btn" value="DÉCOUVRIR L'ENTRAÎNEMENT">-->
         <a href="#" class="btn">DÉCOUVRIR L'ENTRAÎNEMENT</a>    
                </article>
                
             </div>
             
            
     </section>
     <section class="JW_search_new">
          
                      <div class="center">
                      <h2>une nouvelle recherche?</h3>
                          <div class="colums">
                             <p>QUEL EST VOTRE OBJECTIF?</p>
                             <div class="selet3">
                                     <select>
                                        
                                        <option value="-11">CHOISISSEZ S'IL VOUS PLAIT</option>
                    
                    
                                        <option value="-10">GMT -10:00 USA (Hawaï)</option>
                    
                    
                                        <option value="-9.5">GMT-09:30 Îles Marquises</option>
                    
                    
                                        <option value="-9">GMT -09:00 USA (Alaska)</option>
                    
                    
                                        <option value="-8">GMT -08:00 Amérique du Nord (Pacifique)</option>
                    
                    
                                        <option value="-7">GMT -07:00 USA (Mountain/Montagnes Rocheuses)</option>
                    
                    
                                        <option value="-6">GMT -06:00 USA (Central)</option>
                    
                    
                                        <option value="-5">GMT -05:00 USA (Eastern/Est)</option>
                    
                    
                                        <option value="-4">GMT -04:00 Atlantique</option>
                    
                    
                                        <option value="-3.5">GMT -03:30 Canada (Terre-Neuve)</option>
                    
                    
                                        <option value="-3">GMT -03:00 Argentine</option>
                    
                    
                                        <option value="-2">GMT -02:00 Centre-Atlantique</option>
                    
                    
                                        <option value="-1">GMT -01:00 Açores</option>
                    
                    
                                        <option value="0">GMT Royaume-Uni, Espagne</option>
                    
                    
                                        <option value="1">GMT+01:00  France ,Europe de l'Ouest</option>
                    
                    
                                        <option value="2">GMT+02:00 Europe de l'Est</option>
                    
                    
                                        <option value="3">GMT +03:00 Russie</option>
                    
                    
                                        <option value="3.5">GMT +03:30 Iran</option>
                    
                    
                                        <option value="4">GMT+04:00 Arabie</option>
                    
                    
                                        <option value="4.5">GMT +04:30 Afghanistan</option>
                    
                    
                                        <option value="5">GMT+05:00 Pakistan, Asie occidentale</option>
                    
                    
                                        <option value="5.5" selected="">GMT +05:30 Inde</option>
                    
                    
                                        <option value="6">GMT+6:00 Bangladesh, Asie centrale</option>
                    
                    
                                        <option value="6.5">GMT +06:30 Birmanie</option>
                    
                    
                                        <option value="7">GMT +07:00 Bangkok, Hanoi, Djakarta</option>
                    
                    
                                        <option value="8">GMT +08:00 Chine, Taiwan</option>
                    
                    
                                        <option value="9">GMT +09:00 Japon</option>
                    
                    
                                        <option value="9.5">GMT +09:30 Australie (heure centrale)</option>
                    
                    
                                        <option value="10">GMT+10:00 Australie (heure de l'Est)</option>
                    
                    
                                        <option value="10.5">GMT+10:30 Australie (île de Lord Howe)</option>
                    
                    
                                        <option value="11">GMT +11:00 Pacifique central</option>
                    
                    
                                        <option value="11.5">GMT+11:30 Îles Norfolk</option>
                    
                    
                                        <option value="12">GMT+12:00 Fidji, Nouvelle-Zélande</option>
                                      </select>
                             </div>
                          </div>
                          
                          <div class="colums">
                             <p>QUEL EST VOTRE NIVEAU?</p>
                             <div class="selet3">
                                     <select>
                                        <option value="-12">CHOISISSEZ S'IL VOUS PLAIT</option>
                                        <option value="-11">GMT -11 Samoa</option>
                    
                    
                                        <option value="-10">GMT -10:00 USA (Hawaï)</option>
                    
                    
                                        <option value="-9.5">GMT-09:30 Îles Marquises</option>
                    
                    
                                        <option value="-9">GMT -09:00 USA (Alaska)</option>
                    
                    
                                        <option value="-8">GMT -08:00 Amérique du Nord (Pacifique)</option>
                    
                    
                                        <option value="-7">GMT -07:00 USA (Mountain/Montagnes Rocheuses)</option>
                    
                    
                                        <option value="-6">GMT -06:00 USA (Central)</option>
                    
                    
                                        <option value="-5">GMT -05:00 USA (Eastern/Est)</option>
                    
                    
                                        <option value="-4">GMT -04:00 Atlantique</option>
                    
                    
                                        <option value="-3.5">GMT -03:30 Canada (Terre-Neuve)</option>
                    
                    
                                        <option value="-3">GMT -03:00 Argentine</option>
                    
                    
                                        <option value="-2">GMT -02:00 Centre-Atlantique</option>
                    
                    
                                        <option value="-1">GMT -01:00 Açores</option>
                    
                    
                                        <option value="0">GMT Royaume-Uni, Espagne</option>
                    
                    
                                        <option value="1">GMT+01:00  France ,Europe de l'Ouest</option>
                    
                    
                                        <option value="2">GMT+02:00 Europe de l'Est</option>
                    
                    
                                        <option value="3">GMT +03:00 Russie</option>
                    
                    
                                        <option value="3.5">GMT +03:30 Iran</option>
                    
                    
                                        <option value="4">GMT+04:00 Arabie</option>
                    
                    
                                        <option value="4.5">GMT +04:30 Afghanistan</option>
                    
                    
                                        <option value="5">GMT+05:00 Pakistan, Asie occidentale</option>
                    
                    
                                        <option value="5.5" selected="">GMT +05:30 Inde</option>
                    
                    
                                        <option value="6">GMT+6:00 Bangladesh, Asie centrale</option>
                    
                    
                                        <option value="6.5">GMT +06:30 Birmanie</option>
                    
                    
                                        <option value="7">GMT +07:00 Bangkok, Hanoi, Djakarta</option>
                    
                    
                                        <option value="8">GMT +08:00 Chine, Taiwan</option>
                    
                    
                                        <option value="9">GMT +09:00 Japon</option>
                    
                    
                                        <option value="9.5">GMT +09:30 Australie (heure centrale)</option>
                    
                    
                                        <option value="10">GMT+10:00 Australie (heure de l'Est)</option>
                    
                    
                                        <option value="10.5">GMT+10:30 Australie (île de Lord Howe)</option>
                    
                    
                                        <option value="11">GMT +11:00 Pacifique central</option>
                    
                    
                                        <option value="11.5">GMT+11:30 Îles Norfolk</option>
                    
                    
                                        <option value="12">GMT+12:00 Fidji, Nouvelle-Zélande</option>
                                      </select>
                             </div>
                          </div>
                          
                          <div class="colums">
                             <p>QUEL EST VOTRE CHOIX DE PRATIQUE ?</p>
                             <div class="selet3">
                                     <select>
                                        <option value="-12">CHOISISSEZ S'IL VOUS PLAIT</option>
                                        <option value="-11">GMT -11 Samoa</option>
                    
                    
                                        <option value="-10">GMT -10:00 USA (Hawaï)</option>
                    
                    
                                        <option value="-9.5">GMT-09:30 Îles Marquises</option>
                    
                    
                                        <option value="-9">GMT -09:00 USA (Alaska)</option>
                    
                    
                                        <option value="-8">GMT -08:00 Amérique du Nord (Pacifique)</option>
                    
                    
                                        <option value="-7">GMT -07:00 USA (Mountain/Montagnes Rocheuses)</option>
                    
                    
                                        <option value="-6">GMT -06:00 USA (Central)</option>
                    
                    
                                        <option value="-5">GMT -05:00 USA (Eastern/Est)</option>
                    
                    
                                        <option value="-4">GMT -04:00 Atlantique</option>
                    
                    
                                        <option value="-3.5">GMT -03:30 Canada (Terre-Neuve)</option>
                    
                    
                                        <option value="-3">GMT -03:00 Argentine</option>
                    
                    
                                        <option value="-2">GMT -02:00 Centre-Atlantique</option>
                    
                    
                                        <option value="-1">GMT -01:00 Açores</option>
                    
                    
                                        <option value="0">GMT Royaume-Uni, Espagne</option>
                    
                    
                                        <option value="1">GMT+01:00  France ,Europe de l'Ouest</option>
                    
                    
                                        <option value="2">GMT+02:00 Europe de l'Est</option>
                    
                    
                                        <option value="3">GMT +03:00 Russie</option>
                    
                    
                                        <option value="3.5">GMT +03:30 Iran</option>
                    
                    
                                        <option value="4">GMT+04:00 Arabie</option>
                    
                    
                                        <option value="4.5">GMT +04:30 Afghanistan</option>
                    
                    
                                        <option value="5">GMT+05:00 Pakistan, Asie occidentale</option>
                    
                    
                                        <option value="5.5" selected="">GMT +05:30 Inde</option>
                    
                    
                                        <option value="6">GMT+6:00 Bangladesh, Asie centrale</option>
                    
                    
                                        <option value="6.5">GMT +06:30 Birmanie</option>
                    
                    
                                        <option value="7">GMT +07:00 Bangkok, Hanoi, Djakarta</option>
                    
                    
                                        <option value="8">GMT +08:00 Chine, Taiwan</option>
                    
                    
                                        <option value="9">GMT +09:00 Japon</option>
                    
                    
                                        <option value="9.5">GMT +09:30 Australie (heure centrale)</option>
                    
                    
                                        <option value="10">GMT+10:00 Australie (heure de l'Est)</option>
                    
                    
                                        <option value="10.5">GMT+10:30 Australie (île de Lord Howe)</option>
                    
                    
                                        <option value="11">GMT +11:00 Pacifique central</option>
                    
                    
                                        <option value="11.5">GMT+11:30 Îles Norfolk</option>
                    
                    
                                        <option value="12">GMT+12:00 Fidji, Nouvelle-Zélande</option>
                                      </select>
                             </div>
                          </div>
                     <div class="clear"></div>
                  <div class="validate"><input type="button" value="VALIDER VOTRE NOUVELLE RECHERCHE"></div>
      </div>
     </section>
<?php include("footer.php"); exit;?>
<!--======================new end====================================================-->
<div id="container">
<div id="wraper_inner">
<div class="breadcrumbs">
  <ul>
    <li>
      <?=$parObj->_getLabenames($arrayData,'searchPath','name');?>
    </li>
    <li><a href="<?=ROOT_JWPATH?>index.php">
      <?=$parObj->_getLabenames($arrayData,'homeName','name');?>
      </a></li>
    <li>></li>
    <li><a href="<?=ROOT_JWPATH?><?php echo ($lanId != 1) ? 'entrainement' :'training';?>" class="">
      <?=$parObj->_getLabenames($arrayData,'searchName','name');?>
      </a></li>
     <li>></li>
    <li><a href="#" class="select">
    	<?php
			if($ttlSrch!=""){ 
				echo $ttlSrch;
			}else{
				echo ucfirst($titlecat);
			}
		?>
      </a>
     </li>
     
  </ul>
</div>
<div class="heading">

 <span class="name">
  <?= $titlecat;?>
 </span>

</div>
<div class="col-2">

<form name="sport_search" method="post" >
  <input type="hidden" value="<?=$_REQUEST['user_goal']?>"  name="user_goal"/>
  <input type="hidden" value="<?=$_REQUEST['user_level']?>"  name="user_level"/>
  <input type="hidden" value="<?=$_REQUEST['user_no_session']?>"  name="user_no_session"/>
  <input type="hidden" value="<?=$_REQUEST['langChange']?>"  name="langChange"/>
  <input type="hidden" value="<?=$_REQUEST['categoryName']?>"  name="categoryName"/>
  <input type="hidden" value="<?=$_REQUEST['search']?>"  name="search"/>
  <input type="hidden" value="<?=$_REQUEST['searchKeyButton']?>"  name="searchKeyButton"/>
  <input type="hidden" value="<?=$_REQUEST['searchKey']?>"  name="searchKey"/>
  <input type="hidden" value="<?=$_REQUEST['searchKey']?>"  name="searchKey"/>
</form>
<?php /*-----------------------------------------------------------------------*/	
echo $result;	  
	?>
<div class="left">
  <div class="heading">
    <?=$parObj->_getLabenames($arrayData,'newSearchNote','name');?>
    <div class="corner-lft"></div>
    <div class="corner-rit"></div>
  </div>
   <!--for search error popup -->             

		<div class="popup" id="searchAlertMsg" style="display:none;position:fixed;z-index:100000;">
  <div><img src="images/pop-top.png" alt="jiwok" /></div>
  <div class="inner">
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center" style="text-align:left"><div id="alertMsgSearch"></div><br/><br/></td>
      </tr>
      <tr>
        <td align="center"><a id="okIdsearchAlert"><input class="bu_03"  name="renewSubscriptionIdBtn" type="button" value="Ok" /></a></td>
      </tr>
    </table>
    <div class="clear"></div>
  </div>
  <div><img src="images/pop-btm.png" alt="jiwok" /></div>
</div>



<!--         for search error popup                -->
  <div class="survey">
    <form name="searchWizard" method="get" id="searchWizard" action="<?=ROOT_JWPATH?>entrainement/">
      <div class="corner"></div>
      <p>
        <?=$parObj->_getLabenames($arrayData,'searchResultOpt1','name');?>
      </p>
      
      <select name="user_goal" class="list-box-2" onChange="return assignchoice();"  id="user_goal">
        <option value="" selected="selected">
        <?=$parObj->_getLabenames($arrayData,'select','name');?>
        </option>
       
       
        
        <?php foreach($wizard_goals as $wizard_goal){ ?>
        <?php if($lanId == 1) {

				if($wizard_goal['flex_id'] != 'gol11' && $wizard_goal['flex_id'] != 'gol10')

				{ ?>
        <option value="<? echo $wizard_goal['flex_id'];?>" ><? echo $wizard_goal['item_name'];?></option>
        <?php } } 	else {?>
        <option value="<? echo $wizard_goal['flex_id'];?>" ><? echo $wizard_goal['item_name'];?></option>
        <? }?>
        <? }?>
      </select>
       <input type="hidden" name="cont_goal" id="cont_goal" value=""/>
      <hr class="ylw" />
      <p>
        <?=$parObj->_getLabenames($arrayData,'searchResultOpt2','name');?>
      </p>
      <select name="user_level" class="list-box-2" onChange="return assignchoice();" id="user_level">
        <option value="" selected="selected">
        <?=$parObj->_getLabenames($arrayData,'select','name');?>
        </option>
        <?php

foreach($wizard_levels as $wizard_level_id=>$wizard_level_value){

$wizard_level_item_name=$parObj->_getLabenames($arrayDataWiz,"jiwok_level".$wizard_level_id,'name');

if($wizard_level_item_name==""){

	$wizard_level_item_name=htmlentities(utf8_decode($wizard_level['item_name']));}

?>
        <option value=<?=$wizard_level_id ?>>
        <?=$wizard_level_item_name?>
        </option>
        <?php } ?>
      </select>
      <input type="hidden" name="cont_level" id="cont_level" value=""/>
      <hr class="ylw" />
      <?php if($lanId == 1) { ?>
      <p>
        <?=$parObj->_getLabenames($arrayData,'searchResultOpt3','name');?>
      </p>
      <select name="user_no_session" class="list-box-2" onChange="return assignchoice();" id="user_no_session">
        <option value="" selected="selected">
        <?=$parObj->_getLabenames($arrayData,'select','name');?>
        </option>
        <?php foreach($wizard_rythms as $rythm_id => $rythm_value){ ?>
        <option value="<?php echo $rythm_id; ?>" ><?php echo $rythm_value; ?></option>
        <? }?>
      </select>
      <input type="hidden" name="cont_session" id="cont_session" value=""/>
      <?php }else{ ?>
      <p>
        <?=$parObj->_getLabenames($arrayData,'searchResultOpt4','name');?>
      </p>
      <select name="user_sport" class="list-box-2" onChange="return assignchoice();" id="user_sport">
        <option value="" selected="selected">
        <?=$parObj->_getLabenames($arrayData,'select','name');?>
        </option>
        <?php foreach($wizard_sports as $sports_row){ ?>
        <!------------------Temporary adjustment for polish sports hiding----------------->
        <?php 
		 if($lanId ==5)
		 {
			
		 if(!(($sports_row['flex_id']==16) || ($sports_row['flex_id']==6) || ($sports_row['flex_id']==13)))
		 {?>
        <option value="<?php echo $sports_row['flex_id']; ?>" ><?php echo $sports_row['item_name']; ?></option>
        <? }
		 }
		
		else {?>
          <!----------------------------------------------------------------------->
        <option value="<?php echo $sports_row['flex_id']; ?>" ><?php echo $sports_row['item_name']; ?></option>
        <? }}?>
      </select>
      <input type="hidden" name="cont_sport" id="cont_sport" value=""/>
      <?php } ?>
      <hr class="ylw" />
      <p>
        <input type="hidden" name="langfield" id="langfield" value="<?= $lanId ;?>"/>
        <input type="submit" class="searchSubmit" name="search" value="<?=$parObj->_getLabenames($arrayData,'searchResultBtnTxt','name');?>" onClick="return validateSearch();"  />
      </p>
    </form>
    <div class="clear"></div>
  </div>
  <div class="shadow"><img src="<?=ROOT_FOLDER?>images/shade-btm2.png" alt="Jiwok" /></div>
  <?php /* include("search_right_bottom.php"); */ ?>
</div>
<div class="clear"></div>
</div>
</div>
</div>
<?php include("footer.php"); ?>