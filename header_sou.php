<?php 
//For redirecting the old jiwok if english
if(substr($_SERVER['REQUEST_URI'],0,4) == "/en/")
{
	header("location:http://en.jiwok.com".$_SERVER['REQUEST_URI'],true,301);
}
$pg_name_cms	= "";
$page_name 		= end(explode("/",$_SERVER['PHP_SELF']));
// Getting the cms meta based on the page titile
if(($page_name == 'contents.php') || ($page_name == 'coaches.php') || ($page_name == 'sw_downloads_beta.php')){
	$pg_name_cms	=  end(explode("/",$_SERVER['REQUEST_URI']));
}
$pg_name 		= str_replace(".php","",$page_name);
$seoPath		= str_replace(".php","",end(explode("/",$_SERVER['REQUEST_URI'])));
$path			= ($_SERVER['QUERY_STRING']) ? $page_name.'?'.$_SERVER['QUERY_STRING'] :$page_name;

$backButtonLink	= "javascript:history.go(-1);";
include_once("includes/filterValidation.php");
include_once('includeconfig.php');
include_once("includes/classes/class.member.php");
include_once("includes/classes/class.seo.php"); // SEO class
//for checking fb connection need to delete
//~ if($page_name == 'userreg1.php')
//~{
			//~ include("fbLoginInc.php");  
			//~ include("fbLogin.php"); 
// }
//for checking fb connection need to delete
$objGenHed	= new General();
$objMem		= new Member($lanId);
$parObj 	= new Contents();
$ftrObj 	= new footerLinks();
$ObjSeo 	= new seo($lanId);

if($pg_name=="userreg1_1"){
	$pg_name	=	"userreg1";
}
if($pg_name=="userreg2_2"){
	$pg_name	=	"userreg2";
}
//SEO settings from table
if($pg_name_cms == "")// Getting the cms meta based on the page titile
	$metaTags	=	($ObjSeo->_getAllByName($pg_name)) ? $ObjSeo->_getAllByName($pg_name):$ObjSeo->_getAllByName('jiwok');
else
	$metaTags	=	($ObjSeo->_getAllByName($pg_name_cms)) ? $ObjSeo->_getAllByName($pg_name_cms):$ObjSeo->_getAllByName('jiwok');
if($metaTags[0]['meta_title'] == "" &&  $metaTags[0]['meta_keyword'] == "") $metaTags	=	 $ObjSeo->_getAllByName('jiwok');

//get current page for return after login
$curPage			= 	$objGenHed->curQueryPageName();
if($objGenHed->curPageName() == 'login_failed.php')
$curPage			= 	'index.php';

//collecting data from the xml for the static contents
$returnData			= $parObj->_getTagcontents($xmlPath,'header','label');
$arrayDataHead		= $returnData['general'];

//for displaying cookie value on login box
//Added by Shilpa for SEO
include('error_url_redirect.php');
if($cookieEmail != '')
	{
		$email			=	$cookieEmail;
		$pass			=	$cookiePass;
	}
else
	{
		$email			=	$parObj->_getLabenames($arrayDataHead,'email','name');
		$pass			=	$parObj->_getLabenames($arrayDataHead,'password','name');
	}

if($_REQUEST['forum'] == 1 && $_REQUEST['returnUrl'])
	{
	
		$_SESSION['returnPath'] = 1;
		$curPage = "http://www.jiwok.com/forum/test.php";
	}
	
//The below  for extracting the  affliate account id and banner id (starts)
$tmpAffArray1 	= explode('a_aid=',$_SERVER['REQUEST_URI']);
$tmpAffArray2	= explode('&',$tmpAffArray1[1]);
$a_aid			= $tmpAffArray2[0];
$tmpAffArray3	= explode('=',$tmpAffArray2[1]);
$a_bid			= $tmpAffArray3[1];
//The above  for extracting the  affliate account id and banner id (ends)
if(trim($a_bid)!= "" && trim($a_aid)!= "")
	{
		if($lanId=="")	 $lanId=1;
		include_once('includes/classes/class.programs.php');
		$objPgm1     	= new Programs($lanId);
		$objGen1     	= new General();
		$getUrlKey1		= $objGen1->_lastVistedPage('Defalt');
		
		$_SESSION['affili']['a_aid'] = $a_aid;
		$_SESSION['affili']['a_bid'] = $a_bid;
		
		$papUserId 	= $objPgm1->_getUserIdPap($a_aid);
		$campaignId = $objPgm1->_getCampaignId($a_bid);
		
		$insArray 	= array();
		$insArray['clickid'] 	= '';
		$insArray['userid'] 	= addslashes($papUserId);
		$insArray['bannerid'] 	= addslashes($a_bid);
		$insArray['campaignid'] = addslashes($campaignId);
		$insArray['rtype'] 		= 'R';
		$insArray['datetime'] 	= date("Y-m-d H:i:s");
		$insArray['refererurl'] = $getUrlKey1[0];
		
		$ip				= $_SERVER['REMOTE_ADDR'];
		$insArray['ip'] = $ip; 
		$res 			= $objPgm1->_insertPapValues($insArray);
		$query 			= "select MAX(clickid) as max from qu_pap_rawclicks";
		$result 		= $GLOBALS['db']->getRow($query, DB_FETCHMODE_ASSOC); 
		$clickid 		= trim($result['max'])+1;
	}
