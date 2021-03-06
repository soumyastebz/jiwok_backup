<?php
    $lifetime		=	600;
	session_start();
	setcookie(session_name(),session_id(),time()+$lifetime);
    $homePage 	= 	TRUE;	
    include_once('includeconfig.php'); 
    include_once('user_logged.php');
	include_once("includes/classes/class.homepage.php");
	include_once('includes/classes/class.CMS.php');
	include_once('includes/classes/class.programs.php');
	include_once('includes/classes/class.slide.php');//main slider
	include_once("includes/classes/class.testimonials.php");//testimonials
	include_once "includes/classes/class.Search.php";//top search
	include_once('includes/classes/class.coaches.php');//coach slider
	require_once('papGlobal.php');
    //session_destroy(); die('Session cleared');
	if($lanId=="")	$lanId=2;
	$objGen     	= new General();
	$objHome     	= new Homepage($lanId);
	$objCMS    		= new CMS($lanId);
	$objPgm     	= new Programs($lanId);
	$parObj 		= new Contents('index.php');
	$slide          = new Service($lanId);
	$slide_values   = $slide->_showPageNew("",1,4,$field='manager_id',$type='ASC');
	$objtestimoni	= new Testimonial();
	$searchObj      = new Search();
	$objCoaches		= new coaches($lanId);
    $coachesCatData = $objCoaches->_getAllContent_home($lanId);
	//~ echo "<pre>";
	//~ print_r($coachesCatData);
	//~ exit;
	//collecting data from the xml for the static contents
    $headingData	= $parObj->_getTagcontents($xmlPath,'homepage','pageHeading');
    //collecting data from the xml for the static contents
    $returnData		= $parObj->_getTagcontents($xmlPath,'homepage','label');
    $arrayData		= $returnData['general'];
    $returnData		= $parObj->_getTagcontents($xmlPath,'searchWizard','label');
	$arrayDataWiz	= $returnData['general'];
    //for collecting the urls and keywords
    $getHomeId	=	0;
    $getUrlKey	=	$objGen->_lastVistedPage('Defalt');
    if($getUrlKey!='Defalt'){
            $getUrlKey			=	$objGen->_getUrlKeyword($getUrlKey);
            $url				=	$getUrlKey[0];
            $key				=	$getUrlKey[1];
            $getHomeId			=	$objHome->_getHomePageId($url,$key,$lanId);
    if(isset($_SESSION['home']['HomeId']))
        	$getHomeId			=	$_SESSION['home']['HomeId'];
    else		
        	$_SESSION['home']['HomeId']	=	$getHomeId;	
    if($getHomeId <> 0)
        	$getHomeContent		=	$objHome->_displayHomeContent($getHomeId,$lanId);

			$homeTitle			=	$getHomeContent[0]['homepage_title'];

			$homeContent		=	$getHomeContent[0]['homepage_content'];

			$bgImage			=	"./uploads/homepage/".$getHomeContent[0]['homepage_image'];
     }
     if($getHomeId == 0){
            //~ $contentType		=	"HOME";
			$contentType		=	"Home_page_Icon_Text";
            $getHomeDetails		=	$objCMS->_getContent($contentType,$lanId);
			
			 $homeTitle			=	$getHomeDetails['content_display_title'];
             $homeContent		=	$getHomeDetails['content_body'];
             $bgImage			=	"./images/img_main_home.jpg";
   	}
   	$fromLimit		=	0;
    $upLimit		=	1;
    $getTestimoni	=	$objtestimoni->_displayPage($getHomeId,$lanId,$fromLimit,$upLimit);//for getting testimonial details
    if(trim($_GET['a_aid'])!= "" && trim($_GET['a_bid'])!= "")
    {
     	$a_aid = trim($_GET['a_aid']);

		$a_bid = trim($_GET['a_bid']);

		$_SESSION['affili']['a_aid'] = $a_aid;

		$_SESSION['affili']['a_bid'] = $a_bid;

		$papUserId = $objPgm->_getUserIdPap($a_aid);

		$campaignId = $objPgm->_getCampaignId($a_bid);

		$insArray = array();

		$insArray['clickid'] = '';

		$insArray['userid'] = addslashes($papUserId);

		$insArray['bannerid'] = addslashes($a_bid);

		$insArray['campaignid'] = addslashes($campaignId);

		$insArray['rtype'] = 'R';

		$insArray['datetime'] = date("Y-m-d H:i:s");

		$insArray['refererurl'] = $getUrlKey[0];

		$ip=$_SERVER['REMOTE_ADDR'];

		$insArray['ip'] = $ip; 

		$res = $objPgm->_insertPapValues($insArray);

		$query = "select MAX(clickid) as max from qu_pap_rawclicks";

		$result = $GLOBALS['db']->getRow($query, DB_FETCHMODE_ASSOC); 

		$clickid = trim($result['max'])+1;

		$_GET['a_aid']='';

		$_GET['a_bid']='';

		header('location:index.php',true,301);
	}	
   // Redirect to logout from ticket section
   if($_REQUEST['logout'] == 1)
    { 
		header("location:logout.php?returnUrl=".base64_encode('index.php'),true,301);
		exit; 
	} 	
