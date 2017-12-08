<?php
if(!isset($_SESSION)){
    session_start();
}

// Include the autoloader provided in the SDK
require_once 'tools/php-fb-graph-5.5/src/Facebook/autoload.php';

// Include required libraries
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

/*
 * Configuration and setup Facebook SDK
 */
$appId         = '292024831260993'; //Facebook App ID
$appSecret     = '124f64a48a3c2d2edfdcb6556264ab14'; //Facebook App Secret
//$redirectURL   = 'https://www.jiwok.com/fbLoginInc_fbtest.php'; //Callback URL
$redirectURL   = 'https://www.jiwok.com/index.php'; //Callback URL
$fbPermissions = array('email');  //Optional permissions

$fb = new Facebook(array(
    'app_id' => $appId,
    'app_secret' => $appSecret,
    'default_graph_version' => 'v2.9',
));

// Get redirect login helper
$helper = $fb->getRedirectLoginHelper();
?>