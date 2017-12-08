<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

include_once('includeconfig.php');
include_once("includes/classes/class.member.php");
include_once('includes/classes/class.CMS.php');
include_once("includes/classes/class.programs.php");
include_once("giftpaybox.php");

$curr = new CurrencyConverter(DB_HOST,DB_USER,DB_PASSWD,DB_NAME,'jiwok_currency');

$objGen   	=	new General();
$parObj 	=   new Contents('gift_payment.php');
$objMem		= 	new Member($lanId);
$objCMS		= 	new CMS($lanId);
$objPgm     = 	new Programs($lanId);

	// for reproducing into orginal alphabets

function normalze($string)
	{	
		$fReplace2 	= array('"','"','');	
		$trans 	 	= array('�','�','�');	
		$trans2		= array_combine($trans, $fReplace2);	
		$string 	= utf8_decode($string);	
		return utf8_encode(strtr($string,$trans2));	
	}

if(isset($_SESSION['login']['userId']))
	{
		$userId		=	$_SESSION['login']['userId']; //if user is coming from the registration page.
	}

elseif(isset($_SESSION['user']['userId']))
	{
		$userId		=	$_SESSION['user']['userId']; //get user id from the session variable/if user is coming from the UserArea Page.
	}

if($lanId=="") $lanId=1;

 // get title form the request
$contentTitle		=	'PAYMENTCONDITION';

//get contents according to the title
$contents 			= $objCMS->_getContent($contentTitle,$lanId);

//collecting data from the xml for the static contents
$returnData			= 	$parObj->_getTagcontents($xmlPath,'giftpayment','label');
$arrayData			= 	$returnData['general'];

//get language name
$lanName			=	$objMem->_getLanName($lanId);
		//$payDetails = $objPgm->_getUserPaymentTemp($userId);

		//	echo "1";

		///for backend page updation

		//$reffPara			= trim(stripslashes($payDetails['pay_id']));
		
if($_SESSION['gift_type'])
{
	$planQuery  	=	"select * from jiwok_payment_plan where plan_status=1 and  plan_currency='".$lanId."' AND plan_duration=".$_SESSION['gift_type'];
	$planResult		= 	$GLOBALS['db']->getRow($planQuery,DB_FETCHMODE_ASSOC);
}

		if($_SESSION['giftsubscription'] == 'TRUE')

		{

		$_SESSION['giftsubscription'] = 'TRUE';

		$message=addslashes($_SESSION['frend_msg']);

		$query1="INSERT INTO gift_member (purchaseid,firstname,lastname,email,code,status,friendname,friendemail,friendmessage) VALUES ('','$_SESSION[user_name]','$_SESSION[user_lname]','$_SESSION[user_email]','0','$_SESSION[gift_friend]','$_SESSION[friend_name]','$_SESSION[friend_email]','$message')";

		
		$res	= $GLOBALS['db']->query($query1);

		$query2="select max(purchaseid) as max from gift_member where firstname='$_SESSION[user_name]' and email='$_SESSION[user_email]'";

		$purchase_row	= $GLOBALS['db']->getRow($query2,DB_FETCHMODE_ASSOC);

		$purchase_id=$purchase_row['max'];

	    }	
		$reffPara			=	'0';
		if($_SESSION['gift_type']){

		$reffPara			=	$reffPara.','.$_SESSION['gift_type'];

		}else{

		$reffPara			=	$reffPara.','.'0';

		}
		$reffPara			=	$reffPara.','.$_SESSION['gift_friend'];
		if($purchase_id != "" && $purchase_id != 0){

		$reffPara			=	$reffPara.','.$purchase_id; 

		}else{

		$reffPara			=	$reffPara.','.'0';

		}

		$reffPara			=	$reffPara.','.$_SESSION['user_name'];

		$reffPara			=	$reffPara.','.$_SESSION['user_email'];

		$reffPara			=	$reffPara.','.$_SESSION['friend_name'];

		$reffPara			=	$reffPara.','.$_SESSION['friend_email'];

		$reffPara			=	$reffPara.','.'0';

		//$reffPara			=	$reffPara.','.$_SESSION['frend_msg'];

        $reffPara.=	",gift";

		$reffPara.=",".$lanId;

		$reffPara			=	$reffPara.','.$_SESSION['user_lname']; 
	 //$fee=$_SESSION['memberfee'];

		//$price_tmp = str_replace('.', ',',$_SESSION['payment']['payFee']);

		 //$fee=$_SESSION['memberfee'];

		$membqry="SELECT membership_fee,membership_feedollar,reusable_membership_fee,reusable_membership_feedollar FROM settings";

        $membreslt = $GLOBALS['db']->getAll($membqry, DB_FETCHMODE_ASSOC);

        if($lanId ==1) {//if english

			if ($_SESSION['reusable']=="FX") {//if reusable is selected

				$feedoll = $membreslt[0]['reusable_membership_feedollar'];

			}

			else {

				$feedoll = $membreslt[0]['membership_feedollar'];

			}

			if($_SESSION['gift_type'])

			{

				$passme 			=	$_SESSION['gift_type'];

				//$price_tmp_doll		=	$feedoll * $passme;
				$price_tmp_doll		=	$planResult[plan_amount];

			}

		}

		//else {

			if ($_SESSION['reusable']=="FX") {//if reusable is selected

				$fee = $membreslt[0]['reusable_membership_fee'];

			}

			else {

				$fee = $membreslt[0]['membership_fee'];

			}

			if($_SESSION['gift_type']) {

				 $passme = $_SESSION['gift_type'];

				 //$price_tmp = $fee * $passme;
				 $price_tmp =	$planResult[plan_amount];

			}

		//}

		//$price_tmp = str_replace('.', ',',$_SESSION['payment']['payFee']);
  //echo  $price_tmp;
		//$_SESSION['payment']['payFee'] = trim(stripslashes($payDetails['payFee']));

		$_SESSION['payment']['payFee'] = $price_tmp;

		$_SESSION['payment']['user_email'] = $_SESSION['user_email'];

		//echo $_SESSION['user']['user_email']." | ".$payDetails['user_email'];

