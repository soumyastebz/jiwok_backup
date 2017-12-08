<?php
function getPaymentNewForm($planId)
{
	
		$user_id		=	$_SESSION['user']['userId'];
		
		/*Getting the user Details*/
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
		}
		else if($_SESSION['language']['langId']==5)
		{
			$language_code	=	'POL';
			$currencyCode	=	5;
		}
		else
		{
			$language_code	=	'FRA';
			$currencyCode	=	2;
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
		
		
		$PBX_MODE			=	'4';			//Mode of retrieval of information
		
		//identification 
		/****************************PBX_SITE to be chnaged here*****************************************/
		$PBX_SITE			= 	'6209738';	//Site number (TPE) given by the bank
		/************************************************************************************************/
		$PBX_RANG			= 	'01';			//Rank number (?machine?) given by the bank
		$PBX_IDENTIFIANT	= 	'513381780';	//PAYBOX identifier, supplied by PAYBOX SERVICES at the time of registration.
		
			
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
		/*$newpaymentClassObj	=	new newpaymentClass();
		$PBX_CMD		= 	$newpaymentClassObj->getInvoiceid("",$user_id);*///$client_email;// reference id
		$PBX_CMD		=	getInvoiceidNew("",$user_id);
		$PBX_LANGUE		= 	$language_code;	// language
		$PBX_OUTPUT     = 	"C";
		
		//informations n�cessaires aux traitements (r�ponse)
		$PBX_RETOUR     = 	"authorization_no:A\;transaction_num:S\;amount:M\;order_reference:R\;transaction_id:T\;errorcode:E\;Directplus:U\;sign:K";
		//NUMAPPEL -> transaction_id
		//NUMTRANS -> transaction_num
		
		//$PBX_EFFECTUE    = "http://www.jiwok.com/jiwokv2/paybox_paid.php";	//payment accepted URL
		$PBX_EFFECTUE 	= 	ROOT_JWPATH.'payment_new.php?msg=Mw==';
		//$PBX_EFFECTUE    = 'http://www.jiwok.com/payment_success.php?msg='.base64_encode(3);
		//$PBX_REFUSE      = "http://www.jiwok.com/jiwokv2/payment_error.php"; //payment refused
		$PBX_REFUSE   	= 	ROOT_JWPATH.'payment_new.php?msg=Mg==';
		//$PBX_ANNULE      = "http://www.jiwok.com/jiwokv2/payment_cancelled.php"; //payment cancelled
		$PBX_ANNULE     = 	ROOT_JWPATH.'payment_new.php?msg=MQ==';
		//page in case of error
		//$PBX_ERREUR     = 	"http://www.jiwok.com/".$url."payment_unauthorise.php";
		
		//$PBX_REPONDRE_A =  'http://www.jiwok.com/payment_new.php?msg=ipn';
		
		//$PBX_AUTOSEULE  = 'O'; // For paybox registration only, no payment will occur
		
		$PBX_RUF1		=	'POST';
		
		$PBX_REFABONNE  = $client_email;	// client email
		
		
		/***** Changed on 26th Nov *******/
		//$PBX_CMD = "ma_ref123IBS_2MONT0000000790IBS_NBPAIE12IBS_FREQ01IBS_QUAND28IBS_DELAIS001";
		$today 	=	date('d');
		if (strlen($today)<2) $today 	= 	"0".$today;
		///amount assigning
		//$membqry="SELECT `membership_fee`,`membership_feedollar` FROM `settings`";
		$membqry	=	"SELECT membership_fee,membership_feedollar,reusable_membership_fee,reusable_membership_feedollar FROM settings";
		$membreslt 	= 	$GLOBALS['db']->getAll($membqry, DB_FETCHMODE_ASSOC);
		
	
		$PBX_TOTAL	= $price_centeme;
		//$PBX_TOTAL	= 5990;
	
		$PBX      = 	"PBX_MODE=$PBX_MODE PBX_SITE=$PBX_SITE PBX_RANG=$PBX_RANG PBX_IDENTIFIANT=$PBX_IDENTIFIANT PBX_TOTAL=$PBX_TOTAL PBX_DEVISE=$PBX_DEVISE PBX_REFABONNE=$PBX_REFABONNE PBX_CMD=$PBX_CMD PBX_PORTEUR=$PBX_PORTEUR PBX_EFFECTUE=$PBX_EFFECTUE PBX_REFUSE=$PBX_REFUSE PBX_ANNULE=$PBX_ANNULE PBX_ERREUR=$PBX_ERREUR PBX_RETOUR=$PBX_RETOUR PBX_OUTPUT=$PBX_OUTPUT PBX_LANGUE=$PBX_LANGUE ";
		
		//echo $PBX; exit;
	
		/***** Changed on 26th Nov *******/
		//echo $PBX;
		//lancement paiement par ex�cution
		//$form_content	= shell_exec( "/var/www/vhosts/default/cgi-bin/modulev3.cgi $PBX" );
		//$form_content	= 	shell_exec( "/home/sites_web/client/newdesign/cgi-bin/modulev3.cgi $PBX");
		$form_content	= 	shell_exec( "/home/sites_web/client/newdesign.back/cgi-bin/modulev3.cgi $PBX");
		//var_dump($form_content);
		//$form_content	= shell_exec( "/var/www/html/cgi-bin/modulev3.cgi $PBX" );
		$form_content	.= 	'<input type="submit" value="Payez maintenant &gt;" class="buttonNew"/></form>';
		return $form_content;
}

