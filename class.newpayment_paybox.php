<?php
require_once 'dbFunctions.php';
require_once 'nonDbFunctions.php';
require_once('paymentStrings.php');
require_once('pdfgenerate/fpdf.php');
class newpayment_payboxClass extends paymentStrings {
	public $p_currency;
	public $currencyCode;
	public $currency;
	public $paymentTable;
	function __CONSTRUCT() {
		parent::__CONSTRUCT();
		$this->paymentTable="payment";
		define("GOOGLE_SHORTNER_API_KEY", "AIzaSyBAdFjF4osoCwsLIQXMVqQsYaM_YrFh1bI");
		define("GOOGLE_SHORTNER_ENDPOINT", "https://www.googleapis.com/urlshortener/v1");				
	}
	//validating paybox response
	function validatePayboxData($response){ 
		$responseparam		=	false;
		$numtrans			=	trim($response['num_trans']);
		$numappel			=	trim($response['num_appel']);		
		$paybx_exist		=	"SELECT `num_trans` ,`num_appel` FROM `payment_paybox` WHERE `num_trans`= ".$numtrans." AND `num_appel` = ".$numappel;
		$paybx_exist		=	$this->dbSelectOne($paybx_exist);
		if(count($paybx_exist)	==	0)
		{
			$responseparam	=	true;
			
		}
		return $responseparam;
	}
	//after paybox payment updations
	function afterPayment($params,$type){
					
		//print_r($params);exit;
		$objGen   		=	new General();

		//For identifying whether from brands or main
		if($_SERVER['HTTP_HOST'] == "www.jiwok.com")
		{
			$_SESSION['paymentFrom']	=	'jiwok';
		}
		else
		{
			$_SESSION['paymentFrom']	=	$_SESSION['brand'];
		}
		/*Getting the user brand*/
		
		$brand			=	$this->getUserBrand($_SESSION['user']['userId']); 
		/*Getting the user's card details*/
		/*$cc				=	$params['cardno'];*/
		$cvv			=	$params['cvv'];
		$exp			=	$params['exp'];
		$user_id		=	$_SESSION['user']['userId'];
		/*Getting the user type.*/
		$typeOfUser	    =	$params['userType'];
		$ORDERREFERENCE	=	$params['order_reference'];
		/*include xml based on the site*/
		$parObj 	    =   new Contents('payment_new.php');
		if(($_SESSION['paymentFrom']	==	'semideparis')	||	($_SESSION['paymentFrom']	==	'kalenji')	||	($_SESSION['paymentFrom']	==	'domyos')	||	($_SESSION['paymentFrom']	==	'parismarathon'))
		{
			$xmlPath        = 	"../templates/".$_SESSION['paymentFrom']."/".$_SESSION['language']['xml'];
			$parsedXml		=	simplexml_load_file($xmlPath);
			$normalArray	= 	$this	->	xml2array($parsedXml,'');		
			//$arrayUser 	= 	$this	->	xml2array($arrayData['customer'],'');			
			$i	=	0;
			foreach($normalArray['new_payment'] as $key=>$arrayDatas)
			{
				$lower1[name]		=	$key;
				$lower2[$i][value]	=	$arrayDatas;
				$lower2[$i][attr]	=	$lower1;
				$i++;
			}
			$arrayData		=	$lower2;			
		}
		else
		{
			$xmlPath        = 	$_SESSION['language']['xml'];		
			$returnData		= 	$parObj->_getTagcontents($xmlPath,'new_payment','label');
			$arrayData		= 	$returnData['general'];
		}		
		/*xml ends*/		
		/*Getting the user Details*/
		$sqlQry		    =	"SELECT * FROM `user_master` where user_id='".$user_id."'";
		$resQry			=	$this->dbSelectOne($sqlQry);
		$user_alt_email	=	$resQry['user_alt_email'];
		$user_fname		=	$resQry['user_fname'];
		$user_lname		=	$resQry['user_lname'];
		$emailUser      = 	$resQry['user_email'];	
		$payboxEmail	=	$resQry['paybox_email'];	
		/*Getting the currency details as per the language*/
		if($_SESSION['language']['langId']==1)
		{
			$this->currency			=	"840";//Dollar
			$this->p_currency	  	=	"Dollar";//Dollar
			$this->currencyCode		= 	1;
			$this->currency_symbol	=	"$";//Euro
		}
		else if($_SESSION['language']['langId']==5)
		{
			$this->p_currency		=	"Zloty";//Polish Currency
			$this->currencyCode		= 	5;
			$this->currency			=	"985";//Zloty
			$this->currency_symbol	=	"zl";//Zloty symbol place
		}
		else
		{
			$this->p_currency		=	"Euro";//Euro
			$this->currencyCode		= 2;
			$this->currency			=	"978";//Euro
			$this->currency_symbol	=	"€";//Euro symbol place
		}
		//Find the number of transaction of the day									
		$transQuery		=	"SELECT COUNT( * ) AS numberOfTrans FROM payment WHERE payment_date = '".date("Y-m-d")."' AND payment_status	=	1";
		$transResult	=	$this->dbSelectOne($transQuery);
		$transOfTheDay	=	$transResult['numberOfTrans']+1;
		/*Getting the selected plan Details*/
		$plan		 		=	$params['payment_plan'];
		$dbQuery	 		=	"select * from jiwok_payment_plan where plan_id='".$plan."' and plan_currency='".$this->currencyCode."'";
		$res				=	$this->dbSelectOne($dbQuery);
		//Check for the discont code entered or not by the user
		$discStatusAmount	=	0;
		$croneAmount		=	$res['plan_amount'];
		//if(($_SESSION['payment']['discCode']) &&  ($plan	==	1))
		//Get the discount code details
		if($_SESSION['payment']['discCode'])
		{
			$discountCodeQuery	=	"SELECT * FROM affiliate_discountcode WHERE discount_code ='".$_SESSION['payment']['discCode']."' AND CURDATE() BETWEEN start_date AND end_date AND code_status = 'A'";
			$resDiscountCode	=	$this->dbSelectOne($discountCodeQuery);
		}
		if((($_SESSION['payment']['discCode']) &&  ($plan	==	1))	||	(($_SESSION['payment']['discCode']) &&  ($this->currencyCode		== 	5)	&&	($resDiscountCode['all_plan_status']	==	1)))		
		{
			$res['plan_amount']	=	$_SESSION['discountAmount'];
			$discStatusAmount	=	$_SESSION['discountAmount']."##".$_SESSION['payment']['discUser_id'];
		}		
		$price		 					=	$res['plan_amount'];
		$price_centeme					=	$price * 100;
		//$authorizationPlain 			=	$this->getAuthorizationfields($cc,$cvv,$exp,$payboxEmail);
		$authorizationPlain['MONTANT']	=	$price_centeme;	
		$authorizationPlain["REFERENCE"]=	$ORDERREFERENCE;//$this->getInvoiceid("",$user_id);
		
		//If the user is Polish only authentication and debit(One time payment)
		//No registration process and auto renewal
		if($resQry['user_language']	==	5)
		{
			$authorizationPlain["TYPE"]	=	'3';			
		}
		//For Testing
		if($user_id	==	65532)
		{
			//echo "<pre/>";
			//print_r($authorizationPlain);			
			//die();
		}
		
		//========make params as if previous curl payment================
		$CODEREPONSE					=	$params['error_code'] ;	
		$NUMTRANS						=	$params['num_trans'] ;
		$NUMAPPEL						=	$params['num_appel'] ;
		$REFABONNE						=	$payboxEmail;//$params['order_reference'] ;	
		$PORTEUR						=	$params['cc']; 			
		$errorcode	        			=	$CODEREPONSE;	
		if(($_SESSION['paymentFrom']	==	'semideparis')	||	($_SESSION['paymentFrom']	==	'kalenji')	||	($_SESSION['paymentFrom']	==	'domyos')	||	($_SESSION['paymentFrom']	==	'parismarathon'))
			include_once "../Swift/lib/swift_required.php";
		else	
			include_once "Swift/lib/swift_required.php";			
		/*Processing payment as per the user type*/
		
		switch ($typeOfUser)
		{			
			case "oldPaymentUser":
				/*If Registration is successful code=00000  as a res of par_str*/
				if($CODEREPONSE == '00000') 
				{ 
					
					//Find the old paybox status
					$selectOldPay	=  "select  * from payment where payment_userid ='".$user_id."' AND payment_status = '1' AND version	=	'Old'";
					$resultOldPay	= 	$GLOBALS['db']->getAll($selectOldPay,DB_FETCHMODE_ASSOC);
					
					$PaymentExpDateqry 	= 	"SELECT payment_expdate FROM payment WHERE payment_userid=".addslashes($user_id)." AND payment_status='1' && payment_date!='0000-00-00' AND version	=	'Old' ORDER BY payment_date DESC";
					$PaymentExpDateresult = $GLOBALS['db']->getRow($PaymentExpDateqry, DB_FETCHMODE_ASSOC);
					
					$PaymentExpDate		=	$PaymentExpDateresult[payment_expdate];
					$today				=	date('Y-m-d');
					$qrydayCount 		=	"SELECT DATEDIFF('$PaymentExpDate', '$today')";
					$resultdayCount		= 	$GLOBALS['db']->getRow($qrydayCount, DB_FETCHMODE_ARRAY);
					$dayCount 			= 	$resultdayCount[0];	
					
					
					if(($resQry['user_unsubscribed']	!=	'2')	&&	(sizeof($resultOldPay) > 0) && ($dayCount	>	0))
					{							
						//Send a mail to admin to inform of the unsubscribe request using send grid	
						//-------------------------------------------------------------------------------
						// Send mails using the sendgrid start							
						$hello_fname			=		"Hi,";							
						$content				=		"please remove this user(".$user_fname.") : ".$payboxEmail." on paybox system please only on paybox not in admin side";
						//$content				=		"";
						$text = "\n";
						$html = "
						<html>
						  <head></head>
						  <body>
							<p>".$hello_fname."<br><br/><br/>".$content."<br/><br/><br/>".$thanku."<br/>http://www.jiwok.com
							</p>
						  </body>
						</html>
						";
						// --from is your email address
						// --to is who you are sending your email to
						// --subject will be the subject line of your email
						$from = array('info@jiwok.com' => 'info@jiwok.com');
						$to = array('info@jiwok.com'=>'info@jiwok.com');
						//$to = array('dileepe.reubro@gmail.com'=>'Jiwok Admin');
						$subject = 'Unsubscribe this only on old Paybox System';
						// Login credentials
						$username = 'denis@jiwok.com';
						$password = 'nike2000';
											 
						// Setup Swift mailer parameters
						$transport = Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 25);
						$transport->setUsername($username);
						$transport->setPassword($password);
						$swift = Swift_Mailer::newInstance($transport);
								 
									  
						// Create a message (subject)
						$message = new Swift_Message($subject);
						 
						// attach the body of the email
						$message->setFrom($from);
						$message->setBody($html, 'text/html');
						$message->setTo($to);
						$message->addPart($text, 'text/plain');
						//$message->attach($attach);
								
										 
						// send message 
						$recipients = $swift->send($message, $failures);																	
						//-------------------------------------------------------------------------------
						//								Sendgrid end
						//-------------------------------------------------------------------------------							
					}
					if($type == '2')//Payment Code,User not in payment period
					{ 
					
						$trId						=	$ORDERREFERENCE;//$this->getInvoiceid("",$user_id);
						$paymentNew		    		=	$this->getpaymentfields($price_centeme,$cvv,$exp,$payboxEmail);/*For Geting 				Payment field values*/
						$paymentNew["PORTEUR"]	    =	$PORTEUR; /*Apending The Credit card image for payment*/
						$paymentNew["REFERENCE"]	=	$trId;
						$payboxNumApl				=	$NUMAPPEL;	
						$payboxNumTns				=	$NUMTRANS;
						$paymentNew["NUMAPPEL"]		=	$NUMAPPEL;
						$paymentNew["NUMTRANS"]		=	$NUMTRANS;							
						$errorcode	        		=	$CODEREPONSE;
						//For e-credit card (virtual credit card who could be used only 1 time)
						//We need to change the parameter type from 00053 to 00002
						//When using virtual credit card getting the error code as 00057 
						/*if($CODEREPONSE == '00057')
						{
							$paymentNew		    	=	$this->getpaymentfields($price_centeme,$cvv,$exp,$emailUser);
							$paymentNew['TYPE']		=	'00002';
							$paymentNew["PORTEUR"]	=	$PORTEUR; 
							$paymentNew["REFERENCE"]=	$trId;
							$passingParam1			=	$this->arrayDbConcatEqual($paymentNew);
							$result					=	$this->curlForPayment($passingParam1); 
							$oneArray				=	parse_str($result);		
							$errorcode	        	=	$CODEREPONSE;									
						}*/
						if($CODEREPONSE == '00000')/*If Payment is successful code=00000 */
						{									
							
							/*INVOICE GENERATION CODE*/									
								
							$transactionidnum		=	$trId;																	
							$amountDescription		=	$parObj->_getLabenames($arrayData,'amountpaid','name')." ".$this->currency_symbol.$price;				
							$amount					=	$price.$parObj->_getLabenames($arrayData,'pdfCurrency','name');															
							$plan_id				=	$plan; 
							$month				    = 	$plan_id." month"; /*Will Get Like 1 month or 3 month etc*/
							$payment_expiry_date	=	date("Y-m-d", strtotime($month));
							$payment_amount			=	$price;
							$payment_currency		=	$this->p_currency;
							$cc_image				=	$PORTEUR;	
							/*Insertion of  payment table*/
							$dbFields1				= 	"payment_userid, payment_date, payment_status, payment_amount, payment_expdate, payment_no_times, payment_firstdate, payment_currency,version";
							$dbValues1		=  "'".$user_id."','".date('Y-m-d')."','1','".$payment_amount."','".$payment_expiry_date."','1','".date('Y-m-d')."','".$payment_currency."','New'";
							$this->dbInsertSingle($this->paymentTable,$dbFields1,$dbValues1);
							
							
							// Tracking payment using Trak.io
							$qryUser	 		=	"SELECT * from user_master where user_id='".$user_id."'";
							$userDetails		= 	$GLOBALS['db']->getRow($qryUser, DB_FETCHMODE_ASSOC);
																
							$trackproperties 					= array();
							$trackproperties['email']			= $userDetails['user_email'];
							$trackproperties['UserId']			= $user_id;
							$trackproperties['payment_date']	= date('Y-m-d');
							$trackproperties['payment_amount']	= $payment_amount;
							$trackproperties['payment_expdate']	= $payment_expiry_date;
							$trackproperties['payment_currency']	= $payment_currency;
							$trackproperties['payment_mode']	= 'Manual Payment';
							//$response 							= $trakObj->trakPayment($userDetails['user_email'],$trackproperties);
							
							//Update the discount_users table if user uses discount
							if($discStatusAmount	!=	0)
							{
								$dbValues		=	"payment_status	=	'success'";
								$dbCond			=	"id 	=	'".$_SESSION['payment']['discUser_id']."'";
								$this->dbUpdate('discount_users',$dbValues,$dbCond);
								unset($_SESSION['discountAmount']);
								unset($_SESSION['payment']['discCode']); 										
							}									
							/*Insertion of  payment_transactions  table*/
							$paymentNew["NUMAPPEL"]		=	$NUMAPPEL;
							$paymentNew["NUMTRANS"]		=	$NUMTRANS;
							$paymentNew["NUMQUESTION"]	=	$NUMQUESTION;									
							$selectQueryMax	=  "select  max(payment_id) as payment_id from payment where payment_userid ='".$user_id."' AND payment_status = '1'";
							$resultMax		= 	$GLOBALS['db']->getAll($selectQueryMax,DB_FETCHMODE_ASSOC);	
							$payId			=	$resultMax[0]['payment_id'];
															
							$paymentDetails	=	base64_encode(serialize($paymentNew));
							$dbFields2		= 	"	user_id, payment_id, details, trans_refrns_id";
							$dbValues2		=  	"'".$user_id."','".$payId."','".$paymentDetails."','".$transactionidnum."'";
							$this->dbInsertSingle('payment_transactions',$dbFields2,$dbValues2);
							/**/
							/*$dbPayboxQuery	 	=  "SELECT payment_expdate FROM `payment` WHERE `payment_userid` = '".$userId."' AND `payment_expdate` >= CURDATE() AND `payment_status` = 1";
							$resPaybox			=	$this->dbSelectOne($dbPayboxQuery);			*/																			
							
							/*@PAYMENT_PAYBOX TABLE INSERTION*/
							$fields			=	"user_id, card_image, cvv, exp,num_trans,num_appel, brand, status, refabonne ";
							$values			=	"'".$user_id."','".base64_encode($cc_image)."','".$cvv."','".$exp."','".$payboxNumTns."','".$payboxNumApl."','".$brand."','ACTIVE','".$trId."'";
							$pp_id			=	$this->dbInsertSingle('payment_paybox',$fields,$values);									
							
							/*Insertion of  payment_cronjob tble*/
							$dbFields		=	"user_id,pp_id,plan_id,payment_expiry_date,payment_amount,payment_currency,status";
							$dbValues		=	"'".$user_id."','".$pp_id."','".$plan_id."','".$payment_expiry_date."','".$croneAmount."','".$payment_currency."','VALID'";																		
							if($this->dbInsertSingle('payment_cronjob',$dbFields,$dbValues))   //PAYBOX_CRON TABLE
							{									
								//$from_name				=  "Jiwok";	
								//$replyto=$from_mail		=  "Info@jiwok.com";	
								//$mailto					= $user_alt_email; /*sent into alternate email */
								$period 				=	date("F j, Y", strtotime(date('Y-m-d')))." to ".date("F j, Y", strtotime($payment_expiry_date)); 
								$cc_part 				=   substr($cc, -4);/*Last 4 digit of cc*/
								$creditcard				= 	"XXXX-XXXX-XXXX-".$cc_part;
								$subject				=	$parObj->_getLabenames($arrayData,'subject','name');
								$hello_fname			=	$parObj->_getLabenames($arrayData,'hello','name')." ".$user_fname;
								$thank					=	$parObj->_getLabenames($arrayData,'thank','name');
								//$body2					=		$parObj->_getLabenames($arrayData,'body2','name');
								$body_summary			=	$parObj->_getLabenames($arrayData,'body_summary','name');
								//$body_summary_desc=		$parObj->_getLabenames($arrayData,'body_summary_desc','name');
								$body_help				= 	$parObj->_getLabenames($arrayData,'body_help','name');
								$body_help				= 	str_replace("xbrx","<br/>",$body_help);/*P break line*/
								$thanku					= 	$parObj->_getLabenames($arrayData,'thanku','name');
								$body_summary_desc		= 	$parObj->_getLabenames($arrayData,'body_summary_desc','name');
								$body_summary_desc 		=  	str_replace("xxcc",$creditcard, $body_summary_desc);/*Crefdit card num adding*/
								$price_with_symbol		=  	$this->currency_symbol.$price;
								$body_summary_desc 		=	str_replace("xxcash",$price_with_symbol, $body_summary_desc);/*Cash adding*/
								$body_summary_desc 		=  	str_replace("xxexp",date("d-m-Y", strtotime($month)),$body_summary_desc);/*Exp date adding*/
								$body_summary_desc		=  	str_replace("xmnth",$plan,$body_summary_desc);/*month adding*/
								$acountURL 				= 	$parObj->_getLabenames($arrayData,'body_summary','name')." : <a href='".$parObj->_getLabenames($arrayData,'myAccountUrl','name')."?mode=autologin&authid=".base64_encode($user_id)."'>".$parObj->_getLabenames($arrayData,'myAccountUrl','name')."</a>"; /*Will chaneg*/	
								$body_summary_desc1		=	$parObj->_getLabenames($arrayData,'body_summary_desc1','name');
								$body_summary_desc2		=	$parObj->_getLabenames($arrayData,'body_summary_desc2','name');	
								$body_summary_desc3		=	$parObj->_getLabenames($arrayData,'body_summary_desc3','name');
								$body_summary_desc4		=	$parObj->_getLabenames($arrayData,'body_summary_desc4','name');																	
								//Pdf content starts
								$pdfHello				=	$parObj->_getLabenames($arrayData,'pdfHello','name')." ".$user_fname.",";
								$pdfWelcome				=	$parObj->_getLabenames($arrayData,'pdfWelcome','name');
								$pdfEmail				=	$parObj->_getLabenames($arrayData,'pdfEmail','name')." : ".$emailUser;										
								$pdfCardDetls1			=	$parObj->_getLabenames($arrayData,'pdfCardDetls1','name');
								$pdfCardDetls1			=	str_replace("XXXX-XXXX",$creditcard,$pdfCardDetls1);
								$pdfCardDetls1			=	str_replace("xxxx",$price,$pdfCardDetls1);
								$pdfDueDates			=	$parObj->_getLabenames($arrayData,'pdfDueDates','name')." : ".date("d-m-Y", strtotime($month));	//date("F j, Y", strtotime($payment_expiry_date));																					
								$pdfCardDetls2			=	$parObj->_getLabenames($arrayData,'pdfCardDetls2','name');
								$pdfContactus			=	$parObj->_getLabenames($arrayData,'pdfContactus','name');
								$pdfContactusUrl		=	$parObj->_getLabenames($arrayData,'body_summary_desc3','name');
								$pdfGoodluck			=	$parObj->_getLabenames($arrayData,'pdfGoodluck','name');
								$pdfSiteUrl				=	$parObj->_getLabenames($arrayData,'pdfSiteUrl','name');
								$pdfInvoiceHead			=	$parObj->_getLabenames($arrayData,'pdfInvoiceHead','name');
								$pdfTransactionid		=	$parObj->_getLabenames($arrayData,'pdfTransactionid','name')." : ".str_replace(",",", ",$transactionidnum);
								$pdfImpoNoteDescription	=	$parObj->_getLabenames($arrayData,'pdfImpoNoteDescription','name');
								$pdfPass				=	$parObj->_getLabenames($arrayData,'pdfPass','name');
								$pdfPrice				=	$parObj->_getLabenames($arrayData,'pdfPrice','name');
								$pdfMonth				=	$plan." ".$parObj->_getLabenames($arrayData,'newMonthsTxt','name');
								$pdfCurrency			=	$price." ".$parObj->_getLabenames($arrayData,'pdfCurrency','name');
								$fromTo					=	$parObj->_getLabenames($arrayData,'pdfSubscriptionFrom','name')." ".date('Y-m-d')." ".$parObj->_getLabenames($arrayData,'pdfSubscriptionTo','name')." ".date("d-m-Y", strtotime($month));
								$pdfAmountpaid			=	$parObj->_getLabenames($arrayData,'pdfAmountpaid','name').$price.$parObj->_getLabenames($arrayData,'pdfCurrency','name');
								$pdfCerditCard			=	$parObj->_getLabenames($arrayData,'pdfCerditCard','name')." : ".$creditcard;
							//Pdf content ends
								
								$line1					=	"============================================================================";
								$line2					=	"============================================";
								$line3					=	"-------------------------------------";										
								$pdf	=	new FPDF('P','mm','A4');
								$pdf->AddPage();
								//$pdf->SetFont('Arial','B',16);		
								//$pdf->Cell(80,10,$invoice);
								$pdf->Ln(10);
								$pdf->Cell(180,10,utf8_decode(trim(strtoupper($pdfInvoiceHead))),0,1,'C');
								//$pdf->Cell(20,10,$invoice,1,1,'C');
								$pdf->SetFont('helvetica', '', 12);
								$pdf->Ln(10);	
								$pdf->Cell(180,10,trim(date('d-m-Y')),0,1,'R');
								//$pdf->Cell(40,10,date('Y-m-d'));							
								$pdf->Cell(90,10,utf8_decode($pdfHello));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($pdfWelcome));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($pdfEmail));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($pdfCardDetls1));
								$pdf->Ln(5);
								$pdf->Cell(40,10,utf8_decode($pdfDueDates));									
								$pdf->Ln(10);		
								$pdf->MultiCell(0,5,utf8_decode($pdfCardDetls2));	
								$pdf->Ln(5);
								//$pdf->Cell(40,10,utf8_decode($pdfContactus));	
								$pdf->Write(10,utf8_decode($pdfContactus),'');										
								$pdf->SetFont('','U');
								$link	=	$pdf->AddLink();
								$pdf->SetTextColor(0,0,255);
								$pdf->Write(10,$pdfContactusUrl,$link);
								//$pdf->Cell(40,10,utf8_decode($pdfContactusUrl),0,1,'R');
								$pdf->SetFont('helvetica', '', 12);	
								$pdf->SetTextColor(0,0,0);										
								$pdf->Ln(10);
								$pdf->Cell(80,10,utf8_decode($pdfGoodluck));		
								$pdf->AddPage('P','A4');
								$pdf->Ln(10);
								$pdf->Cell(90,10,utf8_decode($pdfInvoiceHead));
								$pdf->Ln(5);
								$pdf->Cell(40,10,utf8_decode($line1));
								//Only for the french pdf generation
								if($_SESSION['language']['langId']	==	2)
								{									
									$pdf->Ln(10);
									//$pdf->Cell(40,10,utf8_decode(date("d-m-Y").' - '.$transOfTheDay.'    '.$pdfTransactionid));
									$pdf->MultiCell(0,8,utf8_decode(utf8_decode(date("d-m-Y").' - '.$transOfTheDay.'    '.$pdfTransactionid)));
									//$pdf->MultiCell(140,5,utf8_decode($pdfTransactionid));
									//$pdf->Cell(40,10,utf8_decode($pdfTransactionid));
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode($line3));
									//New contents starts
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode($user_fname.' '.$user_lname));
									
									$pdf->Ln(5);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfPurchaseTitle','name').$pdfPass.' '.$pdfMonth));
									
									$htPrice	=	round($price/1.2002, 2);
									$pdf->Ln(5);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfHTAmount','name').' '.$htPrice.' '.utf8_encode(chr(128))));
									
									$pdf->Ln(5);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfTVAPercentage','name')));
									
									$pdf->Ln(5);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfVATAmount','name').' '.round($price - $htPrice,2).' '.utf8_encode(chr(128))));
									
									$pdf->Ln(5);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfTTCAmount','name').''.$price.' '.utf8_encode(chr(128))));
									
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfTaxLabel','name')));
									
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode($line3));
								}
								else
								{
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode(date("d-m-Y")));
									$pdf->MultiCell(140,5,utf8_decode($pdfTransactionid));
									//$pdf->Cell(40,10,utf8_decode($pdfTransactionid));
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode($line3));
								}
								$pdf->Ln(20);
								$pdf->MultiCell(0,5,utf8_decode($pdfImpoNoteDescription));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($pdfPass));
								$pdf->Cell(40,10,utf8_decode($pdfPrice));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($line3));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($pdfMonth));
								$pdf->Cell(40,10,utf8_decode($pdfCurrency));
								$pdf->Ln(20);
								$pdf->Cell(40,10,utf8_decode($fromTo));
								$pdf->Ln(10);
								$pdf->Cell(0,10,$line2,0,0,'C');
								$pdf->Ln(10);
								$pdf->Cell(0,10,utf8_decode($pdfAmountpaid),0,0,'C');
								$pdf->Ln(5);
								$pdf->Cell(0,10,utf8_decode($pdfCerditCard),0,0,'C');
								$pdf->Ln(10);
								$pdf->Cell(0,10,$line2,0,0,'C');		
								if(($_SESSION['paymentFrom']	==	'semideparis')	||	($_SESSION['paymentFrom']	==	'kalenji')	||	($_SESSION['paymentFrom']	==	'domyos')	||	($_SESSION['paymentFrom']	==	'parismarathon'))
									$pdffilepath	=	"../pdfgenerate/user pdf/";
								else
									$pdffilepath	=	"pdfgenerate/user pdf/";
								$pdffile	=	$user_id."_".date('Y-m-d').".pdf";
								$pdfname	=	$pdffilepath.$pdffile;									
								$pdf->Output($pdfname,'F');  
								/*INVOICE GENERATION CODE ENDS*/
																	
								//-------------------------------------------------------------------------------
								// Send mails using the sendgrid
								//								Sendgrid start
								//------------------------------------------------------------------------------- 
								//include_once "Swift/lib/swift_required.php";
								/*
									 * Create the body of the message (a plain-text and an HTML version).
									 * --text is your plain-text email
									 * --html is your html version of the email
									 * If the reciever is able to view html emails then only the html
									 * email will be displayed
								 */ 
								$text 	=	"\n";
								/*$html = "
								<html>
								  <head></head>
								  <body>
									<p>".$hello_fname."<br><br>
									   ".$thank."<br/><br/>".$acountURL."<br/><br/>".$body_summary_desc1."<br/>".$body_summary_desc2."<br/>".$body_summary_desc3."<br/><br/>".$body_summary_desc4."<br/><br/>http://www.jiwok.com
									</p>
								  </body>
								</html>
								";*/
								
								$thank				=	$parObj->_getLabenames($arrayData,'thankCrone','name');																	
								$body_summary_desc5	=	$parObj->_getLabenames($arrayData,'nameCrone','name');
								
								
								$htmlBody			 =	"<p>".$hello_fname.",<br><br>
											".$thank."<br/><br/>".$acountURL."<br/><br/>".$body_summary_desc4."<br/>																																																															 														<br/>".$body_summary_desc5."<br/><br/>http://www.jiwok.com<br/><br/>".$$body_summary_desc1."<br/><br/>".$body_summary_desc2."<br/><br/>".																		$body_summary_desc3."</p>";	
								$footerText			=	$objGen->mailFooter[$_SESSION['language']['langId']];
								$html				=	$objGen->mailTpl;
								$html				=	str_replace("#MESSAGECNT#",$htmlBody,$html);
								$html				=	str_replace("#FOOTERTXT#",$footerText,$html);														
								// --from is your email address
								// --to is who you are sending your email to
								// --subject will be the subject line of your email
								$from 		= 	array('info@jiwok.com' => 'info@jiwok.com');
								
								$to 		= 	array($resQry['user_email']=>$user_fname);
								$subject 	= 	$parObj->_getLabenames($arrayData,'mailCronSubject','name');
								// Login credentials
								$username 	= 	'denis@jiwok.com';
								$password 	= 	'nike2000';
														 
								// Setup Swift mailer parameters
								$transport 	= 	Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 25);
								$transport->setUsername($username);
								$transport->setPassword($password);
								$swift 		= 	Swift_Mailer::newInstance($transport);
									 
								/*// Create a attachment
								$attachment['mime'] = 'application/pdf';
								$attachment['file'] = 'pdfgenerate/user pdf/'.$pdffile;
								$attachment['filename'] = $pdffile;
								$attach = Swift_Attachment::fromPath($attachment['file'], $attachment['mime'])->setFilename($attachment['filename']);*/
	  
								// Create a message (subject)
								$message 	= 	new Swift_Message($subject);
								 
								// attach the body of the email
								$message->setFrom($from);
								$message->setBody($html, 'text/html');
								$message->setTo($to);
								$message->addPart($text, 'text/plain');
								//$message->attach($attach);
									
									 
								// send message 
								if ($recipients 	= 	$swift->send($message, $failures))
								{
									/*Sending mails to admin intimating payment is done starts*/
									$hello_fname		=	"Hi,";							
									$content			=	"This user(".$user_fname." ".$user_lname.") : with emailid :".$emailUser." doing a payment trough new payment.The amount is: ".$amount." and the invoice id is: ".$transactionidnum." and refference id is : ".$NUMTRANS;										
									$text 				=	"\n";
									$html 				= 	"
									<html>
									  <head></head>
									  <body>
										<p>".$hello_fname."<br><br/><br/>".$content."<br/><br/><br/>".$thanku."<br/>http://www.jiwok.com
										</p>
									  </body>
									</html>";
									// --from is your email address
									// --to is who you are sending your email to
									// --subject will be the subject line of your email
									//$from = array($emailUser => $user_fname);
									$from 		= 	array('info@jiwok.com' => 'info@jiwok.com');
									$to 		= 	array('info@jiwok.com'=>'info@jiwok.com');										
									$subject 	= 	'Payment process mail';
									// Login credentials
									$username 	= 	'denis@jiwok.com';
									$password 	= 	'nike2000';														 
									// Setup Swift mailer parameters
									$transport 	= 	Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 25);
									$transport->setUsername($username);
									$transport->setPassword($password);
									$swift 		= 	Swift_Mailer::newInstance($transport);									  
									// Create a message (subject)
									$message 	= 	new Swift_Message($subject);									 
									// attach the body of the email
									$message->setFrom($from);
									$message->setBody($html, 'text/html');
									$message->setTo($to);
									$message->addPart($text, 'text/plain');
									//$message->attach($attach);													 
									// send message 
									$recipients	=	$swift->send($message, $failures);																	
									/*Sending mails to admin intimating payment is done ends*/		
									// This will let us know how many users received this message
									$_SESSION['successPayment']	=	'cGF5bWVudFN1Y2Nlc3M=';	
									header("location:userArea.php?origin=payment");	//header("location:userArea.php?origin=payment&value=".$payment_amount."&actn=cGF5bWVudFN1Y2Nlc3M=&".$payment_currency);
									exit;
								}
								// something went wrong =(
								else
								{										
									//print_r($failures);										
									header("location:payment_new.php?errorcode=52");
									exit;
								}										
								//-------------------------------------------------------------------------------
								//								Sendgrid end
								//-------------------------------------------------------------------------------									
							}
							else
							{
								header("location:userArea.php?origin=payment");
								exit;
							}
						}
						else
						{
							$fields					=	"user_id, error_code, type";
							$values					=	"'".$user_id."','".$CODEREPONSE."','PAYMENT'";
							$this->dbInsertSingle('paybox_transaction_errors',$fields,$values);
							/*$unsubcription 	=	$this->getunsubscribed($price_centeme,$cvv,$exp,$payboxEmail);
							$unsubcription["PORTEUR"]	=	$cc;   					
							$unsubcription["NUMAPPEL"]	=	$NUMAPPEL;	
							$unsubcription["NUMTRANS"]	=	$NUMTRANS;
							$passingParam1				=	$this->arrayDbConcatEqual($unsubcription);
							$this->curlForPayment($passingParam1);*/
							header("location:payment_new.php?errorcode=".$errorcode."&payment_plan=".$params['payment_plan']);
							exit;
						}							
					}
					elseif($type == '1')//User is in payment period
					{								
						$dbPayboxQuery	 	=  "SELECT payment_expdate FROM `payment` WHERE `payment_userid` = '".$user_id."' AND `payment_expdate` >= CURDATE() AND `payment_status` = 1 ORDER BY  `payment_id` DESC";
						
						$resPaybox				=	$this->dbSelectOne($dbPayboxQuery);								
						$plan_id				=	$plan; 
						$month				    = $plan_id." month"; /*Will Get Like 1 month or 3 month etc*/
						$payment_expiry_date	=	$resPaybox['payment_expdate'];	
						$payment_expiry_date	=	date("Y-m-d", strtotime("$payment_expiry_date + $month"));							
						$payment_amount			=	$price;
						$payment_currency		=	$this->p_currency;
						$cc_image				=	$PORTEUR;	
						$trId					=	$ORDERREFERENCE;
						
						$paymentNew		    		=	$this->getpaymentfields($price_centeme,$cvv,$exp,$payboxEmail);/*For Geting 				Payment field values*/
						$paymentNew["PORTEUR"]	    =	$PORTEUR; /*Apending The Credit card image for payment*/
						$paymentNew["REFERENCE"]	=	$trId;
						$payboxNumApl				=	$NUMAPPEL;	
						$payboxNumTns				=	$NUMTRANS;
						$paymentNew["NUMAPPEL"]		=	$NUMAPPEL;
						$paymentNew["NUMTRANS"]		=	$NUMTRANS;							
						$errorcode	        		=	$CODEREPONSE;
						$paymentNew["NUMQUESTION"]	=	$NUMQUESTION;	
						
						/*Insertion of  payment table*/
						$dbFields1				= 	"payment_userid, payment_date, payment_status, payment_amount, payment_expdate, payment_no_times, payment_firstdate, payment_currency,version";
						$dbValues1				=  "'".$user_id."','".date('Y-m-d')."','1','".$payment_amount."','".$payment_expiry_date."','1','".date('Y-m-d')."','".$payment_currency."','New'";
							$this->dbInsertSingle($this->paymentTable,$dbFields1,$dbValues1);
							
						/*Insertion of  payment_transactions  table*/
							
															
							$selectQueryMax	=  "select  max(payment_id) as payment_id from payment where payment_userid ='".$user_id."' AND payment_status = '1'";
							$resultMax		= 	$GLOBALS['db']->getAll($selectQueryMax,DB_FETCHMODE_ASSOC);	
							$payId			=	$resultMax[0]['payment_id'];
															
							$paymentDetails	=	base64_encode(serialize($paymentNew));
							$dbFields2		= 	"	user_id, payment_id, details, trans_refrns_id";
							$dbValues2		=  	"'".$user_id."','".$payId."','".$paymentDetails."','".$trId."'";
							$this->dbInsertSingle('payment_transactions',$dbFields2,$dbValues2);
						/*@PAYMENT_PAYBOX TABLE INSERTION*/
						$fields					=	"user_id, card_image, cvv, exp, num_trans, num_appel, brand, status, refabonne ";
						$values					=	"'".$user_id."','".base64_encode($cc_image)."','".$cvv."','".$exp."','".$NUMTRANS."','".$NUMAPPEL."','".$brand."','ACTIVE','".$trId."'";
						$pp_id					=	$this->dbInsertSingle('payment_paybox',$fields,$values);					
						
						
						/*Insertion of  payment_cronjob tble*/
						$dbFields		=	"user_id,pp_id,plan_id,payment_expiry_date,payment_amount,payment_currency,discount_amount,status";
						//$dbValues		=  "'".$user_id."','".$pp_id."','".$plan_id."','".$payment_expiry_date."','".$payment_amount."','".$payment_currency."','".$discStatusAmount."','VALID'";
						$dbValues		=  "'".$user_id."','".$pp_id."','".$plan_id."','".$payment_expiry_date."','".$croneAmount."','".$payment_currency."','".$discStatusAmount."','VALID'";
						if($this->dbInsertSingle('payment_cronjob',$dbFields,$dbValues))   //PAYBOX_CRON TABLE
							{										
								$message	=	nl2br($parObj->_getLabenames($arrayData,'oldPaiUserSuccessMsg','name'));
								$message	=	str_replace("##",date("d-m-Y", strtotime($payment_expiry_date)),$message);
								$message	=	base64_encode($message);
								if($discStatusAmount	!=	0)
								{
									unset($_SESSION['discountAmount']);
									unset($_SESSION['payment']['discCode']);
								}
								/*----------------------------------------------------*/
								$pdfmailarray			=	array(
													 'payment_expiry_date'  => $payment_expiry_date,
													 'cc_image' 			=> $cc_image,
													 'plan'  				=> $plan_id,
													 'price' 				=> $price, 
													 'month'  				=> $month,
													 'user_id' 				=> $user_id,
													 'user_fname' 			=> $user_fname,
													 'user_lname' 			=> $user_lname,
													 'emailUser' 			=> $emailUser,
													 'transactionidnum' 	=> $trId,
													 'transOfTheDay' 		=> $transOfTheDay,
													 'NUMTRANS' 			=> $NUMTRANS
													 );
								 $this ->pdf_mailgenerator($pdfmailarray);
						/*-----------------------------------------------------------------*/
								//header("location:userArea.php?actnMode=".$message);
								header("location:userArea.php?origin=payment");										
								exit;																	
							}
						else
							{
								header("location:payment_new.php?errorcode=50");
								exit;
							}
					}					
					
				}
				else
				{
					$fields					=	"user_id, error_code, type";
					$values					=	"'".$user_id."','".$CODEREPONSE."','REGISTRATION'";
					$this->dbInsertSingle('paybox_transaction_errors',$fields,$values);
					header("location:payment_new.php?errorcode=".$errorcode."&payment_plan=".$params['payment_plan']);
					exit;
				}
				break;
			case "newUser":
				/*If Registration is successful code=00000  as a res of par_str*/
				
				if($CODEREPONSE == '00000') 
				{	
					if($type == '1')
					{	
									
						//Payment Code
							$trId						=	$ORDERREFERENCE;//$this->getInvoiceid("",$user_id);
							/*For Geting Payment field values*/							
							$paymentNew		    		=	$this->getpaymentfields($price_centeme,$cvv,$exp,$payboxEmail);
							$paymentNew["PORTEUR"]	    =	$PORTEUR; /*Apending The Credit card image for payment*/
							$paymentNew["REFERENCE"]	=	$trId;
							$payboxNumApl				=	$NUMAPPEL;
							$payboxNumTns				=	$NUMTRANS;
							$paymentNew["NUMAPPEL"]		=	$NUMAPPEL;
							$paymentNew["NUMTRANS"]		=	$NUMTRANS;
																
						/*INVOICE GENERATION CODE*/									
							
							$transactionidnum	=	$trId;																	
							$amountDescription	=	$parObj->_getLabenames($arrayData,'amountpaid','name')." ".$this->currency_symbol.$price;																		
							$amount				=	$price.$parObj->_getLabenames($arrayData,'pdfCurrency','name');															
							$cc_image			=	$PORTEUR;							
							
															
							/*@PAYMENT_PAYBOX TABLE INSERTION*/
							$fields					=	"user_id,card_image,cvv,exp,num_trans,num_appel, brand, status, refabonne";
							$values					=	"'".$user_id."','".base64_encode($cc_image)."','".$cvv."','".$exp."','".$payboxNumTns."','".$payboxNumApl."','".$brand."','ACTIVE','".$trId."' ";
							$pp_id					=	$this->dbInsertSingle('payment_paybox',$fields,$values);		
												
							$plan_id				=	$plan; 
							$month				    = 	$plan_id." month"; /*Will Get Like 1 month or 3 month etc*/
							$payment_expiry_date	=	date("Y-m-d", strtotime($month));
							$payment_amount			=	$price;
							$payment_currency		=	$this->p_currency;													
							/*Insertion of  payment tble*/
							$dbFields1				= "payment_userid, payment_date, payment_status, payment_amount, payment_expdate, payment_no_times, payment_firstdate, payment_currency,version";
							$dbValues1				=  "'".$user_id."','".date('Y-m-d')."','1','".$payment_amount."','".$payment_expiry_date."','1','".date('Y-m-d')."','".$payment_currency."','New'";
							$this->dbInsertSingle($this->paymentTable,$dbFields1,$dbValues1);
							
							// Tracking payment using Trak.io
							$qryUser	 			=	"SELECT * from user_master where user_id='".$user_id."'";
							$userDetails			= 	$GLOBALS['db']->getRow($qryUser, DB_FETCHMODE_ASSOC);
																
							$trackproperties 					= array();
							$trackproperties['email']			= $userDetails['user_email'];
							$trackproperties['UserId']			= $user_id;
							$trackproperties['payment_date']	= date('Y-m-d');
							$trackproperties['payment_amount']	= $payment_amount;
							$trackproperties['payment_expdate']	= $payment_expiry_date;
							$trackproperties['payment_currency']	= $payment_currency;
							$trackproperties['payment_mode']	= 'Manual Payment';
							//$response 							= $trakObj->trakPayment($userDetails['user_email'],$trackproperties);
							
							
							//Update the discount_users table if user uses discount
							if($discStatusAmount	!=	0)
							{
								$dbValues		=  "payment_status	=	'success'";
								$dbCond			=	"id 	=	'".$_SESSION['payment']['discUser_id']."'";
								$this->dbUpdate('discount_users',$dbValues,$dbCond); 
								unset($_SESSION['discountAmount']);
								unset($_SESSION['payment']['discCode']);										
							}										
							/*Insertion of  payment_transactions  table*/
							//$paymentNew["PORTEUR"]	    =	$PORTEUR; 
							$paymentNew["NUMAPPEL"]		=	$NUMAPPEL;
							$paymentNew["NUMTRANS"]		=	$NUMTRANS;
							//$paymentNew["NUMQUESTION"]	=	$NUMQUESTION;
							$selectQueryMax	=  "select  max(payment_id) as payment_id from payment where payment_userid ='".$user_id."' AND payment_status = '1'";
							$resultMax		= 	$GLOBALS['db']->getAll($selectQueryMax,DB_FETCHMODE_ASSOC);	
							$payId			=	$resultMax[0]['payment_id'];
																
							$paymentDetails	=	base64_encode(serialize($paymentNew));
							$dbFields2		= "	user_id, payment_id, details,trans_refrns_id";
							$dbValues2		=  "'".$user_id."','".$payId."','".$paymentDetails."','".$transactionidnum."'";
							$this->dbInsertSingle('payment_transactions',$dbFields2,$dbValues2);
							/**/							
							/*Insertion of  payment_cronjob tble*/
							$dbFields		=	"user_id,pp_id,plan_id,payment_expiry_date,payment_amount,payment_currency,status";
							$dbValues		=  "'".$user_id."','".$pp_id."','".$plan_id."','".$payment_expiry_date."','".$croneAmount."','".$payment_currency."','VALID'";								
							if($this->dbInsertSingle('payment_cronjob',$dbFields,$dbValues))   //PAYBOX_CRON TABLE
							{									
								//Chech the user for any referrance starts
								$dbRefrQuery			=  "select * from jiwok_referrals where user_id='".$user_id."'  and status='0'";
								$resRefr				=	$GLOBALS['db']->getRow($dbRefrQuery,DB_FETCHMODE_ASSOC);
								if(count($resRefr)	>	0)
								{
									$dbValues		=  "status 	=	'1'";
									$dbCond			=	"id 	=	'".$resRefr['id']."'";
									$this->dbUpdate('jiwok_referrals',$dbValues,$dbCond); 
									$this->setFreeMonths($resRefr['referrer_user_id']);	
								}											
								//Chech the user for any referrance ends
								//$from_name				=  "Jiwok";	
								//$replyto=$from_mail		=  "Info@jiwok.com";	
								//$mailto					= $user_alt_email; /*sent into alternate email */
								$period 				=	date("F j, Y", strtotime(date('Y-m-d')))." to ".date("F j, Y", strtotime($payment_expiry_date)); 
								$cc_part 				=   substr($cc, -4);/*Last 4 digit of cc*/
								$creditcard				= 	"XXXX-XXXX-XXXX-".$cc_part;
								$subject				=	$parObj->_getLabenames($arrayData,'subject','name');
								$hello_fname			=	$parObj->_getLabenames($arrayData,'hello','name')." ".$user_fname;
								$thank					=	$parObj->_getLabenames($arrayData,'thank','name');
								//$body2					=		$parObj->_getLabenames($arrayData,'body2','name');
								$body_summary			=	$parObj->_getLabenames($arrayData,'body_summary','name');
								//$body_summary_desc=		$parObj->_getLabenames($arrayData,'body_summary_desc','name');
								$body_help				= 	$parObj->_getLabenames($arrayData,'body_help','name');
								$body_help				= 	str_replace("xbrx","<br/>",$body_help);/*P break line*/
								$thanku					= 	$parObj->_getLabenames($arrayData,'thanku','name');
								$body_summary_desc		= 	$parObj->_getLabenames($arrayData,'body_summary_desc','name');
								$body_summary_desc 		=  	str_replace("xxcc",$creditcard, $body_summary_desc);/*Crefdit card num adding*/
								$price_with_symbol		=  	$this->currency_symbol.$price;
								$body_summary_desc 		=  	str_replace("xxcash",$price_with_symbol, $body_summary_desc);/*Cash adding*/
								$body_summary_desc 		=  	str_replace("xxexp",date("d-m-Y", strtotime($month)),$body_summary_desc);/*Exp date adding*/
								$body_summary_desc		=  	str_replace("xmnth",$plan,$body_summary_desc);/*month adding*/
								$acountURL 				= 	$parObj->_getLabenames($arrayData,'body_summary','name')." : <a href='".$parObj->_getLabenames($arrayData,'myAccountUrl','name')."?mode=autologin&authid=".base64_encode($user_id)."'>".$parObj->_getLabenames($arrayData,'myAccountUrl','name')."</a>"; /*Will chaneg*/	
								$body_summary_desc1		=	$parObj->_getLabenames($arrayData,'body_summary_desc1','name');
								$body_summary_desc2		=	$parObj->_getLabenames($arrayData,'body_summary_desc2','name');	
								$body_summary_desc3		=	$parObj->_getLabenames($arrayData,'body_summary_desc3','name');
								$body_summary_desc4		=	$parObj->_getLabenames($arrayData,'body_summary_desc4','name');																							
								//Pdf content starts
								$pdfHello				=	$parObj->_getLabenames($arrayData,'pdfHello','name')." ".$user_fname.",";
								$pdfWelcome				=	$parObj->_getLabenames($arrayData,'pdfWelcome','name');
								$pdfEmail				=	$parObj->_getLabenames($arrayData,'pdfEmail','name')." : ".$emailUser;										
								$pdfCardDetls1			=	$parObj->_getLabenames($arrayData,'pdfCardDetls1','name');
								$pdfCardDetls1			=	str_replace("XXXX-XXXX",$creditcard,$pdfCardDetls1);
								$pdfCardDetls1			=	str_replace("xxxx",$price,$pdfCardDetls1);
								$pdfDueDates			=	$parObj->_getLabenames($arrayData,'pdfDueDates','name')." : ".date("d-m-Y", strtotime($month));//date("F j, Y", strtotime($payment_expiry_date));																		
								$pdfCardDetls2			=	$parObj->_getLabenames($arrayData,'pdfCardDetls2','name');
								$pdfContactus			=	$parObj->_getLabenames($arrayData,'pdfContactus','name');
								$pdfContactusUrl		=	$parObj->_getLabenames($arrayData,'body_summary_desc3','name');
								$pdfGoodluck			=	$parObj->_getLabenames($arrayData,'pdfGoodluck','name');
								$pdfSiteUrl				=	$parObj->_getLabenames($arrayData,'pdfSiteUrl','name');
								$pdfInvoiceHead			=	$parObj->_getLabenames($arrayData,'pdfInvoiceHead','name');
								$pdfTransactionid		=	$parObj->_getLabenames($arrayData,'pdfTransactionid','name')." : ".str_replace(",",", ",$transactionidnum);
								$pdfImpoNoteDescription	=	$parObj->_getLabenames($arrayData,'pdfImpoNoteDescription','name');
								$pdfPass				=	$parObj->_getLabenames($arrayData,'pdfPass','name');
								$pdfPrice				=	$parObj->_getLabenames($arrayData,'pdfPrice','name');
								$pdfMonth				=	$plan." ".$parObj->_getLabenames($arrayData,'newMonthsTxt','name');
								$pdfCurrency			=	$price." ".$parObj->_getLabenames($arrayData,'pdfCurrency','name');
								$fromTo					=	$parObj->_getLabenames($arrayData,'pdfSubscriptionFrom','name')." ".date('d-m-Y')." ".$parObj->_getLabenames($arrayData,'pdfSubscriptionTo','name')." ".date("d-m-Y", strtotime($month));
								$pdfAmountpaid			=	$parObj->_getLabenames($arrayData,'pdfAmountpaid','name').$price.$parObj->_getLabenames($arrayData,'pdfCurrency','name');
								$pdfCerditCard			=	$parObj->_getLabenames($arrayData,'pdfCerditCard','name')." : ".$creditcard;
								//Pdf content ends
								
								/*INVOICE GENERATION CODE*/									
								//$transactionidnum	=	"2392052";
								//$transactionidnum	=	$this->getInvoiceid($NUMTRANS,$user_id);										
								$line1					=	"============================================================================";
								$line2					=	"============================================";
								$line3					=	"-------------------------------------";										
								$pdf=new FPDF('P','mm','A4');
								$pdf->AddPage();
								//$pdf->SetFont('Arial','B',16);		
								//$pdf->Cell(80,10,$invoice);
								$pdf->Ln(10);
								$pdf->Cell(180,10,utf8_decode(trim(strtoupper($pdfInvoiceHead))),0,1,'C');
								//$pdf->Cell(20,10,$invoice,1,1,'C');
								$pdf->SetFont('helvetica', '', 12);
								$pdf->Ln(10);	
								$pdf->Cell(180,10,trim(date('d-m-Y')),0,1,'R');
								//$pdf->Cell(40,10,date('Y-m-d'));							
								$pdf->Cell(90,10,utf8_decode($pdfHello));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($pdfWelcome));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($pdfEmail));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($pdfCardDetls1));
								$pdf->Ln(5);
								$pdf->Cell(40,10,utf8_decode($pdfDueDates));									
								$pdf->Ln(10);		
								$pdf->MultiCell(0,5,utf8_decode($pdfCardDetls2));	
								$pdf->Ln(5);
								//$pdf->Cell(40,10,utf8_decode($pdfContactus));	
								$pdf->Write(10,utf8_decode($pdfContactus),'');										
								$pdf->SetFont('','U');
								$link = $pdf->AddLink();
								$pdf->SetTextColor(0,0,255);
								$pdf->Write(10,$pdfContactusUrl,$link);
								//$pdf->Cell(40,10,utf8_decode($pdfContactusUrl),0,1,'R');
								$pdf->SetFont('helvetica', '', 12);
								$pdf->SetTextColor(0,0,0);
								$pdf->Ln(10);
								$pdf->Cell(80,10,utf8_decode($pdfGoodluck));		
								$pdf->AddPage('P','A4');
								$pdf->Ln(10);
								$pdf->Cell(90,10,utf8_decode($pdfInvoiceHead));
								$pdf->Ln(5);
								$pdf->Cell(40,10,utf8_decode($line1));
								//Only for the french pdf generation
								if($_SESSION['language']['langId']	==	2)
								{									
									$pdf->Ln(10);
									//$pdf->Cell(40,10,utf8_decode(date("d-m-Y").' - '.$transOfTheDay.'    '.$pdfTransactionid));
									$pdf->MultiCell(0,8,utf8_decode(utf8_decode(date("d-m-Y").' - '.$transOfTheDay.'    '.$pdfTransactionid)));
									//$pdf->MultiCell(140,5,utf8_decode($pdfTransactionid));
									//$pdf->Cell(40,10,utf8_decode($pdfTransactionid));
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode($line3));
									//New contents starts
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode($user_fname.' '.$user_lname));
									
									$pdf->Ln(5);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfPurchaseTitle','name').$pdfPass.' '.$pdfMonth));
									
									$htPrice	=	round($price/1.2002, 2);
									$pdf->Ln(5);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfHTAmount','name').' '.$htPrice.' '.utf8_encode(chr(128))));
									
									$pdf->Ln(5);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfTVAPercentage','name')));
									
									$pdf->Ln(5);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfVATAmount','name').' '.round($price - $htPrice,2).' '.utf8_encode(chr(128))));
									
									$pdf->Ln(5);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfTTCAmount','name').''.$price.' '.utf8_encode(chr(128))));
									
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfTaxLabel','name')));
									
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode($line3));
								}
								else
								{
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode(date("d-m-Y")));
									$pdf->MultiCell(140,5,utf8_decode($pdfTransactionid));
									//$pdf->Cell(40,10,utf8_decode($pdfTransactionid));
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode($line3));
								}
								$pdf->Ln(20);
								$pdf->MultiCell(0,5,utf8_decode($pdfImpoNoteDescription));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($pdfPass));
								$pdf->Cell(40,10,utf8_decode($pdfPrice));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($line3));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($pdfMonth));
								$pdf->Cell(40,10,utf8_decode($pdfCurrency));
								$pdf->Ln(20);
								$pdf->Cell(40,10,utf8_decode($fromTo));
								$pdf->Ln(10);
								$pdf->Cell(0,10,$line2,0,0,'C');
								$pdf->Ln(10);
								$pdf->Cell(0,10,utf8_decode($pdfAmountpaid),0,0,'C');
								$pdf->Ln(5);
								$pdf->Cell(0,10,utf8_decode($pdfCerditCard),0,0,'C');
								$pdf->Ln(10);
								$pdf->Cell(0,10,$line2,0,0,'C');		
								if(($_SESSION['paymentFrom']	==	'semideparis')	||	($_SESSION['paymentFrom']	==	'kalenji')	||	($_SESSION['paymentFrom']	==	'domyos')	||	($_SESSION['paymentFrom']	==	'parismarathon'))
									$pdffilepath	=	"../pdfgenerate/user pdf/";
								else	
									$pdffilepath	=	"pdfgenerate/user pdf/";
								$pdffile	=	$user_id."_".date('Y-m-d').".pdf";
								$pdfname	=	$pdffilepath.$pdffile;									
								$pdf->Output($pdfname,'F'); 
								/*INVOICE GENERATION CODE ENDS*/
																	
								//-------------------------------------------------------------------------------
								// Send mails using the sendgrid start										
								$text 		=	"\n";
								/*$html = "
								<html>
								  <head></head>
								  <body>
									<p>".$hello_fname."<br>
									   ".$thank."<br/><br/>".$acountURL."<br/><br/>".$body_summary_desc1."<br/>".$body_summary_desc2."<br/>".$body_summary_desc3."<br/><br/>".$body_summary_desc4."<br/><br/>http://www.jiwok.com
									</p>
								  </body>
								</html>
								";*/
								$thank				=	$parObj->_getLabenames($arrayData,'thankCrone','name');																	
								$body_summary_desc5	=	$parObj->_getLabenames($arrayData,'nameCrone','name');
								$htmlBody 			= 	"<p>".$hello_fname.",<br><br>
												".$thank."<br/><br/>".$acountURL."<br/><br/>".$body_summary_desc4."<br/>".								 						$body_summary_desc5."<br/><br/>http://www.jiwok.com<br/><br/>".        		                                                   		$$body_summary_desc1."<br/><br/>".$body_summary_desc2."<br/><br/>".																																										 														$body_summary_desc3."</p>";
								$footerText			=	$objGen->mailFooter[$_SESSION['language']['langId']];
								$html				=	$objGen->mailTpl;
								$html				=	str_replace("#MESSAGECNT#",$htmlBody,$html);
								$html				=	str_replace("#FOOTERTXT#",$footerText,$html);		
								// --from is your email address
								// --to is who you are sending your email to
								// --subject will be the subject line of your email
								$from 		= 	array('Info@jiwok.com' => 'Info@jiwok.com');
								
								$to 		= 	array($resQry['user_email']=>$user_fname);
								$subject 	= 	$parObj->_getLabenames($arrayData,'mailCronSubject','name');
								// Login credentials
								$username 	= 	'denis@jiwok.com';
								$password 	= 	'nike2000';
														 
								// Setup Swift mailer parameters
								$transport 	= 	Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 25);
								$transport->setUsername($username);
								$transport->setPassword($password);
								$swift 		= 	Swift_Mailer::newInstance($transport);
									 
								/*// Create a attachment
								$attachment['mime'] = 'application/pdf';
								$attachment['file'] = 'pdfgenerate/user pdf/'.$pdffile;
								$attachment['filename'] = $pdffile;
								$attach = Swift_Attachment::fromPath($attachment['file'], $attachment['mime'])->setFilename($attachment['filename']);*/
	  
								// Create a message (subject)
								$message 	= 	new Swift_Message($subject);
								 
								// attach the body of the email
								$message->setFrom($from);
								$message->setBody($html, 'text/html');
								$message->setTo($to);
								$message->addPart($text, 'text/plain');
								//$message->attach($attach);
									
									 
								// send message 
								if ($recipients = $swift->send($message, $failures))
								{
									/*Sending mails to admin intimating payment is done starts*/
									$hello_fname	=	"Hi,";							
									$content		=	"This user(".$user_fname." ".$user_lname.") : with emailid :".$emailUser." doing a payment trough new payment.The amount is: ".$amount." and the invoice id is: ".$transactionidnum." and refference id is : ".$NUMTRANS;
									//$content				=		"";
									$text 			= 	"\n";
									$html 			=	"
									<html>
									  <head></head>
									  <body>
										<p>".$hello_fname."<br><br/><br/>".$content."<br/><br/><br/>".$thanku."<br/>http://www.jiwok.com
										</p>
									  </body>
									</html>
									";
									// --from is your email address
									// --to is who you are sending your email to
									// --subject will be the subject line of your email
									$from 		=	array('info@jiwok.com' => 'info@jiwok.com');
									$to 		= 	array('info@jiwok.com'=>'info@jiwok.com');
									//$to = array('dileepe.reubro@gmail.com'=>'Jiwok Admin');
									$subject 	= 	'Payment process mail';
									// Login credentials
									$username 	= 	'denis@jiwok.com';
									$password 	= 	'nike2000';														 
									// Setup Swift mailer parameters
									$transport 	= 	Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 25);
									$transport->setUsername($username);
									$transport->setPassword($password);
									$swift 		= 	Swift_Mailer::newInstance($transport);												  
									// Create a message (subject)
									$message 	= 	new Swift_Message($subject);									 
									// attach the body of the email
									$message->setFrom($from);
									$message->setBody($html, 'text/html');
									$message->setTo($to);
									$message->addPart($text, 'text/plain');
									//$message->attach($attach);													 
									// send message 
									$recipients = 	$swift->send($message, $failures);																	
									/*Sending mails to admin intimating payment is done ends*/	
									// This will let us know how many users received this message
									$_SESSION['successPayment']	=	'cGF5bWVudFN1Y2Nlc3M=';
									header("location:userArea.php?origin=payment");	//header("location:userArea.php?origin=payment&value=".$payment_amount."&actn=cGF5bWVudFN1Y2Nlc3M=&".$payment_currency);
									exit;
								}
								// something went wrong =(
								else
								{										
									//print_r($failures);										
									header("location:payment_new.php?errorcode=52");
									exit;
								}										
								//-------------------------------------------------------------------------------
								//								Sendgrid end
								//-------------------------------------------------------------------------------	
								
								
								//header("location:userArea.php?origin=payment");
																
								}
							else
							{
								header("location:payment_new.php?errorcode=50");
								exit;
							}
						
					}
					else
					{
						$dbPayboxQuery	 	=  "SELECT payment_expdate FROM `payment` WHERE `payment_userid` = '".$user_id."' AND `payment_expdate` >= CURDATE() AND `payment_status` = 1 ORDER BY  `payment_id` DESC";
						$resPaybox				=	$this->dbSelectOne($dbPayboxQuery);	
						$plan_id				=	$plan; 
						$month				    = 	$plan_id." month"; /*Will Get Like 1 month or 3 month etc*/
						$payment_expiry_date	=	$resPaybox['payment_expdate'];			
						$payment_expiry_date	=	date("Y-m-d", strtotime("$payment_expiry_date + $month"));					
																	
						$payment_amount			=	$price;
						$payment_currency		=	$this->p_currency;
						$cc_image				=	$PORTEUR;	
						$trId					=	$ORDERREFERENCE;
						
						
						$paymentNew		    		=	$this->getpaymentfields($price_centeme,$cvv,$exp,$payboxEmail);/*For Geting 				Payment field values*/
						$paymentNew["PORTEUR"]	    =	$PORTEUR; /*Apending The Credit card image for payment*/
						$paymentNew["REFERENCE"]	=	$trId;
						$payboxNumApl				=	$NUMAPPEL;	
						$payboxNumTns				=	$NUMTRANS;
						$paymentNew["NUMAPPEL"]		=	$NUMAPPEL;
						$paymentNew["NUMTRANS"]		=	$NUMTRANS;							
						$errorcode	        		=	$CODEREPONSE;
						$paymentNew["NUMQUESTION"]	=	$NUMQUESTION;	
						
						/*Insertion of  payment table*/
						$dbFields1				= 	"payment_userid, payment_date, payment_status, payment_amount, payment_expdate, payment_no_times, payment_firstdate, payment_currency,version";
						$dbValues1				=  "'".$user_id."','".date('Y-m-d')."','1','".$payment_amount."','".$payment_expiry_date."','1','".date('Y-m-d')."','".$payment_currency."','New'";
							$this->dbInsertSingle($this->paymentTable,$dbFields1,$dbValues1);
							
						/*Insertion of  payment_transactions  table*/
							
															
							$selectQueryMax	=  "select  max(payment_id) as payment_id from payment where payment_userid ='".$user_id."' AND payment_status = '1'";
							$resultMax		= 	$GLOBALS['db']->getAll($selectQueryMax,DB_FETCHMODE_ASSOC);	
							$payId			=	$resultMax[0]['payment_id'];
															
							$paymentDetails	=	base64_encode(serialize($paymentNew));
							$dbFields2		= 	"	user_id, payment_id, details, trans_refrns_id";
							$dbValues2		=  	"'".$user_id."','".$payId."','".$paymentDetails."','".$trId."'";
							$this->dbInsertSingle('payment_transactions',$dbFields2,$dbValues2);
						
						/*@PAYMENT_PAYBOX TABLE INSERTION*/
						$fields					=	"user_id, card_image, cvv, exp, num_trans, num_appel, brand, status, refabonne ";
						$values					=	"'".$user_id."','".base64_encode($cc_image)."','".$cvv."','".$exp."','".$NUMTRANS."','".$NUMAPPEL."','".$brand."','ACTIVE','".$trId."'";
						$pp_id					=	$this->dbInsertSingle('payment_paybox',$fields,$values);
						/*Insertion of  payment_cronjob tble*/
						$dbFields				=	"user_id,pp_id, plan_id, payment_expiry_date, payment_amount, payment_currency, discount_amount,status";
						//$dbValues		=  "'".$user_id."','".$pp_id."','".$plan_id."','".$payment_expiry_date."','".$payment_amount."','".$payment_currency."','".$discStatusAmount."','VALID'";
						$dbValues		=  "'".$user_id."','".$pp_id."','".$plan_id."','".$payment_expiry_date."','".$croneAmount."','".$payment_currency."','".$discStatusAmount."','VALID'";
						$this->dbInsertSingle('payment_cronjob',$dbFields,$dbValues);
						if($discStatusAmount	!=	0)
						{
							unset($_SESSION['discountAmount']);
							unset($_SESSION['payment']['discCode']);	
						}																	
						$message	=	base64_encode($parObj->_getLabenames($arrayData,'54','name'));		
						//header("location:userArea.php?actnMode=".$message);
						/*------------------------------------------*/
						$pdfmailarray			=	array(
													 'payment_expiry_date'  => $payment_expiry_date,
													 'cc_image' 			=> $cc_image,
													 'plan'  				=> $plan_id,
													 'price' 				=> $price, 
													 'month'  				=> $month,
													 'user_id' 				=> $user_id,
													 'user_fname' 			=> $user_fname,
													 'user_lname' 			=> $user_lname,
													 'emailUser' 			=> $emailUser,
													 'transactionidnum' 	=> $trId,
													 'transOfTheDay' 		=> $transOfTheDay,
													 'NUMTRANS' 			=> $NUMTRANS
													 );
						$this ->pdf_mailgenerator($pdfmailarray);
						/*-------------------------------------------*/
						header("location:userArea.php?origin=payment");
					}
				}
				else
				{
					$errcode				=	$response['error_code'];
					$fields					=	"user_id, error_code, type";
					$values					=	"'".$userId."','".$errcode."','REGISTRATION'";
					$dbfun->dbInsertSingle('paybox_transaction_errors',$fields,$values);
					header("location:payment_new.php?errorcode=".$errcode."&payment_plan=".$response['payment_plan']);
					exit;
				
				}
				
				break;
			case "newPolishUser":
				/*If Authentication and payment is successful code=00000  as a res of par_str*/
				if($CODEREPONSE == '00000') 
				{						
					if(($_SESSION['paymentFrom']	==	'semideparis')	||	($_SESSION['paymentFrom']	==	'kalenji')	||	($_SESSION['paymentFrom']	==	'domyos')	||	($_SESSION['paymentFrom']	==	'parismarathon'))
					{
						require_once('../TCPDF4polish/tcpdf/config/lang/eng.php');
						require_once('../TCPDF4polish/tcpdf/tcpdf.php');
						//For custome header and footer
						require_once('../TCPDF4polish/tcpdf/mypdf.php');
					
					}
					else	
					{	
						require_once('TCPDF4polish/tcpdf/config/lang/eng.php');
						require_once('TCPDF4polish/tcpdf/tcpdf.php');
						//For custome header and footer
						require_once('TCPDF4polish/tcpdf/mypdf.php');
					
					}
					/*Sending mails to admin intimating payment is done*/					
					$transactionidnum	=	$ORDERREFERENCE;//$this->getInvoiceid("",$user_id);																	
					$amountDescription	=	$parObj->_getLabenames($arrayData,'amountpaid','name')." ".$this->currency_symbol.$price;																		
					$amount				=	$price.$parObj->_getLabenames($arrayData,'pdfCurrency','name');													
					//Check user is in payment period or not							
					//Need to extend the payment expiry date
					$plan_id			=	$plan; 
					$month				=	$plan_id." month"; /*Will Get Like 1 month or 3 month etc*/
					$payment_amount		=	$price;
					$payment_currency	=	$this->p_currency;	
					if($type	==	1)
					{						
						$dbQueryExpiryDate	=	"select * FROM payment where payment_userid='".$user_id."' AND 	payment_status = '1'	AND `payment_expdate` > CURDATE()";
						$resExpiryDate		=	$this->dbSelectOne($dbQueryExpiryDate);
						//'2015-04-29'
						$payment_expiry_date=	date('Y-m-d',strtotime($resExpiryDate[payment_expdate].' + '.$month));
						
						/*Insertion of  payment tble*/
						$dbFields1		= "payment_userid, payment_date, payment_status, payment_amount, payment_expdate, payment_no_times, payment_firstdate, payment_currency,version";
						$dbValues1	=  "'".$user_id."','".date('Y-m-d')."','1','".$payment_amount."','".$payment_expiry_date."','1','".date('Y-m-d')."','".$payment_currency."','New'";
						$this->dbInsertSingle($this->paymentTable,$dbFields1,$dbValues1); 
						
						// Tracking payment using Trak.io
							$qryUser	 		=	"SELECT * from user_master where user_id='".$user_id."'";
							$userDetails		= 	$GLOBALS['db']->getRow($qryUser, DB_FETCHMODE_ASSOC);
																
							$trackproperties 					= array();
							$trackproperties['email']			= $userDetails['user_email'];
							$trackproperties['UserId']			= $user_id;
							$trackproperties['payment_date']	= date('Y-m-d');
							$trackproperties['payment_amount']	= $payment_amount;
							$trackproperties['payment_expdate']	= $payment_expiry_date;
							$trackproperties['payment_currency']	= $payment_currency;
							$trackproperties['payment_mode']	= 'Manual Payment';
							//$response 							= $trakObj->trakPayment($userDetails['user_email'],$trackproperties);
						
					}
					else
					{
						$payment_expiry_date	=	date("Y-m-d", strtotime($month));																			
						/*Insertion of  payment tble*/
						$dbFields1		= "payment_userid, payment_date, payment_status, payment_amount, payment_expdate, payment_no_times, payment_firstdate, payment_currency,version";
						$dbValues1	=  "'".$user_id."','".date('Y-m-d')."','1','".$payment_amount."','".$payment_expiry_date."','1','".date('Y-m-d')."','".$payment_currency."','New'";
						$this->dbInsertSingle($this->paymentTable,$dbFields1,$dbValues1);
						
						// Tracking payment using Trak.io
							$qryUser	 		=	"SELECT * from user_master where user_id='".$user_id."'";
							$userDetails		= 	$GLOBALS['db']->getRow($qryUser, DB_FETCHMODE_ASSOC);
																
							$trackproperties 					= array();
							$trackproperties['email']			= $userDetails['user_email'];
							$trackproperties['UserId']			= $user_id;
							$trackproperties['payment_date']	= date('Y-m-d');
							$trackproperties['payment_amount']	= $payment_amount;
							$trackproperties['payment_expdate']	= $payment_expiry_date;
							$trackproperties['payment_currency']	= $payment_currency;
							$trackproperties['payment_mode']	= 'Manual Payment';
							//$response 							= $trakObj->trakPayment($userDetails['user_email'],$trackproperties);
						
					}					
					//Update the discount_users table if user uses discount
					if($discStatusAmount	!=	0)
					{
						$dbValues		=  "payment_status	=	'success'";
						$dbCond			=	"id 	=	'".$_SESSION['payment']['discUser_id']."'";
						$this->dbUpdate('discount_users',$dbValues,$dbCond); 
						unset($_SESSION['discountAmount']);
						unset($_SESSION['payment']['discCode']);										
					}										
					/*Insertion of  payment_transactions  table*/
					//$paymentNew["PORTEUR"]	    =	$PORTEUR; 
					$authorizationPlain["NUMAPPEL"]		=	$NUMAPPEL;

					$authorizationPlain["NUMTRANS"]		=	$NUMTRANS;
					$authorizationPlain["NUMQUESTION"]	=	$NUMQUESTION;
					$selectQueryMax	=  "select  max(payment_id) as payment_id from payment where payment_userid ='".$user_id."' AND payment_status = '1'";
					$resultMax		= 	$GLOBALS['db']->getAll($selectQueryMax,DB_FETCHMODE_ASSOC);	
					$payId			=	$resultMax[0]['payment_id'];
														
					$paymentDetails	=	base64_encode(serialize($authorizationPlain));
					$dbFields2		= "	user_id, payment_id, details,trans_refrns_id";
					$dbValues2		=  "'".$user_id."','".$payId."','".$paymentDetails."','".$transactionidnum."'";												
					if($this->dbInsertSingle('payment_transactions',$dbFields2,$dbValues2))   //PAYBOX_transaction TABLE
					{									
						//Chech the user for any referrance starts
						$dbRefrQuery			=  "select * from jiwok_referrals where user_id='".$user_id."'  and status='0'";
						$resRefr				=	$GLOBALS['db']->getRow($dbRefrQuery,DB_FETCHMODE_ASSOC);
						if(count($resRefr)	>	0)
						{
							$dbValues		=  "status 	=	'1'";
							$dbCond			=	"id 	=	'".$resRefr['id']."'";
							$this->dbUpdate('jiwok_referrals',$dbValues,$dbCond); 
							$this->setFreeMonths($resRefr['referrer_user_id']);	
						}											
						//Chech the user for any referrance ends				
						$period 			=	 date("F j, Y", strtotime(date('Y-m-d')))." to ".date("F j, Y", strtotime($payment_expiry_date)); 
						$cc_part 			=   substr($cc, -4);/*Last 4 digit of cc*/
						$creditcard			= 	"XXXX-XXXX-XXXX-".$cc_part;
						$subject			=	$parObj->_getLabenames($arrayData,'subject','name');
						$hello_fname		=	$parObj->_getLabenames($arrayData,'hello','name')." ".$user_fname;
						$thank				=	$parObj->_getLabenames($arrayData,'thank','name');
						$body_summary_desc	= 	$parObj->_getLabenames($arrayData,'body_summary_desc','name');
						$body_summary_desc 	=  	str_replace("xxcc",$creditcard, $body_summary_desc);/*Crefdit card num adding*/
						$price_with_symbol	=  	$this->currency_symbol.$price;
						$body_summary_desc 	=  	str_replace("xxcash",$price_with_symbol, $body_summary_desc);/*Cash adding*/
						$body_summary_desc 	=  	str_replace("xxexp",date("d-m-Y", strtotime($month)),$body_summary_desc);/*Exp date adding*/
						$body_summary_desc	=  	str_replace("xmnth",$plan,$body_summary_desc);/*month adding*/
						$acountURL 			= 	$parObj->_getLabenames($arrayData,'body_summary','name')." : <a href='".$parObj->_getLabenames($arrayData,'myAccountUrl','name')."?mode=autologin&authid=".base64_encode($user_id)."'>".$parObj->_getLabenames($arrayData,'myAccountUrl','name')."</a>"; /*Will chaneg*/	
						$body_summary_desc1	=	$parObj->_getLabenames($arrayData,'body_summary_desc1','name');
						$body_summary_desc2	=	$parObj->_getLabenames($arrayData,'body_summary_desc2','name');	
						$body_summary_desc3	=	$parObj->_getLabenames($arrayData,'body_summary_desc3','name');
						$body_summary_desc4	=	$parObj->_getLabenames($arrayData,'body_summary_desc4','name');	
						//Check the page lanuage. Polish mail having different mail structure. 
						if($_SESSION['language']['langId']	==	5)
						{								
																													
							//Pdf content starts
							$pdfHello			=	$user_fname.",";
							$pdfWelcome			=	$parObj->_getLabenames($arrayData,'pdfWelcome','name');								
							$pdfDueDates		=	$parObj->_getLabenames($arrayData,'pdfDueDates','name')." : ".date("d-m-Y", strtotime($month));//date("F j, Y", strtotime($payment_expiry_date));																									
							$pdfContactus		=	$parObj->_getLabenames($arrayData,'pdfContactus','name');
							$pdfContactusUrl	=	$parObj->_getLabenames($arrayData,'body_summary_desc3','name');							
							$pdfGoodluck		=	$parObj->_getLabenames($arrayData,'pdfGoodluck','name');							
							$pdfSiteUrl			=	$parObj->_getLabenames($arrayData,'pdfSiteUrl','name');							
							$pdfInvoiceHead		=	$parObj->_getLabenames($arrayData,'pdfInvoiceHead','name');							
							$pdfTransactionid	=	$parObj->_getLabenames($arrayData,'pdfTransactionid','name')." : ".str_replace(",",", ",$transactionidnum);	
												
							$pdfAmountpaid		=	$parObj->_getLabenames($arrayData,'pdfAmountpaid','name').$price.$parObj->_getLabenames($arrayData,'pdfCurrency','name');
							$pdfCerditCard		=	str_replace("###",$creditcard, $parObj->_getLabenames($arrayData,'pdfCerditCard','name'));
							
							$pdf 	= 	new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);						
							// set document information
							$pdf->SetCreator(PDF_CREATOR);
							$pdf->SetAuthor('JiWok');
							$pdf->SetTitle('DOWÓD SPRZEDAZY');
							$pdf->SetSubject('DOWÓD SPRZEDAZY');
							$pdf->SetKeywords('FAKTURA, PDF, jiwok, polish, invoice');
							
							// set default header data
							$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
							
							// set header and footer fonts
							$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
							$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
							
							// set default monospaced font
							$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
							
							//set margins
							$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
							$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
							$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
							
							//set auto page breaks
							$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
							
							//set image scale factor
							$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
							
							//set some language-dependent strings
							$pdf->setLanguageArray($l);
							
							// set default font subsetting mode
							$pdf->setFontSubsetting(true);
							
							// set font
							$pdf->SetFont('freeserif', 'B', 16);
							
							// add a page
							$pdf->AddPage();
							
							// set color for text
							$pdf->SetTextColor(0, 0, 0);
							// write the text
							$pdf->SetFont('freeserif', '', 12);
							$pdf->Cell(0, 15, '', 0, 1, 'R');
							$html = '<div style=" text-align:right;">'.$parObj->_getLabenames($arrayData,'polishPdfDateLabel','name').': '.trim(date('d-m-Y')).'<br/></div>';
							$pdf->writeHTML($html, false, 0, true, 0);
							
							$pdf->Cell(0, 15, '', 0, 1, 'C');
							$html = '<div style=" text-align:center;font-size:16pt;"><b>'.trim(strtoupper($pdfInvoiceHead)).'</b>['.str_replace(",",", ",$transactionidnum).']<br/></div>';
							$pdf->writeHTML($html, false, 0, true, 0);
							
							$pdf->Cell(0, 5, '', 0, 1, 'L');
							$html = '<div style=" font-family:freeserif;">'.$parObj->_getLabenames($arrayData,'polishPdfAddressLine1','name').': <br/>JIWOK<br/>'.$parObj->_getLabenames($arrayData,'polishPdfAddressLine3','name').'<br/>'.$parObj->_getLabenames($arrayData,'polishPdfAddressLine4','name').'<br/>'.$parObj->_getLabenames($arrayData,'polishPdfAddressLine5','name').'<br/><br/>'.trim(date('d-m-Y')).', '.$parObj->_getLabenames($arrayData,'pdfTransactionid','name').str_replace(",",", ",$transactionidnum).'<br/>===========================================================================<br/>'.$parObj->_getLabenames($arrayData,'polishPdfPlanMeans','name').'<br/><br/>'.$parObj->_getLabenames($arrayData,'polishPdfSelectedPlan','name')." ".$plan." ".$parObj->_getLabenames($arrayData,'singleMonth','name').'<br/>'.$parObj->_getLabenames($arrayData,'polishPdfPlanFrom','name')." ".trim(date('d-m-Y')).' '.$parObj->_getLabenames($arrayData,'polishPdfPlanTo','name').' '.date("d-m-Y", strtotime($payment_expiry_date)).'<br/>'.$parObj->_getLabenames($arrayData,'polishPdfPriceLabel','name').' '.$price.$parObj->_getLabenames($arrayData,'pdfCurrency','name').'<br/>===========================================================================<br/>'.$pdfAmountpaid.'<br/>'.$pdfCerditCard.'<br/>===========================================================================</div>';
							$pdf->writeHTML($html, false, 0, true, 0);
							
							$pdf->AddPage();
							$pdf->Cell(0, 15, '', 0, 1, 'L');
							$html = '<div style=" font-family:freeserif;">'.$pdfHello.'<br>'.$pdfWelcome.'<br/>'.$parObj->_getLabenames($arrayData,'polishPdfEmailLabel','name')." ".$emailUser.'<br/>'.str_replace("##",$creditcard, $parObj->_getLabenames($arrayData,'polishPdfCcCharged','name')).' '.$price.$parObj->_getLabenames($arrayData,'pdfCurrency','name').'<br/>'.$parObj->_getLabenames($arrayData,'pdfDueDates','name').date("d-m-Y", strtotime($payment_expiry_date)).'<br/><br/>'.$parObj->_getLabenames($arrayData,'polishPdfPromoCode','name').'<br/><br/>'.$parObj->_getLabenames($arrayData,'pdfContactus','name').'<br/><a href="www.jiwok.com/pl/contact+us">'.$pdfContactusUrl.'</a><br/><br/><br/>'.$parObj->_getLabenames($arrayData,'pdfGoodluck','name').'<br/><br/>'.$parObj->_getLabenames($arrayData,'polishPdfJiwokSign','name').'</div>';
							$pdf->writeHTML($html, false, 0, true, 0);
							// ---------------------------------------------------------
						}
						else
						{						
							//$body2			=	$parObj->_getLabenames($arrayData,'body2','name');
							$body_summary		=	$parObj->_getLabenames($arrayData,'body_summary','name');
							//$body_summary_desc=	$parObj->_getLabenames($arrayData,'body_summary_desc','name');
							$body_help			= 	$parObj->_getLabenames($arrayData,'body_help','name');
							$body_help			= 	str_replace("xbrx","<br/>",$body_help);/*P break line*/
							$thanku				= 	$parObj->_getLabenames($arrayData,'thanku','name');
							$body_summary_desc	= 	$parObj->_getLabenames($arrayData,'body_summary_desc','name');
							$body_summary_desc 	=  	str_replace("xxcc",$creditcard, $body_summary_desc);/*Crefdit card num adding*/
							$price_with_symbol	=  	$this->currency_symbol.$price;
							$body_summary_desc 	=  	str_replace("xxcash",$price_with_symbol, $body_summary_desc);/*Cash adding*/
							$body_summary_desc 	=  	str_replace("xxexp",date("d-m-Y", strtotime($month)),$body_summary_desc);/*Exp date adding*/
							$body_summary_desc	=  	str_replace("xmnth",$plan,$body_summary_desc);/*month adding*/
							$acountURL 			= 	$parObj->_getLabenames($arrayData,'body_summary','name')." : <a href='".$parObj->_getLabenames($arrayData,'myAccountUrl','name')."?mode=autologin&authid=".base64_encode($user_id)."'>".$parObj->_getLabenames($arrayData,'myAccountUrl','name')."</a>"; /*Will chaneg*/	
							$body_summary_desc1	=	$parObj->_getLabenames($arrayData,'body_summary_desc1','name');
							$body_summary_desc2	=	$parObj->_getLabenames($arrayData,'body_summary_desc2','name');	
							$body_summary_desc3	=	$parObj->_getLabenames($arrayData,'body_summary_desc3','name');
							$body_summary_desc4	=	$parObj->_getLabenames($arrayData,'body_summary_desc4','name');																							
							//Pdf content starts
							$pdfHello			=	$parObj->_getLabenames($arrayData,'pdfHello','name')." ".$user_fname.",";
							$pdfWelcome			=	$parObj->_getLabenames($arrayData,'pdfWelcome','name');
							$pdfEmail			=	$parObj->_getLabenames($arrayData,'pdfEmail','name')." : ".$emailUser;										
							$pdfCardDetls1		=	$parObj->_getLabenames($arrayData,'pdfCardDetls1','name');
							$pdfCardDetls1		=	str_replace("XXXX-XXXX",$creditcard,$pdfCardDetls1);
							$pdfCardDetls1		=	str_replace("xxxx",$price,$pdfCardDetls1);
							$pdfDueDates		=	$parObj->_getLabenames($arrayData,'pdfDueDates','name')." : ".date("d-m-Y", strtotime($month));//date("F j, Y", strtotime($payment_expiry_date));																		
							$pdfCardDetls2		=	$parObj->_getLabenames($arrayData,'pdfCardDetls2','name');
							$pdfContactus		=	$parObj->_getLabenames($arrayData,'pdfContactus','name');
							$pdfContactusUrl	=	$parObj->_getLabenames($arrayData,'body_summary_desc3','name');
							$pdfGoodluck		=	$parObj->_getLabenames($arrayData,'pdfGoodluck','name');
							$pdfSiteUrl			=	$parObj->_getLabenames($arrayData,'pdfSiteUrl','name');
							$pdfInvoiceHead		=	$parObj->_getLabenames($arrayData,'pdfInvoiceHead','name');
							$pdfTransactionid	=	$parObj->_getLabenames($arrayData,'pdfTransactionid','name')." : ".str_replace(",",", ",$transactionidnum);
							$pdfImpoNoteDescription=	$parObj->_getLabenames($arrayData,'pdfImpoNoteDescription','name');
							$pdfPass			=	$parObj->_getLabenames($arrayData,'pdfPass','name');
							$pdfPrice			=	$parObj->_getLabenames($arrayData,'pdfPrice','name');
							$pdfMonth			=	$plan." ".$parObj->_getLabenames($arrayData,'newMonthsTxt','name');
							$pdfCurrency		=	$price." ".$parObj->_getLabenames($arrayData,'pdfCurrency','name');
							$fromTo				=	$parObj->_getLabenames($arrayData,'pdfSubscriptionFrom','name')." ".date('d-m-Y')." ".$parObj->_getLabenames($arrayData,'pdfSubscriptionTo','name')." ".date("d-m-Y", strtotime($month));
							$pdfAmountpaid		=	$parObj->_getLabenames($arrayData,'pdfAmountpaid','name').$price.$parObj->_getLabenames($arrayData,'pdfCurrency','name');
							$pdfCerditCard		=	$parObj->_getLabenames($arrayData,'pdfCerditCard','name')." : ".$creditcard;
							//Pdf content ends						
							$pdf 	= 	new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
							
							// set document information
							$pdf->SetCreator(PDF_CREATOR);
							$pdf->SetAuthor('JiWok');
							$pdf->SetTitle('FAKTURA');
							$pdf->SetSubject('Payment invoice');
							$pdf->SetKeywords('FAKTURA, PDF, jiwok, polish, invoice');
							
							// set default header data
							$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
							
							// set header and footer fonts
							$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
							$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
							
							// set default monospaced font
							$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
							
							//set margins
							$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
							$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
							$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
							
							//set auto page breaks
							$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
							
							//set image scale factor
							$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
							
							//set some language-dependent strings
							$pdf->setLanguageArray($l);
							
							// set default font subsetting mode
							$pdf->setFontSubsetting(true);
							
							// set font
							$pdf->SetFont('freeserif', 'B', 16);
							
							// add a page
							$pdf->AddPage();
							
							// set color for text
							$pdf->SetTextColor(0, 0, 0);
							
							// write the text
							$pdf->Cell(0, 15, '', 0, 1, 'C');
							$html = '<div style=" text-align:center;">'.trim(strtoupper($pdfInvoiceHead)).'</div>';
							$pdf->writeHTML($html, false, 0, true, 0);
							
							$pdf->SetFont('freeserif', '', 12);
							$pdf->Cell(0, 15, '', 0, 1, 'R');
							$html = '<div style=" text-align:right;">'.trim(date('d-m-Y')).'</div>';
							$pdf->writeHTML($html, false, 0, true, 0);
							
							$pdf->Cell(0, 5, '', 0, 1, 'L');
							$html = '<div style=" font-family:freeserif;">'.$pdfHello.'</div>';
							$pdf->writeHTML($html, false, 0, true, 0);
							
							$pdf->Cell(0, 5, '', 0, 1, 'L');
							$html = '<div style=" font-family:freeserif;">'.$pdfWelcome.'</div>';
							$pdf->writeHTML($html, false, 0, true, 0);
							
							$pdf->Cell(0, 5, '', 0, 1, 'L');
							$html = '<div style=" font-family:freeserif;">'.$pdfEmail.'</div>';
							$pdf->writeHTML($html, false, 0, true, 0);
							
							$pdf->Cell(0, 5, '', 0, 1, 'L');
							$html = '<div style=" font-family:freeserif;">'.$pdfCardDetls1.'<br/>'.$pdfDueDates.'</div>';
							$pdf->writeHTML($html, false, 0, true, 0);
							
							$pdf->Cell(0, 5, '', 0, 1, 'L');
							$html = '<div style=" font-family:freeserif;">'.$pdfCardDetls2.'</div>';
							$pdf->writeHTML($html, false, 0, true, 0);
							
							$pdf->Cell(0, 5, '', 0, 1, 'L');
							$html = '<div style=" font-family:freeserif;">'.$pdfContactus.'<a href="www.jiwok.com/pl/contact+us">'.$pdfContactusUrl.'</a></div>';
							$pdf->writeHTML($html, false, 0, true, 0);
							
							$pdf->Cell(0, 5, '', 0, 1, 'L');
							$html = '<div style=" font-family:freeserif;">'.$pdfGoodluck.'</div>';
							$pdf->writeHTML($html, false, 0, true, 0);
							
							$pdf->AddPage();
							$pdf->Cell(0, 15, '', 0, 1, 'L');
							
							$html = '<div style=" font-family:freeserif;">'.$pdfInvoiceHead.'</div>';
							$pdf->writeHTML($html, false, 0, true, 0);
							
							$pdf->Write(5,'=======================================================================', '', 0, '', false, 0, false, false, 0);
							
							$pdf->Cell(0, 10, '', 0, 1, 'L');
							$html = '<div style=" font-family:freeserif;">'.date("d-m-Y").' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$pdfTransactionid.'</div>';
							$pdf->writeHTML($html, false, 0, true, 0);
							
							
							$pdf->Write(15,'-------------------------------------', '', 0, '', false, 0, false, false, 0);
							
							$pdf->Cell(0, 10, '', 0, 1, 'L');
							$pdf->Write(15,$pdfImpoNoteDescription, '', 0, '', false, 0, false, false, 0);
							
							
							$pdf->Cell(0, 20, '', 0, 1, 'L');
							$html = '<div style=" font-family:freeserif;">'.$pdfPass.' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp; '.$pdfPrice.'</div>';
							$pdf->writeHTML($html, false, 0, true, 0);
							
							$pdf->Write(5,'-------------------------------------', '', 0, '', false, 0, false, false, 0);
							
							$pdf->Cell(0, 5, '', 0, 1, 'L');
							$html = '<div style=" font-family:freeserif;">'.$pdfMonth.' &nbsp; &nbsp; &nbsp;  &nbsp;&nbsp;&nbsp;'.$pdfCurrency.'</div>';
							$pdf->writeHTML($html, false, 0, true, 0);
							
							
							$pdf->Cell(0, 10, '', 0, 1, 'L');
							$pdf->Write(15,$fromTo, '', 0, '', false, 0, false, false, 0);
							
							$pdf->Cell(0, 15, '', 0, 1, 'L');
							$pdf->Cell(0, 5, '============================================', 0, 2, 'C');
							
							$pdf->Cell(0, 5, '', 0, 1, 'L');
							$pdf->Cell(0, 5, $pdfAmountpaid, 0, 2, 'C');
							
							$pdf->Cell(0, 5, '', 0, 1, 'L');
							$pdf->Cell(0, 5, $pdfCerditCard, 0, 2, 'C');
							
							$pdf->Cell(0, 5, '', 0, 1, 'L');
							$pdf->Cell(0, 5, '============================================', 0, 2, 'C'); 
							// ---------------------------------------------------------
						}
						//Close and output PDF document
						$pdf->Output('pdfgenerate/user pdf/'.$user_id."_".date('Y-m-d').'.pdf', 'F');	
						/*INVOICE GENERATION CODE ENDS*/
															
						//-------------------------------------------------------------------------------
						// Send mails using the sendgrid start										
						$text = "\n";						
						$thank				=	$parObj->_getLabenames($arrayData,'thankCrone','name');																	
						$body_summary_desc5	=	$parObj->_getLabenames($arrayData,'nameCrone','name');
						$htmlBody 			= 	"<p>".$hello_fname.",<br><br>
										".$thank."<br/><br/>".$acountURL."<br/><br/>".$body_summary_desc4."<br/>".								 						$body_summary_desc5;
						if($_SESSION['language']['langId']==5)
						{
							$htmlBody	.=	"<br/><br/>http://www.jiwok.pl<br/><br/>";
						}
						else
						{
							$htmlBody	.=	"<br/><br/>http://www.jiwok.com<br/><br/>";
						}

					 $htmlBody	.=	$body_summary_desc1."<br/><br/>".$body_summary_desc2."<br/><br/>".$body_summary_desc3."</p>";
						$footerText			=	$objGen->mailFooter[$_SESSION['language']['langId']];
						$html				=	$objGen->mailTpl;
						if($_SESSION['language']['langId']==5)
						{
							$imgfr		=	"<img mc:edit='header_image' style='width: 600px;' mc:allowdesigner mc:allowtext src='http://gallery.mailchimp.com/a5cb65461711684694f82bfce/images/hearder_newsletter.png'>";
							$imgpl			=	"<img mc:edit='header_image' style='width: 600px;' mc:allowdesigner mc:allowtext src='http://www.jiwok.com/images/hearder_newsletter_pl.png'>";
							$html				=	str_replace($imgfr,$imgpl,$html);
						}
						$html				=	str_replace("#MESSAGECNT#",$htmlBody,$html);
						$html				=	str_replace("#FOOTERTXT#",$footerText,$html);		
						// --from is your email address
						// --to is who you are sending your email to
						// --subject will be the subject line of your email
						$from = array('kontakt@jiwok.pl' => 'kontakt@jiwok.pl');
						
						$to 		= 	array($resQry['user_email']=>$user_fname);
						$subject 	= 	$parObj->_getLabenames($arrayData,'mailCronSubject','name');
						// Login credentials
						$username 	= 	'denis@jiwok.com';
						$password 	= 	'nike2000';
												 
						// Setup Swift mailer parameters
						$transport 	= 	Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 25);
						$transport->setUsername($username);
						$transport->setPassword($password);
						$swift 		= 	Swift_Mailer::newInstance($transport);
							 
						/*// Create a attachment
						$attachment['mime'] = 'application/pdf';
						$attachment['file'] = 'pdfgenerate/user pdf/'.$pdffile;
						$attachment['filename'] = $pdffile;
						$attach = Swift_Attachment::fromPath($attachment['file'], $attachment['mime'])->setFilename($attachment['filename']);*/

						// Create a message (subject)
						$message = new Swift_Message($subject);
						 
						// attach the body of the email
						$message->setFrom($from);
						$message->setBody($html, 'text/html');
						$message->setTo($to);
						$message->addPart($text, 'text/plain');
						//$message->attach($attach);
							
							 
						// send message 
						if ($recipients = $swift->send($message, $failures))
						{
							/*Sending mails to admin intimating payment is done starts*/
							$hello_fname		=		"Hi,";							
							$content			=		"This user(".$user_fname." ".$user_lname.") : with emailid :".$emailUser." doing a payment trough new payment.The amount is: ".$amount." and the invoice id is: ".$transactionidnum." and refference id is : ".$NUMTRANS;
							//$content				=		"";
							$text = "\n";
							$html = "
							<html>
							  <head></head>
							  <body>
								<p>".$hello_fname."<br><br/><br/>".$content."<br/><br/><br/>".$thanku;
							if($_SESSION['language']['langId']==5)
							{
								$html	.=	"<br/><br/>http://www.jiwok.pl<br/><br/>";
							}
							else
							{
								$html	.=	"<br/><br/>http://www.jiwok.com<br/><br/>";
							}
								
							$html	.=	"</p>
							 </body>
							</html>
							";
							// --from is your email address
							// --to is who you are sending your email to
							// --subject will be the subject line of your email
							$from 		= 	array('kontakt@jiwok.pl' => 'kontakt@jiwok.pl');
							$to 		= 	array('info@jiwok.com'=>'info@jiwok.com','kontakt@jiwok.pl' => 'kontakt@jiwok.pl');
							//$to = array('dileepe.reubro@gmail.com'=>'Jiwok Admin');
							$subject 	= 	'Payment process mail';
							// Login credentials
							$username 	= 	'denis@jiwok.com';
							$password 	= 	'nike2000';														 
							// Setup Swift mailer parameters
							$transport 	= 	Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 25);
							$transport->setUsername($username);
							$transport->setPassword($password);
							$swift 		= 	Swift_Mailer::newInstance($transport);												  
							// Create a message (subject)
							$message 	= 	new Swift_Message($subject);									 
							// attach the body of the email
							$message	->	setFrom($from);
							$message	->	setBody($html, 'text/html');
							$message	->	setTo($to);
							$message	->	addPart($text, 'text/plain');
							//$message->attach($attach);													 
							// send message 
							$recipients =	$swift->send($message, $failures);																	
							/*Sending mails to admin intimating payment is done ends*/	
							// This will let us know how many users received this message
							$_SESSION['successPayment']	=	'cGF5bWVudFN1Y2Nlc3M=';
							header("location:userArea.php?origin=payment");	//header("location:userArea.php?origin=payment&value=".$payment_amount."&actn=cGF5bWVudFN1Y2Nlc3M=&".$payment_currency);
							exit;
						}
						// something went wrong =(
						else
						{										
							//print_r($failures);										
							header("location:payment_new.php?errorcode=52");
							exit;
						}										
						//-------------------------------------------------------------------------------
						//								Sendgrid end
						//-------------------------------------------------------------------------------									
					}
					else
					{
						header("location:payment_new.php?errorcode=50");
						exit;
					}					
				}
				else
				{					
					$fields					=	"user_id, error_code, type";
					$values					=	"'".$user_id."','".$CODEREPONSE."','PAYMENT'";
					$this->dbInsertSingle('paybox_transaction_errors',$fields,$values);
					header("location:payment_new.php?errorcode=".$errorcode."&payment_plan=".$params['payment_plan']);
					exit;
				}
				break;						
			
			default:
						die("Nothing");
						break;
		}
	}
	function getUserBrand($uid){
	
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
	function getInvoiceid($payid,$userId){
		
		$reffPara			=	$payid;
		if(isset($_SESSION['payment']['discUser_id']))
		{
			if($reffPara != "")
				$reffPara			=	$reffPara.','.$_SESSION['payment']['discUser_id'];
			else
				$reffPara			=	$_SESSION['payment']['discUser_id'];	
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
		}
		else
		{
			$reffPara			=	$reffPara.','.'0';
		}
		
		if($userId != '')
		{
		$reffPara			=	$reffPara.','.$userId;
		}
		else
		{
		$reffPara			=	$reffPara.','.'0';
		}
		
		if(isset($_SESSION['payment']['discId']))
		{
			$reffPara			=	$reffPara.','.$_SESSION['payment']['discId'];
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
			$res							=	$this->dbSelectOne($dbQuery);
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
		
		$brand					=	$this->getUserBrand($userId);
		//Find user's paybox email
		$dbQuery	 		=  "select paybox_email from user_master where user_id	='".$userId."'";
		$res				=	$this->dbSelectOne($dbQuery);
		$payBoxEmail		=	$res['paybox_email'];		
		$reffPara1			=	$reffPara.",0,0,0,0,0,".$brand.",".$this->p_currency.",".$lanVar.",".date('d-m-Y').",".$payBoxEmail; // order reference for euro payment
		$order_reference	=	$reffPara1;
		return $order_reference;
	
	}
	function pdf_mailgenerator($userarray){
		
								
								$payment_expiry_date	=	$userarray['payment_expiry_date'];
								$cc						=	$userarray['cc_image'];
								$user_fname				=	$userarray['user_fname'];
								$price					=	$userarray['price'];
								$month					=	$userarray['month'];
								$plan					=	$userarray['plan'];
								$user_id				=	$userarray['user_id'];								
								$emailUser				=	$userarray['emailUser'];
								$transactionidnum		=	$userarray['transactionidnum'];
								$transOfTheDay			=	$userarray['transOfTheDay'];
								$user_lname				=	$userarray['user_lname'];
								$NUMTRANS				=	$userarray['NUMTRANS'];
		$objGen   		=	new General();
		if($_SERVER['HTTP_HOST'] == "www.jiwok.com")
		{
			$_SESSION['paymentFrom']	=	'jiwok';
		}
		else
		{
			$_SESSION['paymentFrom']	=	$_SESSION['brand'];
		}
		/*Getting the user brand*/
		
		$brand			=	$this->getUserBrand($_SESSION['user']['userId']); 
		
		$parObj 	    =   new Contents('payment_new.php');
		if(($_SESSION['paymentFrom']	==	'semideparis')	||	($_SESSION['paymentFrom']	==	'kalenji')	||	($_SESSION['paymentFrom']	==	'domyos')	||	($_SESSION['paymentFrom']	==	'parismarathon'))
		{
			$xmlPath        = 	"../templates/".$_SESSION['paymentFrom']."/".$_SESSION['language']['xml'];
			include_once "../Swift/lib/swift_required.php";
			$parsedXml		=	simplexml_load_file($xmlPath);
			$normalArray	= 	$this	->	xml2array($parsedXml,'');		
			//$arrayUser 	= 	$this	->	xml2array($arrayData['customer'],'');			
			$i	=	0;
			foreach($normalArray['new_payment'] as $key=>$arrayDatas)
			{
				$lower1[name]		=	$key;
				$lower2[$i][value]	=	$arrayDatas;
				$lower2[$i][attr]	=	$lower1;
				$i++;
			}
			$arrayData		=	$lower2;	
					
		}
		else
		{
			$xmlPath        = 	$_SESSION['language']['xml'];		
			$returnData		= 	$parObj->_getTagcontents($xmlPath,'new_payment','label');
			$arrayData		= 	$returnData['general'];
			include_once "Swift/lib/swift_required.php";
		}		
		/*xml ends*/	
		
								//$from_name				=  "Jiwok";	
								//$replyto=$from_mail		=  "Info@jiwok.com";	
								//$mailto					= $user_alt_email; /*sent into alternate email */
								$period 				=	date("F j, Y", strtotime(date('Y-m-d')))." to ".date("F j, Y", strtotime($payment_expiry_date)); 
								$cc_part 				=   substr($cc, -4);/*Last 4 digit of cc*/
								$creditcard				= 	"XXXX-XXXX-XXXX-".$cc_part;
								$subject				=	$parObj->_getLabenames($arrayData,'subject','name');
								$hello_fname			=	$parObj->_getLabenames($arrayData,'hello','name')." ".$user_fname;
								$thank					=	$parObj->_getLabenames($arrayData,'thank','name');
								//$body2					=		$parObj->_getLabenames($arrayData,'body2','name');
								$body_summary			=	$parObj->_getLabenames($arrayData,'body_summary','name');
								//$body_summary_desc=		$parObj->_getLabenames($arrayData,'body_summary_desc','name');
								$body_help				= 	$parObj->_getLabenames($arrayData,'body_help','name');
								$body_help				= 	str_replace("xbrx","<br/>",$body_help);/*P break line*/
								$thanku					= 	$parObj->_getLabenames($arrayData,'thanku','name');
								$body_summary_desc		= 	$parObj->_getLabenames($arrayData,'body_summary_desc','name');
								$body_summary_desc 		=  	str_replace("xxcc",$creditcard, $body_summary_desc);/*Crefdit card num adding*/
								$price_with_symbol		=  	$this->currency_symbol.$price;
								$body_summary_desc 		=	str_replace("xxcash",$price_with_symbol, $body_summary_desc);/*Cash adding*/
								//$body_summary_desc 		=  	str_replace("xxexp",date("d-m-Y", strtotime($month)),$body_summary_desc);/*Exp date adding*/
								$body_summary_desc 		=  	str_replace("xxexp",$payment_expiry_date,$body_summary_desc);
								$body_summary_desc		=  	str_replace("xmnth",$plan,$body_summary_desc);/*month adding*/
								$acountURL 				= 	$parObj->_getLabenames($arrayData,'body_summary','name')." : <a href='".$parObj->_getLabenames($arrayData,'myAccountUrl','name')."?mode=autologin&authid=".base64_encode($user_id)."'>".$parObj->_getLabenames($arrayData,'myAccountUrl','name')."</a>"; /*Will chaneg*/	
								$body_summary_desc1		=	$parObj->_getLabenames($arrayData,'body_summary_desc1','name');
								$body_summary_desc2		=	$parObj->_getLabenames($arrayData,'body_summary_desc2','name');	
								$body_summary_desc3		=	$parObj->_getLabenames($arrayData,'body_summary_desc3','name');
								$body_summary_desc4		=	$parObj->_getLabenames($arrayData,'body_summary_desc4','name');																	
								//Pdf content starts
								$pdfHello				=	$parObj->_getLabenames($arrayData,'pdfHello','name')." ".$user_fname.",";
								$pdfWelcome				=	$parObj->_getLabenames($arrayData,'pdfWelcome','name');
								$pdfEmail				=	$parObj->_getLabenames($arrayData,'pdfEmail','name')." : ".$emailUser;										
								$pdfCardDetls1			=	$parObj->_getLabenames($arrayData,'pdfCardDetls1','name');
								$pdfCardDetls1			=	str_replace("XXXX-XXXX",$creditcard,$pdfCardDetls1);
								$pdfCardDetls1			=	str_replace("xxxx",$price,$pdfCardDetls1);
								//$pdfDueDates			=	$parObj->_getLabenames($arrayData,'pdfDueDates','name')." : ".date("d-m-Y", strtotime($month));	
								$pdfDueDates			=	$parObj->_getLabenames($arrayData,'pdfDueDates','name')." : ".$payment_expiry_date;																							
								$pdfCardDetls2			=	$parObj->_getLabenames($arrayData,'pdfCardDetls2','name');
								$pdfContactus			=	$parObj->_getLabenames($arrayData,'pdfContactus','name');
								$pdfContactusUrl		=	$parObj->_getLabenames($arrayData,'body_summary_desc3','name');
								$pdfGoodluck			=	$parObj->_getLabenames($arrayData,'pdfGoodluck','name');
								$pdfSiteUrl				=	$parObj->_getLabenames($arrayData,'pdfSiteUrl','name');
								$pdfInvoiceHead			=	$parObj->_getLabenames($arrayData,'pdfInvoiceHead','name');
								$pdfTransactionid		=	$parObj->_getLabenames($arrayData,'pdfTransactionid','name')." : ".str_replace(",",", ",$transactionidnum);
								$pdfImpoNoteDescription	=	$parObj->_getLabenames($arrayData,'pdfImpoNoteDescription','name');
								$pdfPass				=	$parObj->_getLabenames($arrayData,'pdfPass','name');
								$pdfPrice				=	$parObj->_getLabenames($arrayData,'pdfPrice','name');
								$pdfMonth				=	$plan." ".$parObj->_getLabenames($arrayData,'newMonthsTxt','name');
								$pdfCurrency			=	$price." ".$parObj->_getLabenames($arrayData,'pdfCurrency','name');
								$fromTo					=	$parObj->_getLabenames($arrayData,'pdfSubscriptionFrom','name')." ".date('Y-m-d')." ".$parObj->_getLabenames($arrayData,'pdfSubscriptionTo','name')." ".$payment_expiry_date;
								$pdfAmountpaid			=	$parObj->_getLabenames($arrayData,'pdfAmountpaid','name').$price.$parObj->_getLabenames($arrayData,'pdfCurrency','name');
								$pdfCerditCard			=	$parObj->_getLabenames($arrayData,'pdfCerditCard','name')." : ".$creditcard;
							//Pdf content ends
								
								$line1					=	"============================================================================";
								$line2					=	"============================================";
								$line3					=	"-------------------------------------";										
								$pdf	=	new FPDF('P','mm','A4');
								$pdf->AddPage();
								//$pdf->SetFont('Arial','B',16);		
								//$pdf->Cell(80,10,$invoice);
								$pdf->Ln(10);
								$pdf->Cell(180,10,utf8_decode(trim(strtoupper($pdfInvoiceHead))),0,1,'C');
								//$pdf->Cell(20,10,$invoice,1,1,'C');
								$pdf->SetFont('helvetica', '', 12);
								$pdf->Ln(10);	
								$pdf->Cell(180,10,trim(date('d-m-Y')),0,1,'R');
								//$pdf->Cell(40,10,date('Y-m-d'));							
								$pdf->Cell(90,10,utf8_decode($pdfHello));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($pdfWelcome));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($pdfEmail));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($pdfCardDetls1));
								$pdf->Ln(5);
								$pdf->Cell(40,10,utf8_decode($pdfDueDates));									
								$pdf->Ln(10);		
								$pdf->MultiCell(0,5,utf8_decode($pdfCardDetls2));	
								$pdf->Ln(5);
								//$pdf->Cell(40,10,utf8_decode($pdfContactus));	
								$pdf->Write(10,utf8_decode($pdfContactus),'');										
								$pdf->SetFont('','U');
								$link	=	$pdf->AddLink();
								$pdf->SetTextColor(0,0,255);
								$pdf->Write(10,$pdfContactusUrl,$link);
								//$pdf->Cell(40,10,utf8_decode($pdfContactusUrl),0,1,'R');
								$pdf->SetFont('helvetica', '', 12);	
								$pdf->SetTextColor(0,0,0);										
								$pdf->Ln(10);
								$pdf->Cell(80,10,utf8_decode($pdfGoodluck));		
								$pdf->AddPage('P','A4');
								$pdf->Ln(10);
								$pdf->Cell(90,10,utf8_decode($pdfInvoiceHead));
								$pdf->Ln(5);
								$pdf->Cell(40,10,utf8_decode($line1));
								//Only for the french pdf generation
								if($_SESSION['language']['langId']	==	2)
								{									
									$pdf->Ln(10);
									//$pdf->Cell(40,10,utf8_decode(date("d-m-Y").' - '.$transOfTheDay.'    '.$pdfTransactionid));
									$pdf->MultiCell(0,8,utf8_decode(utf8_decode(date("d-m-Y").' - '.$transOfTheDay.'    '.$pdfTransactionid)));
									//$pdf->MultiCell(140,5,utf8_decode($pdfTransactionid));
									//$pdf->Cell(40,10,utf8_decode($pdfTransactionid));
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode($line3));
									//New contents starts
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode($user_fname.' '.$user_lname));
									
									$pdf->Ln(5);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfPurchaseTitle','name').$pdfPass.' '.$pdfMonth));
									
									$htPrice	=	round($price/1.2002, 2);
									$pdf->Ln(5);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfHTAmount','name').' '.$htPrice.' '.utf8_encode(chr(128))));
									
									$pdf->Ln(5);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfTVAPercentage','name')));
									
									$pdf->Ln(5);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfVATAmount','name').' '.round($price - $htPrice,2).' '.utf8_encode(chr(128))));
									
									$pdf->Ln(5);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfTTCAmount','name').''.$price.' '.utf8_encode(chr(128))));
									
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode($parObj->_getLabenames($arrayData,'pdfTaxLabel','name')));
									
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode($line3));
								}
								else
								{
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode(date("d-m-Y")));
									$pdf->MultiCell(140,5,utf8_decode($pdfTransactionid));
									//$pdf->Cell(40,10,utf8_decode($pdfTransactionid));
									$pdf->Ln(10);
									$pdf->Cell(40,10,utf8_decode($line3));
								}
								$pdf->Ln(20);
								$pdf->MultiCell(0,5,utf8_decode($pdfImpoNoteDescription));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($pdfPass));
								$pdf->Cell(40,10,utf8_decode($pdfPrice));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($line3));
								$pdf->Ln(10);
								$pdf->Cell(40,10,utf8_decode($pdfMonth));
								$pdf->Cell(40,10,utf8_decode($pdfCurrency));
								$pdf->Ln(20);
								$pdf->Cell(40,10,utf8_decode($fromTo));
								$pdf->Ln(10);
								$pdf->Cell(0,10,$line2,0,0,'C');
								$pdf->Ln(10);
								$pdf->Cell(0,10,utf8_decode($pdfAmountpaid),0,0,'C');
								$pdf->Ln(5);
								$pdf->Cell(0,10,utf8_decode($pdfCerditCard),0,0,'C');
								$pdf->Ln(10);
								$pdf->Cell(0,10,$line2,0,0,'C');		
								if(($_SESSION['paymentFrom']	==	'semideparis')	||	($_SESSION['paymentFrom']	==	'kalenji')	||	($_SESSION['paymentFrom']	==	'domyos')	||	($_SESSION['paymentFrom']	==	'parismarathon'))
									$pdffilepath	=	"../pdfgenerate/user pdf/";
								else
									$pdffilepath	=	"pdfgenerate/user pdf/";
								$pdffile	=	$user_id."_".date('Y-m-d').".pdf";
								$pdfname	=	$pdffilepath.$pdffile;									
								$pdf->Output($pdfname,'F');  
								/*INVOICE GENERATION CODE ENDS*/
																	
								//-------------------------------------------------------------------------------
								// Send mails using the sendgrid
								//								Sendgrid start
								//------------------------------------------------------------------------------- 
								//include_once "Swift/lib/swift_required.php";
								/*
									 * Create the body of the message (a plain-text and an HTML version).
									 * --text is your plain-text email
									 * --html is your html version of the email
									 * If the reciever is able to view html emails then only the html
									 * email will be displayed
								 */ 
								$text 	=	"\n";
								/*$html = "
								<html>
								  <head></head>
								  <body>
									<p>".$hello_fname."<br><br>
									   ".$thank."<br/><br/>".$acountURL."<br/><br/>".$body_summary_desc1."<br/>".$body_summary_desc2."<br/>".$body_summary_desc3."<br/><br/>".$body_summary_desc4."<br/><br/>http://www.jiwok.com
									</p>
								  </body>
								</html>
								";*/
								
								$thank				=	$parObj->_getLabenames($arrayData,'thankCrone','name');																	
								$body_summary_desc5	=	$parObj->_getLabenames($arrayData,'nameCrone','name');
								
								
								$htmlBody			 =	"<p>".$hello_fname.",<br><br>
											".$thank."<br/><br/>".$acountURL."<br/><br/>".$body_summary_desc4."<br/>																																																															 														<br/>".$body_summary_desc5."<br/><br/>http://www.jiwok.com<br/><br/>".$$body_summary_desc1."<br/><br/>".$body_summary_desc2."<br/><br/>".																		$body_summary_desc3."</p>";	
								$footerText			=	$objGen->mailFooter[$_SESSION['language']['langId']];
								$html				=	$objGen->mailTpl;
								$html				=	str_replace("#MESSAGECNT#",$htmlBody,$html);
								$html				=	str_replace("#FOOTERTXT#",$footerText,$html);														
								// --from is your email address
								// --to is who you are sending your email to
								// --subject will be the subject line of your email
								$from 		= 	array('info@jiwok.com' => 'info@jiwok.com');
								
								$to 		= 	array($emailUser =>$user_fname);
								$subject 	= 	$parObj->_getLabenames($arrayData,'mailCronSubject','name');
								// Login credentials
								$username 	= 	'denis@jiwok.com';
								$password 	= 	'nike2000';
														 
								// Setup Swift mailer parameters
								$transport 	= 	Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 25);
								$transport->setUsername($username);
								$transport->setPassword($password);
								$swift 		= 	Swift_Mailer::newInstance($transport);
									 
								/*// Create a attachment
								$attachment['mime'] = 'application/pdf';
								$attachment['file'] = 'pdfgenerate/user pdf/'.$pdffile;
								$attachment['filename'] = $pdffile;
								$attach = Swift_Attachment::fromPath($attachment['file'], $attachment['mime'])->setFilename($attachment['filename']);*/
	  
								// Create a message (subject)
								$message 	= 	new Swift_Message($subject);
								 
								// attach the body of the email
								$message->setFrom($from);
								$message->setBody($html, 'text/html');
								$message->setTo($to);
								$message->addPart($text, 'text/plain');
								//$message->attach($attach);
									
									 
								// send message 
								if ($recipients 	= 	$swift->send($message, $failures))
								{
									/*Sending mails to admin intimating payment is done starts*/
									$hello_fname		=	"Hi,";							
									$content			=	"This user(".$user_fname." ".$user_lname.") : with emailid :".$emailUser." doing a payment trough new payment.The amount is: ".$amount." and the invoice id is: ".$transactionidnum." and refference id is : ".$NUMTRANS;										
									$text 				=	"\n";
									$html 				= 	"
									<html>
									  <head></head>
									  <body>
										<p>".$hello_fname."<br><br/><br/>".$content."<br/><br/><br/>".$thanku."<br/>http://www.jiwok.com
										</p>
									  </body>
									</html>";
									// --from is your email address
									// --to is who you are sending your email to
									// --subject will be the subject line of your email
									//$from = array($emailUser => $user_fname);
									$from 		= 	array('info@jiwok.com' => 'info@jiwok.com');
									$to 		= 	array('info@jiwok.com'=>'info@jiwok.com');										
									$subject 	= 	'Payment process mail';
									// Login credentials
									$username 	= 	'denis@jiwok.com';
									$password 	= 	'nike2000';														 
									// Setup Swift mailer parameters
									$transport 	= 	Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 25);
									$transport->setUsername($username);
									$transport->setPassword($password);
									$swift 		= 	Swift_Mailer::newInstance($transport);									  
									// Create a message (subject)
									$message 	= 	new Swift_Message($subject);									 
									// attach the body of the email
									$message->setFrom($from);
									$message->setBody($html, 'text/html');
									$message->setTo($to);
									$message->addPart($text, 'text/plain');
									//$message->attach($attach);													 
									// send message 
									$recipients	=	$swift->send($message, $failures);																	
									/*Sending mails to admin intimating payment is done ends*/		
									// This will let us know how many users received this message
									$_SESSION['successPayment']	=	'cGF5bWVudFN1Y2Nlc3M=';	
									header("location:userArea.php?origin=payment");	//header("location:userArea.php?origin=payment&value=".$payment_amount."&actn=cGF5bWVudFN1Y2Nlc3M=&".$payment_currency);
									exit;
								}
								// something went wrong =(
								else
								{										
									//print_r($failures);										
									header("location:payment_new.php?errorcode=52");
									exit;
								}										
								//-------------------------------------------------------------------------------
								//								Sendgrid end
								//-------------------------------------------------------------------------------									

	}
}

?>