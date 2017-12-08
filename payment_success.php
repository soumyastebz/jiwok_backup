<?php 
ob_start();
session_start();
$url	=	"http://beta.jiwok.com/";
$lanfl	=	"0";
if($_SESSION['language']['langId']=='1')
{
	$url.="en/";
	$lanfl="1";
}
unset($_SESSION['payment']);
unset($_SESSION['subscription']);

if($lanfl!='0')
{

     $pattern = "/(\/)(en)(\/)/";
	 $url_execute=$_SERVER['REQUEST_URI'];
	 $check_val=preg_match($pattern,$url_execute);
	 if(!$check_val)
	 {
	  header("Location:".$url."payment_success.php?msg=".$_REQUEST['msg']);exit;  
	 }

}

include_once('includeconfig.php');
include_once('regDetail.php');$parObj 	=   new Contents('payment.php');
//collecting data from the xml for the static contents
		$returnConfmData			= $parObj->_getTagcontents($xmlPath,'payment','messages');
		$arrayConfrmData				= $returnConfmData['confirmMessage'];
		$array_error_messages		= $returnConfmData['errorMessage'];
//collecting data from the xml for the static contents
		$returnData					= $parObj->_getTagcontents($xmlPath,'payment','label');
		$arrayData					= $returnData['general'];

		

		if(isset($_SESSION['login']['userId'])){
$userId		=	$_SESSION['login']['userId']; //if user is coming from the registration page.
}

elseif(isset($_SESSION['user']['userId'])){
$userId		=	$_SESSION['user']['userId']; //get user id from the session variable/if user is coming from the UserArea Page.

}
		if(isset($_SESSION['login']['user_email'])){
$useremail	=	$_SESSION['login']['user_email']; //if user is coming from the registration page.
}

elseif(isset($_SESSION['user']['user_email'])){
$useremail	=	$_SESSION['user']['user_email']; //get user id from the session variable/if user is coming from the UserArea Page.

}
if($_REQUEST['msg']!=''){
		$errorMsg	=	base64_decode($_REQUEST['msg']);
		
		switch($errorMsg){
		//for the faulier in the login from the 
		case 1:
		      $msg =  $parObj->_getLabenames($arrayConfrmData,'cnfm1','name');
			  break;
	    //for the my account section		  
		case 2:	 
			  $msg =  $parObj->_getLabenames($arrayConfrmData,'cnfm2','name');
			  break; 
		//for the my account section		  
		case 3:	 
			  $msg = $parObj->_getLabenames($arrayConfrmData,'cnfm3','name');
			  break; 
			  //for the my account section		  
		case 4:	 
			  $msg = $parObj->_getLabenames($arrayConfrmData,'cnfm4','name');
			  break; 			
		case 'err6':
			  $msg = $parObj->_getLabenames($array_error_messages, 'err6', 'name');
			  break;
		case 'err7':
			  $msg = $parObj->_getLabenames($array_error_messages, 'err7', 'name');
			  break;
		case 'err8':
			  $msg = $parObj->_getLabenames($array_error_messages, 'err8', 'name');
			  break;
		case 'err21':
			  $msg = $parObj->_getLabenames($array_error_messages, 'err21', 'name');
			  break;
		default: {//For displaying errors during payment renewal. Encoded error code of the error is in $_GET['msg'] 
					$msg = $parObj->_getLabenames($array_error_messages, $errorMsg, 'name');
					if (trim(msg)=="")  $msg = $parObj->_getLabenames($array_error_messages, 'err0', 'name');
				}
		}
	}

	

	statusRecord($useremail,'4th step over','payment_success.php',$userId,'0','11');
?>
<?php include('header.php');?>
<!--original
<div id="container">
  <div id="wraper_inner">
    <div class="breadcrumbs">
      <ul>
        <li>
          <?=$parObj->_getLabenames($arrayData,'newPgeTxt','name');?>
          : </li>
        <li><a href="<?=ROOT_JWPATH?>">
          <?=$parObj->_getLabenames($arrayData,'newHmeTxt','name');?>
          </a></li>
        <li>></li>
        <li><a class="select">
          <?=utf8_encode($parObj->_getLabenames($arrayData,'payment','name'));?>
          </a></li>
      </ul>
    </div>
	<div class="heading"><span class="name">
      <?=$parObj->_getLabenames($arrayData,'payment','name');?>
      </span> </div>
    <p>
    <div class="left_col_payment" style="height: 200px;"> <span class="contentHolderPaymentInfo">
      <?php if($msg != "") echo  html_entity_decode($msg);?>
      <br />
      <br />
      <a href="myprofile.php#renew" class="blu">
      <?=$parObj->_getLabenames($arrayData,'clickhere','name')?>
      </a>
      <?=$parObj->_getLabenames($arrayData,'gobacktoprofile','name')?>
      | <a href="payment_renew.php" class="blu">
      <?=$parObj->_getLabenames($arrayData,'clickhere','name')?>
      </a>
      <?=$parObj->_getLabenames($arrayData,'continuepayment','name')?>
      </span> </div>
  </div>
  <div class="clear"></div>
</div>
</div>
original ends-->

  <div class="frame_inner" id="container">
  <div id="wraper_inner">
    <div class="row-1">
         <div class="title">
          <ul class="bredcrumbs">
              <li>
          <?=$parObj->_getLabenames($arrayData,'newPgeTxt','name');?>
          : </li>
        <li><a href="<?=ROOT_JWPATH?>">
          <?=$parObj->_getLabenames($arrayData,'newHmeTxt','name');?>
          </a></li>
        <li>></li>
        <li><a class="select">
          <?=utf8_encode($parObj->_getLabenames($arrayData,'payment','name'));?>
          </a></li>
           </ul>
           <p class="Q"><?=$parObj->_getLabenames($arrayData,'payment','name');?></p>
      </div>
         </div>
       <div class="clear"></div>
       <div class="partners_JW">
          <p class="TX_ylw" style="color: #83C0EA;"><?php if($msg != "") echo  html_entity_decode($msg);?>
  
      <a href="myprofile.php#renew" style="color: #EF5411;">
      <?=$parObj->_getLabenames($arrayData,'clickhere','name')?>
      </a>
      <?=$parObj->_getLabenames($arrayData,'gobacktoprofile','name')?>
      | <a href="payment_renew.php" style="color: #EF5411;">
      <?=$parObj->_getLabenames($arrayData,'clickhere','name')?>
      </a>
      <?=$parObj->_getLabenames($arrayData,'continuepayment','name')?>
      </p>
     
       </div>  
       </div>
     </div>
<?php include('footer.php');?>
<script type="text/javascript"> GetListFromCrowdSound();</script>
