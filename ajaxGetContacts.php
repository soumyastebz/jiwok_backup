<?php

$mailUsername	=	"";
if($_REQUEST["mailUsername"]!=""){
	$mailUsername	=	$_REQUEST["mailUsername"];
}

$mailPassword	=	"";
if($_REQUEST["mailPassword"]!=""){
	$mailPassword	=	$_REQUEST["mailPassword"];
}

/*ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);*/

require_once 'includeconfig.php';
require_once 'includes/classes/Referrel/class.referal.php';
include('tools/OpenInviter/openinviter.php');

$referal		=	new referal();
$inviter=new OpenInviter();
$oi_services=$inviter->getPlugins();
$parObj 		= new Contents('referrel_system.php');

$returnData		= $parObj->_getTagcontents($xmlPath,'referralSectionNew','label');
$arrayData		= $returnData['general'];

$plugType='email';

$provider=$inviter->getPluginByDomain($mailUsername);
if (!empty($provider)){
	if(isset($oi_services['email'][$provider])) 
		$plugType='email';
	elseif(isset($oi_services['social'][$provider])) 
		$plugType='social';
	else $plugType='';
}else{
	echo $result['error']	=		"<p class='err'>".$parObj->_getLabenames($arrayData,'refEmailMiss','name')."</p>";
	exit;
}
$mailIdStr	=	"";
	
if (1){			
	$inviter->startPlugin($provider);
	$internal=$inviter->getInternalError();
	if ($internal)
		$ers['inviter']=$internal;
	elseif (!$inviter->login($mailUsername,$mailPassword)){
		$internal=$inviter->getInternalError();
		echo $result['error']	=		"<p class='err'>".$parObj->_getLabenames($arrayData,'refLoginFailed','name')."</p>";
		exit;
	}elseif (false===$contacts=$inviter->getMyContacts()){
		echo $result['error']	=		"<p class='err'>Unable to get contacts !</p>";
		exit;
	}else{
		$import_ok=true;
		$step='send_invites';
		$_POST['oi_session_id']=$inviter->plugin->getSessionID();
		$_POST['message_box']='';
	}
	if (1){
		$inviter->showContacts();
		//$mailArray		=	$referal->getAllMails();
		$htmlPopup		=	'<table width="400" border="0" cellspacing="0" cellpadding="0" class="pop">';
		//$net 				= $referal->saveContacts($contacts,$mailArray);
		
		$commonCnt	=	array_intersect($contacts, $mailArray);
		
		foreach($contacts as $mailId => $name){
			
			$mailIdStr	.=	"#".$mailId;
			
			$htmlPopup	.=	'<tr class="'.$currentClass.'"><td>';
			$htmlPopup		.=	'<label>
										<input type="checkbox" name="checkbox[]" id="checkbox" class="chkMail" checked="checked" />
									</label>';

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
	
		/*foreach($contacts as $mailId => $name){
			$htmlPopup	.=	'<tr class="'.$currentClass.'"><td>';
			if(in_array($mailId,$commonCnt)){
				$htmlPopup		.=	'<img src="../images/symbol.jpg" class="membImage"/>';
			}
			else{
				$htmlPopup		.=	'<label>
										<input type="checkbox" name="checkbox[]" id="checkbox" class="chkMail" />
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
		}*/
		$result	=	array("mailTxt"=>$mailIdStr,"mailData"=>$htmlPopup);
		echo json_encode($result);
	}
}
exit;	
?>