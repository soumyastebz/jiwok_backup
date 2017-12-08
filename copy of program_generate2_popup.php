	


<!---------------------------------------------------------------------------------------------------------------------->
<!--/*               Tag of line pop up starts here              */-->
<!------------------------------------------------------------------------------ ---------------------------------------->

<style>
#inner_checksessionmessage td{
	text-align:left;	
}
.paymentTopDiv{
    font: bold 11px Verdana,Arial,Helvetica,sans-serif;
    padding: 0;	
	color : #FFFFFF;
}
.paymentBtn {
    padding-top: 25px;
    text-align: center !important;
}
.moreInfoPgm{
		
}
.fancyClose{
  	background: url("fancyboxcontents/fancybox/fancy_close.png") repeat scroll -153px -2px transparent !important;
    height: 24px !important;
    right: -15px !important;
    top: -20px !important;
    width: 29px !important;
}
.popup .inner table.content tr td {
    vertical-align: text-top;
	padding-bottom: 7px;
	padding-right: 7px;
}
#various1,#various2{
	color:#fff;	
	 padding-left: 5px;
}
#taggOffPopUp tr{
	display:table-row !important;
}
.popup .inner table.content {
    padding: 3px 0 0 !important;
}
.bu_03_new {
    background: url("../images/buttons_ylow.png") no-repeat scroll 0 -264px transparent;
    border: 0 none;
    color: #FFFFFF;
    cursor: pointer;
    font: bold 14px "Trebuchet MS";
    height: 32px;
    width: 170px;
}
</style>

<div class="pop_produiOverlayBox2" id="produiOverlayBox2" style="display: none; position:fixed;z-index:12;">
 <div class="popbox_produiOverlayBox2">
 		<a id="fancybox-close1" onclick="hideUnsubscribe1('produiOverlayBox2');" 
title="<?=$parObj->_getLabenames($arrayData,'close','name');?>" style="display:inline;"></a>      
     <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr id="refrsh">
        <td align="center" style="display:block" id="showRefresh">
			<input onClick="showGenres('<?=$flexid?>');" alt="show" 
title="show" type="button" name="show" id="show" value="<?=$parObj->_getLabenames($arrayData,'refresh','name');?>" 
class="orange-botton" /></td>
      </tr>
	   <tr>
        <td>
			<input type="hidden" name="workout_Flex" id="workout_Flex"/>
			<input type="hidden" name="pageNum" id="pageNum"/>
			<input type="hidden" name="remember" id="remember"/>
			<input type="hidden" name="vocal_type_h" id="vocal_type_h"/>
			<input type="hidden" name="genList" id="genList"/>
		</td>
      </tr>
</table>
   
    <span id="genreSelect" style="display:block;">
	<?php 
	if(count($genres)>0)
		{ 
	?>
	<table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
	  <tr>
		<td align="center" id="genreError" class="red-1" 

style="display:none"><?=$parObj->_getLabenames($arrayData,'choosegenreError','name');?></td>
	  </tr>
	</table>
 
	<form id="generate" name="generate" method="post">
	<table width="100%" border="0" cellspacing="2" cellpadding="2" class="content" id="taggOffPopUp">
	  <tr>
		<td align="center" colspan="2"><?=$parObj->_getLabenames($arrayData,'choosegenre','name');?></td>
	  </tr>
      <tr>
      	<td colspan="2"><hr style="color:#FFF;" /></td>
      </tr>
        <?php 	
		// Memory code starts
			$random_genre_status 		= 0;		
			$vocal_type_memory_status 	= 2;	
			$userMemoryStatus			= 0;
			$genreMemoryArr				= array();	
			$genreMemory	 		= "Not selected";
				if(count($memorizedGenres)>0){
					if($memorizedGenres[0]['remember_status']==1){
						$userMemoryStatus		= $memorizedGenres[0]['remember_status'];
						$genreMemory		= $memorizedGenres[0]['genre_name_memory'];
						$genreMemoryArr		= explode(',',$genreMemory);
						
						$vocal_type_memory_status	= $memorizedGenres[0]['vocal_coach_status'];
						$random_genre_status		= $memorizedGenres[0]['random_genre_status'];
					}			
				}
			
			// Memory code starts ends--------------
			$totalFile =	0;
			for($i=0;$i<count($genres);$i++)
				{
					$genre 			= trim(stripslashes($genres[$i]['genre_name']));
					$status_genre 	= trim(stripslashes($genres[$i]['remember_status']));
					$genre_id 		= trim(stripslashes($genres[$i]['id']));
					$file_count 	= trim(stripslashes($genres[$i]['file_count']));
					if($file_count == '')
					  $file_count 	= 0;
					$totalFile += $file_count;  
				?>
                <tr id="showGenres">
					<td align="left" valign="middle"><?php
						//For implementing additional options without disturbing other users
						if($userid!=7475)
							{
						?>
							<input name="genre" type="checkbox" 

value="<?=$genre_id."_".$file_count;?>" <?php if(in_array($genre,$genreMemoryArr)){?> checked="checked" <? }?>  

onclick="uncheckRandom(this);" />
					  <?php
							}
						else
							{
					  ?>
							<input name="genre" type="checkbox" 

value="<?=$genre_id."_".$file_count;?>" <?php if(in_array($genre,$genreMemoryArr)){?> checked="checked" <? }?>  

onclick="uncheckRandom(this);"/>
					  <?php				
							}
					  ?></td>
					  <td><?php echo $genre,'( 

',$file_count,$parObj->_getLabenames($arrayData,'songdetect','name'),' )';?></td>
                      </tr>
				 <?php
				 }	
				 ?>               
		   
	  	<tr>
			<td align="left" valign="top"><?php
		  	//For implementing additional options without disturbing other users
			if($userid!=7475)
				{
		  	?>
				<input type="checkbox" name="vocal_type" value="1" id="vocal_type" <?php 

if($vocal_type_memory_status==1){?> checked="checked" <?php }?>/>
          	<?php
				}
			else
				{					
		  	?>
				<input type="checkbox" name="vocal_type" value="1" id="vocal_type" <?php 

if($vocal_type_memory_status==1){?> checked="checked" <?php }?>/>
		 	<?php									
				}
		 	?></td>
			<td><?=$parObj->_getLabenames($arrayData,'vocalType','name')?></td>
	  	</tr>
	 	<tr>
			<td align="left" valign="top">
			  <?php
			  //For implementing additional options without disturbing other users
				if($userid!=7475)
					{
				?>
					<input type="checkbox" name="random1" value="1"  onclick="uncheckAllGenre(this);" 

id="random1" <?php if($random_genre_status==1){?> checked="checked"<?php }?>/>
			  <?php
					}
				else
					{					
				?>
					<input type="checkbox" name="random1" value="1"  onclick="uncheckAllGenre(this);" 

id="random1" <?php if($random_genre_status==1){?> checked="checked"<?php }?>/>
			  <?php									
					}
			  ?>
		  	  </td>
			  <td><?=$parObj->_getLabenames($arrayData,'randomgenre','name')?></td>
		 </tr>          
	 	<tr>
			<td align="left" valign="top">
          <?php
		  //For implementing additional options without disturbing other users
			if($userid!=7475)
				{
			?>
          		<input name="remchoice"  id="remchoice" type="checkbox" <?php if($userMemoryStatus==1){?> 

checked="checked"<?php }?>/>
          <?php
				}
			else
				{					
			?>
          		<input name="remchoice"  id="remchoice" type="checkbox" <?php if($userMemoryStatus==1){?> 

checked="checked"<?php }?>/>
          <?php						
				}				
		  ?>
		  	 </td>
			 <td><?=$parObj->_getLabenames($arrayData,'remchoice','name')?></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input class="orange-botton" name="GenerateMP3" type="button"  