include("header.php"); 
//For showing popup about new law for cookie
	if($lanId==5){
		if(!$_SESSION['show_cookie_popup']){
			
			$_SESSION['show_cookie_popup'] = 'TRUE';
		}else{
			$_SESSION['show_cookie_popup'] = 'FALSE';
		}
	}	
	//need check design of footer banner
?>



<!-----slider open---------------->
<div class="frame slider-first">
<div class="callbacks_container">
        <ul class="rslides" id="slider4">
		  <?php
		  if($slide_values){
          foreach($slide_values as $values){
			  ?>  
         <li>
			<?php //echo $values['slide_link'];?>
		   <img src="<?=ROOT_FOLDER?>uploads/slides/<?php  echo $values['slide_image_video']; ?>" alt="jiwok">
           <h2 class="caption">
		   <span>JIWOK.</span>
           <?php echo $values['slide_content_name']; ?></h2>
             
           <a href="#" OnClick="location.href='<?=ROOT_JWPATH?>userreg1.php'" class="link homepage"><?=$parObj->_getLabenames($arrayData,'newTopButtonTxt','name');?></a>
                
         </li>
         <?php }} ?>
       </ul>
    </div>
    </div>
<!-----slider closed---------------->   
    <section class="articles-main">
     <div class="in mid-wrapper">
		<?php if($homeContent){ echo $homeContent;}?>
         </div> 
    </section>
   
	<?php include('search_index.php'); ?>

<!--------Testimonials open----------------------->
    <div class="grid_second frame mid-wrapper">
    
		 <?php if($getTestimoni){	
		foreach($getTestimoni as $getTestimoniValues){
		   $testimonial_by = $objGen->_output($getTestimoniValues['user_name']);
			$content = substr($objGen->_output($getTestimoniValues['testimonial_desc']),0, 35);
			$pos = strrpos($content, " ");
			if ($pos>0) {
				$content = substr($content, 0, $pos);
			}
			$desc = substr($objGen->_output($getTestimoniValues['testimonial_desc']),0, 120);
			$pos = strrpos($desc, " ");
			if ($pos>0) {
				$desc = substr($desc, 0, $pos);
			}?>
		
        <figure>
           <img data-lazy-src="<?=ROOT_FOLDER?>images/corner.png" class="corner">
           <img data-lazy-src="<?=ROOT_FOLDER?>images/testimonial.jpg" alt="Jiwok">
        </figure>
        
        <article>
           <h2><?php echo $content;?>
          </h2>
<p class="second-line"><?php echo $testimonial_by ;?></p>
<blockquote class="third-line"><?php echo $desc;?></blockquote>
        <p align="right"><a href="<?=ROOT_JWPATH?>press_testimonial_details.php?select=t" class="btn_orng_2 ease">
			<b>
			<?=$parObj->_getLabenames($arrayData,'all','name');?>
			</b>
			</a></p>
        </article>
        <?php }
		}?>
    </div>
    
