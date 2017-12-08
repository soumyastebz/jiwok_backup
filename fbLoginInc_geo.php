<?php 
//~ ini_set('display_errors',1);
//~ error_reporting(E_ALL);
/*include_once('/home/sites_web/client/newdesign/tools/fbconnect/src/facebook.php');
include_once('/home/sites_web/client/newdesign/includes/classes/class.fbLogin.php');*/
include_once('tools/fbconnect/src/facebook.php');
include_once('includes/classes/class.fbLogin.php');
$facebook = new Facebook(array(
  'appId'  => '1474495546178758',
  'secret' => '134cf69f691a5006af647d3c55cb051e',
));
$fbLogin		=	new fbLogin();
$contentObj 	=   new Contents('header_geo.php');
//$redUrl		=	JIWOK_URL."images/";echo $redUrl	;exit;


$fbLoginData	= 	$contentObj->_getTagcontents($xmlPath,'fbLogin','label');   // Get content from XML
$fbLoginAry		= 	$fbLoginData['general'];

/*ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);*/

$currUrlFb=(!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
//die($currUrlFb);

if($_REQUEST["fbLogin"]!="1"){
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

$targt	=	"index_neethu.php";

if($fbLogin->fbMode=="registration"){
	$targt	=	"userreg1.php";	
}


// See if there is a user from a cookie
$user = $facebook->getUser();

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    //$user_profile = $facebook->api('/me');print_r($user_profile);
     $user_profile = $facebook->api('/me', array('fields' => 'id,email'));
  } catch (FacebookApiException $e) {
    //echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
    $user = null;
  }
}


if (($user_profile)&&($_REQUEST["fbLogin"]=="1")) { // Check whether the user fb user set and user tried to login 
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
				$popupMsg	=	str_replace("#EMAIL_ID#",$user_profile["email"],$popupMsg);
			}else if($fbLogin->getUserFBSts($userId)==1){ // Check wheteher user allow fblogin
				
				$redUrl		=	JIWOK_URL."index_neethu.php";
				if(isset($_SESSION["fbLoginRed"])){
					if($_SESSION["fbLoginRed"]!=""){
						$redUrl		=	str_replace("fbLogin=1","",$_SESSION["fbLoginRed"]);
					}
				}
				
				$_SESSION["LoginModeUserId"]	=	$userId;
				$_SESSION["LoginMode"]	=	"fbLogin";
				unset($_SESSION["fbLoginRed"]);
				$fbLogin->setUserTocken($userId,$facebook->accessToken,$user_profile['id']);
				header('Location:'.$redUrl);
				exit;
			}else{
				//User not allow fb login
				header('Location:'.$redUrl);
				exit;
			}
		}else{ // New user
			$fbLogin->setUserData($user_profile,$facebook->accessToken);
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
 } else {
	if($_REQUEST["fbLogin"]=="1"){ 
		$redirectURLfb1 = JIWOK_URL.$targt."?fbLogin=1";
		header("location:http:".$redirectURLfb1);
		exit;
	} 
    $showBtn	=	true;
}
?>