value="<?=$parObj->_getLabenames($arrayData,'generateMP3','name')?>" <?php if(count($genres)==1){ ?> 

onclick="confirmSongs('<?=$flexid?>','one','<?=$totalFile?>');" <?php } else { ?> 

onclick="confirmSongs('<?=$flexid?>','','<?=$totalFile?>');" <?php } ?>  /></td>
		</tr>
         <tr>
      	<td colspan="2"><hr style="color:#FFF;" /></td>
      </tr>
    </table>
   </form>
    <?php 
		} 
	else 
		{
	?>
	<table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center"><?=$parObj->_getLabenames($arrayData,'genredetectmsg','name');?></td>
      </tr>
    </table>
    <?php 
		} 
	?>
    </span>
	<!--Pop message for : Your mp3 session is being created, we will inform you when it is ready-->
	<table width="100%" border="0" cellspacing="2" cellpadding="0" class="content" id="generateSuccess" 
style="display:none;">
      <tr>
        <td align="center"><?=$parObj->_getLabenames($arrayData,'generateSuccess','name');?></td>
      </tr>
    </table>
	<!--Pop message for :Your subscription has expired. Please make your payment in order to continue enjoying the 

services of Jiwok-->
	<table width="100%" border="0" cellspacing="2" cellpadding="0" class="content" id="errorPayment" 
style="display:none;">
      <tr>
        <td align="center"><?=$parObj->_getLabenames($arrayData,'subscribepayerror','name');?></td>
      </tr>
      <tr>
		<td align="center"><input class="blu-botton"  

value="<?=$parObj->_getLabenames($arrayData,'renewnow','name');?>" 

onClick="window.location.href='payment_renewal.php?origin=juser'" type="button"/></td>
      </tr>
    </table>
	
	<!--Pop message for :in your selected musical genres, the number of songs is quite low. It is possible that our 

software will repeat the same songs several times. Confirm your choice-->
	
	<table width="100%" border="0" cellspacing="2" cellpadding="0" class="content" id="songsConfirm" 
style="display:none;">
      <tr>
        <td align="center"><?=$parObj->_getLabenames($arrayData,'songconfirm','name');?></td>
      </tr>
      <tr>
		<td align="center"><input class="blu-botton" type="button" 

value="<?=$parObj->_getLabenames($arrayData,'confirmbutton','name');?>" onClick="insertGenres('<?=$flexid?>');"/>&nbsp; 

<input type="button" class="btn_pop ease" value="<?=$parObj->_getLabenames($arrayData,'cancelbutton','name');?>"  

onclick="cancelConfirm('<?=$flexid?>');"/></td>
      </tr>
    </table>
	<!--Pop message for :Your Jiwok software does not appear to be connected or open on your computer. Please check, 

thank you -->
	<table width="100%" border="0" cellspacing="2" cellpadding="0" class="content" id="tagOfflineDetect" 
style="display:none;">
      <tr>
        <td align="center"><?=$parObj->_getLabenames($arrayData,'tagofflineconnect','name');?></td>
      </tr>
      <tr>
		<td align="center"><input class="btn_pop ease" type="button" 
value="<?=$parObj->_getLabenames($arrayData,'okbutn','name');?>" onClick="loadTagOfflineTest();"/>&nbsp;<input 

class="btn_pop ease" type="button" value="<?=$parObj->_getLabenames($arrayData,'cancelbutn','name');?>"  