if((isset($_SESSION['jiwokforum']['email']) && isset($_SESSION['user']['userId'])) || isset($_SESSION['jiwokforum']['user'])) 
	{  
		$forum_path = "http://www.jiwok.com/forum/index.php?sid=".$_SESSION['jiwokforum']['sid'];  
	}
elseif(!isset($_SESSION['jiwokforum']['email']) && isset($_SESSION['user']['userId'])) 
	{ 
		unset($_SESSION['jiwokforum']['email']);  
		$forum_path = "forum/test.php"; 
	}
else
	{ 
		$forum_path = "http://www.jiwok.com/forum/index.php?open=1";  
	}
	
/* done by vinitha for dynamic switching of  languages*/
include_once('includes/classes/class.Languages.php');

/* For getting errors */
if($_SESSION['user']['userId'] == 107944){ // For account soumya.reubro@gmail.com
		//error_reporting(E_ALL ^ E_NOTICE);
		//echo "Debugging";
}

$langObj		=	new Language();
$langDetails	=	$langObj->_getFlagArray();
foreach($langDetails as $lang)
	{
 		$icon	=	"images/icon_".strtolower($lang['language_name']).".jpg";
		$struct.=	'<a href="#" onclick="langChange('.$lang['language_id'].');"  title="'.$lang['language_name'].'" ><img src="'.ROOT_FOLDER.$icon.'" alt="" class="flagPsn" /></a>';
	}
