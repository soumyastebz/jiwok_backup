<?php
include_once('includeconfig.php');
include_once("includes/classes/class.Languages.php");
include_once("includes/classes/class.download.php");
session_start();
ob_start();

$parObj 		= new Contents('program_generate2.php');

$headingData	= $parObj->_getTagcontents($xmlPath,'trainingprogram','pageHeading');

$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');

$arrayData		= $returnData['general'];




if($lanId=="")
     $lanId=1;
$queueid        =   $_REQUEST["queueid"];
$server         =   $_REQUEST["server"];
if($server == 1)
    $dir    =   SERVER1;
/*else if($server == 2)
    $dir    =   $server2;*/
$objPgm     	=	 new Programs($lanId);
$progra_qu      =   $objPgm->getQueueDetails($queueid,$lanId);
if($progra_qu && $server)
{
    $program_id     =   $progra_qu["program_flex_id"];
    $workout_id     =   $progra_qu["workout_flex_id"];
    $pgm_title      =   $progra_qu["program_title"];
    $workoutnum     =   $progra_qu["workoutOrderNumber"];
    $newname        =   'Seance'.$workoutnum.'_'.$pgm_title.'.mp3';
    $newname        =   str_replace(' ','_',$newname);
    $name           =   $queueid.'_'.$program_id.'.mp3';
}
else
{
 	// header("location:failure.php");

}

if($newname && $name)
	{

		$ok =   downloadFile($dir.$name,$newname);
        if($ok)
        {
            $objPgm->updateStatus($queueid);
			echo "hi";
        }
        
	}
function downloadFile($filename,$newname)
{
  // echo $filename;exit;
    ob_clean();
   /* $fnamerev	=	strrev($filename);
    $expfname	=	explode(".",$fnamerev);///for extension
    $expfname1	=	$expfname[0];
    $filetype	=	strrev($expfname1);
    $ctype 		=	$filetype;
    header("Pragma: public");
    header("Expires: 0");
	header("Content-type: application/force-download");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
    header("Content-Type: $ctype");
    header("Content-Disposition: attachment; filename=\"".$filename."\";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".filesize($filename));
    readfile("$filename");*/

//$file = "http://95.142.162.55/mp3dir/42964_POST_NAT_D_VELO_2X_8S.mp3";

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

header("Content-Type: application/force-download");
header( "Content-Disposition: attachment; filename=".basename($newname));
header( "Content-Description: File Transfer");
@readfile($filename); 
return 'success';


}
?>
