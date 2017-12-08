<?php
	session_start();
	include_once('includeconfig.php');
	include_once('includes/classes/class.faq.php');
	include_once('includes/classes/class.CMS.php');
	if($lanId=="")
		 $lanId=1;

	$objGen     	= new General();
	$objFaq     	= new Faq($lanId);
	$parObj 		= new Contents('faq.php');
	$objCMS			= new CMS();


	//get all faqs by language

	$getAllFaq		=	$objFaq->_getAll();
	$totalFaq		=	count($getAllFaq);
	
	

	//collecting data from the xml for the static contents
	$returnData		= $parObj->_getTagcontents($xmlPath,'faq','label');
	$arrayData		= $returnData['general'];
	//get contents of the post a ticket section
	$contents 			= $objCMS->_getContent($contentTitle,$lanId);

?>

<?php include("header.php"); ?>
<!--
<script src="js/flaunt.js"></script>
-->

  <div class="frame_inner">
    <div class="row-1"><div class="return">
		 <a href="<?=$backButtonLink?>" class="small"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'backTxtNew','name'),'UTF-8');?></a>
           
         </div>
         <div class="title">
          <ul class="bredcrumbs">
               <li><?=mb_strtoupper($parObj->_getLabenames($arrayData,'pageText','name'),'UTF-8');?></li>
        <li><a href="<?=ROOT_JWPATH?>index.php"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'homeText','name'),'UTF-8');?></a></li>
        <li>></li>
        <li><a href="#" class="select"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'helpNew','name'),'UTF-8');?></a></li>
           </ul>
           <p class="Q"> <?=$parObj->_getLabenames($arrayData,'help','name');?> </p>
      </div>
         
         </div>
      
       <form method="post" action="<?=ROOT_JWPATH?>ticket.php">
   <div class="ask-btn">
		<?=$objGen->_output($contents['content_body']);?>
	
		<input name="" type="submit" value="<?=mb_strtoupper($parObj->_getLabenames($arrayData,'askBtnTxt','name'),'UTF-8');?> >>>" class="ask-btn" />
		</div></form>
      
         <div class="clear"></div>
         
         
         
         
         
         <aside class="popular-issu">
           <h1><?=$parObj->_getLabenames($arrayData,'qusetHeadNew','name');?></h1>
           <ul>
		 <?php
	  $qstClassName="";
	  for($i=0;$i<$totalFaq;$i++){
	  	if($qstClassName=="blu"){
			$qstClassName="gry";
		}else{
			$qstClassName="blu";
		}

	  ?>
	  	<li class="<?php echo $qstClassName; ?>">
			<a href="#<?=base64_encode($getAllFaq[$i]['faq_id']);?>">
				<?=$objGen->_output($getAllFaq[$i]['manager_question']);?>
			</a>
		</li>
	  <?php } ?>
           </ul>
          
           <a href="<?=ROOT_JWPATH?>userreg1.php" class="sign-up-btn">
          <?=mb_strtoupper($parObj->_getLabenames($arrayData,'signUpTxtNew','name'),'UTF-8');?><br /><?=mb_strtoupper($parObj->_getLabenames($arrayData,'nowTxtNew','name'),'UTF-8');?>
           <span><?=mb_strtoupper($parObj->_getLabenames($arrayData,'signUpCaptionNew','name'),'UTF-8');?></span>
           </a>
           <form method="post" action="<?=ROOT_JWPATH?>ticket.php">

		<?=$objGen->_output($contents['content_body']);?>
	
		<input name="" type="submit" value="<?=mb_strtoupper($parObj->_getLabenames($arrayData,'askBtnTxt','name'),'UTF-8');?>" class="ask-btn2" />
		</form>
          
         </aside>
         
         
         
         
         
         
         <section class="answers" itemscope itemtype="http://schema.org/Question">
            <h2 itemprop="name" ><?=$parObj->_getLabenames($arrayData,'ansHeadNew','name');?></h2>
           <ul id="answer">
			   <?php
	  	$qstClassName="";
	  	for($i=0;$i<$totalFaq;$i++){
			if(($i%2) == 0)
						{
							$qstClassName	=	'';	
						}
					else
						{
							$qstClassName	=	'gry';	
						}
			if($_SERVER['HTTP_HOST'] == "www.jiwok.com.jiwok-wbdd2.najman.lbn.fr")
				{ 
							$getAllFaq[$i]['manager_answer']	 =	str_replace("http://www.jiwok.com","http://www.jiwok.com.jiwok-wbdd2.najman.lbn.fr",$getAllFaq[$i]['manager_answer']);
				}
	  ?>
	  
       <li class="<?php echo $qstClassName; ?>">
       
	   	 <a name="<?=base64_encode($getAllFaq[$i]['faq_id']);?>"></a>
	   	 
         <h3 class="qst" itemprop="text"><?=nl2br($objGen->_output($getAllFaq[$i]['manager_question']));?> </h3>
           <span itemprop="suggestedAnswer acceptedAnswer" itemscope itemtype="http://schema.org/Answer">
           <span itemprop="text"><?=nl2br($objGen->_output($getAllFaq[$i]['manager_answer']));?> </span>
           </span>
           
       </li>
       <?php } ?>
       
			 
           </ul>
         
         </section>
         
         
         
         
         
         
     </div>
     
     <?php include("footer.php"); ?>
