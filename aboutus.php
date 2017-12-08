<?php
/*--------------------------------------------------*/
// Project 		: Jiwok
// Created on	: 05-05-2011
// Created by	: Ganga
// Purpose		: New Design Integration - About Us 
/*--------------------------------------------------*/

$aboutus 		= $objCMS->_getContent($t = 'ABOUTUS', $lanId);
$aboutus_a 		= $objCMS->_getContent($a = 'ABOUTUS_A', $lanId);
$aboutus_b 		= $objCMS->_getContent($b = 'ABOUTUS_B', $lanId);
$aboutus_c 		= $objCMS->_getContent($c = 'ABOUTUS_C', $lanId);
$aboutus_f 		= $objCMS->_getContent($f = 'ABOUTUS_FNEW', $lanId);
$aboutus_g 		= $objCMS->_getContent($g = 'ABOUTUS_GNEW', $lanId);
if($lanId	==	5)
{
	$aboutus_Magda 		= $objCMS->_getContent($f = 'COACH_MagdaNEW', $lanId);
	$aboutus_Filip 		= $objCMS->_getContent($g = 'COACH_FilipNEW', $lanId);
	$aboutus_Jan 		= $objCMS->_getContent($g = 'COACH_JanNEW', $lanId);
}
?>


<!--=================================-->

<div class="frame_inner">
<ul class="bredcrumbs" style="padding-bottom:30px">
               <li><a><?=mb_strtoupper(($parObj->_getLabenames($arrayData,'searchPath','name')),'UTF-8');?></a><a href="<?=ROOT_JWPATH?>index.php"> <?=strtoupper($parObj->_getLabenames($arrayData,'homeName','name'));?>
          </a></li>
          <li><a>&gt;</a></li>
               <li><?=mb_strtoupper($objGen->_output($aboutus['content_display_title']),'UTF-8');?></li>
            </ul>

      <div class="frame-about">
           <ul class="teams">
              <li>
                 <figure><img src="<?=ROOT_FOLDER?>images/denis-dhekaier.jpg" alt="denis-dhekaier"></figure>
                 <figcaption>Denis Dhekaier</figcaption>
              </li>
             <li>
                 <figure><img src="<?=ROOT_FOLDER?>images/frederic-najman.jpg" alt="frederic-najman"></figure>
                 <figcaption>Frédéric Najman</figcaption>
              </li>
           
             
           </ul>
           
           <ul class="jw_entertain">
              <li class="clearfix"><p class="content">
              <h2><?=$objGen->_output($aboutus_a['content_display_title']);?></h2>
              <?=strip_tags($objGen->_output($aboutus_a['content_body']));?></p>
              <div class="content"><?=$objGen->_output($aboutus_b['content_body']);?></div> 
                  <span class="button"><a href="<?=ROOT_JWPATH?>coaches.php"><input type="button" value="<?=mb_strtoupper($parObj->_getLabenames($arrayData,'aboutus-coach-button','name'),'UTF-8');?>"></a></span></li>
                  
             <li class="clearfix"><div class="content"><?=$objGen->_output($aboutus_c['content_body']);?></div> 
                  <span class="button"><a href="<?=ROOT_JWPATH?>uploads/"><input type="button" value="<?=mb_strtoupper($parObj->_getLabenames($arrayData,'aboutus-download-button','name'),'UTF-8');?>"></a></span></li>    
           </ul>
           
           <div class="team_pro">
              <?=$objGen->_output($aboutus_g['content_body']);?>
           
           </div>
           
           <div class="team_pro">
             <?=$objGen->_output($aboutus_f['content_body']);?>    
          
           </div>
           <?php
	if($lanId	==	5)
	{?>
		<div class="team_pro">
	  <?=$objGen->_output($aboutus_Jan['content_body']);?>      
          </div>
    	<div class="team_pro">
	  <?=$objGen->_output($aboutus_Filip['content_body']);?>      
    </div>
    	<div class="team_pro">
	  <?=$objGen->_output($aboutus_Magda['content_body']);?>      
    </div>
     W niespełna 5 miesięcy postawiliśmy na nogi Jiwok Polska.Jesteśmy dumni z tej inicjatywy, ale i pełni pokory. Jeśli macie uwagi co do działania serwisu, napiszcie: <a href='mailto:kontakt@jiwok.pl' style='color:#00F;'>kontakt@jiwok.pl</a>! Nie ma nic lepszego niż konstruktywna krytyka. Czekamy na feedback z Waszej strony!
    <?php	
	}
	?>  
           <div class="btn-team_pro"><a href="<?=ROOT_JWPATH?>userreg1.php"><input class="btn_orng_j1" value="<?=mb_strtoupper($parObj->_getLabenames($arrayData,'aboutus-button','name'),'UTF-8');?>" type="button"></a></div>
      </div>
    </div>
<!--==================================-->
