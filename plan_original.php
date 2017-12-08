<?php
session_start();

ini_set('display_errors',0);
error_reporting(E_ERROR | E_PARSE);

include_once('includeconfig.php');
include_once("includes/classes/class.service.php");
include_once("includes/classes/class.homepage.php");
include_once('includes/classes/class.CMS.php');

if($lanId=="")  $lanId=1;

$objGen     	= new General();	 
$objService		= new Service();
$objHome     	= new Homepage();
$parObj 		= new Contents('services_details.php');
$objCMS    		= new CMS($lanId);

$limitFrom		=	0;
$upperLimit		=	10;

//get the testimonial; title for displaying in homepage

$getService	=	$objService->_getAllContent($lanId);
//collecting data from the xml for the static contents
$returnData		= $parObj->_getTagcontents($xmlPath,'services','label');
$arrayData		= $returnData['general'];

// for service background section
$getHomeId	=	0;
if(isset($_SESSION['home']['HomeId']))
	$getHomeId			=	$_SESSION['home']['HomeId'];
else		
	$_SESSION['home']['HomeId']	=	$getHomeId;
	

if($getHomeId <> 0){
	$getHomeContent		=	$objHome->_displayHomeContent($getHomeId,$lanId);
	$bgImage			=	"./uploads/homepage/".$getHomeContent[0]['service_image'];
}

if($getHomeId == 0){
	$bgImage			=	"./images/services-image.jpg";
}

?>
<?php include("header.php"); ?>
<?php include("menu.php"); ?>

<div id="container">
  <div id="wraper_inner">
    <div class="breadcrumbs">
      <ul>
        <li>
          <?=$parObj->_getLabenames($arrayData,'newPageTxt','name');?>
          :</li>
        <li><a href="<?=ROOT_JWPATH?>index.php">
          <?=$parObj->_getLabenames($arrayData,'newHmeeTxt','name');?>
          </a></li>
        <li>></li>
        <li><a class="select">
          <?=$parObj->_getLabenames($arrayData,'newHeadTxt','name');?>
          </a></li>
      </ul>
    </div>
    <div class="heading"><span class="name">
      <?=$parObj->_getLabenames($arrayData,'newHeadTxt','name');?>
      </span> <span class="date"><strong>&gt; </strong><a href="<?=$backButtonLink?>" class="white">
      <?=$parObj->_getLabenames($arrayData,'newBackTxt','name');?>
      </a></span>
    </div>

	 <!--Sample MP3 display ends here-->
	 <!--<div class="shedule">
      <div class="video"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="410" height="261" title="service_video">
  <param name="movie" value="videoplayer.prt1.swf" />
  <param name="quality" value="high" />
  <embed src="videoplayer.prt1.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="410" height="261"></embed>
</object></div>
      <div class="detail">
        <h2>Un coach sportif professionnel qui élabore votre programme personnel</h2>
        <p>Jiwok est un service qui vous permet d'être coaché en musique! Avec Jiwok vous avez votre coach sportif professionnel dans votre lecteur mp3. C'est une manière simple, ludique et encourageante d'atteindre un objectif santé ou sportif en courant, sur un vélo d'intérieur, elliptique, tapis de course, tapis de marche, ou natation.</p>
        <p><span class="gry">Tous nos entraînements santé et sportifs ont été créés dans un laboratoire secret (mais en plein air) par des entraîneurs professionels de différentes fédérations françaises (plus de 50 ans d'expérience cumulés). </span></p>
        <p><span class="gry">Vous recevrez les conseils et encouragements des meilleurs pour rester en forme ou améliorer vos performances. </span><br />
        </p>
      </div>
      <div class="clear"></div>
    </div>-->
    <?php 
	for($i=0;$i<count($getService);$i++)
		{		
	?>
    <div class="shedule">
      <?=$objGen->_output($getService[$i]['service_description']);?>
    </div>
    <hr class="blu2" />
    <?php 
		} 
	?>	
	 <!--Sample MP3 display starts here-->
    <?php $sampleMp3	=	$objCMS->_getContent($a = 'SAMPLE_MP3', $lanId); ?>
	 <div class="services">
      <h2><?=$objGen->_output($sampleMp3['content_display_title']);?>&copy;</h2>
	   <?=$objGen->_output($sampleMp3['content_body']);?>
    </div>
    
    
    
    
	<div class="MP-3">
      <div class="plyer">
        <h2><?=$parObj->_getLabenames($arrayData,'samplmp3Head','name');?></h2>
        <em class="italic"><?=$parObj->_getLabenames($arrayData,'freesongs','name');?></em> <br />
        <?=$parObj->_getLabenames($arrayData,'sessionLasts','name');?>
        <div><!--<img src="images/music-plyer.jpg" alt="Jiwok" />-->
          <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="272" height="39" title="MP3 music">
          <?php
		  if($lanId	!=	5)
		  {?>
            <param name="movie" value="<?=ROOT_JWPATH?>audio_player.swf" />
          <?php
		  }
		  else
		  {?>
          	<param name="movie" value="<?=ROOT_JWPATH?>audio_player_pl.swf" />	
          <?php
		  }?>
            <param name="quality" value="high" />
          <?php
		  if($lanId	!=	5)
		  {?>
            <embed src="<?=ROOT_JWPATH?>audio_player.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="272" height="39"></embed>
          <?php
		  }
		  else
		  {?>
          	<embed src="<?=ROOT_JWPATH?>audio_player_pl.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="272" height="39"></embed>	
          <?php
		  }?>  
            
            
          </object>
        </div>
      </div>
      <em class="italic blu">* <?=$parObj->_getLabenames($arrayData,'samplmp3Note','name');?></em> </div>
	  <div class="clear"></div>
   
   
   
   
   
    <hr class="blu2" />		
    <?php $regSer	=	$objCMS->_getContent($b = 'Free_Registration_Service_Page', $lanId); ?>
    <div id="wraper_msg">
      <div class="heading-top">
        <?=$objGen->_output($regSer['content_display_title']);?>
      </div>
      <div class="btm">&nbsp;</div>
      <div class="container">
        <?=$objGen->_output($regSer['content_body']);?>
      </div>
    </div>
    <?php include("offerSection_NewDesig.php"); ?>
  </div>
</div>
<?php include("footer.php"); ?>
<script type="text/javascript">
swfobject.registerObject("FlashID");
</script>
