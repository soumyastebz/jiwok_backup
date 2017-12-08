<?php 
session_start();
ini_set('display_errors',0);
error_reporting(E_ERROR | E_PARSE);
include_once('includeconfig.php');
include_once('./includes/classes/class.member.php');
include_once('./includes/classes/class.Languages.php');
include_once("includes/classes/class.discount.php");
include_once("includes/classes/class.programs.php");
include_once("includes/classes/class.Payment.php");
//error in db so removed  include_once('mes_historical.php');
settype( $temptime, double ); // for date add function in general class

// create an object for manage the parsed content 
$parObj 		=   new Contents('myprofile.php');
$objGen   		=	new General();
$dbObj     		=   new DbAction();	
$objMember		= 	new Member($lanId);
$objDisc		= 	new Discount($lanId);
$lanObj 		= 	new Language();	
$objPgm     	= 	new Programs($lanId);
$paymentObj		=	new Payment();

if($_SESSION['user']['userId']==''){
	header('location:login_failed.php?login=failed');
}else{
	$userId	=	$_SESSION['user']['userId'];
}
	
if($lanId=="") $lanId=1;

$userpaymentstatus = $objPgm->checkUserPaymentStaus($userId);	
//collecting data from the xml for the static contents
$returnData		= $parObj->_getTagcontents($xmlPath,'registrationUser','label');
$arrayData		= $returnData['general'];
//get reff code details
$reffCode		=	$objDisc->_getUserReffId($userId);
$signedUser     = 	$objDisc->_getDiscDetailByType($reffCode['user_reff_id'],'signed');
$newUser     	= 	$objDisc->_getDiscDetailByType($reffCode['user_reff_id'],'new');
//collecting data from the xml for the static contents
$returnDataProfile		= $parObj->_getTagcontents($xmlPath,'myprofile','label');
$arrayDataProfile		= $returnDataProfile['general'];
$returnDataPgm		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
$arrayDataPgm		= $returnDataPgm['general'];
//get language details
$LanguagesArray		   	=	$lanObj->_getLanguageArray(); 
//for fetching the country name
$countriesArray 		= $objMember->_getCountries();
//for fetching the timezone name
$timezoneArray 		= $objMember->_getTimezone();
/* Take all label name from label_manager table with menumaster_id = $genreMenuMasterId  */
$genreMenus				= $objMember->_getGenreMenus($siteMasterMenuConfig['GENRE_ID'],$lanId);
/* Take all label name from label_manager table with menumaster_id = $userOptionMenuMasterId  */
$optionMenus			= $objMember->_getOptionalMenus($siteMasterMenuConfig['USER_OPTIONAL_FIELDS'],$lanId);
/* Take voice preference from label_manager */
$voicePrefer			= $objMember->_getOptionalMenus($siteMasterMenuConfig['VOICE'],$lanId);
$weightUnits    		= $objMember->_getOptionalMenus($siteMasterMenuConfig['WEIGHT'],$lanId);
$heightUnits    		= $objMember->_getOptionalMenus($siteMasterMenuConfig['HEIGHT'],$lanId);
//display user genre
$usergenre				= $objMember->_getUserGenre($userId,$siteMasterMenuConfig['GENRE_ID']);
//getall sports from database
$sportArray				= $objMember->_getAllSports($lanId);	
//get user optional fields
$userOptional			= $objMember->_getUserOptinalFieldValues($userId,$siteMasterMenuConfig['USER_OPTIONAL_FIELDS']);
//fetch the user details
$userDetail		=	$objMember->_getAllByUserId($userId);
//For Nike Data
$resultNike		=	$objMember->_getNikeDetail($userId);
///user genre implode
$today = date("Y-m-d");
$sql_search  = "select * from payment where payment_userid='$userId' and (payment_expdate='NULL' or payment_expdate < '$today')";
$res_search  = $GLOBALS['db']->query($sql_search);
$no_of_rows=count($res_search);	
//For referral payment
$refQuery	=	"SELECT reffSts,version FROM payment WHERE payment_userid='$userId' AND payment_status='1' ORDER BY payment_expdate DESC ";
$refResult	=	$GLOBALS['db']->getRow($refQuery, DB_FETCHMODE_ASSOC);;
//for user sport practice
foreach($userOptional['id'] as $key => $data){
	$userOption[$data]		=	$userOptional['value'][$key];
}
foreach($userOption as $key => $data){
	if($key == $siteMasterMenuConfig['SPORTSCAT']){	
		$userSports	=	$data;
		break;
	}
}

$userSportsArray	=	explode(',',$userSports);
for($i=0;$i<count($userSportsArray);$i++){
	$sportName[$i]		= 	$sportArray[$userSportsArray[$i]];
}
$sportNames				=	implode(',',$sportName);
//for user genre
foreach($usergenre as $data) 
	$userGenreDetail[] 	= $genreMenus[$data];