onclick="hideUnsubscribeTag('tagOfflineDetect');"/></td>
      </tr>
    </table>
	<table border="0" cellspacing="0" cellpadding="0" align="center" class="content">
	<!--Pop message for :The same session is already being created. -->
      <tr id="alreadyGenerated" style="display:none;" height="30">
        <td align="center" class="white" valign="top"><?=$parObj->_getLabenames($arrayData,'alreadyGenerated','name');?></td>
      </tr>
	<!--Pop message for :You cannot generate this meeting. In fact, you can only generate sessions over the next 31 days 

to avoid saturating our system.  -->
      <tr id="giftCodeUserMp3Alert" style="display:none;">
		<td align="center"><?php
		$text	=	$parObj->_getLabenames($arrayData,'giftCodeUserMp3Alert','name');
		$url	=	"<a href='".$parObj->_getLabenames($arrayData,'giftCodeTicketUrl','name')."'>"
					.$parObj->_getLabenames($arrayData,'giftCodeTickeReplace','name')."</a>";
		$text	=	str_replace("##",$url,$text);
		echo $text;
		?></td>
      </tr>
    </table>	
    <div class="clear"></div>&nbsp;
  </div>
</div>
<!---------------------------------------------------------------------------------------------------------------------->
<!--/*               Tag of line pop up ends here              */-->
<!---------------------------------------------------------------------------------------------------------------------->


<div class="popup" id="unsubscribePgm" style="display:none;position:fixed;z-index:10;">
<!--
  <div><img src="images/pop-top.png" alt="jiwok" /></div>
-->
  <div class="inner"> <a id="fancybox-close" onclick="hideUnsubscribe1('unsubscribePgm');" title="close" style="display: 

inline;"></a>
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center"><?=$parObj->_getLabenames($arrayData,'unsubscribeconfirm','name');?></td>
      </tr>
      <tr>
        <td align="center"><input class="blu-botton" name="Yes" type="button" 

value="<?=$parObj->_getLabenames($arrayData,'yes','name');?>" 

onclick="unsubscribeProgram('<?=$pgmSubscribe['programs_subscribed_id']?>','<?=$flexid?>')" />
          &nbsp;
          <input class="blu-botton" name="No" type="button" value="<?=$parObj->_getLabenames($arrayData,'no','name');?>" 

onclick="hideUnsubscribe('unsubscribePgm');" /></td>
      </tr>
    </table>
    <div class="clear"></div>
  </div>
 
</div>

<!--Popup box will display with the following message : You are not a member. Would you like to take another training program 

-->

<div class="popup" id="otherSearch" style="display:none; position:fixed; z-index:10;">
<!--
  <div><img src="images/pop-top.png" alt="jiwok" /></div>
-->
  <div class="inner"> <a id="fancybox-close" onclick="hideUnsubscribe('otherSearch');" title="close" style="display: 

inline;"></a>
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td colspan="2" align="center"><?=$parObj->_getLabenames($arrayData,'searchconfirm','name');?></td>
      </tr>
      <tr>
        <td colspan="2" align="center"><input class="blu-botton" name="Yes" type="button" 

value="<?=$parObj->_getLabenames($arrayData,'yes','name');?>" 

onclick="document.getElementById('otherSearch').style.display='none';window.location.href='search.php';" />
          &nbsp;
          <input class="blu-botton" name="No" type="button" value="<?=$parObj->_getLabenames($arrayData,'no','name');?>" 

onclick="window.location.href='userArea.php';" /></td>
      </tr>
    </table>
    <div class="clear"></div>
  </div>
  
</div>

<!-- mp3 generation overlay code with tagg offline detected genres starts-->

<div class="popup" id="checksessionmessage" style="display:none; position:fixed; z-index:10;">
<!--
  <div><img src="images/pop-top.png" alt="jiwok" /></div>
-->
  <div class="inner">
  	<a id="fancybox-close" onclick="hideUnsubscribe('checksessionmessage');" title="close" style="display: inline;"></a>
    <div class="paymentTopDiv"><?=$parObj->_getLabenames($arrayData,'sessioncheckmessage','name');?></div>
		<table width="100%" border="0" cellspacing="2" cellpadding="0" class="content" id="inner_checksessionmessage" 

style="display:block;">
		  <tr style="display:none;">
			<td align="center"><?=$parObj->_getLabenames($arrayData,'bullet1','name');?></td>
		  </tr >
		   <tr style="display:none;">
			<td align="center"><?=$parObj->_getLabenames($arrayData,'bullet2','name');?></td>
		  </tr>
		  <tr style="display:none;">
			<td align="center"><?=$parObj->_getLabenames($arrayData,'bullet3','name');?></td>
		  </tr>
		  <tr style="display:none;">
			<td align="center"><?=$parObj->_getLabenames($arrayData,'bullet4','name');?></td>
		  </tr>
		  <tr style="display:none;">
			<td align="center"><?=$parObj->_getLabenames($arrayData,'bullet5','name');?></td>
		  </tr>
		  <tr>
			<td align="center" class="paymentBtn" width="360px"><input class="bu_03_new" type="button" 

value="<?=$parObj->_getLabenames($arrayDataProfile,'renew','name');?>" 

onclick="showSessionMessage1('checksessionmessage');"/>	</td>
		  </tr>
	   </table>
	<div class="clear"></div>
	</div>

</div>
		  
<!--Pop up for :our month's subscription enables you to generate the meetings of the month in progress-->

<div class="popup" id="activatepopupmessage" style="display: none; position:fixed;z-index:10;">
<!--
  <div><img src="images/pop-top.png" alt="jiwok" /></div>
-->
  <div class="inner"id="inner_activatepopupmessage" style="display:block;">
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content" >
      <tr>
        <td align="center"><?=$parObj->_getLabenames($arrayData,'generatepgmexperror','name');?></td>
      </tr>
      <tr>
        <td align="center"> <input class="blu-botton" type="button" 