?>
<?php 
switch($lanId)
{
	case 1:	$ln_code	=	"EN";break;
	case 2: $ln_code	=	"FR";break;
	case 3: $ln_code	=	"IT";break;
	case 4: $ln_code	=	"ES";break;
	case 5: $ln_code	=	"PL";break;
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="shortcut icon" type="image/ico" href="<?=ROOT_FOLDER?>images/favicon.ico" />
<?php
/*--------------------------------------------------*/
if($page_name	==	"press_testimonial_details.php")
	{ 
		switch($lanId){
			case 1:
			$title_name="Jiwok views and user testimonials";
			break;
			case 2:
			$title_name="Avis Jiwok et témoignages d'utilisateurs";
			break;
			case 3:
			$title_name="Vistas Jiwok y testimonios de usuarios";
			break;
			case 4:
			$title_name="Viste Jiwok e testimonianze degli utenti";
			break;
			case 5:
			$title_name="Odsłon Jiwok i referencje użytkowników";
			break;
			}?>
		<title>
		   <?php echo $title_name;?>
		</title>
	<?php
	}
else if($page_name == "search_result.php")
{
/*---------------------------------------------------------------*/
		if($subcatPageDetails['keywords']!='')
		{ ?>
		<meta name="keywords" content="<?php echo($subcatPageDetails['keywords']);?>" /><?php } ?>
		<title>
		<?php 
		if($cat_flex_id)
		{
			$seo_qry	=	"SELECT * FROM sub_category WHERE flex_id= '".$cat_flex_id."' AND language_id=".$lanId;
			$seo_rslt		=	$GLOBALS['db']->getRow($seo_qry, DB_FETCHMODE_ASSOC);
			if($seo_rslt['seo_title']!="")
			{	if(!($maxPage ==1))
				{
					echo mb_strtoupper(html_entity_decode($seo_rslt['seo_title']),'UTF-8').' '.$pageNum;
				}
				else
				{
					echo mb_strtoupper(html_entity_decode($seo_rslt['seo_title']),'UTF-8');
				}
			}
			else
			{
				 if($subcatPageDetails['title']!=''){echo mb_strtoupper(html_entity_decode(substr($subcatPageDetails['title'],0,60)),'UTF-8'); }else{echo mb_strtoupper(html_entity_decode(substr($categoryName,0,60)),'UTF-8');}
			}
		}
		else 
		{
			?>
			<?php if($subcatPageDetails['title']!=''){echo mb_strtoupper(html_entity_decode(substr($subcatPageDetails['title'],0,60)),'UTF-8'); }else{echo mb_strtoupper(html_entity_decode(substr($categoryName,0,60)),'UTF-8');
			?>
			
			<?php 
			} 
		}
		?>
	</title>
	<?php 
	if($cat_flex_id	&&	$seo_rslt['seo_description']!="")
	{
	?>
		<meta name="description" content="<?php echo strip_tags($objGen->_output(trim(($seo_rslt['seo_description']))));?>" />
	<?php
	}	
	?>
	     <!--‭<meta name="robots" content="noindex, follow" />-->
    <?php
}
else if(($page_name == "program_generate2.php") || ($page_name == "program_details.php"))
{
	 if($page_name == "program_details.php")

	{ 
		if($program_id_from_array)
		{
			$seo_prgm			=	"SELECT * FROM program_detail WHERE program_master_id=".$program_id_from_array." AND language_id=".$lanId;
			$seo_prgrm_rslt		=	$GLOBALS['db']->getRow($seo_prgm, DB_FETCHMODE_ASSOC);
			if($seo_prgrm_rslt['seo_title']!="")
			{
				?>
				<title> <?php echo mb_strtoupper(html_entity_decode($seo_prgrm_rslt['seo_title']),'UTF-8') ?> </title>
				<meta name="description" content="<?php echo strip_tags($objGen->_output(trim(($seo_prgrm_rslt['seo_description']))));?>" />
			<?php
			}
			else
			{
				$metaContentProgram = strip_tags($data['program_target']).' '.strip_tags($data['program_desc']);
				$metaContentProgram = mb_substr($metaContentProgram,0,160);
				?>
			<title><?php if($data['program_title']!=''){echo mb_strtoupper(html_entity_decode(substr($data['program_title'],0,60)),'UTF-8'); }else{?>JIWOK<?php }?></title>
			<meta name="description" content="<?php echo $metaContentProgram;?>" />
			<?php
			}
			
		}
		else
		{
			?>
			<title><?php if($data['program_title']!=''){echo mb_strtoupper(html_entity_decode(substr($data['program_title'],0,60)),'UTF-8'); }else{?>JIWOK<?php }?></title>
			<meta name="description" content="<?php echo stripslashes($metaTags[0]['meta_description']);?>" />
			<?php
		}
	}
	else
	{

		?>
		<title><?php if($data['program_title']!=''){echo mb_strtoupper(html_entity_decode(substr($data['program_title'],0,60)),'UTF-8'); }else{?>JIWOK<?php }?></title>
		<?php
	}
	
}
else
{
?>
<!--SEO implementation-->
<meta name="google-site-verification" content="x54WnBbjwCMZU3t-uoGM_HNk_yTR81P55DgBfG0rEYU" />
<title><?php echo mb_strtoupper((html_entity_decode(stripslashes($metaTags[0]['meta_title']))),'UTF-8');?></title>
<meta name="keywords" content="<?php echo stripslashes($metaTags[0]['meta_keyword']);?>" />
<meta name="description" content="<?php echo stripslashes($metaTags[0]['meta_description']);?>" />
<?php
}
?>

<!--Added by Shilpa for SEO specification of language-->
<META HTTP-EQUIV="Content-Language" CONTENT="<?= $ln_code ?>">
<?php  
$fb_title	=	$parObj->_getLabenames($arrayDataHead,'fb_title','name');
$fb_link	=	$parObj->_getLabenames($arrayDataHead,'fb_link','name');
$fb_image	=	$parObj->_getLabenames($arrayDataHead,'fb_image','name');
$fb_desc	=	$parObj->_getLabenames($arrayDataHead,'fb_desc','name');
if($page_name == "main.php")
{
	$fb_image	= "http://www.jiwok.com/marathon/images/Jiwok_Marathon.png";
	$fb_link	= "http://www.jiwok.com/Marathon-Paris-seance-course-jiwok";
	echo '<link rel="canonical" href="'.$fb_link.'">';
}
?>
<?php //gg commented for https -sei for https
   if($page_name  == "payment_new.php"){
     $jiwok_url    = "https://www.jiwok.com/";
	 $jiwok_context= "https://schema.org";
	 $fb_link      = "https://www.jiwok.com/";
	 $fb_image     = "https://www.jiwok.com/images/fbLogo_Jiwok.png";
	 }else{
	 $jiwok_url  = ROOT_JWPATH;
	 $jiwok_context="http://schema.org";
	 
 }?>
<meta property="og:title" content="<?php echo $fb_title;?>" />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?=$fb_link;?>" />
<meta property="og:image" content="<?=$fb_image;?>"/>
<meta property="og:site_name" content="Jiwok"/>
<meta property="fb:app_id" content="185115341583991"/>
<meta property="og:description" content="<?=$fb_desc;?>"/>

<script type="application/ld+json">
{
    "@context": "<?php echo $jiwok_context; ?>",
    "@type": "WebSite",
    "url": "<?php echo $jiwok_url; ?>",
    "inLanguage": "<?= $ln_code ?>",
    "name": "<?php echo mb_strtoupper((html_entity_decode(stripslashes($metaTags[0]['meta_title']))),'UTF-8');?>",
    "image": "<?=ROOT_FOLDER?>images/jiwok_screenshot_seo.jpeg",
    "headline" : "Jiwok,<?php echo mb_strtoupper((html_entity_decode(stripslashes($metaTags[0]['meta_title']))),'UTF-8');?>",
    "publisher": {
	    "@type": "Organization",
	    "name": "Jiwok"
    },
    "keywords": "<?php echo stripslashes($metaTags[0]['meta_keyword']);?>"
}
</script>
<link rel="shortcut icon" type="image/ico" href="<?=ROOT_FOLDER?>images/favicon.ico" />
<!--Added by Shilpa for href lan tag implementation in all paginated pages except ajax pages-->
<?php 
	include('seo_manage.php');
?>

<script src="<?=ROOT_FOLDER?>js/css3-mediaqueries.js"></script>	<!--for devices-new-site--->
<script type="text/javascript" src="<?=ROOT_FOLDER?>js/jquery-2.1.0.min.js"></script>
<?php if($page_name	!=	"press_testimonial_details.php"){ ?>
<script type="text/javascript" src="<?=ROOT_FOLDER?>Scripts/swfobject_modified.js"></script>
<script type="text/javascript" src="<?=ROOT_FOLDER?>includes/js/header.js"></script>
<script type="text/javascript" src="<?=ROOT_FOLDER?>Scripts/AC_RunActiveContent.js"></script>
<script type="text/javascript" src="<?=ROOT_FOLDER?>includes/js/general.js"></script>
<?php /*?><script type="text/javascript" src="<?=ROOT_FOLDER?>js/flaunt.js"></script><?php */?>
<script type="text/javascript">
;(function($){$(function(){$('.nav').append($('<div class="nav-mobile"></div>'));$('.nav-item').has('ul').prepend('<span class="nav-click"><i class="nav-arrow"></i></span>');$('.nav-mobile').click(function(){$('.nav-list').toggle();});$('.nav-list').on('click','.nav-click',function(){$(this).siblings('.nav-submenu').toggle();$(this).children('.nav-arrow').toggleClass('nav-rotate');});});})(jQuery);
</script>
<?php } ?>
<script src="<?=ROOT_FOLDER?>js/jquery.devrama.lazyload.min-0.9.3.js"></script><!--images lazy loading-->
<!--
<script type="text/javascript" src="<?=ROOT_FOLDER?>resources/accordion/jquery-1.5.1.js"></script>
-->


<?php if($pg_name=='referrel_system'){ ?>
<!--<link href="<?=ROOT_FOLDER?>resources/referal.css" rel="stylesheet" type="text/css" />-->
<?php } ?>

<?php /*?><?php if($pg_name=='search' or $pg_name=='sitemap' or $pg_name=='sitemap_en' or $pg_name=='singlePrograms' or $pg_name=='download_file'){ ?>
	<link href="<?=ROOT_FOLDER?>includes/js/accordian_css/skins/grey.css" rel="stylesheet" type="text/css" />
<?php } ?><?php */?>
<?php if($pg_name=='historical'){ ?>
		
		<link rel='stylesheet' type='text/css' href='<?=ROOT_FOLDER?>resources/training_calendar_new_design.css' />
<?php } ?>
<?php if($pg_name=='userArea') { ?>
		<link rel='stylesheet' type='text/css' href='<?=ROOT_FOLDER?>resources/training_calendar_new_design.css' />
<?php } ?>
<?php if(($pg_name=='program_generate2') || (($pg_name=='program_details'))){ ?>
		<link href="<?=ROOT_FOLDER?>demo_files/jquery.mCustomScrollbar.css" rel="stylesheet" />        
		
<?php } ?>
<?php if($pg_name=='program_details'){ ?>
		<script type="text/javascript" src="<?=ROOT_FOLDER?>datepicker/js/datepicker.js"></script>
        <link  type="text/css"  href="<?=ROOT_FOLDER?>datepicker/css/datepicker.css" rel="stylesheet" />
<?php } 
if($pg_name=='program_generate2') { ?>
<link rel='stylesheet' type='text/css' href='<?=ROOT_FOLDER?>resources/training_calendar_new_design.css' />
<?php 
}
if($page_name == 'index.php')
{ 
	?>
    <script>
    // You can also use "$(window).load(function() {"
     $(document).ready(function(){
    
      // Slideshow 4
      $("#slider4").responsiveSlides({
        auto: true,
        pager: true,
        nav: false,
        speed: 500,
        namespace: "callbacks",
        before: function () {
          $('.events').append("<li>before event fired.</li>");
        },
        after: function () {
          $('.events').append("<li>after event fired.</li>");
        }
      });

    });
    //for coaches slider ends========
     var catCount=0;
	<?php if(count($coachesCatData)>0){ ?>
				catCount=<?php echo count($coachesCatData); ?>;  // Setting the number of categories
			    $(document).ready(function(){
				var category_id=$("#category_id").val();
				selectCat(category_id);
				});
				function selectCat(category_id){
				var lanId      =$("#lanId").val();
				 $('#catrgory').empty().append($('<img src="<?=ROOT_FOLDER?>images/loading.gif" />'));
				$.ajax({
				url : 'category_display.php',
				type: "POST",
				data: "category_id="+category_id+"&lanId="+lanId,
				beforeSend: function(){
							
						},
						
						complete: function(){
						
						},
				success: function(response){
					 
				       $('#catrgory').hide().html(response).fadeIn('xslow');
				      
					}
		          });
			    }
	<?php } ?>
  </script>
		 
    <?php
}

if($page_name == 'payment_new.php')
{
	?>
    <link type="text/css" rel="stylesheet" href="<?=ROOT_FOLDER?>resources/jquery.pwstabs-1.2.1.css">
    <?php
}
 if(($pg_name=='userreg1')||($pg_name=='userreg2')||($pg_name=='giftreg')||($pg_name=='edit_profile')||($pg_name=='new_payment')||($pg_name=='payment_renew')){ ?>
	<!----------TOOLTIP----------->
	<SCRIPT language="JavaScript1.2" src="<?=ROOT_FOLDER?>includes/js/tooltip.js" type="text/javascript"></SCRIPT>
<?php } ?>


<!--|_____________CALENDAR_____________|-->

<?php if($pg_name=='historical')
	{ ?>
		
        <script type='text/javascript' src="<?=ROOT_FOLDER?>includes/js/training_calendar_historical.js"></script>
		
<?php } 
	 if($pg_name=='userArea')
	 { ?>         
		
        <script type='text/javascript' src="<?=ROOT_FOLDER?>includes/js/training_calendar_userarea.js"></script>
      <script type='text/javascript' src="<?=ROOT_FOLDER?>includes/js/popup.js"></script>
     <script type='text/javascript' src="<?=ROOT_FOLDER?>includes/js/userArea.js"></script>
        <script src="<?=ROOT_FOLDER?>js/jquery.easytabs.min.js" type="text/javascript"></script>
   			<script src="js/lib.js"></script>
	   <script type="text/javascript">
        $(document).ready( function() {
          $('#tab-container').easytabs();
        });
      </script>
<?php } 
	 if($pg_name=='program_generate2')
	 { ?>
		<script type='text/javascript' src="<?=ROOT_FOLDER?>includes/js/programGenerate.js"></script>
		<script type='text/javascript' src="<?=ROOT_FOLDER?>includes/js/training_calendar.js"></script>
<?php } 
	 if($pg_name=='program_details') { ?>
<script type="text/javascript" src="<?=ROOT_FOLDER?>includes/js/programDetails.js"></script> 
	  <?php }
	 if(($pg_name=='program_details') ||($pg_name=='program_generate2'))
	 { ?>
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
	
  equalheight('.height_equal');
});
$(window).resize(function(){
  equalheight('.height_equal');
});
</script>

