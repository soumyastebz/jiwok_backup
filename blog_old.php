<?php
session_start();

/*--------------------------------------------------*/
// Project 		: Jiwok
// Created on	: 12-05-2011
// Created by	: Ganga
// Purpose		: New Design Integration - Blog post listing dash-board
/*--------------------------------------------------*/

$lang 			= $objPgm->_getSiteLanguage($lanId);
$myLang 		= $site_lang[$lang]; // take language from global variables set in globals.php

if($myLang == "fr") 
	{ 
		// for new design test only
		/*$bloguser 	= "newdesigntest"; 
		$blogdb 	= "newdesigntest"; 
		$blogps 	= "bnJ8kmvC";  
		$blog_url 	= "http://www.jiwok.com/newdesigntest/";*/  
		
		// Live old blog settings That working
/*		$bloguser 	= "jiwok_blog_fr_n"; 
		$blogdb 	= "blog_fr"; 
		$blogps 	= "s6NFayq1";  
		$blog_url 	= "http://blog.jiwok.com/"; */
		
		$bloguser 	= "newdesigntest"; 
		$blogdb 	= "newdesignbackup"; 
		$blogps 	= "bnJ8kmvC";  
		$blog_url 	= "http://www.jiwok.com/blog/"; 

	} 

else 
	{
		 $bloguser 	= "jiwok_blog_en_n"; 
		 $blogdb 	= "blog_en"; 
		 $blogps 	= "IGfh6oJQ"; 
		 $blog_url 	= "http://blog.jiwok.com/en/"; 
	}
$dbConnBlog	=	@mysql_pconnect("localhost",$bloguser,$blogps);
$dbLinkBlog	=	@mysql_select_db($blogdb,$dbConnBlog);

$sql_blog 	= 	@mysql_query("select ID, post_title, post_content, left(post_date,10) 
							  from wp_posts where post_status = 'publish' 
							  order by post_date desc limit 0,4");
							  
$fPattern2 	= 	array('/à/','/è/','/À/','/È/','/é/','/É/','/â/','/ê/','/î/','/ô/','/û/','/Â/','/Ê/','/Î/','/Ô/','/Û/','/ä/','/ë/','/ï/','/ö/','/ü/','/Ä/','/Ë/', '/Ï/','/Ö/','/Ü/','/ç/','/Ç/','/ /');
$fReplace2 	= 	array('a','e','A','E','e','E','a','e','i','o','u','A','E','I','O','U','a','e','i','o','u','A','E','I','O','U','c','C','-');

?>
<div class="DB_tabs">
  <div class="icons blog"></div>
  <div class="shade"></div>
  <div class="headings"><?=$parObj->_getLabenames($arrayData,'myblog','name')?></div>
  <ul id="blog">
  <?php
   	while($posts = @mysql_fetch_assoc($sql_blog))
		{ 
			 $post_id 		= $posts['ID'];
			 $blog_date   	= stripslashes(trim($posts['left(post_date,10)']));
			 $blog_title  	= stripslashes(trim($posts['post_title']));
			 // $blogdate 		= date("l, F d, Y",strtotime($blog_date));
			 
			 /* Date format to selected language*/
			 
			 $blogdate 		= date("l, F d, Y",strtotime($blog_date));
			 $w_date 		= date('d',strtotime($blog_date));
			 $w_day 		= date('l',strtotime($blog_date));
			 $w_month 		= date('F',strtotime($blog_date));
			 $w_year 		= date('Y',strtotime($blog_date));
			 if($lanName	==	"english")
				{
					$w_month = $w_month;
				}
			 else
				{
					$w_month = utf8_encode($monthArray[$w_month]);
					$w_day = $dayArray[$w_day];
				}
			$formattedDate	= "$w_day, $w_month  $w_date, $w_year";		

			 /* Date format to selected language ends */

			$blogtitle 	= str_replace(" ","-",$blog_title);
			$val2 			= preg_replace($fPattern2,$fReplace2,$blogtitle);
			// http://blog.jiwok.com/$blogdate/utf8_encode($val2) // old url
			$content 		= substr(val2,0, 60);
			$pos 			= strrpos($content, " ");
			if ($pos>0) 
			{
				$content = substr($content, 0, $pos);
			}
  ?>
  	<li class="line"><em><!--Sunday, March 6, 2011--><?=$formattedDate?></em> <a href="<?=$blog_url?><?=utf8_encode($val2)?>" title="<?=utf8_encode($val2)?>" target="_blank"><strong>&gt;</strong> <?php echo substr($val2,0,40),(strlen($val2) > 20) ? '&hellip;':'';?></a></li>
	<?php
		}
	?>
  </ul>
  <?php
  if($lanId==1){
	  $blogUrl	=	"http://blog.jiwok.com/en/";
  }else{
	  $blogUrl	=	"http://www.jiwok.com/blog/";	  
  }
  ?>
  <p align="right"><a href="<?php echo $blogUrl; ?>" class="orange"><strong>&gt; </strong><?=$parObj->_getLabenames($arrayData,'allblogs','name')?></a></p>
</div>