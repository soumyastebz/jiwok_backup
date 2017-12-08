<?php
session_start();
require_once 'includeconfig.php';
require_once 'includes/classes/class.coaches.php';
$lanId	=	$_REQUEST["lanId"];
$objCoaches		= new coaches($lanId);
if($_REQUEST["category_id"]!=""){
	$category_id	=	$_REQUEST["category_id"];	
	//initial statge 
	$coachesCatData=$objCoaches	->_getCategoryDetails_home($category_id,$lanId);
	$coachesCatList=$objCoaches	->_getCategorylist_home();
}
?>
<!--not needed
<link href="resources/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery.min.js"></script>
<script src="js/responsiveslides.min.js"></script>-->
<script>
    $(function () {
     $("#slider3").responsiveSlides({
        auto: true,
        pager: true,
        nav: false,
        speed: 800,
        namespace: "callbacks",
        before: function () {
          $('.events').append("<li>before event fired.</li>");
        },
        after: function () {
          $('.events').append("<li>after event fired.</li>");
        }
      });
    });
    </script>
      <ul class="rslides" id="slider3">
		<?php 
		$i=0;	    
		foreach($coachesCatData as $catData){
	
		?>
	   <li>
       <?php
	  
				if($catData['coach_name'] == "Stéphanie Gross")
				{
					$coach_image="stephanie.jpg";
				}
				elseif($catData['coach_name'] == "Cédric Deanaz")
				{
					$coach_image="cedrivc_home.jpg";
				}
				elseif($catData["coach_categorymaster_id"]=="3"){
				$coach_image="pascal+choisel.jpg";
				}
				else{
				$coach_image="slide-second_01.jpg";
				}
	   ?>
	    <div class="image">
        <img src="<?=ROOT_FOLDER?>images/corner.png" class="corner">
        <img src="<?=ROOT_FOLDER?>images/<?=$coach_image?>" alt="Slide 01"></div>
        <div class="caption"> 
			  <h2><?php echo $catData["coach_name"];?></h2>
			  <p class="third-line">
			  <?php
			   $len=strlen($catData["coach_category_description"]);
			   
			   echo substr(nl2br($catData["coach_category_description"]),0,250);
			   if($len>250){
				   echo "<a href='coaches.php'>...</a>";
			   }
			   ?></p>
		</div>
        </li>
        <?php
		$i+=1;
		}
		?>
      </ul> 
      <div class="link_call">
       <?php 
		 $i=0;
		foreach($coachesCatList as $catData){ 
	   ?>
        <!--<input type="button" onClick="selectCat('<?php echo $catData["id"]; ?>')" id="catId_<?php echo $i;?>" value="<?php echo stripslashes($catData["coach_category"]); ?>">-->
        <a href="javascript:selectCat('<?php echo $catData["id"]; ?>');" id="catId_<?php echo $i;?>" class="ease"> <?php echo stripslashes(mb_strtoupper($catData["coach_category"],'UTF-8')); ?></a>
       <?php
		 $i+=1;
		}
	   ?>
	  </div>
<?php
exit;
?>