value="<?=$parObj->_getLabenames($arrayData,'submit','name');?>" onclick="showGenerateType2('activatepopupmessage')"/></td>
      </tr>
    </table>
    <div class="clear"></div>
  </div>
  
</div>
 

<!-- div to Renew Subscription START  -->

<div class="popup" id="renewSubscriptionId" style="display: none; position:fixed; z-index:10;">
<!--
  <div><img src="images/pop-top.png" alt="jiwok" /></div>
-->
  <div class="inner"id="inner_activatepopupmessage" style="display:block;">
  	<a id="fancybox-close" onclick="renewSubscriptionDisplay('none');" title="close" style="display:inline;"></a>
    <h2><?=$parObj->_getLabenames($arrayDataProfile,'popuppayment','name');?></h2>
  <form name="renewSubscriptionFrm" action="payment_renewal.php" method="post" onSubmit="renewSubscriptionDisplay('none');" >
    <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content" >
		  <tr>
			<td align="center" colspan="2"><?php if($errorMsg == 1){echo $discountMsg;}?></td>
		  </tr>
		   <tr>
			<td align="left" width="60%"><?=$parObj->_getLabenames($arrayDataProfile,'renewDisc','name');?></td>
			<td align="left" width="40%"><input type="text" name="user_discount" value="" ></td>
		  </tr>
		  <tr>
			<td align="left"><?=$parObj->_getLabenames($arrayDataProfile,'gotonext','name');?></td>
			<td align="center"><input class="blu-botton" name="renewSubscriptionIdBtn" type="submit" value="<?=$parObj->_getLabenames($arrayDataProfile,'renew','name');?>" /></td>
		  </tr>
     </table>
     </form>
    <div class="clear"></div>
  </div>
</div>
<!--Program comments posting popup box-->
 <section class="pop_produiOverlayBox1" id="produiOverlayBox1" style="display: none; position:fixed; z-index:10;">
 <div class="popbox_produiOverlayBox1" id="inner_activatepopupmessage" style="display:block;">
  	<a id="fancybox-close1" onclick="hideUnsubscribe('produiOverlayBox1');" title="close" style="display:inline;"></a>
    <h3><?=$parObj->_getLabenames($arrayData,'entercomment','name');?></h3>
	  <form action="program_generate2.php#produiRight2" id="addcomment" method="post" name="addcomment">
		     <p><textarea class="HistoryCommetTextbx" name="commentText" id="commentText" style="width:100%; height:85%;"></textarea>
			  </p>
			  <div align="center">
			  <input class="btn_pop ease" name="Comment" type="submit" value="<?=$parObj->_getLabenames($arrayData,'addcommt','name');?>" onclick="return addWorkoutComment();"/>
              </div>
			  <input type="hidden" name="workoutFlex" id="workoutFlex" />
			  <input type="hidden" name="program_id" id="program_id" value="<?=trim($_REQUEST['program_id'])?>" />
			  <input type="hidden" name="p" id="p" value="<?=trim($_REQUEST['p'])?>" /></td>
		      <div id="chkContainer"></div>
       </form>
    <div class="clear"></div>&nbsp;
  </div>
</section>
<!-- More info help content for Python -->
	<div style="display: none;">
		<div class="pop_popupstyle">
		<div id="" class="popbox_popupstyle" >
			<?=$parObj->_getLabenames($arrayData,'moreInfoTextPython1','name');?><br/><br/>
            <a href="<?=ROOT_JWPATH?>ticket.php" ><?=$parObj->_getLabenames($arrayData,'moreInfoTextPython2','name');?></a>
		</div>
		</div>
		<div class="pop_popupstyle1">
        <div id="" class="popbox_popupstyle1" >
             <?php $swLink	="<a href='".ROOT_JWPATH."software'>".$parObj->_getLabenames($arrayData,'moreInfoTextPc2','name')."</a>" ?>
	         <?=str_replace('XXXXX',$swLink,$parObj->_getLabenames($arrayData,'moreInfoTextPc1','name'));?><br/><br/>
             <a href="<?=ROOT_JWPATH?>ticket.php" ><?=$parObj->_getLabenames($arrayData,'moreInfoTextPc3','name');?></a>
		</div>
		</div>
	</div> 
<!-- More info help content ends -->
<!-- popup to ask whether user want to use music proposed by jiwok  or  his own music(by tag offline) starts-->

 <section class="pop pop_produiOverlayBoxType" id="produiOverlayBoxType" style="display: none; position:fixed;z-index:10;">
 
  <div class="popbox popbox_produiOverlayBoxType"id="inner_activatepopupmessage" style="display:block;">
  	<h3><?=$parObj->_getLabenames($arrayData,'makechoice','name');?></h3>
  	<a id="fancybox-close1"  onclick="hideUnsubscribe1('produiOverlayBoxType');" title="close" style="display:inline;"></a>
	 <form id="generatetype" name="generatetype" method="post">
		   
			      <label class="label_radio" for="genreType"><input name="genreType" id="genreType" type="radio" value="1" />
			    <?=$parObj->_getLabenames($arrayData,'sergen','name');?>
                <a id="various1" onclick="javascript:view_second();" href="#"><span  class="qstn"><img src="images/help.png" alt=""></span></a></label>
           <br/>
              <label class="label_radio" for="genreType1"><input name="genreType" id="genreType1" type="radio" value="2"/><?=$parObj->_getLabenames($arrayData,'lessfast','name');?>
           <a id="various2" href="#" onclick="javascript:view_second1();" class="" ><span class="qstn"><img src="images/help.png" alt=""></span></a></label>
           
           <p align="center" colspan="2" id="generateTypeError" style="display:none;color:red" >
	     	<?=$parObj->_getLabenames($arrayData,'makechoice','name');?>!
	       </p>	
            
           <div align="center">  <input class="btn_pop ease" name="GenerateMP3" type="button" 

