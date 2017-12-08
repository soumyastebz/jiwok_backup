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
<script src="js/flaunt.js"></script>
<script type="text/javascript">
	function showOnlyQuestionAnswer(qstId)
	{ 
		//$(".class").css("display", "none");
		$('ul#answer li').css('display', 'none');
		$("#myLi_"+qstId).css("display", "block");	
		
		/*var	vv	= Base64.encode(qstId);
		var a = document.getElementById('qs_'+qstId);
		a	=	a.href;
		var imgchg=a.replace("javascript:void(0);","faq/question="+qstId);
		$('#qs_'+qstId).attr("href",imgchg);*/
			
	}
</script>
  <div class="frame_inner">
    <div class="row-1"><div class="return">
		 <a href="<?=$backButtonLink?>" class="small"><?=$parObj->_getLabenames($arrayData,'backTxtNew','name');?></a>
           
         </div>
         <div class="title">
          <ul class="bredcrumbs">
               <li><?=$parObj->_getLabenames($arrayData,'pageText','name');?></li>
        <li><a href="<?=ROOT_JWPATH?>index.php"><?=$parObj->_getLabenames($arrayData,'homeText','name');?></a></li>
        <li>></li>
        <li><a href="#" class="select"><?=$parObj->_getLabenames($arrayData,'helpNew','name');?></a></li>
           </ul>
           <p class="Q"> <?=$parObj->_getLabenames($arrayData,'help','name');?> </p>
      </div>
         
         </div>
      
       <form method="post" action="<?=ROOT_JWPATH?>ticket.php">
   <div class="ask-btn">
		<?=$objGen->_output($contents['content_body']);?>
	
		<input name="" type="submit" value="<?=$parObj->_getLabenames($arrayData,'askBtnTxt','name');?> >>>" class="ask-btn" />
		</div></form>
      
         <div class="clear"></div>
         
         
         
         
         
         <aside class="popular-issu">
           <h3><?=$parObj->_getLabenames($arrayData,'qusetHeadNew','name');?></h3>
           <ul>
			   <?php
	  $qstClassName="";
	  foreach($getAllFaq as $key=>$val)
			{
				$faqIds	[] =	$val['faq_id']	;
				
			}
	  for($i=0;$i<$totalFaq;$i++)
	  	{
			
			if($qstClassName=="blu")
				{
					$qstClassName="gry";
				}
			else{
					$qstClassName="blu";
				}
		
		if($_SERVER['HTTP_HOST'] == "10.0.0.8")
				{ 
					$getAllFaq[$i]['manager_answer']	 =	str_replace("http://www.jiwok.com","http://www.jiwok.com.jiwok-wbdd2.najman.lbn.fr",$getAllFaq[$i]['manager_answer']);
				}
		
	  ?>
          
              	<li class="<?php echo $qstClassName; ?>">
        <noscript>
     <style>#qs_<?=$getAllFaq[$i]['faq_id']?> { display: none; }</style>
     <a  href="<?= JIWOK_URL?>faq/question=<?=base64_encode($getAllFaq[$i]['faq_id']);?>"> 
				<?=$objGen->_output($getAllFaq[$i]['manager_question']);?>
			</a>
   </noscript>   
			<a id="qs_<?=$getAllFaq[$i]['faq_id']?>" href="javascript:void(0);" onClick="showOnlyQuestionAnswer(<?=$getAllFaq[$i]['faq_id']?>)">
				<?=$objGen->_output($getAllFaq[$i]['manager_question']);?>
			</a>
		</li>
	  <?php } ?> 
           </ul>
          
           <a href="<?=ROOT_JWPATH?>userreg1.php" class="sign-up-btn">
          <?=$parObj->_getLabenames($arrayData,'signUpTxtNew','name');?><br /><?=$parObj->_getLabenames($arrayData,'nowTxtNew','name');?>
           <span><?=$parObj->_getLabenames($arrayData,'signUpCaptionNew','name');?></span>
           </a>
           <form method="post" action="<?=ROOT_JWPATH?>ticket.php">

		<?=$objGen->_output($contents['content_body']);?>
	
		<input name="" type="submit" value="<?=$parObj->_getLabenames($arrayData,'askBtnTxt','name');?>" class="ask-btn2" />
		</form>
          
         </aside>
         
         
         
         
         
         
         <section class="answers">
            <h3><?=$parObj->_getLabenames($arrayData,'ansHeadNew','name');?></h3>
           <ul id="answer">
			   
			   <?php
	 	 if(isset($_GET['question']))
			{
				
				$fid			=	base64_decode($_GET['question']);				
				$getoneFaq		=	$objFaq-> _getFaqbyId($fid);
				
               if(in_array($fid,$faqIds))
			   	{
					
					$pos = array_search($fid,$faqIds);
					if(($pos%2) == 0)
						{
							$qstClassName	=	'blu';	
						}
					else
						{
							$qstClassName	=	'gry';	
						}
				}
				?>
				<li class="<?php echo $qstClassName; ?>" >
	   	 		<a name="<?=base64_encode($getoneFaq[0]['faq_id']);?>"></a>
        		  <span class="qst"><?=nl2br($objGen->_output($getoneFaq[0]['manager_question']));?> </span>
         		<span class="ans"><?=nl2br($objGen->_output($getoneFaq[0]['manager_answer']));?></span> 
      			 </li>
	<?php	}else
			{
	  	$qstClassName="";
	  	for($i=0;$i<$totalFaq;$i++){
			if($qstClassName=="blu"){
				$qstClassName="gry";
			}else{
				$qstClassName="blu";
			}
	  ?>
	  
       <li class="<?php echo $qstClassName; ?>" id="myLi_<?=$getAllFaq[$i]['faq_id'];?>">
	   	 <a name="<?=base64_encode($getAllFaq[$i]['faq_id']);?>"></a>
         <span class="qst"><?=nl2br($objGen->_output($getAllFaq[$i]['manager_question']));?> </span>
         <span class="ans"><?=nl2br($objGen->_output($getAllFaq[$i]['manager_answer']));?> </span>
       </li>
       <?php } } ?>
			   
			 
           </ul>
         
         </section>
         
         
         
         
         
         
     </div>
     
     <?php include("footer.php"); ?>
