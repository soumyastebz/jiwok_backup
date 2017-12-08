<?php
/************************************************************ 
    Project Name	::> Jiwok 
    Module 		::> File for Real time module REST implementation
    Programmer	::> Soumya & Georgina
    Date			::> 11-07-2016
    DESCRIPTION::::>>>>
    This file used for creating webservices for jiwok app to benoit 
*************************************************************/
    error_reporting(E_ALL);
    include_once('config.php');
	require_once('classes/class.restUtils.php');
	require_once('classes/class.parseMain.php');
	$utilityObj	=	new RestUtils(); 
	$realObj	=	new parseMain();
	  
    //Collecting the page name
	$fullUrl	=	$_SERVER["REQUEST_URI"];
	if($_SERVER['HTTP_HOST']	==	'10.0.0.8')
	{
		$fullUrl	=	str_replace('/jiwokv3/appApi', "", $fullUrl);
	}else{
		$fullUrl	=	str_replace('appApi/', "", $fullUrl);
	}
		$fullUrl	=	substr($fullUrl,1,strlen($fullUrl));
	    $pageAction	=	explode('?',$fullUrl);
	    $pageActionSet=$pageAction[0];	
	    $data 		= 	$utilityObj	->	processRequest();
   switch($pageActionSet)  
   {            case 'userextra':
			    switch($data->getMethod())
				    {   case 'get':	
						if($data->getHttpAccept() == 'json')  
						{  
							$productList	=	array();
							$utilityObj	->	sendResponse(200, json_encode($productList), 'application/json');  
						}  
						else if($data->getHttpAccept() == 'xml')  
						{        echo "<pre>";
							     print_r($data);
							     die();
							     $outPutxml["result"]	=	$realObj	->_userextraAdd($data->request_vars);
								 $utilityObj	->	sendResponse(200,json_encode($outPutxml),'application/json');  	
						}    
						break;  
						default:    
								//just send the new ID as the body
								$outPut['STATUSCODE']	=	405;
								$outPut['MESSAGE']		=	"Method Not Allowed";
								$utilityObj	->	sendResponse(404, json_encode($outPut), 'application/json');   						
						break;   
			       }
			    case 'increment':
			    switch($data->getMethod())
				    {   case 'get':	
						if($data->getHttpAccept() == 'json')  
						{  
							$productList	=	array();
							$utilityObj	->	sendResponse(200, json_encode($productList), 'application/json');  
						}  
						else if($data->getHttpAccept() == 'xml')  
						{  
							$outPutxml["result"]	=	$realObj	->_increment($data->request_vars);
							$utilityObj	->	sendResponse(200,json_encode($outPutxml),'application/json');  	
						}    
						break;  
						default:    
								//just send the new ID as the body
							$outPut['STATUSCODE']	=	405;
							$outPut['MESSAGE']		=	"Method Not Allowed";
							$utilityObj	->	sendResponse(404, json_encode($outPut), 'application/json');   						
						break;   
			       }       
   }
?>
