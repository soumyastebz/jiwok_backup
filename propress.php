<?php
/************************************************************ 
   Project Name	::> Jiwok 
   Module 		::> page  for Pressmagazine api 
   Programmer	::> Soumya
   
*************************************************************/
/*
ini_set("display_errors",1);
error_reporting(E_ALL);*/
if(isset($_SERVER['HTTP_ORIGIN'])){
	$http_origin = $_SERVER['HTTP_ORIGIN'];
	if (($http_origin == "http://www.kioskpress.fr/") || ($http_origin == "http://jiwok.kioskpress.fr"))
	{  
		header("Access-Control-Allow-Origin:".$http_origin);
	}
}
include_once('includes/classes/class.pressMagazine.php');
 $pressApi		=	new pressMagazine();
if((isset($_REQUEST['token']) == "") || (isset($_REQUEST['magname']) == "")){
	
	$data['message']	=	"Please Provide  input params";
	$res				=	jsonResData($data,400);
	echo $res;exit;
	
}
else
{
	$token				=	trim(urldecode($_REQUEST['token']));
	$magname			=	trim(urldecode($_REQUEST['magname']));
	$validDataToken		=	$pressApi->emptyValidationRequest('token',$token);	
	$validDatamagname	=	$pressApi->emptyValidationRequest('magname',$magname);	
	if(($validDataToken	==1) && ($validDatamagname == 1)){
		$tokenExist		=	$pressApi->checkUSerIdForToken($token);
		if($tokenExist	!=0){ //valid user cond 
			$userId			=	$tokenExist['user_id'];		
			$userData 		=	$pressApi->getUserData($userId,$magname,$token);
			if($userData !=0)
			{
				$data['token']	=	$token;
				$data['email']	=	$userData['email'];
				//$data['status']	=	$userData['status'];
				$res				=	jsonResData($data,$userData['status']);				
				exit;
			}
			else
			{
				//no useremail for this  id on db 
				$data['token']	=	$token;
				$data['email']	=	"";
				//$data['status']	=	"";
				$res				=	jsonResData($data,"");
				exit;
			}

		}
		else{
			$data['token']	=	$token;
			$data['email']	=	"";
			//$data['status']	=	"";
			$res				=	jsonResData($data,"");
			exit;
			//echo 'no such token in db';
		}
		
	}
	else
	{
		$data['message']	=	"Please Provide valid input params";
		$res				=	jsonResData($data,400);
		exit;
	}
}


function jsonResData($data,$status){
		$data['status']		=	$status;	
		header('Content-Type: application/json');
		echo json_encode($data);
}

?>