$userMusic			= implode(', ',$userGenreDetail);
// find out user is in payment period. (code added on mar 16 2009):starts
if($objPgm->_checkUserPaymentPeriod($userId)){
  	$finalFormat	=	$objPgm->_getPaymentExpDate($userId);
	$Payed			=	1;
}else{
	$Payed			=	0;
}


// chech whether unsubscribed
$unSub					=	$objDisc->_isUnSubscribed($userId);
//$unSub = 0;


// User can unsubscribe his membership upto x days before the expiration month so find out it.
$period		  = $objMember->_getUnsubscribeMembershipPeriod(); // get the value of x date from settings table
$period		  = -($period['membership_unsubscribeperiod']); // need to minus this period with expiration date
$chkDate		  = explode('-',$finalFormat);
$temptime1		  = mktime(0,0,0,$chkDate[1],$chkDate[2],$chkDate[0]);  
$temptime1 	  = $objGen->dateAdd('d',$period,$temptime1);
$newdate1 		  = strftime('%m-%d-%Y',$temptime1);
$sel_dt1		  = explode('-',$newdate1);
$chngFormt1   	  = implode('/',$sel_dt1);
$dd1         	  = strtotime($chngFormt1);
$finalFormat1 	  = date('Y-m-d',$dd1);
$toDay			  = date('Y-m-d');
if($toDay > $finalFormat1){
	  // unsubscription is possible upto x days before the expiration month, after this date unsubscription wil be in next month.
	$finalFormat	=	$objGen->_dateTomdY($finalFormat);
	$finalFormat	=	$objGen->_addMonthToDateYmd($finalFormat,1);
}
$image_user = './uploads/users/'.$objGen->_output($userDetail['user_photo']);
$image_user = './uploads/users/'.$objGen->_output($userDetail['user_photo']);
if(!is_file($image_user)) { 
	if(file_exists('../www.beta.jiwok.com/uploads/users/'.$objGen->_output($userDetail['user_photo'])))
				{
					
				$image_user = 'http://en.jiwok.com/uploads/users/'.$objGen->_output($userDetail['user_photo']);
				}
				else{
				
				$image_user = 'images/profile-dummy.png';
				} 
	
} 
list($widtht,$heightt) = getimagesize($image_user);
if($lanId==1){
	$print_finalFormat	= date('d M, Y', strtotime($finalFormat));
}else if($lanId==2){
	$tmpDte	=	date('Y/m/d', strtotime($finalFormat));
	$tmpDteAry	=	explode("/",$tmpDte);
	$frenchMonth	=	array("01"=>'janvier',"02"=>'février',"03"=>'mars',"04"=>'avril',"05"=>'mai',"06"=>'juin',"07"=>'juillet',"08"=>'août',"09"=>'septembre',"10"=>'octobre',"11"=>'novembre',"12"=>'décembre');
	//print_r($frenchMonth);
	//echo $tmpDteAry[1];
	$print_finalFormat	=	$tmpDteAry[2]." ".$frenchMonth[$tmpDteAry[1]]." ".$tmpDteAry[0];
}



// find out user membership expiraion date. (code added on mar 16 2009):ends
$user_country	= $objMember->_getCountryName($userDetail['user_country'], strtolower($LanguagesArray[$lanId]));
$user_timezone	= $objMember->_getTimezoneName($userDetail['user_timezone'],$lanId);

$langArrayNew		= array("1"=>"English","2"=>"Français","3"=>"espagnol","4"=>"italien","5"=>"polski");
?>
<?php include("header.php"); ?>


