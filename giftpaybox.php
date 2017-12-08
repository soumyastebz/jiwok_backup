<style type="text/css">
.butts{
	background: url("images/buttons_ylow.png") repeat-x scroll 0 -162px transparent;
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
<?php

function getPaymentNewForm($amount, $client_email, $order_reference, $language_name,$buttonValue)
{
	$language_array		=	array(
							'english'=>'GBR',
							'french'=>'FRA',
							'polish'=>'POL');

	$language_name		=	strtolower($language_name);
	$language_code		= 	$language_array[$language_name];
	/*-----------------mandatory in any request :---------*/
	/*$PBX_SITE			=	'1999888';// Site number (provided by Paybox)
	$PBX_RANG 			=   '32';	 //Rank number (provided by Paybox)
	$PBX_IDENTIFIANT	=	'110647233';// Internal identifier (provided by Paybox)
	$PBX_TOTAL 			= 	'1000';//Transaction amount
	$PBX_DEVISE			=	'978';// Transaction currency
	$PBX_CMD 			=	'TEST Paybox';// Merchant reference for the order
	$PBX_PORTEUR 		=	 'test@paybox.com';//E-mail address of the end customer
	$PBX_RETOUR 		= 	'Mt:M;Ref:R;Auto:A;Erreur:E;Directplus:U;Cardtype:C;Expiry:D;';//List of parameters that Paybox should send back after the payment
	$PBX_HASH 			=	'SHA512"';// Type of hash algorithm used to calculate the HMAC hash
	$PBX_TIME 			=	'2014-02-28T11:01:50+01:00"';// Timestamp of the transaction
	$PBX_HMAC 			=	'F2A799494504F9E50E91E44C129A45BBA2
							6D23F2760CDF92B93166652B9787463E12BAD4C660455FB0447F882B22256DE6E703AD6669B73C59
							B034AF0CFC7E';*/// HMAC hash calculated with the secret key
	/*-----------------mandatory in any request :---------*/
	
	$form_Con		=	'<form method="POST" action="https://preprod-tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi" name="PAYBOX">

<input type="hidden" value="1999888" name="PBX_SITE">
<input type="hidden" value="O" name="PBX_AUTOSEULE">

<input type="hidden" value="32" name="PBX_RANG">
<input type="hidden" value="0000" name="PBX_TOTAL">
<input type="hidden" value="978" name="PBX_DEVISE">
<input type="hidden" value="FRA" name="PBX_LANGUE">
<input type="hidden" value="TEST Paybox" name="PBX_CMD">
<input type="hidden" value="test@paybox.com" name="Psuccess.phBX_PORTEUR">
<input type="hidden" value="http://www.jiwok.com/payment_p?msg=MQ==" name="PBX_ANNULE">
<input type="hidden" value="http://www.jiwok.com/giftafterpayment.php" name="PBX_EFFECTUE">
<input type="hidden" value="http://www.jiwok.com/payment_success.php?msg=Mg==" name="PBX_REFUSE">
<input type="hidden" value="authorization_no:A;amount:M;order_reference:R;transaction_id:T;error_code:E;sign:K;Directplus:U" name="PBX_RETOUR">
<input type="hidden" value="https://preprod-tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi" name="PBX_PAYBOX">
<input type="hidden" value="HTML" name="PBX_SOURCE">
<input type="hidden" value="304-OS INCONNU" name="PBX_VERSION">
<input type="submit" class="blu-botton" value="Payez maintenant &gt;"></form>';
return 	$form_Con;


}
function getGiftPayBoxForm($amount, $client_email, $order_reference, $language_name,$buttonValue)
{
	//$amount must be in centimes (1 euro = 100 centimes).  size - 3 to 10 numbers. So for 2 euros, $amount must be 200.
	$language_array	=	array(
							'english'=>'GBR',
							'french'=>'FRA',
							'polish'=>'POL');

	$language_name	=	strtolower($language_name);
	$language_code	= 	$language_array[$language_name];
	$_SESSION['giftsubscription'] = 'TRUE';
	################
	#	Mandatory Fields
	################
	//mode d'appel
	$PBX_MODE			=	'4';			//Mode of retrieval of information
	//identification 
	/****************************PBX_SITE to be chnaged here*****************************************/
	$PBX_SITE			= 	'6209738';	//Site number (TPE) given by the bank
	/************************************************************************************************/
	$PBX_RANG			= 	'01';			//Rank number (?machine?) given by the bank
	$PBX_IDENTIFIANT	= 	'513381780';	//PAYBOX identifier, supplied by PAYBOX SERVICES at the time of registration.
	//informations paiement (appel)
	//$PBX_DEVISE	= '978';			// Currency
	if($language_code	==	'GBR')
	{
		$PBX_DEVISE	=	'840';			// Currency US Dollar
		//$PBX_CURRENCYDISPLAY		=	'USD';
		$url		=	'en/';
	}
	else if($language_code	==	'POL')
	{
		$PBX_DEVISE	=	'985';			// Currency US Dollar
		//$PBX_CURRENCYDISPLAY		=	'USD';
		$url		=	'pl/';
	}
	else//
	{
		$PBX_DEVISE	=	'978';			// Currency
		$url		=	'';
	}
	$PBX_PORTEUR	= 	$client_email;	// client email
	$PBX_CMD		= 	$order_reference;	// reference id
	$PBX_LANGUE		= 	$language_code;	// language
	$PBX_OUTPUT     = 	"C";
	//informations n�cessaires aux traitements (r�ponse)
	$PBX_RETOUR     = 	"authorization_no:A\;amount:M\;order_reference:R\;transaction_id:T\;error_code:E\;sign:K";
	//$PBX_EFFECTUE    = "http://www.jiwok.com/jiwokv2/paybox_paid.php";	//payment accepted URL
	$PBX_EFFECTUE 	= 	ROOT_JWPATH.$url.'giftafterpayment.php';
	//$PBX_EFFECTUE    = 'http://www.jiwok.com/payment_success.php?msg='.base64_encode(3);
	//$PBX_REFUSE      = "http://www.jiwok.com/jiwokv2/payment_error.php"; //payment refused
	$PBX_REFUSE   	= 	ROOT_JWPATH.$url.'payment_success.php?msg='.base64_encode(2);
	//$PBX_ANNULE      = "http://www.jiwok.com/jiwokv2/payment_cancelled.php"; //payment cancelled
	$PBX_ANNULE     = 	ROOT_JWPATH.$url.'payment_success.php?msg='.base64_encode(1);
	//page in case of error
	$PBX_ERREUR     = 	ROOT_JWPATH.$url."payment_unauthorise.php";
	/***** Changed on 26th Nov *******/
	//$PBX_CMD = "ma_ref123IBS_2MONT0000000790IBS_NBPAIE12IBS_FREQ01IBS_QUAND28IBS_DELAIS001";
	$today 	=	date('d');
	if (strlen($today)<2) $today 	= 	"0".$today;
	///amount assigning
	//$membqry="SELECT `membership_fee`,`membership_feedollar` FROM `settings`";
	$membqry	=	"SELECT membership_fee,membership_feedollar,reusable_membership_fee,reusable_membership_feedollar FROM settings";
	$membreslt 	= 	$GLOBALS['db']->getAll($membqry, DB_FETCHMODE_ASSOC);
	if($language_code	==	'GBR')
	{
		if ($_SESSION['reusable']	==	"FX") 
		{//if reusable was selected
			$fee 	= 	$membreslt[0]['reusable_membership_feedollar'];
		}
		else 
		{
			$fee 	= 	$membreslt[0]['membership_feedollar'];
		}
		//Find the amount
		//$fee= $membreslt[0]['membership_feedollar'];
		//$amt = $fee * $_SESSION['gift_type'];
		$amt 	=	$amount;
		//$amt=$curr->convert($amt,'EUR','USD');
		$amt	=	$amt * 100;
	}
	else
	{
		if ($_SESSION['reusable']=="FX") 
		{//if reusable was selected
			$fee 	= 	$membreslt[0]['reusable_membership_fee'];
			$amt 	= 	$fee * $_SESSION['gift_type'] * 100;
		}
		else 
		{
			$fee 	= 	$membreslt[0]['membership_fee'];
			$amt 	= 	$amount * 100;
		}
		//$fee= $membreslt[0]['membership_fee'];
		//$amt = $fee * $_SESSION['gift_type'] * 100;
        $l 	= 	strlen($amt);
        $n	=	10-$l;
        for($i	=	0;$i<$n;$i++)
        {
         	$amt	=	"0".$amt;
		}
	}
	$PBX_TOTAL 	= 	$amt;
	//$PBX_TOTAL		= "0000000001";
	//$PBX      = "PBX_MODE=$PBX_MODE PBX_SITE=$PBX_SITE PBX_RANG=$PBX_RANG PBX_IDENTIFIANT=$PBX_IDENTIFIANT PBX_TOTAL=$PBX_TOTAL PBX_DEVISE=$PBX_DEVISE PBX_CMD=$PBX_CMD PBX_PORTEUR=$PBX_PORTEUR PBX_EFFECTUE=$PBX_EFFECTUE PBX_REFUSE=$PBX_REFUSE PBX_ANNULE=$PBX_ANNULE PBX_ERREUR=$PBX_ERREUR PBX_RETOUR=$PBX_RETOUR PBX_OUTPUT=$PBX_OUTPUT PBX_LANGUE=$PBX_LANGUE";

	$PBX      = 	"PBX_MODE=$PBX_MODE PBX_SITE=$PBX_SITE PBX_RANG=$PBX_RANG PBX_IDENTIFIANT=$PBX_IDENTIFIANT PBX_TOTAL=$PBX_TOTAL PBX_DEVISE=$PBX_DEVISE PBX_CMD=$PBX_CMD PBX_PORTEUR=$PBX_PORTEUR PBX_EFFECTUE=$PBX_EFFECTUE PBX_REFUSE=$PBX_REFUSE PBX_ANNULE=$PBX_ANNULE PBX_ERREUR=$PBX_ERREUR PBX_RETOUR=$PBX_RETOUR PBX_OUTPUT=$PBX_OUTPUT PBX_LANGUE=$PBX_LANGUE";

	/***** Changed on 26th Nov *******/
	//echo $PBX;
	//lancement paiement par ex�cution
	//$form_content	= shell_exec( "/var/www/vhosts/default/cgi-bin/modulev3.cgi $PBX" );
	$form_content	= 	shell_exec( "/home/sites_web/client/newdesign/cgi-bin/modulev3.cgi $PBX");
	//var_dump($form_content);
	//$form_content	= shell_exec( "/var/www/html/cgi-bin/modulev3.cgi $PBX" );
	$form_content	.= 	'<input type="submit" value="'.$buttonValue.'" class="butts" /></form>';
	return $form_content;
}
?>