<?php } ?>
<script type="text/javascript"  src="<?=ROOT_FOLDER?>js/responsiveslides.min.js"></script>
<!-----login popup starts ------------>
<?php if($page_name != 'userreg1.php'){ ?>
<script type="text/javascript" src="<?=ROOT_FOLDER?>js/jquery.bpopup.min.js"></script>
<script>
;(function($) {
        $(function() {
            $('.login_btn').bind('click', function(e) {
                e.preventDefault();
                $('.pop').bPopup({
                speed: 2000,
                transition: 'slideDown'
               });
           });
           
           $.DrLazyload();//for images lazy loading
      });
    })(jQuery);
 

    var d = document;
    var safari = (navigator.userAgent.toLowerCase().indexOf('safari') != -1) ? true : false;
    var gebtn = function(parEl,child) { return parEl.getElementsByTagName(child); };
    onload = function() {
        var body = gebtn(d,'body')[0];
        body.className = body.className && body.className != '' ? body.className + ' has-js' : 'has-js';
        if (!d.getElementById || !d.createTextNode) return;
        var ls = gebtn(d,'label');
        for (var i = 0; i < ls.length; i++) {
            var l = ls[i];
            if (l.className.indexOf('label_') == -1) continue;
            var inp = gebtn(l,'input')[0];
            if (l.className == 'label_check') {
                l.className = (safari && inp.checked == true || inp.checked) ? 'label_check c_on' : 'label_check c_off';
                l.onclick = check_it;
            };
          };
    };
    var check_it = function() {
        var inp = gebtn(this,'input')[0];
        if (this.className == 'label_check c_off' || (!safari && inp.checked)) {
            this.className = 'label_check c_on';
            if (safari) inp.click();
        } else {
            this.className = 'label_check c_off';
            if (safari) inp.click();
        };
    };

function changeCheckboxToChecked(remember)
{ if(remember == 0)
	{ document.getElementById("remember").value =	1;
      return false;
	}
	else
	{ document.getElementById("remember").value =	0;
      return false;
	}
}
</script>
<?php }?>
<!---------login popup ends --------->