<style>
.has-js .label_check, .has-js .label_radio {  display:inline-block;}
.popnew  { background:#408dc1;width:100%; margin:50px auto 0;display:none; max-width:738px;z-index:500; 
           color:#f4d03e;font-family: 'Montserrat(OTT)', sans-serif; border-radius:15px}
.popnew .close  { position:absolute; top:-13px; right:-13px; cursor:pointer;}
 .popnew    { width:auto; margin:50px 10px; }
 .rows{font-weight: 600};
</style>
<!---pop up-->
<script>


function unsubscribeMembership()

{

 	//alert(membershipId)
  
	document.getElementById('unsubscribePgm').innerHTML='<h2><img src=\"images/close-button.gif\" onclick=\"hideUnsubscribe(\'unsubscribePgm\');\" alt=\"close\" title=\"close\" style=\"cursor:pointer;\" border=\"0\"\/><\/h2><h1>Please Wait</h1>';

	//document.getElementById('produiOverlayBox1').style.display='none';

	//document.getElementById('produiOverlayBox2').style.display='none';

	xmlHttp3=createAjaxFn();	

	alert(JSON.stringify(xmlHttp3));

	if (xmlHttp3==null)

	{

		alert ("Browser does not support HTTP Request");

		return;

	}

	url="membership_unsubscribe.php";

	xmlHttp3.onreadystatechange=function()

	{

		if (xmlHttp3.readyState==4)

		{	

			if(xmlHttp3.responseText == 1){				

				document.getElementById('unsubscribePgm').innerHTML='<h2><img src=\"images/close-button.gif\" onclick=\"hideUnsubscribemsg(\'unsubscribePgm\');\" alt=\"close\" title=\"close\" style=\"cursor:pointer;\" border=\"0\"\/><\/h2><h1>&nbsp;</h1><p>Sans rancune :), votre abonnement se terminera le 09 Dec, 2011<\/p><p>&nbsp;<\/p><h3><input class=\"overlayBtn\" name=\"Yes\" type=\"button\" value=\"Fermer\" onclick=\"hideUnsubscribemsg(\'unsubscribePgm\')\" \/><\/h3>';

			}

		}

	}

	xmlHttp3.open("GET",url,true);

	xmlHttp3.send(null);



}









function unSubscrb(versionId,userId){ 
	document.getElementById("unscbContainer").innerHTML	=	"<table width=\"100%\"><tr><td width=\"100%\" align=\"center\">&nbsp;</td></tr><tr><td width=\"100%\" align=\"center\"><img src=\"images/loading.gif\" /></td></tr></table>";
	var ajaxUrl	=	"http://www.jiwok.com/unSubscibePayBox.php?action=unsubscribe&id="+userId;
	if(versionId==1){
		ajaxUrl	=	"http://www.jiwok.com/unSubscibePayBox.php?action=unsubscribeOld&id="+userId;
	}else if(versionId==2){
		ajaxUrl	=	"http://www.jiwok.com/unSubscibePayBox.php?action=unsubscribe&id="+userId;
	}
	unscubscribePaybox(ajaxUrl);
}




function unscubscribePaybox(url)
{
url	=	url+"&langId=<?php echo $lanId; ?>";
if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
}else{// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.onreadystatechange=function(){
  if (xmlhttp.readyState==4 && xmlhttp.status==200){
	  var resTxt	=	xmlhttp.responseText;
      document.getElementById("unscbContainer").innerHTML	=	"<h2>"+resTxt+"</h2>";
  }
}
xmlhttp.open("GET",url,true);
xmlhttp.send();
}



</script>
<script type="text/javascript">
    ;(function($) {
        $(function() {
            $('.btn_blue').bind('click', function(e) { 
                e.preventDefault();
                $('.pop').bPopup({
	    easing: 'easeOutBounce', 
            speed: 2000,
            transition: 'slideDown'
        });
            });
        });

    })(jQuery);
	
	;(function($) {
        $(function() {
            $('.view-popup1').bind('click', function(e) {
                e.preventDefault();
                $('.pop1').bPopup({
	    easing: 'easeOutBounce', 
            speed: 2000,
            transition: 'slideDown'
        });
            });
        });

    })(jQuery);
    <!--pop up-->
	</script>
