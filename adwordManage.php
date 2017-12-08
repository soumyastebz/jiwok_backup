<?php 
$refSet	=	false;
if(isset($_REQUEST["utm_campaign"])){
	if(strtolower($_REQUEST["utm_campaign"])=="adwords_1"){
		$_SESSION['medium'] = "adwords";
		$_SESSION['referrarId']="0";
		$refSet	=	true;
	}else if(strtolower($_REQUEST["utm_campaign"])=="fb_campaign"){
		$_SESSION['medium'] = "fbcampaign";
		$_SESSION['referrarId']="0";
		$refSet	=	true;	
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))=="coaching  sportif"){
		$_SESSION['medium'] = "coaching  sportif";					
		$_SESSION['referrarId']="0";
		$refSet	=	true;	
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))== utf8_encode("coaching course à pied")){
		$_SESSION['medium'] = utf8_encode("coaching course à pied");
		$_SESSION['referrarId']="0";
		$refSet	=	true;	
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))=="entrainement marathon"){
		$_SESSION['medium'] = "entrainement Marathon";
		$_SESSION['referrarId']="0";
		$refSet	=	true;	
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))=="entrainement semi-marathon"){
		$_SESSION['medium'] = "entrainement semi-marathon";
		$_SESSION['referrarId']="0";
		$refSet	=	true;
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))==utf8_encode("coaching perte de poids générique")){
		$_SESSION['medium'] = utf8_encode("coaching perte de poids générique");
		$_SESSION['referrarId']="0";
		$refSet	=	true;
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))=="lp"){
		$_SESSION['medium'] = "Email perte poids socialweb";
		$_SESSION['referrarId']="0";
		$refSet	=	true;
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))== utf8_encode("entrainement salle de sport générique")){
		$_SESSION['medium'] = utf8_encode("entrainement salle de sport générique");
		$_SESSION['referrarId']="0";
		$refSet	=	true;
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))=="entrainement tapis de course"){
		$_SESSION['medium'] = "Entrainement tapis de course";
		$_SESSION['referrarId']="0";
		$refSet	=	true;
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))=="entrainement tapis marche"){
		$_SESSION['medium'] = "Entrainement tapis marche";
		$_SESSION['referrarId']="0";
		$refSet	=	true;
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))== utf8_encode("entrainement vélo d'appartement")){
		$_SESSION['medium'] = utf8_encode("entrainement vélo d'appartement");
		$_SESSION['referrarId']="0";
		$refSet	=	true;
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))== utf8_encode("entrainement vélo elliptique")){
		$_SESSION['medium'] = utf8_encode("entrainement vélo elliptique");
		$_SESSION['referrarId']="0";
		$refSet	=	true;		
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))=="coaching sportif"){
		$_SESSION['medium'] = "coaching sportif";
		$_SESSION['referrarId']="0";
		$refSet	=	true;		
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))== utf8_encode("course à pied perte de poids")){
	    $_SESSION['medium'] =	utf8_encode("course à pied perte de poids");
		$_SESSION['referrarId']="0";
		$refSet	=	true;		
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))== utf8_encode("entrainements course à pied")){
		$_SESSION['medium'] = utf8_encode("entrainements course à pied");
		$_SESSION['referrarId']="0";
		$refSet	=	true;		
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))=="entrainement perte de poids"){
		$_SESSION['medium'] = "Entrainement perte de poids";
		$_SESSION['referrarId']="0";
		$refSet	=	true;		
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))=="entrainement appareils cardio"){
		$_SESSION['medium'] = "Entrainement appareils cardio";
		$_SESSION['referrarId']="0";
		$refSet	=	true;		
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))=="fb_sport"){
		$_SESSION['medium'] = "fb_sport";
		$_SESSION['referrarId']="0";
		$refSet	=	true;
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))=="fb_forme"){
		$_SESSION['medium'] = "fb_forme";
		$_SESSION['referrarId']="0";
		$refSet	=	true;
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))=="oxygem"){
		$_SESSION['medium'] = "oxygem";
		$_SESSION['referrarId']="0";
		$refSet	=	true;
	}else if(strtolower(urldecode($_REQUEST["utm_campaign"]))=="runningheroes"){
		$_SESSION['medium'] = "runningheroes";
		$_SESSION['referrarId']="0";
		$refSet	=	true;
	}
	

	if($_SESSION['medium']!=""){
		$expire=time()+60*60*24*30;
		setcookie("jiwok_medium",$_SESSION['medium'],$expire);
		
		
	}
}
//http://www.jiwok.com/?utm_source=facebook&utm_medium=cpc&utm_campaign=fb_sport
// http://www.jiwok.com/?utm_source=facebook&utm_medium=cpc&utm_campaign=fb_forme

if(isset($_REQUEST["referrer"]) && isset($_REQUEST["media"]) && !($refSet)){//Checking if a referrel
	//Yes it is a referrel
	$referalCheck 	=	$_REQUEST["referrer"];
	$media 			=	$_REQUEST["media"];
	if(($media == 'fb') || ($media == 'tw')){
			$field = "jiwok_share_social";
	}else{
		if($media != 'mail')
			header("Location:index.php");
		else
			$field = "jiwok_share_email";
	}
	$selectQry		=	"SELECT * FROM ".$field." WHERE referral_secret_token = '".$referalCheck."'";
	$secret_tocken	= $GLOBALS['db']->getRow($selectQry,DB_FETCHMODE_ASSOC);
	if(count($secret_tocken) != 0){			
		$_SESSION['referrarId'] = $secret_tocken['user_id'];
		$_SESSION['medium'] = $media;
	}else
		header("Location:index.php");
}
?>