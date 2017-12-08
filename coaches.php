<?php 
session_start();

include_once('includeconfig.php');
include_once("includes/classes/class.programs.php");
include_once('includes/classes/class.CMS.php');
include_once('includes/classes/class.coaches.php');

if($lanId=="") $lanId=1;

$objGen     	= new General();
$objTraining	= new Programs($lanId);
$parObj 		= new Contents('coaches.php');
$objCMS			= new CMS($lanId);
$objCoaches		= new coaches($lanId);

//collecting data from the xml for the static contents
$returnData		= $parObj->_getTagcontents($xmlPath,'coachesNew','label');
$arrayData		= $returnData['general'];

//Retreaving Coaches Categories
if($lanId==5)
$coachesCatData=$objCoaches->_getAllContent_newdesign($lanId,'coach_category','DESC');
else
$coachesCatData=$objCoaches->_getAllContent_newdesign($lanId);
?>

<?php include("header.php");  ?>
      <script type="text/javascript" src="js/tabber.js"></script>
       <link type="text/css" rel="stylesheet" href="resources/jquery.pwstabs-1.2.1.css">  

 <div class="frame3">
         <div class="row-1" style="padding-bottom:0">
			 <div class="return">
				 <a href="<?=$backButtonLink?>" class="small">
				 <?=$parObj->_getLabenames($arrayData,'newBackTxt','name');?>
				 </a>
            </div>
         <div class="title">
          <ul class="bredcrumbs">
              <li>
          <?=$parObj->_getLabenames($arrayData,'newHmeTxt','name');?>
          :</li>
        <li><a href="<?=ROOT_JWPATH?>index.php">
          <?=$parObj->_getLabenames($arrayData,'newIndxTxt','name');?>
          </a></li>
        <li>></li>
        <li><a href="#" >
          <?=$parObj->_getLabenames($arrayData,'newPgeTxt','name');?>
          </a></li>
           </ul>
           <h3 class="hed-2"><?=$parObj->_getLabenames($arrayData,'newHeadTxt','name');?></h3>
      </div>
         
         </div>
         
        
      <?php
		$contentTitle	= 'COACHES_TOPNEW';
		$contents 		= $objCMS->_getContent($contentTitle, $lanId);
		echo $contents['content_body']; 
	?>
	
	
	
	
	

<!--
     <div class="tabbertab">
     <h2>  Les Coachs</h2>
	          <div class="coach-detail">
                <figure>
                   <img src="images/corner.png" class="corner">
                <img src="images/coach-01.jpg" alt="coach"></figure>
                
                <article>
                  <p class="hed">Pascal Choisel</p>
                  <ul>
                    <li>Coach Jiwok</li>
                    <li>Coordonnateur du pôle France de la fédération française de Triathlon</li>
                    <li>Entraîneur de sportifs(ves) de haut niveau au pôle triathlon de Montpellier</li>
                    <li>Pascal et Stéphanie ont entrainé et entrainent les Champion de France junior , Champion du Monde Universitaires, Vice Champion de France junior , Vice Champion de France moins de 23 ans , 3ème Championnat du Monde moins de 23 ans, Vice Championne de France Elite , Championne du Monde junior, Champion du Monde militaire individuel , Vice championne d’Europe junior, Championne de France junior, etc etc</li>
                  </ul>
                
                </article>
             </div>
             <div class="coach-detail">
                <figure>
                   <img src="images/corner.png" class="corner">
                <img src="images/coach-01.jpg" alt="coach"></figure>
                
                <article>
                  <p class="hed">Pascal Choisel</p>
                  <ul>
                    <li>Coach Jiwok</li>
                    <li>Coordonnateur du pôle France de la fédération française de Triathlon</li>
                    <li>Entraîneur de sportifs(ves) de haut niveau au pôle triathlon de Montpellier</li>
                    <li>Pascal et Stéphanie ont entrainé et entrainent les Champion de France junior , Champion du Monde Universitaires, Vice Champion de France junior , Vice Champion de France moins de 23 ans , 3ème Championnat du Monde moins de 23 ans, Vice Championne de France Elite , Championne du Monde junior, Champion du Monde militaire individuel , Vice championne d’Europe junior, Championne de France junior, etc etc</li>
                  </ul>
                
                </article>
             </div>
             <div class="coach-detail last">
                <figure>
                   <img src="images/corner.png" class="corner">
                <img src="images/coach-01.jpg" alt="coach"></figure>
                
                <article>
                  <p class="hed">Pascal Choisel</p>
                  <ul>
                    <li>Coach Jiwok</li>
                    <li>Coordonnateur du pôle France de la fédération française de Triathlon</li>
                    <li>Entraîneur de sportifs(ves) de haut niveau au pôle triathlon de Montpellier</li>
                    <li>Pascal et Stéphanie ont entrainé et entrainent les Champion de France junior , Champion du Monde Universitaires, Vice Champion de France junior , Vice Champion de France moins de 23 ans , 3ème Championnat du Monde moins de 23 ans, Vice Championne de France Elite , Championne du Monde junior, Champion du Monde militaire individuel , Vice championne d’Europe junior, Championne de France junior, etc etc</li>
                  </ul>
                
                </article>
             </div>
     </div>


     <div class="tabbertab">
	  <h2> Les Nutritionnistes</h2>
        <div class="coach-detail">
                <figure>
                   <img src="images/corner.png" class="corner">
                <img src="images/coach-01.jpg" alt="coach"></figure>
                
                <article>
                  <p class="hed">Pascal Choisel</p>
                  <ul>
                    <li>Coach Jiwok</li>
                    <li>Coordonnateur du pôle France de la fédération française de Triathlon</li>
                    <li>Entraîneur de sportifs(ves) de haut niveau au pôle triathlon de Montpellier</li>
                    <li>Pascal et Stéphanie ont entrainé et entrainent les Champion de France junior , Champion du Monde Universitaires, Vice Champion de France junior , Vice Champion de France moins de 23 ans , 3ème Championnat du Monde moins de 23 ans, Vice Championne de France Elite , Championne du Monde junior, Champion du Monde militaire individuel , Vice championne d’Europe junior, Championne de France junior, etc etc</li>
                  </ul>
                
                </article>
             </div>
             <div class="coach-detail last">
                <figure>
                   <img src="images/corner.png" class="corner">
                <img src="images/coach-01.jpg" alt="coach"></figure>
                
                <article>
                  <p class="hed">Pascal Choisel</p>
                  <ul>
                    <li>Coach Jiwok</li>
                    <li>Coordonnateur du pôle France de la fédération française de Triathlon</li>
                    <li>Entraîneur de sportifs(ves) de haut niveau au pôle triathlon de Montpellier</li>
                    <li>Pascal et Stéphanie ont entrainé et entrainent les Champion de France junior , Champion du Monde Universitaires, Vice Champion de France junior , Vice Champion de France moins de 23 ans , 3ème Championnat du Monde moins de 23 ans, Vice Championne de France Elite , Championne du Monde junior, Champion du Monde militaire individuel , Vice championne d’Europe junior, Championne de France junior, etc etc</li>
                  </ul>
                
                </article>
             </div>
             
     </div>


     <div class="tabbertab">
	  <h2> &gt;  Les Médecins</h2>
        <div class="coach-detail last">
                <figure>
                   <img src="images/corner.png" class="corner">
                <img src="images/coach-01.jpg" alt="coach"></figure>
                
                <article>
                  <p class="hed">Pascal Choisel</p>
                  <ul>
                    <li>Coach Jiwok</li>
                    <li>Coordonnateur du pôle France de la fédération française de Triathlon</li>
                    <li>Entraîneur de sportifs(ves) de haut niveau au pôle triathlon de Montpellier</li>
                    <li>Pascal et Stéphanie ont entrainé et entrainent les Champion de France junior , Champion du Monde Universitaires, Vice Champion de France junior , Vice Champion de France moins de 23 ans , 3ème Championnat du Monde moins de 23 ans, Vice Championne de France Elite , Championne du Monde junior, Champion du Monde militaire individuel , Vice championne d’Europe junior, Championne de France junior, etc etc</li>
                  </ul>
                
                </article>
             </div>
             
             
     </div>
