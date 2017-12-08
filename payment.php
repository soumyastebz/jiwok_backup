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
$returnData		= $parObj->_getTagcontents($xmlPath,'payments','label');
$arrayData		= $returnData['general'];

$returnDataOffer	= $parObj->_getTagcontents($xmlPath,'offerSection_NewDesign','label');
$arrayDataOffer		= $returnDataOffer['general'];

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
//echo $objCMS->_getContent($b = 'PAYMENT_CMS1', $lanId);exit;
?>
<?php include("header.php"); ?>
   <section class="payment">
        <div class="frame3">
         <div class="row-1" style="padding-bottom:0"><div class="return">
            <a href="<?=$backButtonLink?>" class="white"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'newBackTxt','name'),'UTF-8');?></a>
         </div><div class="bred-hovr01 second"><ul class="bredcrumbs">
        <li><?=mb_strtoupper($parObj->_getLabenames($arrayData,'newPageTxt','name'),'UTF-8');?></li>
        <li><a href="<?=ROOT_JWPATH?>index.php">
          <?=mb_strtoupper($parObj->_getLabenames($arrayData,'newHmeTxt','name'),'UTF-8');?>
          </a></li>
        <li>></li>
        <li>
          <?=mb_strtoupper($parObj->_getLabenames($arrayData,'newHeadTxt','name'),'UTF-8');?>
         </li>
           </ul></div>
         <div class="title">
           <h3 class="hed-2"><br> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'newHeadTxt','name'),'UTF-8');?></h3>
      </div>
         
         </div>
         
         <div class="row_jw">
            <a href="#"><div class="col-orng"> <a style=" color: #FFFFFF;" href="<?=ROOT_JWPATH?>userreg1.php" class="sign"><?=$parObj->_getLabenames($arrayData,'newSignUpCptnTxt','name');?> </a><span><?=$parObj->_getLabenames($arrayData,'newSignUpBtmCptnTxt','name');?></span></div></a>
            <a href="#"><div class="col-blu"><a style=" color: #FFFFFF;" href="<?=ROOT_JWPATH?>payment_renew.php" class="sign"><?=$parObj->_getLabenames($arrayData,'paymentrenewCptnTxt','name');?> </a></div></a>
         </div>
         <?php $regSer	=	$objCMS->_getContent($b = 'PAYMENT_CMS1', $lanId); ?>
         <div class="workout"> 
           <h3><?=mb_strtoupper($objGen->_output($regSer['content_display_title']),'UTF-8');?></h3>
           <p><?=$objGen->_output($regSer['content_body']);?></p>
         </div>
    
  <?php include("offerSection_NewDesig_payment_pl.php"); ?>
         <div class="workout">
           <h3> <?=mb_strtoupper($parObj->_getLabenames($arrayDataOffer,'newMoreGftTxt','name'),'UTF-8');?></h3>
           <p><?=$parObj->_getLabenames($arrayDataOffer,'newMoreGftDesc1Txt','name');?>
           <br />
        <?=$parObj->_getLabenames($arrayDataOffer,'newMoreGftDesc2Txt','name');?>
        <br />
        <?=$parObj->_getLabenames($arrayDataOffer,'newMoreGftDesc3Txt','name');?></p>
<a href="<?=ROOT_JWPATH?>giftreg.php" class="btn"><?=mb_strtoupper($parObj->_getLabenames($arrayDataOffer,'newMoreGftDesc4Txt','name'),'UTF-8');?></a>
         </div>
<!-- no need of same button two time.So removed bottom buttons
         <div class="row_jw">
            <a href="#"><div class="col-orng">Pensez à utiliser<span>nos cartes ou bons cadeaux</span></div></a>
            <a href="#"><div class="col-blu">Pensez à utiliser</div></a>
         </div>
-->
      </div> 
     
      </section>
      
     <div class="frame">
		  
       <ul class="faw_JW"> 
		   <?php $regSer	=	$objCMS->_getContent($b = 'PAYMENT_CMS2', $lanId); ?>
         <li>
            <p class="Q"><?=mb_strtoupper($objGen->_output($regSer['content_display_title']),'UTF-8');?></p><br>
         <p> <?=$objGen->_output($regSer['content_body']);?></p>
         </li>
         <?php $regSer	=	$objCMS->_getContent($b = 'PAYMENT_CMS3', $lanId); ?>
          <li>
            <p class="Q"> <?=mb_strtoupper($objGen->_output($regSer['content_display_title']),'UTF-8');?></p><br>
         <p><?=$objGen->_output($regSer['content_body']);?></p>
         </li>
       </ul>
      
     </div>
<?php include("footer.php"); ?>
<script type="text/javascript">
swfobject.registerObject("FlashID");
</script>
