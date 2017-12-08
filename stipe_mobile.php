<?php
//userid,token,planid,email
$data = $_POST;
$userId = $data['userid'];
$token = $data['token'];
$changedplanId=$data['planid'];
$stripe_email = $data['email'];
$brand  = 'android';
$selectQuery1	=  "select  * from payment_paybox where user_id ='".$userId."' and status='ACTIVE'";
$result1		= 	$GLOBALS['db']->getAll($selectQuery1,DB_FETCHMODE_ASSOC);
$selectQuery1stripe	=  "select  * from stripe_payment where user_id ='".$userId."' and status='ACTIVE'";
$striperesult1		= 	$GLOBALS['db']->getAll($selectQuery1stripe,DB_FETCHMODE_ASSOC);

if(sizeof($result1)>0)
{
$count_result     = sizeof($result1) ;
}
else if(sizeof($striperesult1)>0)
{ 
$count_result     = sizeof($striperesult1) ;
$selectQuerystripe	=	"SELECT payment_expdate FROM `payment` WHERE `payment_userid` = '".$userId."' AND `payment_expdate` > CURDATE() AND `payment_status` = 1 AND version='stripe'";
$result_stripe		= 	$GLOBALS['db']->getAll($selectQuerystripe,DB_FETCHMODE_ASSOC); 
$plan_next_paydate	=	$result_stripe[0]['payment_expdate'];// to extend payment only at expiry date of current plan end 
}
//For oldpayment subcription perion user popup message
$selectQuery2	=	"SELECT payment_expdate FROM `payment` WHERE `payment_userid` = '".$userId."' AND `payment_expdate` > CURDATE() AND `payment_status` = 1 AND version='New'";
$result2		= 	$GLOBALS['db']->getAll($selectQuery2,DB_FETCHMODE_ASSOC); 
	
$selectQuery3	=  "select  * from payment where payment_userid ='".$userId."' AND payment_status = '1'";
$result3		= 	$GLOBALS['db']->getAll($selectQuery3,DB_FETCHMODE_ASSOC);
$selectQuery4	=	"SELECT COUNT(*) AS count FROM `payment` WHERE `payment_userid` = '".$userId."' AND `payment_expdate` > CURDATE() AND `payment_status` = 1 ";
//$res	=& $GLOBALS['db']->getOne($select_query, $userId);
$result4		= 	$GLOBALS['db']->getAll($selectQuery4,DB_FETCHMODE_ASSOC);

///stripe payment code

