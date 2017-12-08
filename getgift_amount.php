<?php 
include_once("includes/config.php");

include('includes/classes/CurrencyConverter.php');

$month=$_GET['month']; 



$fee=$_GET['fee'];

$lanid=$_GET['lanid'];

$reusable=$_GET['reusable'];



if($lanid == 1)

{

 //$x = new CurrencyConverter(DB_HOST,DB_USER,DB_PASSWD,DB_NAME,'jiwok_currency');

//$priceval= str_replace(".",".",$x->convert($fee*$month,'EUR','USD'))." Dollar";

//$priceval= str_replace(",",",",$fee*$month).'$';
	$priceval= str_replace(",",",",$month).'$';

}

else

{
	if($lanid == 5)
	{
		$priceval= str_replace(".",",",$month).'PLN';
	}
	else
	{

	//$priceval= str_replace(".",",",$fee*$month).'&euro;';
	$priceval= str_replace(".",",",$month).'&euro;';
	}

}



echo  $priceval;



?>

