<?php
include_once("includes/classes/class.testimonials.php");$objtestimoni	=	new Testimonial();// get corresponding testimonial according to the home page$fromLimit		=	0;$upLimit		=	4;$getTestimoni	=	$objtestimoni->_displayPage($getHomeId,$lanId,$fromLimit,$upLimit);

//~ echo "<pre>";
//~ print_r($getTestimoni);
//~ exit;?>
<?php if($getTestimoni)  { ?>
<div id="Testimonial">
    <h2><img src="images/testmonial_Icon.gif"  alt="banner" /><?=$parObj->_getLabenames($arrayData,'testimonials','name');?></h2>
    
    <ul id="TestimonialTab">
<?php  for($i=0;$i<count($getTestimoni);$i++) {?>
    <li>
      <div class="Temoignages"><?php if($getTestimoni[$i]['user_image']) { ?><img src="uploads/testimonials/<?=$getTestimoni[$i]['user_image']?>" width="63" height="55" alt="banner" /> <?php } else {?><img src="images/testmonio-thump.png" alt="banner" /> <?php } ?>
      <div class="Temoignages-frame"><img src="images/bg_thumb_small.png" alt="banner" /></div>
      </div>
      <div class="Temoignages_Comment">
        <p><?=substr($objGen->_output($getTestimoni[$i]["testimonial_desc"]),0,145)."...";?></p>
      <p><strong><a href="testimonial_details.php"><?=$parObj->_getLabenames($arrayData,'moredet','name');?></a></strong></p>
      </div>
      <div class="CommentersName">Par : <span><?=$objGen->_output($getTestimoni[$i]["user_name"]);?></span></div>
     </li>
<?php } ?>
	</ul>
     <div class="clear"></div>
   </div>
<?php } ?>