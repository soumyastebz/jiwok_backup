
<!--Program Register button starts-->
         <?php 
				if($workout_cnt>0)
					{ 
         				if(!($objPgm->_checkLogin())) 
							{  
				?><a href="javascript:;" class="btn-sign" name="subscribe" id="subscribe" onclick="window.location.href='login_failed.php?fromPgm=1&returnUrl=<?=$loginUrl?>&msg=<?=base64_encode(3)?>';" rel="nofollow"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'subscribe_unlog','name','UTF-8'));?></a><?php 
							}
	 					elseif($programType=="program")
	 						{ 
	   							$programDt1 = $objPgm->_getUserTrainingProgramConfirm($userid);
	  							if(count($programDt1) > 0) 
									{ 
	   			?><a href="javascript:;" class="btn-sign" name="subscribe" id="subscribe" onclick="displayConfirm();" rel="nofollow"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'subscribe','name','UTF-8'));?></a><?php				
	  								} 
								else 
									{ 
				?><a href="javascript:;" class="btn-sign" name="subscribe" id="subscribe" onclick="displayDate();" rel="nofollow"><?=mb_strtoupper($parObj->_getLabenames($arrayData,'subscribe','name','UTF-8'));?></a><?php 
									}
	  						}		
	  					else
							{ 
	  							if($workout_cnt>0)
									{
	  					?>
					  		<form name="tt" action="" method="get">
								<?php 
									if($objPgm->checkUserPaymentStaus($userid) == 0 && 
									  !$objPgm->checkProgramSubscribed($userid)):
								?>
								<a href="javascript:;" class="btn-sign" name="subscribe" id="subscribe" onclick="showSessionMessage();" rel="nofollow"><?=$parObj->_getLabenames($arrayData,'subscribe','name');?></a>
								<input name="subscribe" type="hidden" id="subscribe" value="<?=mb_strtoupper($parObj->_getLabenames($arrayData,'subscribe','name','UTF-8'));?>" />
								<?php 
									else:
								?>
								<a href="javascript:;" class="btn-sign" name="subscribe" id="subscribe" onclick="document.tt.submit();" rel="nofollow"><?=$parObj->_getLabenames($arrayData,'subscribe','name');?></a>
								<input name="subscribe" type="hidden" id="subscribe" value="<?=mb_strtoupper($parObj->_getLabenames($arrayData,'subscribe','name','UTF-8'));?>"/>
								<?php 
									endif;
								?>
								<input type="hidden" name="program_id" value="<?=base64_encode($program_id)?>" id="program_id" />
								<input type="hidden" name="todo" value="subscribe" id="todo" />
							</form>
								<?php 
									}  
							} 
					}	 
				?>
          <!--Program Register button ends-->
          