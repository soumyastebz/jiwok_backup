<?php
$forumPosts	  	= $objPgm->_getForumPosts();
$exception 		= 0;
?>
<ul class="listing_02">
                                
           
  <?php
  for($i=0;$i<count($forumPosts);$i++)
	{
		$forum_id 		= trim(stripslashes($forumPosts[$i]['forum_id']));
 		$topic_id 		= trim(stripslashes($forumPosts[$i]['topic_id'])); 
		if($forumPosts[$i]['post_subject'] != '')
			{
		 		$forum_title	= trim(stripslashes($forumPosts[$i]['post_subject']));
			}
		else 
			{
				$forum_title	= trim(stripslashes($forumPosts[$i]['post_text']));
			}

		$fPattern2 	 = array('/à/','/è/','/À/','/È/','/é/','/É/','/â/','/ê/','/î/','/ô/','/û/','/Â/','/Ê/','/Î/','/Ô/','/Û/','/ä/','/ë/','/ï/','/ö/','/ü/','/Ä/','/Ë/', '/Ï/','/Ö/','/Ü/','/ç/','/Ç/','/ /');

		$fReplace2 	 = array('a','e','A','E','e','E','a','e','i','o','u','A','E','I','O','U','a','e','i','o','u','A','E','I','O','U','c','C','-');
				
		$value1 = preg_replace($fPattern2,$fReplace2,$forum_title);
  ?>
	  <a title="<?=substr($value1,0,100)?>" href="<?php echo JIWOK_URL ?>forum/viewtopic.php?f=<?=$forum_id?>&t=<?=$topic_id?>" class="Blog_link" target="_blank"><li><?=mb_strtoupper(substr($value1,0,80),'UTF-8'); if(strlen($value1) > 20) { ?>&hellip;<? }?></li></a> 
  <?php 
  	} 
   ?>
  </ul>
   <div align="right"><a href="<?php echo JIWOK_URL ?>forum/" class="orange"><input type="button" value="<?=mb_strtoupper($parObj->_getLabenames($arrayData,'postforums','name'),'UTF-8')?>" class="btn-read ease"></a></div>
