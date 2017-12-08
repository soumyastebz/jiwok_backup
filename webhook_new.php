<?php 
//error_reporting(E_ALL);
require_once 'config.php';
include_once('../includeconfig.php');
require('../pdfgenerate/fpdf.php');
include_once "../Swift/lib/swift_required.php";
include_once "../includes/classes/class.sendgrid.php";
$dbObj          =   new DbAction();	
$parObj 	    =   new Contents();
$sendg          =   new sendgrid();
$xmlPath        =   "../xml/french/page.xml";	
$returnData		= 	$parObj->_getTagcontents($xmlPath,'new_payment','label');
$arrayData		= 	$returnData['general'];

$input          = @file_get_contents("php://input");
$event_json     = json_decode($input,true);
$event_json1    = json_decode($input);

$myFile = 'testdata.txt';
$fh = fopen($myFile, 'a');

//========================
$stringData = '=======start========';
fwrite($fh, $stringData);
$stringData =date("Y-m-d H:i:s");  
fwrite($fh, $stringData);

// Verify the event by fetching it from Stripe
//~ $event = \Stripe\Event::retrieve($event_json->id);
//~ mail("dijo.reubro@gmail.com","test",$event);
//===========================================

$sub            = $event_json['data']['object']['lines']['data'][0]['id'];
$cus            = $event_json['data']['object']['customer'];
$currency       = $event_json['data']['object']['lines']['data'][0]['currency'];
$amount         = $event_json['data']['object']['lines']['data'][0]['amount'];
$plan           = $event_json['data']['object']['lines']['data'][0]['plan']['id'];
$charge_id      = $event_json['data']['object']['charge'];
$status         = $event_json['data']['object']['status'];//new
//==============================write log file=======================//
$stringData =print_r($event_json,true);
fwrite($fh, $stringData);
$stringData = $sub;
		fwrite($fh, $stringData); 
		$stringData = $cus;
		fwrite($fh, $stringData); 
		$stringData = $currency;
		fwrite($fh, $stringData); 
		$stringData = $amount;
		fwrite($fh, $stringData); 
		$stringData = $plan;
		fwrite($fh, $stringData); 
		$stringData = $charge_id;
		fwrite($fh, $stringData); 
//==============================write log file ends=======================//

if($event_json['data']['object']['lines']['data']>1)
{
	//$plan           = $event_json['data']['object']['lines']['data'][1]['plan']['id'];
}
if($plan=='1' || $plan=='11')
{
	$plan               =   '1';
	$month				=	"1 month";
		
}
else if($plan=='3' || $plan=='31')
{
	$plan ='3';
	$month				=	"3 month";
}
else if($plan=='6' || $plan=='61')
{
	$plan ='6';
	$month				=	"6 month";
}
else if($plan=='12' || $plan=='121')
{
	$plan ='12';
	$month				=	"12 month";
}
else if($plan=='euro1')
{
	$plan ='1';
	$month				=	"1 month";
	
}
$payment_expiry_date	=	date("Y-m-d", strtotime($month));
//to get lang currency 
if($currency  == "eur")
{
	$lanId=2;
	$currency	=	"euro";
}
elseif($currency  == "pln")
{
	$lanId=5;
	$currency	=	"zlotty";
}

//==============================write log file=======================//
$stringData = $plan."iiiiiiiiiiiiii";
		fwrite($fh, $stringData); 
//==============================write log file=======================//
$dbQuery_plan	 =  "select * from jiwok_payment_plan where plan_id='".$plan."' and plan_currency='".$lanId."'";
$res_plan		 =	$GLOBALS['db']->getAll($dbQuery_plan,DB_FETCHMODE_ASSOC);
$plan_amount_val =	$res_plan[0]['plan_amount'];

//xml ends	

$dbQuery	 =  "select * from stripe_payment where customer_id='".$cus."' and status='ACTIVE'";
//$dbQuery	 =  "select * from stripe_payment where customer_id='cus_7aiV38lGitgHpU' and status='ACTIVE'";
$res_new	 =	$GLOBALS['db']->getAll($dbQuery,DB_FETCHMODE_ASSOC); 
$user_id     =  $res_new[0]['user_id'];
$pp_stripe_id	=$res_new[0]['id'];

   
		    
