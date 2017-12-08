<?php
//~ $month ='3 month';
//~ $payment_expiry_date	=	date("Y-m-d", strtotime($month));
//~ $new = explode('-',$payment_expiry_date);
//~ $new[2]='02';
//~ $date= $new[0].'-'.$new[1].'-'.$new[2];
//~ print_r($date);exit;
//$date = date("Y-m-d");
$date ='2016-06-12';
$new = explode('-',$date);
if($new[2]< 15){
$month ='12 month';
$payment_expiry_date	=	date("Y-m-d", strtotime($month));
$new = explode('-',$payment_expiry_date);
$new[2]='02';
$date= $new[0].'-'.$new[1].'-'.$new[2];
print_r($date);exit;
}
else if($new[2] >= 15)
{
	$month ='13 month';
$payment_expiry_date	=	date("Y-m-d", strtotime($month));
$new = explode('-',$payment_expiry_date);
$new[2]='02';
$date= $new[0].'-'.$new[1].'-'.$new[2];
print_r($date);exit;
}
?>
