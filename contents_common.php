<?php
switch($contentTitle){
	//~ case 'press':
		//~ $searchPath	=	$parObj->_getLabenames($arrayData,"searchPath","name");
		//~ $homeName	=	$parObj->_getLabenames($arrayData,"homeName","name");
		//~ $newPgeTxt	=	$contents['content_display_title'];
		//~ $newBackTxt	=	$parObj->_getLabenames($arrayData,"newBackTxt","name");
		//~ $homeUrl	=	ROOT_JWPATH.'index.php';
	//~ break;
	case 'termsnew':
		$searchPath	=	$parObj->_getLabenames($arrayData,"searchPath","name");
		$homeName	=	$parObj->_getLabenames($arrayData,"homeName","name");
		$newPgeTxt	=	$contents['content_display_title'];
		$newBackTxt	=	$parObj->_getLabenames($arrayData,"newBackTxt","name");
		$homeUrl	=	ROOT_JWPATH.'index.php';
	break;
	case 'TERMS_A':
		$searchPath	=	$parObj->_getLabenames($arrayData,"searchPath","name");
		$homeName	=	$parObj->_getLabenames($arrayData,"homeName","name");
		$newPgeTxt	=	$contents['content_display_title'];
		$newBackTxt	=	$parObj->_getLabenames($arrayData,"newBackTxt","name");
		$homeUrl	=	ROOT_JWPATH.'index.php';
	break;
	case 'TERMS_B':
		$searchPath	=	$parObj->_getLabenames($arrayData,"searchPath","name");
		$homeName	=	$parObj->_getLabenames($arrayData,"homeName","name");
		$newPgeTxt	=	$contents['content_display_title'];
		$newBackTxt	=	$parObj->_getLabenames($arrayData,"newBackTxt","name");
		$homeUrl	=	ROOT_JWPATH.'index.php';
	break;
	case 'TERMS_C':
		$searchPath	=	$parObj->_getLabenames($arrayData,"searchPath","name");
		$homeName	=	$parObj->_getLabenames($arrayData,"homeName","name");
		$newPgeTxt	=	$contents['content_display_title'];
		$newBackTxt	=	$parObj->_getLabenames($arrayData,"newBackTxt","name");
		$homeUrl	=	ROOT_JWPATH.'index.php';
	break;
	case 'partnersnew':
		$searchPath	=	$parObj->_getLabenames($arrayData,"searchPath","name");
		$homeName	=	$parObj->_getLabenames($arrayData,"homeName","name");
		$newPgeTxt	=	$contents['content_display_title'];
		$newBackTxt	=	$parObj->_getLabenames($arrayData,"newBackTxt","name");
		$homeUrl	=	ROOT_JWPATH.'index.php';
	break;
	case 'jobnew':
		$searchPath	=	$parObj->_getLabenames($arrayData,"searchPath","name");
		$homeName	=	$parObj->_getLabenames($arrayData,"homeName","name");
		$newPgeTxt	=	$contents['content_display_title'];
		$newBackTxt	=	$parObj->_getLabenames($arrayData,"newBackTxt","name");
		$homeUrl	=	ROOT_JWPATH.'index.php';
	break;
	case 'contactusnew':
		$searchPath	=	$parObj->_getLabenames($arrayData,"searchPath","name");
		$homeName	=	$parObj->_getLabenames($arrayData,"homeName","name");
		$newPgeTxt	=	$contents['content_display_title'];
		$newBackTxt	=	$parObj->_getLabenames($arrayData,"newBackTxt","name");
		$homeUrl	=	ROOT_JWPATH.'index.php';
	break;
	default:
		$contentTitle	=	base64_decode($_REQUEST['title']);// get title form the request	
		$contents 		= 	$objCMS->_getContent($contentTitle,$lanId);//get contents according to the title
		$searchPath		=	$parObj->_getLabenames($arrayData,"searchPath","name");
		$homeName		=	$parObj->_getLabenames($arrayData,"homeName","name");
		$newPgeTxt		=	$contents['content_display_title'];
		$newBackTxt		=	$parObj->_getLabenames($arrayData,"newBackTxt","name");
		$homeUrl		=	ROOT_JWPATH.'index.php';
	break;
}

$header='<div class="breadcrumbs">
		  <ul>
			<li>'.$searchPath.' :</li>
			<li><a href="'.$homeUrl.'">'.$homeName.'</a></li>
			<li>></li>
			<li><a href="#" class="select">'.$newPgeTxt.'</a></li>
		  </ul>
		</div>';
?>