-->
<div class="coaches">
         
<div class="tabberlive">
<ul class="tabbernav">
        <script type="text/javascript">
			var catCount=0;
			<?php if(count($coachesCatData)>0){ ?>
				catCount=<?php echo count($coachesCatData); ?>;  // Setting the number of categories
				$(document).ready(function(){ drop(0);
					selectCat(0);  // Select the first category on page load
				});
			<?php } ?>
		</script>
        <?php 
		$i=0;
		foreach($coachesCatData as $catData){ 
		?>
<!--
			<li><a href="javascript:selectCat('<?php echo $i; ?>');" id="catId_<?php echo $i;?>" class="">&gt; <?php echo stripslashes($catData["coach_category"]); ?></a> </li> 
-->
			<li class=" " id ="drop_<?php echo $i;?>"><a href="javascript:selectCat('<?php echo $i; ?>');" onClick="drop(<?php echo $i; ?>);" id="catId_<?php echo $i;?>" class="">&gt; <?php echo stripslashes($catData["coach_category"]); ?></a></li> 
		<?php
		$i+=1;
		}
		?>
  </ul>
  <script>
  function drop(elementId){unsetAllCatnew();document.getElementById("drop_"+elementId).setAttribute("class","tabberactive");
	  document.getElementById("catId_"+elementId).className="tabberactive";
	 function unsetAllCatnew(){var i=0;for(i=0;i<catCount;i++){document.getElementById("drop_"+i).setAttribute("class","");
		 document.getElementById("drop_"+i).className="";document.getElementById("catCntntHldr_"+i).style.display="none";}}
	  }
  </script>
 
      <?php 
		$i=0;
		foreach($coachesCatData as $catData){ 
		?>
			   <div class="tabbertab"> <div  id="catCntntHldr_<?php echo $i; ?>" style="display:none;"> <?php echo stripslashes($catData["coach_category_description"]);?> </div></div>
			  <?php
			$i+=1;
		}
		?>
      <div class="clear"></div>
    </div>
</div>

       
         
         
         
         
         
         
      <?php 
		$contentTitle	= 'COACHES_BOTTOMNEW';
		$contents 		= $objCMS->_getContent($contentTitle, $lanId);
		if($_SERVER['HTTP_HOST'] == "www.jiwok.com.jiwok-wbdd2.najman.lbn.fr")
		{ 
			$contents['content_body'] 		 =	str_replace("http://www.jiwok.com","http://www.jiwok.com.jiwok-wbdd2.najman.lbn.fr",$contents['content_body']);
		}
		echo $contents['content_body']; 
	?>
    
  </div> 
  
   
         
  
<?php include("footer.php"); ?>