//for xml
$dbQuery	 =  "SELECT a.id as cronId,a.user_id,a.pp_id,a.plan_id,a.payment_expiry_date,a.payment_amount,a.payment_currency,a.status,a.discount_amount,b.*,c.user_email,c.user_fname,c.user_lname,c.user_language,c.paybox_email FROM `stripe_auto_renewal` as a join stripe_payment as b  on a.pp_id=b.id join user_master as c on b.user_id=c.user_id  where a.payment_expiry_date <= '".date('Y-m-d')."' and a.status='VALID' and a.user_id  = '".$user_id."' order by a.id desc limit 0,1";
$res  =	$GLOBALS['db']->getAll($dbQuery,DB_FETCHMODE_ASSOC);
$cid	=	$res[0]['cronId'];

//==============================write log file=======================//
$stringData = $dbQuery;
fwrite($fh, $stringData); 
$stringData = $res[0]['user_email'];
fwrite($fh, $stringData);
		 
$stringData =print_r($res,true);
fwrite($fh, $stringData);
//==============================write log file=======================//
    if(($event_json1->type == 'invoice.payment_succeeded') && ($amount !="0"))
	{      
	
		$stringData = '=======included in payment success event ========';
		fwrite($fh, $stringData);     
	
	  	$stringData = $event_json['data']['object']['lines']['data'][0]['plan']['id'];
		fwrite($fh, $stringData);    
							 if($event_json['data']['object']['lines']['data'][0]['plan']['id'] =='euro1')
										{
											$stringData = $payment_expiry_date;
											fwrite($fh, $stringData);  
											$plan 				=	'1';
											$month				=	"1 month";	
											$mytrial			=	strtotime(trim($payment_expiry_date));
											$stringData = $mytrial;
											fwrite($fh, $stringData); 
											$cu = \Stripe\Customer::retrieve($cus);											
												$subscription = $cu->subscriptions->retrieve($sub);
												$subscription->plan =$plan;
												$subscription->prorate  = "False";
												$subscription->trial_end = trim($mytrial);
												$planResponse = $subscription->save();
												
										}  
										
						$dbQuery11	 =  "select  id,trans_refrns_id from stripe_transaction where user_id='".$user_id."' order by id desc limit 0,1";
						$res_new11	 =	$GLOBALS['db']->getAll($dbQuery11,DB_FETCHMODE_ASSOC);  
						if($res_new11[0]['trans_refrns_id'] =='')
						{
						$rr = $res_new11[0]['id'];
	                    $elmts_charge = array();
						$elmts_charge['trans_refrns_id'] =  $charge_id;
						
						//a user with subscription can have sub_id for first time and within seconds a webhook called on backend and 
						//we will get charge id of that subscription.so we are saving the ch_id on transaction table.
						$elmts_charge=$dbObj->_updateRecord("stripe_transaction",$elmts_charge,"id IN($rr)");	
					    }
//All actions should happen only if the user's subscription date is not today date .
$join_date = strtotime($res_new[0]['join_date']);
$join_date = date('Y-m-d', $join_date);
	//$join_date			=	'2015-12-18';	
//	if($join_date != date('Y-m-d'))
	if($join_date)	
	{ 
$stringData = '=======step3 ========';
fwrite($fh, $stringData);  
if(sizeof($res))
{
						
	
	$stringData = '=======step4 ========';
	fwrite($fh, $stringData);  
	
	
	if(($res[0]['discount_amount']	!=	0)	&&	($res[0]['plan_id']	==	1))
				{					
					$pieces = explode("##", $res[0]['discount_amount']);					
					$res[0]['payment_amount']	=	trim($pieces[0]);
					$discStatusId	=	trim($pieces[1]);
				}
				
//payment table insertion

$elmts					   = array();
$elmts['payment_userid']   = $user_id;


$elmts['payment_date']     = date('Y-m-d');
$elmts['payment_status']   = '1';
$elmts['payment_amount']   = ($amount/100);
$elmts['payment_expdate']  = $payment_expiry_date;
$elmts['payment_currency'] = $currency;
$elmts['version']          = 'stripe';

//==============================write log file=======================//
$stringData =print_r($elmts,true);
fwrite($fh, $stringData);
//==============================write log file=======================//
$dbObj->_insertRecord("payment",$elmts);
$stringData = '=======step5 ========';
fwrite($fh, $stringData);  
//Update the discount_users table if user uses discount
						if($discStatusId	!=	0)
						{
							$elmts_new                   =  array();
							$elmts_new['payment_status'] =  'success';
							//gg now $rslt_new=$dbObj->_updateRecord("discount_users",$elmts_new,"id IN($discStatusId)");										
						}
//ends
//insertions to transaction table
$stringData = '=======step6 ========';
fwrite($fh, $stringData);  
$selectQueryMax	=  "select  max(payment_id) as payment_id from payment where payment_userid ='".$user_id."' AND payment_status = '1'";
$resultMax		= 	$GLOBALS['db']->getAll($selectQueryMax,DB_FETCHMODE_ASSOC);	
$payId			=	$resultMax[0]['payment_id'];
$paymentDetails	           = base64_encode(serialize($input));
$elmts1					   = array();						
$elmts1['user_id']         = $user_id;
$elmts1['payment_id']      = $payId;
$elmts1['details']         = $paymentDetails;
$elmts1['status']          = 'PAID';				
$elmts1['trans_refrns_id'] = $charge_id;	
$elmts1['balance']		   = ($amount/100);									
$dbObj->_insertRecord("stripe_transaction",$elmts1);	
//ends
/*Updation of  stripe_auto_renewal tble from valid to paid,and new insertion for new autorenewal*/

$stringData = '=======step7 ========';
fwrite($fh, $stringData); 
$elmts_new1 = array();
$elmts_new1['status'] =  'PAID';
$rslt_new=$dbObj->_updateRecord("stripe_auto_renewal",$elmts_new1,"id IN($cid)");
//ends

//insertion to stripe_auto_renewal
$elmts2					  = array();						
$elmts2['user_id'] = $user_id;
$elmts2['pp_id']   = $res[0]['pp_id'];
$elmts2['plan_id'] = $plan;
$elmts2['payment_expiry_date'] = $payment_expiry_date;		
$elmts2['payment_amount']   = ($amount/100);
$elmts2['payment_currency'] = $currency;
$elmts2['status'] = 'VALID';		
$elmts2['customer_id'] = $cus;
$elmts2['subsciption_id'] = $sub;		
if($dbObj->_insertRecord("stripe_auto_renewal",$elmts2));
{
$stringData = '=======step8 ========';
fwrite($fh, $stringData);  

							$period 				=	 date('d-m-Y')." to ".date("d-m-Y", strtotime($month)); 
							//$cc_part 				=   substr($cc, -4);/*Last 4 digit of cc*/
							//$creditcard				= "XXXX-XXXX-XXXX-".$cc_part;
							$subject				=		$parObj->_getLabenames($arrayData,'subject','name');
							$hello_fname			=		$parObj->_getLabenames($arrayData,'hello','name')." ".$res[0]['user_fname'];
							$thank					=		$parObj->_getLabenames($arrayData,'thank','name');
							//$body2					=		$parObj->_getLabenames($arrayData,'body2','name');
							$body_summary			=		$parObj->_getLabenames($arrayData,'body_summary','name');							
							$body_help				= $parObj->_getLabenames($arrayData,'body_help','name');
							$body_help				= str_replace("xbrx","<br/>",$body_help);/*P break line*/
							$thanku					= $parObj->_getLabenames($arrayData,'thanku','name');
							$body_summary_desc		= $parObj->_getLabenames($arrayData,'body_summary_desc','name');
							$body_summary_desc 		=  str_replace("xxcc",$creditcard, $body_summary_desc);/*Crefdit card num adding*/
							$price_with_symbol		=  $amount.$currency;
							//$body_summary_desc 		=  str_replace("xxcash",$price_with_symbol, $body_summary_desc);/*Cash adding*/
							//$body_summary_desc 		=  str_replace("xxexp",date("d-m-Y", strtotime($month)),$body_summary_desc);/*Exp date adding*/
							//$body_summary_desc		=  str_replace("xmnth",$value['plan_id'],$body_summary_desc);/*month adding*/
							//$acountURL = "http://www.jiwok.com/myprofile.php"; /*Will chaneg*/									
							$acountURL 				= 	$parObj->_getLabenames($arrayData,'body_summary','name')." : ".$parObj->_getLabenames($arrayData,'myAccountUrl','name'); /*Will chaneg*/	
							$body_summary_desc1		=	$parObj->_getLabenames($arrayData,'body_summary_desc1','name');
							$body_summary_desc2		=	$parObj->_getLabenames($arrayData,'body_summary_desc2','name');	
							$body_summary_desc3		=	$parObj->_getLabenames($arrayData,'body_summary_desc3','name');
							$body_summary_desc4		=	$parObj->_getLabenames($arrayData,'body_summary_desc4','name');																
							//Pdf content starts
							//$pdfHello			=		$parObj->_getLabenames($arrayData,'pdfHello','name')." ".$user_fname.",";
							$pdfWelcome			=		$parObj->_getLabenames($arrayData,'pdfWelcome','name');
							//$pdfEmail			=		$parObj->_getLabenames($arrayData,'pdfEmail','name')." : ".$emailUser;										
							$pdfCardDetls1		=		$parObj->_getLabenames($arrayData,'pdfCardDetls1','name');
							//$pdfCardDetls1		=		str_replace("XXXX-XXXX",$creditcard,$pdfCardDetls1);
							//$pdfCardDetls1		=		str_replace("xxxx",$price,$pdfCardDetls1);
							$pdfDueDates		=		$parObj->_getLabenames($arrayData,'pdfDueDates','name')." : ".date("d-m-Y", strtotime($month));										
							$pdfCardDetls2		=		$parObj->_getLabenames($arrayData,'pdfCardDetls2','name');
							$pdfContactus		=		$parObj->_getLabenames($arrayData,'pdfContactus','name');
							$pdfContactusUrl	=		$parObj->_getLabenames($arrayData,'body_summary_desc3','name');
							$pdfGoodluck		=		$parObj->_getLabenames($arrayData,'pdfGoodluck','name');
							$pdfSiteUrl			=		$parObj->_getLabenames($arrayData,'pdfSiteUrl','name');
							$pdfInvoiceHead		=		$parObj->_getLabenames($arrayData,'pdfInvoiceHead','name');
							//$pdfTransactionid	=		$parObj->_getLabenames($arrayData,'pdfTransactionid','name')." : ".$transactionidnum;
							$pdfImpoNoteDescription=	$parObj->_getLabenames($arrayData,'pdfImpoNoteDescription','name');
							$pdfPass			=		$parObj->_getLabenames($arrayData,'pdfPass','name');
							$pdfPrice			=		$parObj->_getLabenames($arrayData,'pdfPrice','name');
							$pdfMonth			=		$plan_id." ".$parObj->_getLabenames($arrayData,'newMonthsTxt','name');
							$pdfCurrency		=		$price." ".$parObj->_getLabenames($arrayData,'pdfCurrency','name');
							$fromTo				=		$parObj->_getLabenames($arrayData,'pdfSubscriptionFrom','name')." ".date('d-m-Y')." ".$parObj->_getLabenames($arrayData,'pdfSubscriptionTo','name')." ".date("d-m-Y", strtotime($month));
							$pdfAmountpaid		=		$parObj->_getLabenames($arrayData,'pdfAmountpaid','name').$price.$parObj->_getLabenames($arrayData,'pdfCurrency','name');
							//$pdfCerditCard		=		$parObj->_getLabenames($arrayData,'pdfCerditCard','name')." : ".$creditcard;
							//Pdf content ends
							
							$line1				=	$line1	=	"============================================================================";
							$line2				=	"============================================";
							$line3				=	"-------------------------------------";										
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
							$pdf->Ln(10);
							$pdf->Cell(40,10,utf8_decode(date("d-m-Y")));
							$pdf->Cell(40,10,utf8_decode($pdfTransactionid));
							$pdf->Ln(10);
							$pdf->Cell(40,10,utf8_decode($line3));
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
							
							$pdffilepath="../includes/classes/Payment/pdfgenerate/user pdf/";
							$pdffile=$user_id."_".date('Y-m-d').".pdf";
							$pdfname=$pdffilepath.$pdffile;									
							$pdf->Output($pdfname,'F');   
////pdf ends and mail code starts






$stringData = '=======pdf created ========';
fwrite($fh, $stringData);  

							 $text = "\n";
								$thank				=	$parObj->_getLabenames($arrayData,'thankCrone','name');
								$body_summary_desc5	=	$parObj->_getLabenames($arrayData,'nameCrone','name');
								$html = "
								<html>
								  <head></head>
								  <body>
									<p>".$res[0]['user_fname'].",<br><br>
									   ".$thank."<br/><br/>".$acountURL."<br/><br/>".$body_summary_desc4."<br/>".$body_summary_desc5."<br/>http://www.jiwok.com<br/><br/>".$$body_summary_desc1."<br/>".$body_summary_desc2."<br/>".$body_summary_desc3."
									</p>
								  </body>
								</html>";
							
							$from = array('admin@jiwok.com' => 'Jiwok Admin');
							$to = array($res[0]['user_email']=>$res[0]['user_fname']);
							$subject = $parObj->_getLabenames($arrayData,'mailCronSubject','name');
		
							$recipients = $sendg->send($subject,$from,$to,$html,$text,$marathon='',$iso='',$file='',$replyto='');
							
							$stringData = $subject."   ".$from ."   " .$to ."   ".$html ."    " .$text;
							fwrite($fh, $stringData); 
							//Send grid mail to user when the user making first payment through newpayment plan ends
							
							//Sending a message to admin indicating payment is done												
							//$amount				=	$price.$parObj->_getLabenames($arrayData,'pdfCurrency','name');
							$hello_fname			=		"Hi,";							
							$content				=		"This user(".$res[0]['user_fname']." ".$res[0]['user_lname'].") : with emailid :".$res[0]['user_email']." doing a payment through stripe.The amount is:".($amount/100).$currency;
							//$content				=		"";
							$text = "\n";
							$html = "
							<html>
							  <head></head>
							  <body>
								<p>".$hello_fname."<br><br/><br/>".$content."<br/><br/><br/>".$thanku."<br/>http://www.jiwok.com
								</p>
							  </body>
							</html>";
							
							$from = array('info@jiwok.com' => 'Jiwok Admin');
						//	$to = array('info@jiwok.com'=>'Jiwok Admin');
							$to = array('neethu.reubro@gmail.com'=>'Jiwok Admin');
							//$to = array('dileepe.reubro@gmail.com'=>'Jiwok Admin');
							$subject = 'Payment autorenewal(Test mail)';
							
							$recipients = $sendg->send($subject,$from,$to,$html,$text,$marathon='',$iso='',$file='',$replyto='');

$stringData = '=======mail sent  ========';
fwrite($fh, $stringData);  

///mail code ends

//mail and pdf ends
http_response_code(200);
  }
$stringData = '=======step9 ========';
fwrite($fh, $stringData);  
http_response_code(200);
}
}
 $stringData = '=======step10 ========';
fwrite($fh, $stringData);  
http_response_code(200);
 }
else if($event_json1->type == 'invoice.payment_failed')
{
	
	$stringData = '=======included 222222 ========';
		fwrite($fh, $stringData);
	               if($user_id)
	               {    
					   
	                        $elmts_new = array();
							$elmts_new['status'] =  'UNSUBSCRIBED';
							$dbCond			                   =  "customer_id='".$cus."' and status= 'VALID'";
							$rslt_new=$dbObj->_updateRecord("stripe_auto_renewal",$elmts_new,$dbCond);	
							
	                        $elmts_new1 = array();
							$elmts_new1['status'] =  'EXPIRED';
							$elmts_new1['unsubscribed_date'] =  date('Y-m-d');
							$rslt_new1=$dbObj->_updateRecord("stripe_payment",$elmts_new1,"id IN($pp_stripe_id)");	
	
	
							$paymentDetails       =	base64_encode(serialize($input));
							$temparray            = array();
							$temparray['user_id'] = $user_id;
							$temparray['data']    = $paymentDetails;
							$chkNewEntry          = $dbObj->_insertRecord("stripe_transaction_errors",$temparray);
						}
							http_response_code(200);
}
$stringData = '=======end ========';
		fwrite($fh, $stringData);
		fclose($fh);
http_response_code(200);
?>