<? if($contentTitle=='contactusnew'){?>
 <?php $newHeadTxt=$contents['content_display_title']; ?>
 <section class="banner-static contents-area">
	  <div class="bred-hovr second">
          <ul class="bredcrumbs">
    <?=$header?> </ul>
       </div>

       <div class="bnr-content" style="position:relative;">
          <div class="frame slider-first">
<div class="callbacks_container">
      <ul class="rslides callbacks callbacks1" id="slider4">
        <li><img src="<?=ROOT_FOLDER?>images/contact_new.jpg" alt="Slide 01"> </li>
		</ul>
    </div>
         </div>     
                  <div class="heading4JW"><p><?php echo $newHeadTxt; ?></p></div> 
                  <?php echo $contents['content_body']; ?>
                 
               
           </div>
 </section>
<?php }
else if($contentTitle=='partnersnew') {?>
	 <div class="frame_inner">
 <div class="row-1"><div class="return">
         <a href="<?=$backButtonLink?>" class="white"><?php echo $newBackTxt; ?></a>
         </div>
         <div class="title">
          <ul class="bredcrumbs">
              <?=$header?>
           </ul>
           <p class="Q"><?php echo "Les partenaires Jiwok"; ?></p>
      </div>
         
         </div>
       <div class="clear"></div>
        <div class="partners_JW">
		  <?php echo trim($contents['content_body']); ?>	
</div></div>
</div>
<?php }else if($contentTitle=='jobnew'){?>
	<?php $newHeadTxt=$contents['content_display_title']; ?>
 <section class="banner-static contents-area">
	  <div class="bred-hovr second">
          <ul class="bredcrumbs">
    <?=$header?> </ul>
       </div>
        <div class="bnr-content" style="position:relative;">
          <div class="frame slider-first">
<div class="callbacks_container">
      <ul class="rslides callbacks callbacks1" id="slider4">
        <li><img src="<?=ROOT_FOLDER?>images/contact_new.jpg" alt="Slide 01"> </li>
		</ul>
    </div>
         </div>
                  <div class="heading4JW"><p><?php echo $newHeadTxt; ?></p></div> 
                  <?php echo $contents['content_body']; ?>
                 
               
           </div>
 </section>
	<?php } else if($contentTitle=='termsnew'){?>
		<div class="frame_inner">
 <div class="row-1"><div class="return">
         <a href="<?=$backButtonLink?>" class="white"><?php echo $newBackTxt; ?></a>
         </div>
         <div class="title">
          <ul class="bredcrumbs">
              <?=$header?>
           </ul>
           <p class="Q"><?php echo $newPgeTxt; ?></p>
      </div>
         
         </div>
       <div class="clear"></div>
        <div class="partners_JW">
	<?php echo $contents['content_body']; ?></div></div>

<?php }else{ ?>
<?	echo $contentTitle;exit;?>
<?php $newHeadTxt=$contents['content_display_title']; ?>
<div id="container">
  <div id="wraper_inner">
    <?=$header?>
	<div class="heading"><span class="name"><?php echo $newHeadTxt; ?></span> <span class="date"><strong>&gt; </strong><a href="<?=$backButtonLink?>" class="white"><?php echo $newBackTxt; ?></a></span></div>
	
	<?php if($lanId==5 && $contentTitle== 'TERMS_ANEW')
	{

	?>
    	<div align="center" style="border:thick solid; width:80px; float:right; margin:2px"><a href="" target="_self" id="prints" >Drukuj</a> </div>
		<div align="center" style="border:thick solid; width:80px; float:right; margin:2px"><a href="terms_download.php?id=A" target="_self" >Pobierz</a> </div>
	<?php 	
	}
	elseif($lanId==5 && $contentTitle== 'TERMS_B')
	{
	
	?>
    <div align="center" style="border:thick solid; width:80px; float:right; margin:2px"><a href="" target="_self" id="prints" >Drukuj</a> </div>
    <div align="center" style="border:thick solid; width:80px; float:right; margin:2px"><a href="terms_download.php?id=B" target="_self" >Pobierz</a> </div>
	<?php 	
	}
	elseif($lanId==5 && $contentTitle== 'TERMS_C')
	{
	
	?>
    <div align="center" style="border:thick solid; width:80px; float:right; margin:2px"><a href="" target="_self" id="prints" >Drukuj</a> </div>
    <div align="center" style="border:thick solid; width:80px; float:right; margin:2px"><a href="terms_download.php?id=C" target="_self" >Pobierz</a> </div>
	<?php 	
	}
	?>
	<div id="divToPrint"><?php echo $contents['content_body']; ?></div>

  </div>
</div>
 <?php }?>
<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>-->
<script type="text/javascript" src="jquery/jquery-1.4.3.min.js"></script>
<!--
 <script src="<?=ROOT_FOLDER?>js/flaunt.js"></script>
-->
<script src="jquery/jquery.jqprint-0.3.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
$(function() {
                $("#prints").click( function() {
				    $('#divToPrint').jqprint();
                    return false;
                });
             });
</script>
<!---style
<div>W razie jakichkolwiek pyta? dotycz?cych naszych partner&oacute;w, informacji dla prasy lub rejestracji w serwisie Jiwok, prosimy o bezpo?redni kontakt na adres:<a href="mailto:kontakt@jiwok.pl">kontakt@jiwok.pl.</a> W przypadku pozosta?ych pyta? zwi?zanymi z problemami technicznymi lub z treningiem prosimy klikn?? poni?ej, otrzymacie Pa?stwo mo?liwie najszybsz? odpowied?:<br />
<a href="http://www.jiwok.com/pl/ticket.php" title="Click Here"><input class="btn-sign" name="" style="float:right;display:block; color:#ffffff; border:2px solid #E68023;background:#E68023;padding:15px 53px; border-radius:3px;padding:6px 53px;float:none; display:block;" type="button" value="Zadaj pytanie" /></a></div>

-->
