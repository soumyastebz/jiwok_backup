<?php
session_start();
include_once('includeconfig.php');
include_once("includes/classes/class.programs.php");
if($lanId=="")
     $lanId=1;
$errorMsg = '';	 
$userid		= $_SESSION['user']['userId'];	
$objGen     	= new General();
$objPgm     	= new Programs($lanId);
$parObj 		= new Contents();
$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
$arrayData		= $returnData['general'];
$flexid = trim($_REQUEST['flexid']);
$workFlex = trim($_REQUEST['workFlex']);
$display="";
$genres = $objPgm->_getUserGenres($userid);

 if(count($genres)>0) { 
 $totalFile =0;
$display.="<div align=\"center\" id=\"genreError\" style=\"color:#CC0000; display:none\">".$parObj->_getLabenames($arrayData,'choosegenreError','name')."</div><form id=\"generate\" name=\"generate\" method=\"post\"><h1 align=\"center\" ><strong>".$parObj->_getLabenames($arrayData,'choosegenre','name')."</strong></h1><ul id=\"showGenres\" style=\"display:block;\">";
for($i=0;$i<count($genres);$i++)
{
	$genre = trim(stripslashes($genres[$i]['genre_name']));
	$status_genre = trim(stripslashes($genres[$i]['remember_status']));
	$genre_id = trim(stripslashes($genres[$i]['id']));
	$file_count = trim(stripslashes($genres[$i]['file_count']));
			if($file_count=='')
			  $file_count = 0;
	$totalFile += $file_count;		  
$display.="<li><span><input name=\"genre\" type=\"checkbox\" value=\"".$genre_id."_".$file_count."\" ";
if($status_genre=='1'){ 
$display.="checked=\"checked\"";
}
$display.="onclick=\"uncheckRandom(this);\"/></span><label>&nbsp;".$genre."(".$file_count." ".$parObj->_getLabenames($arrayData,'songdetect','name').")</label></li>";
} 
/* $display.="</ul><p>&nbsp;</p><ul><li><span><input type=\"checkbox\" name=\"random1\" value=\"1\"  onclick=\"uncheckAllGenre(this);\" id=\"random1\"/></span><label>&nbsp;<strong>".$parObj->_getLabenames($arrayData,'randomgenre','name')."</strong></label></li><li><span><input name=\"remchoice\"  id=\"remchoice\" type=\"checkbox\"/></span><label>&nbsp;<strong>".$parObj->_getLabenames($arrayData,'remchoice','name')."</strong></label></li></ul><ul><li>&nbsp;</li></ul><h2><input class=\"ProduiBtn3\" name=\"GenerateMP3\" type=\"button\" value=\"".$parObj->_getLabenames($arrayData,'generateMP3','name')."\""; */
 $display.="</ul><p>&nbsp;</p><ul><li><span><input type=\"checkbox\" name=\"vocal_type\" value=\"1\"  id=\"vocal_type\"/></span><label>&nbsp;<strong>".$parObj->_getLabenames($arrayData,'vocalType','name')."</strong></label></li><li><span><input type=\"checkbox\" name=\"random1\" value=\"1\"  onclick=\"uncheckAllGenre(this);\" id=\"random1\"/></span><label>&nbsp;<strong>".$parObj->_getLabenames($arrayData,'randomgenre','name')."</strong></label></li><li><span><input name=\"remchoice\"  id=\"remchoice\" type=\"checkbox\"/></span><label>&nbsp;<strong>".$parObj->_getLabenames($arrayData,'remchoice','name')."</strong></label></li></ul><ul><li>&nbsp;</li></ul><div><input class=\"orange-botton margin-adjust\"  name=\"GenerateMP3\" type=\"button\" value=\"".$parObj->_getLabenames($arrayData,'generateMP3','name')."\"";
 if(count($genres)==1){
 $display.=" onclick=\"confirmSongs('$flexid','one','$totalFile');\"/>";
 }
 else{
  $display.=" onclick=\"confirmSongs('$flexid','','$totalFile');\"/>";
  }
  $display.="</div></form>";
 } else {
$display.="<h1 align=\"center\" ><strong>".$parObj->_getLabenames($arrayData,'genredetectmsg','name')."</strong></h1>";
}



echo $display;
?>