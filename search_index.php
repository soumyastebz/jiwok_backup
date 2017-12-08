<?php 
include_once("includes/classes/class.Search.php");
$searchObj      = new Search();
$wizard_goals	= $searchObj->getWizardGoals($lanId);
$wizard_levels	= $searchObj->getLevels($lanId);
array_walk_recursive($wizard_levels, array($searchObj, '_utf8encode'));
$wizard_rythms   = $searchObj->getWizardRythms($lanId);
array_walk_recursive($wizard_rythms, array($searchObj, '_utf8encode'));
// get all sports
$wizard_sports   = $searchObj->getWizardSports($lanId);
array_walk_recursive($wizard_rythms, array($searchObj, '_utf8encode'));
$returnData		= $parObj->_getTagcontents($xmlPath,'listprograms','label');
$arrayDataSearch		= $returnData['general'];
$returnData		= $parObj->_getTagcontents($xmlPath,'searchWizard','label');
$arrayDataWiz	= $returnData['general'];
include_once('search_redirect.php');
?>
<script>
function validateSearch()
{   
	var goal			=	document.getElementById('user_goal').value;
	var level			=	document.getElementById('user_level').value;
	var lang			=	document.getElementById('langfield').value;
	var cont_goal		=	document.getElementById('cont_goal').value;
	var cont_level		=	document.getElementById('cont_level').value;
	if(lang	==	1)
	{
		var cont_session	=	document.getElementById('cont_session').value;
		var session	=	document.getElementById('user_no_session').value;
	}
	else
	{
		var cont_sport		=	document.getElementById('cont_sport').value;
		var sport	=	document.getElementById('user_sport').value;
	}
	if(goal	==	""	&&	level	==	""	&&	sport	==	"")
	{
    	document.getElementById('alertMsgSearch').innerHTML="<?=$parObj->_getLabenames($arrayDataWiz,'search_empty','name');?>";
		var w	=	300;
		var h	=	300;
		var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    	var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;
    	var left = ((screen.width / 2) - (w / 2)) + dualScreenLeft;
    	var top = ((screen.height / 2) - (h / 2)) + dualScreenTop;
		$('#searchAlertMsg').css({"position":"fixed","top":top});
		$('#searchAlertMsg').css({"position":"fixed","left":left});
		$('#searchAlertMsg').fadeIn("slow"); 
		return false;
	}
	else if(goal	==	""	&&	level	==	""	&&	session	==	"")
	{   
		document.getElementById('alertMsgSearch').innerHTML="<?=$parObj->_getLabenames($arrayDataWiz,'search_empty','name');?>";
		var windowWidth=window.screen.availWidth;
		var windowHeight=window.screen.availHeight;
		var dimhgt	=	windowHeight/2;
		var dimwdth	=	windowWidth/2;
	    $('#searchAlertMsg').css({"position":"fixed","top":windowHeight/2});
		$('#searchAlertMsg').css({"position":"fixed","left":windowWidth/2});
		$('#searchAlertMsg').fadeIn("slow"); 
		return false;
	}
}
function assignchoice()
{
			var lang	=	document.getElementById('langfield').value;
			
			if(document.getElementById('user_goal').value != ""){
			var text_goal			=	document.getElementById('user_goal').options[document.getElementById('user_goal').selectedIndex].text;
			text_goal				=	text_goal.split(' ').join('-');
			$('#cont_goal').attr("value",text_goal);
		}
		else{
			var text_goal			=	"";
			$('#cont_goal').attr("value",text_goal);
		}
		if(document.getElementById('user_level').value != ""){
			var text_level			=	document.getElementById('user_level').options[document.getElementById('user_level').selectedIndex].text;
			text_level				=	text_level.split(' ').join('-');
			$('#cont_level').attr("value",text_level);
		}
		else{
			var text_level			=	"";
			$('#cont_level').attr("value",text_level);
		}
		if(lang	==	1){
			if(document.getElementById('user_no_session').value != ""){
				var text_session	=	document.getElementById('user_no_session').options[document.getElementById('user_no_session').selectedIndex].text;
				$('#cont_session').attr("value",text_session);
			}
			else{
				var text_session	=	"";
				text_session		=	text_session.split(' ').join('-');
				$('#cont_session').attr("value",text_session);
			}
		}
		else{
			if(document.getElementById('user_sport').value != ""){
				var text_sport		=	document.getElementById('user_sport').options[document.getElementById('user_sport').selectedIndex].text;
				$('#cont_sport').attr("value",text_sport);
			}
			else{
				var text_sport		=	"";
				text_sport			=	text_sport.split(' ').join('-');
				$('#cont_sport').attr("value",text_sport);
			}
		}
}
$(document).ready(function(){
        $("#okIdsearchAlert").click(function(){
		$('#searchAlertMsg').fadeOut("slow");
		});
		$(document).keypress(function(e){
			if(e.keyCode==27){
				$('#searchAlertMsg').fadeOut("slow");
			}
		});
	});
</script>

  <!--for search error popup -->             
  <div  id="searchAlertMsg" class="pop_search" style="display:none; left: 140px; position: absolute; top: 47.5px; z-index: 9999; opacity: 1;">
  <div class="popbox_search">
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center" ><h3><div id="alertMsgSearch"></div></h3><br/><br/></td>
      </tr>
      <tr>
        <td align="center"><a id="okIdsearchAlert"><input class="btn_pop ease"  name="renewSubscriptionIdBtn" type="button" value="Ok" /></a></td>
      </tr>
    </table>
    <div class="clear"></div>
  </div>