<!---------------------------->


    <div class="frame_inner">
       <section class="profile">
         <div class="left">
            <ul class="bredcrumbs">
              <li style="color: #1473B4;font-weight: 700;">
          <?=mb_strtoupper($parObj->_getLabenames($arrayDataProfile,'newPgeTxt','name'),'UTF-8');?>
          :</li>
        <li><a href="<?=ROOT_JWPATH?>index.php">
          <?=mb_strtoupper($parObj->_getLabenames($arrayDataProfile,'newHmeTxt','name'),'UTF-8');?>
          </a></li>
        <li>></li>
        <li><a style="color: #E67F23;"href="<?=ROOT_JWPATH?>userArea.php">
          <?=mb_strtoupper($parObj->_getLabenames($arrayDataProfile,'newMyAccTxt','name'),'UTF-8');?>
          </a></li>
        <li>></li>
        <li><a style="color: #E67F23;"class="select">
          <?=mb_strtoupper($parObj->_getLabenames($arrayDataProfile,'newHeadTxt','name'),'UTF-8');?>
          </a></li>
              
             
            </ul>
            <figure class="profile-image"><img src="<?=$image_user?>" alt="profile image"></figure>
         </div>
       
       <div class="profile-edit profile-1">
         <h2 class="name"> <?=$objGen->_output(mb_strtoupper($userDetail['user_fname']));?> <?=$objGen->_output(mb_strtoupper($userDetail['user_lname']));?></h2>
         <h3 class="title2"><?=mb_strtoupper($parObj->_getLabenames($arrayDataPgm,'myinfo','name'),'UTF-8');?></h3>
           <div class="aligned">
        <p class="title3">
        <?php
		  $AccountExpDate 		= $objPgm->_findAccountExpireDate($userId);
		  $userNewPaymentStatus	=	$objPgm->checkUserPaymentStausCount($userId,'New');
		 // $userNewPaymentStatus = 0;
		  $newPaymentRegisterCount	=	$objPgm->newPaymentRegisterCount($userId,'');
		  $userOldPaymentStatus		=	$objPgm->checkUserPaymentStausCount($userId,'Old');
		  
		  $today				=	date('Y-m-d');
		  $qry 				=	"SELECT DATEDIFF('$AccountExpDate', '$today')";
		  $result				= 	$GLOBALS['db']->getRow($qry, DB_FETCHMODE_ARRAY);
		  $dayCount 			= 	$result[0];
		  
		  //-------------------------------------------------------------------------------------//
		  
		  if($userDetail['user_language']==5)
		  {
			  if($Payed==1)
					{?>
						<p><a href="payment_renew.php" class="title3" style="color:#E57200";> 
						<?php 
						echo $parObj->_getLabenames($arrayDataProfile,'popuppayment','name').$parObj->_getLabenames($arrayDataProfile,'linkClick','name');?>
						 </a></p><?php 
					}
		  }
		  
		  else
		  {
		  //--------------------------------------------------------------------------------------//
		  
		  
		  				  
			  //New starts
			  if($userNewPaymentStatus	>	0)
				{
					//if($dayCount	>	0	&&	$refResult[version]!='Old')
					if($dayCount	>	0	)
					{//echo "1";?>
						<a class="title3" href="payment_renew.php" style="color:#E57200";>
						<?= $parObj->_getLabenames($arrayDataProfile,'changePlan','name').$parObj->_getLabenames($arrayDataProfile,'linkClick','name');?>
						 </a><?php
					}
					else
					{//echo $AccountExpDate;?>
						<p><a href="payment_renew.php" class="title3" style="color:#E57200";> 
						<?php 
						echo $parObj->_getLabenames($arrayDataProfile,'popuppayment','name').$parObj->_getLabenames($arrayDataProfile,'linkClick','name');?>
						 </a></p><?php
									
					}
				}
				else
				{
					if($newPaymentRegisterCount	>	0)
					{//echo "3";?>
						<a class="title3" href="payment_renew.php" style="color:#E57200";>
						<?= $parObj->_getLabenames($arrayDataProfile,'changePlan','name').$parObj->_getLabenames($arrayDataProfile,'linkClick','name');?>
						 </a><?php								
					}
					elseif(($userOldPaymentStatus	>	0)	&&	($dayCount	>	0))	
					{//echo "4";
						?>
						<p><a href="payment_renew.php" class="title3" style="color:#E57200";><?php
						echo $parObj->_getLabenames($arrayDataProfile,'planChangeMsg','name'); 	?>
						</a></p><?php	
					}
					else//if($userpaymentstatus	>	0)
					{//echo "5";
						?>
						<p><a href="payment_renew.php" class="title3" style="color:#E57200";> 
						<?php 
						echo $parObj->_getLabenames($arrayDataProfile,'popuppayment','name').$parObj->_getLabenames($arrayDataProfile,'linkClick','name');?>
						 </a></p><?php
					}
			}
			
			//------------------------------------------------------------------------------------------------//
			
		}
			
			//-------------------------------------------------------------------------------------------------//
		  //New ends
		 /* if(($userNewPaymentStatus > 0 || $newPaymentRegisterCount>0) && ($today < trim($AccountExpDate)))
			{
				?>
				<a class="profileactivelink" href="payment_renew.php">
           		 <?= $parObj->_getLabenames($arrayDataProfile,'planChangeMsg','name');?>
            </a>
            <?php
			}
          else if($paymentObj->canRenewSubscriptionToday($userId)==true && $userpaymentstatus >= 0 )
		  {
		  ?>           
            <a class="profileactivelink" href="payment_renewal.php?origin=juser">
           		 <?= $parObj->_getLabenames($arrayDataProfile,'popuppayment','name')." >>>";?>
            </a>
            <?php
		  }?>*/
		  ?>
        </p>
         
          <div class="bloks">
            <div class="rows">
               <?=mb_strtoupper($parObj->_getLabenames($arrayData,'email','name'),'UTF-8');?>:<span class="mail">  <?=$objGen->_output($userDetail['user_alt_email']);?></span>
            </div>
            <div class="rows"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'fname','name'),'UTF-8');?>:<span class="mail"> <?=mb_strtoupper($objGen->_output($userDetail['user_fname']),'UTF-8');?></span></div>
           <div class="rows"> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'lname','name'),'UTF-8');?>:<span class="mail"> <?=mb_strtoupper($objGen->_output($userDetail['user_lname']),'UTF-8');?></span></div>
           <div class="rows"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'gender','name'),'UTF-8');?>: <span class="mail"> <?php if($objGen->_output($userDetail['user_gender'])== 0){ echo mb_strtoupper($parObj->_getLabenames($arrayData,'man','name'),'UTF-8');}else{ echo mb_strtoupper($parObj->_getLabenames($arrayData,'woman','name'),'UTF-8');}?></span></div>
           <div class="rows"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'dob','name'),'UTF-8');?>:<span class="mail"> <?=mb_strtoupper($objGen->_output($userDetail['user_dob']),'UTF-8');?></span></div>
           <div class="rows"> <?=mb_strtoupper($parObj->_getLabenames($arrayData,'country','name'),'UTF-8');?>:<span class="mail"> <?=mb_strtoupper($objGen->_output($user_country),'UTF-8');?></span></div>
           <div class="rows"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'timezone','name'),'UTF-8');?>:<span class="mail"> <?=mb_strtoupper($objGen->_output($user_timezone),'UTF-8');?></span></div>
           <div class="rows"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'languagepreffer','name'),'UTF-8');?>:<span class="mail"> <?= mb_strtoupper($langArrayNew[$userDetail['user_language']],'UTF-8');?></span></div>
            
            
            
             
          </div>
          
