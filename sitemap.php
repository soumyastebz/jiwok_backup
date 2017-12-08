<?php
ob_start();
session_start();
/*--------------------------------------------------*/
// Project 		: Jiwok
// Created on	: 09-06-2011
// Created by	: Ganga
// Purpose		: New Design Integration - Sitemap 
/*--------------------------------------------------*/
include("header.php");
include_once("includes/classes/class.programs.php");
$objGen     	= new General();
$parObj 		= new Contents('sitemap.php');
$objTraining	= new Programs($lanId);
//collecting data from the xml for the static contents
$returnData		= $parObj->_getTagcontents($xmlPath,'contents','label');
$arrayData		= $returnData['general'];

$SiteData		= $parObj->_getTagcontents($xmlPath,'siteMap','label');
//print_r($returnData);
$SiteData		= $SiteData['general'];
//get page title
$page_title		= '';
?>
<script language="javascript">
 function showhide(ctrl)
 	{		
		$("#"+ctrl).slideToggle();
	}
</script> 
<script>
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
	
  equalheight('.top_10 ul li');
});
$(window).resize(function(){
  equalheight('.top_10 ul li');
});
</script>
<!--command for adding banner image
  <div class="frame3">
         <div class="row-1" style="padding-bottom:0"><div class="return">
            
            <a href="<?=$backButtonLink?>" class="small">
      <?=mb_strtoupper($parObj->_getLabenames($SiteData,'back','name'),'UTF-8');?>
      </a>
         </div>
         <div class="title">
          <ul class="bredcrumbs">
               <li>
          <?=mb_strtoupper($parObj->_getLabenames($SiteData,'searchPath','name'),'UTF-8');?>
        </li>
        <li><a href="<?=ROOT_JWPATH?>">
          <?=mb_strtoupper($parObj->_getLabenames($SiteData,'homeName','name'),'UTF-8');?>
          </a></li>
        <li>></li>
        <li><a >
          <?=mb_strtoupper($parObj->_getLabenames($SiteData,'mainhead','name'),'UTF-8');?>
          </a></li>
           </ul>
           <h3 class="hed-2"><?=$parObj->_getLabenames($SiteData,'mainhead','name');?></h3>
      </div>
         
         </div>
      </div> 