<script>
	//paste this code under head tag or in a seperate js file.
	// Wait for window load
	$(window).load(function() {
		// Animate loader off screen
		$(".se-pre-con").fadeOut("slow");;
	});
</script>




<link href="<?=ROOT_FOLDER?>resources/style.css" rel="stylesheet" type="text/css" />
<link href="<?=ROOT_FOLDER?>resources/style_dev.css" rel="stylesheet" type="text/css" />
<?php 
if((($page_name != 'index.php') && ($page_name != 'userreg1.php') && ($page_name != 'userreg2.php')))
{
	?><link href="<?=ROOT_FOLDER?>css/main.css" rel="stylesheet" type="text/css" >
    <?php	 
}

//==========================mixpanel tracking code starts=======================
if($lanId	==	2)
	 {
		if($pg_name	==	'index')
		{
			include_once('includes/classes/class.mixPanel.php');
			$mpanel					=	new mixPanel();
			$mpanelString			=	$mpanel	->	trackAndRegister("Home Page","","");
			$_SESSION['homepage2']	=	1;
			echo $mpanelString;
		}
		else if($pg_name	==	'userreg2')
		{
			include_once('includes/classes/class.mixPanel.php');
			$mpanel					=	new mixPanel();
			/*if($_SESSION['homepage2']	!=	1)
			{*/
				$mpanelString		=	$mpanel	->	trackAndRegister("Home Page","","");
				echo $mpanelString;
				$mpanelString		=	$mpanel	->	trackSignup("Signup",$_SESSION['login']['user_email']);
				
			/*}
			else
			{
				$mpanelString		=	$mpanel	->	trackSignup("Signup",$_SESSION['login']['user_email']);
				unset($_SESSION['homepage2']);
			}*/
			
			echo $mpanelString;
		}
		else if(($pg_name	==	'userArea')	||	($_SESSION['mixRegStat']	==	1))
		{
			//Confirms that the user is not registered and paid through any of the brands
			 
			$bndQuery				=	"SELECT	* FROM brand_user WHERE user_id =".$userid;	
			$bndResult				=	$GLOBALS['db']->getRow($bndQuery, DB_FETCHMODE_ASSOC);
			if(!$bndResult)
			{
				$userQuery			=	"SELECT	user_doj FROM user_master WHERE user_id	=".$userid;	
				$userResult			=	$GLOBALS['db']->getRow($userQuery, DB_FETCHMODE_ASSOC);
				$doj_time			= 	strtotime($userResult['user_doj']);
				$online_time		= 	strtotime('2013-03-13');		
				
					
				if(($_REQUEST['origin']	==	'registration')	||	($_SESSION['mixRegStat']	==	1))
				{
					include_once('includes/classes/class.mixPanel.php');
					$mpanel				=	new mixPanel();
					$params				=	array("name"	=>	$user_name,"email"	=>	$_SESSION["user"]["user_email"]);
					$mpanelString		=	$mpanel	->	trackAndRegister("Signed in",$_SESSION["user"]["user_email"],$params);
					
					echo $mpanelString;
					unset($_SESSION['mixRegStat']);
				}
				
			}
		}
			 
	 }
