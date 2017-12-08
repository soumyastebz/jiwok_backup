<?php
	session_start();
	ob_start();
	include_once('includeconfig.php');
	include_once("includes/classes/class.programs.php");
    include_once("includes/classes/class.Search.php");
	
	if($_SESSION['user']['userId']==''){
		header('location:index.php');
		}else{
		$userId	=	$_SESSION['user']['userId'];
		}
		
	if($lanId=="")
		 $lanId=1;
		 
	$objGen     	= new General();
	$objTraining	= new Programs($lanId);
	$parObj 		= new Contents('singlePrograms.php');
    $searchObj      = new Search();
	
	$wizard_goals	= $searchObj->getWizardGoals($lanId);

	$wizard_levels	= $searchObj->getLevels($lanId);
	array_walk_recursive($wizard_levels, array($searchObj, '_utf8encode'));

    $wizard_rythms   = $searchObj->getWizardRythms($lanId);
	array_walk_recursive($wizard_rythms, array($searchObj, '_utf8encode'));
    // get all sports

	$wizard_sports   = $searchObj->getWizardSports($lanId);
	array_walk_recursive($wizard_rythms, array($searchObj, '_utf8encode'));	/* New code for SEO */			

$returnData		= $parObj->_getTagcontents($xmlPath,'searchWizard','label');
			$arrayDataWiz	= $returnData['general'];			$returnDataPG		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
			$arrayDataPG		= $returnDataPG['general'];

			//collecting data from the xml for the static contents

			$returnDataList		= $parObj->_getTagcontents($xmlPath,'listprograms','label');
			$arrayDataList		= $returnDataList['general'];			//collecting data from the xml for the static contents
	$headingData	= $parObj->_getTagcontents($xmlPath,'listprograms','pageHeading');
	
	
	//collecting data from the xml for the static contents
	$returnData		= $parObj->_getTagcontents($xmlPath,'listprograms','label');
	$arrayData		= $returnData['general'];
	
	$returnDataList		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
	$arrayDataPgm		= $returnDataList['general'];
	
	include('header.php');
	$getAllTraining = $objTraining->_getAllSingleTrainingPrograms($userId,'','',$lanId);
	$trn_cou = count($getAllTraining);
	//echo "<pre/>";print_r(	$getAllTraining);exit;
	$result	=	'';
	$data =  '';
	if($trn_cou >0)
	{ 
	
	$data .= '<div class="bnr-content">
              <div class="inner">
			  <div class="heading3 entrainment"><div><h1>'.mb_strtoupper($parObj->_getLabenames($arrayData,'single','name'),'UTF-8').'</h1></div></div>
              <div class="line2 second"><p>&nbsp;</p></div>
              <div class="bnr-content2">
              <div>';
	
		$data .= '<p>'.$parObj->_getLabenames($arrayDataPgm,'single','name').'</p></div>
                    </div>
                </div>
           </div>';
		$result .= ' <section class="JW_ents">';
		for($i=0;$i<$trn_cou;$i++)
		{
			
	//HTML DATA TO BE DISPLAY ON PARENT PAGE
	 	
		$target		=	$objGen->_output($getAllTraining[$i]['program_target']);
		$pgmFor 	= 	$objTraining->_getGroups(trim($getAllTraining[$i]['program_for']),$lanId,'group');
		$schedule 	= 	$objTraining->_getName1(trim($getAllTraining[$i]['schedule_type']),$lanId,'schedule_type');
		$pgmLevel 	= 	$objTraining->_getName1(trim($getAllTraining[$i]['program_level_flex_id']),$lanId,'levels');
		$cntTarget	=	strlen($target);
		
		if(mb_strlen($objGen->_output($getAllTraining[$i]['program_title']), 'UTF-8') > 90)
					 { 
						$pgm_title_org	=	 mb_substr($objGen->_output($getAllTraining[$i]['program_title']),0,90 ).'...';
					 }
					 else
					 {
						  $pgm_title_org = $objGen->_output($getAllTraining[$i]['program_title']);
					  }
		
		$result .= '<div class="colums">';
		/*if($objGen->_output($getAllTraining[$i]['program_image'])!="")
		{
			$data.="<div class='image'><img src='uploads/programs/".$objGen->_output($getAllTraining[$i]['program_image'])."' alt='Jiwok' width='118' height='140' /></div>";
		}
		else
		{
			$data.="<div class='image'><img src='images/no_photo_pgm.jpg' alt='program' border='0' width='118' height='140' /></div>";
		}*/
	//image list
		$result	.=	'<figure>
                  <img src="'.ROOT_FOLDER.'images/corner-4.png" alt="jiwok" class="corner">
                  <img src="'.ROOT_FOLDER.'images/dummy-2.jpg" alt="'.htmlentities($altname).'">
                </figure>';
		$result.= '<article>';
		$result.= '<form>';
		$result.="<a href='".ROOT_JWPATH."program_generate2.php?program_id=".base64_encode($getAllTraining[$i]['program_id'])."'><h2 class=\"font-x-1\">".$pgm_title_org."</h2></a>";
		$result.=	'<div class="listing">
       <div class="left"><span>'.$parObj->_getLabenames($arrayDataPgm,'duration','name').'</span></div>
       <div class="right">'.$objGen->_output(trim($getAllTraining[$i]['program_schedule']))." ".$objGen->_output($schedule).'</div>
     </div>';
	  $result.=	'<div class="listing">
       <div class="left"><span>'.$parObj->_getLabenames($arrayDataPgm,'level','name').'</span></div>
       <div class="right">'.$objGen->_output(trim($pgmLevel)).'</div>
     </div>';
	 $result.=	'<div class="listing">
       <div class="left"><span>'.$parObj->_getLabenames($arrayDataPgm,'for','name').'</span></div>
       <div class="right">'.$objGen->_output(trim($pgmFor)).'</div>
     </div>';
		$result.='<div class="rating">
           <img src="'.ROOT_FOLDER.'images/rating-star_small.png" alt="rating">
           <img src="'.ROOT_FOLDER.'images/rating-star_small.png" alt="rating">
           <img src="'.ROOT_FOLDER.'images/rating-star_small.png" alt="rating">
           <img src="'.ROOT_FOLDER.'images/rating-star_small.png" alt="rating">
           <img src="'.ROOT_FOLDER.'images/rating-star_small.png" alt="rating">
         </div>';	
		
		$result.="</form>";
		$result.= '<a  class="btn" href="'.ROOT_JWPATH.'program_generate2.php?program_id='.base64_encode($getAllTraining[$i]['program_id']).'">'.mb_strtoupper("Découvrir cet entraînement","UTF-8").'</a>';
		$result.= '</article> ';   
		$result .=	'</div>';
		
		}
		$result .= ' </section>';
		
	}
	else
	{ 
	
	//$programListUrl	=	"http://www.jiwok.com/entrainement/Seance+unique+%2830+min-+1+heure...%29-11";
	$programListUrl	=	ROOT_FOLDER."entrainement/seance-unique-%2830-min--1-heure...%29-11";
	if($lanId==1){
		$programListUrl	=	"http://www.jiwok.com/en/training/Single+workout+%2830+min-+1+hour...%29-11";
	}
	
	if($lanId==5){
		$programListUrl	=	ROOT_FOLDER."pl/entrainement/pojedyncze-treningi-%28np.-trening-30-minutowy%29-11";
	}
	
	//===========================
	$data .= '<div class="bnr-content">
                <div class="inner">
				<div class="heading3 entrainment"><div><h1>'.mb_strtoupper($parObj->_getLabenames($arrayData,'single','name'),"UTF-8").'</h1></div></div>
                  <div class="line2 second"><p>&nbsp;</p></div>
                                <div class="bnr-content2">
                        <div>';
	
	//============================
	
	
	
	$data .= $parObj->_getLabenames($arrayDataPgm,'nosingle','name')." ";
	$data .="<p>".$parObj->_getLabenames($arrayDataPgm,'nosingleNew','name')." <a href='".$programListUrl."'><u>".$parObj->_getLabenames($arrayDataPgm,'nosingleNew1','name')."</u></a></p>
	</div>
                    </div>
                </div>
           </div>
	";
	
}

