<?php
	session_start();
	include_once('includeconfig.php');
	include_once('./includes/classes/class.member.php');
	include_once('./includes/classes/class.Languages.php');
    include_once('mes_historical.php');

		// create an object for manage the parsed content 
		$parObj 		=   new Contents('');
		$objGen   		=	new General();
		$dbObj     		=   new DbAction();	
		$objMember		= 	new Member($lanId);
		$lanObj 		= 	new Language();	
		if($_SESSION['user']['userId']==''){//need to remove 547
		header('location:index.php');
		}else{
		$userId	=	$_SESSION['user']['userId'];
		}
		
		if($lanId=="") $lanId=1;
		$langName=strtolower($lanObj->_getLanguagename($lanId));
		$useremail 	 = $_POST[useremail];   // used in forum table updation, and for user identity
		
		//collecting data from the xml for the static contents
		$returnData		= $parObj->_getTagcontents($xmlPath,'registrationUser','label');
		$arrayData		= $returnData['general'];
		//neethu
		$returnDataPgm		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
		$arrayDataPgm		= $returnDataPgm['general'];
		//neethu
		//collecting data from the xml for the static contents
		$returnDataEditProfile		= $parObj->_getTagcontents($xmlPath,'editprofile','label');
		$arrayDataEditProfile		= $returnDataEditProfile['general'];
		//collecting data from the xml for the static contents
		$returnDataProfile		= $parObj->_getTagcontents($xmlPath,'myprofile','label');
		$arrayDataProfile		= $returnDataProfile['general'];
		//collecting the error messaage from the xl file
		$errorReturn=$parObj->_getTagcontents($xmlPath,'registrationUser','messages');
		$errorData=$errorReturn['errorMessage'];
		//get language details
		$LanguagesArray		   	=	$lanObj->_getLanguageArray(); 
		//for fetching the country name
		$countriesArray 		= $objMember->_getCountries();
		/* Take all label name from label_manager table with menumaster_id = $genreMenuMasterId  */
		
		//for fetching the timezone name
		$TimezoneArray = $objMember->_getTimezone();
		//getall jobs from database
		$jobArray		=	$objMember->_getAllJobs($lanId);
		//getall sports from database
		$sportArray		=	$objMember->_getAllSports($lanId);
				
		/* Take all label name from label_manager table with menumaster_id = $userOptionMenuMasterId  */
		$optionMenus			= $objMember->_getOptionalMenus($siteMasterMenuConfig['USER_OPTIONAL_FIELDS'],$lanId);
		/* Take voice preference from label_manager */
		$voicePrefer			= $objMember->_getOptionalMenus($siteMasterMenuConfig['VOICE'],$lanId);
		 
		$weightUnits    		= $objMember->_getOptionalMenus($siteMasterMenuConfig['WEIGHT'],$lanId);
		
		$heightUnits    		= $objMember->_getOptionalMenus($siteMasterMenuConfig['HEIGHT'],$lanId);
			
		//get user optional field selected values 
		$useroptionalValues		= $objMember->_getUserOptinalFieldValues($userId,$siteMasterMenuConfig['USER_OPTIONAL_FIELDS']);
	if(isset($_POST[update]))
	{ 
				
	/* Validation for add and update*/
		$errorMsg = 0;
		// check if user_alt_email is NULL
		if(trim($_POST['user_alt_email']) == '')
		{ $emailMsg = $parObj->_getLabenames($errorData,'noemail','name'); $errorMsg = 1; $err0 = 1; }
		// If user_alt_email is not NULL 
		if(trim($_POST['user_alt_email']) != '') {	
			//check if it has a valid email format
			if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", trim($_POST['user_alt_email'])))	{ 
				$emailMsg = $parObj->_getLabenames($errorData,'emailerr','name'); $errorMsg = 1; $err0 = 1; 
			} else {
			//check if it is unique
				  $email_exist_query	= "SELECT COUNT(*) FROM user_master 
										WHERE user_id <> ? AND ( user_email = ? OR user_alt_email = ? OR paybox_email = ? )";
				$num_email   = $GLOBALS['db']->getOne($email_exist_query, array($_SESSION['user']['userId'], 
																				$_POST['user_alt_email'], 
																				$_POST['user_alt_email'], 
																				$_POST['user_alt_email']));
			    
			      												
				if($num_email > 0 ) { $emailMsg = $parObj->_getLabenames($errorData,'emailexist','name'); $errorMsg = 1; $err0 = 1; }
			}
  
		}
		if(trim($_POST['user_fname']) == '')
		{ $errorMsg = 1; $fname = $parObj->_getLabenames($errorData,'fname','name'); $err1 = 1; }
		if(trim($_POST['user_fname']) != '')
		{ 
			if($lanId!=5)
			{
				$pattern 	= 	'/^[a-zA-Z\xC0-\xFF\' ]+$/u';	
			}
			else
			{
				$pattern 	=	'/^\p{L}[\p{L} _.-]+$/u';
			}	
				$okay 	= 	preg_match($pattern,trim($_POST['user_fname']));
		if(!$okay)	{	$errorMsg = 1; $fname = $parObj->_getLabenames($errorData,'fnameerr','name'); $err1 = 1; }
		}
		
		if(trim($_POST['user_lname']) == '')
		{ $errorMsg = 1; $lname = $parObj->_getLabenames($errorData,'lname','name'); $err2 = 1; }
		if(trim($_POST['user_lname']) != '')
		{ 
			if($lanId!=5)
			{
				$pattern 	= 	'/^[a-zA-Z\xC0-\xFF\' ]+$/u';
			}
			else
			{
				$pattern 	= 	'/^\p{L}[\p{L} _.-]+$/u';
			}
				$okay 	= 	preg_match($pattern,trim($_POST['user_lname']));
		if(!$okay)	{	$errorMsg = 1; $lname = $parObj->_getLabenames($errorData,'lnameerr','name'); $err2 = 1; }
		}
		
		if($_POST['user_gender'] == '') { $errorMsg = 1; $gender = $parObj->_getLabenames($errorData,'gender','name'); $err3 = 1; }
		
		if($_POST['user_day'] == 0 && $_POST['user_month'] == 0 && $_POST['user_year'] == 0)
		{ $errorMsg = 1; $dob = $parObj->_getLabenames($errorData,'dob','name'); $err4 = 1; }
		elseif($_POST['user_day'] == 0 || $_POST['user_month'] == 0 || $_POST['user_year'] == 0)
		{ $errorMsg = 1; $dob = $parObj->_getLabenames($errorData,'doberr','name'); $err4 = 1; }
		if($_POST['user_country'] == '') 
		{ $errorMsg = 1; $country_err_msg = $parObj->_getLabenames($errorData,'country','name'); $err10 = 1; }
        if($_POST['user_timezone'] == '')
		{ $errorMsg = 1; $timezone_err_msg = $parObj->_getLabenames($errorData,'timezone','name'); $err22 = 1; }
		if($_POST['user_language'] == '') 
		{ $errorMsg = 1; $language = $parObj->_getLabenames($errorData,'language','name'); $err11 = 1; }
		if(trim($_POST['nike_login']) == '' && trim($_POST['nike_password']) != '')
			{ $errorMsg = 1; $nikelog = $parObj->_getLabenames($errorData,'login','name'); $err16 = 1; }	
		if(trim($_POST['nike_login']) != '' && trim($_POST['nike_password']) == '')
			{ $errorMsg = 1; $nikepss = $parObj->_getLabenames($errorData,'pass','name'); $err17 = 1; }	
		if($_FILES['user_photo']['name'] != '')
		{
			$imageType = @exif_imagetype($_FILES['user_photo']['tmp_name']);
		    if(!(($imageType >= 1) and ($imageType <=3))) { 
		     $errorMsg = 1; $photo = $parObj->_getLabenames($errorData,'photo','name'); $err13 = 1; }
			else
			{
			$image_name 	= $_FILES['user_photo']['name'];
			$image_tmp_path = $_FILES['user_photo']['tmp_name'];
			}
		}
		elseif($_POST['image_name'] != '')
		{
			$imageType = @exif_imagetype($_FILES['user_photo']['tmp_name']);
			  if(!(($imageType >= 1) and ($imageType <=3))) { 
			   $errorMsg = 1; $photo = $parObj->_getLabenames($errorData,'photo','name'); $err13 = 1; }
			else
			{
			$image_name 	= $_POST['image_name'];
			$image_tmp_path = $_POST['image_path'];
			}
		}
		if($_POST['user_weight_value'] == '')
		{ $errorMsg = 1; $weight = $parObj->_getLabenames($errorData,'weight','name'); $err14 = 1; }
		elseif(!is_numeric($_POST['user_weight_value']) || $_POST['user_weight_value'] == 0 || $_POST['user_weight_value'] < 0)
		{ $errorMsg = 1; $weight = $parObj->_getLabenames($errorData,'weighterr','name'); $err14 = 1; }
		
		
		if($_POST['user_height_value'] == '')
		{ $errorMsg = 1; $height = $parObj->_getLabenames($errorData,'height','name'); $err15 = 1; }
		elseif(!is_numeric($_POST['user_height_value']) || $_POST['user_height_value'] == 0 || $_POST['user_height_value'] < 0)
		{ $errorMsg = 1; $height = $parObj->_getLabenames($errorData,'heighterr','name'); $err15 = 1; }
		
		
		//for sport list
		if($_POST['sport']!=""){
		$_POST['option_'.$siteMasterMenuConfig['SPORTSCAT']] =implode(',',$_POST['sport']);
		}
	
		/* *****IF THERE IS NO ERROR ...START UPDATE PROCCESS***** */
		if($errorMsg==0){
				
				$sp = $_POST['sport'];
				
				if(($_REQUEST['user_year']!=0 && $_REQUEST['user_month']!=0) && $_REQUEST['user_day'] !=0  ){
				$_POST['user_dob'] = $_REQUEST['user_day'].'/'.$_REQUEST['user_month'].'/'.$_REQUEST['user_year']; 
				unset($_POST['user_day']);
				unset($_POST['user_month']);
				unset($_POST['user_year']);
				}
				
				if($_POST['update']){ 
					//print_r($_POST);die;
					$_POST		=	$objGen->_clearElmtsWithoutTrim($_POST);
                    if($_FILES['user_photo']['name'] != ""){
                            $fileName   = uniqid();
                            $extension  = end(explode(".",$_FILES['user_photo']['name']));
                            $nextUpload = $objGen->_fileUploadWithImageResize('user_photo','./uploads/users/',$fileName,166,145);
							$fileName = $fileName.".".$extension;
							$img_file = "ph_".$fileName;
							$thumb_file = "th_".$fileName;
							$thumbUpload = $objGen->_fileImageResize2($img_file,'uploads/users/',$thumb_file,87,105);
                            $_POST['user_photo']    = $fileName;
							if($_POST['user_photo'] !="" && is_file("./uploads/users/".$_POST['current_photo'])){ 
							unlink("./uploads/users/".$_POST['current_photo']);
							unlink("./uploads/users/th_".$_POST['current_photo']);
							unlink("./uploads/users/ph_".$_POST['current_photo']);
							}
							
                  }
                  else{
                            $_POST['user_photo']    = $_POST['current_photo'];
                  }
					 $objMember->_updateFullMemberDetails($userId,$_POST,$lanId,$sp);
				 // updating user_master table completes
				 	
				//update forum table for the user
					$ForumArray = array();
					$ForumArray['user_timezone']	 	= trim($_POST['user_timezone']);
					if($_POST['user_language'] == 1)	// if preferred language is editing please use this else don't
						$ForumArray['user_lang'] 			= 'en'; 
					else
						$ForumArray['user_lang'] 			= 'fr'; 	
						
					$forum = $dbObj->_updateRecord('forum_users', $ForumArray, 'user_email="'.stripslashes($useremail).'"'); // update
					unset($ForumArray);	
				// Forum table updating Completes
				// update ticket table
					$fullname = trim($_POST['user_fname'])." ".trim($_POST['user_lname']);
					$TicketArray = array();
					$TicketArray['client_name']		 	= $fullname;
					$TicketArray['email']		 		= $_POST['user_alt_email'];
					if($_POST['user_language'] == 1)  // if preferred language is editing please use this else don't
						$TicketArray['default_lang'] 		= 'en'; 
					else
						$TicketArray['default_lang'] 		= 'fr';
						
					$ticket = $dbObj->_updateRecord('hdp_clients', $TicketArray, 'email="'.stripslashes($_POST[useremail]).'"');
					unset($TicketArray);
				// Ticket table updating Completes
					$nikeArray['nike_login'] = trim(stripslashes($_POST['nike_login']));
					$nikeArray['nike_password'] = base64_encode(trim(stripslashes($_POST['nike_password'])));
				$nke = $dbObj->_updateRecord('nike', $nikeArray, 'nike_userid="'.stripslashes($_SESSION['user']['userId']).'"');
				header("Location:myprofile.php?status=success_update");
			  }	
		   } else {
			//validation Error
				$currentImage	= $_POST['current_photo'];
		   }
			/* *****END OF UPDATE PROCCESS***** */
		}
		else
		{
						
			for($i=0;$i<count($useroptionalValues[id]);$i++)
			{
					$_POST['option_'.$useroptionalValues['id'][$i]] = $objGen->_output($useroptionalValues['value'][$i]);
			}
			//fetch the user details
			$userDetail		=	$objMember->_getAllByUserId($userId);
			$_POST[user_type] 		= 	$userDetail[user_type];
			$_POST[user_fname] 		=	$userDetail[user_fname];
			$_POST[user_lname] 		=	$userDetail[user_lname];
			$_POST[user_gender] 	=	$userDetail[user_gender];
			$_POST['user_alt_email']	=	$userDetail['user_alt_email'];
			$useremail				=	$userDetail[user_email];
			$dob			 		=	$userDetail[user_dob];
			$usrDob					=	explode("/", $dob);
			$currentImage			= 	$userDetail[user_photo];
			$_POST['user_day']		=	$usrDob[0];
			$_POST['user_month']	=	$usrDob[1];
			$_POST['user_year']		=	$usrDob[2];
			$_POST[user_address] 	=	$userDetail[user_address];
			$_POST[user_city] 		=	$userDetail[user_city];
			$_POST[user_state] 		= 	$userDetail[user_state];
			$_POST[user_country] 	=	$userDetail[user_country];
			$_POST[user_timezone] 	=	$userDetail[user_timezone]; 
			$_POST[user_zip] 		=	$userDetail[user_zip];
			$_POST[user_language] 	=	$userDetail[user_language];
			//$_POST[user_voice] 	    =	$userDetail[user_voice];
			$_POST[user_photo] 		=	$userDetail[user_photo];
			$_POST[user_weight_value]   =	$userDetail[user_weight_value];
			$_POST[user_weight_unit]    =	$userDetail[user_weight_unit];
			$_POST[user_height_value]   =	$userDetail[user_height_value];
			$_POST[user_height_unit] 	 =	$userDetail[user_height_unit];
		//For Nike Data
		$resultNike		=	$objMember->_getNikeDetail($userId);
		if(trim($resultNike['nike_login']) !='')
			$_POST['nike_login']		=	$resultNike['nike_login'];
		else
			$_POST['nike_login'] = '';
		if(trim($resultNike['nike_password']) !='')		
			$_POST['nike_password']		=	base64_decode($resultNike['nike_password']);
		else
			$_POST['nike_password']	= '';	
	}
	 if($currentImage != ""){
        $imageDetails = getimagesize('./uploads/users/'.$currentImage);
    }
	$country_list	= $objMember->getCountryList($langName);
    $time_zones	= $objMember->getTimezoneList();
		
