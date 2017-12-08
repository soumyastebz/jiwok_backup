<?php
if(isset($_SESSION['language']['langId']))
  	{
  	 	$blogPageUrl	= "";
  		if($_SESSION['language']['langId']==1)
			{		
			  	$blogPageUrl.="http://blog.jiwok.com/en/";		
			  	$giftPageUrl.="gift";		
			}
		else		
			{		
		  		$blogPageUrl.=ROOT_JWPATH."blog/";		
		 		$giftPageUrl.="bon-cadeau-jiwok";		
			}
	}
$coachUrl = "coach-sportif"; 
switch($lanId){
	
	case	1 : $coachUrl = "coach-athlete";
				break;
	case	2 : $coachUrl = "coach-sportif";
				break;
	case	3 : $coachUrl = "entrenador-atleta";
				break;
	case	4 : $coachUrl = "allenatore-atleta";
				break;
}
$returnData		= $parObj->_getTagcontents($xmlPath,'payments','label');
$payment_menu		= $returnData['general'];
?>
<div class="foot-nav frame">
       
        <!------------for index page footer menu------------->
        <?php
		if($page_name == "index.php"){
		?>
         <ul class="nav_01">
          <li><?php 
			if($_SESSION['user']['userId'] != "")
			{ 
			?>
				<a href="<?=ROOT_JWPATH?>userArea.php"><?=$parObj->_getLabenames($arrayDataHead,'myhome','name');?></a><?php
			} 
			else 
			{ ?>
<!--
            	<a href="#">
            	<?=$parObj->_getLabenames($arrayDataHead,'home','name');?>
            	</a>
-->
            	<?php
			} 
			?>
          </li>
          <li><a href="<?=ROOT_JWPATH?><?php echo ($lanId != 1) ? 'entrainement' :'training';?>"><?=$parObj->_getLabenames($arrayDataHead,'tryWorkOut','name');?> </a></li>
         <!-- <li><a href="#">VOTRE ENTRAINEMENT SUR MESURE</a></li>-->
         <?php  if($lanId  == 5)
	     { ?>
			<li><a href="<?=ROOT_JWPATH?>plan.php">O treningach</a></li><?php }	?>
            
          <li><a href="<?=ROOT_JWPATH?><?php echo $coachUrl; ?>"><?=$parObj->_getLabenames($arrayDataHead,'coachesNew','name');?></a></li>
          <?php if($lanId	==	5)
			{?>
                 <li><a href="<?=ROOT_JWPATH?>payment.php"><?=$parObj->_getLabenames($payment_menu,'newHeadTxt','name');?></a></li>
                 <li><a href="http://www.facebook.com/Jiwokpl" >Facebook</a></li> 
          <?php
			}
			else
			{?>
                    <li><a href="<?php echo $blogPageUrl; ?>" ><?=$parObj->_getLabenames($arrayDataHead,'blogNew','name');?></a></li>
            <?php 
            }
			// Only french version contains FORUM
			if($lanId == 2) 
			{?>
			
			<li><a href="<?=ROOT_JWPATH?>forum/" ><?=$parObj->_getLabenames($arrayDataHead,'fourmNew','name');?></a></li>
			<?php 
			}?>
            <?php /*contents.php?title=contactusnew?>*/?>
            <li><a href="<?=ROOT_JWPATH?>contact-us"><?=$parObj->_getLabenames($arrayDataHead,'contactNew','name');?></a></li>
            <li><a href="<?=ROOT_JWPATH?>faq"><?=$parObj->_getLabenames($arrayDataHead,'helpNew','name');?></a></li>
             </ul> 
          <?php } 
		  else { ?>
           <ul class="nav_03 no-line">
          <li><a href="<?=ROOT_JWPATH?>faq"><?=$parObj->_getLabenames($arrayDataHead,'helpNew','name');?></a></li>
          <li> <a href="<?=ROOT_JWPATH?>sitemap.php"><?=$parObj->_getLabenames($arrayDataFooter,'sitemap','name');?></a></li>
          <li><a href="<?=ROOT_JWPATH?>about-us"><?=$parObj->_getLabenames($arrayDataFooter,'aboutus','name');?></a></li>
         <?php /*?> <li> <a href="<?=ROOT_JWPATH?>terms-and-conditions"><?=$parObj->_getLabenames($arrayDataFooter,'terms','name');?></a></li><?php */?>
          <li> <a href="<?=ROOT_JWPATH?>contents.php?title=termsnew"><?=$parObj->_getLabenames($arrayDataFooter,'terms','name');?></a></li>
          </ul>
          <?php
		  }
		  ?>
        
     </div>
     
    
