<?php
/************************************************************ 
   Project Name	::> Jiwok 
   Module 		::> Class for  logUserEvents 
   Programmer	::>  soumya

*************************************************************/
include_once("class.parseCommon.php");
class logUserEvent
{
	public function __construct()
	{
		//Initializing the dom
		$this	->	doc 	= 	new DOMDocument('1.0', 'utf-8');
		$doc	->	formatOutput = true;
		$this	->	xmlMainNode	=	$this	->	doc ->createElement( "RESPONSE" );
		$this	->	doc		->	appendChild( $this	->	xmlMainNode );	
		$this -> com     =   new parseCommon();
		

	}
	function _logUserEventsValidate($requestElemets)
	{
		
		 global $res;// this value will change to 0 if any error occur
		 if(!array_key_exists('userid',$requestElemets)){ //userid validation 
		 
				      $res=0;
					  $outPut['STATUSCODE']	=	400;
			          $outPut['MESSAGE']    =	"param is missing for userid";
					  return $outPut;
				   }
				   
       	      $userid      = $this ->com->valueChecking($requestElemets["userid"]);			 
       	      
			  if($userid==""){
			  $res = 0;
			  $outPut['STATUSCODE']	=	400;
			  $outPut['MESSAGE']    =	"No values found for userid";
		      return $outPut;
			  }else{
				  
			  if($this ->com->_validate_number($userid)==0){
			  $res=0;
			  $outPut['STATUSCODE']	=	400;
			  $outPut['MESSAGE']    =	"Expecting  numeric values for  userid";
			  return $outPut;
		      }else{ 
				  //checking userid with user_master table
				  if($this ->com->userExist($userid)==0){
					  $res=0;
					  $outPut['STATUSCODE']	=	400;
					  $outPut['MESSAGE']    =	"userid is not exist";
					  return $outPut;
				  }
			  }
			 }
			  if(!array_key_exists('workoutUUID',$requestElemets)){//validation workoutUUID
				      $res=0;
					  $outPut['STATUSCODE']	=	400;
			          $outPut['MESSAGE']    =	"param is missing for workoutUUID";
					  return $outPut;
				   }
       	      $workoutUUID      = $this ->com->valueChecking($requestElemets["workoutUUID"]);			 
       	      if($workoutUUID==""){
			  $res = 0;
			  $outPut['STATUSCODE']	=	400;
			  $outPut['MESSAGE']    =	"No values found for workoutUUID";
		      return $outPut;
			  }
			  if(!array_key_exists('logType',$requestElemets)){//validation logType
				      $res=0;
					  $outPut['STATUSCODE']	=	400;
			          $outPut['MESSAGE']    =	"param is missing for logType";
					  return $outPut;
				   }
       	      $logType      = $this ->com->valueChecking($requestElemets["logType"]);			 
       	      if($logType==""){
			  $res = 0;
			  $outPut['STATUSCODE']	=	400;
			  $outPut['MESSAGE']    =	"No values found for logType";
		      return $outPut;
			  }
			  if(!array_key_exists('log',$requestElemets)){//validation log
				      $res=0;
					  $outPut['STATUSCODE']	=	400;
			          $outPut['MESSAGE']    =	"param is missing for log";
					  return $outPut;
				   }
       	      $log      = $this ->com->valueChecking($requestElemets["log"]);			 
       	      if($log==""){
			  $res = 0;
			  $outPut['STATUSCODE']	=	400;
			  $outPut['MESSAGE']    =	"No values found for log";
		      return $outPut;
			  }
			  if(!array_key_exists('type',$requestElemets)){//validation log
				      $res=0;
					  $outPut['STATUSCODE']	=	400;
			          $outPut['MESSAGE']    =	"param is missing for type";
					  return $outPut;
				   }
       	      $type      = $this ->com->valueChecking($requestElemets["type"]);			 
       	      if($type==""){
			  $res = 0;
			  $outPut['STATUSCODE']	=	400;
			  $outPut['MESSAGE']    =	"No values found for type";
		      return $outPut;
			  }
			  return  $res;
	}
	//function to add to db
	function _logUserEventsAdd($requestElemets)
	{
		$value					=	array();
		$value['user_id']     	= $this ->com->valueChecking($requestElemets["userid"]);	
		$value['workoutUUID']   = $this ->com->valueChecking($requestElemets["workoutUUID"]);	
		$value['logType']      	= $this ->com->valueChecking($requestElemets["logType"]);
		$value['type_category'] = $this ->com->valueChecking($requestElemets["type"]);
		//$log      		= $this ->com->valueChecking($requestElemets["log"]);
		$log      			= trim(urldecode($requestElemets["log"]));// avoiding escape quots with \ as per benoit's instruction
		//$value['log']      		= base64_encode(serialize($log));	
		$value['log']      		=  $log;
		
		$t                      = microtime(true);
	   $value['date_log']   	=  gmdate('Y-m-d\TH:i:s',$t);
		
		
			
		$tble_ins    	= "";	
		$tble_ins			  =  $this -> com->insertRecord_logdb("app_userlogevent",$value);
		if($tble_ins){
			  $outPut['STATUSCODE']	=	200;
			  $outPut['MESSAGE']		=	"success";
		      return $outPut;
			   }else{
					  $res = 0;
					  $outPut['STATUSCODE']	=	400;
					  $outPut['MESSAGE']    =	"Data is not inserted ,please try again";
					  return $outPut;
			       }
		 
		
	}
	
}
?>