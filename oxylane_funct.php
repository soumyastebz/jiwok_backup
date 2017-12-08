<?php

	error_reporting(E_ALL ^ E_NOTICE);

	/** Return the UTC Timestamp used to sign the header **/

	function getUTCTimeStamp() {  

 		// Get current utc time (take into account summer/winter adjustment)

			$NB_DAY_IN_YEAR = 365.25;

            $NB_MONTH_IN_YEAR = 12;

            $NB_HOUR_IN_DAY = 24;

            $NB_MINUTES_IN_HOUR = 60;

            $year = gmdate("Y");

            $month = gmdate("m");

            $day = gmdate("d");

            $hour = gmdate("H");

            $min = gmdate("i");



            $myTimestamp = $year * $NB_DAY_IN_YEAR * $NB_HOUR_IN_DAY * $NB_MINUTES_IN_HOUR 

                + $month * ($NB_DAY_IN_YEAR/$NB_MONTH_IN_YEAR) * $NB_HOUR_IN_DAY * $NB_MINUTES_IN_HOUR

                + $day * $NB_HOUR_IN_DAY * $NB_MINUTES_IN_HOUR

                + $hour * $NB_MINUTES_IN_HOUR

                + $min;

            

            return $myTimestamp;

        }

	

    /** Return the value for the sign tag in the header **/    

	function wsDispatcherSign($accountParameters){

        	

            $hashMessage = md5($accountParameters["domain"].$accountParameters["version"].$accountParameters["server_app_name"].$accountParameters["wsName"].$accountParameters["client_app_name"].$accountParameters["client_login"].$accountParameters["secret_key"].getUTCTimeStamp());

            return $hashMessage;

        }

		  

	/** Return the label corresponding to the error code **/

	function getErrorLabel ($errorNum){

		  switch($errorNum){

			  case 0:

				  return "No Error";

			  case 1:

				  return "insufficient funds";

			  case 3:

				  return "unknow account";

			  case 4:

				  return "card non active";

			  case 7:

				  return "invalid merchant";

			  case 45:

				  return "invalid ean number";

			  case 73:

				  return "unknown card";

			  default:

				  return "Error number : ".$errorNum;

		  }

	  }

	  

	/** Return the card balance or the error label of the problem **/

	function getCardBalance($clientSoap, $cardNumber, $currency, $merchantId, $subThirdNumber, $thirdNumber, $thirdTypeNumber, $eanCode='')

	  {

	  try{

		  $response = $clientSoap->getGiftcardHistory(array('voGiftCardHistoryRequest' => array('cardNumber'=>$cardNumber,

																								'currency'=>$currency,

																								'language'=>'',

																								'merchantId'=>$merchantId,

																								'subThirdNumber'=>$subThirdNumber,

																								'thirdNumber'=>$thirdNumber,

																								'thirdTypeNumber'=>$thirdTypeNumber,
																								
																								'eanCode'=>$eanCode

																								)));	

		} catch (SoapFault $exception) {

			echo $exception;
			return  "on error occured during the communication";      

		} 
//echo $response->getGiftcardHistoryReturn->status;exit;
		if($response->getGiftcardHistoryReturn->status == 0){

			return  $response->getGiftcardHistoryReturn->balance;

		}else{

			return getErrorLabel($response->getGiftcardHistoryReturn->status);

		}

	  }

	

	function redemCard($clientSoap, $amount, $cardNumber, $cashierNumber, $currency, $merchantId, $subThirdNumber, $thirdNumber, $thirdTypeNumber, $tillNumber, $eanCode = ''){
			try{
				$response = $clientSoap->actionOnGiftCard(array('actionOnGiftCard' => array('amount'=>$amount,
																									'cardNumber'=>$cardNumber,
																									'cardType'=>'',
																									'cashierNumber'=>$cashierNumber,
																									'currency'=>$currency,
																									'futureActivationDate'=>'',
																									'language'=>'',
																									'merchantId'=>$merchantId,
																									'requestCode'=>2202,
																									'subThirdNumber'=>$subThirdNumber,
																									'thirdNumber'=>$thirdNumber,
																									'thirdTypeNumber'=>$thirdTypeNumber,
																									'tillNumber'=>$tillNumber,
																									'transactionNumber'=>'',
																									'autorisationNumber'=>'',
																									'eanCode'=>$eanCode,
																									'promoCode'=>'')));
			 } catch (SoapFault $exception) {
				return  "on error occured during the communication";      
			 } 
			if($response->actionOnGiftCardReturn->status == 0){
				return  "Transaction OK";
			}else{
				return getErrorLabel($response->actionOnGiftCardReturn->status);
			}
	  }
	


	

	/** Return the soap client used to process the requests and set the good headers **/

	function initSoapClient($accountParameters, $soapClientParameters){

		// Prepare SoapHeader parameters with your goods parameters

		

		$headers1 = new SoapHeader('com.decathlon.wsdispatch.uddi', 'WS_DOMAIN', $accountParameters["domain"]); 

        $headers2 = new SoapHeader('com.decathlon.wsdispatch.uddi', 'WS_VERSION', $accountParameters["version"]);

        $headers3 = new SoapHeader('com.decathlon.wsdispatch.uddi', 'WS_APP', $accountParameters["server_app_name"]);

        $headers4 = new SoapHeader('com.decathlon.wsdispatch.uddi', 'WS_NAME', $accountParameters["wsName"]);

        $headers5 = new SoapHeader('com.decathlon.wsdispatch.uddi', 'CLIENT_APP', $accountParameters["client_app_name"]);

        

        $headers6 = new SoapHeader('com.decathlon.wsdispatch.security', 'SIGNATURE', wsDispatcherSign($accountParameters));

        $headers7 = new SoapHeader('com.decathlon.wsdispatch.security', 'LOGIN', $accountParameters["client_login"]);

	  	

	  $soapClient = new SoapClient($accountParameters["wsdl_path"], $soapClientParameters);

		$soapClient->__setSoapHeaders(array($headers1,$headers2,$headers3,$headers4,$headers5,$headers6,$headers7));

		

		return $soapClient;

	  }

	  

	



