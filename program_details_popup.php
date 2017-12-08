<!--Calendar popup if no program subscribed-->
<script>
function lastdate(cuntdate,wrkend)
{	var selctddate	=	cuntdate;
	var arr 		= 	selctddate.split("-");
	var dt			=	arr[0];
	var dm			=	arr[1];
	dm				=	dm-1;
	var dy			=	arr[2];
	var expdate 	= 	new Date(dy,dm,dt);
	expdate.setDate(expdate.getDate() + wrkend);
	var m			=	expdate.getMonth();
	var corrctdmonth=	m+1;
	var subsenddate = expdate.getDate() + '-' + corrctdmonth + '-' + expdate.getFullYear();
	$('#enddate').attr('value',subsenddate);
}
</script>
<?php
$query	    =	"SELECT program_workout.workout_date FROM program_workout LEFT JOIN program_master ON program_master.flex_id = program_workout.training_flex_id WHERE program_master.program_id='".$program_id."' AND program_workout.lang_id='".$lanId."' ORDER BY program_workout.workout_order DESC LIMIT 0,1";
$res		=	mysql_query($query);
$row		=	mysql_fetch_assoc($res);
$wrkoutend	=	$row['workout_date'];
$date 		= 	date('Y-m-d', strtotime($startdate));
$qry 		= 	"SELECT ADDDATE('".$date."', INTERVAL ".$row['workout_date']." DAY) as expiry";  
$result		=	mysql_query($qry);
$rows		=	mysql_fetch_assoc($result);
$expdt		=	$rows['expiry'];
$expdt		=	date('d-m-Y',strtotime($expdt));
?>
<div class="pop_produiOverlayBox" id="produiOverlayBox" style="display:none; position:absolute !important; z-index:10;">
 <div class="inner popbox_produiOverlayBox">
	<a  onclick="hideUnsubscribe();" title="close" style="display:inline;"></a>
    <?php 
    //session check starts
	if(isset($_SESSION['user']['userId'])) 
	{	$freedays 	 = $objPgm->_getFreeDays($userid);
		$paymentTemp = $objPgm->_getUserPaymentTemp($userid);
	?>
	<form name="dateform" method="post" id="dateform" >
	<?php if((($objPgm->_checkUserFreePeriod($userid,$freedays)) || ($objPgm->_checkUserPaymentPeriod($userid)))) 
				{ 
					echo '<h2>',$parObj->_getLabenames($arrayData,'validstart','name'),'</h2>';
        		} 
     ?>
		<input type="hidden" value="<?=base64_encode($program_id)?>" name="program_id" id="program_id"/>
        <input type="hidden" value="<?= $wrkoutend?>" name="wrkoutend" id="wrkoutend" />
		<div align="center" id="test" style="display:none; font-weight:bold;" class="red-1"></div>
	  <?php 
		if(($objPgm->_checkUserFreePeriod($userid,$freedays))) 
			{
				
	  ?>
	  <table width="100%" border="0" cellspacing="2" cellpadding="4" class="content">
		  <tr>
			<td align="center"><?=$parObj->_getLabenames($arrayData,'startdate','name');?><input type="text" class="w8em format-d-m-y divider-dash highlight-days-12 no-fade " id="dp-normal-1" name="dp-normal-1" value="<?=$startdate?>" maxlength="10" onchange="lastdate(this.value,<?=$wrkoutend?>);" readonly /></td>
		  </tr>
           <tr>
			<td class="fin" align="center"><?=$parObj->_getLabenames($arrayData,'expiredate','name');?>&nbsp;<input type="text" id="enddate" name="enddate" value="<?=$expdt?>" maxlength="10" readonly /></td>
		  </tr>
		  <tr>
			<td align="center" ><input class="btn_pop ease" name="validate" id="validate" type="button" value="<?=$parObj->_getLabenames($arrayData,'validdate','name');?>" onclick="checkDate('<?=$userid?>','<?=$freedays?>','<?=base64_encode($program_id)?>','<?=$programType?>');"/></td>
		  </tr>
	  </table>
	  <?php }
		elseif(($objPgm->_checkUserPaymentPeriod($userid))) 
			{	
	  ?>
 	  <table width="100%" border="0" cellspacing="2" cellpadding="4" class="content">
		  <tr>
			<td align="center" valign="middle"><?=$parObj->_getLabenames($arrayData,'startdate','name');?><input type="text" class="w8em format-d-m-y divider-dash highlight-days-12 no-fade" id="dp-normal-1" name="dp-normal-1" value="<?=$startdate?>" maxlength="10" onchange="lastdate(this.value,<?=$wrkoutend?>);" readonly/></td>
		  </tr>
          <tr>
			<td class="fin" align="center"><?=$parObj->_getLabenames($arrayData,'expiredate','name');?>&nbsp;<input type="text" id="enddate" name="enddate" value="<?=$expdt?>" maxlength="10" readonly /></td>
		  </tr>
		  <tr >
			<td align="center" > <input class="btn_pop ease" name="validate" id="validate" type="button" value="<?=$parObj->_getLabenames($arrayData,'validdate','name');?>" onclick="checkDate('<?=$userid?>','<?=$freedays?>','<?=base64_encode($program_id)?>','<?=$programType?>');"/></td>
		  </tr>   
	  </table>
      <?php 
	  	   }
		elseif($objPgm->checkSingleProgramSubscribed($userid))	
		    {		
		?>
		<table width="100%" border="0" cellspacing="2" cellpadding="4" class="content" id="inner_checksessionmessage">
		  <tr>
			<td align="center" class="white"><?=$parObj->_getLabenames($arrayData,'sessioncheckmessage','name');?></td>
		  </tr>  
		 <!-- <tr>
			<td align="center"><?=$parObj->_getLabenames($arrayData,'bullet1','name');?></td>
		  </tr> 
		  <tr>
			<td align="center"><?=$parObj->_getLabenames($arrayData,'bullet2','name');?></td>
		  </tr> 
		  <tr>
			<td align="center"><?=$parObj->_getLabenames($arrayData,'bullet3','name');?></td>
		  </tr> 
		  <tr>
			<td align="center"><?=$parObj->_getLabenames($arrayData,'bullet4','name');?></td>
		  </tr> 
		  <tr>
			<td align="center"><?=$parObj->_getLabenames($arrayData,'bullet5','name');?></td>
		  </tr> -->
		  <tr>
			<td align="center"><input class="btn_pop ease" value="<?=$parObj->_getLabenames($arrayDataProfile,'renew','name');?>" onclick="window.location.href='payment_renewal.php';"/></td>
		  </tr> 
	  </table>
      <?php
			}
		elseif($objPgm->checkProgramSubscribed($userid)) 
			{  ?>
		<table width="100%" border="0" cellspacing="2" cellpadding="4" class="content">
		  <tr>
			<td align="center" valign="middle"><?=$parObj->_getLabenames($arrayData,'startdate','name');?>
			<input type="text" class="w8em format-d-m-y divider-dash highlight-days-12 no-fade" id="dp-normal-1" name="dp-normal-1" value="<?=$startdate?>" onchange="lastdate(this.value,<?=$wrkoutend?>);" maxlength="10" readonly/></td>
		  </tr>
          <tr>
			<td class="fin" align="center"><?=$parObj->_getLabenames($arrayData,'expiredate','name');?>&nbsp;<input type="text" id="enddate" name="enddate" value="<?=$expdt?>" maxlength="10" readonly /></td>
		  </tr>
		  <tr>
			<td align="center"><input class="btn_pop ease" name="validate" id="validate" type="button" value="<?=$parObj->_getLabenames($arrayData,'validdate','name');?>" onclick="checkDate('<?=$userid?>','<?=$freedays?>','<?=base64_encode($program_id)?>','<?=$programType?>');"/></td>
		  </tr>
	  </table>
      <?php  }
		else{  ?>
		<table width="100%" border="0" cellspacing="2" cellpadding="4" class="content">
		  <tr>
			<td colspan="2" align="center"><?=$parObj->_getLabenames($arrayData,'subscribepayerror','name');?></td>
		  </tr>
		  <tr>
			<td colspan="2" align="center"><a href="payment_renew.php" id="paypage" name="paypage"><input class="btn_pop ease" name="payment" id="payment" type="button" value="<?=$parObj->_getLabenames($arrayData,'paynow','name');?>" onclick="window.location.href='payment_new.php';"/></a></td>
		  </tr>
	  	</table>
      <?php } 
	  ?>
    </form>
    <?php 
	     }
	?><div class="clear"></div>&nbsp;
  </div>
 </div>
