<?php
session_start();
include_once("includes/filterValidation.php");
include_once('includeconfig.php');
$langId					=	isset($_REQUEST['langId']) ? $_REQUEST['langId'] : "";
$_SESSION['langId']	  	= 	$langId;
include_once("includes/classes/class.programs.php");
$objTraining			= new Programs($langId);
///////////////////////////////Added by Shilpa for SEO//////////////////////////////////////////////////
if(($_REQUEST['rq'])=="categoryName")
{
		
		$page_name_flag	= $_REQUEST['req_name'];
	    $url		=	explode("-",$page_name_flag);
		$flexval	=	array_pop($url);
		$urlflex	=	"-".$flexval;
		$qry		=	"SELECT * FROM sub_category WHERE flex_id='".$flexval."' AND language_id='".$_SESSION['langId']."'";
		$rslt		=	$GLOBALS['db']->getRow($qry,DB_FETCHMODE_ASSOC);
		/*$catname	= 	str_replace('-', '_',$rslt['category_name']);
		$catname	= 	str_replace("'", '-', $catname);
		$catname	= 	str_replace(',', '-', $catname);
        $catname	=	strtolower($catname);
		$catname	=	str_replace(" ","-",$catname);
		$catname	=	urlencode($catname);
		$catname	=	str_replace("+","-",$catname);
		$catname	=	str_replace("%2F","FF",$catname);
        $path2 		= 	"entrainement/".$catname.$urlflex;*/
		$getAllTrainCats_url 		= 	 $objTraining->makeCategoryTitle($rslt['category_name']);
		$getAllTrainCats_url		=	 strtolower($getAllTrainCats_url);
		$getAllTrainCats_url		=	 $objTraining->normal_url($getAllTrainCats_url);
		$getAllTrainCats_url		=	 urlencode($getAllTrainCats_url);				
		$getAllTrainCats_url		=	 str_replace("+","-",$getAllTrainCats_url);	
	    //$getAllTrainCats[$i]['url']	=	 str_replace('%2F', ' %252F', $getAllTrainCats[$i]['url']); comment by anu
		$getAllTrainCats_url		=	 str_replace('%2F', 'FF', $getAllTrainCats_url);	
		$path2						=	 'entrainement/'.$getAllTrainCats_url.$urlflex;
	
}
else if($_REQUEST['rq']=="program_title_url")
{
	 	$page_name_flag	= $_REQUEST['req_name'];
	 	$url		=	explode("-", $page_name_flag);
		$flexval	=	array_pop($url);
		$urlflex	=	"-".$flexval;
		$qry		=	"SELECT * FROM program_detail WHERE program_master_id='".$flexval."' AND language_id='".$_SESSION['langId']."'";	
		$rslt		=	end($GLOBALS['db']->getAll($qry, DB_FETCHMODE_ASSOC));
	  /*$catname	=	strtolower($rslt['program_title']);
		$catname	=	str_replace(" ","-",$catname);
		$catname	=	str_replace("+","-",$catname);*/
	    $pro_url 	= 	$objTraining->makeProgramTitleUrl($rslt['program_title']);
		$normal_url	= strtolower($objTraining->normal_url($pro_url));
		$path2 		=   $normal_url.$urlflex;
		
}
////////////////////////////////////////Added by Shilpa for SEO////////////////////////////////////////////////////////////////
$path				=	isset($_REQUEST['path']) ? base64_decode($_REQUEST['path']) : "";
if($path	==	"search.php")
{
	$path	=	"entrainement";
}
switch($langId){
	case '1':
		$langFolder = 'en/';// English
	break;
	case '2':
		$langFolder = '';// French
	break;
	case '3':
		$langFolder = 'es/'; // Spanish
	break;
	case '4':
		$langFolder = 'it/'; // Spanish
	break;
	case '5':
		$langFolder = 'pl/'; // Spanish
	break;
	default :
		$langFolder = ''; // French
	break;
}
if($langId==1){
//$page	=	"http://original.jiwok.com/en/index_eng.php";
}else{
//$page					=	$path ? JIWOK_URL.$langFolder.$path : "";
}
/*-------------------------------------------------------------*/
	$pageCms	=	end(explode("?",$path));
	if($pageCms == "title=contactus")
	{
		$path	=	"contact-us";
	}
	if($pageCms ==	"title=aboutus")
	{
		$path	=	"about-us"; 
	}
	if($pageCms ==	"title=terms")
	{
		$path	=	"terms-and-conditions"; 
	}
	if($pageCms ==	"title=press")
	{
		$path	=	"press"; 
	}
	if($pageCms ==	"title=job")
	{
		$path	=	"jobs"; 
	}
/*-------------------------------------------------------------*/
 $page					=	$path ? JIWOK_URL.$langFolder.$path : "";
 ////////////////////////////////////////Added by Shilpa for SEO////////////////////////////////////////////////////////////////
if(($_REQUEST['rq'])=="categoryName"	||	($_REQUEST['rq'])=="program_title_url")
{
	 $page				=	JIWOK_URL.$langFolder.$path2;
}
////////////////////////////////////////Added by Shilpa for SEO////////////////////////////////////////////////////////////////
header("location:$page");
exit();		
?>
