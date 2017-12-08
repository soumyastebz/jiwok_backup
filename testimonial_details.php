<?php
session_start();
include_once('includeconfig.php');
include_once("includes/classes/class.testimonials.php");
include_once("includes/classes/class.homepage.php");


if($lanId=="")		 $lanId=1;	 
$objGen     	=	new General();
$parObj 		= 	new Contents('testimonial_details.php');
$objtestimoni	=	new Testimonial($lanId);
$objHome     	=	new Homepage($lanId);
$returnData		= 	$parObj->_getTagcontents($xmlPath,'homepage','label');
$arrayDataHome	= 	$returnData['general'];

	// get corresponding testimonial according to the home page
	//$getAllTestimoni	=	$objtestimoni->_displayPage('',$lanId);
	//collecting data from the xml for the static contents
	$returnDataService	= $parObj->_getTagcontents($xmlPath,'services','label');
	$arrayDataService	= $returnDataService['general'];
	// for service background section
	// Removing Ajax section 
	//fetch the static content form the xml
	$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
	$arrayData		= $returnData['general'];
	//collecting data from the xml for the static contents
	$returnDataList	= $parObj->_getTagcontents($xmlPath,'listprograms','label');
	$arrayDataList	= $returnDataList['general'];
	$thisPage		= "testimonial";
	// Number of records to show per page
	$recordsPerPage = 5;
	// //default startup page
	$opt_links_count = 4;
	$max = $opt_links_count;
	if(isset($_GET['pageNo']))
		{ 
			if($_GET['pageNo'] == ''):
				$pageNum = 1; 
			else:
				$pageNum = $_GET['pageNo'];
			endif;			
			settype($pageNum, 'integer');
		}
	else 
	 		$pageNum = 1;
	$offset = ($pageNum - 1) * $recordsPerPage;
	//collecting All training program for displaying
	$start	= $offset;
	$len	= $recordsPerPage;
	$getAllTestimoni = $objtestimoni->_displayPage('',$lanId,$start,$len);
	// HTML STARTS HERE
	$data='';
	// End of removing ajax section
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
	/*------------------------------------------------------------------------*/
	
		# to get the total count of the record
		
		$totNo	=	$objtestimoni->_getTotalCount($searchQuery = '',$lanId);		
		$numrows = 	$totNo;
		//$numrows =$getPgmCount['cnt'];
		# 4
		$maxPage = ceil($numrows/$recordsPerPage);
		if($opt_links_count) {
			$start_from = $pageNum - round($max/2) + 1; 			// = 4 - round(5/2) + 1 = 4-3+1 = 2
			$start_from = ($maxPage - $start_from < $max) ? $maxPage - $max + 1 : $start_from ; //(9-2) < 9 ? If yes, 9-5+1. | If no, no change.
			$start_from = ($start_from > 1) ? $start_from : 1;	// If it is lesser than 1, make it 1(all paging must start at the '1' page as it is the first page) : = 2
		} else { // If $opt_links_count is 0, show all pages
			$start_from = 1;
			$max = $maxPage;
		}

		$i = $start_from;
		$count = 0;
		$nav = '';
	//Display '$opt_links_count' number of links
		while($count++ < $max)
		{
			if($i == $pageNum){
			 	$nav .= "<a href='#' class='select'>$i</a>";
			  	//$nav .= $i;
			}
			else{
			 	$nav .= "<a href='".ROOT_JWPATH.$thisPage."/".$i."'>$i</a>";
			}
			$i++;
			if($i > $maxPage) break; //If the current page exceeds the total number of pages, get out.
		}
		if ($pageNum > 1)
		{
			$page  = $pageNum - 1;
			$prevurl	=	ROOT_JWPATH.$thisPage."/".$page;
			$prev  = "<a href='".$prevurl."' \">&lt;</a>";
			//$first = "<a href='".ROOT_JWPATH.$thisPage."/1' \">&lt;</a>";
		}
		else
		{
			$prev  = '';
			$first = '';
		}
		if ($pageNum < $maxPage)
		{
			$page = $pageNum + 1;
			$nexturl	=	ROOT_JWPATH.$thisPage."/".$page;
			$next = "<a href='".$nexturl."' \">&gt;</a>";
			//$last = "<a href='".ROOT_JWPATH.$thisPage."/".$maxPage."' \">&gt;</a>";
		}
		else
		{
			$next = '';
			$last = '';
		}