function getInvoiceidNew($payid,$userId){
		
		$reffPara			=	$payid;
		$mytestlog			=	"discuserpayment.txt";
		$fh = fopen($mytestlog, 'a') ;	
		
		if(isset($_SESSION['payment']['discUser_id']))
		{
			if($reffPara != "")
			{
				$reffPara			=	$reffPara.','.$_SESSION['payment']['discUser_id'];
				fwrite($fh,"**********open log with disc users ***** \n");
				fwrite($fh,$reffPara."with refpara \n");
			}
			else
			{
				fwrite($fh,"**********open log with disc users1 ***** \n");
				$reffPara			=	$_SESSION['payment']['discUser_id'];
				fwrite($fh, $reffPara."\n");	
			}
		}
		else
		{
			if($reffPara != "")
				$reffPara			=	$reffPara.','.'0';
			else
				$reffPara			=	'0';
		}
		
		if(isset($_SESSION['payment']['discCode']))
		{
			$reffPara			=	$reffPara.','.$_SESSION['payment']['discCode'];
			fwrite($fh,$reffPara."with discCode \n");
		}
		else
		{
			$reffPara			=	$reffPara.','.'0';
		}
		fwrite($fh,"******************".$userId. "*******************\n");
		if($userId != '')
		{
		$reffPara			=	$reffPara.','.$userId;
		fwrite($fh,$reffPara."with userId \n");
		}
		else
		{
		$reffPara			=	$reffPara.','.'0';
		fwrite($fh,$reffPara."without userId \n");
		}
		
		if(isset($_SESSION['payment']['discId']))
		{
			$reffPara			=	$reffPara.','.$_SESSION['payment']['discId'];
			fwrite($fh,$reffPara."with discId \n");
		}
		else
		{
			$reffPara			=	$reffPara.','.'0';
		}
		
		if(isset($_SESSION['payment']['ActReffId']))
		{
			$reffPara			=	$reffPara.','.$_SESSION['payment']['ActReffId'];
		}
		else
		{
			$reffPara			=	$reffPara.','.'0';
		}
		
		if(isset($_SESSION['payment']['freedays']))
		{
			$reffPara			=	$reffPara.','.$_SESSION['payment']['freedays'];
		}
		else
		{
			$reffPara			=	$reffPara.','.'0';
		}
		if(!$_SESSION['language']['langId'])
		{
			$dbQuery	 		=  "select user_language from user_master where user_id='".$userId."'";
			$res							=	$GLOBALS['db']->getRow($dbQuery, DB_FETCHMODE_ASSOC);
			$_SESSION['language']['langId']	=	$res['user_language'];
		}
		if($_SESSION['language']['langId']=='1')
			$lanVar	=	'en';
		elseif($_SESSION['language']['langId']=='2')
			$lanVar	=	'fr';
		elseif($_SESSION['language']['langId']=='3')
			$lanVar	=	'es';	
		elseif($_SESSION['language']['langId']=='4')
			$lanVar	=	'it';
		elseif($_SESSION['language']['langId']=='5')
			$lanVar	=	'pl';		
			
		if($_SESSION['language']['langId']==1)
		{
			$p_currency	  	=	"Dollar";//Dollar
			
		}
		else if($_SESSION['language']['langId']==5)
		{
			$p_currency		=	"Zloty";//Polish Currency
		}
		else
		{
			$p_currency		=	"Euro";//Euro
		}	
		
		$brand					=	getUserBrandNew($userId);
		//Find user's paybox email
		$dbQuery	 		=  "select paybox_email from user_master where user_id	='".$userId."'";
		$res				=	$GLOBALS['db']->getRow($dbQuery, DB_FETCHMODE_ASSOC);
		$payBoxEmail		=	$res['paybox_email'];		
		$reffPara1			=	$reffPara.",0,0,0,0,0,".$brand.",".$p_currency.",".$lanVar.",".date('d-m-Y-H-i-s').",".$payBoxEmail; // order reference for euro payment
		$order_reference	=	$reffPara1;
		fwrite($fh,"**********ORDER REF *************** \n");
		fwrite($fh,$reffPara1." \n");
		fclose($fh);
		return $order_reference;
	
	}
	
function getUserBrandNew($uid){
	
		$sql	= "SELECT *
					FROM `brand_master`
					INNER JOIN brand_user ON brand_master.`brand_master_id` = brand_user.`brand_master_id`
					WHERE user_id ='".$uid."'"; 
		
		$brand	= "Jiwok";
		$arrRow		=	$GLOBALS['db']->getRow($sql,DB_FETCHMODE_ASSOC);
		 if(!PEAR::isError($arrRow)) {
			 if(is_array($arrRow))
				$brand	= $arrRow['brand_name'];
		 }
		 return $brand;
	}



?>
