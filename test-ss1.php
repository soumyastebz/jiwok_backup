<?php

error_reporting(0);
include_once("includes/classes/class.xmlparse.php");
$queueid      		=   $_REQUEST["queueid"];
$server         	=   $_REQUEST["server"];
$method				=	$_REQUEST["method"];

function getCurlResponse($url){
		$curlHandle = curl_init($url);
		/*curl_setopt($curlHandle, CURLOPT_POST, 1);
		curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $postvar);
		curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1); */
		curl_setopt($curlHandle, CURLOPT_HEADER, 0);  // DO NOT RETURN HTTP HEADERS 
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);  // RETURN THE CONTENTS OF THE CALL
		$returnFromCurl = curl_exec($curlHandle) or die("curl error");	
	//	echo "$payboxData";print_r($payboxData);
		curl_close($curlHandle); 
		return $returnFromCurl;
}

if($server == 1)
{
	$dir    =   'http://95.142.162.55/mp3dir/';
}
else 
{
	$dir    =   'http://92.243.2.0/mp3dir/';
}


$path				=		'https://www.jiwok.com/webservices/GetWorkoutInQueueGandi.php?queue_id='.$queueid;

//$xmlObj				=		new sxmlParse(file_get_contents($path));
$xmlObj				=	new sxmlParse(getCurlResponse($path));

$pgm_lang_arr		=		$xmlObj->crawlXML("root","workout_lang_selected");
print_r($xmlObj );
print_r($pgm_lang_arr );exit;
$pgm_lang 			= 		$pgm_lang_arr['value'];

if($pgm_lang	==	1)
{
	$pgm_title_arr	=	$xmlObj->crawlXML("root-training_program-title","english");
	$beg			=	'Workout';
}	
else if($pgm_lang	==	2)
{
	$pgm_title_arr	=	$xmlObj->crawlXML("root-training_program-title","french");
	$beg			=	'Seance';
}
else if($pgm_lang	==	3)
{
	$pgm_title_arr	=	$xmlObj->crawlXML("root-training_program-title","spanish");
	$beg			=	'Sesin';
}	
else if($pgm_lang	==	4)
{
	$pgm_title_arr	=	$xmlObj->crawlXML("root-training_program-title","italian");
	$beg			=	'Seduta';
}	
else if($pgm_lang	==	5)
{
	$pgm_title_arr	=	$xmlObj->crawlXML("root-training_program-title","polish");
	$beg			=	'Trening';
}	
else
{
	$pgm_title_arr	=	$xmlObj->crawlXML("root-training_program-title","french");
	$beg			=	'Seance';
}

$pgm_title 		=	trim($pgm_title_arr['value']);


$program_id_arr	=	$xmlObj->crawlXML("root","training_program");

$program_id  			= 		trim($program_id_arr['attr']['id']);

$workout_id_arr 		=  	$xmlObj->crawlXML("root","workout");

$workout_id 			= 		trim($workout_id_arr['attr']['id']);

$workoutnum_arr   		=   	$xmlObj->crawlXML("root","order");

$workoutnum				=		trim($workoutnum_arr['value']);

$newname        		=   	$beg.$workoutnum.'_'.$pgm_title.'.mp3';

$newname        		=   	str_replace(' ','_',$newname); 

$newname        		=   	str_replace(',','_',$newname); 

$name           		=   	$queueid.'_'.$program_id.'.mp3';

if($newname && $name)
{
	if($method	== "http")
	{

		$ok 			=   downloadFile($dir.$name,$newname);
	}
	elseif($method	== "https")
	{

		$ok 			=   downloadFile($dir.$name,$newname);
			}
	
	else if($method	==  "ftp")
	{
		if($server == 1)	
		{
			$ser    =  '95.142.162.55';
		}
		else if($server == 2)
		{
			$ser   =  '92.243.2.0';
		}
		header('Location:ftp://ftp:jiwok@'.$ser.'/'.$name );
		//$ok	=	'success';
		exit;
	}
	else
	{	
		$ok 			=   downloadFile($dir.$name,$newname);
	}
					
	if($ok)

	{
		$valxm	= file_get_contents('http://www.jiwok.com/webservices/UpdateMP3GenerationStatus.php?queue_id='.$queueid.'&status=11');

	}
}

function downloadFile($filename,$newname)
{
	echo $filename.'\n'.$newname;exit;
	$filename = str_replace(' ','%20',$filename);

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
	header("X-Sendfile: ".basename($filename));
	header("Content-Type: application/octet-stream");
	//header("Content-Type: application/force-download");
	header( "Content-Disposition: attachment; filename=".basename($newname));
	header( "Content-Description: File Transfer");
	@readfile($filename); 
	return 'success';

}





?>