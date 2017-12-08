<?php if($lanId == 5) { ?>
<style> .offer_section .right .voucher{padding-bottom:26px;height:203px;background:url("images/voucher_pl.png") no-repeat 0 0;} </style>
<?php } else { ?> 
<style> .offer_section .right .voucher{padding-bottom:26px;height:203px;background:url("images/voucher.jpg") no-repeat 0 0;} </style> <?php } 
$returnDataOffer		= $parObj->_getTagcontents($xmlPath,'offerSection_NewDesign','label');
$arrayDataOffer		= $returnDataOffer['general'];

$returnData_new				=	$parObj->_getTagcontents($xmlPath,'new_payment','label');
$arrayData_new1			= 	$returnData_new['general'];


//By Dileep.E on 01.10.11
//For the new payment plan listing
//For multi language implimentation
if($lanId == 3 || $lanId	==	4)
	$planLanguage	=	2;	
else
	$planLanguage	=	$lanId;
$selectQuery  = "select * from jiwok_payment_plan where plan_status=1 and  plan_currency='".$planLanguage."'";
$result 		= 	$GLOBALS['db']->getAll($selectQuery,DB_FETCHMODE_ASSOC);
$i=0;
foreach($result as $key=>$value)
{
	if($value['plan_id']==1)
	{	
		$plan1['month_amount']	=	$value['plan_month_amount'];
		$plan1['plan_amount']	=	$value['plan_amount'];
		$plan1['plan_discount']	=	$value['plan_discount'];	
	}
	if($value['plan_id']==2)
	{	
		$plan2['month_amount']	=	$value['plan_month_amount'];
		$plan2['plan_amount']	=	$value['plan_amount'];
		$plan2['plan_discount']	=	$value['plan_discount'];	
	}
	if($value['plan_id']==3)
	{	
		$plan3['month_amount']	=	$value['plan_month_amount'];
		$plan3['plan_amount']	=	$value['plan_amount'];
		$plan3['plan_discount']	=	$value['plan_discount'];	
	}
	if($value['plan_id']==6)
	{	
		$plan6['month_amount']	=	$value['plan_month_amount'];
		$plan6['plan_amount']	=	$value['plan_amount'];
		$plan6['plan_discount']	=	$value['plan_discount'];	
	}
	if($value['plan_id']==12)
	{	
		$plan12['month_amount']	=	$value['plan_month_amount'];
		$plan12['plan_amount']	=	$value['plan_amount'];
		$plan12['plan_discount']	=	$value['plan_discount'];	
	}
}
//Ends