/*		if (isset($_SESSION['user']['user_email'])) {

			$_SESSION['payment']['user_email'] = $_SESSION['user']['user_email'];

		} else {

			$_SESSION['payment']['user_email'] = trim(stripslashes($payDetails['user_email']));

		}*/
?>
<?php include('header.php');?>
<?php include('menu.php');?>
<div id="container">
  <div id="wraper_inner">
    <div class="breadcrumbs">
      <ul>
        <li>
          <?=$parObj->_getLabenames($arrayData,'newPgeTxt','name');?> : 
        </li>
        <li><a href="<?=ROOT_JWPATH?>">
          <?=$parObj->_getLabenames($arrayData,'newHmeTxt','name');?>
          </a></li>
        <li>></li>
        <li><a class="select">
          <?=$parObj->_getLabenames($arrayData,'payment','name');?>
          </a></li>
      </ul>
    </div>
    <div class="heading"><span class="name">
      <?=$parObj->_getLabenames($arrayData,'payment','name');?>
      </span> <span class="date"><strong>&gt; </strong><a href="<?=$backButtonLink?>" class="white">
      <?=$parObj->_getLabenames($arrayData,'newBackTxt','name');?>
      </a></span></div>
    <div class="col-2">
      <div class="left mar-rt">
        <p class="gry"><b>
          <?=$parObj->_getLabenames($arrayData,'forprice','name');?>
          :
          <?php
			if($lanId == 1)
			{//if English
				echo str_replace(".",".",  number_format($price_tmp_doll,2))." dollars ";//(".str_replace(".",",",  number_format($price_tmp,2))." euros )
		 	}
		 	else if($lanId == 5)
			{
		   		echo str_replace(".",",",  number_format($price_tmp,2))." zlotys";
				//echo str_replace(".",",",  number_format($price_tmp,2))." euros (".str_replace(",",".",  number_format($price_tmp_doll,2))." dollars )";
			}
		 	else
			{
		   		echo str_replace(".",",",  number_format($price_tmp,2))." euros";
				//echo str_replace(".",",",  number_format($price_tmp,2))." euros (".str_replace(",",".",  number_format($price_tmp_doll,2))." dollars )";
			}?>
          </b></p>
        <p align="right"><a href="javascript:void(0)" class="blu" onclick="return unsubscribe();">
          [ <?=$parObj->_getLabenames($arrayData,'condition1','name');?> ]
          </a></p>
        <p class="gry"><b>
          <?=$parObj->_getLabenames($arrayData,'service','name');?>
          </b></p>
        <ul id="tree">
          <li><b>
            <?=$parObj->_getLabenames($arrayData,'fname','name');?>
            </b> :
            <?=$_SESSION['user_name'];?>
          </li>
          <li><b>
            <?=$parObj->_getLabenames($arrayData,'lname','name');?>
            </b> :
            <?=$_SESSION['user_lname'];?>
          </li>
          <li><b>
            <?=$parObj->_getLabenames($arrayData,'youmail','name');?>
            </b> :
            <?=$_SESSION['user_email'];?>
          </li>
          <li><b>
            <?=$parObj->_getLabenames($arrayData,'gtype','name');?>
            </b> :
            <?=$passme;?>
            <?=$parObj->_getLabenames($arrayData,'months','name');?>
          </li>
          <?php 
		  	if($_SESSION['giftregistration']['first_name'] != '') 
				{ 
		  ?>
          <li><b>
            <?=$parObj->_getLabenames($arrayData,'frndfname','name');?>
            </b> :
            <?=stripslashes($_SESSION['giftregistration']['first_name']);?>
          </li>
          <li><b>
            <?=$parObj->_getLabenames($arrayData,'frndlname','name');?>
            </b> :
            <?=stripslashes($_SESSION['giftregistration']['last_name']);?>
          </li>
          <li><b>
            <?=$parObj->_getLabenames($arrayData,'frndmail','name');?>
            </b> :
            <?=$_SESSION['friend_email'];?>
          </li>
          <li><b>
            <?=$parObj->_getLabenames($arrayData,'frndmsg','name');?>
            </b> :<br />
            <?=str_replace("\n","<br />",$_SESSION['frend_msg']);?>
          </li>
          <?php 
				}
		?>
        </ul>
        </li>
        </ul>
      </div>
      <div class="right">
        <div class="hom_tabs lt-n-rt" style="margin-left:80px;">
          <div class="name">
            <div class="La_presse"></div>
            <?=$parObj->_getLabenames($arrayData,'payment','name');?>
          </div>
          <div class="content">
            <div class="logos"> <img src="images/e_transactions.png" alt="e-Transactions" class="eTransactionsLogo" /> <br>
              <img src="images/var_logos.jpg" alt="Jiwok" class="varLogosPsn" /></div>
            <br />
			<div align="center">
            <?php
			if($lanId == 1) 
			{
				$amount				=	$price_tmp_doll;
				$client_email		=	$_SESSION['payment']['user_email']; 
				$reffPara			=	$reffPara.','.'Dollar'; // order reference for euro payment
				if ($_SESSION['reusable']=="FX")
					$reffPara1 =  $reffPara.','.'FX';
				else
					$reffPara1 =  $reffPara.','.'0';
				$order_reference	=	base64_encode($reffPara1);
				$language_name		=	"english";
				$buttonValue		=	$parObj->_getLabenames($arrayData,'pay','name');
				echo getGiftPayBoxForm($amount, $client_email, $order_reference, $language_name,$buttonValue);
		  	} 
			else if($lanId	==	5)
			{
				$amount=$price_tmp;
				$client_email		=	$_SESSION['payment']['user_email'];
				$reffPara			=	$reffPara.','.'zlotys'; // order reference for euro payment
				if ($_SESSION['reusable']=="FX")
					$reffPara1 =  $reffPara.','.'FX';
				else
					$reffPara1 =  $reffPara.','.'0';
				$order_reference	=	base64_encode($reffPara1);
				$language_name		=	"polish";
				$buttonValue		=	$parObj->_getLabenames($arrayData,'pay','name');
				echo getGiftPayBoxForm($amount, $client_email, $order_reference, $language_name,$buttonValue);
		    }
			else 
			{
				$amount=$price_tmp;
				$client_email		=	$_SESSION['payment']['user_email'];
				$reffPara			=	$reffPara.','.'Euro'; // order reference for euro payment
				if ($_SESSION['reusable']=="FX")
					$reffPara1 =  $reffPara.','.'FX';
				else
					$reffPara1 =  $reffPara.','.'0';
				$order_reference	=	base64_encode($reffPara1);
				$language_name		=	"french";
				$buttonValue		=	$parObj->_getLabenames($arrayData,'pay','name');
				echo getGiftPayBoxForm($amount, $client_email, $order_reference, $language_name,$buttonValue);
		    }
		    ?></div>
            <div class="clear"></div>
          </div>
          <div class="botm"><img src="images/hom-tab_botm.jpg" alt="Jiwok" /></div>
        </div>
      </div>
      <?php include('payment_bottom.php');?>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
  </div>
