<?php

//chmod('./uploads/regdetails.txt','0777');
function statusRecord($email='nil',$stage='nil',$page='nil',$id='nil',$amnt='nil',$ref='nil'){
$stringData="\n".$email."--------".$stage."--------".$page."-------".$id."-----".$amnt."------".$ref."--".date('Y-m-d H:i:s')."\n";
$myFile = "./uploads/regdetails.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
fwrite($fh, $stringData);
fclose($fh);
}

function oxylaneCardBalance($email='nil',$stage='nil',$userid='nil',$balance='nil',$card_no='nil')
{
$stringData="\n".$email."--------".$stage."--------".$id."-----".$balance."------".$card_no."--".date('Y-m-d H:i:s')."\n";
$myFile = "./uploads/oxy_cardbalance.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
fwrite($fh, $stringData);
fclose($fh);
}

function oxylaneRedeemCard($email='nil',$stage='nil',$userid='nil',$status='nil',$card_no='nil',$pay_fee='nil')
{
$stringData="\n".$email."--------".$stage."--------".$id."-----".$status."------".$card_no."--".$pay_fee."--".date('Y-m-d H:i:s')."\n";
$myFile = "./uploads/oxy_redemcard.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
fwrite($fh, $stringData);
fclose($fh);
}


?>
