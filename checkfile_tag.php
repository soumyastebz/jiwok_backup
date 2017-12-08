<?php

include_once('includeconfig.php');

include_once("includes/classes/class.Languages.php");

include_once("includes/classes/class.programs.php");

session_start();

ob_start();

$userid					=	$_REQUEST["user"];

$pgm_flexid			=	$_REQUEST["pgmFlex"];

$workout_flex_id		=	$_REQUEST["workFlex"];

$wFlex  = str_replace('%20',' ',trim($workout_flex_id));

$wFlex  = str_replace('%2B','+',$wFlex);



$objPgm     			=	 new Programs($lanId);

if($objPgm->_checkWorkoutExistsInQueue($userid,$pgm_flexid,$wFlex,2))

{

		$result = 'success';

}

echo $result;

?>