<?php
ini_set('display_errors',1);
error_reporting(E_ERROR | E_PARSE);
?>
<?php
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

 
    <?php
    if($lanId	==	5)
    {?>  <div class="payment-outer">
    <ul id="billing">
      <?php   if(sizeof($plan12) > 0){ ?>
		  
		  <div class="pament-tables first">
                 <div class="colums">
                   <h3> <?=mb_strtoupper($parObj->_getLabenames($arrayDataOffer,'offer1','name'),'UTF-8');?></h3>
                   <span class="span_top"><?=$plan12['month_amount'].$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayDataOffer,'newMonthsTxt','name');?>
                    <?=$parObj->_getLabenames($arrayDataOffer,'actualPaymentAmount','name').$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name');?></span>
                   <div class="label"><img src="<?=ROOT_FOLDER?>images/252.png"></div>
                   <div class="span_bottom"><span>-<?=$plan12['plan_discount']?>%</span><?=$parObj->_getLabenames($arrayDataOffer,'newOfferCapt','name');?></div>
                 </div>
            </div>
		
      
      <?php }
      if(sizeof($plan6) > 0){ ?>
		   <div class="pament-tables">
                 <div class="colums">
                   <h3><?=mb_strtoupper($parObj->_getLabenames($arrayDataOffer,'offer2','name'),'UTF-8');?></h3>
                   <span class="span_top"> <?=$plan6['month_amount'].$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayDataOffer,'newMonthsTxt','name');?><?=$parObj->_getLabenames($arrayDataOffer,'actualPaymentAmount','name').$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name');?></span>
                   <div class="label"><img src="<?=ROOT_FOLDER?>images/150.png"></div>
                   <div class="span_bottom"><span>-<?=$plan6['plan_discount']?>%</span> <?=$parObj->_getLabenames($arrayDataOffer,'newOfferCapt','name');?></div>
                 </div>
            </div>
		  
      <?php }
	  if(sizeof($plan3) > 0){?>
		   <div class="pament-tables">
                 <div class="colums">
                   <h3><?=mb_strtoupper($parObj->_getLabenames($arrayDataOffer,'offer3','name'),'UTF-8');?></h3>
                   <span class="span_top"><?=$plan3['month_amount'].$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayDataOffer,'newMonthsTxt','name');?> <?=$parObj->_getLabenames($arrayDataOffer,'actualPaymentAmount','name').$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name');?></span>
                   <div class="label"><img src="<?=ROOT_FOLDER?>images/85.png"></div>
                   <div class="span_bottom"><span>-<?=$plan3['plan_discount']?>%</span><?=$parObj->_getLabenames($arrayDataOffer,'newOfferCapt','name');?></div>
                 </div>
            </div>
		  
      <?php }		 
		  if(sizeof($plan1) > 0){?>
<!--
			   <div class="pament-tables">
                 <div class="colums">
                   <h3> <?=$parObj->_getLabenames($arrayDataOffer,'offer4','name');?><br>
              (
              <?=$parObj->_getLabenames($arrayData_new1,'planNewTxt','name');?>
              ): </h3>
                   <span class="span_top"><?=$plan1['month_amount'].$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayDataOffer,'singleMonth','name');?></span>

                   <div class="label"><img src="images/85.png"></div>
                   <div class="span_bottom"><span>-<?=$plan3['plan_discount']?>%</span><?=$parObj->_getLabenames($arrayDataOffer,'newOfferCapt','name');?></div>
                 </div>
            </div>