include("header.php");
include("menu.php");
?>
<div id="container">
  <div id="wraper_inner">
    <div class="breadcrumbs">
      <ul>
        <li><?=$parObj->_getLabenames($arrayDataHome,'searchPath','name');?></li>
        <li><a href="<?=ROOT_JWPATH?>"><?=$parObj->_getLabenames($arrayDataHome,'homeName','name');?></a></li>
        <li>></li>
        <li><a href="#" class="select"><?=$parObj->_getLabenames($arrayDataHome,'testimonials','name');?></a></li>
      </ul>
    </div>
    <div class="heading"><span class="name"><?=$parObj->_getLabenames($arrayDataHome,'testimonials','name');?></span> <span class="date"><strong>&gt; </strong><a href="<?=$backButtonLink?>" class="white"><?=$parObj->_getLabenames($arrayDataHome,'back','name');?></a></span></div>
	<?php 
		for($i=0;$i<count($getAllTestimoni);$i++){	
		$image1	= "/home/sites_web/client/newdesign/uploads/testimonials/".$objGen->_output($getAllTestimoni[$i]['user_image']);
			$image	=	"/uploads/testimonials/".$objGen->_output($getAllTestimoni[$i]['user_image']);
			if(is_file($image1))
			{ 
				$image	=	$image;
			}
			else
			{ 
				if(isset($_GET['pageNo']))
				{ 
					$image	=	"../images/photo.jpg";
				}
				else
				{
					$image	=	"images/photo.jpg";
				}
			}
			$testimonial_by = $objGen->_output($getAllTestimoni[$i]['user_name']);

			$content = substr($objGen->_output($getAllTestimoni[$i]['testimonial_desc']),0, 60);
			$pos = strrpos($content, " ");
			if ($pos>0) {
				$content = substr($content, 0, $pos);
			}
	?>
    <div class="col-3">
      <div class="clints">
        <div class="name"><?php echo $testimonial_by;?></div>
        <div class="frame"></div>
        <img src="<?php echo $image;?>" alt="lusija" /> </div>
      <div class="testimonials">
        <h2><?php echo $content;?>&hellip;</h2>
        <?php echo nl2br($objGen->_output($getAllTestimoni[$i]['testimonial_desc']));?>
      </div>
      <div class="clear"></div>
    </div>
    <hr class="blu2" />
	<?php
	}
	?>
    
    <div class="last-col">
      <div class="left">
        <div class="sign-up-col">
          <div class="corner"></div>
          <div class="corner-rit"></div>
          <a href="<?=ROOT_JWPATH?>userreg1.php" class="sign"><?=$parObj->_getLabenames($arrayDataHome,'testimonial_signup_button','name')." >>>";?></a><?php /*?> <a href="<?=ROOT_JWPATH?>userreg1.php" class="meroon pad-lft"><?=$parObj->_getLabenames($arrayDataHome,'testimonial_signup_session','name');?></a><?php */?></div>
      </div>
	  <!---------------------------------------------------------------------------->
      <div class="paging">
        <ul id="pagination"><li><?php
		
		echo $prev,$nav,$next; 
		?></li>
        </ul>
      </div>
      <div class="result"><?=$parObj->_getLabenames($arrayDataHome,'testimonial_result','name');?> <span><?php echo $pageNum;?>- <?php echo $start+$recordsPerPage;?></span> / <?php echo $numrows;?> </div>
      <div class="clear"></div>
    </div>
  </div>
  <div class="clear"></div>
</div>
<?php
include("footer.php"); 
?>