?>

  <section class="plan plan-content-middle">
        <div class="frame3">
    <?php
    if($lanId	==	5)
    {?><div class="payment-outer">
    
      <?php   if(sizeof($plan12) > 0){ ?>
       <div class="pament-tables plan-box first">
                 <div class="colums">
                   <h3><?=$parObj->_getLabenames($arrayDataOffer,'offer1','name');?></h3>
                   <span class="span_top"><?=$plan12['month_amount'].$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayDataOffer,'newMonthsTxt','name');?></span>
                   <div class="label"><img src="<?=ROOT_FOLDER?>images/252.png"></div>
                   <div class="span_bottom"><span>-<?=$plan12['plan_discount']?>%</span><?=$parObj->_getLabenames($arrayDataOffer,'actualPaymentAmount','name').$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name');?></div>
                 </div>
                
            
            </div>
      <?php }
      if(sizeof($plan6) > 0){ ?>  
         <div class="pament-tables plan-box">
                 <div class="colums">
                   <h3><?=$parObj->_getLabenames($arrayDataOffer,'offer2','name');?></h3>
                   <span class="span_top"> <?=$plan6['month_amount'].$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayDataOffer,'newMonthsTxt','name');?></span>
                   <div class="label"><img src="<?=ROOT_FOLDER?>images/150.png"></div>
                   <div class="span_bottom"><span>-<?=$plan6['plan_discount']?>%</span><?=$parObj->_getLabenames($arrayDataOffer,'newOfferCapt','name');?></div>
                 </div>
            </div>
      <?php }
	  if(sizeof($plan3) > 0){?>    
          <div class="pament-tables plan-box">
                 <div class="colums">
                   <h3><?=$parObj->_getLabenames($arrayDataOffer,'offer3','name');?></h3>
                   <span class="span_top"> <?=$plan3['month_amount'].$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayDataOffer,'newMonthsTxt','name');?></span>
                   <div class="label"><img src="<?=ROOT_FOLDER?>images/85.png"></div>
                   <div class="span_bottom"><span>-<?=$plan3['plan_discount']?>%</span><?=$parObj->_getLabenames($arrayDataOffer,'newOfferCapt','name');?></div>
                 </div>
                 
            </div>
          <?php }		 
		  if(sizeof($plan1) > 0){?>    
          <div class="pament-tables plan-box">
                 <div class="colums">
                   <h3><?=$parObj->_getLabenames($arrayDataOffer,'offer4','name');?></h3>
                   <span class="span_top"><?=$plan1['month_amount'].$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayDataOffer,'singleMonth','name');?></span>
                   <div class="label"><img src="<?=ROOT_FOLDER?>images/35.png"></div>
                   <div class="span_bottom"><span></span></div>
                 </div>
            </div>
          <?php }?>
          </div>
          <div class="workout">
           <h3><?=$parObj->_getLabenames($arrayDataOffer,'newMoreGftTxt','name');?>.</h3>
           <p><?=$parObj->_getLabenames($arrayDataOffer,'newMoreGftDesc1Txt','name');?>.<br />
              <?=$parObj->_getLabenames($arrayDataOffer,'newMoreGftDesc2Txt','name');?> <br />
            <?=$parObj->_getLabenames($arrayDataOffer,'newMoreGftDesc3Txt','name');?>.            </p>
<a href="<?=ROOT_JWPATH?>userreg1.php" class="btn"><?=$parObj->_getLabenames($arrayDataOffer,'newSignOfferTxt','name');?> </a>
<p align="center">