//=======================mixpanel tracking code ends ======================
?>
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1101143489970449');
fbq('track', "PageView");
<?php if(($pg_name	==	'userArea') && ($_REQUEST['origin']	==	'registration')){?>
fbq('track', 'CompleteRegistration');
<?php } 
 if(($pg_name	==	'userArea') && ($_REQUEST['origin']	==	'payment'))
{
	$paidqry			=	"SELECT	payment_amount,payment_currency FROM payment WHERE payment_userid =".$userid." AND payment_status = '1' ORDER BY payment_id  DESC limit 0,1 ";	
	$paymentResult		=	$GLOBALS['db']->getRow($paidqry, DB_FETCHMODE_ASSOC);

if(strtolower($paymentResult['payment_currency'])	==	"euro")
{
	 $FBcurrency="EUR";	
}
elseif(strtolower($paymentResult['payment_currency'])	==	"zloty")
{
	 $FBcurrency="PLN";
}
elseif(strtolower($paymentResult['payment_currency'])	==	"usd")
{
	 $FBcurrency="USD";
}
?>
fbq('track', 'Purchase', {value: '<?php echo $paymentResult['payment_amount'];?>', currency:'<?php echo $FBcurrency;?>'});
<?php }
?>
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1101143489970449&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->


</head>
<body <?php if(($page_name == 'userreg1.php') || ($page_name == 'userreg2.php') || ($pg_name =='login_failed') || ($pg_name =='login_failed1') || ($page_name == 'payment_new.php')) { ?> id="entry" <?php } if($pg_name=='userArea')
	 { ?> onLoad="<?php echo $onloadglobal ?>"<?php } ?>>