?>

<?php include("header.php"); ?>
<?php include("menu.php"); ?>

<style>

.has-js .label_check, .has-js .label_radio {  display:inline-block;background-position: 0 5px;}
	<!--res-->
.simplePopup {
display:none;
position:fixed;
border:4px solid #808080;
background:#fff;
z-index:3;
padding:12px;
width:70%;
min-width:70%;
}

.simplePopupClose {
float:right;
cursor:pointer;
margin-left:10px;
margin-bottom:10px;
}

.simplePopupBackground {
display:none;
background:#000;
position:fixed;
height:100%;
width:100%;
top:0;
left:0;
z-index:1;
}
	<!--res-->
</style>
	<script src="<?=ROOT_FOLDER?>re_pop/js/jquery.simplePopup.js" type="text/javascript"></script>
<!---pop up-->

    <div class="frame_inner">
       <section class="profile">
         <div class="left">
               <ul class="bredcrumbs">
                <li style="color: #1473B4; font-weight: 700;"><?=$parObj->_getLabenames($arrayDataProfile,'newPgeTxt','name');?> :</li>
                <li><a href="<?=ROOT_JWPATH?>index.php"><?=$parObj->_getLabenames($arrayDataProfile,'newHmeTxt','name');?></a></li>
                <li style="color: #1473B4; font-weight: 700;">></li>
               <li ><a href="#" class="select" style="color: #E67F23;"><?=$parObj->_getLabenames($arrayDataProfile,'newHeadTxt','name');?></a></li>
            </ul>
            <!--crop-->
             <? if($currentImage){?>
<!--
			 <form name="crop" enctype="multipart/form-data" action="edit_profile.php" method="post">  	 
-->
<!--
<div class="test1">hhhhhhii</div>
-->
<div id="cropping">
          <figure class="profile-image" id="newww" >
				<input type="hidden" name="current_photo" value="<?=$currentImage?>"/>&nbsp;
				  <? if($currentImage != ""){?>
					   <img src='./uploads/users/<?=$currentImage?>'>
					  <?} else
					{?>
				
				<img src="images/profile-dummy.png" alt="profile image">
				
        <? }?>

<!--
       <input type="button"   class="show1" value="Crop" onclick="return showDiv();">
-->
		  <input type="button"   class="show1" value="Crop" >

<!--
       <a href="#"  class="show1" onclick="return showDiv();">Crop</a>
-->


<!--
<button type="button" id="buttn_{$index}"  name="reexpedition" value = "Update" onclick="return showDiv();" class="show1">Reexpedition </button>
-->

        </figure></div>
        
        <!----cropping code goes here-->
         <div id="pop1" class="simplePopup" style="display:none;position:absolute;
border:4px solid #808080;
background:#fff;
z-index:3;
padding:12px;
width:70%;
min-width:70%;" >
  <!--res-->

  <link href="crop/assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="crop/assets/css/cropper.min.css" rel="stylesheet">
  <style>

    body{
      font-family: 'Trebuchet MS', serif;
    }

    .container{
    /* width: 800px;
    margin: 0 auto; */
  }


  .no-js #loader { display: none;  }
  .js #loader { display: block; position: absolute; left: 100px; top: 0; }
  .se-pre-con {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url(assets/img/preloader.gif) center no-repeat #fff;
  }
  .se-pre-upload {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url(assets/img/upload-preloader.gif) center no-repeat rgba(255, 255, 255, 0.9);
  }


  .crop-box{
    max-height: 350px;
  }

  ul.tabs{
    margin: 0px;
    padding: 0px;
    list-style: none;
  }
  ul.tabs li{
    background: none;
    color: #222;
    display: inline-block;
    padding: 10px 15px;
    cursor: pointer;
  }

  ul.tabs li.current{
    background: #ededed;
    color: #222;
    font-weight: bold;
  }

  .tab-content{
    display: none;
    background: #ededed;
    padding: 15px;
  }

  .tab-content.current{
    display: inherit;
    padding-bottom: 370px;
  }
  .cropButton{
    margin-top:10px;
  }
#error1{
    padding: 20px;
    border: 0 none;
    height: 20px;
    left: 30%;
    margin-left: -50px;
    margin-top: -50px;
    position: fixed;
    top: 50%;
    width: 50%;
    text-align: center;
    z-index: 9999999;
}

