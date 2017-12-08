<?php
/************************************************************ 
   Project Name	::> Jiwok 
   Module 		::> Class for Real time module REST implementation
   Programmer	::> Dileep.E
   Date			::> 14-07-2012
   DESCRIPTION::::>>>>
   This class used for real time module operation for REST implementation.
*************************************************************/
class RestRequest  
{  
	public $request_vars;  
    public $data;  
    public $http_accept;  
    public $method;  
      
    public function __construct()  
    {  
    	$this->request_vars      = array();  
        $this->data              = '';  
        $this->http_accept       = (strpos($_SERVER['HTTP_ACCEPT'], 'json')) ? 'json' : 'xml';  
        $this->method            = 'get';  
	}  
      
    public function setData($data)  
    {  
    	$this->data = $data;  
	}  
      
    public function setMethod($method)  
    {  
    	$this->method = $method;  
   	}  
      
   	public function setRequestVars($request_vars)  
    {  
    	$this->request_vars = $request_vars;  
   	}  
      
    public function getData()  
    {  
    	return $this->data;  
   	}  
      
    public function getMethod()  
    {  
    	return $this->method;  
   	}  
      
    public function getHttpAccept()  
    {  
    	return $this->http_accept;  
  	}  
      
    public function getRequestVars()  
    {  
    	return $this->request_vars;  
   	}  
}
