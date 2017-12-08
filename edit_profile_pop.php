<?php
	session_start();
	include_once('includeconfig.php');
	include_once('./includes/classes/class.member.php');
	include_once('./includes/classes/class.Languages.php');

		// create an object for manage the parsed content 
		$parObj 		=   new Contents('edit_profile.php');
		$objGen   		=	new General();
		$dbObj     		=   new DbAction();	
		$objMember		= 	new Member($lanId);
		$lanObj 		= 	new Language();	
		if($_SESSION['user']['userId']=='547'){//need to remove 547
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
			//Update member profile information
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
<!DOCTYPE HTML>
<html>
<head>
<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Jiwok</title>
<link rel="shortcut icon" type="image/ico" href="images/favicon.ico" />

<!-- Internet Explorer HTML5 enabling code: -->
<!--[if IE]>
           <script src="js/html5.js"></script>

<![endif]-->

<link href="resources/style.css" rel="stylesheet" type="text/css" />

		<link href="css/main.css" rel="stylesheet">
<style>
.has-js .label_check, .has-js .label_radio {  display:inline-block;background-position: 0 5px;}

</style>
<!----pop up-->

<!-- pop up-->
<!---------------------------->
</head>
<body>
<header>
  <div class="frame">
  <h1 class="logo">
     <a href="index.html"><img src="images/logo.png" alt="Jiwok" title="Jiwok"></a>
    
  </h1>
  <hgroup>
     <!--<input type="submit" value="LOGIN" class="login_btn">-->
     <a href="#" class="login_btn">LOGIN</a>
     <!--<span class="log"><input name="" type="checkbox" value=""> Se souvenir de moi | <a href="#">Mot de passe oublié ?</a> | </span>
     <span class="lang">
         <a href="#"><img src="images/FR.png" alt="Frunch" title="Frunch"></a>
         <a href="#"><img src="images/us.png" alt="US" title="English"></a>
         <a href="#"><img src="images/german.png" alt="German" title="Polish"></a>
     </span>
     <ul class="login">
         <li class="fb"><a href="#"><img src="images/fb-connect.png" alt="FB connect" title="Login with Facebook"></a></li>
         <li><input type="text" value=""></li>
         <li><input type="password"></li>
         <li><input type="submit" value="GO"></li>
     </ul>-->
  </hgroup>
  </div>
</header>

    
    
    <div class="top-nav">
        <nav class="nav">    
					<ul class="nav-list">
						<li class="nav-item"><a href="?=home">ACCUEIL</a></li>
						<li class="nav-item"><a href="?=home">VOTRE ENTRAINEMENT SUR MESURE</a></li>
                        <li class="nav-item"><a href="?=home">LES COACHS</a></li>
                        <li class="nav-item"><a href="?=home">BLOG</a></li>
                        <li class="nav-item"><a href="?=home">FORUM</a></li>
                        <li class="nav-item"><a href="?=home">CONTACT</a></li>
                        <li class="nav-item"><a href="?=home">AIDE</a></li>
					</ul>
				</nav>
     </div>
    <div class="frame_inner">
       <section class="profile">
         <div class="left">
            <ul class="bredcrumbs">
              <li style="color: #1473B4; font-weight: 700;"><?=$parObj->_getLabenames($arrayDataProfile,'newPgeTxt','name');?> :</li>
        <li><a href="<?=ROOT_JWPATH?>index.php"><?=$parObj->_getLabenames($arrayDataProfile,'newHmeTxt','name');?></a></li>
        <li style="color: #1473B4; font-weight: 700;">></li>
        <li ><a href="#" class="select" style="color: #E67F23;"><?=$parObj->_getLabenames($arrayDataProfile,'newHeadTxt','name');?></a></li>
            </ul>
   <form name="editProfile" enctype="multipart/form-data" action="edit_profile.php" method="post">           
<figure class="profile-image" >
				<input name="user_photo" type="file" class="filefieldEdit" />
				<input type="hidden" name="current_photo" value="<?=$currentImage?>"/>&nbsp;
				  <? if($currentImage != ""){?>
					   <img src='./uploads/users/<?=$currentImage?>'>
					  <?} else
					{?>
				
				<img src="images/profile-dummy.png" alt="profile image">
        <? }?>
       
        </figure>

         </div>
       <div class="profile-edit">
<!--
         <h2 class="name">DENIS DHEKAIER</h2>
-->
          <h2 class="name"><?=stripslashes($_POST['user_fname'])?> <?=stripslashes($_POST['user_lname'])?></h2>
         <h3 class="title2">MES INFOS</h3>
        <p class="title3"><?=$parObj->_getLabenames($arrayDataProfile,'newHeadTxt','name');?> </p>
      
			
			
        
<!--
///
<p <? if($err13 == 1){ ?> class="errorMessageBgCenter"<? }?>><?=$parObj->_getLabenames($arrayDataEditProfile,'myImage','name');?>  :  
					  <span><input name="user_photo" type="file" class="filefieldEdit" />
					  <input type="hidden" name="current_photo" value="<?=$currentImage?>"/>&nbsp;
				  <? if($currentImage != ""){?>
				  <a href="javascript:void(0)" class="bold" onClick="openNewWindow('./uploads/users/<?=$currentImage?>','windowname',<?=($imageDetails[0]+100);?>,<?=($imageDetails[1]+50);?>)">View</a>
				  <? }?></span>
					  <? if($err13 == 1){ ?><span class="errorMessageCommonEP" onMouseOver="tooltip('<?=$photo?>');" onMouseOut="exit();">[?]</span><? }?></p>
//
-->
          <div class="bloks">
           <div class="rows">
               <div class="col-01" <? if($err0 == 1){ ?> <? }?>><?=$parObj->_getLabenames($arrayData,'email','name');?>:</div>
               <div class="col-02 "><input name="user_alt_email" type="text" class="tfl" value="<?=stripslashes($_POST['user_alt_email'])?>"></div>
               <? if($err0 == 1){ ?><span class="errorMessageCommonEP" onMouseOver="tooltip('<?=$emailMsg?>');" onMouseOut="exit();">[?]</span><? }?>
            </div>
      
             <div class="rows">
               <div class="col-01" <? if($err1 == 1){ ?> <? }?>><?=$parObj->_getLabenames($arrayData,'fname','name');?>:</div>
               <div class="col-02"><input name="user_fname" type="text" class="tfl" value="<?=stripslashes($_POST['user_fname'])?>"></div>
               <? if($err1 == 1){ ?><span class="errorMessageCommonEP" onMouseOver="tooltip('<?=$fname?>');" onMouseOut="exit();">[?]</span><? }?>
            </div>
            
           <div class="rows">
               <div class="col-01" <? if($err2 == 1){ ?> <? }?>><?=$parObj->_getLabenames($arrayData,'lname','name');?>:</div>
               <div class="col-02"><input name="user_lname" type="text" class="tfl" value="<?=stripslashes($_POST['user_lname'])?>"></div>
            <? if($err2 == 1){ ?><span class="errorMessageCommonEP" onMouseOver="tooltip('<?=$lname?>');" onMouseOut="exit();">[?]</span><? }?>
            </div>
            
            <div class="rows">
               <div class="col-01" <? if($err3 == 1){ ?> <? }?>><?=$parObj->_getLabenames($arrayData,'gender','name');?>: </div>
               <div class="col-02"><label class="label_radio" for="">
                      <input name="user_gender"  value="1" type="radio" <? if($_POST['user_gender'] == 0) { echo 'checked'; } ?> value="0" />
                      <?=$parObj->_getLabenames($arrayData,'man','name');?>
                      </label>
                      <label class="label_radio" for="">
                      <input name="user_gender"  value="1" type="radio" <? if($_POST['user_gender'] == 1)
                      { echo 'checked'; } ?> value="1" /><?=$parObj->_getLabenames($arrayData,'woman','name');?>
                      </label>
 <? if($err3 == 1){ ?><span class="errorMessageCommonEP" onMouseOver="tooltip('<?=$gender?>');" onMouseOut="exit();">[?]</span><? }?>
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
               <div class="col-02"><select name="user_country" id="user_country" class="selet">
                   <?php foreach($country_list as $country_data){ ?>
					<option value="<?php echo $country_data['countries_id']; ?>" <? if($_POST['user_country']== $country_data['countries_id']) 
					print 'selected'; if($_POST['user_country']=='' && strtolower($country_data['countries_name'])=='france') print 'selected';?>><?php echo $country_data['countries_name'];?></option><?php } ?>


                       </select>
                       <? if($err10 == 1){ ?><span class="errorMessageCommonEP" onMouseOver="tooltip('<?=$country_err_msg?>');" onMouseOut="exit();">[?]</span><? }?></div>
            </div>  
            
            <div class="rows">
               <div class="col-01" <? if($err22 == 1){ ?><? }?>><?=$parObj->_getLabenames($arrayData,'timezone','name');?>:</div>
               <div class="col-02"><select  name="user_timezone" id="user_timezone" class="selet">
                  <?php foreach($time_zones as $time_zone){ ?>

					<option value="<?php echo $time_zone['time_tz']; ?>" <? if($_POST['user_timezone']== $time_zone['time_tz']) print 'selected';?>><?php if($lanId==2){echo $time_zone['gmt_timezone'];}else{echo $time_zone['time_name'];}?></option><?php } ?>
                       </select><? if($err22 == 1){ ?><span class="errorMessageCommonEP" onMouseOver="tooltip('<?=$timezone_err_msg?>');" onMouseOut="exit();">[?]</span><? }?></div>
            </div>
           
             <div class="rows">
               <div class="col-01" <? if($err12 == 1){ ?><? }?>><?=$parObj->_getLabenames($arrayData,'languagepreffer','name');?>: </div>
               <div class="col-02"><select class="selet" name="user_language">
                  <?
				  foreach($siteLanguagesConfig as $key=>$data){
					 ?>

					<option  value="<?=$key?>" <? if($_POST['user_language']==$key) print 'selected'; if($_POST['user_language']=='' && 
					   $langName==strtolower($data)) print 'selected';?>><?=$parObj->_getLabenames($arrayData,strtolower($data),'name');?></option><? } ?>  
                       </select></div>
            </div>
          <a href="#" class="view-popup" style="color: #1473B4;font-size: 16.39px;">Cropping tool >>></a>
          </div>
       
<!--
           <div class="bloks">
             <h3><?=$parObj->_getLabenames($arrayDataProfile,'newNikePlusTxt','name');?> </h3>
              <div class="rows">
               <div class="col-02"><?=$parObj->_getLabenames($arrayDataProfile,'nikeuser','name');?>: </div>
               <div class="col-01"><input name="nike_login" type="text" class="tfl" value="<?=stripslashes($_POST['nike_login'])?>"></div>
               <? if($err16 == 1){ ?>
							<span class="errorMessageCommonEP" onMouseOver="tooltip('<?=$nikelog?>');" onMouseOut="exit();">[?]</span><? }?>
              </div>
              
              <div class="rows">
               <div class="col-02"><?=$parObj->_getLabenames($arrayDataProfile,'nikepass','name');?>:</div>
               <div class="col-01"><input name="nike_password" type="password" class="tfl" value="<?=stripslashes($_POST['nike_password'])?>"></div>
                <? if($err17 == 1){ ?><span class="errorMessageCommonEP" onMouseOver="tooltip('<?=$nikepss?>');" onMouseOut="exit();">[?]</span><? }?>
              </div>
           </div>
-->
         
          <div class="bloks">
             <h3><?=$parObj->_getLabenames($arrayDataProfile,'newOptionalInfoTxt','name');?></h3>
                <div class="rows">
                   <div class="col-01">SPORTS PRATIQUÉS :</div>
                   <div class="col-02"><select class="selet" name="sport[]">
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
                           </select></div>
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
<!--
                 <div class="rows">
                            <span class="colums"><label>FCM : </label> <input name="" type="text" class="tfl_small"></span>
                            <span class="colums">  <label> FCR:</label>   <input name="" type="text" class="tfl_small"></span>
                             <span class="colums"> <label> VMA:</label>  <input name="" type="text" class="tfl_small"></span>
                </div>
-->
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
          <p class="prfEdtSmt" ><input type="hidden" name="useremail" id="useremail" value="<?=stripslashes($userDetail['user_alt_email']);?>" />
				<input name="update" id="update" type="submit" value="<?=$parObj->_getLabenames($arrayDataEditProfile,'update','name');?>" class="btn_orng" /></p>
				
				
          <div class="bils">
             <h3>MES FACTURES</h3>
             <h3 class="title2">MES INFOS</h3>
             <p class="title3">MES ANCIENNES SÉANCES</p>
          </div>
</form>        
       </div>
       </section>
       <!-----pop up--->
       
       <section class="pop"> <img src="images/close.png" alt="close" class="close b-modal __b-popup1__">



          <div class="popbox">
           
          
          <form action="#" method="get" accept-charset="utf-8">
         
<figure class="profile-image" >
				<input name="user_photo" type="file" class="filefieldEdit" />
				<input type="hidden" name="current_photo" value="<?=$currentImage?>"/>&nbsp;
				  <? if($currentImage != ""){?>
					   <img src='./uploads/users/<?=$currentImage?>'>
					  <?} else
					{?>
				
				<img src="images/profile-dummy.png" alt="profile image">
        <? }?>
       
        </figure>
</form>


          <div align="center"><input type="submit" class="btn_pop ease" value="VALIDER"></div></div>
          </section>
       <!--pop up ends-->
     </div>
     
     
     
     
     <div class="foot-nav frame">
                                                                                                                                                              
        <ul class="nav_03">
          <li><a href="#">aide</a></li>
          <li><a href="#">plan du site</a></li>
          <li><a href="#">qui sommes nous?</a></li>
          <li><a href="#">termes et conditions</a></li>
        </ul>
     </div>
     <footer>
          <div class="frame">
             <nav class="col-01">
                <a class="logo" href="#"><img src="images/logo-footer.png" alt="Jiwok"></a>
                <ul class="footnav_01">
                   <li><a href="#">LES TÉMOIGNAGES</a></li>
                   <li><a href="#">LA PRESSE</a></li>
                   <li><a href="#">LES PASS JIWOK</a></li>
                </ul>
                <a class="find" href="#">RETROUVEZ NOUS<br> 
SUR GOOGLE +</a>
             </nav>
              <nav class="col-02">
                <h2><span>CE QUE JIWOK VOUS APPORTE</span></h2>
                    <ul class="footnav_02">
                      <li><a href="#">Courir plus vite</a></li>
                      <li><a href="#">Améliorer sa VMA</a></li>
                      <li><a href="#">Débuter en course à pied</a></li>
                    </ul>
                    <h2><span>LES COACHS</span></h2>
              </nav>
              <nav class="col-03">
                <h2><span>VOTRE ENTRAÎNEMENT SUR MESURE</span></h2>
                <div class="clear"></div>
                <div class="colums">
                <ul class="footnav_02">
                      <li><a href="#">Carte cadeau sport running Courir plus</a></li>
                      <li><a href="#">vite et améliorer sa vma</a></li>
                      <li><a href="#">débuter en course à pied</a></li>
                      <li><a href="#">Entrainement la parisienne</a></li>
                      <li><a href="#">Entrainement Marathon Paris</a></li>
                      <li><a href="#">Entrainement marche</a></li>
                      <li><a href="#">Entrainement marche nordique</a></li>
                      <li><a href="#">Entrainement marche sur tapis</a></li>
                      <li><a href="#">Entrainement marche sur tapis roulant</a></li>
                      <li><a href="#">Entrainement Mud Day - Spartan -</a></li>
                      <li><a href="#">Fappading - Course d’obstacles</a></li>
                      <li><a href="#">Entrainement Natation</a></li>
                    </ul>
                     <ul class="footnav_02">
                      <li><a href="#">Entrainement tapis de course</a></li>
                      <li><a href="#">Entrainement tapis roulant</a></li>
                      <li><a href="#">Entrainement triathlon</a></li>
                      <li><a href="#">Entrainement Ultra Trail</a></li>
                      <li><a href="#">Entrainement velo appartement</a></li>
                    </ul>
                   </div>
                   
                   <div class="colums">
                <ul class="footnav_02">
                      <li><a href="#">Entrainement vélo d’appartement</a></li>
                      <li><a href="#">Entrainement velo elliptique</a></li>
                      <li><a href="#">Entrainement velo interieur</a></li>
                      <li><a href="#">Perdre du poids 10 kg</a></li>
                      <li><a href="#">Perdre du poids 5 kg</a></li>
                      <li><a href="#">Plan entrainement 10 km</a></li>
                    </ul>
                    
                     <ul class="footnav_02">
                      <li><a href="#">Plan entrainement 20 km Paris</a></li>
                      <li><a href="#">Plan entrainement marathon new york</a></li>
                      <li><a href="#">Plan entrainement semi-marathon</a></li>
                      <li><a href="#">Plan Entrainement Trail</a></li>
                      <li><a href="#">Preparer le test de coope</a></li>
                    </ul>
                   </div>
              </nav>  
          </div>
     </footer>
     <div class="block_foot">
        <h4>Débuter le jogging, running, course à pied, vélo appartement, elliptique, marche, trail</h4>
    
<p>Si vous désirez commencer à courir, faire du sport, débuter elliptique ou vous remettre au sport, Jiwok est le service qu'il vous faut !
Tous les débutant pourront ainsi rapidement progresser.
Vous serez coacher et vous pourrez progresser afin de rester en forme, retouver votre ligne et perdre du poids ( de 3 à 10 kilos).</p>

<h4>Plan marathon, semi marathon, 10 km, trail</h4>
<p>Jiwok propose également des plan d'entrainement marathon, semi marathon, 10 km et trail pour les débutants et les confirmées. Des objectifs de 1 h 30 à 4 h 30 afin de vous permettre de progresser, de courir plus et d'amioler votre temps de couse
Vous pourrez également améliorer votre vma.</p>
<h4>Perdre du poids en faisant du sport</h4>
<p>Avec les conseils de votre coach jiwok, vous pourrez perdre du poids en courant, nageant ou pedélant grâce au jogging, running, course à pied, marche, tapis de course, vélo d'appartement, elliptique et natation
Retrouver la forme rapidement et progressivement avec des séances de sports adaptés à votre niveau physique</p>
<h4>courir et faire du sport en musique</h4>
<p>Suivez les séances et les playlist Jiwok pour courir en musique. Les musiques selectionnés par jiwok vous permettront de courir, jogging, running avec des morceaux de musiques adaptés au sport</p>
<h4>application ihpone et android sport</h4>
<p>Installez notre application Iphone et Android pour suivre les séances de coaching Jiwok : séance running, jogging, course à pied, natation, marche, elliptique, vélo, tapis de course, marathon, semi marathon, 10 km, trail, débutant en sport, perte de poids</p>
 </div>
  
  <ul class="foot_links">

   <li><a href="http://www.jiwok.com/about-us">Qui sommes-nous?</a></li>

   <li>|</li>

   <li> <a href="http://www.jiwok.com/sitemap.php">Plan du site</a></li>

   <li>|</li>

   <li> <a href="http://www.jiwok.com/contact-us">Contact</a></li>

  
   <li>|</li>

   <li> <a href="http://www.jiwok.com/terms-and-conditions">Termes et conditions</a></li>

   <li>|</li>

   <li> <a href="http://www.jiwok.com/press">Presse</a></li>

   <li>|</li>

   <li> <a href="http://www.jiwok.com/jobs">Job</a></li>

   <li>|</li>

    

   <li> <a href="http://www.jiwok.com/partners">Partenaires</a></li>

   <li>|</li>
   <li> <a href="http://www.jiwok.com/faq">Aide</a></li>
   </ul>  
    
   <p class="copyright">Copyrights JIWOK 2015  |  powered by Reubro International Debugging</p> 
     </div>
     <script src="js/jquery.min.js"></script>
     <script src="js/flaunt.js"></script>
	<!-- pop up-->
	<script type="text/javascript" src="js/jquery.bpopup.min.js"></script>
    <script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
	<!--pop up ends-->
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
    <!-----pop up script-->
    
            ;(function($) {
        $(function() {
            $('.view-popup').bind('click', function(e) {
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
    <!--- pop up ends-->
</script>

</body>
</html>
