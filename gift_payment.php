<?php
session_start();
//error_reporting(E_ALL ^ E_NOTICE);

include_once('includeconfig.php');
include_once("includes/classes/class.member.php");
include_once('includes/classes/class.CMS.php');
include_once("includes/classes/class.programs.php");
//include_once("giftpaybox.php");

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
 <div class="frame_inner">
    <div class="row-1">
		 <ul class="bredcrumbs">
            <li><?=$parObj->_getLabenames($arrayData,'newPgeTxt','name');?> : </li>
        <li><a href="<?=ROOT_JWPATH?>">
          <?=$parObj->_getLabenames($arrayData,'newHmeTxt','name');?>
          </a></li>
        <li>></li>
              <li><a style="color: #E67F23;"><?=$parObj->_getLabenames($arrayData,'payment','name');?></a></li>  
             
            </ul><br>

            <div class="return">
            <a href="<?=$backButtonLink?>"><?=$parObj->_getLabenames($arrayData,'newBackTxt','name');?></a>
         </div>

         <div class="title" style="color:black;">
           <h3><?=$parObj->_getLabenames($arrayData,'payment','name');?></h3><br>
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
			<!-- <p align="right"><a href="javascript:void(0)" style=" color: #2A80B9;"onclick="return unsubscribe();">
          [ <//?=$parObj->_getLabenames($arrayData,'condition1','name');?> ]
          </a></p>-->
         </div>
         
         </div>
    <section class="payment_img_sec">
               <div class="coupons">
				  
                   <div class="cpn-title"><p class="single"><?=$parObj->_getLabenames($arrayData,'payment','name');?></p></div>
                   <div class="newcouponscontent">
                   <span class="double"><img src="<?=ROOT_FOLDER?>images/e_transactions.png" alt="e-Transactions"></span><br>
                   <span class="double"><img src="<?=ROOT_FOLDER?>images/var_logos.jpg" alt="Jiwok"></span>
                   <script type="text/javascript" src="<?=ROOT_FOLDER?>js/jquery-2.0.0.min.js"></script>
                   <script src="https://checkout.stripe.com/checkout.js"></script>

                   <div align="center" >
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
				$amount				=$price_tmp;
				$client_email		=	$_SESSION['payment']['user_email'];
				$reffPara			=	$reffPara.','.'zlotys'; // order reference for euro payment
				if ($_SESSION['reusable']=="FX")
					$reffPara1 =  $reffPara.','.'FX';
				else
					$reffPara1 =  $reffPara.','.'0';
				$order_reference	=	base64_encode($reffPara1);
				$language_name		=	"polish";
				$buttonValue		=	$parObj->_getLabenames($arrayData,'pay','name');
				$curr				=	"pln";
				 $total_amount 		= $amount * 100; 
				//echo getGiftPayBoxForm($amount, $client_email, $order_reference, $language_name,$buttonValue);
				
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
                $total_amount = $amount * 100; 
				$curr				=	"eur";
				}
                ?>
                             
                       <input type="submit" id="customButton" class="butts" value="Payez maintenant &gt;">

		   
 <script>
  var handler = StripeCheckout.configure({
    key: 'pk_live_8j42smc327cbrhEl91rQXMRl',
    image: '<?=ROOT_FOLDER?>images/stripe.png',
    locale: 'auto',
    
    token: function(token) {   
        
            $.ajax({
            type: 'POST',
            url: 'gift_payment_success.php',
            //data: 'token='+token+'&amount='+<?php //echo $amount;?>,
            data: {'token': token, 'amount':<?php echo $total_amount;?>,'language_name':'<? echo $language_name;?>', 'order_reference':'<? echo $order_reference;?>','user_id':'<? echo $userId;?>'},
            success: function (data){ //alert(data);return false;
             if(data == 0) 
             { //alert("11");
                window.location ='payment_success.php';
             }
             else if (data == 1) 
             {
				 //error case
				  window.location ='giftafterpayment.php';
				
			 }else{ alert("error");return false;}
            }
          });
    }
  });
$(document).ready(function(){ 
  $('#customButton').on('click', function(e) { 
    // Open Checkout with further options
    handler.open({
      name: 'Jiwok',
      currency: "<?php echo $curr; ?>",
      //amount: '<?php echo $total_amount;?>',
     panelLabel : "Valider",
      email:'<?php echo $_SESSION['giftregistration']['user_email'];?>',
			allowRememberMe:false,
			locale:'fr'
    });
    e.preventDefault();
  });
    });

  // Close Checkout on page navigation
  $(window).on('popstate', function() {
    handler.close();
  });
</script>
		    </div>
             
           </div>
          
          </div>  <br>
           <p align="right"><a href="javascript:void(0)" style=" color: #2A80B9;"onclick="return unsubscribe();">
          [ <?=$parObj->_getLabenames($arrayData,'condition1','name');?> ]
          </a></p>
          </section>
          
          

       <section class="profile">
         