<!--
           <div class="bloks second">
             <h3> <?=mb_strtoupper($parObj->_getLabenames($arrayDataProfile,'newNikePlusTxt','name'),'UTF-8');?></h3>
              <div class="rows"><?=$parObj->_getLabenames($arrayDataProfile,'nikeuser','name');?> : <span class="mail"> <?=$objGen->_output($resultNike['nike_login']);?></span></div>
              <div class="rows"><?=$parObj->_getLabenames($arrayDataProfile,'nikepass','name');?>: <span class="mail">••••••••</span></div>
           </div>
-->
         
          <div class="bloks second">
             <h3><?=mb_strtoupper($parObj->_getLabenames($arrayDataProfile,'newOptionalInfoTxt','name'),'UTF-8');?></h3>
                <div class="rows"><?=mb_strtoupper($optionMenus[$siteMasterMenuConfig['SPORTSCAT']],'UTF-8');?> : <span class="mail"><?=$sportNames;?></span></div>
                <div class="rows"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'weight','name'),'UTF-8');?> :  <span class="mail"><?=$objGen->_output($userDetail['user_weight_value'])." ".mb_strtoupper($weightUnits[$userDetail['user_weight_unit']],'UTF-8');?></span></div>
                <div class="rows"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'height','name'),'UTF-8');?> :<span class="mail"><?=$objGen->_output($userDetail['user_height_value'])." ".mb_strtoupper($heightUnits[$userDetail['user_height_unit']],'UTF-8');?></span></div>
                
                
          </div> 
          
          <div class="btn-box">
          <p><form action="edit_profile.php" method="post">
			  <input type="submit" class="btn_orng" value="<?=mb_strtoupper($parObj->_getLabenames($arrayDataProfile,'button','name'),'UTF-8');?>" name="editProfile"></form></p>
			  
          <p><input   type="button" class ="btn_blue" onclick="showChangePassword();" value="<?=$parObj->_getLabenames($arrayDataProfile,'changepass','name');?>" id="editPassword" name="editPassword"></p>
        </div>
       <!--style="width:100%;"-->
       
        </div>
          <div class="bils">
			  <?if($pdf == 1){?>
           <a href="myInvoices.php"> <h3><?=mb_strtoupper($parObj->_getLabenames($arrayDataPgm,'invoice','name'),'UTF-8');?></h3></a><?}?>
             <h3 class="title2"><?=mb_strtoupper($parObj->_getLabenames($arrayDataPgm,'newHstryTxt','name'),'UTF-8');?></h3>
             <div class="aligned"> <p class="title3">
             
              <?php 
			   if(count($program)>0 && $workOrderHistory > 0) 
			   {
			   ?>
                <a style="color: #83C0EA;" href="<?=ROOT_JWPATH?>historical.php?pgm_id=<?=base64_encode($program_id)?>&workoutFlexId=<?=trim($workoutFlexHistory)?>&ccess=Y29tc2Vzcw==">
                <?=mb_strtoupper($parObj->_getLabenames($arrayDataPgm,'myhistorytext','name'),'UTF-8');?>
                </a>
                <?php 
			   }
			   else 
			   { 
			   ?>
                <a style="color: #83C0EA;" href="<?=ROOT_JWPATH?>historical.php">
                <?=mb_strtoupper($parObj->_getLabenames($arrayDataPgm,'myhistorytext','name'),'UTF-8');?>
                </a>
                <?php
			   } 
			   ?></p></div>
<!--
             <h3 class="title2">MES INFOS</h3>