$newArray =	array();
$newArray['token'] = $token;

					  $customer = \Stripe\Customer::create(array(
					  "source"  => $token,
					  "plan"    => $changedplanId,					 
					  "email"   => $stripe_email,
					   )
				      );
				      
						if(!$customer->id)
							{
								
							   $paymentDetails	=	base64_encode(serialize($customer));
								$temparray = array();
								$temparray['user_id'] = $userId;
								$temparray['data'] = $paymentDetails;
								$chkNewEntry = $this->_insertRecord("stripe_transaction_errors",$temparray);
								
							}
			$xmlPath        = 	$_SESSION['language']['xml'];		
			$returnData		= 	$parObj->_getTagcontents($xmlPath,'new_payment','label');
			$arrayData		= 	$returnData['general'];
			/*Getting the user Details*/
		$sqlQry		    =	"SELECT * FROM `user_master` where user_id='".$user_id."'";
		$resQry			=	$this->dbSelectOne($sqlQry);
		$user_alt_email	=	$resQry['user_alt_email'];
		$user_fname		=	$resQry['user_fname'];
		$user_lname		=	$resQry['user_lname'];
		$emailUser      = 	$resQry['user_email'];	
		$payboxEmail	=	$resQry['paybox_email'];	
		$plan		 		=	$changedplanId;
		$currencyCode = '2';
		$dbQuery	 		=	"select * from jiwok_payment_plan where plan_id='".$plan."' and plan_currency='".$currencyCode."'";
		$res				=	$this->dbSelectOne($dbQuery);
		$croneAmount		=	$res['plan_amount'];
		$price		 					=	$res['plan_amount'];
		$price_centeme					=	$price * 100;
		if($customer->id && $customer->subscriptions->data[0]->id) 
				{
							if($result4[0]['count'] > 0)
							{
								/////
								 if($customer->subscriptions->data[0]->id)
						  { 
							$fields	=	"user_id,stripe_user_token,status,payment_email,customer_id, subsciption_id, plan_id,brand";
							$values	=	"'".$user_id."','".$token."','ACTIVE','".$stripe_email."','".$customer->id."','".$customer->subscriptions->data[0]->id."','".$changedplanId."','".$brand."'";
							$pp_id	=	$this->dbInsertSingle('stripe_payment',$fields,$values);
						 }
							
						$dbPayboxQuery	 	=  "SELECT payment_expdate FROM `payment` WHERE `payment_userid` = '".$user_id."' AND `payment_expdate` >= CURDATE() AND `payment_status` = 1 ORDER BY  `payment_id` DESC";
						$resPaybox				=	$this->dbSelectOne($dbPayboxQuery);								
						$plan_id				=	$plan; 
						$month				    = $plan_id." month"; /*Will Get Like 1 month or 3 month etc*/
						$payment_expiry_date	=	$resPaybox['payment_expdate'];	
						$payment_expiry_date	=	date("Y-m-d", strtotime("$payment_expiry_date + $month"));	
									
							$payment_amount			=	$price;
							$payment_currency		=	"Euro";													
							/*Insertion of  payment tble*/
							$dbFields1				= "payment_userid, payment_date, payment_status, payment_amount, payment_expdate, payment_no_times, payment_firstdate, payment_currency,version";
							$dbValues1				=  "'".$user_id."','".date('Y-m-d')."','1','".$payment_amount."','".$payment_expiry_date."','1','".date('Y-m-d')."','".$payment_currency."','stripe_mobile'";
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
																
							/*Insertion of  payment_transactions  table*/
							
							$selectQueryMax	=  "select  max(payment_id) as payment_id from payment where payment_userid ='".$user_id."' AND payment_status = '1'";
							$resultMax		= 	$GLOBALS['db']->getAll($selectQueryMax,DB_FETCHMODE_ASSOC);	
							$payId			=	$resultMax[0]['payment_id'];
							$paymentDetails	=	base64_encode(serialize($customer));
							
							$dbFields2		= "	user_id, payment_id, details,status,trans_refrns_id,brand,balance";
							$dbValues2		=  "'".$user_id."','".$payId."','".$paymentDetails."','PAID','','".$brand."','".$price."'";
							$this->dbInsertSingle('stripe_transaction',$dbFields2,$dbValues2);
												
							/*Insertion of  payment_cronjob tble*/
							$dbFields		=	"user_id,pp_id,plan_id,payment_expiry_date,payment_amount,payment_currency,status,customer_id,subsciption_id";
							$dbValues		=  "'".$user_id."','".$pp_id."','".$plan."','".$payment_expiry_date."','".$croneAmount."','".$payment_currency."','VALID','".$customer->id."','".$customer->subscriptions->data[0]->id."'";								
							if($this->dbInsertSingle('stripe_auto_renewal',$dbFields,$dbValues))   //stripe cron TABLE
							{	
																		
								
								$period 				=	date("F j, Y", strtotime(date('Y-m-d')))." to ".date("F j, Y", strtotime($payment_expiry_date)); 
								$cc_part 				=   substr($cc, -4);/*Last 4 digit of cc*/
								$creditcard				= 	"XXXX-XXXX-XXXX-".$cc_part;
								$subject				=	$parObj->_getLabenames($arrayData,'subject','name');
								$hello_fname			=	$parObj->_getLabenames($arrayData,'hello','name')." ".$user_fname;
								$thank					=	$parObj->_getLabenames($arrayData,'thank','name');
								$body_summary			=	$parObj->_getLabenames($arrayData,'body_summary','name');
								$body_help				= 	$parObj->_getLabenames($arrayData,'body_help','name');
								$body_help				= 	str_replace("xbrx","<br/>",$body_help);/*P break line*/
								$thanku					= 	$parObj->_getLabenames($arrayData,'thanku','name');
								$body_summary_desc		= 	$parObj->_getLabenames($arrayData,'body_summary_desc','name');
								//$body_summary_desc 		=  	str_replace("xxcc",$creditcard, $body_summary_desc);/*Crefdit card num adding*/
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
								$pdf->AddPage();//print_r($pdf);exit;
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
								// send message 
								if ($recipients =$this->sendg->send($subject,$from,$to,$html,$text))
								{
									/*Sending mails to admin intimating payment is done starts*/
									$hello_fname	=	"Hi,";							
									$content		=	"This user(".$user_fname." ".$user_lname.") : with emailid :".$emailUser." doing a payment through stripe from ".$from_host.".The amount is: ".$amount." and customer id is ".$cus_id;
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
									//$to 		= 	array('info@jiwok.com'=>'info@jiwok.com');
						$to = array('neethu.reubro@gmail.com'=>'Jiwok Admin');
									$subject 	= 	'Payment process mail';	 
									// send message 
									$recipients =$this->sendg->send($subject,$from,$to,$html,$text);
																									
									/*Sending mails to admin intimating payment is done ends*/	
									// This will let us know how many users received this message
									$_SESSION['successPayment']	=	'cGF5bWVudFN1Y2Nlc3M=';
									//~ header("location:userArea.php?origin=payment");	//header("location:userArea.php?origin=payment&value=".$payment_amount."&actn=cGF5bWVudFN1Y2Nlc3M=&".$payment_currency);
									//~ exit;
									if($recipients){echo "1";exit;}
								}
								// something went wrong =(
								else
								{		echo "0";exit;								
									//print_r($failures);										
									header("location:payment_new.php?errorcode=52");
									exit;
								}										
								//-------------------------------------------------------------------------------Sendgrid end
								}
							else
							{
								header("location:payment_new.php?errorcode=50");
								exit;
							}
								////
							}
							else if($result4[0]['count'] <= 0)
							{
								
							}
				}
//ends

