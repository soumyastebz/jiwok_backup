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
		  		$blogPageUrl.=JIWOK_URL."blog/";		
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
<div class="top-nav">
        <nav class="nav">    
					<ul class="nav-list">
						<li class="nav-item"><?php 
	if($_SESSION['user']['userId'] != "")
			{ ?>
            	<a href="<?=ROOT_JWPATH?>userArea.php"><?=$parObj->_getLabenames($arrayDataHead,'myhome','name');?></a>
				<?php
			} 
		else 
			{	?>
            	<a href="<?=ROOT_JWPATH?>"><?=$parObj->_getLabenames($arrayDataHead,'home','name');?></a>
				<?php
			} 	?>
            </li>
            <li class="nav-item"><a href="<?=ROOT_JWPATH?><?php echo ($lanId != 1) ? 'entrainement' :'training';?>"><?=$parObj->_getLabenames($arrayDataHead,'tryWorkOut','name');?> </a></li>
            
             <?php  if($lanId  == 5){ ?>
			 <li class="nav-item"><a href="<?=ROOT_JWPATH?>plan.php">O treningach</a></li>
			 <?php }	?>
              <li class="nav-item"><a href="<?=ROOT_JWPATH?><?php echo $coachUrl; ?>"><?=$parObj->_getLabenames($arrayDataHead,'coachesNew','name');?></a></li>
             <?php   if($lanId	==	5){?>
    	 	 <li class="nav-item"><a href="<?=ROOT_JWPATH?>payment.php"><?=$parObj->_getLabenames($payment_menu,'newHeadTxt','name');?></a></li>
		     <li class="nav-item"><a href="http://www.facebook.com/Jiwokpl" >Facebook</a></li>        
    		<?php
			}
			else
			{?>
    			 <li class="nav-item"><a href="<?php echo $blogPageUrl; ?>" ><?=$parObj->_getLabenames($arrayDataHead,'blogNew','name');?></a></li>
			<?php 
            }
			// Only french version contains FORUM
			if($lanId == 2) 
			{?>
                  <li class="nav-item"><a href="<?=ROOT_JWPATH?>forum/" ><?=$parObj->_getLabenames($arrayDataHead,'fourmNew','name');?></a></li>        
             <?php }?>
                        <li class="nav-item"><a href="<?=ROOT_JWPATH?>contact-us"><?=$parObj->_getLabenames($arrayDataHead,'contactNew','name');?></a></li>
                        <li class="nav-item"><a href="<?=ROOT_JWPATH?>faq"><?=$parObj->_getLabenames($arrayDataHead,'helpNew','name');?></a></li>
                       
					</ul>
                    
				</nav>
                
     </div>
