<?php /************************************************************ 
   Project Name	::> geonaute 
   Module 		::> Class for Registration purpose
   Programmer	::> Soumya/Georgina
   Date			::> 15-07-2015
   DESCRIPTION::::>>>>
   Class for Registration purpose
*************************************************************/
class parseCommon
{  /*
	Function 			: userExit
	Usage	   			: To check whether userid is existing in user_master table
	Variable Passing 	: userid
	*/
	public function userExist($userid){
		       $result    = 1;
               $statement =$GLOBALS['db_com']->prepare("SELECT count( user_id ) AS count FROM user_master where user_id= :user_id");
	           $statement->execute(array(':user_id' => $userid));
	           $result       = end($statement->fetch());
	           
	           
	           return $result;
    }
    /*
	 Function 			: _validate_number
     Usage	   			: To check whether the value is number or not
     Variable Passing 	:
	*/
	public function _validate_number($number_val){ 
	 $result = 1;
	  if(is_numeric(trim($number_val)) == 0) {
		$result = 0; 
	  }
	  return $result;
	}
	public function valueChecking($value){
	        return mysql_escape_string(trim(urldecode($value)));
	} 
	public function insertRecord($table,$data)
    {
    // try our best not to use reserved words
    $columns = array_map(function($column) {
        return "`$column`";
    }, array_keys($data));
    // just use question marks as the place holders
    $values = array_fill(0, count($data), '?');
    // construct the query    
    $sql = sprintf('INSERT INTO `%s` (%s) VALUES (%s)', $table,join(',', $columns),join(',', $values));
    // prepare and execute it
    $stmt = $GLOBALS['db_app']->prepare($sql);
    $stmt->execute(array_values($data));
    $val  = $stmt->rowCount();
    return $val ? 1 : 0;
   }
   public function updateRecord($table_name,$elmts,$where='')
    {  
		 $values="";
   	 		foreach($elmts as $k=>$v){
				$values.= $k."='".$v."',";
			}
			$values    = substr($values,0,-1);
         if($where  == ""){
				$query = "UPDATE $table_name SET $values";
			}else{			
				$query = "UPDATE $table_name SET $values WHERE $where";
				}
				$stmt  = $GLOBALS['db_app']->exec($query);
			  return $stmt ? 1 : 0;
	   
   }
   public function updateRecord_field($table_name,$elmts,$where='',$str='')
    {     
		    $values="";
		    foreach($elmts as $k=>$v){
				$values.= $k."='".$v."',";
			}
			if($str){
				$values=$values.$str;}
			//$values    = substr($values,0,-1);
		 if($where  == ""){
				$query = "UPDATE $table_name SET $values";
			}else{			
				$query = "UPDATE $table_name SET $values WHERE $where";
			}
			$stmt  =$GLOBALS['db_app']->exec($query);
		    return $stmt ? 1 : 0;
    }
   
    
}
?>
