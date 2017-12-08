<?php 
session_start();
set_time_limit(1600);
ini_set("memory_limit", "128M");
ini_set('max_execution_time', 1200);

function getCurlResponse($url){
		$curlHandle = curl_init($url);
		/*curl_setopt($curlHandle, CURLOPT_POST, 1);
		curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $postvar);
		curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1); */
		curl_setopt($curlHandle, CURLOPT_HEADER, 0);  // DO NOT RETURN HTTP HEADERS 
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);  // RETURN THE CONTENTS OF THE CALL
		$returnFromCurl = curl_exec($curlHandle) or die("curl error");	
	//	echo "$payboxData";print_r($payboxData);
		curl_close($curlHandle); 
		return $returnFromCurl;
}
$queueid      		=   $_REQUEST["queueid"];
$server         	=   $_REQUEST["server"];
$method				=	$_REQUEST["method"];
$path				=		'https://www.jiwok.com/webservices/GetWorkoutInQueueGandi.php?queue_id='.$queueid;
include_once("includes/classes/class.xmlparse.php");
//$xmlObj				=	new sxmlParse(file_get_contents($path));
$xmlObj				=	new sxmlParse(getCurlResponse($path));
$user_lang_arr		=	$xmlObj->crawlXML("root","page_lang_selected");
$user_lang 			= $user_lang_arr['attr']['id']; 

include_once('includeconfig.php');
include_once("includes/classes/class.programs.php");
if($lanId=="")
	 $lanId=1;

if($lanId == 1){
	$msg 	=	'Your session is ready. If the download does not start in a few seconds, press the link below';
	$dwn	=	'Download';
	$dwnfl	=	'Download File';
}
else if($lanId == 5)
{
	$msg 	=	'Twój trening jest gotowy! Jeśli pobieranie nie rozpocznie się w ciągu kilku sekund, kliknij na poniższy link';
	$dwn	=	'Pobierz';
	$dwnfl	=	'Pobierz plik';
}
else{
	$msg     =	'Votre séance est prête. Si le téléchargement ne démarre pas dans quelques secondes, cliquez sur le bouton ci-dessous';
	$dwn 	=	'Télécharger';
	$dwnfl	=	'Téléchargez votre séance';
}

$queueid        =   $_REQUEST["queueid"];
$server         =   $_REQUEST["server"];
$method			=	$_REQUEST["method"];

if($method == 'ftp')
{
	$lnk	=	"https://www.jiwok.com/download_new.php?queueid=".$queueid."&server=".$server."&method=".$method."&ifs=true";
	header('Location:'.$lnk);
	exit;
}
	
if($server == 1)
{
	$dir    =   SERVER1;
}

if(empty($queueid) || $server == "")
{
	if($pgm_lang == 1)
	{
	
	}else
	{

	}
}

$objGen     	= new General();
$objTraining	= new Programs($lanId);
$parObj 		= new Contents('contents.php');

$returnData		= $parObj->_getTagcontents($xmlPath,'contents','label');
$arrayData		= $returnData['general'];

//collecting All training program category for displaying
$getAllTrainCats	= $objTraining->_getAllGenItem('category',$lanId);

?>
<?php include("header.php"); ?>
<section class="banner-static  bnr-mrgn mbl">
	  
       <div class="bnr-content" style="position:relative;">
         <div class="frame slider-first">
         <div class="callbacks_container">
          <ul class="rslides callbacks callbacks1" id="slider4">
         <li><img data-lazy-src="<?=ROOT_FOLDER?>images/contact_new.jpg" alt="Slide 01"> </li>
		  </ul>
         </div>
         </div>
         <div class="heading4JW"><p><?=$dwnfl;?></p></div> 
         <div class="heading5">
		 <div><?=$msg;?></div>
     </div>
<?php $link	=	"http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>
<div style="text-align:center">
<a class="link" style="padding: 10px 80px; position:relative;"  href="<?=$link;?>"  style="color:#f6d338;"><?=$dwn;?></a>
</div>
<iframe src="download_new.php?queueid=<?=$queueid?>&server=<?=$server?>&method=<?=$method?>&ifs=true" style="display:none;"></iframe>     
      </div>
 </section>
<section class="theme-select">
        <div class="frame">
           <p class="heading"> <?=$parObj->_getLabenames($SiteData,'title13','name');?> </p>
           <div class="categories">
             <?php include("right_cate_list.php"); ?>
           </div>
           
        </div>
     </section>
<script language="javascript">
 function showhide(ctrl)
 	{		
		$("#"+ctrl).slideToggle();
	}
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
	
  equalheight('.top_10 ul li');
});
$(window).resize(function(){
  equalheight('.top_10 ul li');
});
</script>
<?php
include("footer.php"); 
?>
