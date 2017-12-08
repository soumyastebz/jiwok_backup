<form action="" name="oxyform" method="post">
<div class="voucher_section">   
        <h2><?=$parObj->_getLabenames($arrayData,'newGiftHeadTxt','name');?></h2>
        <div class="right-acc-login">
            <div class="log-sec">
                <img src="images/login-card.png" alt="" />
            </div>
            <div class="log-form">
<table width="100%" border="0" cellspacing="2" cellpadding="0">
  <tbody>
      <tr>
 <?php if($paid_user){ ?><br /> <?=$paid_user; } else if($err_user_gift_card)	{?><br /><?=$err_user_gift_card; }?>
    <td align="right" valign="middle">
		<?=$parObj->_getLabenames($arrayData,'newJCodext','name');?> 
		<a href="javascript:void(0)" onmouseover="tooltip('<html><br> <img src=\'images/popajaxexcplainjcode.png\' /></html>');" onmouseout="exit();"><img src="images/help-new.png" alt="Help">
		</a>      
        
	</td>
    <td align="left" valign="top">
		<input name="Jcode" type="text" class="tfl-3" value="<? echo $_POST['Jcode']; ?>">
         <?php if($err_user_gift_jiwok){?><br />
			<?=$err_user_gift_jiwok; ?>
	 <?php }?>
	 	</td>
  </tr>
  <tr>
    <td align="right" valign="middle">
	<?=$parObj->_getLabenames($arrayData,'newBarCdeTxt','name');?>
     <a href="javascript:void(0)" onmouseover="tooltip('<html><br> <img src=\'images/popajaxexcplainnumber.png\' /></html>');" onmouseout="exit();"><img src="images/help-new.png" alt="Help">
	</a>  
    
	</td>
    <td>
	<input name="giftcard" type="text" class="tfl-3" value="<? echo $_POST['giftcard']; ?>">
    <?php if($err_user_card){?>
		<br />
		<?=$err_user_card; ?>
		<?php }?>
		</td>
  </tr>
</tbody></table>
  </div>
 </div>
        <div class="right-login-btn">
       <!-- <h2>Valider ma commande</h2>-->
        <input name="oxysubmit" type="submit" value="<?=$parObj->_getLabenames($arrayData,'newGiftBtnTxt','name');?>" class="style2" />
        </div>
 </div>
               </form>