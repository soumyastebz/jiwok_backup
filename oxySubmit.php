<?php	

include_once('regDetail.php');
include_once("oxylane_funct.php");

$returnDataOxy		= $parObj->_getTagcontents($xmlPath,'contents','label');
$arrayDataOxy		= $returnDataOxy['general'];
if(isset($_POST['oxysubmit']))
{  
	
	// validation
	if(trim($_POST['giftcard'])!=""){		
		if(!is_numeric(trim($_POST['giftcard']))){ 
			$errorMsg = 1; $err_user_card = $parObj->_getLabenames($arrayDataOxy,'invalidoxylanecard','name'); $err1 = 1; 
		}elseif((strlen(trim($_POST['giftcard'])) !=16) && (strlen(trim($_POST['giftcard'])) !=17) ){
			$errorMsg = 1; $err_user_card = $parObj->_getLabenames($arrayDataOxy,'invalidoxylanecard','name'); $err1 = 1;
			
		}
		else
		{
			
			$jiwok_card			=	$dbObj->_getList("select * from oxylane_data where code='".trim($_POST['giftcard'])."'and status='1'");
			/*============for first time user======*/
			$usedAmt=	$jiwok_card	[0]['used_amount'];
			$jiwok_card			=	$jiwok_card[0]['id'];
			if(!$jiwok_card || ($usedAmt != 0))
			{
				
				$errorMsg = 1; $err_user_card = $parObj->_getLabenames($arrayDataOxy,'invalidoxylanecard','name'); $err1 = 1; 
			}
		
		}
			
	}
	else
	{
	
		$errorMsg = 1; $err_user_card = $parObj->_getLabenames($arrayDataOxy,'enteroxylanecard','name'); $err1= 1; 
	}
	if(!trim($_POST['Jcode'])!="")
	{
		$errorMsg = 1; $err_user_gift_jiwok = $parObj->_getLabenames($arrayDataOxy,'invalidjiwokcode','name'); $err2= 1; 
	}
	else
	{
		$jcode_id	=	$dbObj->_getList("select * from campaign_manage where camp_id='".trim($_POST['Jcode'])."'");
		$jcode_id1	=	$jcode_id[0]['id'];
		if (!$jcode_id1)
		{
			$errorMsg = 1; $err_user_gift_jiwok = $parObj->_getLabenames($arrayDataOxy,'notexistjiwokcode','name'); $err2 = 1; 
		}
	}	
	if(!$errorMsg){	
	//=======================================================
	
	$cardNum 	                =   trim($_POST['giftcard']);
    $jiwok_oxycode				=	$dbObj->_getList("select * from oxylane_data where code='".$cardNum."' and status='1'");

	if (count($jiwok_oxycode)> 0)
	{
		
		$balance	=	"";
		$balance	=	($jiwok_oxycode[0]['amount']-$jiwok_oxycode[0]['used_amount']);
		$usedAmt	=	$jiwok_oxycode[0]['used_amount'];
		$qry	=	$dbObj->_getList("select * from campaign_manage where camp_id='".trim($_POST['Jcode'])."'");
		
		if (count($qry)>0)
		{
			$camp_value	=	$qry[0]['camp_value'];
					
		//	if(is_numeric($balance) && $balance >=$camp_value)
//			{	
				
				$balance	=	$camp_value;
				$jiwok_result_java				=	$dbObj->_getList("select * from campaign_manage where camp_id='".trim($_POST['Jcode'])."' and camp_value=".$balance);
				$jiwok_camp_id			=	$jiwok_result_java[0]['id'];
				if(!$jiwok_camp_id)
				{
					$errorMsg = 1; $err_user_gift_jiwok = $parObj->_getLabenames($arrayDataOxy,'notexistjiwokcode','name'); $err2 = 1; 
				}	
			/*}
			else
			{
				
				$errorMsg = 1;
				$err_user_gift_card							= $parObj->_getLabenames($arrayDataOxy,'failedoxylane','name');
				$err_user_gift_card							=	str_replace("(n)",$balance,$err_user_gift_card);				
				
			}*/
			/*else
			{
			
				$errorMsg = 1;
				$err_user_gift_jiwok= $parObj->_getLabenames($arrayDataOxy,'erroroxylane','name');$err2 = 1;	
			}*/
		}
	}
	
	
	else{ 
	//----------------------------------------------------
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
		$soapClientParameters = array('soap_version'   => 'SOAP_1_2');
		//print_r($accountParameters);exit;
		//global config
		$currency = 978; //for € config
	
		//jiwok config
	
		$merchantId = 97254100001;
	
		$subThirdNumber = '';
	
		$thirdNumber = '';
	
		$thirdTypeNumber = '';
	
		//card parameters
	
		$cardNumber = str2num(trim($_POST['giftcard']));
		$eanCode = '';
							
		//transaction parameter
	
		$cashierNumber = 1;
	
		$tillNumber = 0;
		//echo "<pre/>";
		//print_r($accountParameters);
		//print_r($soapClientParameters);
		//die('here');
		$soapClient = initSoapClient($accountParameters, $soapClientParameters);
	
		$balance = getCardBalance($soapClient, $cardNumber, $currency, $merchantId, $subThirdNumber, $thirdNumber, $thirdTypeNumber, $eanCode);
		
		oxylaneCardBalance($_SESSION['user']['user_email'],'get card balance',$userId,$balance,$_POST['giftcard']);
		if(is_numeric($balance) && $balance>0){
			$jiwok_result_java				=	$dbObj->_getList("select * from campaign_manage where camp_id='".trim($_POST['Jcode'])."' and camp_value=".$balance);
			$jiwok_camp_id			=	$jiwok_result_java[0]['id'];
			if(!$jiwok_camp_id)	{
				$errorMsg = 1; $err_user_gift_jiwok = $parObj->_getLabenames($arrayDataOxy,'notexistjiwokcode','name'); $err2 = 1; 
			}	
		}else{
			$div_overlay = $parObj->_getLabenames($arrayDataOxy,'erroroxylane','name');
			
		}
		
	}
	}
	//-------------------------------------------------------------
	if(!$errorMsg){
		if(is_numeric($balance)){	
			$jiwok_code						=	trim($_POST['Jcode']);
			if($jiwok_code){
				$jiwok_result				=	$dbObj->_getList("select * from campaign_manage where camp_id='".$jiwok_code."'");
				$gift_card_free_period	    =	$jiwok_result[0]['no_of_months'];
				$gift_card_camp_name=	$jiwok_result[0]['camp_name'];
			}
						/*else
										{
											if($balance)
											{
												$jiwok_result_bal				=	$dbObj->_getList("select * from campaign_manage where camp_value=".$balance." ORDER BY created_date DESC LIMIT 1");
												$gift_card_free_period		=	$jiwok_result_bal[0]['no_of_months'];
												$gift_card_camp_name	=	$jiwok_result_bal[0]['camp_name'];
												}
											}*/
						
			$selectSettings					=	"select * from settings";
			$result							=	$GLOBALS['db']->getAll($selectSettings,DB_FETCHMODE_ASSOC);
			
			$total_amt_redeem				=	$result[0]['membership_fee'];
			
			$total_amt_redeem				=	$total_amt_redeem	*100;
			
			if($balance>0){
				
				$redeem_amt				=	 $balance;
				$redemAmount			=	$balance;
				//transaction parameter
				$cashierNumber = 1;
				$tillNumber = 0;
				

				$textdisable	=	 "<script language=\"javascript\" type=\"text/javascript\"> document.getElementById('giftcard').disabled = \"true\";</script>";
				if($gift_card_free_period){	
					/*$max_month_frm 			=	$gift_card_free_period;
					$redeem_amt					=	(($max_month_frm-1) * $total_amt_redeem) + $org_fee;
					$redemAmount				=	 $redeem_amt;*/
					//$redemAmount =1;
				//=================================================================	
				if($cardNum!="")
				{
					
					$usedAmt	=	$usedAmt+$balance;
							
					$codeData	=	$jiwok_oxycode[0]["code"];
					
					$oxyCode['used_amount']		= $usedAmt;
					$status = $dbObj->_updateRecord("oxylane_data",$oxyCode,"code='{$codeData}'");

				}
					
				else
				{
					$status = redemCard($soapClient, $redemAmount, $cardNumber, $cashierNumber, $currency, $merchantId, $subThirdNumber, $thirdNumber, $thirdTypeNumber, $tillNumber, $eanCode);
						
					oxylaneRedeemCard($_SESSION['user']['user_email'],'get redeem card',$userId,$status,$_POST['giftcard'],$payFee);
				}
					if($status){
						
						/*********Define $payFee=9.9 as per client request********/
						$payFee	=	"9.9";
						
						/*$payFee 								= $redeem_amt/100;	
						$payFee_euro							=	str_replace(".",",",$payFee);*/
						//$div_overlay							=	$parObj->_getLabenames($arrayDataOxy,'oxysuccessone','name').$gift_card_free_period." ".$parObj->_getLabenames($arrayDataOxy,'oxysuccesstwo','name');
			
						$expdate								= date('Y-m-d',strtotime(date('Y-m-d').' + '.$gift_card_free_period.' months'));
	
						$nextId									= $_SESSION['login']['userId'];
							
						
							$payment_result	= 	mysql_query("SELECT * FROM payment WHERE payment_userid='".addslashes($userId)."' AND payment_status = '1' AND payment_expdate >= CURDATE( )");
							
							$count = mysql_num_rows($payment_result);
							if ($count >0) 
							{
								$result		=	mysql_fetch_array($payment_result);
												
								$version	=	$result['version'];
								$payment_result1	=mysql_query("SELECT MAX(payment_expdate) as payment_expdate FROM payment WHERE payment_userid='".addslashes($userId)."' AND payment_status = '1'");
								$result_date		=	mysql_fetch_array($payment_result1);	
								$payment_expdate	=$result_date['payment_expdate'];	
								$payment_expdate= date('Y-m-d',strtotime($payment_expdate.' + '.$gift_card_free_period.' months'));
								
							if($version	=='New')
							{
								
								$selectQuery1	=  "select  * from payment_paybox where user_id ='".$userId."' and status='ACTIVE'";
								$result1		= 	$GLOBALS['db']->getAll($selectQuery1,DB_FETCHMODE_ASSOC);
								
								if(sizeof($result1)> 0)
								{
									$payment_expiry	=	mysql_query("select MAX(payment_expiry_date) as payment_expiry_date from payment_cronjob where user_id='".$userId."' AND status = 'VALID'");
									
									$result2		=	mysql_fetch_array($payment_expiry);
									$date	=$result2['payment_expiry_date'];
									
									$date = date('Y-m-d',strtotime($date.' + '.$gift_card_free_period.' months'));
									
									$cronUpdateQuery1	=	"UPDATE payment_cronjob	SET payment_expiry_date='".$date."' where user_id='".$userId."' AND status = 'VALID'";	
									$res = $GLOBALS['db']->query($cronUpdateQuery1);				
									
								}
								
							
							}
							
							
							$payElmts['payment_userid']				=	$userId;

							$payElmts['payment_amount']			=	$payFee;

							$payElmts['payment_date']				=	date('Y-m-d');

							$payElmts['payment_status']				=	1;

							$payElmts['payment_expdate']			=	$payment_expdate;
							
							$objDb->_insertRecord("payment",$payElmts);
						
							/*$paymentUpdateQuery1	=	"UPDATE payment SET payment_expdate='".$payment_expdate."', 	
payment_amount='".$payFee."'WHERE payment_userid='".addslashes($userId)."' AND payment_status = '1'" ;
							$res1	=	$GLOBALS['db']->query($paymentUpdateQuery1);*/
							
							
					}
							//////////////payment
	
						// if($payFee != ''){
					else
					{
		
							//insert payment page

							$payElmts['payment_userid']				=	$userId;

							$payElmts['payment_amount']			=	$payFee;

							$payElmts['payment_date']				=	date('Y-m-d');

							$payElmts['payment_status']				=	1;

							$payElmts['payment_expdate']			=	$expdate;
							
							$objDb->_insertRecord("payment",$payElmts);
						}
							
							
							$oxylane['user_id']							=	$userId;

							$oxylane['jiwok_code']						=	$jiwok_code;

							$oxylane['free_period']						=	$gift_card_free_period;

							$oxylane['payment']						=	$payFee;

							$oxylane['status ']							=	1;
							

							$objDb->_insertRecord("user_oxylane_details",$oxylane);
							
							
							$elmts['user_discount_status']		= 1;
							
							$chk = $dbObj->_updateRecord("user_master",$elmts,"user_id = {$userId}");
							
							
							$campReport		=	array();
							
							$campReport['user_id']					=	$userId;
			
							$campReport['gift_card_no']				=	trim($_POST['giftcard']);
							
							$campReport['jiwok_code']				=	$jiwok_code;
							
							$campReport['date']						=	date('Y-m-d');
							
							$campReport['no_of_months']			=	$gift_card_free_period;
							
							$campReport['campaign_name']		=	$gift_card_camp_name;
							
							$campReport['payment_amount']		=	$payFee;
							
							$campReport['email']						=	$_SESSION['user']['user_email'];
							
							$campReport['status']						=	1;
							
							$objDb->_insertRecord("campaign_reports",$campReport);
							
							header("location:oxylane_success.php?gift=".$gift_card_free_period);
						 //}
					}else{
						$div_overlay = $parObj->_getLabenames($arrayDataOxy,'erroroxylane','name');
					}
				}else{
					if($max_month_frm_bal>=1){
						 $div_overlay	= $parObj->_getLabenames($arrayDataOxy,'entermonthone','name').$max_month_frm_bal.$parObj->_getLabenames($arrayDataOxy,'entermonthtwo','name');
						 $divdisplay	=	 "<script language=\"javascript\" type=\"text/javascript\"> document.getElementById('hiddendiv').style.visibility = \"visible\";</script>"; 
						 $capt_display =  "<script language=\"javascript\" type=\"text/javascript\"> document.getElementById('capt_div').style.visibility = \"hidden\";</script>"; 
					}
				}
			}else{
				$div_overlay							= $parObj->_getLabenames($arrayDataOxy,'failedoxylane','name');
				$div_overlay							=	str_replace("(n)",$balance,$div_overlay);
				/*$payFee1 								= $redeem_amt/100;	
				$payFee_euro							=	str_replace(".",",",$payFee1);
				$div_overlay							=	str_replace("7.90",$payFee_euro,$div_overlay);*/
			}
		}else{
			$div_overlay = $parObj->_getLabenames($arrayDataOxy,'erroroxylane','name');
		}
	}	
    
}