-->
            
          </div>
       
       </div>
       
       </section>
        <!-----newwww-->
     
   
         <?
		 $isNewPaybox	=	false;
		 $qry	=	"select * from payment_paybox where user_id=".$_SESSION['user']['userId']." and status='ACTIVE' order by id desc";
		 echo $qry;
		 $payboxData  = mysql_query($qry);
		 
		 if(mysql_num_rows($payboxData)>0){
			 $payboxRow	=	mysql_fetch_array($payboxData);
			 print_r($payboxRow);
			 if($payboxRow["status"]=="ACTIVE"){
				 $isNewPaybox	=	true;
				 $payboxUserId	=	$payboxRow["id"];
			 }
		 }
		 $qry			=	"select * from payment_paybox where user_id=".$_SESSION['user']['userId']." order by id desc";
		 $resultUnSub	=	mysql_query($qry);
		 if(mysql_num_rows($resultUnSub)>0)
		 {
			$unSubData	=	mysql_fetch_array($resultUnSub);
			//print_r($unSubData);
			if(($unSubData['status']=='UNSUBSCRIBED')&&($unSubData['unsubscribed_date']!='0000-00-00')	&&	($dayCount	>	0))
			{
				echo "haiii enter";
				$sts	=	true;
				//$parObj->_getLabenames($arrayDataProfile,'memUnsubscribeComment','name');
				$unSubMsg	=	$parObj->_getLabenames($arrayDataProfile,'memUnsubscribeMessage','name').date("d-m-Y", strtotime($AccountExpDate));
			}
		}		 

//-------------------------------------------------------------------------------------------------------------------------------//

if($userDetail['user_language']!=5)
{
echo $userDetail['user_language'];
echo "paybox".$isNewPaybox;
echo "payed".$Payed;
echo "unsubscrb".$unSub;
echo "usernewpayment".$userNewPaymentStatus;
echo "newpaymentrgstr".$newPaymentRegisterCount;
echo "daycouont".$dayCount;
//-------------------------------------------------------------------------------------------------------------------------------//
		 if($isNewPaybox){
			 //echo "enterrr";
		 ?>
            
              <h2 class="title3" ;="" style="color:#E57200" >
              <?=$parObj->_getLabenames($arrayDataProfile,'memUnsubscribeComment','name');?>
              <script language="javascript">
			  		function test(){
							document.getElementById('unsubscribePgm1').style.display='block';
							centerPopupMyProfile('unsubscribePgm1');
							  
							
					}
					
				function centerPopupMyProfile(popupId){
					var popId	=	popupId;
					popupId	=	"#"+popupId; 
				
					var windowWidth = document.documentElement.clientWidth;
					var windowHeight = document.documentElement.clientHeight;
					var popupHeight = document.getElementById(popId).clientHeight;
					var popupWidth =document.getElementById(popId).clientWidth;
					$(popupId).css({
						"position": "fixed",
						"top": windowHeight/2-popupHeight/2,
						"left": windowWidth/2-popupWidth/2
					});

				}
			  </script>
                <a href="javascript:void(0)" onclick="return test();"><font class="red">
              <?=$parObj->_getLabenames($arrayDataProfile,'memUnsubscribeLink','name');?>
              </font></a>
              </h2>          
              <div class="btm"></div>
            
	     <?php
		 }
		 else if($Payed == 1 && $unSub == 0 && $userNewPaymentStatus== 0 && $newPaymentRegisterCount==0 &&	$dayCount	>	0)
		 {
			 //echo "haiiiiii unsubscribe";
			 ?>
            <div class="container-3">
              <h2 class="title3" ;="" style="color:#83C0EA" >
              <?=$parObj->_getLabenames($arrayDataProfile,'memUnsubscribeComment','name');?>
                <a href="javascript:void(0)" onclick="return unsubscribe();"><font class="red">
              <?=$parObj->_getLabenames($arrayDataProfile,'memUnsubscribeLink','name');?>
              </font></a>
              </h2>          
              <div class="btm"></div>
            </div>
   <?php }
		if($sts)	
		{?>
        	<div class="container-3" style="padding-right:30px;">
              <h2 class="title3" ;="" style="color:#83C0EA" >
              <?=$unSubMsg?>                
              </h2>          
              <div class="btm"></div>
            </div>
  <?php }
  if($Payed == 1 && ($unSub != 0||	$refResult[reffSts]!=0) && ($userNewPaymentStatus== 0	||	$refResult[reffSts]!=0) && $newPaymentRegisterCount==0 &&	$dayCount	>	0)
	//echo "no subsribeee";
  {?>
       <div class="container-3" style="padding-right:30px;">
              <h2>
              <?=$parObj->_getLabenames($arrayDataProfile,'memUnsubscribeMessage','name').date("d-m-Y", strtotime($AccountExpDate))?>                
              </h2>          
              <div class="btm"></div>
            </div>
	<?php }
	//----------------------------------------------------------------------//
	}
	//----------------------------------------------------------------------//
	
	?>          
     <!---new---> 
     </div>
   
     </div>
   
    <!---pop up for changing password-->
    <div class="pop" id="changePassword" >
		<!-- <img src="<?=ROOT_FOLDER?>images/close.png" alt="close" class="close b-modal __b-popup1__" onclick="hideUnsubscribe('changePassword');" />-->
       <a style="display: inline;" title="close" onclick="hideUnsubscribe_change_password('changePassword');" id="fancybox-close1"></a>
        <div class="popbox login">
          <h3>
            <?=$parObj->_getLabenames($arrayDataProfile,'changepasstext','name');?>
          </h3>
          <span id = "showChange1" style="display:block;">

          <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
            <tr>
              <td colspan="2" align="left" id="showerror" class="error_message"></td>
            </tr>
            <tr>
              <td width="35%" align="left" valign="middle"><?=$parObj->_getLabenames($arrayDataProfile,'oldpass','name');?>
                <font class='red-1'>*</font></td>
              <td width="65%"><input type="password" id="oldpass" class="field" name="oldpass" /></td>
            </tr>
            <tr>
              <td align="left" valign="middle"><?=$parObj->_getLabenames($arrayDataProfile,'newpass','name');?>
                <font class='red-1'>*</font></td>
              <td><input type="password" id="newpass" name="newpass" class="field" maxlength="15"/></td>
            </tr>
            <tr>
              <td align="left" valign="middle"><?=$parObj->_getLabenames($arrayDataProfile,'confirmpass','name');?>
                <font class='red-1'>*</font></td>
              <td><input type="password" id="confirmpass" name="confirmpass" class="field"  maxlength="15"/></td>
            </tr>
            <tr>
				
              <td colspan="2" align="center"><input class="btn_pop ease" name="Update" type="button" value="<?=$parObj->_getLabenames($arrayDataProfile,'update','name');?>" onclick="UpdatePassword()" /></td>
           
            </tr>
          </table>