value="<?=$parObj->_getLabenames($arrayData,'submit','name');?>" onclick="<?php if($objPgm->_getUserTagOfflineLogin($userid)){?> showGenerateOverlayTag2();<?php }else{?>confirmGenreType('<?=$flexid?>'); 

<?php }?>"/>
           <input type="hidden" name="workout_Id_Flex" id="workout_Id_Flex"/>
</div>
	   </form>
    </div>
  
</section>
<!-- popup to ask whether user want to use music proposed by jiwok  or  his own music(by tag offline) ends-->
<!-- mp3 download link code with user genres starts-->
<div class="pop_produiOverlayBox3" id="produiOverlayBox3" style="display:block;position:fixed; z-index:10;">
<!--<div><img src="images/pop-top.png" alt="jiwok" /></div>-->
<div class="popbox_produiOverlayBox3">
<a id="fancybox-close1"  onclick="callFbWindow();" title="<?=$parObj->_getLabenames($arrayData,'close','name');?>" style="display:inline;"></a>
	  <input type="hidden" name="workout_Fid" id="workout_Fid"/>
	  <input type="hidden" name="jiwokGenList" id="jiwokGenList"/>
	  <input type="hidden" name="pageNum" id="pageNum"/>
	  <input type="hidden" name="remember1" id="remember1"/>
	  <input type="hidden" name="vocal_type_h1" id="vocal_type_h1"/>
	   <span id="jiwokGenreSelect" style="display:block;">
	  <?php 	
	  if(count($genreMusicArray)>0) 
	  	{ 
	  ?>
	 <table width="100%" border="0" cellspacing="0" cellpadding="0" class="content">
      <tr>
        <td align="center" id="jiwokGenreError" class="red-1" style="display:none"><?=$parObj->_getLabenames($arrayData,'choosegenreError','name');?></td>
      </tr>
	 </table>
	  <form id="downloadmp3" name="downloadmp3" method="post">
		<ul id="showGenres" style="display:block;">
		<h2><?= str_replace('XXBR','<br/>',$parObj->_getLabenames($arrayData,'jiwokgenres','name'));?></h2>
		 <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
		  <tr>
		  <?php 
				//****************************Memory code starts *************************	
				$random_genre_status 		= 0;		
				$vocal_type_memory_status 	= 2;	
				$userMemoryStatus			= 0;
				$genreMemoryArr				= array();	
				//if($userid==7475){//For implementing additional options without disturbing other users
					$genreMemory	 		= "Not selected";
					if(count($memorizedGenres)>0){
						if($memorizedGenres[0]['f_remember_status']==1){
							$userMemoryStatus		= 

$memorizedGenres[0]['f_remember_status'];
							$genreMemory		= $memorizedGenres[0]['f_genre_name_memory'];
							$genreMemoryArr		= explode(',',$genreMemory);
							/*if(count($genreMemoryTmpArr1)>0){
								for($i=0;$i<count($genreMemoryTmpArr1);$i++){
									$genreMemoryTmpArr2	= 

explode(',',$genreMemoryTmpArr1[$i]);
									

$genreMemoryArr[$genreMemoryTmpArr2[0]]=$genreMemoryTmpArr2[1];
								}
							}//if(count($genreMemoryTmpArr1)>0)	*/
							$vocal_type_memory_status	= 

$memorizedGenres[0]['f_vocal_coach_status'];
							$f_random_genre_status		= 

$memorizedGenres[0]['f_random_genre_status'];
						}//if($memorizedGenres[0]['remember_status']==1)			
					}//if(count($memorizedGenres)>0)
				//}//if($userid==7475)
	//****************************Memory code ends *************************
	
			foreach($genreMusicArray as $key=>$value)	
				{					
			?>
					<td align="left" width="50%" valign="middle">

						

							<input name="jiwokGenre"  id="jiwokGenre" type="checkbox" onclick="uncheckRandomFirst(this);"  <?php if(in_array($value,$genreMemoryArr)){?> 

checked="checked" <? }?> value="<?=$value;?>"/>&nbsp;<?=htmlentities(nl2br($value));?>
<!--<label for="jiwokGenre"><span></span></label>-->
<!--<label class="label_check" for="" style="padding-bottom:15px;"></label>-->

</td>

		  <?php 
		  			echo (($key+1)%2 == 0) ? '</tr><tr>' : '';
		  		}			
			?>
            <!--if($_SESSION['user']['userId']	!=	'82501'	&&	$_SESSION['user']['userId']	!=	'60378') echo "style='display:none;'";-->
           <td align="left" width="50%" valign="middle"> <!--<label class="label_check" for="bgmusic" style="padding-bottom:15px;">--><input name="bgmusic"  id="bgmusic" type="checkbox" value="1" onclick="uncheckAllGenreFirstBgmusic(this.value);"/>&nbsp;<?=$parObj->_getLabenames($arrayData,'bgmusic','name')?><!--</label> --></td> 
            
		  </tr>          
		 </table>
		 <?php
		 /*$flag=0; 
		 if($langVocal[0]['workout_lang_selected']) 
				{if($langVocal[0]['workout_lang_selected'])==5
				 {
				 	$flag=1;
				 }
				}
			else
			{
				if($userRegLan['user_language'] == 5) {
				$flag=1;
				}
			} */?>
		 <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content" id="vocal" <?php   


	if($langVocal[0]['workout_lang_selected']){if($langVocal[0]['workout_lang_selected'] == 5)	{?> style="display:none;" <?php }}else{ if($userRegLan['user_language'] == 5) {?> style="display:none;" <?php }}?>
>
		 		 <tr>
					<td align="left" colspan="2" 

class="white"><?=$parObj->_getLabenames($arrayData,'sel_voc','name')?></td>
				  </tr>
		 		 <tr>
					<td align="left"><input name="vocal_v"  id="vocal_v1" type="radio" value="sv" <?php 

if($langVocal[0]['voicegrade'] == 'sv') {  echo 'checked="checked"';  } if(!$langVocal[0]['voicegrade']) { echo ' 

checked="checked"' ; }if($langVocal[0]['workout_lang_selected']){if($langVocal[0]['workout_lang_selected'] == 5)	{echo ' 

checked="checked"' ; }}else{ if($userRegLan['user_language'] == 5) { echo ' 

checked="checked"' ; }} ?> />&nbsp;<?=$parObj->_getLabenames($arrayData,'sel_sv','name')?></td>
					<td align="left"><input name="vocal_v"  id="vocal_v2" type="radio" value="hv" <?php 

if($langVocal[0]['voicegrade'] == 'hv'	&&	$langVocal[0]['workout_lang_selected'] != 5) { ?> checked="checked" <?php }   

?>/>&nbsp;<?=$parObj->_getLabenames($arrayData,'sel_hv','name')?></td>
				 </tr>                
                  <?php
				 //For back ground music on or off				
				 ?>
             <!--<tr ?php if($_SESSION['user']['userId']	!=	'82501'	&&	$_SESSION['user']['userId']	!=	'60378') echo "style='display:none;'";?>>
			<td align="left"><input name="bgmusic"  id="bgmusic" type="checkbox"/>&nbsp;?=$parObj->_getLabenames($arrayData,'bgmusic','name')?>    
                 </td>
			<td></td>
		</tr>-->
                
                 
		 </table>
		<table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
			 <tr>
			 	<td align="left" width="5%">
				<?php
				//For implementing additional options without disturbing other users
				if($userid!=7475)
					{
				?>		  
				<!--<label class="label_check" for="" style="padding-bottom:31px;">--><input type="checkbox" name="f_random" value="1"  onclick="uncheckAllGenreFirst(this.value);" 

id="f_random" <?php if($f_random_genre_status==1){?> checked="checked"<?php }?>/>
				 <?php
					}
				else
					{						
				?>	<!--</label>-->
				<input type="checkbox" name="f_random" value="1"  onclick="uncheckAllGenreFirst(this);" 

id="f_random" <?php if($f_random_genre_status==1){?> checked="checked"<?php }?>/>
				<?php										
					}				
				?></td>
			<td width="95%"><?=$parObj->_getLabenames($arrayData,'randomgenre','name')?></td>
		</tr>
		<tr>
			<td align="left" width="5%">
			<?php
			//For implementing additional options without disturbing other users
			if($userid!=7475)
				{
			?>		  
			<!--<label class="label_check" for="" style="padding-bottom:31px;">--><input name="remchoice"  id="f_remchoice" type="checkbox" <?php if($userMemoryStatus==1){?> 

checked="checked"<?php }?>/>
			 <?php
				}
			else
				{						
			?>	<!--</label>-->
			<input name="remchoice"  id="f_remchoice" type="checkbox" <?php if($userMemoryStatus==1){?> 

checked="checked"<?php }?>/>
			<?php										
				}				
			?></td>
			<td width="95%"><?=$parObj->_getLabenames($arrayData,'remchoice','name')?></td>
		</tr>
	  </table>
	</ul>
	 <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr id="disp" style="display:block">
        <td colspan="2" align="left"><a href="javascript:;" onclick="fundata();" alt="more" title="more" 

class="white"><?=$parObj->_getLabenames($arrayData,'more','name')?></a></td>
      </tr>
      <tr id="hidedisp" style="display:none">
        <td colspan="2" align="left"><a href="javascript:;" onclick="fundata1();" alt="hide" title="hide" 

class="white"><?=$parObj->_getLabenames($arrayData,'hide','name')?></a></td>
      </tr>
  <tr id="more" style="display:none">
   <td align="left" colspan="2">
	 <table width="100%" border="0" cellspacing="0" cellpadding="0">
	 <tr>
        <td align="left" colspan="2"><?=$parObj->_getLabenames($arrayData,'sel_lan','name')?></td>
	 </tr>
	<tr>
		<?php 
			if($langVocal[0]['workout_lang_selected']) 
				{ 
				
			?>
			  <td> <!--<label class="label_radio" for="language1" style="padding-bottom:17px;">--><input name="language"  id="language1" type="radio" value="1" onclick="showTable(this.value);" <?php 

if($langVocal[0]['workout_lang_selected'] == 1) { ?> checked="checked" <?php } 

?>/>&nbsp;<?=$parObj->_getLabenames($arrayData,'sel_lang_en','name')?><!--</label>--></td>
			  <td> <!--<label class="label_radio" for="language2" style="padding-bottom:17px;">--><input name="language"  id="language2" type="radio" value="2" onclick="showTable(this.value);" <?php 

if($langVocal[0]['workout_lang_selected'] == 2) { ?> checked="checked" <?php }  

?>/>&nbsp;<?=$parObj->_getLabenames($arrayData,'sel_lang_fr','name')?><!--</label>--></td>
			<td> <!--<label class="label_radio" for="language5" style="padding-bottom:17px;">--><input name="language"  id="language5" type="radio" value="5" onclick="showTable(this.value);" <?php 

if($langVocal[0]['workout_lang_selected'] == 5) { ?> checked="checked" <?php }  ?> 

/>&nbsp;<?=$parObj->_getLabenames($arrayData,'sel_lang_pl','name')?><!--</label>--></td>	
              <td><input name="language"  id="language3" type="radio" value="3" onclick="showTable(this.value);" <?php 

if($langVocal[0]['workout_lang_selected'] == 3) { ?> checked="checked" <?php }  ?> 

style="display:none;"/>&nbsp;<!--<?=$parObj->_getLabenames($arrayData,'sel_lang_fr','name')?>--></td>
              <td><input name="language"  id="language4" type="radio" value="4" onclick="showTable(this.value);" <?php 

if($langVocal[0]['workout_lang_selected'] == 4) { ?> checked="checked" <?php }  ?> 

style="display:none;"/>&nbsp;<!--<?=$parObj->_getLabenames($arrayData,'sel_lang_fr','name')?>--></td>
			<?php 
				} 
			else 
				{
			?>
			  <td><input name="language"  id="language1" type="radio" value="1" onclick="showTable(this.value);"  <?php 

if($userRegLan['user_language'] == 1) { ?> checked="checked" <?php } 

?>/>&nbsp;<?=$parObj->_getLabenames($arrayData,'sel_lang_en','name')?></td>
			  <td><input name="language"  id="language2" type="radio" value="2" onclick="showTable(this.value);"  <?php 

if($userRegLan['user_language'] == 2) { ?> checked="checked" <?php }  

?>/>&nbsp;<?=$parObj->_getLabenames($arrayData,'sel_lang_fr','name')?></td>
			<td><input name="language"  id="language5" type="radio" value="5" onclick="showTable(this.value);"  <?php 

if($userRegLan['user_language'] == 5) { ?> checked="checked" <?php }  

?> />&nbsp;<?=$parObj->_getLabenames($arrayData,'sel_lang_pl','name')?></td>
              <td><input name="language"  id="language3" type="radio" value="3" onclick="showTable(this.value);"  <?php if($userRegLan['user_language'] == 3) { 

?> checked="checked" <?php }  ?> 

style="display:none;"/>&nbsp;<!--<?=$parObj->_getLabenames($arrayData,'sel_lang_fr','name')?>--></td>
              <td><input name="language"  id="language4" type="radio" value="4" onclick="showTable(this.value);" <?php if($userRegLan['user_language'] == 4) { 

?> checked="checked" <?php }  ?> 

style="display:none;"/>&nbsp;<!--<?=$parObj->_getLabenames($arrayData,'sel_lang_fr','name')?>--></td>
		   <?php 
				} 

			?>
		
	 </tr>	 
	<tr><td height="8px" colspan="5"></td></tr>
	<tr>
		<td align="left" colspan="5"><?php
		//For implementing additional options without disturbing other users
		if($userid!=7475)
			{	
		?>
			  <!--<label class="label_check" for="f_vocal_type" style="padding-bottom:15px;">--><input type="checkbox" name="f_vocal_type" value="1" id="f_vocal_type" <?php 

if($vocal_type_memory_status==1){?> checked="checked" <?php }?>/><!--</label>-->
		<?php
			}
		else
			{						
		?>
			  <input type="checkbox" name="f_vocal_type" value="1" id="f_vocal_type" <?php 

if($vocal_type_memory_status==1){?> checked="checked" <?php }?>/>
		<?php							
			}
		?>&nbsp;<?=$parObj->_getLabenames($arrayData,'vocalType','name')?></td>
	  </tr>
	 </table>
   </td>
  </tr>
	  <tr>
		<td align="center" colspan="2"><input class="btn_pop ease" name="GenerateMP3" type="button" 

value="<?=$parObj->_getLabenames($arrayData,'generateMP3','name')?>" onclick="downloadFile('<?=$flexid?>');"/></td>
	  </tr>
    </table>
   </form>
  <?php 
  		} 
	else 
		{
	?>    
	<table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
      <tr>
        <td align="center"><?=$parObj->_getLabenames($arrayData,'genredetectmsg','name');?></td>
      </tr>
	  <tr>
        <td align="center" id="downloadSuccess" style="display: none; "></td>
      </tr>
    </table>
	<?php 
	  	} 
	?>
	  </span>  
	  <span id="downloadSuccess" style="display:none;"></span>
      <div class="clear"></div>
  </div>
  
</div>

<!-- mp3 download link code with user genres ends -->
<style>
.popup #innerId {
    height: 107px !important;
}
.ProduiBtn4{
	background: url("../images/orange_bg.jpg") repeat-x scroll 0 0 #2495BF;
    border: 0 none;
    border-radius: 0 8px 0 8px;
    color: #FFFFFF;
    cursor: pointer;
    font-size: 11px;
    font-weight: bold;
    padding: 5px;
    position: relative;
    text-align: center;
}
</style>

