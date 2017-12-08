<?php 
/*--------------------------------------------------*/
// Project 		: Jiwok
// Integratedby	: Shilpa
// Created on	: 08-07-2011
// Modified on	: 11-07-2011
// Purpose		: href lan tag implementation in all paginated pages except ajax pages
/*--------------------------------------------------*/
  
    //gg commented for https -sei for https
    $path_en="http://en.jiwok.com/";
	if($page_name  == "payment_new.php"){
	   $path_en ="https://en.jiwok.com/";
    }
	if($pg_name	!=	'payment_renew'	&&  $pg_name	!=	'payment_new'	&&  $pg_name	!=	'payment_new_test'	 &&	$pg_name	!=	'payment_giftpopup_bis'	&& $pg_name	!=	'program_generate2')
	{
		include_once("includes/classes/class.programs.php");
	}
	$url_curnt			=	basename($_SERVER['REQUEST_URI']);
	$objTraining			= new Programs($langId);
		
	/*if($lanId	==	1)
	{
		$url_seo		=	"en/".$url_curnt;
	}
	else if($lanId	==	5)
	{
		$url_seo		=	"pl/".$url_curnt;
	}
	else
	{
		$url_seo		=	$url_curnt;
	}*/
if(($_REQUEST['categoryName']=="")&&($url_curnt!='blog')&&($url_curnt!='forum')&&($_REQUEST['program_title_url']==""))
{
	
		switch($url_curnt){
        case 'plan.php':
        $url_en=$path_en."en"."/".'services_details.php';
        break;
        case 'contact-us':
        $url_en=$path_en."en"."/".'contact+us';
        break;
        case 'coaches.php':
        $url_en=$path_en."en"."/".'coach-athlete';
        break;
        case 'pl':
        $url_en=$path_en;
        break;
        default:
        $url_en=$path_en."en"."/".$url_curnt;
	    }?>
		<link rel="alternate" hreflang="en" href="<?=$url_en?>"/>
		<link rel="alternate" hreflang="fr" href="<?=ROOT_FOLDER.$url_curnt?>"/>
		<link rel="alternate" hreflang="pl" href="<?=ROOT_FOLDER."pl"."/".$url_curnt?>"/>
<?php
}
else if ($url_curnt	==	'blog')
{
?>	
		<link rel="alternate" hreflang="fr" href="<?=ROOT_FOLDER.$url_curnt?>"/>
		<link rel="alternate" hreflang="en" href="http://blog.jiwok.com/en/"/>
<?php
}
else if ($url_curnt	==	'forum')
{
?>	
		<link rel="alternate" hreflang="fr" href="<?=ROOT_FOLDER.$url_curnt?>"/>
<?php
}
else if ($_REQUEST['categoryName']!="")
{
	if($_SESSION['search_val'])
	{
	 
		
	}
	else
	{
		$url		=	explode("-",basename($_SERVER['REQUEST_URI']));
		$flexval	=	array_pop($url);
		$urlflex	=	"-".$flexval;
		$qry		=	"SELECT * FROM sub_category WHERE flex_id='".$flexval."'";	
		$rslt		=	$GLOBALS['db']->getAll($qry, DB_FETCHMODE_ASSOC);
		foreach($rslt as $val)
		{ 
			$catname	=	strtolower($val['category_name']);
			/*str_replace(" ","-",$catname);
			str_replace("+","-",$catname);*/
			$getAllTrainCats_url 		= 	 $objTraining->makeCategoryTitle($val['category_name']);
			$getAllTrainCats_url		=	 strtolower($getAllTrainCats_url);
			$getAllTrainCats_url		=	 $objTraining->normal_url($getAllTrainCats_url);
			$getAllTrainCats_url		=	 urlencode($getAllTrainCats_url);				
			$getAllTrainCats_url		=	 str_replace("+","-",$getAllTrainCats_url);	
		//$getAllTrainCats[$i]['url']	=	 str_replace('%2F', ' %252F', $getAllTrainCats[$i]['url']); comment by anu
			$getAllTrainCats_url		=	 str_replace('%2F', 'FF', $getAllTrainCats_url);	
			$path2						=	 'entrainement/'.$getAllTrainCats_url.$urlflex;
			if($val['language_id']	==	'1')
			{?>
				<link rel="alternate" hreflang="en" href="<?=$path_en."en/".$path2 ?>"/>
			<?php
			}
			if($val['language_id']	==	'2')
			{?>
				<link rel="alternate" hreflang="fr" href="<?=ROOT_FOLDER.$path2 ?>"/>
			<?php
			}
			if($val['language_id']	==	'5')
			{?>
				<link rel="alternate" hreflang="pl" href="<?=ROOT_FOLDER."pl/".$path2 ?>"/>
			<?php
			}
		}
	}
?>	
<?php 
}
else if ($_REQUEST['program_title_url']!="")
{
	
		$url		=	explode("-",basename($_SERVER['REQUEST_URI']));
		$flexval	=	array_pop($url);
		$urlflex	=	"-".$flexval;
		$qry		=	"SELECT * FROM program_detail WHERE program_master_id='".$flexval."'";	
		$rslt		=	$GLOBALS['db']->getAll($qry, DB_FETCHMODE_ASSOC);
		foreach($rslt as $val)
		{ 
			/*$catname	=	strtolower($val['program_title']);
			str_replace(" ","-",$catname);
			str_replace("+","-",$catname);*/
			$pro_url 	= 	$objTraining->makeProgramTitleUrl($val['program_title']);
			$normal_url	= strtolower($objTraining->normal_url($pro_url));
			$path2 		=   $normal_url.$urlflex;
			if($val['language_id']	==	'1')
			{?>
				<link rel="alternate" hreflang="en" href="<?=$path_en."en/".$path2 ?>"/>
			<?php
			}
			if($val['language_id']	==	'2')
			{?>
				<link rel="alternate" hreflang="fr" href="<?=ROOT_FOLDER.$path2 ?>"/>
			<?php
			}
			if($val['language_id']	==	'5')
			{?>
				<link rel="alternate" hreflang="pl" href="<?=ROOT_FOLDER."pl/".$path2 ?>"/>
			<?php
			}
		}
?>	
<?php 
}
?>
