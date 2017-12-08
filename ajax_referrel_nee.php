<?php
session_start();

/*ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);*/

require_once 'includeconfig.php';
require_once 'includes/classes/Referrel/class.referal.php';
require_once 'includes/classes/Referrel/TwitterV1.1/TwitterAPIExchange.php';
require_once 'includes/classes/Referrel/Facebook/facebook.php';
require_once 'includes/classes/Referrel/OpenInviter/openinviter.php';
include_once "Swift/lib/swift_required.php";


$referal		=	new referal();
$parObj 		= new Contents('referrel_system.php');

$returnData		= $parObj->_getTagcontents($xmlPath,'referralSectionNew','label');
$arrayData		= $returnData['general'];

$lanId			=	$_SESSION['language']['langId'];
if($lanId=="")	$lanId=2;


$referal->debugQuery	=	false;
extract($_POST);
$fbDetails		=	array(
								'appId'  => FACEBOOK_APP_ID,
								'secret' => FACEBOOK_SECRET_KEY,
								'cookie' => true
								);
switch($action){
	case "fb_share":
		$fbDetails   	= 	$referal->fbConnect();
		$facebook 		= 	new Facebooks($fbDetails);
		$fbMessage	=	$referal->getFbMessage($mesFb);
		try{
			$facebook->api("/".FACEBOOK_USER_ID."/feed", "post", $fbMessage);
		}
		catch(FacebookApiException $e){
			error_log($e);
			define(USER_STATUS,"Revoked");
			$updateArray	=	array("facebook_status"=>"2");	
			$referal->updateFbStatus($updateArray);
		}
		$result	=	array("result"=>USER_STATUS,"message"=>$fbMessage);
		$referal->saveShareData(1,$_SESSION['user']['userId'],$lanId);
		echo json_encode($result);
		break;
	case "fb_add_token":
		$userId	=	$_SESSION['user']['userId'];
		if($referal->checkUser($userId)){
			$updateArray	=	array("facebook_user_id"=>$uid,"facebook_access_token"=>$acc_tkn,"facebook_status"=>"1");	
			$referal->updateFbStatus($updateArray);
		}
		else{
			$userToken			=	$referal->getUserToken();
			$insertDbFields	=	"`user_id`,`facebook_user_id`,`facebook_access_token`,`referral_secret_token`,`facebook_status`";
			$insertDbValues	=	$userId.",'".$uid."','".$acc_tkn."','".$userToken."',1";
			$referal->dbInsertSingle("jiwok_share_social",$insertDbFields,$insertDbValues);
		}
		$fbDetails["access_token"]	=	$acc_tkn;
		$fbMessage						=	$referal->getFbMessage($mesFb); //print_r($fbMessage);exit;
	//	$fb	 								= 	new Facebooks($fbDetails);
		//$nn = $fb->api("/".$uid."/feed", "post", $fbMessage);
		//$result = $fb->api('/me/feed/','post',$fbMessage);
		
		
		/////

/*$msg = "testmsg";
$title = "testt";
$uri = "http://somesite.com";
$desc = "testd";
$pic = "http://static.adzerk.net/Advertisers/d18eea9d28f3490b8dcbfa9e38f8336e.jpg";
$attachment =  array(
'access_token' => $acc_tkn,
'message' => $msg,
'name' => $title,
'link' => $uri,
'description' => $desc,
'picture'=>$pic,

'actions' => json_encode(array('name' => 'post','link' => "www.jiwok.com"))
);
$bb = "https://graph.facebook.com/$uid/feed";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$bb);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLbbOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $attachment);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //to suppress the curl output
$result = curl_exec($ch); //to suppress the curl output 
$result = curl_exec($ch);curl_close ($ch);print_r($fbDetails);exit;


		///
		$result								=	array("result"=>true);*/
		//echo json_encode($result);
		echo "1";
		//echo $fbMessage['link'];
		break;
	case "tweet_auth":
	//echo "HHHH".$_SESSION['twt_result'];print_r($_SESSION);exit;
	
	
		if(isset($_SESSION["twt_result"])){
		
			if ($_SESSION["twt_result"] == '1'){
				$resultmsg 	= 'Tweet Posted: '.$_SESSION['tweetmsg'];
				$userId		=	$_SESSION['user']['userId'];
				if($referal->checkUser()){
					$updateArray	=	array("user_id"=>$userId,"twitter_oauth_token"=>$_SESSION['end_user_oauth_token'],"twitter_oauth_token_secret"=>$_SESSION['end_user_oauth_token_secret'],"twitter_status"=>"1");	
					$referal->updateTwitterStatus($updateArray);
				}
				else{
					$userToken			=	$referal->getUserToken();
					$insertDbFields	=	"`user_id`,`twitter_oauth_token`,`twitter_oauth_token_secret`,`referral_secret_token`,`twitter_status`";
					$insertDbValues	=	$userId.",'".$_SESSION['end_user_oauth_token']."','".$_SESSION['end_user_oauth_token_secret']."','".$userToken."',1";
					$referal->dbInsertSingle("jiwok_share_social",$insertDbFields,$insertDbValues);
				}
				$result	=	array("result"=>"Tweeted Successfully","status"=>true);
				$referal->saveShareData(2,$_SESSION['user']['userId'],$lanId);
			}
			else{
				echo $resultmsg = 'Could not post Tweet. Error: '.$httpCode.' Reason: '.$result->error;
				$result  = array("msg"=>$resultmsg);
			}
			echo json_encode($result);
		}
		else{
			$result  = array("msg"=>"session not set");
			echo json_encode($result);
		}exit;
		break;
	case "tweet_me":
	
	        $tweetDetails			=	$referal->twConnect();
		$oauthToken			=	$tweetDetails["twitter_oauth_token"];
		$oauthTokenSecret	=	$tweetDetails["twitter_oauth_token_secret"];
		$ction 					= 	new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET,$oauthToken,$oauthTokenSecret);
		$userToken				=	$referal->getUserToken();
		$makeUrl				=	"www.jiwok.com/index.php?utm_source=referal&utm_medium=referal&utm_term=referal&utm_campaign=referal&referrer=".$userToken."&media=tw&".md5(mt_rand());
		$snippedUrl				=	$referal->googleApiCall($makeUrl);
		$tweetmsg 				= 	str_replace($repSniperUrl,$snippedUrl,$mesTw);
		$result 					= 	$ction->post('statuses/update', array('status' => $tweetmsg));print_r($result);exit;
		$httpCode 				= 	$ction->http_code;
		if ($httpCode == 200){
			$userId	=	$_SESSION['user']['userId'];
			$result	=	array("result"=>"Tweeted Successfully","status"=>true);
			$referal->saveShareData(2,$_SESSION['user']['userId'],$lanId);
		}
		else{
			$updateArray	=	array("twitter_status"=>"2");	
			$referal->updateTwitterStatus($updateArray);
			$result			=	array("result"=>"Connect To Twitter","status"=>false);
		}
		echo json_encode($result);
		break;
	case "get_contacts":
		$userContacts		=	$referal->getContacts();
		if(is_array($userContacts)){
			foreach($userContacts as &$contacts){
				$contact[]	=	$contacts["referred_mail"];
			}
			$dataArr		=	array("email"=>$contact);
			echo json_encode($dataArr);
		}
		break;
	case "open_invite_contacts":
		$inviter		=	new openinviter();
		$inviter->startPlugin($_POST['provider']);
		if(($_POST['emailid']=="")||($_POST['password']=="")){
			$result	=	array("mailTxt"=>"","mailData"=>"<p class='err'>".$parObj->_getLabenames($arrayData,'refEmailMiss','name')."</p>");
			echo json_encode($result);
			exit;
		}
		if(!$inviter->login($_POST['emailid'],$_POST['password'])){
			$internal		=		$inviter->getInternalError();
			$result	=	array("mailTxt"=>"","mailData"=>"<p class='err'>".$parObj->_getLabenames($arrayData,'refLoginFailed','name')."</p>");
			echo json_encode($result);
			//echo $result['error']	=		"<p class='err'>".$parObj->_getLabenames($arrayData,'refLoginFailed','name')."</p>";
		}
		else{
			$contacts	=	$inviter->getMyContacts();
			$currentClass	=	"";
			$nextClass		=	"bg";
			$mailIdStr	=	"";
			if(is_array($contacts)){
				//$mailArray		=	$referal->getAllMails();
				$htmlPopup		=	'<table width="400" border="0" cellspacing="0" cellpadding="0" class="pop">';
				$net 				= $referal->saveContacts($contacts,$mailArray);
				foreach($contacts as $mailId => $name){
					
					$mailIdStr	.=	"#".$mailId;
					
					$htmlPopup	.=	'<tr class="'.$currentClass.'"><td>';
					if(0){
						$htmlPopup		.=	'<img src="../images/symbol.jpg" class="membImage"/>';
					}
					else{
						$htmlPopup		.=	'<label>
													<input type="checkbox" name="checkbox[]" id="checkbox" class="chkMail" checked="checked" />
												</label>';
					}
					if(filter_var($name, FILTER_VALIDATE_EMAIL)){
						$name	=	explode("@",$name);
						$name	=	$name[0];
					}
					$htmlPopup		.=	ucwords($name);
					$htmlPopup		.=	"</td>";
					$htmlPopup		.= "<td class='Idmail'>".$mailId."</td></tr>";
					$tmpHolder		=	$currentClass;
					$currentClass	=	$nextClass;
					$nextClass		=	$tmpHolder;
				}
				$htmlPopup		.=	"<tr class='nRes'><td colspan='2' align='center'>No Records Found</td></tr>";
				//echo $result["mailData"]	=	$htmlPopup;
				$result	=	array("mailTxt"=>$mailIdStr,"mailData"=>$htmlPopup);
				echo json_encode($result);
			}
			else{
				echo $result['error']	=	"<p class='err'>No Contacts Found</p>";
			}
		}
  		break;
  	case "send_mail":
		$messageTpl	=	$parObj->_getLabenames($arrayData,'reffMailContent','name');
		$messageTpl	=	str_replace("#N_L#","<br />",$messageTpl);
		$messageTpl	=	str_replace("{dot}",".",$messageTpl);

		$contacts 	= 	preg_split("/[\s,]+/", $mailIds,-1,PREG_SPLIT_NO_EMPTY);
		//$allMail		=	$referal->getAllMails();
		if(count($contacts)==0){
			$result["error"]	=	$parObj->_getLabenames($arrayData,'reffEptyMail','name');
		}
		foreach($contacts as $mailId){
			if(!filter_var($mailId, FILTER_VALIDATE_EMAIL)){
				$errMail[]	=	$mailId;
			}else{
				$validEmail[]	=	$mailId;
			}
		}
		$userName 	=	$referal->getUserName();
		$userToken	=	$referal->getUserToken();
		if(count($validEmail) > 0){ 
			$resultMail	=	$referal->sendmail($validEmail,$userName,$messageTpl,$userToken);//print_r($resultMail);exit;
		}
		if(isset($dupMail) || isset($errMail)){
			if(isset($errMail)){
				$MailIds		= 	implode(",",$errMail);	
				$Message	=	$MailIds.$parObj->_getLabenames($arrayData,'reffvalidMailTxt','name');
			}
			if(isset($dupMail)){
				$MailIds		= 	implode(",",$dupMail);	
				$Message	.=	$MailIds.$parObj->_getLabenames($arrayData,'reffAcccTxt','name');
			}
			$result["error"]	=	$Message;
		}
		
		if(is_array($resultMail)){
			foreach($resultMail as $mailStatus){
				if($mailStatus["status"]){
					$sucMail[]	=	$mailStatus["to"];
				}
				else{
					$failMail[]	=	$mailStatus["to"];
				}
			}
			if(isset($sucMail) && !empty($sucMail)){
				$messageFirst	=	implode(",",$sucMail);
				$messageFirst	=	str_replace("#MAILIDS#",$messageFirst,$parObj->_getLabenames($arrayData,'refMailInvScs','name'));
			}
			if(isset($failMail) && !empty($failMail)){
				$messageLast	=	implode(",",$failMail);
				$messageLast	=	"Mail sending failed for ".$messageLast;
			}
		}
		if(isset($messageLast)){
			if($result["error"] != "")$result["error"]	.= ";";
			$result["error"]	.=	$messageLast;	
		}
		if(isset($messageFirst)){
			$result["success"]	.=	$messageFirst;	
		}
		echo json_encode($result);
		break;
	default:
		echo "Not A Valid option";
		break;
}
?>
