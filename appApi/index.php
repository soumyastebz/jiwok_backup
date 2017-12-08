<?php
/************************************************************ 
    Project Name	::> Jiwok 
    Module 		::> File for webservice for jiwok app parse,log  REST implementation
    Programmer	::> Soumya & Georgina
    Date			::> 11-07-2016
    DESCRIPTION::::>>>>
    This file used for creating webservices for jiwok app to benoit 
*************************************************************/
/*   error_reporting(E_ALL);
ini_set("display_errors",1);*/
    include_once('config.php');
	require_once('classes/class.restUtils.php');
	require_once('classes/class.parseMain.php');
	require_once('classes/class.logUserEvents.php');
	$utilityObj	=	new RestUtils(); 
	$realObj	=	new parseMain();
	$logObj		=	new logUserEvent();	
	  
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
						{        
							    if(!empty($pageAction[1])){
							     $valuesGet              =  $realObj	->valuesGet($pageAction[1]);
							    
							     $outPutxml	 =  $realObj	->_userextraAdd($valuesGet);
							     if($outPutxml['STATUSCODE']==400){
									 $utilityObj	->	sendResponse(400,json_encode($outPutxml),'application/json'); 
								 }else{
							     $utilityObj	->	sendResponse(200,json_encode($outPutxml),'application/json'); 
							     } 
							    }else{
								$outPut['STATUSCODE']	=	400;
								$outPut['MESSAGE']		=	"Params not found";
								$utilityObj	->	sendResponse(400, json_encode($outPut), 'application/json');  
								}
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
							   if(!empty($pageAction[1])){
								 $valuesGet              =  $realObj	->valuesGet($pageAction[1]);
							     $outPutxml	 =  $realObj	->_increment($valuesGet);
							     if($outPutxml['STATUSCODE']==400){
									 $utilityObj	->	sendResponse(400,json_encode($outPutxml),'application/json'); 
								 }else{
							         $utilityObj	->	sendResponse(200,json_encode($outPutxml),'application/json'); 
							     } 
							    }else{
								$outPut['STATUSCODE']	=	400;
								$outPut['MESSAGE']		=	"Params not found";
								$utilityObj	->	sendResponse(400, json_encode($outPut), 'application/json');
							   }
							  	
						}    
						break;  
						default:    
								//just send the new ID as the body
							$outPut['STATUSCODE']	=	405;
							$outPut['MESSAGE']		=	"Method Not Allowed";
							$utilityObj	->	sendResponse(404, json_encode($outPut), 'application/json');   						
						break;   
			       }   
				   //for log user events
				    case 'logUserEvent':
					
			    switch($data->getMethod())
				    {
						
					 case 'post':	
					
						if($data->getHttpAccept() == 'json')  
						{
							$productList	=	array();
							$utilityObj	->	sendResponse(200, json_encode($productList), 'application/json');  
						}  
						else if($data->getHttpAccept() == 'xml')  
						{        
								
							    if(!empty($data ->request_vars)){
								
							     $valuesGet              =   $data ->request_vars;
							   
							     $validateData	 =  $logObj->_logUserEventsValidate($valuesGet);
								 
								 if( $validateData == 1){
									 $outPutxml	=	$logObj->_logUserEventsAdd($valuesGet);
								 }
								 else{
									  $outPutxml	=	  $validateData;
								 }
								
							     if($outPutxml['STATUSCODE']==400){
									 $utilityObj	->	sendResponse(400,json_encode($outPutxml),'application/json'); 
								 }else{
							     $utilityObj	->	sendResponse(200,json_encode($outPutxml),'application/json'); 
							     } 
							    }else{
								$outPut['STATUSCODE']	=	400;
								$outPut['MESSAGE']		=	"Params not found";
								$utilityObj	->	sendResponse(400, json_encode($outPut), 'application/json');  
								}
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
