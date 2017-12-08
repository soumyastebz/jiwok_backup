<?php
	session_start();
	include_once('includeconfig.php');
	include_once('./includes/classes/class.member.php');
	include_once('./includes/classes/class.Languages.php');
    include_once('mes_historical.php');
		// create an object for manage the parsed content 
		$parObj 		=   new Contents('edit_profile.php');
		$objGen   		=	new General();
		$dbObj     		=   new DbAction();	
		$objMember		= 	new Member($lanId);
		$lanObj 		= 	new Language();	
		
		$userId	=	$_SESSION['user']['userId'];
		$userDetail		=	$objMember->_getAllByUserId($userId);
		$currentImage			= 	$userDetail[user_photo];
	
?>

<?php include("header.php"); ?>
<?php include("menu.php"); ?>
 <figure class="profile-image" id="new11">
				<input type="hidden" name="current_photo" value="<?=$currentImage?>"/>&nbsp;

					   <img src='./uploads/users/<?=$currentImage?>'>

				
				
       
       <a href="#" class="view-popup">Crop</a>
        </figure>
