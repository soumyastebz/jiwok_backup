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

$getService	=	$objService->_getAllContent($lanId);//print_r($getService);exit;
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
	
  equalheight('.JI_training-2 ul.team li');
});
$(window).resize(function(){
  equalheight('.JI_training-2 ul.team li');
});
</script>

  <section class="breadcrumbs">
     
     	<div class="frame3">
          <ul class="bredcrumbs plancrumbs">
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
     </section>
      <section class="plan-title">
        <div class="frame3">
        
        <div class="top-wrapper">
        <h1><?=$parObj->_getLabenames($arrayData,'newHeadTxt','name');?></h1>
        <p><a href="<?=$backButtonLink?>" style="color:#fff; font-size:16px; padding-top:18px;"> > <?=$parObj->_getLabenames($arrayData,'newBackTxt','name');?></a></p>
        </div>
                
        
         <!--<div class="row-1" style="padding-bottom:0"><div class="return">
           <a href="<?=$backButtonLink?>" class="small"><?=$parObj->_getLabenames($arrayData,'newBackTxt','name');?></a>
         </div>
         <div class="title">
          
           <h3 class="plan-hed-2"><?=$parObj->_getLabenames($arrayData,'newHeadTxt','name');?></h3>
      </div>
         
         </div>-->
         </div>
         </section>
         
         <section class="plan-list">
         	<div class="frame3">
            	<ul>
                	
						  <?php 
	for($i=0;$i<count($getService);$i++)
		{		
	?><li>
                    	 <?=$objGen->_output($getService[$i]['service_description']);?> </li>
                          <?php 
		} 
	?>	
                   
                    	
                    
                </ul>
            </div>
         </section>
         
          <section class="inscription01 plan-content-middle">
         	<div class="frame3">
            	<div class="inscr01-left">
					 <?php $sampleMp3	=	$objCMS->_getContent($a = 'SAMPLE_MP3_NEW', $lanId);?>
                	<h3><?=$objGen->_output($sampleMp3['content_display_title']);?>&copy;</h3>
                    <article>
                    	
                       
                       <?=$objGen->_output($sampleMp3['content_body']);?>
						
                    </article>
                    
                </div>
                
                
                
                <div class="inscr01-right">
                
                
                
                	<div class="plan-audio">
                    	<h5><?=$parObj->_getLabenames($arrayData,'samplmp3Head','name');?></h5>
                        <p><span style="font-weight:bold;color:#fff;"><?=$parObj->_getLabenames($arrayData,'freesongs','name');?></span><?=$parObj->_getLabenames($arrayData,'sessionLasts','name');?></p>
                        <div class="plan-player">
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
                   <div><!--<img src="images/music-plyer.jpg" alt="Jiwok" />-->
         
        </div>
                      <span>
                        	<sup>*</sup><?=$parObj->_getLabenames($arrayData,'samplmp3Note','name');?>
                        </span>
                </div>
            </div>
         </section>
          <?php $regSer	=	$objCMS->_getContent($b = 'Free_Registration_Service_Page_new', $lanId);?>
          <section class="inscription02">
         	<div class="frame3">
            	
                	<h3><?=$objGen->_output($regSer['content_display_title']);?></h3>
                    <article>
                    	 <?=$objGen->_output($regSer['content_body']);?>
                    </article>
            </div>
         </section>
     
     

    <!--  <section class="plan plan-content-bottom-plan">
        <div class="frame3">
        
         <div class="payment-outer">
            <div class="pament-tables first">
                 <div class="colums">
                   <h3>12 miesięcy</h3>
                   <span class="span_top">21.00zł/miesiąc <br>zamiast35zł</span>
                   <div class="label"><img src="images/252.png"></div>
                   <div class="span_bottom"><span>-50%</span>par rapport au plein tarif</div>
                 </div>
                 <a href="#" class="choose">Wybierz swój abonament</a>
            
            </div>
            <div class="pament-tables">
                 <div class="colums">
                   <h3>12 miesięcy</h3>
                   <span class="span_top">21.00zł/miesiąc <br>zamiast35zł</span>
                   <div class="label"><img src="images/150.png"></div>
                   <div class="span_bottom"><span>-50%</span>par rapport au plein tarif</div>
                 </div>
                 <a href="#" class="choose">Wybierz swój abonament</a>
            
            </div>
            <div class="pament-tables">
                 <div class="colums">
                   <h3>12 miesięcy</h3>
                   <span class="span_top">21.00zł/miesiąc <br>zamiast35zł</span>
                   <div class="label"><img src="images/85.png"></div>
                   <div class="span_bottom"><span>-50%</span>par rapport au plein tarif</div>
                 </div>
                 <a href="#" class="choose">Wybierz swój abonament</a>
            
            </div>
            <div class="pament-tables">
                 <div class="colums">
                   <h3>12 miesięcy</h3>
                   <span class="span_top">21.00zł/miesiąc <br>zamiast35zł</span>
                   <div class="label"><img src="images/35.png"></div>
                   <div class="span_bottom"><span>-50%</span>par rapport au plein tarif</div>
                 </div>
                 <a href="#" class="choose">Wybierz swój abonament</a>
            
            </div>
        </div>
         <div class="workout">
           <h3>Za co dok?adnie p?ac? korzystaj?c z serwisu Jiwok?</h3>
           <p>W ten prosty sposób, za pomocą pojedynczego kliknięcia, możesz zrobić bliskiej Ci osobie oryginalny, praktyczny i sportowy prezent: Treningi sportowe z osobistym trenerem na mp3! .
To naprawdę proste: przesyłasz bon e-mailem lub drukujesz</p>
<a href="#" class="btn">Informations bancaires</a>
         </div>
         
      </div> 
     
      </section>
      
      <section class="plan-gift">
      	<div class="frame3">
        	<div class="gift-left">
            	<img src="images/plan-gift.jpg" alt="" />
            </div>
            <div class="gift-right">
            	<h5>Par ailleurs, Jiwok propose des bons cadeaux.</h5>
                <p>Ainsi, vous pouvez en quelques clics, offrir un cadeau original, ludique et sportif : Un coach mp3. Très simple à offrir : soit directement par e-mail soit en l’imprimant (et vous le pliez dans une enveloppe). </p>
                <a href="#" class="small">Offrez un bon cadeau Jiwok maintenant</a>
                <h5>A propos de votre abonnement Jiwok: </h5>
                <p>Pour éviter toute discontinuité de votre pass, votre abonnement Jiwok sera renouvelé automatiquement sur une période équivalente à celle initialement souscrite. Vous pouvez bien sûr annuler le renouvellement automatique de votre abonnement à tout moment à partir de la page "Mon compte". La notification de résilier l'abonnement devra être faite par le membre à Jiwok au plus tard 48 h avant la date d'échéance de l'abonnement en cours. .</p>
            </div>
        </div>
      </section>-->



<?php include("offerSection_NewDesig.php"); 
 include("footer.php"); ?>
<script type="text/javascript">
swfobject.registerObject("FlashID");
</script>
