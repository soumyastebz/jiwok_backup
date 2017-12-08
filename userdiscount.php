<?php
       	if($_POST['user_discount']!= '' ){
		
			$reffId						=	$_POST['user_discount'];	

		 	$discCnt	=	$objDisc->_isExistsDisc($reffId);		

			if(isset($_SESSION['login']['userId'])){

			$userId		=	$_SESSION['login']['userId']; //if user is coming from the registration page.
			
			}
              
			    $selectSettings		=	"select * from settings";

										$result				=	$GLOBALS['db']->getAll($selectSettings,DB_FETCHMODE_ASSOC);

										foreach($result as $key=>$data){
										
									/*	if($lanId == 1) 
										{
											$memShipFee			=   $objGen->_output($data['membership_feedollar']);
										}
										else
										{*/
							
											$memShipFee			=   $objGen->_output($data['membership_fee']);

									//	}


										}
			  
				if( $discCnt['cnt'] > 0 ){
											
							$discCode		=	$reffId;

							$Type			=	'DISC';

							//get any active reff id for this user

							$activeDisc	=	$objDisc->_getLastCode($userId,$Type);

							///chk whether the user is already entered a disc/reff id 

							$discApplyed			=	$objDisc->_isAlreadyApply($userId,$discCode,$Type);

							if(count($activeDisc) > 0){

								$activeDiscCode			=	$activeDisc['discount_code'];

								//chage date format 

								$enteredDiscDate		=	$objGen->_dateTomdY($activeDisc['start_date']);

								

								$getDiscCodeDetails		=	$objDisc->_getDiscDetails($activeDiscCode);

								$discMonth				=	$getDiscCodeDetails['discount_month'];

								$discExpiryDate			=	$getDiscCodeDetails['end_date'];   //yyyy-mm-dd

								//chage date format 

								$splittedDate			=	$objGen->_dateTomdY($discExpiryDate);

											

								//check whether discount period ended after apply discount

								//ADD month to date

								$userDiscExpiryDate		=	$objGen->_addMonthToDate($enteredDiscDate,$discMonth);

													

								$dateDiff1				=	$objGen->date_difference($today,$userDiscExpiryDate);

								$dateDiff2				=	$objGen->date_difference($today,$splittedDate);

								

								if($dateDiff1 > 0 ){

									if($discMonth > 0){

										if($dateDiff2 > 0){

										$errorMsg = 1;

										$err16 = 1;

										$discountMsg	= $parObj->_getLabenames($arrayErrorDataDiscount,'err5','name'); ///You have already one active discount code			

										}

									}

								}		

								}

								if($discApplyed['cnt'] == 0){

								

									$getDiscCodeDetails		=	$objDisc->_getDiscDetails($discCode);

									

									$discMonth				=	$getDiscCodeDetails['discount_month'];

									$AffdiscPer				=	$getDiscCodeDetails['discount_percentage'];

									$discExpiryDate			=	$getDiscCodeDetails['end_date'];   //yyyy-mm-dd

									//chage date format 

									$discExpiryDate			=	$objGen->_dateTomdY($discExpiryDate);

									$dateDiff2				=	$objGen->date_difference($today,$discExpiryDate);

									

										if($discMonth > 0){

											if($dateDiff2 > 0){

											

											$totalDiscPer	+=	$AffdiscPer;

											//for database updation

											$_SESSION['payment']['discCode']		=	$activeDiscCode;

											 $freeDaysMore							=	$getDiscCodeDetails['free_days'];

											 $_SESSION['payment']['NoMonthDisc']	=	$discMonth;

											}else{

												$errorMsg = 1;

												$err16 = 1;

												$discountMsg		= 	$parObj->_getLabenames($arrayErrorDataDiscount,'err6','name'); //Discount code already expired

											}

										}else{

										//if discount code  have only free period

										$totalDiscPer		+=	$AffdiscPer;

										$freeDaysMore		=	$getDiscCodeDetails['free_days'];

										}

						}else{

										$errorMsg = 1;

										$err16 = 1;

										$discountMsg	= $parObj->_getLabenames($arrayErrorDataDiscount,'err7','name'); //You had entered this discount code before

							}

					}else{

						$errorMsg = 1;

						$err16 = 1;

						$discountMsg=$parObj->_getLabenames($arrayErrorDataDiscount,'err8','name'); //Referral id/discount code not valid

						}

				  

		if($errorMsg != 1){
		
				$discDetail		= $objDisc->_getDiscDetails($_POST['user_discount']); //for findout free trail period of this discount code
		
								$freePeriofFrmDiscDetails = $discDetail['free_days'];
		
								if($freePeriofFrmDiscDetails != ''){
		
								$_POST['disc_period']					= $defaultFreePeriod + $freePeriofFrmDiscDetails;
		
								}else{
		
								$_POST['disc_period']					= $defaultFreePeriod ;
		
								}
		
				$Type	=	"DISC";
		
				$activeDisc	=	$objDisc->_getLastCode($userId,$Type);
				
				if(count($activeDisc) > 0){
		
								//echo "<br>dis=".
								$activeDiscCode			=	$activeDisc['discount_code'];
		
								$enteredDiscDate		=	$activeDisc['start_date'];
		
								//chage date format 
		
								$enteredDiscDate		=	$objGen->_dateTomdY($enteredDiscDate);
		
								
		
								$activeDiscId			=	$activeDisc['id'];
		
								
		
								$getDiscCodeDetails		=	$objDisc->_getDiscDetails($activeDiscCode);
		
								$discMonth				=	$getDiscCodeDetails['discount_month'];
		
								$AffdiscPer				=	$getDiscCodeDetails['discount_percentage'];
		
								$discExpiryDate			=	$getDiscCodeDetails['end_date'];   //yyyy-mm-dd
		
								//chage date format 
		
								$discExpiryDate			=	$objGen->_dateTomdY($discExpiryDate);
		
								
		
								//check whether discount period ended after apply discount
		
								//ADD month to date
		
								$expiryDate			=	$objGen->_addMonthToDate($enteredDiscDate,$discMonth);
		
													
		
								$dateDiff1				=	$objGen->date_difference($today,$discExpiryDate);
		
								$dateDiff2				=	$objGen->date_difference($today,$expiryDate);
		
								
		
								if($dateDiff1 > 0 ){
		
									if($discMonth > 0 ){
		
										if($dateDiff2 > 0){
		
											$totalDiscPer		+=	$AffdiscPer;
		
											//for database updation
		
											
		
											$_SESSION['payment']['discId']			=	$activeDiscId;
		
											$_SESSION['payment']['discCode']		=	$activeDiscCode;
		
											$_SESSION['payment']['NoMonthDisc']		=	$discMonth;
		
										}
		
									}else{
		
											//if discount code only have free period
		
											$totalDiscPer		+=	$AffdiscPer;
		
											//for database updation
		
											 $_SESSION['payment']['discId']			=	$activeDiscId;
		
											 $_SESSION['payment']['discCode']		=	$activeDiscCode;
									}	
		
								}	
		
						}
						
						$_SESSION['payment']['percentage']	= $totalDiscPer;
						$discount			=	$_SESSION['payment']['percentage'];
						
						if($discount > 0){
		
							$discAmt			=	($memShipFee*$discount)/100;
			
							$payFee				=	$memShipFee-$discAmt;
		
						}else{
		
							$payFee				=	$memShipFee;
		
						}
						$payFee				= 	round($payFee,2);
						
						
						if(isset($_SESSION['payment']['NoMonthDisc']))
		
						$discMonthPeriod	=	$_SESSION['payment']['NoMonthDisc'];
		
						else
		
						$discMonthPeriod	=	0;
						
						$subscription		=	$objDisc->_subscriptionMonthFees($memShipFee,$payFee,$discMonthPeriod,'');
						$_SESSION['payment']['payFee']	=	$payFee;
						
						if($freeDaysMore == ''){ $freeDaysMore	=	0; 	}
		
						$_SESSION['payment']['freedays']	=	$freeDaysMore;
						
						if($reffId != ''){
		
										if($reffCnt['cnt'] > 0){
		
											$reffType			=	'REFF';
		
										}elseif( $discCnt['cnt'] > 0 ){
		
											$reffType			=	'DISC';
		
											if(!isset($_SESSION['payment']['discCode'])){
		
												$_SESSION['payment']['discCode']	=	$reffId;
		
											}
		
										}
		
										$elmts['discount_code']				=	$reffId;
		
										$elmts['discount_type']				=	$reffType;
		
										$elmts['user_id']					=	$userId;
		
										$elmts['start_date']				=	date('Y-m-d');
		
										$elmts['payment_status']			=	'failed';
		
										$dbObj->_insertRecord("discount_users",$elmts);
		
										$disUserId=mysql_insert_id();
		
										$_SESSION['payment']['discUser_id']	= $disUserId;
		
								}
								
								
								
								if($payFee != ''){
		
									//insert payment page
		
										$payElmts['payment_userid']				=	$userId;
		
										$payElmts['payment_amount']				=	$payFee;
		
										$payElmts['payment_date']				=	date('Y-m-d');
		
										$payElmts['payment_status']				=	0;
										
										
										$dbObj->_insertRecord("payment",$payElmts);
										
										statusRecord($_SESSION['login']['user_email'],'payment insertion','userreg2.php',$userId,$payFee,'8');
		
										$payReffId=mysql_insert_id();
		
										$_SESSION['payment']['pay_id']	= $payReffId;
										$paymentTemp = array();

										$paymentTemp['user_id']		 		= $userId;
				
										$paymentTemp['pay_id'] 				= $_SESSION['payment']['pay_id'];
				
										$paymentTemp['discUser_id'] 		= $_SESSION['payment']['discUser_id'];
				
										$paymentTemp['ActReffId'] 			= $_SESSION['payment']['ActReffId'];
				
										$paymentTemp['freedays'] 			= $_SESSION['payment']['freedays'];
				
										$paymentTemp['payFee'] 				= $_SESSION['payment']['payFee'];
				
										$paymentTemp['user_email'] 			= $_SESSION['login']['user_email'];
				
										$paymentTemp['pay_date']			= date('Y-m-d');
										
										$paymentTemp['discCode'] 			= $_SESSION['payment']['discCode'];
				
										$paymentTemp['discId'] 				= $_SESSION['payment']['discId'];	
										
										$payEntry = $dbObj->_insertRecord("user_payment_temp",$paymentTemp);
		
								}
								
											
			}
								
	}
	else{
		//echo "aji";
		header("Location:payment1_reg.php");
		exit;
		}		
	?>