</p>
         </div>
     <?php
	}
	else
	{?>
		 
      <div class="payment-outer">
		   <?php   if(sizeof($plan12) > 0){ ?>
            <div class="pament-tables plan-box first">
                 <div class="colums">
                   <h3><?=$parObj->_getLabenames($arrayDataOffer,'offer1','name');?>:</h3>
                   <span class="span_top">
                    <?=$parObj->_getLabenames($arrayDataOffer,'invoicedetails1','name')." ".$plan12['plan_amount']." ".$parObj->_getLabenames($arrayDataOffer,'currency','name');?>  </span>
                   <div class="label"><img src="<?=ROOT_FOLDER?>images/4.90.png"></div>
                   <div class="span_bottom"><span>-<?=$plan12['plan_discount']?>%</span><?=$parObj->_getLabenames($arrayDataOffer,'newOfferCapt','name');?></div>
                 </div>
                 
            
            </div>
            <?php }
      if(sizeof($plan6) > 0){ ?>
            <div class="pament-tables plan-box">
                 <div class="colums">
                   <h3><?=$parObj->_getLabenames($arrayDataOffer,'offer2','name');?>:</h3>
                   <span class="span_top">
                   <?=$parObj->_getLabenames($arrayDataOffer,'invoicedetails1','name')." ".$plan6['plan_amount']." ".$parObj->_getLabenames($arrayDataOffer,'currency','name');?> 
                   </span>
                   <div class="label"><img src="<?=ROOT_FOLDER?>images/5.80.png"></div>
                   <div class="span_bottom"><span>-<?=$plan6['plan_discount']?>%</span><?=$parObj->_getLabenames($arrayDataOffer,'newOfferCapt','name');?></div>
                 </div>
              
            </div>
            <?php }
	  if(sizeof($plan3) > 0){?>
            <div class="pament-tables plan-box">
                 <div class="colums">
                   <h3><?=$parObj->_getLabenames($arrayDataOffer,'offer3','name');?>:</h3>
                   <span class="span_top">
                   <?=$parObj->_getLabenames($arrayDataOffer,'invoicedetails1','name')." ".$plan3['plan_amount']." ".$parObj->_getLabenames($arrayDataOffer,'currency','name');?> </span>
                   <div class="label"><img src="<?=ROOT_FOLDER?>images/6.63.png"></div>
                   <div class="span_bottom"><span>-<?=$plan3['plan_discount']?>%</span><?=$parObj->_getLabenames($arrayDataOffer,'newOfferCapt','name');?></div>
                 </div>
                 
            </div>
            <?php  }
		  if(sizeof($plan2) > 0){?>
            <div class="pament-tables plan-box">
                 <div class="colums">
                   <h3>  <?=$plan2['month_amount']?> <?=$parObj->_getLabenames($arrayDataOffer,'newForTxt','name');?><?=$parObj->_getLabenames($arrayDataOffer,'newMonthsTxt','name');?></h3>
                   <span class="span_top"> <?=$parObj->_getLabenames($arrayDataOffer,'invoicedetails3','name')." ".$plan2['plan_amount']." ".$parObj->_getLabenames($arrayDataOffer,'currency','name');?></span>
                   <div class="label"><img src="<?=ROOT_FOLDER?>images/9.90.png"></div>
                   <div class="span_bottom"><span><?=$plan2['plan_discount']?></span><?=$parObj->_getLabenames($arrayDataOffer,'newOfferCapt','name');?></div>
                 </div>
                 
            </div> <?php }
		  if(sizeof($plan1) > 0){?>
			  
			  
			   <div class="pament-tables plan-box">
                 <div class="colums">
                   <h3><?=$parObj->_getLabenames($arrayDataOffer,'offer4','name');?>
              </h3>
                   <span class="span_top">
              <?=$parObj->_getLabenames($arrayData_new1,'planNewTxt','name');?>:<?=$plan1['month_amount'];?>
              <?=$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name');?>
              /
              <?=$parObj->_getLabenames($arrayDataOffer,'newMonthsTxt','name');?>
              </span>
                   <div class="label"><img src="<?=ROOT_FOLDER?>images/9.90.png"></div>
                   <div class="span_bottom"><span></span></div>
                 </div>
			  
			  
			  
			  <?}?>
        </div>
        <div class="workout">
<!--
           <h3>Za co dok?adnie p?ac? korzystaj?c z serwisu Jiwok?</h3>
           <p>W ten prosty sposób, za pomocą pojedynczego kliknięcia, możesz zrobić bliskiej Ci osobie oryginalny, praktyczny i sportowy prezent: Treningi sportowe z osobistym trenerem na mp3! .
To naprawdę proste: przesyłasz bon e-mailem lub drukujesz</p>
-->
<a href="<?=ROOT_JWPATH?>userreg1.php" class="btn"><?=mb_strtoupper($parObj->_getLabenames($arrayDataOffer,'newSignOfferTxt','name'),'UTF-8');?></a>
         </div>
        
        
        
	<?}?>
    </div>
</section>
<?php
    if($lanId	==	2)
    {?>
		 <section class="plan-gift">
      	<div class="frame3">
        	<div class="gift-left">
            	<img src="<?=ROOT_FOLDER?>images/voucher.jpg" alt="" />
            </div>
            <div class="gift-right">
            	<h5><?=$parObj->_getLabenames($arrayDataOffer,'newMoreGftTxt','name');?></h5>
                <p> <?=$parObj->_getLabenames($arrayDataOffer,'newMoreGftDesc1Txt','name');?>
        .<br />
        <?=$parObj->_getLabenames($arrayDataOffer,'newMoreGftDesc2Txt','name');?>
        <br />
        <?=$parObj->_getLabenames($arrayDataOffer,'newMoreGftDesc3Txt','name');?></p>
                <a href="<?=ROOT_JWPATH?>giftreg.php" class="small"><?=$parObj->_getLabenames($arrayDataOffer,'newMoreGftDesc4Txt','name');?></a>
                <h5><?=$parObj->_getLabenames($arrayDataOffer,'newBtmTxtHead','name');?>:  </h5>
                <p> <?=$parObj->_getLabenames($arrayDataOffer,'newBtmTxt','name');?></p>
            </div>
        </div>
      </section>
		
		<?php } ?>
