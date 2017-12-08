<?php

ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);
	
session_start();

require_once 'includes/classes/Referrel/TwitterV1.1/TwitterAPIExchange.php';
require_once 'includeconfig.php';
require_once 'includes/classes/Referrel/class.referal.php';

$referal			=	new referal();

define("OAUTH_ACCESS_TOKEN","1545176754-KY43gpzgtG2DFY276dFBWmjNI1iZYmokd7qgzsd");
	define("OAUTH_ACCESS_TOKEN_SECRET","5HSUumGjwCYmwiLP8EA0WySnJLKEJ1vJ3TVM4kWoZlGre");
	define("CONSUMER_KEY","6Rbaudtgatz6q2BbDOOTNiGBK");
	define("CONSUMER_SECRET","OFxATUmp54N9oXTusujF4l2k3OPN3pfBFPDEQSNLz7Jhrm1pNO");

$settings = array(
    'oauth_access_token' => OAUTH_ACCESS_TOKEN,
    'oauth_access_token_secret' => OAUTH_ACCESS_TOKEN_SECRET,
    'consumer_key' => CONSUMER_KEY,
    'consumer_secret' => CONSUMER_SECRET
);


if($_REQUEST["resetTockens"]=="1"){
	unset($_SESSION['oauth_token']);	
}
if(isset($_REQUEST['repSniperUrl'])){
	$_SESSION['repSniperUrl']	=	$_REQUEST['repSniperUrl'];
	$repSniperUrl				=	$_SESSION['repSniperUrl']	;
	$_SESSION['mesTw']			=	$_REQUEST['mesTw'];
	$mesTw						=	$_SESSION['mesTw'];
	$userToken					=	$referal->getUserToken();
$makeUrl						=	"www.jiwok.com/index.php?utm_source=referal&utm_medium=referal&utm_term=referal&utm_campaign=referal&referrer=".$userToken."&media=tw&".md5(mt_rand());
$snippedUrl						=	$referal->googleApiCall($makeUrl);
$tweetmsg 						= 	str_replace($repSniperUrl,$snippedUrl,$mesTw);

$_SESSION['tweetmsg']			=	$tweetmsg;
}


//after userverify function get oauth verifier 
if((isset($_REQUEST['oauth_token']))&&(isset($_REQUEST['oauth_verifier'])))
	{
		
		if(!isset($_SESSION)){
			session_start();
			
			}
		
			//echo "<pre/>";echo "hereeee";print_r($_SESSION['tweetmsg']);exit;
		//Your application should verify that the token matches the request token received in step 1.
		$oauthverifier			=	$_GET['oauth_verifier'];
		$oauthtoken				=	$_GET['oauth_token'];
		$userAcccesstoken 		=	getMyAccesstoken($oauthverifier,$oauthtoken,$settings);// 3rd step get accesstoken using oauth vewrifier	
		
		$end_users_token		=	explode("=",$userAcccesstoken[0]);
		$end_users_token_secret	=	explode("=",$userAcccesstoken[1]);
		$tweetmsg				=	$_SESSION['tweetmsg'];// page refresh time will loss message so keep in session 
		$tweetPost				=	postUserTimeline($end_users_token[1],$end_users_token_secret[1],$tweetmsg);// tokens from get aaccess token api of end user 
		
	}

//=========
//if($_REQUEST['mode']=='0'){
if(!isset($_SESSION["oauth_token"])){

	if($_REQUEST['mode']=='0'){

		
	
	
		$accesstoken 	= 	RequestAccessToken($settings );// 1st step request accesstoken
		$oauthToken		=	$accesstoken;			
		$userredirect	=	UserVerify($settings,$oauthToken);//2nd step redirect user and get oauth verifier
	

	//======

		
	}
	else if($_REQUEST['mode']=='1'){
	
	    $tweetDetails		=	$referal->twConnect();//echo "wwwwww";print_r($tweetDetails);exit;
		$tweetPost			=	postUserTimeline($tweetDetails['twitter_oauth_token'],$tweetDetails['twitter_oauth_token_secret'],$_SESSION['tweetmsg']);
		
}
}



//==================
// first request accesstoken 
function RequestAccessToken($settings ) {
$url 				= 'https://api.twitter.com/oauth/request_token';
$requestMethod 		= 'POST';
$postfields 		= array();

 $twitter 			= new TwitterAPIExchange($settings);
  $my 				= $twitter->buildOauth($url, $requestMethod)
    ->setPostfields($postfields)
    ->performRequest();
	 
	$myRequest			=	explode("&",$my);
	$myRequesttoken		=	explode("=",$myRequest[0]);
	$mytoken			=	$myRequesttoken[1];
	return $mytoken;
}

function UserVerify($settings,$oauthToken ) {
$url 			= 'https://api.twitter.com/oauth/authenticate';
$getfield 		= '?oauth_token='.$oauthToken;
$requestMethod 	= 'GET';
$twitter 		= new TwitterAPIExchange($settings);
echo   $twitter->setGetfield($getfield)
				->buildOauth($url, $requestMethod)
    ->performRequest();

return $twitter;
}
//function to post on twitter 
function postUserTimeline($end_user_oauth_token,$end_user_oauth_token_secret,$tweetmsg ){

$consumer_key		= 	"6Rbaudtgatz6q2BbDOOTNiGBK";
$consumer_secret 	= 	"OFxATUmp54N9oXTusujF4l2k3OPN3pfBFPDEQSNLz7Jhrm1pNO";

$url 				= 'https://api.twitter.com/1.1/statuses/update.json';
$requestMethod 		= 'POST';
$postfields 		= array(
    'status' => $tweetmsg 
   
);
$settings_new = array(
    'oauth_access_token' => $end_user_oauth_token,
    'oauth_access_token_secret' => $end_user_oauth_token_secret,
    'consumer_key' => $consumer_key,
    'consumer_secret' => $consumer_secret 
);
$twitter = new TwitterAPIExchange($settings_new);
$mypost	=	 $twitter->buildOauth($url, $requestMethod)
    ->setPostfields($postfields)
    ->performRequest();
$postStatusArr	=	json_decode($mypost,true);

if (array_key_exists('errors', $postStatusArr)) {
   $_SESSION['twt_result']='0';
}
else{
	$_SESSION['twt_result']='1';
	$_SESSION['end_user_oauth_token']=$end_user_oauth_token;
	$_SESSION['end_user_oauth_token_secret']=$end_user_oauth_token_secret;
	
	///db insertion
	
	//db ends
}
/*echo "<pre/>";
print_r($_SESSION[]);exit;*/
?>
<script type="text/javascript">
window.close();
</script>
<?php
}
function getMyAccesstoken($oauthverifier,$oauthtoken,$settings)
{
	
	$url 		= 'https://api.twitter.com/oauth/access_token';
$requestMethod 	= 'POST';
$postfields 	= array(
    'oauth_token' => $oauthtoken, 
    'oauth_verifier' => $oauthverifier
);

$twitter 			= new TwitterAPIExchange($settings);
 $newAccessToken	=  $twitter->buildOauth($url, $requestMethod)
    ->setPostfields($postfields)
    ->performRequest();
	//collect accesstoken 
	$oauth_tokenResult 			=	explode("&",$newAccessToken);		
	return $oauth_tokenResult;
}

?>