</div>
<?php include('footer.php');?>
<script language="javascript" type="text/javascript">
function unsubscribe()
	{
		document.getElementById('unsubscribePgm').style.display='block';
	}
function hideUnsubscribe(styleid)
	{
		document.getElementById(styleid).style.display='none';
	}
GetListFromCrowdSound();

</script>
<!--popup disply-->
<div class="popup" id="unsubscribePgm" style="top: 100px; left: 290px; position:fixed; display:none; z-index:10;">
  <div><img src="images/pop-top.png" alt="jiwok" /></div>
  <div class="inner"> <a id="fancybox-close" onclick="hideUnsubscribe('unsubscribePgm');" title="close" style="display: inline;"></a>
    <h2>
      <?=$parObj->_getLabenames($arrayData,'paymentPopupTitle','name');?>
    </h2>
    <div id="scroll" style="height:250px; overflow:auto;">
      <table width="100%" border="0" cellspacing="2" cellpadding="0" class="content">
        <tr>
        <?php
		if($lanId	!=	5)
		{?>
          	<td colspan="2" align="center"><?=normalze(html_entity_decode($parObj->_getLabenames($arrayData,'paymentPopupPara1','name'), ENT_QUOTES, "utf-8"));?></td>
       	<?php
		}
		else
		{?>
        	<td colspan="2" align="center"><?=html_entity_decode($parObj->_getLabenames($arrayData,'paymentPopupPara1','name'), ENT_QUOTES, "utf-8");?></td>
        <?php
		}?>        
        </tr>
        <tr>
          <td colspan="2" align="center"><?=normalze(html_entity_decode($parObj->_getLabenames($arrayData,'paymentPopupPara2','name'), ENT_QUOTES, "utf-8"));?></td>
        </tr>
        <tr>
          <td colspan="2" align="center"><?=normalze(html_entity_decode($parObj->_getLabenames($arrayData,'paymentPopupPara3','name'), ENT_QUOTES, "utf-8"));?></td>
        </tr>
      </table>
    </div>
    <div class="clear"></div>
      </div>
  <div><img src="images/pop-btm.png" alt="jiwok" /></div>
</div>
<!--popup disply ends here-->
