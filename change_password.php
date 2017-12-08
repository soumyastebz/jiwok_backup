<? 
session_start();
include_once('includeconfig.php');
include_once("includes/classes/class.programs.php");
include_once('./admin/forumpass.php');

if($lanId=="")
     $lanId=1;
$errorMsg 		= '';	 
$userId 		= $_SESSION['user']['userId'];	
$objGen     	= new General();
$objPgm     	= new Programs($lanId);
$objAction		= 	new DbAction();
$objPass 		= 	new ForumPass();
$parObj 		= new Contents('change_password.php');
$headingData	= $parObj->_getTagcontents($xmlPath,'myprofile','pageHeading');
$returnData		= $parObj->_getTagcontents($xmlPath,'myprofile','label');
$arrayData		= $returnData['general'];

$oldpass 		= stripslashes(trim(utf8_decode($_REQUEST['oldpass'])));
$newpass		= stripslashes(trim(utf8_decode($_REQUEST['newpass'])));
$confirmpass 	= stripslashes(trim(utf8_decode($_REQUEST['confirmpass'])));
$data 	  	 	= $objPgm->_getUserDetails($userId);
$pass 			= base64_decode(trim(stripslashes($data['user_password'])));
$mdpassword		= addslashes(base64_encode(md5(utf8_decode($_REQUEST['oldpass']))));

$error='';
if($oldpass=="")
{
$error.= $parObj->_getLabenames($arrayData,'changepasserror1','name')."<br>"; // old password required
}
if($newpass=="")
{
$error .= $parObj->_getLabenames($arrayData,'changepasserror2','name')."<br>";//New password required
}
if($confirmpass=="")
{
$error.= $parObj->_getLabenames($arrayData,'changepasserror3','name')."<br>";//Password confirm required
}
if($newpass!="" && strlen($newpass)<2)
{
$error.= $parObj->_getLabenames($arrayData,'changepasserror4','name')."<br>";//New password should atleast tbe 2 characters! 
}
if($confirmpass!="" && strlen($confirmpass)<2)
{
$error.= $parObj->_getLabenames($arrayData,'changepasserror5','name')."<br>";//Confirm password should atleast tbe 2 characters!
}
if($newpass!="" && $confirmpass!="" && ($newpass!=$confirmpass))
{
  $error.= $parObj->_getLabenames($arrayData,'changepasserror6','name')."<br>"; //Passwords doesn't match
} 
if($oldpass!="" && $pass!=$oldpass && $mdpassword!=$data['user_password'])
{
  $error.= $parObj->_getLabenames($arrayData,'changepasserror7','name')."<br>";//Invalid old password
} 
if($error!="")
{
  $display = trim($error);
}
else
{
	$res = $objPgm->_updatePassword($newpass,$userId);
	$forum_userid = $objPgm->_getForumTicketId('user_id','username','forum_users',trim(stripslashes($data['user_email'])));
	$forumUserId = trim($forum_userid['user_id']);
	$getpass = $objPass->phpbb_hash($newpass); // new password
	$forum_upd = $objPgm->_updateForumTicketPass('forum_users','user_password',$getpass,'user_id',$forumUserId);
	
	$ticket_userid = $objPgm->_getForumTicketId('client_id','email','hdp_clients',trim(stripslashes($data['user_email'])));
	$ticketUserId = trim($ticket_userid['client_id']);
	$ticket_pass = md5($newpass); 
	$ticket_upd = $objPgm->_updateForumTicketPass('hdp_clients','pass_word',$ticket_pass,'client_id',$ticketUserId );
	$display = "success";

}
echo $display;

?>