<!-- payment overlay box for sigle training programs starts   -->
<!--
Message : Your subscription has expired. Please make your payment in order to continue enjoying the services of Jiwok.
-->
<div class="pop_produiOverlayBoxPayment" id="produiOverlayBoxPayment" style="display:none; position:absolute; z-index:10;">
  
  <div class="inner popbox_produiOverlayBoxPayment">
    <a id="fancybox-close" onclick="hideUnsubscribe();" title="close" style="display: inline;"></a>
	<table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
		<?php
		$paymentTemp = $objPgm->_getUserPaymentTemp($userid);
		if(count($paymentTemp)>0) 
			{ 
		?>
		  <tr>
			<td align="center"><?=$parObj->_getLabenames($arrayData,'subscribepayerror','name');?></td>
		  </tr>
		  <tr>
			<td align="center"><input class="btn_pop ease" name="payment" id="payment" type="button" value="<?=$parObj->_getLabenames($arrayData,'paynow','name');?>" onclick="window.location.href='payment1_reg.php';"/></td>
		  </tr>
		<?php 
			}
		else 
			{
		?>
		  <tr>
			<td align="center"><?=$parObj->_getLabenames($arrayData,'subscribepayerror','name');?></td>
		  </tr>
		  <tr>
			<td align="center"><input class="btn_pop ease" name="payment" id="payment" type="button" value="<?=$parObj->_getLabenames($arrayData,'paynow','name');?>" onclick="window.location.href='payment_renew.php';"/></td>
		  </tr>
		<?php 
			} 
		?>
	</table>
    <div class="clear"></div>
  </div>
  