<!--------Testimonials closed---------------------->
<!--------Top search open-------------------------->
   <?php

		$topCntrCnt=0;
		$topSrch=$searchObj->getTopSearchs($lanId);//print_r($topSrch);exit;
		if(count($topSrch)>0){
			
		?>
     <div  class="frame top-entry mid-wrapper">
		<h2><?=$parObj->_getLabenames($arrayData,'top-10','name');?></h2>
        <span id="site_top10">
    <?php
        $height=4;$hashes=1; $j=9;
         for ($i=0; $i<$height; $i++) 
         { 
     ?>
		 <ul> 
		<?php
		  for ($k=0; $k<$hashes; $k++) 
		  {     
				if($_SERVER['HTTP_HOST'] == "www.jiwok.com.jiwok-wbdd2.najman.lbn.fr")
				{
					$topSrch[$j]['web_link'] =	str_replace("http://www.jiwok.com","http://www.jiwok.com.jiwok-wbdd2.najman.lbn.fr",$topSrch[$j]['web_link']);
				}  
		  ?>
				  <li>
				   <div>
				   <span class="count"><?php echo $topSrch[$j]['rank']; ?></span>
				   <a href="<?php echo $topSrch[$j]['web_link'];  ?>"><span class="text">
					   <h5>
						   <!-- for adding style, give 2 classes ------------>
					   <?php 
					
						if(strlen($topSrch[$j]['title'])>16){
							
						  ?>
						  <div class="two-line"><?php echo mb_strtoupper($topSrch[$j]['title'],'UTF-8');?></div>
						  <?php }else{?>
						  <div class="one-line"><?php echo mb_strtoupper($topSrch[$j]['title'],'UTF-8');?></div>
						 <?php }?>
					   </h5>
					   </span></a>
				   </div>
				</li>
		  <?php
		  $j--;
		  }?>
		 </ul>
    <?php
         $hashes ++; 
         } 
     ?>
     </span>
       <!-----------for mobile devices------>   
      <span id="mob_top10" style="display:none">
	 <?php
        $height=4;$hashes=1; $j=0;
         for ($i=0; $i<$height; $i++) 
         { 
     ?>
		 <ul> 
		<?php
		  for ($k=0; $k<$hashes; $k++) 
		  {     
				if($_SERVER['HTTP_HOST'] == "www.jiwok.com.jiwok-wbdd2.najman.lbn.fr")
				{
					$topSrch[$j]['web_link'] =	str_replace("http://www.jiwok.com","http://www.jiwok.com.jiwok-wbdd2.najman.lbn.fr",$topSrch[$j]['web_link']);
				}  
		  ?>
				  <li>
				   <div>
				   <span class="count"><?php echo $topSrch[$j]['rank']; ?></span>
				   <a href="<?php echo $topSrch[$j]['web_link'];  ?>"><span class="text">
					   <h5>
						   <!-- for adding style, give 2 classes ------------>
					   <?php 
					
						if(strlen($topSrch[$j]['title'])>16){
							
						  ?>
						  <div class="two-line"><?php echo mb_strtoupper($topSrch[$j]['title'],'UTF-8');?></div>
						  <?php }else{?>
						  <div class="one-line"><?php echo mb_strtoupper($topSrch[$j]['title'],'UTF-8');?></div>
						 <?php }?>
					   </h5>
					   </span></a>
				   </div>
				</li>
		  <?php
		  $j++;
		  }?>
		 </ul>
    <?php
         $hashes ++; 
         } 
     ?>
     </span>
    </div>   
      <!-----------for mobile devices------>  
      <?php }
   
         ?>
  
         
         
<!--------Top search closed-------------------------->
<!---------coaches slider open----------------------->
<div class="frame slider-second mid-wrapper">
<div class="callbacks_container">
<div id="catrgory">
</div>
</div>
</div>
<input type="hidden" value="<?php echo $lanId; ?>" name="lanId" id="lanId">
<input type="hidden" value="0" name="category_id" id="category_id">
<!---------coaches slider closed----------------------->
    <section class="frame press mid-wrapper">
    
         <div class="box">
        <ul>
           <li>
             <span class="press_ID"><img data-lazy-src="<?=ROOT_FOLDER?>images/press_01.jpg" alt="press"></span>
             <span class="count">8"</span>
           </li>
           <li>
             <span class="press_ID"><img data-lazy-src="<?=ROOT_FOLDER?>images/press_02.jpg" alt="press"></span>
             <span class="count">15"</span>
           </li>
           <li>
             <span class="press_ID"><img data-lazy-src="<?=ROOT_FOLDER?>images/press_03.jpg" alt="press"></span>
             <span class="count">19"</span>
           </li>
           <li>
             <span class="press_ID"><img data-lazy-src="<?=ROOT_FOLDER?>images/press_04.jpg" alt="press"></span>
             <span class="count">32"</span>
           </li>
           <li>
             <span class="press_ID"><img data-lazy-src="<?=ROOT_FOLDER?>images/press_05.jpg" alt="press"></span>
             <span class="count">1'19"</span>
           </li>
            <li>
             <span class="press_ID"><img data-lazy-src="<?=ROOT_FOLDER?>images/press_06.jpg" alt="press"></span>
             <span class="count">3'30"</span>
           </li>
        </ul>
        </div>
        <div class="box">
        <ul>
           <li>
             <span class="press_ID"><img data-lazy-src="<?=ROOT_FOLDER?>images/press_07.jpg" alt="press"></span>
             <span class="count">132m</span>
           </li>
           <li>
             <span class="press_ID"><img data-lazy-src="<?=ROOT_FOLDER?>images/press_08.jpg" alt="press"></span>
             <span class="count">146m</span>
           </li>
           <li>
             <span class="press_ID"><img data-lazy-src="<?=ROOT_FOLDER?>images/press_09.jpg" alt="press"></span>
             <span class="count">347m</span>
           </li>
           <li>
             <span class="press_ID"><img data-lazy-src="<?=ROOT_FOLDER?>images/press_10.jpg" alt="press"></span>
             <span class="count">678m</span>
           </li>
        </ul>
        <a href="<?=ROOT_JWPATH?>press_testimonial_details.php?select=p" class="btn_orng3 ease"><b><?=mb_strtoupper($parObj->_getLabenames($arrayData,'all_press','name'),'UTF-8');?></b></a>
        </div>
       
     </section>
     <!---------------footer.........-->     
     <?php include("footer.php");  ?>