if(isset($_POST['oxymonthsubmit'])){
	
	if(trim($_POST['choice'])!=""){
		if(!is_numeric(trim($_POST['choice']))){
			$errorMsg = 1; $err_user_month = $parObj->_getLabenames($arrayDataOxy,'entermonths','name'); $err3 = 1; 
		}
	}else{
		$errorMsg = 1; $err_user_month = $parObj->_getLabenames($arrayDataOxy,'entermonths','name'); $err3 = 1; 
	}
	$jiwok_code						=	trim($_POST['jiwok_code']);
	$gift_card							=	trim($_POST['gift_card']);
	$max_month_frm_bal			=	trim($_POST['max_month_frm_bal']);
			
	if(!$errorMsg){
		
		$gift_card_camp_name		=	trim($_POST['gift_card_camp_name']);				
		$gift_card_free_period			=	trim($_REQUEST['choice']);
		$selectSettings					=	"select * from settings";
		$result								=	$GLOBALS['db']->getAll($selectSettings,DB_FETCHMODE_ASSOC);
		$total_amt_redeem				=	$result[0]['membership_fee'];
		$total_amt_redeem				=	$total_amt_redeem	*100;	
		
		//transaction parameters
		$cashierNumber = 1;
		$tillNumber = 0;
		
		$expdate									= date('Y-m-d',strtotime(date('Y-m-d').' + '.$gift_card_free_period.' months'));
		
		$nextId									= $_SESSION['login']['userId'];
		
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
		
		$currency = 978; //for € config
		
		$merchantId = 97254100001;
		
		$subThirdNumber = '';
		
		$thirdNumber = '';
		
		$thirdTypeNumber = '';
		
		$cardNumber = str2num(trim($_POST['gift_card']));
		
		$eanCode = '';
		
		$cashierNumber = 1;
		
		$tillNumber = 0;

	//$redemAmount					=	1;

		$soapClient = initSoapClient($accountParameters, $soapClientParameters);
		$balance = getCardBalance($soapClient, $cardNumber, $currency, $merchantId, $subThirdNumber, $thirdNumber, $thirdTypeNumber, $eanCode);
				
		if($balance >$org_fee){
			//$max_month_frm_bal 		=	1;
			//$balance						=	$balance - $org_fee;
			$max_month_frm 			=	$gift_card_free_period;
		//	$max_month_frm_bal		=	$max_month_frm+$max_month_frm_bal;
			$redeem_amt					=	(($max_month_frm-1) * $total_amt_redeem) + $org_fee;
			$redemAmount				=	 $redeem_amt;
			
			$status = redemCard($soapClient, $redemAmount, $cardNumber, $cashierNumber, $currency, $merchantId, $subThirdNumber, $thirdNumber, $thirdTypeNumber, $tillNumber, $eanCode);
			if($status){
				$payFee 									=$redeem_amt/100;
				$payFee_euro							=	str_replace(".",",",$payFee);
				$div_overlay	= /*$payFee_euro." ".*/$parObj->_getLabenames($arrayDataOxy,'oxysuccessone','name').$gift_card_free_period." ".$parObj->_getLabenames($arrayDataOxy,'oxysuccesstwo','name');
				//////////////payment
				if($payFee != ''){
	
					//insert payment page
	
					$payElmts['payment_userid']				=	$userId;
	
					$payElmts['payment_amount']			=	$payFee;
	
					$payElmts['payment_date']				=	date('Y-m-d');
	
					$payElmts['payment_status']				=	1;
	
					$payElmts['payment_expdate ']			=	$expdate;
	
					$objDb->_insertRecord("payment",$payElmts);
					
					
					$oxylane['userid']							=	$userId;
	
					$oxylane['jiwok_code']						=	$jiwok_code;
	
					$oxylane['free_period']						=	$gift_card_free_period;
	
					$oxylane['payment']						=	$payFee;
	
					$oxylane['status ']							=	1;
	
					$objDb->_insertRecord("user_oxylane_details",$oxylane);
					
					
					$campReport		=	array();
					
					$campReport['user_id']					=	$userId;
	
					$campReport['gift_card_no']				=	$gift_card;
					
					$campReport['jiwok_code']				=	$jiwok_code;
					
					$campReport['date']						=	date('Y-m-d');
					
					$campReport['no_of_months']			=	$gift_card_free_period;
					
					$campReport['campaign_name']		=	$gift_card_camp_name;
					
					$campReport['payment_amount']		=	$payFee;
					
					$campReport['email']						=	$_SESSION['user']['user_email'];
					
					$campReport['status']						=	1;
					
					$objDb->_insertRecord("campaign_reports",$campReport);
	
				}
			}else{
				//$div_overlay	=$parObj->_getLabenames($arrayDataOxy,'erroroxylane','name');
			}
		}else{
			$div_overlay	= $parObj->_getLabenames($arrayDataOxy,'failedoxylane','name');
			$payFee1 								= $redeem_amt/100;	
			$payFee_euro							=	str_replace(".",",",$payFee1);
			$div_overlay							=	str_replace("7.90",$payFee_euro,$div_overlay);
		}
	}else{
		$textdisable	=	 "<script language=\"javascript\" type=\"text/javascript\"> document.getElementById('giftcard').disabled = \"true\";</script>"; 
		$divdisplay	=	 "<script language=\"javascript\" type=\"text/javascript\"> document.getElementById('hiddendiv').style.visibility = \"visible\";</script>"; 
		$_POST['giftcard']		=$gift_card;
		$div_overlay	= $parObj->_getLabenames($arrayDataOxy,'entermonthone','name').$max_month_frm_bal.$parObj->_getLabenames($arrayDataOxy,'entermonthtwo','name');
				   
	}
}