<!-- mp3 direct origin force download link code without user genres starts -->
<div class="popup" id="produiOverlayBox4" style="display:none;position:fixed; z-index:10;">
<!--
  <div><img src="images/pop-top.png" alt="jiwok" /></div>
-->
  <div class="inner" id="innerId"> 
	<a id="fancybox-close1" onclick="hideUnsubscribet('produiOverlayBox4');" title="close" style="display:inline;"></a> 
	<span id="downloadSuccessOrigin" style="display:none;"></span>
    <div class="clear"></div>
  </div>
</div>
<!-- mp3 direct origin force download link code without user genres ends  -->

<script type="text/javascript">
function showGenerateOverlayTag2()
	{ 
		var valueGenreTmp = '';
		for(var i=0; i < document.generatetype.genreType.length; i++)
			{  
				if(document.generatetype.genreType[i].checked)
				{
	 				valueGenreTmp = valueGenreTmp + document.generatetype.genreType[i].value;
     			}
			}
		if(valueGenreTmp == 1)
		{	
			confirmGenreType('<?=$flexid?>');		
		}		
		else if(valueGenreTmp == 2)
		{	
			
			showGenerateOverlayTag();
			if((parseInt(isFb)==1)||(parseInt(isTwt)==1)){
				shareWoData("0");
			}	
		}
		else
		{
			confirmGenreType('<?=$flexid?>');		
		}
	}