</style>


  <div class="se-pre-upload"></div>
  <div class="se-pre-con"></div>
<!--
  <div class="container" style="height: 50px; width: 300px;">
-->
<!--
 <div class="container" >
    <ul class="tabs tabResolutionsNav">
    </ul>
    <div class="tabResolutionsContent"> 
-->
   
    

      <?php 
     
      // load basic configrations
      require_once 'crop/config.php';

       $imagePath = 'uploads/users/';
      //neethu
      // $crop_user_id = $_REQUEST['userId'];
        $crop_user_id = $_SESSION['user']['userId'];
      //neethu
      if($currentImage){
        $imageToCrop = $currentImage; 
        $imageName1 = explode(".", $imageToCrop);
      }else{
        $imageToCrop =  '';
      }
      //~ $imageToCrop = $_SESSION['crop']['image'];
		//~ $imageName1 = explode(".", $imageToCrop);



      $fullPath = explode('/' ,"http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
      array_pop($fullPath);
      $fullPath = implode('/', $fullPath);
      $resolutionData = file_get_contents($fullPath.'/crop/processor.php?mode=1');

      $data = json_decode($resolutionData);
      $tabId = 0;

      if($tabId == 0){
        $current = 'current';
      }else{
        $current ='';
      }

      foreach ($data as $item) {
        echo '
        <div id="tab-'.$tabId.'" class="tab-content crop-box '.$current.'">
          <div class="row">
            <div class="col-lg-6 ">
              <div class="cropper-tab-'.$tabId.' crop-box">
              <img src="'.$imagePath.$imageToCrop.'" alt="Invalid Picture">
              </div>
            </div>
          </div>
        </div>
        <div id="msg" ></div>';
        $tabId += 1;
      }

      ?>

      <!--   <img src="assets/img/pic1-1920x1080.jpg" alt="Picture"> -->
        <input type="hidden" name="crop_user_id" id="crop_user_id" value="<?php echo $crop_user_id; ?>">
	  <input type="hidden" name="imageToCrop" id="imageToCrop" value="<?php echo $imageName1[0]; ?>">
   
    <input type="button" id="cropit1" value="Crop All Images!">
<!--
    </div>
-->
    <!--neethu-->
    

   
<!--
  </div><!-- container -->

  <div>
  </div>
  </div> 
   <!----cropping code goes here-->
         <? }?>
         <? if($currentImage=="" ){?>
			 <img src="images/profile-dummy.png" alt="profile image"><?}?>