<?php if($_SESSION['user']['userId'] != "") $imageLink="userArea.php"; else $imageLink="index.php"; ?>	
<div class="se-pre-con"></div>
<header>
  <div class="frame">
  <h1 class="logo">
  <?php  
/*  if(($pg_name == 'index' && isset($homePage) != "") or ($pg_name == 'index_eng' && isset($homePage) != "") or $pg_name == 'userreg1' or $pg_name == 'userreg2' or $pg_name == 'login_failed')
  {*/
	if($lanId	==	1)
	  	{?>
        	<a href="<?=ROOT_FOLDER?>index.php"><img src="<?=ROOT_FOLDER?>images/logo.png" alt="Sports Coach, Training" title="Jiwok" /></a><?php
		}
		elseif($lanId	==	2)
		{?>
        	<a href="<?=ROOT_FOLDER?>index.php"><img src="<?=ROOT_FOLDER?>images/logo.png" alt="Coach sportif, entrainement" title="Jiwok" /></a>
        <?php
		}
		elseif($lanId	==	3)
		{?>
        	<a href="<?=ROOT_FOLDER?>index.php"><img src="<?=ROOT_FOLDER?>images/logo.png" alt="Sportif Entrenador, entrainement" title="Jiwok" /></a>
        <?php
		}
		elseif($lanId	==	4)
		{?>
        	<a href="<?=ROOT_FOLDER?>index.php"><img src="<?=ROOT_FOLDER?>images/logo.png" alt="Coach sportif, entrainement" title="Jiwok" /></a>
        <?php
		}
		elseif($lanId	==	5)
		{?>
        	<a href="<?=ROOT_FOLDER?>pl/index.php"><img src="<?=ROOT_FOLDER?>images/logo.png" alt="Trener sportowy, entrainement" title="Jiwok" /></a>
        	
        <?php
		}
		else
		{?>
        	<a href="<?=ROOT_FOLDER?>index.php"><img src="<?=ROOT_FOLDER?>images/logo.png" alt="Jiwok" title="Jiwok" /></a>
        <?php
		}
 // }
  	//need check 
  /*else
  {
		if($lanId	==	1)
		{?>
  			<div class="logo-inner"><img src="<?=ROOT_FOLDER?>images/logo_resize.png" alt="Jiwok" /></div><?php
		}
		elseif($lanId	==	2)
		{?>
  			<div class="logo-inner"><img src="<?=ROOT_FOLDER?>images/logo_resize.png" alt="Jiwok" /></div><?php
		}
		elseif($lanId	==	3)
		{?>
  			<div class="logo-inner"><img src="<?=ROOT_FOLDER?>images/logo_resize.png" alt="Jiwok" /></div><?php
		}
		elseif($lanId	==	4)
		{?>
  			<div class="logo-inner"><img src="<?=ROOT_FOLDER?>images/logo_resize.png" alt="Jiwok" /></div><?php
		}
		elseif($lanId	==	5)
		{?>
  			<div class="logo-inner"><a href="<?=ROOT_FOLDER?>pl/index.php"><img src="<?=ROOT_FOLDER?>images/logo_resize_pl.png" alt="Jiwok" /></a></div><?php
		}
		else
		{?>
  			<div class="logo-inner"><img src="<?=ROOT_FOLDER?>images/logo_resize.png" alt="Jiwok" /></div><?php
		}
  }*/
  ?>
  </h1>
   <hgroup>
    <?php 
	if(($_SESSION['user']['userId'] == ""))
	{ 
		if(($pg_name!='userreg1')&&($pg_name!='userreg2')&&($pg_name!='login_failed')&&($pg_name!='login_failed1')){
			?> 
				<a href="#" class="login_btn"><?=$parObj->_getLabenames($arrayDataHead,'login','name');?></a> 
			<?php 
		}
   } 
   else 
   {
	    //need check
  	    //get user name for display if the user is logged
		$userId			=	$_SESSION['user']['userId'];
		$userLogged		=	$objMem->_getUserName($userId); 
		
		
     ?>
     <ul class="user">
     <li><a href="#"><?=$objGenHed->_output($userLogged['fname'])." ".$objGenHed->_output($userLogged['lname']);?>&nbsp;&nbsp;</a></li>
     
     <!--gg changed for https -sei for https-->
     <li><a href="<?=$jiwok_url?>logout.php?returnUrl=<?=base64_encode($curPage);?>"><?=$parObj->_getLabenames($arrayDataHead,'logout','name');?></a></li>
     </ul>
     <?php
   }
 ?>