function confirmGenreType(pgm_flexid)
 { 
    var workout_Flex = document.getElementById('workout_Id_Flex').value;
	var j=0;
	var valueGenre = '';
	for(var i=0; i < document.generatetype.genreType.length; i++)
		{
			if(document.generatetype.genreType[i].checked)
			{
				valueGenre = valueGenre + document.generatetype.genreType[i].value;
				j++;
			}
		}		
	if(j>0)
	{
		if(valueGenre==1)
			{ 
				xmlHttp31	=	createAjaxFn();	
				url 		= 
"checkfile.php?pgmFlex="+pgm_flexid+"&workFlex="+encodeURIComponent(String(workout_Flex))+"&user="+<?=$userid;?>+"&orgin=web";	
				xmlHttp31.onreadystatechange	=	function()
					{	
						if(xmlHttp31.readyState==4)				
						{				
							if(xmlHttp31.responseText == 'success')
								{
document.getElementById('produiOverlayBoxType').style.display='none';
										showGenerateOverlay2();
								}
							else{
document.getElementById('produiOverlayBoxType').style.display='none';
										showGenerateOverlay3(workout_Flex); 		
								}					
							//downloadOriginForceFile2();						
						}					
					}
				//	document.getElementById('produiOverlayBoxType').style.display='none';
				//showGenerateOverlay3(workout_Flex); 
				xmlHttp31.open("GET",url,true);
				xmlHttp31.send(null);
			}
			else 
			{ 
					
				xmlHttp31	=	createAjaxFn();		
				url			=	

"checkfile_tag.php?pgmFlex="+pgm_flexid+"&workFlex="+encodeURIComponent(String(workout_Flex))+"&user="+<?=$userid; ?>;	
				xmlHttp31.onreadystatechange=function()
					{			
						if (xmlHttp31.readyState==4)					
						{					
							if(xmlHttp31.responseText == 'success')
								{								

			
									

document.getElementById('produiOverlayBoxType').style.display='none';
									showGenerateOverlay2();
								}
							else
								{
									

document.getElementById('produiOverlayBoxType').style.display='none';
									showGenerateOverlay(workout_Flex);		
								}					
							//downloadOriginForceFile2();						
						}				
					}
				//	document.getElementById('produiOverlayBoxType').style.display='none';
				//showGenerateOverlay3(workout_Flex); 
				xmlHttp31.open("GET",url,true);
				xmlHttp31.send(null);
	 		}		
	}
	else
	{
		document.getElementById('generateTypeError').style.display='block';
	}
 }
 // ------Align all div to center of the screen-----
	function centerPopups(div) 
	{
		var winH = $(window).height(); 
		var winW = $(window).width(); 
		if(div=="produiOverlayBox3"){
			//winH	=	winH-80;
		}
		var centerDiv = $('#' + div); 
		centerDiv.css('top', winH/2-centerDiv.height()/2); 
		centerDiv.css('left', winW/2-centerDiv.width()/2); 
	} 
	
	centerPopups('produiOverlayBoxType'); 
	centerPopups('produiOverlayBox1'); 
	centerPopups('produiOverlayBox2'); 
	centerPopups('produiOverlayBox3'); 
	centerPopups('produiOverlayBox4'); 
	centerPopups('unsubscribePgm'); 
	centerPopups('otherSearch'); 
	centerPopups('checksessionmessage'); 
	centerPopups('activatepopupmessage'); 
	centerPopups('renewSubscriptionId'); 
	
 // ------Div alignment ends-----
 function showTable(val){ 
	if(val==5){
		document.getElementById('vocal').style.display='none';
		document.getElementById('vocal_v1').checked='true';
		//document.getElementById('vocal_v2').checked='false';
	}else{
		document.getElementById('vocal').style.display='block';
	}
 }