<!--
         </form>
-->
         <!--crop-->
    
	     

         </div>
          <form name="editProfile" enctype="multipart/form-data" action="" method="post">  
       <div class="profile-edit">
<!--
         <h2 class="name">DENIS DHEKAIER</h2>
-->
          <h2 class="name"><?=stripslashes($_POST['user_fname'])?> <?=stripslashes($_POST['user_lname'])?></h2>
         <h3 class="title2"><?=mb_strtoupper($parObj->_getLabenames($arrayDataPgm,'myinfo','name'),'UTF-8');?></h3>
        <p class="title3"> </p>
      

          <div class="bloks">
           <div class="rows">
               <div class="col-01" <? if($err0 == 1){ ?>  <? }?>><?=$parObj->_getLabenames($arrayData,'email','name');?>:</div>
               <div class="col-02 "><input name="user_alt_email" type="text" class="tfl" value="<?=stripslashes($_POST['user_alt_email'])?>"></div>
               <? if($err0 == 1){ ?> <img src="images/help1.jpg" class="Q-mark"   alt="help" onMouseOver="tooltip('<?=$emailMsg?>');" onMouseOut="exit();">
				   <? }?>
            </div>
      
             <div class="rows">
               <div class="col-01" <? if($err1 == 1){ ?> <? }?>><?=$parObj->_getLabenames($arrayData,'fname','name');?>:</div>
               <div class="col-02"><input name="user_fname" type="text" class="tfl" value="<?=stripslashes($_POST['user_fname'])?>"></div>
               <? if($err1 == 1){ ?><img src="images/help1.jpg" class="Q-mark"  onMouseOver="tooltip('<?=$fname?>');" onMouseOut="exit();"><? }?>
            </div>
            
           <div class="rows">
               <div class="col-01" <? if($err2 == 1){ ?> <? }?>><?=$parObj->_getLabenames($arrayData,'lname','name');?>:</div>
               <div class="col-02"><input name="user_lname" type="text" class="tfl" value="<?=stripslashes($_POST['user_lname'])?>"></div>
            <? if($err2 == 1){ ?><img src="images/help1.jpg" class="Q-mark"  onMouseOver="tooltip('<?=$lname?>');" onMouseOut="exit();"><? }?>
            </div>
            
            <div class="rows">
               <div class="col-01" <? if($err3 == 1){ ?> <? }?>><?=$parObj->_getLabenames($arrayData,'gender','name');?>: </div>
               <div class="col-02"><label class="label_radio" for="male">
                      <input id="male" type="radio" name="user_gender"  value="0"  <? if($_POST['user_gender'] == 0) { echo 'checked'; } ?> value="0" />
                      <?=$parObj->_getLabenames($arrayData,'man','name');?>
                      </label>
                      <label class="label_radio" for="female">
                      <input id="female" type="radio"  name="user_gender"  value="1" <? if($_POST['user_gender'] == 1)
                      { echo 'checked'; } ?> value="1" /><?=$parObj->_getLabenames($arrayData,'woman','name');?>
                      </label>
 <? if($err3 == 1){ ?><span src="images/help1.jpg" class="Q-mark"  onMouseOver="tooltip('<?=$gender?>');" onMouseOut="exit();">[?]</span><? }?>
 </div>
            </div>   <!--date section starts -->
             <div class="rows"> 
                  <div class="col-01"<? if($err4 == 1){ ?> <? }?>><?=$parObj->_getLabenames($arrayData,'dob','name');?>*:</div>
               <div class="col-02">
				<select name="user_day" class="tfl_small select">
                <option value="0" selected="selected"><?=$parObj->_getLabenames($arrayData,'day','name');?></option>
                 <?
				for($i=1; $i<=31; $i++){
					$string = "<option value={$i}";
					if($i == $_POST['user_day']){
						$string .= " selected";
					}
					$string .= ">{$i}</option>";
					echo $string;
				}
				?>
                </select> 
				<select name="user_month" class="tfl_small select">
                <option value="0" selected="selected"><?=$parObj->_getLabenames($arrayData,'month','name');?></option>
			   <?
					if($lanId	==	5)
					{
						$indexI	=	1;
						foreach($monthArray_PL	as $key=>$monthArray_PLs)
						{
							$siteMonthList[$indexI]	=	$monthArray_PLs;
							$indexI++;
						}
					}					
					for($i=1; $i<=count($siteMonthList); $i++){
						$string = "<option value={$i}";
						if($i == $_POST['user_month']){
							$string .= " selected";
						}
						$string .= ">";
						if($lanId==1	||	$lanId==5)
						 $string .=$siteMonthList[$i];
						else
						{
						  /*$fPattern2 	 = array('a','e','A','E','e','E','a','e','i','o','u','A','E','I','O','U','a','e','i','o','u','A','E','I','O','U','c','C');
							$fReplace2 	 = array('a','e','A','E','e','E','a','e','i','o','u','A','E','I','O','U','a','e','i','o','u','A','E','I','O','U','c','C');
						  $newMonth = preg_replace($fPattern2,$fReplace2,$monthArray[$siteMonthList[$i]]);*/
						  $string .=$monthArray[$siteMonthList[$i]];
						  
						  }
						$string .="</option>";
						echo $string;
					}
				?>
                </select>
				<select name="user_year" class="tfl_small select">
                <option value="0" selected="selected">--Year--</option>
			   <?
					for($i=date('Y')-1; $i>=1900; $i--){
					$string = "<option value={$i}";
					if($i == $_POST['user_year']){
						$string .= " selected";
					}
					$string .= ">{$i}</option>";
					echo $string;
					}
				?>
                 </select>
				 </div>
				  <? if($err4 == 1){ ?><span class="errorMessageCommonEP" onMouseOver="tooltip('<?=$dob?>');" onMouseOut="exit();">[?]</span><? }?></div>
                   <!--date section ends -->
            
           <div class="rows">
               <div class="col-01" <? if($err10 == 1){ ?> <? }?>><?=$parObj->_getLabenames($arrayData,'country','name');?>:</div>
               <div class="col-02"><div  class="selet3"><select name="user_country" id="user_country">
                   <?php foreach($country_list as $country_data){ ?>
					<option value="<?php echo $country_data['countries_id']; ?>" <? if($_POST['user_country']== $country_data['countries_id']) 
					print 'selected'; if($_POST['user_country']=='' && strtolower($country_data['countries_name'])=='france') print 'selected';?>><?php echo $country_data['countries_name'];?></option><?php } ?>


                       </select></div>
                       <? if($err10 == 1){ ?><span class="errorMessageCommonEP" onMouseOver="tooltip('<?=$country_err_msg?>');" onMouseOut="exit();">[?]</span><? }?></div>
            </div>  
            
            <div class="rows">
               <div class="col-01" <? if($err22 == 1){ ?><? }?>><?=$parObj->_getLabenames($arrayData,'timezone','name');?>:</div>
               <div class="col-02"><div  class="selet3"><select  name="user_timezone" id="user_timezone" >
                  <?php foreach($time_zones as $time_zone){ ?>

					<option value="<?php echo $time_zone['time_tz']; ?>" <? if($_POST['user_timezone']== $time_zone['time_tz']) print 'selected';?>><?php if($lanId==2){echo $time_zone['gmt_timezone'];}else{echo $time_zone['time_name'];}?></option><?php } ?>
                       </select></div><? if($err22 == 1){ ?><span class="errorMessageCommonEP" onMouseOver="tooltip('<?=$timezone_err_msg?>');" onMouseOut="exit();">[?]</span><? }?></div>
            </div>
           
             <div class="rows">
               <div class="col-01" <? if($err12 == 1){ ?><? }?>><?=$parObj->_getLabenames($arrayData,'languagepreffer','name');?>: </div>
               <div class="col-02"><div  class="selet3"><select name="user_language">
                  <?
				  foreach($siteLanguagesConfig as $key=>$data){
					 ?>

					<option  value="<?=$key?>" <? if($_POST['user_language']==$key) print 'selected'; if($_POST['user_language']=='' && 
					   $langName==strtolower($data)) print 'selected';?>><?=$parObj->_getLabenames($arrayData,strtolower($data),'name');?></option><? } ?>  
                       </select></div></div>
            </div>

 <div class="rows">
         <div class="col-01" <? if($err13 == 1)?>> <?=$parObj->_getLabenames($arrayDataEditProfile,'myImage','name');?> </div>
          <div class="col-02"><span><input name="user_photo" type="file" class="filefieldEdit" />
					  <input type="hidden" id ="current_photo" name="current_photo" value="<?=$currentImage?>"/>&nbsp;
				  <? if($currentImage != ""){?>
				  <a href="javascript:void(0)" class="col-02" onClick="openNewWindow('./uploads/users/<?=$currentImage?>','windowname',<?=($imageDetails[0]+100);?>,<?=($imageDetails[1]+50);?>)"></a>
				  <? }?></span>
					  <? if($err13 == 1){ ?><span class="errorMessageCommonEP" onMouseOver="tooltip('<?=$photo?>');" onMouseOut="exit();">[?]</span><? }?>
         </div>
         </div>
         </div>
        
          <div class="bloks">
             <h3><?=$parObj->_getLabenames($arrayDataProfile,'newOptionalInfoTxt','name');?></h3>
                <div class="rows">
                   <div class="col-01">SPORTS PRATIQUÉS :</div>
                   <div class="col-02"><div class="selet3"><select name="sport[]">
                        <option value="">--Select--</option>
                         <? 	$string = '';
                            foreach($sportArray as $w => $data)
                            {
                                $string .=  '<option value="'.$w.'"';
                                if(in_array($w,$optionSport))
                                    $string .= ' Selected';
                                $string .= '>'.$data.'</option>';
                            }
                            echo $string;?>
                           </select></div></div>
                </div>
             
                
                <div class="rows">
                   <div class="col-01" <? if($err14 == 1){ ?> class="errorMessageBgCenter"<? }?>><?=$parObj->_getLabenames($arrayData,'weight','name');?>:   </div>
                   <div class="col-02">
                   <input name="user_weight_value" type="text" class="tfl_small" value="<?=stripslashes($_POST[user_weight_value])?>"> 
                   <select name="user_weight_unit" class="tfl_small select"> 
					    <?
						foreach($weightUnits as $key => $data){
						?>
					   <option  value="<?=$key?>" <? if($_POST['user_weight_unit'] == $key) echo 'selected';?>><?=$data?></option>
					    <?
						} ?>
					   </select>
                   </div><? if($err14 == 1){ ?><span class="errorMessageCommonEP" onMouseOver="tooltip('<?=$weight?>');" onMouseOut="exit();">[?]</span><? }?>
                </div>
                
                <div class="rows">
                   <div class="col-01" <? if($err15 == 1){ ?> class="errorMessageBgCenter"<? }?>><?=$parObj->_getLabenames($arrayData,'height','name');?>    </div>
                   <div class="col-02">
                   <input name="user_height_value" type="text" class="tfl_small" value="<?=stripslashes($_POST[user_height_value])?>">
                    <select name="user_height_unit" class="tfl_small select"> 
						 <?
						foreach($heightUnits as $key => $data){
						?>
						<option value="<?=$key?>" <? if($_POST['user_height_unit'] == $key) echo 'selected';?>><?=$data?></option>  <? }?></select>
                   </div><? if($err15 == 1){ ?><span class="errorMessageCommonEP" onMouseOver="tooltip('<?=$height?>');" onMouseOut="exit();">[?]</span><? }?>
                </div>

                </div>