-->
			 <div class="pament-tables">
                 <div class="colums">
                    <h3> <?=mb_strtoupper($parObj->_getLabenames($arrayDataOffer,'offer4','name'),'UTF-8');?></h3>
                   <span class="span_top"><?=$plan1['month_amount'].$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayDataOffer,'singleMonth','name');?></span>
                   <div class="label"><img src="<?=ROOT_FOLDER?>images/85.png"></div>
                   <div class="span_bottom"><span>
              <?=$parObj->_getLabenames($arrayData_new1,'planNewTxt','name');?>
              </span></div>
                 </div>
                
            </div>
      <?php }?>
    </ul></div>
    <?php
	}
	// need to check languages other than polish
	else
	{?>
    <ul id="billing">
      <?php   if(sizeof($plan12) > 0){ ?>
      <li class="ylow">
        <div class="corner"></div>
        <table width="327" border="0" cellspacing="2" cellpadding="0">
          <tr>
            <td width="20" align="left" valign="middle"></td>
            <td width="301"><span class="xxxNew">
              <?=$parObj->_getLabenames($arrayDataOffer,'offer1','name');?>: </span> <span class="x" id="xAmt">
              <?=$plan12['month_amount'];?>
              </span><span class="xxxxNew">
              <?=$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name');?>
              /
              <?=$parObj->_getLabenames($arrayDataOffer,'newMonthsTxt','name');?>
              </span><br/>
              <div class="paymentDesc"> <span class="x" id="paymentDescNew">
                <?=$parObj->_getLabenames($arrayDataOffer,'actualPaymentTxt','name')." ";?>
                </span><span class="xstrike" id="paymentDescNew">
                <?=$parObj->_getLabenames($arrayDataOffer,'actualPaymentAmount','name').$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name');?>
                </span> <br/>
                <?=$parObj->_getLabenames($arrayDataOffer,'invoicedetails1','name')." ".$plan12['plan_amount']." ".$parObj->_getLabenames($arrayDataOffer,'currency','name');?>
              </div></td>
          </tr>
        </table>
        <div class="discount_1"><strong>-<?=$plan12['plan_discount']?>%</strong><br />
          <span class="oferText">
          <?=$parObj->_getLabenames($arrayDataOffer,'newOfferCapt','name');?>
          </span> </div>
      </li>
      <?php }
      if(sizeof($plan6) > 0){ ?>
      <li class="blue">
        <div class="corner"></div>
        <table width="327" border="0" cellspacing="2" cellpadding="0">
          <tr>
            <td width="20" align="left" valign="middle"></td>
            <td width="301"><span class="xxxNew">
              <?=$parObj->_getLabenames($arrayDataOffer,'offer2','name');?>: </span> <span class="x" id="xAmt">
              <?=$plan6['month_amount'];?>
              </span><span class="xxxxNew">
              <?=$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name');?>
              /
              <?=$parObj->_getLabenames($arrayDataOffer,'newMonthsTxt','name');?>
              </span><br/>
              <div class="paymentDesc"> <span class="x" id="paymentDescNew">
                <?=$parObj->_getLabenames($arrayDataOffer,'actualPaymentTxt','name')." ";?>
                </span><span class="xstrike" id="paymentDescNew">
                <?=$parObj->_getLabenames($arrayDataOffer,'actualPaymentAmount','name').$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name');?>
                </span> <br/>
                <?=$parObj->_getLabenames($arrayDataOffer,'invoicedetails1','name')." ".$plan6['plan_amount']." ".$parObj->_getLabenames($arrayDataOffer,'currency','name');?>
              </div></td>
          </tr>
        </table>
        <div class="discount_2"><strong>-<?=$plan6['plan_discount']?>%</strong><br />
          <span class="oferText">
          <?=$parObj->_getLabenames($arrayDataOffer,'newOfferCapt','name');?>
          </span> </div>
      </li>
      <?php }
	  if(sizeof($plan3) > 0){?>
      <li class="blue">
        <div class="corner"></div>
        <div class="corner-rit"></div>
        <table width="327" border="0" cellspacing="2" cellpadding="0">
          <tr>
            <td width="20" align="left" valign="middle"></td>
            <td width="301"><span class="xxxNew">
              <?=$parObj->_getLabenames($arrayDataOffer,'offer3','name');?>: </span> <span class="x" id="xAmt">
              <?=$plan3['month_amount'];?>
              </span><span class="xxxxNew">
              <?=$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name');?>
              /
              <?=$parObj->_getLabenames($arrayDataOffer,'newMonthsTxt','name');?>
              </span><br/>
              <div class="paymentDesc"> <span class="x" id="paymentDescNew">
                <?=$parObj->_getLabenames($arrayDataOffer,'actualPaymentTxt','name')." ";?>
                </span><span class="xstrike" id="paymentDescNew" >
                <?=$parObj->_getLabenames($arrayDataOffer,'actualPaymentAmount','name').$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name');?>
                </span> <br/>
                <?=$parObj->_getLabenames($arrayDataOffer,'invoicedetails1','name')." ".$plan3['plan_amount']." ".$parObj->_getLabenames($arrayDataOffer,'currency','name');?>
              </div></td>
          </tr>
        </table>
        <div class="discount_2"><strong>-<?=$plan3['plan_discount']?>%</strong><br />
          <span class="oferText">
          <?=$parObj->_getLabenames($arrayDataOffer,'newOfferCapt','name');?>
          </span> </div>
      </li>
      <?php }
		  if(sizeof($plan2) > 0){?>
      <li class="blue">
        <div class="corner"></div>
        <div class="corner-rit"></div>
        <table width="327" border="0" cellspacing="2" cellpadding="0">
          <tr>
            <td width="20" align="left" valign="middle"></td>
            <td width="301"><span class="xxxNew">
              <?=$plan2['month_amount']?>
              </span><span class="xx">
              <?=$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name');?>
              </span> <span class="x">
              <?=$parObj->_getLabenames($arrayDataOffer,'newForTxt','name');?>
              </span> <span class="xxxxNew"> 2
              <?=$parObj->_getLabenames($arrayDataOffer,'newMonthsTxt','name');?>
              </span><br />
              <div class="paymentDesc">
                <?=$parObj->_getLabenames($arrayDataOffer,'invoicedetails3','name')." ".$plan2['plan_amount']." ".$parObj->_getLabenames($arrayDataOffer,'currency','name');?>
              </div></td>
          </tr>
        </table>
        <div class="discount_2"><strong>-<?=$plan2['plan_discount']?>%</strong><br />
          <span class="oferText">
          <?=$parObj->_getLabenames($arrayDataOffer,'newOfferCapt','name');?>
          </span> </div>
      </li>
      <?php }
		  if(sizeof($plan1) > 0){?>
      <li class="blue">
        <div class="corner"></div>
        <div class="corner-rit"></div>
        <table width="378" border="0" cellspacing="2" cellpadding="0">
          <tr>
            <td width="20" align="left" valign="middle"></td>
            <td width="348"><span class="xxxNew">
              <?=$parObj->_getLabenames($arrayDataOffer,'offer4','name');?>
              (
              <?=$parObj->_getLabenames($arrayData_new1,'planNewTxt','name');?>
              ):</span> <span class="x" id="xAmt">
              <?=$plan1['month_amount'];?>
              </span><span class="xxxxNew">
              <?=$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name');?>
              /
              <?=$parObj->_getLabenames($arrayDataOffer,'newMonthsTxt','name');?>
              </span><br/>
              <div class="paymentDesc" style="display:none;">
                <?=$parObj->_getLabenames($arrayDataOffer,'invoicedetails1','name')." ".$plan1['plan_amount']." ".$parObj->_getLabenames($arrayDataOffer,'currency','name');?>
              </div></td>
          </tr>
        </table>
      </li>
      <?php }?>
    </ul>
    <?php
	}?>
    <!--<p align="center"><a href="<?=ROOT_JWPATH?>userreg1.php" class="large"><?=$parObj->
    _getLabenames($arrayDataOffer,'newSignOfferTxt','name');?> </a>
    </p>
    -->
   
