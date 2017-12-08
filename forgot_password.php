<?php
session_start();
	include_once('includeconfig.php');
	include_once('includes/classes/class.massmail.php')	;
	include_once("includes/classes/class.programs.php");
	include_once('./admin/forumpass.php');
	include_once "includes/classes/class.sendgrid.php";//****
    $sendg = new sendgrid();
	if($lanId=="")
		 $lanId=1;
	$objDb			= new DbAction();
	$objGen     	= new General();
 	$parObj 		= new Contents('forgot_password.php');
	$objMassmail	= new Massmail($lanId);
	$objPgm     	= new Programs($lanId);
	$objPass 		= new ForumPass();
	//collecting data from the xml for the static contents
	$returnData		= $parObj->_getTagcontents($xmlPath,'forgotpassword','label');
	$arrayData		= $returnData['general'];
	//collecting the error messaage from the xl file
    $errorReturn	=$parObj->_getTagcontents($xmlPath,'forgotpassword','messages');
    $errorData		=$errorReturn['errorMessage'];
	if($_POST['send']){
		
		if(trim($_POST['email']) == "")
                $errorMsg = $parObj->_getLabenames($errorData,'noemail','name');
            else if(!$objGen->_validate_email($_POST['email']))
                $errorMsg = $parObj->_getLabenames($errorData,'erremail','name');
	}
		//for sending the user name an
	   if($_POST['send'] && count($errorMsg) == 0){
		   
	   $sql = "SELECT  user_id,user_alt_email,user_password,user_email,user_fname FROM user_master WHERE user_alt_email='".trim($_POST['email'])."' OR paybox_email='".trim($_POST['email'])."' OR email_history='".trim($_POST['email'])."' OR user_username='".trim($_POST['email'])."' ";
	   $userDetails = $objDb->_getList($sql);
	 	  if(count($userDetails) == 1){
	     $md5Password	=	false;
		 $forUserId	=	$userDetails[0]['user_id'];
		 $login = $userDetails[0]['user_alt_email'];
		 $password = base64_decode($userDetails[0]['user_password']);
		 $userFname	=	$userDetails[0]['user_fname'];
		
			$emailTo = array($_POST['email'] => '');
			$mailArray      =   $objMassmail->_fetchSettingsEmail();
			//For checking the user came through bulk registration.
		 	//If yes the user haveing md5 password
		 	//So we need to reset this password with a random password
		 	//Because we can't reverse engineer a md5 encrypted string	
			if((bool)preg_match('/^[a-f0-9]{32}$/', $password))
			{				
				$md5Password	=	true;
				$password		=	$objGen->createRandomPassword();				
				//echo $password;die();
				$res 			=	$objPgm->_updatePassword($password,$userDetails[0]['user_id']);
				$forum_userid 	=	$objPgm->_getForumTicketId('user_id','username','forum_users',trim(stripslashes($userDetails[0]['user_email'])));
				$forumUserId	=	trim($forum_userid['user_id']);
				$getpass		=	$objPass->phpbb_hash($password); // new password
				$forum_upd		=	$objPgm->_updateForumTicketPass('forum_users','user_password',$getpass,'user_id',$forumUserId);
	
				$ticket_userid	=	$objPgm->_getForumTicketId('client_id','email','ticket_clients',trim(stripslashes($userDetails[0]['user_email'])));
				$ticketUserId	=	trim($ticket_userid['client_id']);
				$ticket_pass	=	md5($password); 
				$ticket_upd		=	$objPgm->_updateForumTicketPass('ticket_clients','pass_word',$ticket_pass,'client_id',$ticketUserId );				
			}	
		   $return_email 	= $mailArray['RETURN_MAIL'];
		   $bounce_email 	= $mailArray['BOUNCE_MAIL'];
		   $subject = $parObj->_getLabenames($arrayData,'subject','name');
		   $siteUrl = 'http://www.jiwok.com/index.php';
		   
		   $mailTpl	=	$parObj->_getLabenames($arrayData,'mailTpl','name');
		   
			if($md5Password)
			{
				$msgTxt = "\n".$parObj->_getLabenames($arrayData,'msg','name').",";
				$msgTxt.= $parObj->_getLabenames($arrayData,'md5Msg','name');
			}
			else
			{
				$msgTxt = "\n".$parObj->_getLabenames($arrayData,'msg','name').",";	
			}
			
			 $mailTpl	=	str_replace("#MAILTXT#",$msgTxt,$mailTpl);
			 
			 $mailTpl	=	str_replace("#EMALID#",$login,$mailTpl);
			 $mailTpl	=	str_replace("#PASSWORD#",$password,$mailTpl);
			 $mailTpl	=	str_replace("#JIWOKURL#", "http://www.jiwok.com/index.php?mode=autologin&authid=".base64_encode($forUserId),$mailTpl);//gg changed
			 $mailTpl	=	str_replace("#CONTACTUS#","http://www.jiwok.com/contents.php?title=contactusnew&mode=autologin&authid=".base64_encode($forUserId),$mailTpl);//gg changed
			 $mailTpl	=	str_replace("#NL#","\n",$mailTpl);
			 $mailTpl	=	str_replace("#FIRSTNAME#",$userFname,$mailTpl);
			 $msg	=	nl2br($mailTpl);
			 $msg	=	str_replace("#MESSAGECNT#",$msg,$objGen->mailTpl);
			 $msg	=	str_replace("#FOOTERTXT#",$objGen->mailFooter[$lanId],$msg);
			 $from = array('coach@jiwok.com' => 'Jiwok Coach');
			
			if($lanId =="2")
			{
			$msg = utf8_decode($msg);
			$sendg->send($subject,$from,$emailTo,$msg,$text='',$marathon='',$iso=1);	
			}
			else
			{
				$sendg->send($subject,$from,$emailTo,$msg);
			}
		   $errorMsg = $parObj->_getLabenames($arrayData,'sentmessage','name');
		}else{
		
		$errorMsg = $parObj->_getLabenames($arrayData,'nouser','name');
		}
	}