function str2num($sNumber)
{
    $aConventions = localeConv();
    $sNumber = trim((string) $sNumber);
    $bIsNegative = (0 === $aConventions['n_sign_posn'] && '(' === $sNumber{0} && ')' === $sNumber{strlen($sNumber) - 1});
    $sCharacters = $aConventions['decimal_point'].
                   $aConventions['mon_decimal_point'].
                   $aConventions['negative_sign'];
    $sNumber = preg_replace('/[^'.preg_quote($sCharacters).'\d]+/', '', trim((string) $sNumber));
    $iLength = strlen($sNumber);
    if (strlen($aConventions['decimal_point']))
    {
        $sNumber = str_replace($aConventions['decimal_point'], '.', $sNumber);
    }
    if (strlen($aConventions['mon_decimal_point']))
    {
        $sNumber = str_replace($aConventions['mon_decimal_point'], '.', $sNumber);
    }
    $sNegativeSign = $aConventions['negative_sign'];
    if (strlen($sNegativeSign) && 0 !== $aConventions['n_sign_posn'])
    {
        $bIsNegative = ($sNegativeSign === $sNumber{0} || $sNegativeSign === $sNumber{$iLength - 1});
        if ($bIsNegative)
        {
            $sNumber = str_replace($aConventions['negative_sign'], '', $sNumber);
        }
    }
    $fNumber = (float) $sNumber;
    if ($bIsNegative)
    {
        $fNumber = -$fNumber;
    }
    return $fNumber;
}

?>