<!--
  <form name="langForm" action="" method="post" style="margin: 0px; padding: 0px;">
  	<input type="hidden" name="langChange" value="" />
  </form> 
-->
<?php
	if(($page_name != 'userreg1.php') && ($page_name != 'userreg2.php') &&($pg_name!='login_failed') &&($pg_name!='login_failed1')&&($page_name != 'payment_new.php') &&($page_name != 'payment_new_test.php'))
			{ ?>
  <div class="choose-language">
      <ul>      
		  <li>
		   <img src="<?=ROOT_FOLDER?>images/jiwok_language-icon.png" alt="select language" />
		   <h5 style="color:#fff;margin:0 0 0 10px;display:inline-block;"><?php echo $ln_code ?></h5><img class="drop-arrow" src="<?=ROOT_FOLDER?>images/lang-dropdown-arrow.png" alt="select language" />
			<ul>
            <?php
		
	         include("flag.php");
	         ?>		  
			</ul>
		  </li>
     </ul>
     </div>
     <?php 
			}
			?>
  </hgroup>
 </div>
</header>
<?php 
if(($_SESSION['user']['userId'] == ""))
{ 
/******login popup starts*****/	
  if(($pg_name!='userreg1')&&($pg_name!='userreg2')&&($pg_name!='login_failed') &&($pg_name!='login_failed1')){
?>  
<section class="pop"> 
             <div class="login">
             <h2><?=$parObj->_getLabenames($arrayDataHead,'login','name');?></h2>
             <form name="loginForm" action="" method="post" accept-charset="utf-8">
			 <p> <input name="user_email" type="text" class="field" value="<?=$email;?>" onfocus="value=''" placeholder="" />
			 </p>
             <p> <input name="user_password" type="password" class="field" value="<?=$pass;?>" onfocus="value=''"  />
			 </p>
             <div class="row loginpop">
			 <!--<label class="label_check" for="remember">-->
             <input name="remember" id="remember" value="0" type="checkbox" onclick="changeCheckboxToChecked(this.value);"  />&nbsp;<label for="remember"><span></span></label>
			 <span><a href="javascript:;"><?=$parObj->_getLabenames($arrayDataHead,'remember','name');?></a>|<a href="<?=ROOT_JWPATH?>forgot_password.php"><?=$parObj->_getLabenames($arrayDataHead,'forgot','name');?></a></span>
			 </div>
			 <p align="right">
				 <input name="loginButton" type="submit" class="btn" value="OK"/>
			 </p>
			 <?php
				  if(1){
					  $fbMode	=	"login";
					  include("fbLoginInc.php");  
					  include("fbLogin_login.php"); 
					}
			 ?>
			 </form>
             </div>
</section>
<?php
  }
 } /******login popup ends*****/
if(($page_name != 'index.php') && ($page_name != 'userreg1.php') && ($page_name != 'userreg2.php') &&($pg_name!='login_failed')&&($pg_name!='login_failed1')&&($page_name != 'payment_new.php') && ($page_name != 'payment_new_test.php'))
{ 
	 include_once("menu_header.php");
}
?>