<?php


if(($response['error_code']	==	'00000') && ($validatePayboxreturns ))
{	
	if($resUserQry['user_language']	==	5)//user language
	{
		if($result4[0]['count'] > 0)
		{					
			//Make payment and extend the expiry date.
			$response['userType']	=	"polishstripe";				
			$newpaymentClass->afterPayment($response,$type = '1');
		}
		else
		{
			//Make payment.
			$response['userType']	=	"polishstripe";				
			$newpaymentClass->afterPayment($response,$type = '2');
		}
	}
	else
	{				
		if($result4[0]['count'] > 0)
		{					
			//Make payment and extend the expiry date.	
			$response['userType']	=	"stripe";				
			$newpaymentClass->afterPayment($response,$type = '1');
		}
		else
		{ //Make payment.
			$response['userType']	=	"stripe";				
			$newpaymentClass->afterPayment($response,$type = '2');
		}			
	}			
}






if(isset($_GET['errorcode']))
    {	
        $eror_code	=	$_GET['errorcode'];
        $err 		= 	"2";
        if($eror_code == '00000' || $eror_code == '50' ||	$eror_code == '51' || $eror_code == '52' || $eror_code == '53')
            $err 	= 	"1";
        $msg		=	$parObj->_getLabenames($arrayData,$eror_code,'name');
        if($msg	==	"")
            $msg		=	$parObj->_getLabenames($arrayData,'default','name');	
        //Special case
        if($eror_code	==	'00057')
        {	
            $message57	=	str_replace("##","<div style='color:#000000; float:left;padding-right:5px;'>info@jiwok.com</div>",$parObj->_getLabenames($arrayData,'57message','name'));
            $msg		.=	"<br/>".$message57;
        }
    }
    
    
    
    
   if(($response['error_code']	!=	'00000') || (!$validatePayboxreturns ))
				{ 
		$(document).ready(function(){
			//alert($( "input[name='payment_plan']:checked" ).val());	
			defaultPlanId = $( "input[name='payment_plan']:checked" ).val();
			$.ajax({
				url : 'payment_new.php',
				type: "POST",
				data: "updatePaymentPlan=1&payment_plan_id="+defaultPlanId,
				success: function(response){
					
					$('#newpaymentPageFrom').html(response);
						
				}
					
			});
		});
		} 
?>
