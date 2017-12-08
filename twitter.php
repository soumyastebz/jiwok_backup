<?php
ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);
session_start();
require_once 'includes/classes/Referrel/Twitter/twitteroauth.php';

if($_REQUEST["resetTockens"]=="1"){
	unset($_SESSION['oauth_token']);	
}


if(!isset($_SESSION["oauth_token"])){
	define("CONSUMER_KEY", "I5R18Hmaa75jA5vUWBE8Q");
	define("CONSUMER_SECRET", "NZKG7O9Af3mAulReW7zIe1dvYBtfw3W2hiwnL8P9jY");
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
	$request_token = $connection->getRequestToken();
	$_SESSION['oauth_token'] = $request_token['oauth_token'];
	$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
	$_SESSION['oauth_verifier'] = $request_token['oauth_verifier'];
	$url = $connection->getAuthorizeURL($request_token);
	header('Location:'.$url);
	die();
}
else{
	echo "haiiiiiiiii";
}
?>
<html>
<head>
<title>Share On Twitter</title>
</head>
<body>
<script type="text/javascript">
window.close();
</script>
</body>
</html>
