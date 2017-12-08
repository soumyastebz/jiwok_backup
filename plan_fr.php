<?php
    if($lanId	==	5)
    {?>
         <div class="payment-outer">
         
         <?php   if(sizeof($plan12) > 0){ ?>
            <div class="pament-tables first">
                 <div class="colums">
                   <h3><?=$parObj->_getLabenames($arrayDataOffer,'offer1','name');?></h3>
                   <span class="span_top"><?=$plan12['month_amount'].$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayDataOffer,'newMonthsTxt','name');?></span>
                   <div class="label"><img src="images/252.png"></div>
                   <div class="span_bottom"><span>-<?=$plan12['plan_discount']?>%</span><?=$parObj->_getLabenames($arrayDataOffer,'actualPaymentAmount','name').$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name');?></div>
                 </div>
                
            
            </div><?php}?>
            <? if(sizeof($plan6) > 0){ ?>  
            <div class="pament-tables">
                 <div class="colums">
                   <h3><?=$parObj->_getLabenames($arrayDataOffer,'offer2','name');?></h3>
                   <span class="span_top"> <?=$plan6['month_amount'].$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayDataOffer,'newMonthsTxt','name');?></span>
                   <div class="label"><img src="images/150.png"></div>
                   <div class="span_bottom"><span>-<?=$plan6['plan_discount']?>%</span><?=$parObj->_getLabenames($arrayDataOffer,'newOfferCapt','name');?></div>
                 </div>
            </div><?php}?>
            
          <? if(sizeof($plan3) > 0){?> 
            <div class="pament-tables">
                 <div class="colums">
                   <h3><?=$parObj->_getLabenames($arrayDataOffer,'offer3','name');?></h3>
                   <span class="span_top"> <?=$plan3['month_amount'].$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayDataOffer,'newMonthsTxt','name');?></span>
                   <div class="label"><img src="images/85.png"></div>
                   <div class="span_bottom"><span>-<?=$plan3['plan_discount']?>%</span><?=$parObj->_getLabenames($arrayDataOffer,'newOfferCapt','name');?></div>
                 </div>
                 
            </div><?}?>
            <? if(sizeof($plan1) > 0){?>    
            <div class="pament-tables">
                 <div class="colums">
                   <h3><?=$parObj->_getLabenames($arrayDataOffer,'offer4','name');?></h3>
                   <span class="span_top"><?=$plan1['month_amount'].$parObj->_getLabenames($arrayDataOffer,'newCurrencyTxt','name').'/'.$parObj->_getLabenames($arrayDataOffer,'singleMonth','name');?></span>
                   <div class="label"><img src="images/35.png"></div>
                   <div class="span_bottom"><span>-50%</span>par rapport au plein tarif</div>
                 </div>
            </div><?php?>
            
        </div>
         <div class="workout">
           <h3>Za co dok?adnie p?ac? korzystaj?c z serwisu Jiwok?</h3>
           <p>W ten prosty sposób, za pomocą pojedynczego kliknięcia, możesz zrobić bliskiej Ci osobie oryginalny, praktyczny i sportowy prezent: Treningi sportowe z osobistym trenerem na mp3! .
To naprawdę proste: przesyłasz bon e-mailem lub drukujesz</p>
<a href="#" class="btn">Informations bancaires</a>
         </div>
         
      </div> 
     
      </section>
      
      <section class="plan-gift">
      	<div class="frame3">
        	<div class="gift-left">
            	<img src="images/plan-gift.jpg" alt="" />
            </div>
            <div class="gift-right">
            	<h5>Par ailleurs, Jiwok propose des bons cadeaux.</h5>
                <p>Ainsi, vous pouvez en quelques clics, offrir un cadeau original, ludique et sportif : Un coach mp3. Très simple à offrir : soit directement par e-mail soit en l’imprimant (et vous le pliez dans une enveloppe). </p>
                <a href="#" class="small">Offrez un bon cadeau Jiwok maintenant</a>
                <h5>A propos de votre abonnement Jiwok: </h5>
                <p>Pour éviter toute discontinuité de votre pass, votre abonnement Jiwok sera renouvelé automatiquement sur une période équivalente à celle initialement souscrite. Vous pouvez bien sûr annuler le renouvellement automatique de votre abonnement à tout moment à partir de la page "Mon compte". La notification de résilier l'abonnement devra être faite par le membre à Jiwok au plus tard 48 h avant la date d'échéance de l'abonnement en cours. .</p>
            </div>
       <?}?>