?>
<?php include("header.php"); ?>

 <section class="banner-static">
	 
	 <div class="bred-hovr second">
          <ul class="bredcrumbs">
	      <div class="breadcrumbs">
		  <ul>
			<li>
			   <?=$parObj->_getLabenames($arrayData,'searchPath','name');?></li>
			<li>
			   <a href="<?=ROOT_JWPATH?>index.php">
               <?=$parObj->_getLabenames($arrayData,'homeName','name');?>
               </a>
             </li>
			<li>></li>
			<li><a href="#" class="select">
                <?=$parObj->_getLabenames($arrayData,'forgot','name');?>
                </a>
            </li>
		  </ul>
		</div>
		</ul>
       </div>
	  
	   <div class="bnr-content" style="position:relative;">
       <div class="frame slider-first">
       <div class="callbacks_container">
<!--
        <ul class="rslides" id="slider4">
-->
        <ul class="rslides callbacks callbacks1" id="slider4">
          <li><img src="<?=ROOT_FOLDER?>images/contact_new.jpg" alt="Slide 01"> </li>
		</ul>
       </div>
       </div>
       <div class="heading4JW"><p><?=$parObj->_getLabenames($arrayData,'forgot','name');?></p></div> 
         <form name="form_fp" action="" method="post">
<div class="heading5">
	<div>
	<?=$parObj->_getLabenames($arrayData,'forgotDetail','name');?>
   </div>
    <?php
   if($errorMsg){?>
     <div class="error_message" style="text-align: justify;" ><?php print $errorMsg;?></div>
     <?php }?>
     
     <div class="login">
		<input type="text" placeholder="<?=$parObj->_getLabenames($arrayData,'email','name');?>" onfocus="value=''" value="" class="field" name="email" id="email">
     </div>
     <div>
      <input type="submit" name="send" value="<?=$parObj->_getLabenames($arrayData,'button','name');?>" class="btn_orng">
   </div>
     </div> 
  </form>
</div>
     </section>
<?php include("footer.php"); ?>
