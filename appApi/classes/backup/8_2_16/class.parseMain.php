<?php
/************************************************************ 
   Project Name	::> Jiwok 
   Module 		::> Class for Real time module
   Programmer	::> Dileep.E
   Date			::> 09-07-2012
   DESCRIPTION::::>>>>
   This class used for real time module operation.
*************************************************************/
$res = 1;
include_once("class.parseCommon.php");
class parseMain
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
    function _userextraAdd($requestElemets){
		      global $res;// this value will change to 0 if any error occur
	           if(!array_key_exists('userid',$requestElemets)){
				      $res=0;
					  $outPut['STATUSCODE']	=	400;
			          $outPut['MESSAGE']    =	"param not found for userid";
					  return $outPut;
				   }
       	      $userid      = $this ->com->valueChecking($requestElemets["userid"]);
       	      $tble_ins    = "";
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
		   if($res !=0)
			{   
				$t                            = microtime(true);
				if(isset($requestElemets["userid"])){
				$value["user_id"] = $this ->com->valueChecking($requestElemets["userid"]);}
				
				if(isset($requestElemets["language"])){
				$value["user_language"] = $this ->com->valueChecking($requestElemets["language"]);}
				
				if(isset($requestElemets["programId"])){
				$value["programId"] = $this ->com->valueChecking($requestElemets["programId"]);}
				
				//yy-mm-dd H:i:s
				if(isset($requestElemets["firstTryDate"])){
				$value["firstTryDate"] = $this ->com->valueChecking($requestElemets["firstTryDate"]);}
				
				if(isset($requestElemets["numberOfTrials"])){
				$value["numberOfTrials"] = $this ->com->valueChecking($requestElemets["numberOfTrials"]);}
				
				if(isset($requestElemets["lastSessionDate"])){
				$value["lastSessionDate"] = $this ->com->valueChecking($requestElemets["lastSessionDate"]);}
				
				if(isset($requestElemets["lastSessionIndex"])){
				$value["lastSessionIndex"] = $this ->com->valueChecking($requestElemets["lastSessionIndex"]);}
				
				if(isset($requestElemets["themesTouched"])){
				$value["themesTouched"] = $this ->com->valueChecking($requestElemets["themesTouched"]);}
				
				if(isset($requestElemets["lastSessionIndex"])){
				$value["lastSessionIndex"] = $this ->com->valueChecking($requestElemets["lastSessionIndex"]);}
				
				if(isset($requestElemets["goalsTouched"])){
				$value["goalsTouched"] = $this ->com->valueChecking($requestElemets["goalsTouched"]);}
				
				if(isset($requestElemets["userStatus"])){
				$value["userStatus"] = $this ->com->valueChecking($requestElemets["userStatus"]);}
				
				if(isset($requestElemets["statusChangeDate"])){
				$value["statusChangeDate"] = $this ->com->valueChecking($requestElemets["statusChangeDate"]);}
				
				if(isset($requestElemets["isRunningHero"])){
				$value["isRunningHero"] = $this ->com->valueChecking($requestElemets["isRunningHero"]);}
				
				if(isset($requestElemets["download3GActive"])){
				$value["download3GActive"] = $this ->com->valueChecking($requestElemets["download3GActive"]);}
				
				if(isset($requestElemets["mapActive"])){
				$value["mapActive"] = $this ->com->valueChecking($requestElemets["mapActive"]);}
				
				if(isset($requestElemets["coverDownloadActive"])){
				$value["coverDownloadActive"] = $this ->com->valueChecking($requestElemets["coverDownloadActive"]);}
				
				if(isset($requestElemets["coachExtraActive"])){
				$value["coachExtraActive"] = $this ->com->valueChecking($requestElemets["coachExtraActive"]);}
				
				if(isset($requestElemets["jiwokMusicActive"])){
				$value["jiwokMusicActive"] = $this ->com->valueChecking($requestElemets["jiwokMusicActive"]);}
				
				if(isset($requestElemets["iTunesMusicActive"])){
				$value["iTunesMusicActive"] = $this ->com->valueChecking($requestElemets["iTunesMusicActive"]);}
				
				if(isset($requestElemets["spotifyMusicActive"])){
				$value["spotifyMusicActive"] = $this ->com->valueChecking($requestElemets["spotifyMusicActive"]);}
				
				if(isset($requestElemets["deezerMusicActive"])){
				$value["deezerMusicActive"] = $this ->com->valueChecking($requestElemets["deezerMusicActive"]);}
				
				if(isset($requestElemets["funk_disco_soul"])){
				$value["funk_disco_soul"] = $this ->com->valueChecking($requestElemets["funk_disco_soul"]);}
				
				if(isset($requestElemets["house_electro"])){
				$value["house_electro"] = $this ->com->valueChecking($requestElemets["house_electro"]);}
				
				if(isset($requestElemets["pop_rock"])){
				$value["pop_rock"] = $this ->com->valueChecking($requestElemets["pop_rock"]);}
				
				if(isset($requestElemets["Rap"])){
				$value["Rap"] = $this ->com->valueChecking($requestElemets["Rap"]);}
				
				if(isset($requestElemets["rnb"])){
				$value["rnb"] = $this ->com->valueChecking($requestElemets["rnb"]);}
				
				if(isset($requestElemets["techno_rave"])){
				$value["techno_rave"] = $this ->com->valueChecking($requestElemets["techno_rave"]);}
				
				if(isset($requestElemets["world_music"])){
				$value["world_music"] = $this ->com->valueChecking($requestElemets["world_music"]);}
				
				//==================================================================value checking
				$statement = $GLOBALS['db_app']->prepare("SELECT count(userextra_id) as tot FROM `parse_userextra` where user_id= :user_id");
	            $statement->execute(array(':user_id' => $userid));
	            $result    = end($statement->fetch());
				if($result>0){
					//update
						$value['updatedAt']   =  gmdate('Y-m-d\TH:i:s',$t).'Z';
					    $tble_ins=$this -> com->updateRecord("parse_userextra",$value,"user_id = ".$value['user_id']."");
				}else{
					//insert
					    $value['createdAt']   =  gmdate('Y-m-d\TH:i:s',$t).'Z';
					    $tble_ins			  =  $this -> com->insertRecord("parse_userextra",$value);
				}
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
			exit;
		}
    function _increment($requestElemets){ 
	          global $res;// this value will change to 0 if any error occur
	          if(!array_key_exists('tableName',$requestElemets)){
				      $res=0;
				      $outPut['STATUSCODE']	=	400;
			          $outPut['MESSAGE']    =	"param not found for tableName";
					  return $outPut;
				   }
				   if(!array_key_exists('recordId',$requestElemets)){
				      $res=0;
				      $outPut['STATUSCODE']	=	400;
			          $outPut['MESSAGE']    =	"param not found for recordId";
					  return $outPut;
					  }
	          $tableName   = $this ->com->valueChecking($requestElemets["tableName"]);
	          $recordId    = $this ->com->valueChecking($requestElemets["recordId"]);
	          $tble_ins    = "";
	          if($tableName==""){
			  $res = 0;
			    $outPut['STATUSCODE']	=	400;
			    $outPut['MESSAGE']    =	"No values found for tableName";
			    return $outPut;
			 }
			  if($recordId==""){
			  $res = 0;
			    $outPut['STATUSCODE']	=	400;
			    $outPut['MESSAGE']    =	"No values found for recordId";
			    return $outPut;
			  }
			 if($res !=0)
			 {  $t                            = microtime(true); 
			    switch($tableName){
				case "packsdownloads":
				$downloads   = $this ->com->valueChecking($requestElemets["downloads"]);
				$statement = $GLOBALS['db_app']->prepare("SELECT count(packId) as tot FROM `parse_packsdownloads` where packId= :packId");
	            $statement->execute(array(':packId' => $recordId));
	            $result    = end($statement->fetch());
					if($result>0){
						   if(!in_array($downloads, array('1','-1'), true ))
							  { 
								  $res = 0;
								  $outPut['STATUSCODE']	=	400;
								  $outPut['MESSAGE']    =	"Expecting  digit 1 or -1 values for downloads";
			                      return $outPut;
							  }
						
						   if($downloads==1){
							$str="downloads=downloads+1";
						    }else if($downloads== -1){
							$str="downloads=downloads-1";
						    }
						    $value['updatedAt']   =   gmdate('Y-m-d\TH:i:s',$t).'Z';
							$tble_ins             =   $this -> com->updateRecord_field("parse_packsdownloads",$value,"packId = '".$recordId."'",$str);
					}else{
							$value['createdAt']   =  gmdate('Y-m-d\TH:i:s',$t).'Z';
							$value['packId']      = $recordId;
							if($downloads){
							$value['downloads']   = 1;
						     }
							$tble_ins			  =  $this -> com->insertRecord("parse_packsdownloads",$value);
					}
				  break;
				  
				  case "programextra":
				     $str1="";
				     if(array_key_exists('usesAsTrial',$requestElemets)){
						       
							   $value['usesAsTrial']        =$this ->com->valueChecking($requestElemets["usesAsTrial"]);
							   if($value['usesAsTrial']==1){
								   $str1 .="usesAsTrial=usesAsTrial+1,"; 
							   }else if($value['usesAsTrial']==-1){
								   $str1.="usesAsTrial=usesAsTrial-1,";
							   }
							}
						    if(array_key_exists('presentations',$requestElemets)){
							   $value['presentations']      =$this ->com->valueChecking($requestElemets["presentations"]);
							   if($value['presentations']==1){
								   $str1 .="presentations=presentations+1,"; 
							   }else if($value['presentations']==-1){
								   $str1.="presentations=presentations-1,";
							   }
							}
						    if(array_key_exists('usesAsSubscription',$requestElemets)){
							   $value['usesAsSubscription'] =$this ->com->valueChecking($requestElemets["usesAsSubscription"]);
							   if($value['usesAsSubscription']==1){
								   $str1 .="usesAsSubscription=usesAsSubscription+1,"; 
							   }else if($value['usesAsSubscription']==-1){
								   $str1.="usesAsSubscription=usesAsSubscription-1,";
							   }
							}
							$str1    = substr($str1,0,-1);
				  $statement = $GLOBALS['db_app']->prepare("SELECT count(programId) as tot FROM `parse_programextra` where programId= :programId");
	              $statement->execute(array(':programId' => $recordId));
	              $result    = end($statement->fetch());
				  if($result>0){
					           $value1['updatedAt']         =   gmdate('Y-m-d\TH:i:s',$t).'Z';
					           $tble_ins                    =   $this -> com->updateRecord_field("parse_programextra",$value1,"programId = '".$recordId."'",$str1);
					}else{
						       $value['createdAt']          =   gmdate('Y-m-d\TH:i:s',$t).'Z';
						       $value['programId']          =   $recordId;
						       $tble_ins			        =   $this -> com->insertRecord("parse_programextra",$value);
					}
				   break;
				  case "userextra":
			      $str1="";
				     if(array_key_exists('numberOfTrials',$requestElemets)){
							   $value['numberOfTrials']        =$this ->com->valueChecking($requestElemets["numberOfTrials"]);
							   if($value['numberOfTrials']==1){
								   $str1 .="numberOfTrials=numberOfTrials+1,"; 
							   }else if($value['numberOfTrials']==-1){
								   $str1.="numberOfTrials=numberOfTrials-1,";
							   }
							}
						    if(array_key_exists('themesTouched',$requestElemets)){
							   $value['themesTouched']      =$this ->com->valueChecking($requestElemets["themesTouched"]);
							   if($value['themesTouched']==1){
								   $str1 .="themesTouched=themesTouched+1,"; 
							   }else if($value['themesTouched']==-1){
								   $str1.="themesTouched=themesTouched-1,";
							   }
							}
						    if(array_key_exists('goalsTouched',$requestElemets)){
							   $value['goalsTouched'] =$this ->com->valueChecking($requestElemets["goalsTouched"]);
							   if($value['goalsTouched']==1){
								   $str1 .="goalsTouched=goalsTouched+1,"; 
							   }else if($value['goalsTouched']==-1){
								   $str1.="goalsTouched=goalsTouched-1,";
							   }
							}
							$str1    = substr($str1,0,-1);
				  $statement = $GLOBALS['db_app']->prepare("SELECT count(user_id) as tot FROM `parse_userextra` where user_id= :user_id");
	              $statement->execute(array(':user_id' => $recordId));
	              $result    = end($statement->fetch());
				  if($result>0){
					           $value1['updatedAt']         =   gmdate('Y-m-d\TH:i:s',$t).'Z';
					           $tble_ins                    =   $this -> com->updateRecord_field("parse_userextra",$value1,"user_id = '".$recordId."'",$str1);
					}else{
						 $res = 0;
						 $outPut['STATUSCODE']	=	400;
						 $outPut['MESSAGE']    =	"recordId or userid is not found on table";
			             return $outPut;
					}
				  break;
				  default:
				  break;
			  }
			if($tble_ins){
			  $outPut['STATUSCODE']	=	200;
			  $outPut['MESSAGE']		=	"success";
		      return $outPut;
				   }else{
					     $res = 0;
					     $outPut['STATUSCODE']	=	400;
						 $outPut['MESSAGE']    =	"Data is not updated ,please try again";
			             return $outPut;
					}
		   }
		 exit;
   }

    function  valuesGet($pageAction){
		$tempVar        =  explode('&',$pageAction);
		$requestElemets = array();
		//GET method not working and for emergency explode the params
	    foreach($tempVar as $key =>$val){	
				$parts  = explode('=',$val);
				if(isset($parts[1]))					
				$requestElemets[$parts[0]] = $parts[1];
		}
	    return $requestElemets;
		
	}

}
?>