-->
<section class="banner-static bannerH bnr-mrgn">
      <div class="bred-hovr second">
          <ul class="bredcrumbs">
               <li>
          <?=mb_strtoupper($parObj->_getLabenames($SiteData,'searchPath','name'),'UTF-8');?>
        </li>
        <li><a href="<?=ROOT_JWPATH?>">
          <?=mb_strtoupper($parObj->_getLabenames($SiteData,'homeName','name'),'UTF-8');?>
          </a></li>
        <li>></li>
        <li><a >
          <?=mb_strtoupper($parObj->_getLabenames($SiteData,'mainhead','name'),'UTF-8');?>
          </a></li>
            </ul>
       </div>
       
           <div class="bnr-image"><img data-lazy-src="<?=ROOT_FOLDER?>images/banner_02.jpg" alt="jiwok"></div>
           <div class="bnr-content">
                <div class="inner">
                  <div class="heading"><p><h1><?=$parObj->_getLabenames($SiteData,'mainhead','name');?></h1></p></div> 
                  <div class="line"><p>&nbsp;</p></div>
               
                 
                </div>
              
           </div>
       
       
       </section>

     
     <section class="theme-select">
        <div class="frame">
           <p class="heading"> <?=$parObj->_getLabenames($SiteData,'title13','name');?> </p>
           <div class="categories">
             <?php include("right_cate_list.php"); ?>
           </div>
           
        </div>
     </section>
     <section class="sitemap">
        <ul>
           <li><a href="<?=ROOT_JWPATH?>"\>
            <?=$parObj->_getLabenames($SiteData,'title1','name');?>
            </a></li>
          <li><a href="<?=ROOT_JWPATH?>userreg1.php" rel="nofollow"\>
            <?=$parObj->_getLabenames($SiteData,'title2','name');?>
            </a></li>
          <li><a href="<?=ROOT_JWPATH?>entrainement" \>
            <?=$parObj->_getLabenames($SiteData,'title13','name');?>
            </a></li>
         <li class="has-sub">
           
               <?php 
		  if($lanId == 1) 
		  	{
		  ?>
		  <ul>
                  <li><a href="<?=ROOT_JWPATH?>training/Run+Faster-1" \>
                <?=$parObj->_getLabenames($SiteData,'main1','name');?>
                </a></li>
              <li><a href="<?=ROOT_JWPATH?>training/Recovery+Training-8" \>
                <?=$parObj->_getLabenames($SiteData,'main2','name');?>
                </a></li>
              <li><a href="<?=ROOT_JWPATH?>training/" \><!--Race+Training-3-->
                <?=$parObj->_getLabenames($SiteData,'main3','name');?>
                </a></li>
                   <li class="has-sub">
                      <ul>
                       <li><a href="<?=ROOT_JWPATH?>training/Marathon-3.1" \>
                  <?=$parObj->_getLabenames($SiteData,'sub1','name');?>
                  </a></li>
                <li><a href="<?=ROOT_JWPATH?>training/Half+Marathon-3.2" \>
                  <?=$parObj->_getLabenames($SiteData,'sub2','name');?>
                  </a></li>
                <li><a href="<?=ROOT_JWPATH?>training/6+km-3.5" \>
                  <?=$parObj->_getLabenames($SiteData,'sub5','name');?>
                  </a></li>
                <li><a href="<?=ROOT_JWPATH?>training/10+km-3.6" \>
                  <?=$parObj->_getLabenames($SiteData,'sub6','name');?>
                  </a></li>
                <li><a href="<?=ROOT_JWPATH?>training/20+km-3.7" \>
                  <?=$parObj->_getLabenames($SiteData,'sub7','name');?>
                  </a></li>
                      </ul>
                   </li>
                   <li><a href="<?=ROOT_JWPATH?>training/Start+running-10" \>
                <?=$parObj->_getLabenames($SiteData,'main4','name');?>
                </a></li>
              <li><a href="<?=ROOT_JWPATH?>training/Single+workout+(30+min-+1+hour...)-11" \>
                <?=$parObj->_getLabenames($SiteData,'main5','name');?>
                </a></li>
               </ul>
              <?php 
				}
			else
				{
			?>
			 <ul>
                   <li><a href="<?=ROOT_JWPATH?>entrainement/Courir+plus+vite-1" \>
                <?=$parObj->_getLabenames($SiteData,'main1','name');?>
                </a></li>
              <li><a href="<?=ROOT_JWPATH?>entrainement/Test+physique+%252F+Preparation+concours-2" \>
                <?=$parObj->_getLabenames($SiteData,'main2','name');?>
                </a></li>
              <li><a href="<?=ROOT_JWPATH?>entrainement/" \><!--Prepare-r+une+course-3-->
                <?=$parObj->_getLabenames($SiteData,'main3','name');?>
                </a></li>
                   <li class="has-sub">
                      <ul>
                      <li><a href="<?=ROOT_JWPATH?>entrainement/Marathon-3.1" \>
                    <?=$parObj->_getLabenames($SiteData,'sub1','name');?>
                    </a></li>
                  <li><a href="<?=ROOT_JWPATH?>entrainement/Semi+Marathon-3.2" \>
                    <?=$parObj->_getLabenames($SiteData,'sub2','name');?>
                    </a></li>
					<!--Commented on 25-06-2011 [Link Not in use]-->
                  <?php /*?><li><a href="<?=ROOT_JWPATH?>entrainement/Trail-3.3" \>
                    <?=$parObj->_getLabenames($SiteData,'sub3','name');?>
                    </a></li>
                  <li><a href="<?=ROOT_JWPATH?>entrainement/Ultra+Trail-3.4" \>
                    <?=$parObj->_getLabenames($SiteData,'sub4','name');?>
                    </a></li><?php */?>
                  <li><a href=" <?=ROOT_JWPATH?>entrainement/6+km-3.5" \>
                    <?=$parObj->_getLabenames($SiteData,'sub5','name');?>
                    </a></li>
                  <li><a href=" <?=ROOT_JWPATH?>entrainement/10+km-3.6" \>
                    <?=$parObj->_getLabenames($SiteData,'sub6','name');?>
                    </a></li>
                  <li><a href=" <?=ROOT_JWPATH?>entrainement/20+km-3.7" \>
                    <?=$parObj->_getLabenames($SiteData,'sub7','name');?>
                    </a></li>
                      </ul>
                   </li>
                   <li><a href="<?=ROOT_JWPATH?>entrainement/Etirements+et+relaxation-6" \>
                <?=$parObj->_getLabenames($SiteData,'main6','name');?>
                </a></li>
              <li><a href="<?=ROOT_JWPATH?>entrainement/Sante+et+perte+de+poids-5" \>
                <?=$parObj->_getLabenames($SiteData,'main4','name');?>
                </a></li>
              <li><a href="<?=ROOT_JWPATH?>entrainement/Etirements+et+relaxation-6" \>
                <?=$parObj->_getLabenames($SiteData,'main5','name');?>
                </a></li>
              <li><a href="<?=ROOT_JWPATH?>entrainement/cardio+training-7" \>
                <?=$parObj->_getLabenames($SiteData,'main7','name');?>
                </a></li>
               </ul>
               <?php
		  }
		  ?>
             
           </li>
              
            <li><a href="<?=ROOT_JWPATH?>coaches.php" \>
            <?=$parObj->_getLabenames($SiteData,'title3','name');?>
            </a></li>
          <li><a href=" <?=ROOT_JWPATH?>faq" \>
            <?=$parObj->_getLabenames($SiteData,'title4','name');?>
            </a></li>
          <li><a href=" http://blog.jiwok.com/" target="_blank" \>
            <?=$parObj->_getLabenames($SiteData,'title5','name');?>
            </a></li>
		  <?php 
		  if($lanId != 1) 
		  	{
		  ?>
          <li><a href=" http://forum.jiwok.com/" target="_blank" \>
            <?=$parObj->_getLabenames($SiteData,'title6','name');?>
            </a></li>
 		  <?php 
		  	}
		  ?>
         <li><a href="<?=ROOT_JWPATH?>about+us" \>
            <?=$parObj->_getLabenames($SiteData,'title7','name');?>
            </a></li>
          <li><a href="<?=ROOT_JWPATH?>contact+us" \>
            <?=$parObj->_getLabenames($SiteData,'title8','name');?>
            </a></li>
          <li><a href="<?=ROOT_JWPATH?>terms+and+conditions" rel="nofollow" \>
            <?=$parObj->_getLabenames($SiteData,'title9','name');?>
            </a></li>
          <li><a href="<?=ROOT_JWPATH?>press" \>
            <?=$parObj->_getLabenames($SiteData,'title10','name');?>
            </a></li>
          <li><a href="<?=ROOT_JWPATH?>jobs" \>
            <?=$parObj->_getLabenames($SiteData,'title11','name');?>
            </a></li>
          <li><a href="<?=ROOT_JWPATH?>partners" \>
            <?=$parObj->_getLabenames($SiteData,'title12','name');?>
            </a></li>
           
        </ul>
        
     </section>
<!--
      <script src="js/flaunt.js"></script>
-->
     <script>
			var _gaq=[['_setAccount','UA-20440416-10'],['_trackPageview']];
			(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
			g.src='//www.google-analytics.com/ga.js';
			s.parentNode.insertBefore(g,s)})(document,'script');
		</script>
     <?php
include("footer.php"); 
?>