</div>
<!-- payment overlay box for sigle training programs ends   -->


<!-- user training program subscribe confirmation overlay box starts   -->
	<!--
	Message : Have you completed your training before: ?
	-->
<?php
 if(isset($_SESSION['user']['userId'])) 
	 { 
 ?> 
   <div class="pop_progm" id="produiOverlayBoxConfirm" style="display:none; position:fixed; z-index:10;">
	  <div class="inner popbox_progm">
		<a id="fancybox-close" onclick="hideUnsubscribe2();" title="close" style="display: inline;"></a><!--Close button-->
		<h2 style="font:bold 18px Arial,Helvetica"><?php echo $parObj->_getLabenames($arrayData,'confirmsub','name'),' ',$subscribed_program_title,' ?';?></h2>
		<?php
		 $programDt1 = $objPgm->_getUserTrainingProgramConfirm($userid);
		 $subscribed_program_title = trim(stripslashes($programDt1['program_title']));
		 $subscribed_program_id = trim(stripslashes($programDt1['programs_subscribed_id']));
		 ?><br/><br/>
		 <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">     
		  <tr>
			<td colspan="2" align="center">
		    <input class="btn_pop ease" name="payment" id="payment" type="button" value="<?=$parObj->_getLabenames($arrayData,'yes','name');?>" onclick="confirmUnsubscribe(<?=$subscribed_program_id?>);"/>&nbsp;
			<input class="btn_pop ease" name="payment" id="payment" type="button" value="<?=$parObj->_getLabenames($arrayData,'no','name');?>" onclick="test2();"/>
			</td>
		  </tr>
		 </table>
		<div class="clear"></div>
	   </div>
	</div>
<?php } ?>
<!--user training program subscribe confirmation overlay box ends   -->

<!--Popup box for following message starts here-->
<!--
Message : We were pleased to offer you your first session for free. Now stay with us to achieve your goal by activating your account
-->
	<div class="pop_checksessionmessage" id="checksessionmessage" style="display:none; position:fixed; z-index:100000;">
	  <div class="inner popbox_checksessionmessage">
 			<span id="inner_checksessionmessage" style="display:block;">
 				<table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">     
				  <tr>
					<td align="center"><?=$parObj->_getLabenames($arrayData,'sessioncheckmessage','name');?></td>
				  </tr>
				  <tr>
					<td align="center"><?=$parObj->_getLabenames($arrayData,'bullet1','name');?></td>
				  </tr>
				  <tr>
					<td align="center"><?=$parObj->_getLabenames($arrayData,'bullet2','name');?></td>
				  </tr>
				  <tr>
					<td align="center"><?=$parObj->_getLabenames($arrayData,'bullet3','name');?></td>
				  </tr>
				  <tr>
					<td align="center"><?=$parObj->_getLabenames($arrayData,'bullet4','name');?></td>
				  </tr>
				  <tr>
					<td align="center"><?=$parObj->_getLabenames($arrayData,'bullet5','name');?></td>
				  </tr>
				  <tr>
					<td align="center"><input class="btn_pop ease" value="<?=$parObj->_getLabenames($arrayDataProfile,'renew','name');?>" onclick="showSessionMessage1();" type="button"/></td>
				  </tr>
				</table>
      		</span> 
	   <div class="clear"></div>&nbsp;
	  </div>
	</div>
<script type="text/javascript">
 // ------Align all div to center of the screen-----
	function centerPopups(div) 
	{
		var winH = $(window).height(); 
		var winW = $(window).width();
		if(div=="produiOverlayBox"){
			winH = winH-120;
		}
		var centerDiv = $('#' + div); 
		centerDiv.css('top', winH/2-centerDiv.height()/2); 
		centerDiv.css('left', winW/2-centerDiv.width()/2); 
	} 
	centerPopups('produiOverlayBox'); 
	centerPopups('produiOverlayBoxPayment'); 
	centerPopups('produiOverlayBoxConfirm'); 
	centerPopups('checksessionmessage'); 

</script>