<!-- 3 boxes code
                <div class="rows">
                          <?
foreach($optionMenus as $key => $data)
	{
		if($key != $siteMasterMenuConfig['JOBCAT'])
		{?>  <span class="colums"><?=$data?>:						
                <? if($key	==	$siteMasterMenuConfig['SPORTSCAT'])
                {		
                    $optionSport = explode(',',$_POST['option_'.$key]);?><label><select multiple="multiple" name="sport[]" size="10" class="textAreaEdit">
                            <option value="">--Select--</option>
                            <? 	$string = '';
                            foreach($sportArray as $w => $data)
                            {
                                $string .=  '<option value="'.$w.'"';
                                if(in_array($w,$optionSport))
                                    $string .= ' Selected';
                                $string .= '>'.$data.'</option>';
                            }
                            echo $string;?>
                        </select>  </label>  <?	}
                else
                {?><label><input name="" type="text" class="tfl_small"></label><? }?>
                </div><?php }
	}?>

          </div> 
 3 boxes code-->
          <p ><input type="hidden" name="useremail" id="useremail" value="<?=stripslashes($userDetail['user_alt_email']);?>" />
				<input name="update" id="update" type="submit" value="<?=$parObj->_getLabenames($arrayDataEditProfile,'update','name');?>" class="btn_orng" />
				<div class="bils">
            <?if($pdf == 1){?><a href="myInvoices.php"> <h3><?=mb_strtoupper($parObj->_getLabenames($arrayDataPgm,'invoice','name'),'UTF-8');?></a></h3><?}?>
             <h3 class="title2"><?=mb_strtoupper($parObj->_getLabenames($arrayDataPgm,'newHstryTxt','name'),'UTF-8');?></h3>
              <p class="title3">
             
              <?php 
			   if(count($program)>0 && $workOrderHistory > 0) 
			   {
			   ?>
                <a style="color: #E57200;" href="<?=ROOT_JWPATH?>historical.php?pgm_id=<?=base64_encode($program_id)?>&workoutFlexId=<?=trim($workoutFlexHistory)?>&ccess=Y29tc2Vzcw==">
                <?=mb_strtoupper($parObj->_getLabenames($arrayDataPgm,'myhistorytext','name'),'UTF-8');?>
                </a>
                <?php 
			   }
			   else 
			   { 
			   ?>
                <a style="color: #E57200;" href="<?=ROOT_JWPATH?>historical.php">
                <?=mb_strtoupper($parObj->_getLabenames($arrayDataPgm,'myhistorytext','name'),'UTF-8');?>
                </a>
                <?php
			   } 
			   ?></p>
			 <!-----pop up--->
       