//for displaying popup code starts-------------------------  
function view_second(){
	jpopup = $('.pop_popupstyle').bPopup({	     
              speed: 200,
              positionStyle: 'fixed',
          });
}
function view_second1(){
	jpopup = $('.pop_popupstyle1').bPopup({	     
              speed: 200,
              positionStyle: 'fixed',
          });
}
//for displaying popup code ends-------------------------
</script>
<script>
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
            if(l.className.indexOf('label_') == -1) continue;
            var inp = gebtn(l,'input')[0];
            if(l.className == 'label_check'){
				l.className = (safari && inp.checked == true || inp.checked) ? 'label_check c_on' : 'label_check c_off';
                l.onclick   = check_it;
            };
            if (l.className == 'label_radio') {
                l.className = (safari && inp.checked == true || inp.checked) ? 'label_radio r_on' : 'label_radio r_off';
                l.onclick = turn_radio;
            };
        };
    };
    var check_it = function(){
        var inp  = gebtn(this,'input')[0];
        if(this.className == 'label_check c_off' || (!safari && inp.checked)) {
           this.className =  'label_check c_on';
            if (safari) inp.click();
        }else{
            this.className = 'label_check c_off';
            if (safari) inp.click();
        };
    };
    var turn_radio = function() {
		
        var inp = gebtn(this,'input')[0];
       
        if (this.className == 'label_radio r_off' || inp.checked) {
            var ls = gebtn(this.parentNode,'label');
            for (var i = 0; i < ls.length; i++) {
                var l = ls[i];
                if (l.className.indexOf('label_radio') == -1)  continue;
                l.className = 'label_radio r_off';
            };
            this.className = 'label_radio r_on';
            if (safari) inp.click();
        } else {
            this.className = 'label_radio r_off';
            if (safari) inp.click();
        };
    };
</script>
