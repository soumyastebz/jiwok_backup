<?php 
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
	
	?>
	
<!--for search error popup-->
<?php 

	if(isset($_REQUEST['searchKey']) || $_REQUEST['search'] != ""):

	 $titlecat = $parObj->_getLabenames($arrayData,'mainhead','name')." :";
	  if(isset($_REQUEST['searchKey'])) { $titlecat .= $show_searchKey; } ?>

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
	
	$ttlSrch	= $parObj->_getLabenames($arrayData,'serchDftTxt','name')." :";
	$titlecat   = $parObj->_getLabenames($arrayData,'mainhead','name')." :";
    if(isset($_REQUEST['searchKey'])) { $titlecat .=$show_searchKey; } 
	endif;
	?>
	<?php

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
		
		//print_r($getPgmIds);exit;

        //echo implode(',',$getPgmIds['flex_id']);

		unset($search_fields, $program_csv_list, $program_arr_list);

		$bannerimage	=	"images/banner_02.jpg";

				

	}elseif(trim($_REQUEST['categoryName'])!=""){ 
		
	
	
		$_REQUEST['categoryName']	= str_replace('FF', '/', $_REQUEST['categoryName']);

		$sport=$_REQUEST['user_sport'];

		$goal=$_REQUEST['user_goal'];

	// for displaying sub-categories or programs of the categoryName

		$getAllTrainSubCats	= array();

		

		$cat_names				=	explode("-",$_REQUEST['categoryName']);

	 	$cat_flex_id			=  array_pop($cat_names);	

		$cat_normal_url   		=	implode("-",$cat_names);
		
		$cat_normal_url			=	str_replace("--","[  ",$cat_normal_url);
		$cat_normal_url			=	str_replace("-"," ",$cat_normal_url);
		$cat_normal_url			=	str_replace("[ ","-",$cat_normal_url);

		//to find out the cat name 
	
		$cat_name1				=	$objTraining->findCatName($cat_flex_id,$lanId);
		
		$cat_name				=	$objTraining->makeCategoryTitle($cat_name1);			
		
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
		//echo "<pre/>";print_r($categoryDetails);
		
		if(sizeof($categoryDetails)==0){
			redirectToSearchPage();

		}

		else

		{


			$subcatPageDetails=$objTraining->getRowFromSubCategoryPage($categoryDetails['flex_id'],$lanId,'',$goal,$sport);
		}

		

		

		//print_r($subcatPageDetails);exit;

		//echo $subcatPageDetails['keywords'];

		if($categoryDetails['parent_id']==0){

			if($categoryDetails['flex_id'] == ''):

				$showall = 0;

			else:

				$showall = 1;

			endif;

			$getAllTrainSubCats	= $objTraining->getCategories($lanId, $categoryDetails['flex_id']);
			
		}
		
		//print_r($getAllTrainSubCats);//exit;	

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

	
		$titlecat   = $cat_name	;
		//~ echo "<pre>";
		//~ print_r($categoryDetails);
		//~ echo $categoryDetails['category_image'];
		if($categoryDetails['category_image'] != '')
		{ 
			$imgPath = "admin/crop/assets/img/";
			$image = $objGen->_output(trim($categoryDetails['category_image']));
	        $image_new	=	$imgPath.$image;
			if(file_exists($image_new) && (!empty($image)))
			{
			
				$bannerimage	=	$imgPath.$image;
			}
			else
			{
				$bannerimage	= "images/banner_04.jpg";
			} 
		}
		else
		{
			$bannerimage	= "images/banner_04.jpg";
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

	//print_r($getPgmIds);exit;

	//display pgm detail form given ids

if(count($getPgmIds) > 0){  

		

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
//=======================================================================================
$data = "";
$empty_search = "";
if(mb_strlen($cat_name, 'UTF-8') > 23)
 { 
 	$cat_name_org	=	 mb_substr($cat_name,0,23 ).'...';
 }
 else
 {
	  $cat_name_org = $cat_name;
  }
 
  
 if($pgmMasterIds!= ""){

//echo "<pre/>";print_r($pgmMasterIds);
		//split the pgm id into array

		$pgmMasterIdArr	=	explode(",",$pgmMasterIds);	

		$pgmListcpount	=	count($pgmMasterIdArr);

		for($i =0;$i<=$pgmListcpount;$i++)

		{	
			 
				$data .= '<div class="colums">';
				$getTraining	=	$objPgm->_getOnePgm($pgmMasterIdArr[$i],$lanId);
				//$getTraining['flex_id'],$getTraining['program_title'],$getTraining['newprogramImage']
				
				
				
			//echo "===========";	print_r($getTraining);echo "===========";
				//===========
				if(count($getTraining) > 0){

			    	$pgm_status = $objGen->_output($getTraining['program_status']);
					if ($pgm_status != '2') {
						$getTrainingGoal	=	$objPgm->_getOnePgmGoal($getTraining['flex_id'],$lanId);

				

					if($getTrainingGoal['item_name'] == ''):

						$altname = 'jiwok';

					else:

						$altname = addslashes($getTrainingGoal['item_name']);

					endif;	
					 if(mb_strlen($objGen->_output($getTraining['program_title']), 'UTF-8') > 90)
					 { 
						$pgm_title_org	=	 mb_substr($objGen->_output($getTraining['program_title']),0,90 ).'...';
					 }
					 else
					 {
						  $pgm_title_org = $objGen->_output($getTraining['program_title']);
					  }
  
					$altname = str_replace("\'","&quot;",$getTraining['program_title']);

					$altname = str_replace('\"','&quot;',$getTraining['program_title']);

				

					$target		=	$objGen->_output($getTraining['program_target']);

					$pgmFor = $objPgm->_getGroups(trim($getTraining['program_for']),$lanId,'group');

					$schedule = $objPgm->_getName1(trim($getTraining['schedule_type']),$lanId,'schedule_type');

					$pgmLevel1 	 = $objPgm->_getName1(trim($getTraining['program_level_flex_id']),$lanId,'level');
					/*limiting length*/
					 if(mb_strlen($objGen->_output($pgmLevel1), 'UTF-8') > 20)
					 { 
						$pgmLevel	=	 mb_substr($objGen->_output($pgmLevel1),0,20 ).'...';
					 }
					 else
					 {
						$pgmLevel = $objGen->_output($pgmLevel1);
					 }
					



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
						
				}
				//HTML DATA TO BE DISPLAY ON PARENT PAGE

					if($pgm_status=='4')

				  		$buttonValue = $parObj->_getLabenames($arrayDataList,'discover','name');

					else

				  		$buttonValue = $parObj->_getLabenames($arrayDataList,'releasesoon','name'); 
				  		
				  		$pro_url 	= $objPgm->makeProgramTitleUrl($getTraining['program_title']);
					    $normal_url	= strtolower($objPgm->normal_url($pro_url));
					      
					   
					      
					    $getTraining['newprogramImage']	= $objGen->processProgramImage($getTraining['flex_id'],$getTraining['program_title'],$getTraining['newprogramImage']);
                        $image1 			            = $objGen->_output(trim($getTraining['newprogramImage']));
                         
                          $imgPath1 = "uploads/programsNew/";
 					      $image_new1	=	$imgPath1.$image1;
						  if(file_exists($image_new1) && (!empty($image1)))
						  {
							$image1	=	$imgPath1.$image1;
						  }
						  else
						  {
							$image1	= "images/dummy-2.jpg";
						  } 
						  
						 
					   $data	.=	'<figure style="margin:0px;">
                       <img data-lazy-src="'.ROOT_FOLDER.'images/corner-4.png" alt="jiwok" class="corner">
                       <div class="entrainement-category" style="height:180px;" >
                       <a target="_blank" href="'.ROOT_JWPATH.$normal_url.'-'.$getTraining['program_master_id'].'">
					   <img data-lazy-src="'.ROOT_FOLDER.$image1.'" alt="'.$titlecat.htmlentities($altname).'">
					   </a>
                        <div>
                        </figure>';
                        unset($image1);
                        unset($getTraining['newprogramImage']);
				  /*$data.='<li><div class="image"><img class="brderwhite" src="'.$imagePgm.'" alt="'.htmlentities($altname).'" '.$imgDimension.' /></div><div class="description">';*/
				  	$slash	=	"/";
					if($lanId	==	5) { $slash	=	' w ';}
				  	if($pgm_status=='4')
                    {
						$data.='<form method="post" id="progrm_training'.$i.'" name="progrm_training'.$i.'" action="'.ROOT_JWPATH.$normal_url.'-'.$getTraining['program_master_id'].'">';
                    }
					$data.= '<article style="padding: 0px 27px;">';
					//category heading
					if($cat_name_org != "")
					{
					//~ $data.= '<h3>'.$cat_name_org.'</h3>';
					}
					
					if($pgm_status=='4')

					{
					$data.='<a href="'.ROOT_JWPATH.$normal_url.'-'.$getTraining['program_master_id'].'"><h2 class="font-x">'.$pgm_title_org.'</h2></a>';

					}

					else

					{
						
						$data.='<p class="font-x">'.$pgm_title_org.'</p>';

					}
						$data.=		'<div class="listing">
       <div class="left"><span>'.$parObj->_getLabenames($arrayDataPG,'duration','name').'</span></div>
       <div class="right">'.$objGen->_output(trim($getTraining['program_schedule']))." ".$objGen->_output(trim($schedule)).'</div>
     </div>
     
     <div class="listing">
       <div class="left"><span>'.$parObj->_getLabenames($arrayDataPG,'rythm','name').'</span></div>
       <div class="right">'.$objGen->_output(trim($getTraining['program_rythm'])).' '.$parObj->_getLabenames($arrayDataPG,'times','name').$slash.$objGen->_output($schedule).'</div>
     </div>
     
     <div class="listing">
       <div class="left"><span>'.$parObj->_getLabenames($arrayDataPG,'level','name').'</span></div>
       <div class="right">'.$objGen->_output(trim($pgmLevel)).'</div>
     </div>';
	 $data.='<div class="rating">
           <img data-lazy-src="'.ROOT_FOLDER.'images/rating-star_small.png" alt="rating">
           <img data-lazy-src="'.ROOT_FOLDER.'images/rating-star_small.png" alt="rating">
           <img data-lazy-src="'.ROOT_FOLDER.'images/rating-star_small.png" alt="rating">
           <img data-lazy-src="'.ROOT_FOLDER.'images/rating-star_small.png" alt="rating">
           <img data-lazy-src="'.ROOT_FOLDER.'images/rating-star_small.png" alt="rating">
         </div>  <input type="button" onclick="progrm_training('.$i.')"   class="btn" value="'.mb_strtoupper($buttonValue,'UTF-8').'">
	</article> ';   
                /* <a href="#" class="btn">"'.$buttonValue.'"</a>*/
             	if($pgm_status=='4')

				{

					$data.="</form>";

				}
			
					}
				}
				
				//============
				$data.='</div>';
				
	   }
		
 }
 else if(count($getAllTrainSubCats)>0)

	{
		
		$buttonValue = $parObj->_getLabenames($arrayDataList,'discover','name');
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
			//******************************
			
			if($catData['category_image'] != '')
			{ 
				$imgPath = "admin/crop/assets/img/";
				$image = $objGen->_output(trim($catData['category_image']));
				$image_new	=	$imgPath.$image;
				if(file_exists($image_new) && (!empty($image)))
				{
				
					$subcatimage	=	$imgPath.$image;
				}
				else
				{
					$subcatimage	= "images/dummy-2.jpg";
				} 
			}
		else
		{
			$subcatimage	= "images/dummy-2.jpg";
		}
			
			//******************************
			
		
			//commented for correcting responsive issue $data.='<div class="colums" style="height:230px !important;padding:0 10px !important; " >';
			$data.='<div class="colums" style="" >';
                    //~ gg $data.='<figure>
                    //~ 
				    //~ <img data-lazy-src="'.ROOT_FOLDER.'images/corner-4.png" alt="jiwok" class="corner">
				    //~ <img data-lazy-src="'.ROOT_FOLDER.$subcatimage.'" alt="Jiwok">
                    //~ </figure>';
                   
           $data	.=	'<figure style="margin:0px;">
                       <img data-lazy-src="'.ROOT_FOLDER.'images/corner-4.png" alt="jiwok" class="corner">
                       <div class="entrainement-category" style="height:180px;" >
                       <a target="_blank" href="'.$subCatUrl.'" >
					   <img data-lazy-src="'.ROOT_FOLDER.$subcatimage.'">
					   </a>
                       <div>
                       </figure>';         	 
		
                
            $data.='<article style="padding: 0px 27px;">';	
               
				
				
            $data.= ' <a href="'.$subCatUrl.'" ><h2 class="font-x">'.$catData['category_name'].'</h2></a>';
            $data.= '<a href="'.$subCatUrl.'" class="btn">'.mb_strtoupper($buttonValue,'UTF-8').'</a>    
             
                    </article>';
               
			$data.='</div>';
		

		}
	}
	else{
		$empty_search = $parObj->_getLabenames($arrayDataList,'noSearchResult','name');
		
		//$result=$data."<li><span class='errorPrograms'>".$parObj->_getLabenames($arrayDataList,'noSearchResult','name')."</span></li></ul>";
	}

	
//========================================================================================
 include("header.php"); ?>
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
   function progrm_training(id) {
	   //alert(id);
	   document.forms["progrm_training"+id].submit();
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

/*$(window).load(function() {
    equalheight('.JW_ents .colums');
});
$(window).resize(function(){
  equalheight('.JW_ents .colums');
});*/

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
			
             jpopup =  $('.pop_search').bPopup({	        
            speed: 2000,
            transition: 'slideDown'
        });
            
		return false;
	}
	else if(goal	==	""	&&	level	==	""	&&	session	==	"")
	{
		document.getElementById('alertMsgSearch').innerHTML="<?=$parObj->_getLabenames($arrayDataWiz,'search_empty','name');?>";
		
              jpopup = $('.pop_search').bPopup({	         
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


   
	
   
</script>
<!---------------------------->
 <!--for search error popup -->  
 
 <section class="pop_search"> <!--<img src="images/close.png" alt="close" class="close b-modal __b-popup1__">-->
          <div class="popbox_search">
           
          <h3><div id="alertMsgSearch"></div></h3><br /><br />
           <div align="center"><input type="submit" id="mypop" class="btn_pop ease" value="VALIDER" ></div></div>
          
          </section> 
 <!--====================newwwwwwwwwwwwwww-->          


<script type="application/ld+json">
  {
  "@context": "http://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [{
    "@type": "ListItem",
    "position": 1,
    "item": {
      "@id": "<?=ROOT_JWPATH?>index.php",
      "name": "<?=strtoupper($parObj->_getLabenames($arrayData,'homeName','name'));?>"
    }
  },{
    "@type": "ListItem",
    "position": 2,
    "item": {
      "@id": "<?=ROOT_JWPATH?><?php echo ($lanId != 1) ? 'entrainement' :'training';?>",
      "name": "<?=strtoupper($parObj->_getLabenames($arrayData,'searchName','name'));?>"
    }
  },{
    "@type": "ListItem",
    "position": 3,
    "item": {
      "@id": "#",
      "name": "<?php echo strtoupper($titlecat); ?>"
    }
  }]
}
</script>
	<section class="banner-static bannerH">
    <div class="bred-hovr second">
          <ul class="bredcrumbs">
               <li><?=mb_strtoupper(($parObj->_getLabenames($arrayData,'searchPath','name')),'UTF-8');?><a href="<?=ROOT_JWPATH?>index.php">
          <?=strtoupper($parObj->_getLabenames($arrayData,'homeName','name'));?>
          </a></li>
               <li>&gt;</li>
               <li><a href="<?=ROOT_JWPATH?><?php echo ($lanId != 1) ? 'entrainement' :'training';?>"><?=strtoupper($parObj->_getLabenames($arrayData,'searchName','name'));?></a></li>
               <li>&gt;</li>
               <li><a href="#">
               <?php
			if($ttlSrch!=""){ 
				echo strtoupper($titlecat);
			}else{
				echo strtoupper($titlecat);
			}
		?></a></li>
            </ul>
       </div>
           <div class="bnr-image"><img data-lazy-src="<?=ROOT_FOLDER.$bannerimage?>" alt="jiwok"></div>
           <div class="bnr-content">
                <div class="inner">
                  <div class="heading3 entrainment">
					  <div>
					  <h1><?= $titlecat;?></h1></div></div> 
                  <div class="line2"><p>&nbsp;</p></div>
                  
                    <!--=============-->
                    <?php 
					$bnr_desc	=	"";
					if($categoryDetails['description']!="")
					{
						
						$bnr_desc	.=	'<div>'.strip_tags($categoryDetails['description']).'</div>';
					 }
 					if(count($getPgmIds) > 0){ if($prgm_flag==1){ 
					$bnr_desc	.= '<div>'.$parObj->_getLabenames($arrayData,'SearchMessage','name').'</div>';
						 
				} }
				if($empty_search !="")
				{
					$bnr_desc	.='<div>'.$empty_search.'</div>';
				}
				
	 if($bnr_desc != "")
	 { ?>
	 <div class="bnr-content2"><?php echo $bnr_desc ?></div>
	<?php } 
               ?>   
                </div>
           </div>
       
       
       </section>
     <?php if($data != "")
	 {	 ?>
      <!--pgm listings-->   
     <section class="JW_ents">
   
           <?php echo $data;?>
             
            
     </section>
     <?php
	 }
	 ?>
     <section class="JW_search_new">
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
                      <div class="center">
                      <h2><?=$parObj->_getLabenames($arrayData,'newSearchNote','name');?></h2>
                       <form name="searchWizard" method="get" id="searchWizard" action="<?=ROOT_JWPATH?>entrainement/">
                          <div class="colums">
                             <p><?=mb_strtoupper($parObj->_getLabenames($arrayData,'searchResultOpt1','name'),'UTF-8');?></p>
                             <div class="selet3">
                                     <select name="user_goal" id="user_goal"  onchange="return assignchoice();" >
                                                                 
                                	<option value="" selected="selected">
									  <?=$parObj->_getLabenames($arrayData,'select','name');?>
                                      </option>
                                      <?php foreach($wizard_goals as $wizard_goal){ ?>
                                      <?php if($lanId == 1) {
                        
                                        if($wizard_goal['flex_id'] != 'gol11' && $wizard_goal['flex_id'] != 'gol10')
                        
                                        { ?>
                                      <option value="<?  echo $wizard_goal['flex_id']; ?>" ><? echo $wizard_goal['item_name'];?></option>
                                      <?php } } 	else {?>
                                      <option value="<? echo $wizard_goal['flex_id']; ?>" ><? echo $wizard_goal['item_name'];?></option>
                                      <? }?>
                                      <? }?>
                               
                                      </select>
                                      <input type="hidden" name="cont_goal" id="cont_goal" value=""/>
                             </div>
                          </div>
                          
                          <div class="colums">
                             <p><?=mb_strtoupper($parObj->_getLabenames($arrayData,'searchResultOpt2','name'),'UTF-8');?></p>
                             <div class="selet3">
                                     <select name="user_level" id="user_level" onChange="return assignchoice();">
                                          <option value="" selected="selected">
                                           <?=$parObj->_getLabenames($arrayData,'select','name');?>
                                          </option>
                                           <?php
                                           foreach($wizard_levels as $wizard_level_id=>$wizard_level_value)
                                            {
                                                $wizard_level_item_name = $parObj->_getLabenames($arrayDataWiz,"jiwok_level".$wizard_level_id,'name');
                                                if($wizard_level_item_name=="")
                                                    {
                                                        $wizard_level_item_name=htmlentities(utf8_decode($wizard_level['item_name']));
                                                    }
                                            
                                            ?>
                                          
                                          <option value=<?=$wizard_level_id?>>
                                          <?=$wizard_level_item_name?>
                                          </option>
                                          <?php }
                                           ?>
                                        
                                      </select>
                                      <input type="hidden" name="cont_level" id="cont_level" value=""/>
                             </div>
                          </div>
                          
                          <div class="colums">
                            <?php if($lanId == 1) { ?>
                                <p>
                                  <?=$parObj->_getLabenames($arrayData,'searchResultOpt3','name');?>
                                </p><?php }else{ ?>
                                <p>
                                  <?=mb_strtoupper($parObj->_getLabenames($arrayData,'searchResultOpt4','name'),'UTF-8');?>
                                </p>
                                <?php } ?>
                            
                             <div class="selet3">
                                     <?php if($lanId == 1) { ?>
                                    <select name="user_no_session"  id="user_no_session" onChange="return assignchoice();" >
                                          <option value="" selected="selected">
                                          <?=$parObj->_getLabenames($arrayData,'select','name');?>
                                          </option>
                                          <?php foreach($wizard_rythms as $rythm_id => $rythm_value){ ?>
                                          <option value="<?php echo $rythm_id; ?>" ><?php echo $rythm_value; ?></option>
                                          <? }?>
                                        </select>
                                        <input type="hidden" name="cont_session" id="cont_session" value=""/>
                                   <?php }else{ ?>
                                   <select name="user_sport" id="user_sport"  onchange="return assignchoice();" >
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
                                                  
                                                      <option value="<?php echo $sports_row['flex_id']; ?>" ><?php echo $sports_row['item_name']; ?></option>
                                                      <? }}?>
                                                    </select>
                                                     <input type="hidden" name="cont_sport" id="cont_sport" value=""/>
                                                    <?php } ?>
                             </div>
                          </div>
                     <div class="clear"></div>
                     <input type="hidden" name="langfield" id="langfield" value="<?= $lanId ;?>"/>
                  <div class="validate"><input type="submit" name="search" value="<?=strtoupper($parObj->_getLabenames($arrayData,'searchResultBtnTxt','name'));?>" onClick="return validateSearch();"  /></div>
                  </form>
      </div>
     </section>
<?php include_once("footer.php"); ?>
