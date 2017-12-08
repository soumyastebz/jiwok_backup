<?php 
if(!isset($_SESSION)){
    session_start();
}


// Include FB config file && User class
require_once 'fbConfig.php';
include_once('includes/classes/class.fbLogin.php');
$fbLogin		=	new fbLogin();
$contentObj 	=   new Contents('header.php');
//$redUrl		=	JIWOK_URL."images/";echo $redUrl	;exit;


$fbLoginData	= 	$contentObj->_getTagcontents($xmlPath,'fbLogin','label');   // Get content from XML
$fbLoginAry		= 	$fbLoginData['general'];

/*ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);*/

$currUrlFb=(!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
//die($currUrlFb);

if(isset($_REQUEST["fbLogin"])!="1"){
	$_SESSION["fbLoginRed"]	=	$currUrlFb;
}

if(strpos($currUrlFb,"?")){
	$currUrlFb	=	$currUrlFb."&";
}else{
	$currUrlFb	=	$currUrlFb."?";
}

$showBtn	=	false;
$userRegConfirm	=	false;

$fbLogin->fbMode	=	$fbMode;

$targt	=	"index.php";

if($fbLogin->fbMode=="registration"){
	$targt	=	"userreg1.php";	
}

//================
if(isset($_SESSION['facebook_access_token'])){
	$accessToken	=	$_SESSION['facebook_access_token'];
	
}

if(isset($accessToken)){
    if(isset($_SESSION['facebook_access_token'])){
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    }else{
        //  Put short-lived access token in session
        $_SESSION['facebook_access_token'] = (string) $accessToken;
        
          // OAuth 2.0 client handler helps to manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();
		$metaData = $oAuth2Client->debugToken($_SESSION['facebook_access_token']);
		 //echo $metaData->getAppId(); 
        
        // Exchanges a short-lived access token for a long-lived one
        $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
        $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
        
        // Set default access token to be used in script
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    }
    
    // Redirect the user back to the same page if url has "code" parameter in query string
    if(isset($_GET['code'])){
        header('Location:fbLoginInc_v5.php');
    }

    // Getting user facebook profile info
    try {
		$profileRequest = $fb->get('/me?fields=id,email,name,gender,first_name,last_name,birthday,timezone');
        //$profileRequest = $fb->get('/me?fields=name,first_name,last_name,email,link,gender,locale,picture');
        $user_profile = $profileRequest->getGraphNode()->asArray();
		
    } catch(FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        session_destroy();
        // Redirect user back to app login page
        header("Location:fbLoginInc_v5.php");
        exit;
    } catch(FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
	
	//=============================
	if (($user_profile)&&(isset($_REQUEST["fbLogin"])=="1")) { 
		 
	
	// Check whether the user fb user set and user tried to login 
	if($user_profile["email"]!=""){	// Check whether email id is null
	
		$userId	=	$fbLogin->isUserExistes($user_profile["email"]); // Check whether the user exists or not
		
		if($userId){	// User exists
			$fbLogin->setUserProfileImage($userId,$user_profile["id"]);
			if($fbLogin->fbMode=="registration"){ // Check whether user tried to register
				//Popup confirmation msg
				
				$userRegConfirm	=	true;
				$fbLogin->setUserData($user_profile,$facebook->accessToken);
				$popupMsg	=	$contentObj->_getLabenames($fbLoginAry,'newRegCnfTxt','name');
				$popupYes	=	$contentObj->_getLabenames($fbLoginAry,'newYesTxt','name');
				$popupNo	=	$contentObj->_getLabenames($fbLoginAry,'newNoTxt','name');
				$popupMsg	=	str_replace("#EMAIL_ID#",$user_profile["email"],$popupMsg);//echo $popupMsg;echo "here1";
			}else if($fbLogin->getUserFBSts($userId)==1){ // Check wheteher user allow fblogin
				
				$redUrl		=	JIWOK_URL."index.php";
				if(isset($_SESSION["fbLoginRed"])){
					if($_SESSION["fbLoginRed"]!=""){
						$redUrl		=	str_replace("fbLogin=1","",$_SESSION["fbLoginRed"]);
					}
				}
				
				$_SESSION["LoginModeUserId"]	=	$userId;
				$_SESSION["LoginMode"]	=	"fbLogin";
				unset($_SESSION["fbLoginRed"]);
				//$fbLogin->setUserTocken($userId,$facebook->accessToken,$user_profile['id']);
				$fbLogin->setUserTocken($userId,$_SESSION['facebook_access_token'],$user_profile['id']);
				 
				header('Location:'.$redUrl);
				exit;
			}else{
				
				//User not allow fb login
				header('Location:'.$redUrl);
				exit;
			}
		}else{ // New user
			$_SESSION['fb_details']= $user_profile;//echo "pp";print_r($_SESSION['fb_details']);exit;
			$fbLogin->setUserData($user_profile,$_SESSION['facebook_access_token']);
			if($lanId !=5)
			{
				//header('Location:user.php')
				$redirectURLfb = JIWOK_URL."userreg2.php";
				header('Location:'.$redirectURLfb);
				exit;
			}
			else
			{
				$redirectURLfb = JIWOK_URL."pl/userreg2.php";
				header('Location:'.$redirectURLfb);
				exit;
			}
			
		}
	}
 
	}
	else {
		
	if(isset($_REQUEST["fbLogin"])=="1"){ 
	
		$redirectURLfb1 = JIWOK_URL.$targt."?fbLogin=1";
		header("location:https:".$redirectURLfb1);
		exit;
	} 
    $showBtn	=	true;
}
	//==================================
  }  
  else {
		
	if(isset($_REQUEST["fbLogin"])=="1"){ 
	
		$redirectURLfb1 = JIWOK_URL.$targt."?fbLogin=1";
		header("location:https:".$redirectURLfb1);
		exit;
	} 
    $showBtn	=	true;
}
?>

