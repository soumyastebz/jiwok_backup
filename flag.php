<?php
session_start();
/*--------------------------------------------------*/
// Project 		: Jiwok
// Created on	: 23-06-2011
// Created by	: Ganga
// Purpose		: Flag display in header 
/*--------------------------------------------------*/

include_once('includeconfig.php');
include_once('includes/classes/class.Languages.php');
$objLanguage			=	new Language();
$getAllLangs			=	$objLanguage->_getAllLanguages();
if($_REQUEST['categoryName'])
{	
	$rq		=	"categoryName";
	$reqnm	=	$_REQUEST['categoryName'];
}
else if($_REQUEST['program_title_url'])
{
	$rq		=	"program_title_url";
	$reqnm	=	$_REQUEST['program_title_url'];
}

    if($getAllLangs)
	foreach($getAllLangs as $key => $val)
	{   //Temp adjustment that to point jiwok eng to old version
		if($val['language_id'] == 1)
		{ ?>
            <li><a href="http://en.jiwok.com/en/index_eng.php"><img src="<?=ROOT_FOLDER?>images/jiwok_03.png" alt="sports coach and training" title="View site in English"/><h5>ENGLISH</h5></a></li>
              
       <?php }
		else if($val['language_id'] == 5)
		  {
			?>
            <li><a href="<?=ROOT_JWPATH?>setLang.php?langId=<?=$val['language_id']?>&path=<?=base64_encode($path);?>&req_name=<?=$reqnm?>&rq=<?=$rq?>"><img src="<?=ROOT_FOLDER?>images/jiwok_09.png" title="traduire ces éléments en polonais" alt="traduire ces éléments en polonais" /><h5>POLISH</h5></a></li>	
<?php

		}
		
		else 
		{
?>
 <li><a href="<?=ROOT_JWPATH?>setLang.php?langId=<?=$val['language_id']?>&path=<?=base64_encode($path);?>&req_name=<?=$reqnm?>&rq=<?=$rq?>"><img src="<?=ROOT_FOLDER?>images/jiwok_06.png" title="Voir le site en français" alt="coach sportif et entrainement"/><h5>FRENCH</h5></a></li>
		
<?php

		}//temp else ends
	}
?>