</div>
<!--for search error popup-->
<div class="frame grid_first mid-wrapper">

       <section class="training">
          <p>
             JIWOK
            <br> 
             <?=$parObj->_getLabenames($arrayDataWiz,'label_drive','name');?>
          </p>
          <a href="<?=JIWOK_URL?>search.php" class="btn_orng">
           <?=$parObj->_getLabenames($arrayDataWiz,'label_button','name');?>
           </a>
      </section>
      <section class="form" >
      <form name="searchWizard"  method="get">
      <div>
      <p>
        <label>
			<strong><?=$parObj->_getLabenames($arrayDataWiz,'searchOpt1','name');?></strong> 
        </label>
        <div class="selet3">
        <select name="user_goal" id="user_goal"  onchange="return assignchoice();" >
     	<option value="" selected="selected"><? if($lanId	==	5)
		{
			echo $parObj->_getLabenames($arrayDataSearch,'select_goal','name'); 
		}
		else
	 	{
			echo $parObj->_getLabenames($arrayDataSearch,'select','name'); 
		}?> </option>
		<?php foreach($wizard_goals as $wizard_goal){ ?>
	        <?php if($lanId == 1) {
				if($wizard_goal['flex_id'] != 'gol11' && $wizard_goal['flex_id'] != 'gol10'){?>			
			        <option value="<? echo $wizard_goal['flex_id'];?>" ><? echo $wizard_goal['item_name'];?></option>
           		<?php }
			}else {?>
				<option value="<? echo $wizard_goal['flex_id'];?>" ><? echo $wizard_goal['item_name'];?></option>
            <? }?>
		<? }?>
     </select>
     <input type="hidden" name="cont_goal" id="cont_goal" value=""/>

     </p> 
        </div>
     <p>
     <label><strong> 
		 <?=$parObj->_getLabenames($arrayDataWiz,'searchOpt2','name');?>
            </strong> 
		 </label>
      <div class="selet3">
     <select name="user_level" class="" id="user_level" onChange="return assignchoice();">
	 <option value="" selected="selected">
		 <?php  if($lanId	==	5){
			echo $parObj->_getLabenames($arrayDataSearch,'select_level','name');
		 	}
			else
			{
				echo $parObj->_getLabenames($arrayDataSearch,'select','name'); 
			}?>
     </option>
			<?php
			foreach($wizard_levels as $wizard_level_id=>$wizard_level_value){
			$wizard_level_item_name=$parObj->_getLabenames($arrayDataWiz,"jiwok_level".$wizard_level_id,'name');
			if($wizard_level_item_name==""){
				$wizard_level_item_name=htmlentities(utf8_decode($wizard_level['item_name']));}
			?>
     <option value=<?=$wizard_level_id ?>><?=$wizard_level_item_name?></option>
			<?php } ?>
	 </select>
      <input type="hidden" name="cont_level" id="cont_level" value=""/>   
      </div>
      </p> 
       <p>
         <label>
		  <h5>
     <?=$parObj->_getLabenames($arrayDataWiz,'searchOpt3','name');?>
          </h5>
	     </label>
<div class="selet3">
         <?php if($lanId == 1) { ?>

        <select name="user_no_session" class="list-box-1" id="user_no_session" onChange="return assignchoice();">



              <option value="" selected="selected">



              <?=$parObj->_getLabenames($arrayDataSearch,'select','name');?>



              </option>



              <?php foreach($wizard_rythms as $rythm_id => $rythm_value){ ?>



              <option value="<?php echo $rythm_id; ?>" ><?php echo $rythm_value; ?></option>



              <? }?>



            </select>
           <input type="hidden" name="cont_session" id="cont_session" value=""/>

<?php }else{ ?>

          <select name="user_sport" class="list-box-1" id="user_sport"  onchange="return assignchoice();">



              <option value="" selected="selected">



              <?php if($lanId ==5)
                 	{
						echo $parObj->_getLabenames($arrayDataSearch,'select_sport','name');
					}
					else
					{
						echo $parObj->_getLabenames($arrayDataSearch,'select','name');
					}?>



              </option>
               <?php foreach($wizard_sports as $sports_row){ ?>
               <!------------------Temporary adjustment for polish sports hiding----------------->
				<?php 
                 if($lanId ==5)
                 {
                    
                 if(!(($sports_row['flex_id']==16) || ($sports_row['flex_id']==6) || ($sports_row['flex_id']==13)))
                 {?>
                <option value="<?php echo $sports_row['flex_id']; ?>" ><?php echo $sports_row['item_name']; ?></option>
                <? }
                 }
             else {?>
             <option value="<?php echo $sports_row['flex_id']; ?>" ><?php echo $sports_row['item_name']; ?></option>
             <? }}?>
             </select>
             <input type="hidden" name="cont_sport" id="cont_sport" value=""/>
             <?php }?>
             <input type="hidden" name="langfield" id="langfield" value="<?= $lanId ;?>"/>
           </p>
           </div>
      </div>
             <input type="submit" class="btn_blu ease" name="search" value="<?php echo $parObj->_getLabenames($arrayData,'newSearchTxt','name');?>" onClick="return validateSearch();"  />
     </form>
      </section>
      
</div>
