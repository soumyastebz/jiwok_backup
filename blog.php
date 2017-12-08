<?php
session_start();
$lang 			= $objPgm->_getSiteLanguage($lanId);
$myLang 		= $site_lang[$lang]; // take language from global variables set in globals.php

if($myLang == "fr") 
	{ 
		
		
		$bloguser 	= "reubromail"; 
		$blogdb 	= "jiwok_new_design"; 
		$blogps 	= "reubromail";  
		
		
	/*	$bloguser     = "newdesigntest";
        $blogdb     = "newdesigntest";
        $blogps     = "bnJ8kmvC";
		$blog_url 	= JIWOK_URL."blog/"; */

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
<div class="frame-4">
             <ul class="listing_02">
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
 <li><?=mb_strtoupper($formattedDate,'UTF-8')?><a href="<?=$blog_url?><?=utf8_encode($val2)?>" title="<?=utf8_encode($val2)?>" target="_blank"><span>......<?php echo substr($val2,0,40),(strlen($val2) > 20) ? '&hellip;':'';?></span></a></li>
                   	<?php
		}
	?>
  </ul>

             <div align="right"><a href="<?php echo $blog_url; ?>"><input type="button" value="<?=mb_strtoupper($parObj->_getLabenames($arrayData,'allblogs','name'),'UTF-8')?>" class="btn-read ease"></a></div>
          </div>