<!-- neethu
  <div>
    <div class="right">
      <div class="voucher">&nbsp;</div>
      <h2>
        <?=$parObj->_getLabenames($arrayDataOffer,'newMoreGftTxt','name');?>
        .</h2>
      <p>
        <?=$parObj->_getLabenames($arrayDataOffer,'newMoreGftDesc1Txt','name');?>
        .<br />
        <?=$parObj->_getLabenames($arrayDataOffer,'newMoreGftDesc2Txt','name');?>
        <br />
        <?=$parObj->_getLabenames($arrayDataOffer,'newMoreGftDesc3Txt','name');?>
        . </p>
      <a href="<?=ROOT_JWPATH?>giftreg.php" class="button-4">
      <?=$parObj->_getLabenames($arrayDataOffer,'newMoreGftDesc4Txt','name');?>
      </a> <br />
      <br />
      <?php
			if($lanId != 5)
			{?>
      <div class="regulations"> <b>
        <?=$parObj->_getLabenames($arrayDataOffer,'newBtmTxtHead','name');?>: </b><br  />
        <br />
        <?=$parObj->_getLabenames($arrayDataOffer,'newBtmTxt','name');?>
        . </div>
      <?php
			}?>
    </div>
  </div>
  <div class="clear"></div>
neethu-->

