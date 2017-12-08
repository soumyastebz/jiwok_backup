<?php 
$getAllTrainCats	= $objTraining->getCategories($lanId);
//echo "<pre/>";print_r($getAllTrainCats);exit;
if($lanId==1)
	{
		$search_link	=	"training";
	} 
else 
	{
		$search_link	=	"entrainement";
	}
?>

<?php 
/*-----------------------------------------------*/


$totalCategories	 = count($getAllTrainCats);
$firstCol			 =	ceil($totalCategories / 3);
$lastCol        	 = $totalCategories - (2 * $firstCol);
$colArray 			 = array($firstCol, $firstCol, $lastCol);
$colIndex = 0;

//echo $firstCol.':'.$firstCol.':'.$lastCol;

   //foreach($getAllTrainCats as $i=>$val){
	   
	   	foreach($colArray as $col){
			
			?>
            <div class="colums">
            <?php
                 
			for($count=0; $count < $col; $count++){
				$i = $colIndex;
				
				//print_r ($getAllTrainCats[$colIndex] ) ;
				//=====================
				$getAllTrainSubCats	= $objTraining->getCategories($lanId,$getAllTrainCats[$i]['flex_id']);
			//echo "<pre/>";print_r($getAllTrainCats[$i]['url'] );
			if($getAllTrainCats[$i]['url']=='')
				{   $getAllTrainCats[$i]['url'] 	= 	 $objTraining->makeCategoryTitle($getAllTrainCats[$i]['category_name']);
					$getAllTrainCats[$i]['url']		=	 strtolower($getAllTrainCats[$i]['url']);
					$getAllTrainCats[$i]['url']		=	 $objTraining->normal_url($getAllTrainCats[$i]['url']);
				 	$getAllTrainCats[$i]['url']		=	 urlencode($getAllTrainCats[$i]['url']);				
					$getAllTrainCats[$i]['url']		=	 str_replace("+","-",$getAllTrainCats[$i]['url']);	
					//$getAllTrainCats[$i]['url']	=	 str_replace('%2F', ' %252F', $getAllTrainCats[$i]['url']); comment by anu
					$getAllTrainCats[$i]['url']		=	 str_replace('%2F', 'FF', $getAllTrainCats[$i]['url']);	
					$getAllTrainCats[$i]['url']		=	 $search_link.'/'.$getAllTrainCats[$i]['url']."-".$getAllTrainCats[$i]['flex_id'];
					//print_r($getAllTrainCats[$i]['url'] );
				} 
			else 
				{
					$getAllTrainCats[$i]['url']		=	 strtolower($getAllTrainCats[$i]['url']);
					$getAllTrainCats[$i]['url']		=	 urlencode($getAllTrainCats[$i]['url']);
					$getAllTrainCats[$i]['url']		=	 str_replace("+","-",$getAllTrainCats[$i]['url']);
					// If '/' is the last character, it should not be urlencoded. If so search and replace it back to '/'.
					$reg_exp						=	 '/^(.+)%2F$/';
					$getAllTrainCats[$i]['url']		=	 preg_replace($reg_exp, '${1}/', $getAllTrainCats[$i]['url']);
				}
				
				
				 if(count($getAllTrainSubCats)>0)
				{ 
				if($lanId==1)
						{
							$search_link = "training";
						} 
					else 
						{
							$search_link = "entrainement";
						}
						//========================
						 
				if($lanId==1)
						{
							$search_link = "training";
						} 
					else 
						{
							$search_link = "entrainement";
						}?>
                
                <a id="topicName" href="javascript:void(0)" onClick="showhide('itoms_0<?=$i;?>');" class="itom_s has-sub"><?php echo ucfirst(htmlentities($getAllTrainCats[$i]['category_name'], ENT_COMPAT, 'UTF-8')); ?></a>
                
                <!--=============================-->
                
                <div class="expand" id="itoms_0<?=$i;?>">
                      <ul>
			<?php 
				$menuCount = 1;
				for($j=0, $maxSub=count($getAllTrainSubCats);$j<$maxSub;$j++)
					{ 
						if($getAllTrainSubCats[$j]['url']=='')
							{ 
								$getAllTrainSubCats[$j]['url']  = $objTraining->makeCategoryTitle($getAllTrainSubCats[$j]['category_name']);
								
								$getAllTrainSubCats[$j]['url']  = strtolower($getAllTrainSubCats[$j]['url']);
								$getAllTrainSubCats[$j]['url']	= $objTraining->normal_url($getAllTrainSubCats[$j]['url']);
								
								$getAllTrainSubCats[$j]['url']	= urlencode($getAllTrainSubCats[$j]['url']);
								
								$getAllTrainSubCats[$j]['url']	=	str_replace("+","-",$getAllTrainSubCats[$j]['url']);
								
								
								//$getAllTrainCats[$i]['url']	= str_replace('%2F', ' %252F', $getAllTrainCats[$i]['url']); comment by anu
								
								$getAllTrainSubCats[$j]['url']	= str_replace('%2F', 'FF', $getAllTrainSubCats[$j]['url']);								
								$getAllTrainSubCats[$j]['url']	= $search_link.'/'.$getAllTrainSubCats[$j]['url']."-".$getAllTrainSubCats[$j]['flex_id'];
							} 
						else 
							{ 
								 $getAllTrainSubCats[$j]['url']	= strtolower($getAllTrainSubCats[$j]['url']);
								 $getAllTrainSubCats[$j]['url']	= urlencode($getAllTrainSubCats[$j]['url']);
								 $getAllTrainSubCats[$j]['url']	=	str_replace("+","-",$getAllTrainSubCats[$j]['url']);
								// If '/' is the last character, it should not be urlencoded. If so search and replace it back to '/'.
								 $reg_exp						= '/^(.+)%2F$/';
								 $getAllTrainSubCats[$j]['url']	= preg_replace($reg_exp, '${1}/', $getAllTrainCats[$j]['url']);
							}						
						if($menuCount == 1) echo '<br />';
						if($getAllTrainSubCats[$j]['category_name'] <> "")
						{
					?><li><a href="<?=ROOT_JWPATH.$getAllTrainSubCats[$j]['url']?>"><?php echo ucfirst(htmlentities($getAllTrainSubCats[$j]['category_name'], ENT_COMPAT, 'UTF-8')); ?></a></li><?php 
						}
					
				$menuCount++;	
				} 
			?>
			</ul>
            </div>
                <!--===============================-->
                
                <?php
				
						//=========================
				}
				
				else
				{ ?>
                
					<a id="topicName" class="itom_s" href="<?=ROOT_JWPATH.$getAllTrainCats[$i]['url']?>"><?php echo ucfirst(htmlentities($getAllTrainCats[$i]['category_name'], ENT_COMPAT, 'UTF-8')); ?></a>
                    
			<?php	}
			
				//===============
				
				$colIndex++;
				}
			?></div>
            <?php
			}	
	//}



    