<!--
               <form action="#" method="get" accept-charset="utf-8">
				  <div class="image-size-label">
					  Resize image
				  </div>
				  <input type="hidden" name="current_photo" value="<?=$currentImage?>"/>&nbsp;
							 <?if($currentImage != "") ?>
								   <img src='./uploads/users/<?=$currentImage?>'>
				  <button class="export">Export</button>
				</div>  
              </form>
-->
<!--
         <iframe src='crop/crop.php?image=<? echo $currentImage?>&userId=<?php echo $userId?>' width='100%' height='500px'></iframe>
-->

</div></p>
 
        
       <!--pop up ends-->
				
				
<!--
          <div class="bils">
             <h3>MES FACTURES</h3>
             <h3 class="title2">MES INFOS</h3>
             <p class="title3">MES ANCIENNES SÉANCES</p>
          </div>
-->
        
       </div></form>
       </section> 
        </div>
    
   
   
   
  <!-- jQuery & Bootstrap -->
  <script type="text/javascript" src="crop/assets/js/jquery.min.js"></script>
  <script type="text/javascript" src="crop/assets/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="crop/assets/js/cropper.min.js"></script>
  <script type="text/javascript" src="crop/assets/js/main.js"></script>
  
<script type="text/javascript">



/*function showDiv()
{
   $('.show1').click(function(){
	//$('#pop1').simplePopup();
	 document.getElementById("pop1").style.display = 'block';
    });
   }*/

$(document).ready(function(){

    $('.show1').click(function(){ 
	//$('#pop1').simplePopup();
	$('#cropping').hide();
	$('#pop1').show();
    });
  
   
  
});
     	</script>