<!--
           <div class="bloks">
           
            <div class="rows"><?=$parObj->_getLabenames($arrayDataProfile,'oldpass','name');?>:<span class="mail"> <input type="password" id="oldpass" name="oldpass" /></span></div>
           <div class="rows"> <?=$parObj->_getLabenames($arrayDataProfile,'newpass','name');?>:<span class="mail"> <input type="password" id="newpass" name="newpass" maxlength="15"/></span></div>
           <div class="rows"><?=$parObj->_getLabenames($arrayDataProfile,'confirmpass','name');?>: <span class="mail"> <input type="password" id="confirmpass" name="confirmpass"  maxlength="15"/></span></div>
           <div class="rows"><input style="text-align:center;" class="bu_03" name="Update" type="button" value="<?=$parObj->_getLabenames($arrayDataProfile,'update','name');?>" onclick="UpdatePassword()" /></div>
          </div>
-->
          
          </span> <span id = "showChange2" style="display:none;font-weight:bold;text-align:center;padding-top:15px;">
          <?=$parObj->_getLabenames($arrayDataProfile,'changepasssuccess','name');?>
          </span></div></div>
         
   <!---newwwww -->
         <!---newwwww div for unsubscribe-->
       <div class="popnew" id="unsubscribePgm" >
      <img src="<?=ROOT_FOLDER?>images/close.png" alt="close" class="close b-modal __b-popup1__" onclick="hideUnsubscribe('unsubscribePgm');" />
     <div class="popbox">
          <h2 <?php if($lanId==5){?> style="border-bottom:none;"<?php }?>>
            <?=$parObj->_getLabenames($arrayDataProfile,'unsubscribeDetailsSentence0','name');?>
            <?php echo $print_finalFormat;?>
			<?php if($lanId!=5){?>
            <?=$parObj->_getLabenames($arrayDataProfile,'unsubscribeDetailsSentence1','name');?>
			<?php }?>
          </h2>
          <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
            <tr>
              <td colspan="2" align="center">-
                <?=$parObj->_getLabenames($arrayDataProfile,'unsubscribeDetailsSentence2','name');?></td>
            </tr>
            <tr>
              <td colspan="2" align="center">-
                <?=$parObj->_getLabenames($arrayDataProfile,'unsubscribeDetailsSentence3','name');?></td>
            </tr>
            <tr>
              <td colspan="2" align="center">-
                <?=$parObj->_getLabenames($arrayDataProfile,'unsubscribeDetailsSentence4','name');?></td>
            </tr>
			<?php if($lanId==5){?>
			  <tr>
              <td colspan="2" align="center" style="border-top:1px solid #FFFFFF;">
            Czy na pewno chcesz zrezygnować ze swojego abonamentu?
             </td>
            </tr>
			 <?php }?>
			
            <tr>
              <td colspan="2" align="center"><input class="btn_pop ease" name="Yes" type="button" value="<?=$parObj->_getLabenames($arrayDataProfile,'unsubscribeYes','name');?>" onClick="unsubscribeMembership()" />
                &nbsp;
                <input class="btn_pop ease" name="No" type="button" value="<?=$parObj->_getLabenames($arrayDataProfile,'unsubscribeNo','name');?>" onClick="hideUnsubscribe('unsubscribePgm');" /></td>
            </tr>
          </table>
          <div class="clear"></div>
        </div>
        </div>
    <!---newwwww end div for unsubscribe-->  
     
       <!----new unsubscribePgm1-->   
      
      <div class="popnew" id="unsubscribePgm1" >
      <img src="<?=ROOT_FOLDER?>images/close.png" alt="close" class="close b-modal __b-popup1__" onclick="hideUnsubscribe('unsubscribePgm1');" />
      <div class="popbox">
          <h2>
            <?=$parObj->_getLabenames($arrayDataProfile,'unsubscribeDetailsSentence0','name');?>
            <?php echo $print_finalFormat;?>
            <?=$parObj->_getLabenames($arrayDataProfile,'unsubscribeDetailsSentence1','name');?>
          </h2>
         <hr>
          <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
            <tr>
              <td colspan="2" align="left">-
                <?=$parObj->_getLabenames($arrayDataProfile,'unsubscribeDetailsSentence2','name');?></td>
            </tr>
            <tr>
              <td colspan="2" align="left">-
                <?=$parObj->_getLabenames($arrayDataProfile,'unsubscribeDetailsSentence3','name');?></td>
            </tr>
            <tr>
              <td colspan="2" align="left">-
                <?=$parObj->_getLabenames($arrayDataProfile,'unsubscribeDetailsSentence4','name');?></td>
            </tr>
            <tr>
            <script language="javascript">
            	function testYes(){ 
						hideUnsubscribe('unsubscribePgm1')						
						unSubscrb(2,'<?php echo $payboxUserId; ?>');
				}
            </script>
              <td colspan="2" align="center"><input class="btn_pop ease" name="Yes" type="button" value="<?=$parObj->_getLabenames($arrayDataProfile,'unsubscribeYes','name');?>" onClick="testYes() " />
                &nbsp;
                <input class="btn_pop ease" name="No" type="button" value="<?=$parObj->_getLabenames($arrayDataProfile,'unsubscribeNo','name');?>" onClick="hideUnsubscribe('unsubscribePgm1');" /></td>
            </tr>
          </table>
          <div class="clear"></div>
        </div>
        </div>
     
      
         <!----new end unsubscribePgm1-->   
         <?php if(count($reffCode)>0) { ?>
      <div class="popnew" id="discountDetails">
        <img src="<?=ROOT_FOLDER?>images/close.png" alt="close" class="close b-modal __b-popup1__" onclick="hideUnsubscribe('discountDetails');" />
        <div class="popbox">
          <h2>
            <?=$parObj->_getLabenames($arrayDataProfile,'referralcodedetails','name');?>
          </h2>
          <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
            <tr>
              <td colspan="2" align="center" class="white"><?=$parObj->_getLabenames($arrayDataProfile,'referralcode','name');?>
                &nbsp;:&nbsp;
                <?=$reffCode['user_reff_id'];?></td>
            </tr>
            <tr>
              <td colspan="2" align="center"><?=$parObj->_getLabenames($arrayDataProfile,'refcodeDetailsSigned1.0','name');?>
                <?=stripslashes($signedUser['discount_percentage'])?>
                %
                <?=$parObj->_getLabenames($arrayDataProfile,'refcodeDetailsSigned1.1','name');?></td>
            </tr>
            <tr>
              <td colspan="2" align="center"><?=$parObj->_getLabenames($arrayDataProfile,'refcodeDetailsNew1.0','name');?>
                <?=stripslashes($newUser['discount_percentage'])?>
                %
                <?=$parObj->_getLabenames($arrayDataProfile,'refcodeDetailsNew1.1','name');?>
                <?=stripslashes($newUser['discount_no_count'])?>
                <?=$parObj->_getLabenames($arrayDataProfile,'refcodeDetailsNew1.2','name');?></td>
            </tr>
          </table>
          <div class="clear"></div>
        </div>
     
      </div>
      <?php } ?>
   
     <!---new-->
     <!-- pop up-->
	<script type="text/javascript" src="<?=ROOT_FOLDER?>js/jquery.bpopup.min.js"></script>
    <script type="text/javascript" src="<?=ROOT_FOLDER?>js/jquery.easing.1.3.js"></script>
	<!--pop up ends--> 
    
     <script src="js/flaunt.js"></script>
	
		<!-- Demo Analytics -->
	
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
            if (l.className.indexOf('label_') == -1) continue;
            var inp = gebtn(l,'input')[0];
            if (l.className == 'label_check') {
                l.className = (safari && inp.checked == true || inp.checked) ? 'label_check c_on' : 'label_check c_off';
                l.onclick = check_it;
            };
            if (l.className == 'label_radio') {
                l.className = (safari && inp.checked == true || inp.checked) ? 'label_radio r_on' : 'label_radio r_off';
                l.onclick = turn_radio;
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
<?php include("footer.php"); ?>