?>
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
	
  equalheight('.JW_ents .colums');
});
$(window).resize(function(){
  equalheight('.JW_ents .colums');
});
</script>
<section class="banner-static">
       <div class="bred-hovr second">
          <ul class="bredcrumbs">
               <li><a href="<?=ROOT_JWPATH?>"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'searchPath','name'),"UTF-8");?>: <?=mb_strtoupper($parObj->_getLabenames($arrayData,'homeName','name'),"UTF-8");?></a></li>
               <li>&gt;</li>
               <li> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'single','name'),"UTF-8");?></li>
            </ul>
       </div>
           <div class="bnr-image"><img data-lazy-src="<?=ROOT_FOLDER?>admin/crop/assets/img/shutterstock_219803131.jpg" alt="jiwok"></div><?php echo $data; ?>
           
       
       
       </section>
       <?php if($trn_cou >0)
	{
		 echo $result; 
	}?>
    <section class="JW_search_new">
          
                      <div class="center">
                      <h2><?=$parObj->_getLabenames($arrayData,'newSearchNote','name');?></h2>
                       <form name="searchWizard" action="search_result.php" method="post">
                          <div class="colums">                         
                             <p> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'searchResultOpt1','name'),'UTF-8');?></p>
                             <div class="selet3">
                             <select name="user_goal">
                                  <option value="" selected="selected">
                                  <?=$parObj->_getLabenames($arrayData,'select','name');?>
                                  </option>
                                  <?php foreach($wizard_goals as $wizard_goal){ ?>
                                  <?php if($lanId == 1) {
                    
                                    if($wizard_goal['flex_id'] != 'gol11' && $wizard_goal['flex_id'] != 'gol10')
                    
                                    { ?>
                                  <option value="<? echo $wizard_goal['flex_id'];?>" ><? echo $wizard_goal['item_name'];?></option>
                                  <?php } } 	else {?>
                                  <option value="<? echo $wizard_goal['flex_id'];?>" ><? echo $wizard_goal['item_name'];?></option>
                                  <? }?>
                                  <? }?>
                                </select>
                                     
                             </div>
                          </div>
                          
                          <div class="colums">
                             <p><?=$parObj->_getLabenames($arrayData,'searchResultOpt2','name');?></p>
                             <div class="selet3">
                                     <select name="user_level">
                                          <option value="" selected="selected">
                                          <?=$parObj->_getLabenames($arrayData,'select','name');?>
                                          </option>
                                          <?php
                            
                                                foreach($wizard_levels as $wizard_level_id=>$wizard_level_value){
                                                
                                                $wizard_level_item_name=$parObj->_getLabenames($arrayDataWiz,"jiwok_level".$wizard_level_id,'name');
                                                
                                                if($wizard_level_item_name==""){
                                                
                                                    $wizard_level_item_name=htmlentities(utf8_decode($wizard_level['item_name']));}
                                                
                                                ?>
                                                              <option value=<?=$wizard_level_id ?>>
                                                              <?=$wizard_level_item_name?>
                                                              </option>
                                                              <?php } ?>
                                        </select>
                             </div>
                          </div>
                          
                          <div class="colums">                         
                         
                           <?php if($lanId == 1) { ?>
                            <p>
                              <?=mb_strtoupper($parObj->_getLabenames($arrayData,'searchResultOpt3','name'),"UTF-8");?>
                            </p>
                             <div class="selet3">
                                <select name="user_no_session" >
                                  <option value="" selected="selected">
                                  <?=$parObj->_getLabenames($arrayData,'select','name');?>
                                  </option>
                                  <?php foreach($wizard_rythms as $rythm_id => $rythm_value){ ?>
                                  <option value="<?php echo $rythm_id; ?>" ><?php echo $rythm_value; ?></option>
                                  <? }?>
                                </select>
                                </div>
									<?php }else{ ?>
                                    <p>
                                      <?=mb_strtoupper($parObj->_getLabenames($arrayData,'searchResultOpt4','name'),"UTF-8");?>
                                    </p>
                                     <div class="selet3">
                                    <select name="user_sport" >
                                      <option value="" selected="selected">
                                      <?=$parObj->_getLabenames($arrayData,'select','name');?>
                                      </option>
                                      <?php foreach($wizard_sports as $sports_row){ ?>
                                      <option value="<?php echo $sports_row['flex_id']; ?>" ><?php echo htmlentities(utf8_decode($sports_row['item_name'])); ?></option>
                                      <? }?>
                                    </select>
                                    </div>
                                    <?php } ?>
                             </div>
                         
                       <div class="clear"></div>
                  <div class="validate"><input type="submit" name="search" value="<?=mb_strtoupper($parObj->_getLabenames($arrayData,'searchResultBtnTxt','name'),"UTF-8");?>"></div>
                          </form>
                     
                   
      </div>
     </section>



<?php include('footer.php');?>