<!--
         //
         
         <div class="left">
               <section class="coupons">
                   <p class="single"><img src="images/coupon-1.jpg" alt="Jiwok coupon"></p>
                   <span class="double"><img src="images/coupon-2.jpg" alt="Jiwok coupon"></span>
                   <span class="double"><img src="images/coupon-2.jpg" alt="Jiwok coupon"></span>
               </section>
            
         </div>
         
         ////
-->
       <div class="profile-edit">
		
          <div class="bloks reg">
          <p>
          <?=$parObj->_getLabenames($arrayData,'service','name');?>
          </p>
            <div class="rows">
              <?=$parObj->_getLabenames($arrayData,'fname','name');?>:
              <?=$_SESSION['user_name'];?>
            </div>
            <div class="rows">
              <?=$parObj->_getLabenames($arrayData,'lname','name');?>:
              <?=$_SESSION['user_lname'];?>
            </div>
            <div class="rows">
               <?=$parObj->_getLabenames($arrayData,'youmail','name');?>:
                <?=$_SESSION['user_email'];?>
            </div>
            <div class="rows">
                 <?=$parObj->_getLabenames($arrayData,'gtype','name');?>:
                <?=$passme;?>
            <?=$parObj->_getLabenames($arrayData,'months','name');?>
            </div>
          
          
          
          
           
           <?php 
		  	if($_SESSION['giftregistration']['first_name'] != '') 
				{ 
		  ?>
          <div class="rows">
            <?=$parObj->_getLabenames($arrayData,'frndfname','name');?>
            :
            <?=stripslashes($_SESSION['giftregistration']['first_name']);?>
        </div>
          <div class="rows">
            <?=$parObj->_getLabenames($arrayData,'frndlname','name');?>
            :
            <?=stripslashes($_SESSION['giftregistration']['last_name']);?>
         </div>
         <div class="rows">
            <?=$parObj->_getLabenames($arrayData,'frndmail','name');?>
             :
            <?=$_SESSION['friend_email'];?>
           </div>
           <div class="rows">
            <?=$parObj->_getLabenames($arrayData,'frndmsg','name');?>
             :<br />
            <?=str_replace("\n","<br />",$_SESSION['frend_msg']);?>
          </div>
          <?php 
				}
		?>
           
           
      
          <!--<p><a href="#" class="btn_orng">MODIFIEZ VOTRE PROFIL</a></p>-->   
   
       </div></div>
       </section>
      <p class="single" style="font:bold;" ><?=$parObj->_getLabenames($arrayData,'paymentbottom1','name');?></p><p class="single"><?=$parObj->_getLabenames($arrayData,'paymentbottom2','name');?></p>  
     </div>
<?php include('footer.php');?>
<style>
.popnew  { background:#408dc1;width:100%; margin:50px auto 0;display:none; max-width:738px;z-index:500; 
           color:#f4d03e;font-family: 'Montserrat(OTT)', sans-serif; border-radius:15px}
.popnew .close  { position:absolute; top:-13px; right:-13px; cursor:pointer;}
 .popnew    { width:auto; margin:50px 10px; }
 .popbox {
    font-size: 13.03px;
}
</style>
<style type="text/css">
.butts{
	background: url("<?=ROOT_FOLDER?>images/buttons_ylow.png") repeat-x scroll 0 -162px transparent;
    border: 0 none;
    border-radius: 5px 6px;
    color: #ffffff;
    cursor: pointer;
    font-size: 11px;
    font-weight: bold;
    padding: 5px;
    position: relative;
    text-align: center;
    width: 260px;
}
</style>

<script language="javascript" type="text/javascript">
function unsubscribe()
 { //alert("here");return false;
	     $('.popnew').bPopup({
            speed: 2000,
            transition: 'slideDown'
        });
	}
function hideUnsubscribe(styleid)
	{
		document.getElementById(styleid).style.display='none';
	}
GetListFromCrowdSound();

</script>
<!--popup disply-->
  <section class="popnew" style="height:50%;overflow:scroll;overflow-x:hidden;"> <img onclick="hideUnsubscribe('unsubscribePgm');" class="close b-modal __b-popup1__">



          <div class="popbox"  >
            
          <h3> <?=$parObj->_getLabenames($arrayData,'paymentPopupTitle','name');?></h3>
          <?php
		if($lanId	!=	5)
		{?>
          	<div colspan="2" style="text-align: justify;" align="center"><?=normalze(html_entity_decode($parObj->_getLabenames($arrayData,'paymentPopupPara1','name'), ENT_QUOTES, "utf-8"));?></div>
       	<?php
		}
		else
		{?>
        	<div colspan="2" style="text-align: justify;" align="center"><?=html_entity_decode($parObj->_getLabenames($arrayData,'paymentPopupPara1','name'), ENT_QUOTES, "utf-8");?></div>
        <?php
		}?>    

          <div colspan="2" style="text-align: justify;"  align="center"><?=normalze(html_entity_decode($parObj->_getLabenames($arrayData,'paymentPopupPara2','name'), ENT_QUOTES, "utf-8"));?></div>
        
          <div colspan="2" style="text-align: justify;" align="center"><?=normalze(html_entity_decode($parObj->_getLabenames($arrayData,'paymentPopupPara3','name'), ENT_QUOTES, "utf-8"));?></div>
   </div>
          </section>
 
