<?php
require_once('stripe_code/config.php');
		/*Getting the user Details*/
		
		$planId         = $_REQUEST['planid'];
		$user_id		=	$_SESSION['user']['userId'];
		$sqlQry		    =	"SELECT * FROM `user_master` where user_id='".$user_id."'";
		$resQry			=	$GLOBALS['db']->getRow($sqlQry, DB_FETCHMODE_ASSOC);
		$user_alt_email	=	$resQry['user_alt_email'];
		$user_fname		=	$resQry['user_fname'];
		$user_lname		=	$resQry['user_lname'];
		$emailUser      = 	$resQry['user_email'];	
		$payboxEmail	=	 $resQry['paybox_email'];
		$client_email   = 	$payboxEmail;
		
		/*Getting the currency details as per the language*/
		if($_SESSION['language']['langId']==1)
		{
			$language_code	=	'GBR';
			$currencyCode	=	1;
			$stripecurrency = "";//stripe doubt
		}
		else if($_SESSION['language']['langId']==5)
		{
			$language_code	=	'POL';
			$currencyCode	=	5;
			$stripecurrency = "PLN";
		}
		else
		{
			$language_code	=	'FRA';
			$currencyCode	=	2;
			$stripecurrency = "EUR";
		}
		
		$plan		 		=	$planId;
		$dbQuery	 		=	"select * from jiwok_payment_plan where plan_id='".$plan."' and plan_currency='".$currencyCode."'";
		$res				=	$GLOBALS['db']->getRow($dbQuery, DB_FETCHMODE_ASSOC);
		
		//Check for the discont code entered or not by the user
		$discStatusAmount	=	0;
		$croneAmount		=	$res['plan_amount'];
		
		//Get the discount code details
		if($_SESSION['payment']['discCode'])
		{
			$discountCodeQuery	=	"SELECT * FROM affiliate_discountcode WHERE discount_code ='".$_SESSION['payment']['discCode']."' AND CURDATE() BETWEEN start_date AND end_date AND code_status = 'A'";
			$resDiscountCode	=	$GLOBALS['db']->getRow($discountCodeQuery, DB_FETCHMODE_ASSOC);
		}
		if((($_SESSION['payment']['discCode']) &&  ($plan	==	1))	||	(($_SESSION['payment']['discCode']) &&  ($currencyCode		== 	5)	&&	($resDiscountCode['all_plan_status']	==	1)))		
		{
			$res['plan_amount']	=	$_SESSION['discountAmount'];
			$discStatusAmount	=	$_SESSION['discountAmount']."##".$_SESSION['payment']['discUser_id'];
		}		
		$price		 					=	$res['plan_amount'];
		$price_centeme					=	$price * 100;
		if($language_code	==	'GBR')
		{
			$url		=	'en/';
		}
		else if($language_code	==	'POL')
		{
			$url		=	'pl/';
		}
		else
		{
			$url		=	'';
		}
		///need to add code for stripe invoice
?>

<!--copy from stripe form page created by neethu-->
<?php require_once('stripe_code/config.php');?>
<link href="resources/style.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="resources/jquery.pwstabs-1.2.1.css">
<link href="resources/style_dev.css" rel="stylesheet" type="text/css">
 <html>
 <body>
      <section class="payment" style="text-align: center;">
        <div class="frame3">
         <table class="pbx_table_logo" border="0">
<tbody>
<tr align="CENTER">
<td>
	<h1 class="logo">
<img src="images/logo.png" title="Jiwok"></h1>
</td>
</tr>
</tbody>
</table>

<form action="" method="POST" id="payment-form" name="stripe">
  <span class="payment-errors"></span>
  <div class="form-row">
    <label>
      <span>Card Number</span>
      <input type="text" size="20" data-stripe="number"/>
    </label>
  </div>
  <div class="form-row">
    <label>
      <span>CVC</span>
      <input type="text" size="4" data-stripe="cvc"/>
    </label>
  </div>
  <div class="form-row">
    <label>
      <span>Expiration (MM/YYYY)</span>
      <input type="text" size="2" data-stripe="exp-month"/>
    </label>
    <span> / </span>
    <input type="text" size="4" data-stripe="exp-year"/>
  </div>
  <button type="submit">Submit Payment</button>
</form>
     </div>    
      </div> 
      </section>
     </div>
</body>
</html>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">
  // This identifies your website in the createToken call below
  Stripe.setPublishableKey('pk_test_0cRU1evXCYUXnY8b0xrahmwG');
  // ...
</script>
<script type="text/javascript">
jQuery(function($) {
  $('#payment-form').submit(function(event) { 
    var $form = $(this);

    // Disable the submit button to prevent repeated clicks
    $form.find('button').prop('disabled', true);

    Stripe.card.createToken($form, stripeResponseHandler);
   
    // Prevent the form from submitting with the default action
    return false;
  });
});
function stripeResponseHandler(status, response) { 
  var $form = $('#payment-form'); alert(JSON.stringify(response));

  if (response.error) { alert("aaa");return false;
    // Show the errors on the form
    $form.find('.payment-errors').text(response.error.message);
    $form.find('button').prop('disabled', false);
  } else { alert("ddd"); //return false;
    // response contains id and card, which contains additional card details
    var token = response.id;
    // Insert the token into the form so it gets submitted to the server
    $form.append($('<input type="hidden" name="stripeToken" />').val(token));
    // and submit
    $form.get(0).submit();
  }
};
</script>
<?php
 $token  = $_POST['stripeToken'];
  $customer = \Stripe\Customer::create(array(
  "source" => $token,
  "plan" => $planId ,
  "email" => $emailUser )
);
//~ if (($customer->subscriptions->data[0]->status =="trialing") || ($customer->subscriptions->data[0]->status =="active"))
if (($customer->id) || ($customer->subscriptions->data[0]->id ))
{
	
}
else
{
	
}

  echo '<h1>Successfully charged $50.00!</h1>';
?>

<!--- form code ends-->