/** =================== you can find here the configuration and some examples of balance inquiry or card redemption =============== **/

	

	//defined your account settings there

	$accountParameters = array("secret_key" => "Pj9aEJWKPI",

							"client_login" => "JIWOK",

							"client_app_name" => "JIWOK",

							"wsName" => "WSGiftCard",

							"server_app_name" => "GIFTCA",

							"version" => "2.1.0",

							"domain" => "CASH",

							"wsdl_path" => "WSGiftCardService_org.xml"

							);

	//paramters for the soap client 

	$soapClientParameters = array('soap_version'   => SOAP_1_2);

	//print_r($accountParameters);exit;

	//global config

	$currency = 978; //for € config

	

	//jiwok config

	$merchantId = 97254100001;

	$subThirdNumber = '';

	$thirdNumber = '';

	$thirdTypeNumber = '';

								

	//card parameters

	//$cardNumber = 7777004673564883;     //1990
	//$cardNumber = 7777004673068589;
//$cardNumber = 7777004673082137;   //1500
//$cardNumber = 7777004673584978;
//$cardNumber = 7777004673074789;
	$eanCode = '';

	

	//transaction parameter

	$redemAmount = 2; //amount in cents

	$cashierNumber = 1;

	$tillNumber = 0;



	$soapClient = initSoapClient($accountParameters, $soapClientParameters);



//How to inquiry the balance of a card

	//$balance = getCardBalance($soapClient, $cardNumber, $currency, $merchantId, $subThirdNumber, $thirdNumber, $thirdTypeNumber, $eanCode);

	//echo "<br>the card amount is : ".$balance." centimes €";

	

//How to redem a card

	/*$status = redemCard($soapClient, $redemAmount, $cardNumber, $cashierNumber, $currency, $merchantId, $subThirdNumber, $thirdNumber, $thirdTypeNumber, $tillNumber, $eanCode);*/

//	echo $status;

		//$balance = getCardBalance($soapClient, $cardNumber, $currency, $merchantId, $subThirdNumber, $thirdNumber, $thirdTypeNumber, $eanCode);

	//echo "<br>the card amount is 1 : ".$balance." centimes €";

	

?>

