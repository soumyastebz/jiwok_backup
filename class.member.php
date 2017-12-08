<?



/**************************************************************************** 



   Project Name	::> Jiwok 



   Module 	::> Class for Members add/edit delete management



   Programmer	::> Vijay



   Date		::> 05-02-2007



   



   DESCRIPTION::::>>>>



   This is a Class code used to add/edit Members  .



  



*****************************************************************************/



include_once("class.DbAction.php");







class Member extends DbAction{



	



	public $language;



	public $objDb;



		



	public function __construct($language){



		//setting the language 



		$this->language		= $language;



		



	}



	



	public function _getCampaignId($bannerid)



	{



	  	$query = "select campaignid from qu_pap_banners where bannerid='".addslashes(trim($bannerid))."'";



		$result = $GLOBALS['db']->getRow($query, DB_FETCHMODE_ASSOC);



		return trim($result['campaignid']);



	



	}



		



	



	//following function for listing the users 



	//function



	public function _showPage($userType,$totalRecs,$i = 0,$no_rec = 0,$field,$type,$searchQuery){



			$fromLimit = $no_rec*($i - 1);



			$toLimit = $no_rec;



			if(trim($searchQuery)!=''){



				$query = "SELECT * FROM user_master WHERE user_type=".$userType.$searchQuery." ORDER BY $field $type LIMIT $fromLimit,$toLimit";



			}else{



			$query = "SELECT * FROM user_master WHERE user_type=".$userType." ORDER BY $field $type LIMIT $fromLimit,$toLimit";



			}



			$result = $GLOBALS['db']->query($query);



			return $result;



	} 

	//following function for listing the distinct users present in the program queue table
	public function _showPageQueue($userType,$totalRecs,$i = 0,$no_rec = 0,$field,$type,$searchQuery)
	{
		$fromLimit 	= 	$no_rec*($i - 1);
		$toLimit 	= 	$no_rec;
		if(trim($searchQuery)!='')
		{
			$query = "SELECT DISTINCT PQ.user_id,PQ.user_name,UM.user_fname,UM.user_lname,UM.user_status FROM program_queue AS PQ LEFT JOIN user_master AS UM ON UM.user_id	=	PQ.user_id	LEFT JOIN brand_user AS BU ON BU.user_id		=	PQ.user_id WHERE UM.user_type=".$userType.$searchQuery." AND PQ.user_name	!=	''	ORDER BY $field $type LIMIT $fromLimit,$toLimit";
		}
		else
		{
			$query = "SELECT DISTINCT PQ.user_id,PQ.user_name,UM.user_fname,UM.user_lname,UM.user_status FROM program_queue AS PQ LEFT JOIN user_master AS UM ON UM.user_id	=	PQ.user_id	LEFT JOIN brand_user AS BU ON BU.user_id		=	PQ.user_id WHERE UM.user_type=".$userType." AND PQ.user_name	!=	''	ORDER BY $field $type LIMIT $fromLimit,$toLimit";
		}		
		$result = $GLOBALS['db']->query($query);
		return $result;
	} 
	//following function for listing the program queue entries for the corresponding users
	public function _showPageQueueView($userId,$totalRecs,$i = 0,$no_rec = 0,$field,$type,$searchQuery)
	{
		$fromLimit 	= 	$no_rec*($i - 1);
		$toLimit 	= 	$no_rec;
		
		$query = "SELECT PQ.*,PD.program_title FROM program_queue AS PQ LEFT JOIN program_detail AS PD ON PQ.program_flex_id=PD.flex_id AND PQ.workout_lang_selected = PD.language_id	WHERE PQ.user_id=".$userId.$searchQuery." AND PQ.user_name	!=	''	ORDER BY $field $type LIMIT $fromLimit,$toLimit";		
		//echo $query;
		$result = $GLOBALS['db']->query($query);
		return $result;
	} 
	//following function for listing the req for unsubscribed users 



	//function



	public function _showReqUnsubPage($i = 0,$no_rec = 0,$field,$type,$searchQuery){







 			$fromLimit = $no_rec*($i - 1);



			$toLimit = $no_rec;



			if(trim($searchQuery)!=''){



				$query = "SELECT * FROM user_master WHERE user_unsubscribed= 1 ".$searchQuery." ORDER BY $field $type LIMIT $fromLimit,$toLimit";



			}else{



			$query = "SELECT * FROM user_master WHERE user_unsubscribed= 1 ORDER BY $field $type LIMIT $fromLimit,$toLimit";



			}



			$result = $GLOBALS['db']->query($query);



			return $result;



	}



	//following function for listing the req for unsubscribed users 



	//function



	public function _showUnsubscribedUsers($i = 0,$no_rec = 0,$field,$type,$searchQuery){







 			$fromLimit = $no_rec*($i - 1);



			$toLimit = $no_rec;



			if(trim($searchQuery)!=''){



				$query = "SELECT * FROM user_master WHERE user_unsubscribed= 2 ".$searchQuery." ORDER BY $field $type LIMIT $fromLimit,$toLimit";



			}else{



			$query = "SELECT * FROM user_master WHERE user_unsubscribed= 2 ORDER BY $field $type LIMIT $fromLimit,$toLimit";



			}



			$result = $GLOBALS['db']->query($query);



			return $result;



	}  



	public function _getOneUser($userId){







 			$sql = "SELECT * FROM user_master WHERE user_id= '".$userId."'";



			



			$result = $GLOBALS['db']->getRow($sql,DB_FETCHMODE_ASSOC);



			return $result;



	} 



	



	/*



		Function to fetch all menu_id of genre from label_manager table



	*/



	public function _getGenreMenus($masterId,$lanId){



		$sql 	= "SELECT menu_id FROM menus WHERE menumaster_id = {$masterId} AND menu_status =1";



		$result = $GLOBALS['db']->getAll($sql,DB_FETCHMODE_ASSOC);



		



		foreach($result as $key => $data){



			$menuId = $data['menu_id'];



			$query	= "SELECT labeltype_id,label_name FROM label_manager WHERE labeltype_id = {$menuId} AND language_id = {$lanId} AND label_type = 'MENU'";



			$res	= $GLOBALS['db']->getRow($query,DB_FETCHMODE_ASSOC);



			$returnArray[$res['labeltype_id']]	= stripslashes($res['label_name']);



		}



		return $returnArray;



	}



	



	/*



		Function to fetch all menu_id of options from label_manager table



	*/



	public function _getOptionalMenus($masterId,$lanId){



		$sql 	= "SELECT menu_id FROM menus WHERE menumaster_id = {$masterId} AND menu_status =1";



		$result = $GLOBALS['db']->getAll($sql,DB_FETCHMODE_ASSOC);



		



		foreach($result as $key => $data){



			$menuId = $data['menu_id'];



			$query	= "SELECT labeltype_id,label_name FROM label_manager WHERE labeltype_id = {$menuId} AND language_id = {$lanId} AND label_type = 'MENU'";



			$res	= $GLOBALS['db']->getRow($query,DB_FETCHMODE_ASSOC);



			$returnArray[$res['labeltype_id']]	= stripslashes($res['label_name']);



		}



		return $returnArray;



	}



	//get user genre options



	public function _getUserGenre($userId,$genreId){



	



		$genreQuery	= "SELECT user_options.menu_id,user_options.menu_value FROM user_options,menus,menu_master WHERE user_options.usermaster_id =".$userId." and menus.menu_id=user_options.menu_id and menu_master. menumaster_id=".$genreId." and  menu_master. menumaster_id=menus.menumaster_id";



	



			$res			= $GLOBALS['db']->getAll($genreQuery,DB_FETCHMODE_ASSOC);



			foreach($res as $k => $data){



					 $key[] 	= $data['menu_id'];



					 



			}



			$result		=	$key;



			return $result;



	}



	



	//get user optional field selected values



	public function _getUserOptinalFieldValues($userId,$optionId){



	



		$genreQuery	= "SELECT user_options.menu_id,user_options.menu_value FROM user_options,menus,menu_master WHERE user_options.usermaster_id =".$userId." and menus.menu_id=user_options.menu_id and menu_master. menumaster_id=".$optionId." and  menu_master. menumaster_id=menus.menumaster_id";



	



			$res			= $GLOBALS['db']->getAll($genreQuery,DB_FETCHMODE_ASSOC);



			foreach($res as $k => $data){



					 $key[id][] 	= $data['menu_id'];



					 $key[value][] 	= $data['menu_value'];



					 



			}



			$result		=	$key;



			return $result;



	}



	



	// Function to add a member



	public function _insertMember($insArray,$lanId,$sp){



		//Getting the value of next id to put it in user_ptions as usermaster_id.



		$res = $GLOBALS['db']->query("SELECT MAX(user_id) as maximum FROM user_master");



		while ($res->fetchInto($row)) {



			$nextId = $row[0]+1;



		}



		//Getting the value of next id to put it in user_ptions as usermaster_id.



		$resNike = $GLOBALS['db']->query("SELECT MAX(nike_id ) as maximumNike FROM nike");



		while ($resNike->fetchInto($row)) {



			$nextIdNike = $row[0]+1;



		}



		



		$elmts							= array_slice($insArray,0,11);	 



		$elmts['user_id']				= $nextId;	



		$elmts['user_status']			= $insArray['user_status'];



		$elmts['user_doj']				= date('Y-m-d');



		$elmts['user_password'] 		= base64_encode($insArray['user_password']);



		$elmts['user_username']			= $insArray['user_username'];



        $elmts['user_weight_value'] 	= $insArray['user_weight_value'];



        $elmts['user_weight_unit']  	= $insArray['user_weight_unit'];



        $elmts['user_height_value'] 	= $insArray['user_height_value'];



        $elmts['user_height_unit'] 		= $insArray['user_height_unit'];



        $elmts['user_photo']       		= $insArray['user_photo'];	



		$elmts['user_dob']        		= $insArray['user_dob'];



		$elmts['user_voice']			= $insArray['user_voice'];



		$elmts['user_newsletter']		= $insArray['user_newsletter'];



		$elmts['user_discount_status']	= $insArray['user_discount_status'];



		if($insArray['user_alt_email']!='') {



			$elmts['user_alt_email']		= $insArray['user_alt_email'];



		}



		



		$this->_insertRecord("user_master",$elmts);



		



		// Insert nike data



		$elmtsNike['nike_id ']		= $nextIdNike; 



		$elmtsNike['nike_userid']	= $nextId;



		$elmtsNike['nike_login']	= $insArray['nike_login'];



		$elmtsNike['nike_password']	= base64_encode($insArray['nike_password']);



		$this->_insertRecord("nike",$elmtsNike);



		



		//Insert into user_options table



		$elmts					= array();



		global $siteMasterMenuConfig;



		$menuList				= $this->_getGenreMenus($siteMasterMenuConfig['GENRE_ID'],$lanId);



		$optionList				= $this->_getGenreMenus($siteMasterMenuConfig['USER_OPTIONAL_FIELDS'],$lanId);



		$elmts['usermaster_id']	= $nextId;



		



		



			//inserting the Genre



			foreach($menuList as $key=>$value){



				if($insArray['genre_'.$key]){



					$elmts['menu_id'] = $key;



					$this->_insertRecord("user_options",$elmts);



				}



			}



		



			//Inserting the optional fields



			foreach($optionList as $key => $data){



				if(trim($insArray['option_'.$key])!=""){



					$elmts['menu_id'] = $key;



					$elmts['menu_value'] = addslashes($insArray['option_'.$key]);



					$this->_insertRecord("user_options",$elmts);



				}



			}



			//print_r($sp);



			if(count($sp) > 1){



									$elmts['menu_id'] = $siteMasterMenuConfig['SPORTSCAT'];



									$elmts['menu_value'] = implode(',',$sp);//print_r($elmts);exit;



									$this->_insertRecord("user_options",$elmts);



			}



			else{



					$elmts['menu_id'] = $siteMasterMenuConfig['SPORTSCAT'];



					$elmts['menu_value'] = $sp[0];



					$this->_insertRecord("user_options",$elmts);



			



			}	



				



	}



	



	// Function to update a member records



	public function _updateMember($userId,$insArray,$lanId,$sp){



		$elmts							= array_slice($insArray,0,11);		



		$elmts['user_status']			= $insArray['user_status'];



		$elmts['user_password'] 		= base64_encode($insArray['user_password']);



		$elmts['user_username']			= $insArray['user_username'];



        $elmts['user_photo']        	= $insArray['user_photo'];



        $elmts['user_weight_value'] 	= $insArray['user_weight_value'];



        $elmts['user_weight_unit']  	= $insArray['user_weight_unit'];



        $elmts['user_height_value'] 	= $insArray['user_height_value'];



        $elmts['user_height_unit']  	= $insArray['user_height_unit'];



		$elmts['user_dob']				= $insArray['user_dob'];



		$elmts['user_voice']			= $insArray['user_voice'];



		$elmts['user_newsletter']		= $insArray['user_newsletter'];



		$elmts['user_discount_status']	= $insArray['user_discount_status'];



		$elmts['user_refferal_status']	= $insArray['user_refferal_status'];



		//print_r($insArray);



		//exit;



		if($insArray['user_alt_email']!='') {
			$elmts['user_alt_email']		= $insArray['user_alt_email'];
			$elmts['user_email']			= $insArray['user_alt_email']; // Update the user email too

		}



		



		$this->_updateRecord("user_master",$elmts,"user_id = {$userId}");



		



		// Update nike data



		$elmtsNike					    = array();



		$elmtsNike['nike_login']		= $insArray['nike_login'];



		$elmtsNike['nike_password']		= base64_encode($insArray['nike_password']);



		$this->_updateRecord("nike",$elmtsNike,"nike_userid = {$userId}");



		//Insert into user_options table



		$elmts					= array();



		global $siteMasterMenuConfig;



		$menuList				= $this->_getGenreMenus($siteMasterMenuConfig['GENRE_ID'],$lanId);



		$optionList				= $this->_getGenreMenus($siteMasterMenuConfig['USER_OPTIONAL_FIELDS'],$lanId);



		$elmts['usermaster_id']	= $userId;







			//delete the existing user options from the database



			$res = $GLOBALS['db']->query("DELETE FROM user_options WHERE usermaster_id=".$userId);



			//inserting the Genre



			foreach($menuList as $key=>$value){



				if($insArray['genre_'.$key]){



					$elmts['menu_id'] = $key;



					$this->_insertRecord("user_options",$elmts);



				}



			}



		



			//Inserting the optional fields



			foreach($optionList as $key => $data){



				if(trim($insArray['option_'.$key])!=""){



					$elmts['menu_id'] = $key;



					$elmts['menu_value'] = addslashes($insArray['option_'.$key]);



					$this->_insertRecord("user_options",$elmts);



				}



			}



		//print_r($sp);exit;



			if(count($sp) > 1){



									$elmts['menu_id'] = $siteMasterMenuConfig['SPORTSCAT'];



									$elmts['menu_value'] = implode(',',$sp);//print_r($elmts);exit;



									$this->_insertRecord("user_options",$elmts);



			}



			else{



					$elmts['menu_id'] = $siteMasterMenuConfig['SPORTSCAT'];



					$elmts['menu_value'] = $sp[0];



					$this->_insertRecord("user_options",$elmts);



			



			}	



				



	}



	/*



	To get all countries from the countries table	



	*/	



	public function _getCountries(){



		$sql 	= "SELECT countries_id,countries_name FROM countries";



		$result = $GLOBALS['db']->getAll($sql,DB_FETCHMODE_ASSOC);



		$countriesArray =array();



		foreach($result as $key => $name){



			$ccode		= $name['countries_id'];



			$cname		= $name['countries_name'];



			



			$countriesArray[$ccode] = $cname;



		}



		return $countriesArray;



	}



	



	/*



	To get all timezone from the timezone table	



	*/	



	public function _getTimezone(){



		$sql 	= "SELECT time_tz,time_name FROM timezone order by time_id";



		$result = $GLOBALS['db']->getAll($sql,DB_FETCHMODE_ASSOC);



		$TimezoneArray =array();



		foreach($result as $key => $name){



			$time_tz		= $name['time_tz'];



			$time_name		= $name['time_name'];



			



			$TimezoneArray[$time_tz] = $time_name;



		}



		return $TimezoneArray;



	}



	



	//get country name added by abhi on dec 10



	public function _getCountryName($id, $language=''){



		$sql 	= "SELECT countries_id,countries_name, countries_name_fr FROM countries WHERE countries_id = ".$id."";



		$result = $GLOBALS['db']->getAll($sql,DB_FETCHMODE_ASSOC);



		foreach($result as $key => $name){



			if($language=='french') {



				$cname		= $name['countries_name_fr'];



			} else {



				$cname		= $name['countries_name'];



			}



		}



		return $cname;



	}



	



	/*



			Function 			: _mailid_exist



			Usage	   			: To check whether the email id already exists



			Variable Passing 	:



	*/



	public function _mailid_exist($email,$id=''){



		$bool	= false;



		



		$query 	= 	"SELECT * FROM user_master WHERE user_email='".$email."' OR user_alt_email = '".$email."'";



		$result = $GLOBALS['db']->getAll($query, DB_FETCHMODE_ASSOC);



		if($id == '' and count($result) >0){



		



			$bool = true;



		}



		



		if($id != '' and count($result) >0){



			if($id != $result[0]['user_id']){



				$bool = true;



			}



			else{



				$bool = false;



			}



		}



		return $bool;



	}



	/*



			Function 			: _username_exist



			Usage	   			: To check whether the login name already exists



			Variable Passing 	:



	*/



	public function _username_exist($username,$id=''){



		$bool	= false;



		



		$query 	= 	"SELECT * FROM user_master WHERE user_username='".addslashes($username)."'";



		$result = $GLOBALS['db']->getAll($query, DB_FETCHMODE_ASSOC);



		if($id == '' and count($result) >0){



		



			$bool = true;



		}



		



		if($id != '' and count($result) >0){



			if($id != $result[0]['user_id']){



				$bool = true;



			}



			else{



				$bool = false;



			}



		}



		return $bool;



	}



	/*



	/*



			Function			: _deleteMember



			Usage				: To delete a Member. This deletes entries from two tables namely, user_master,user_options.



			Variable Passing 	: $id is passed as reference.



			Returns				: Boolean



		*/



		



		public function _deleteMember($id) {



			$bool= 1;



			



			//check number of user id for deletion



			if(count($id) <= 0)



				{



				 $bool= 0;



				}



			else



				{



					//Emplode user id array into a variable



					if(count($id) > 1){



					$usrIds = implode(", ",$id);



					}



					elseif(count($id) == 1){



					$usrIds = $id;



					}



					



					$NewArray = array();



					for($ckuser=0; count($id) > $ckuser; $ckuser++)



					{



					   $Getemail   = mysql_query("select count(*) as yes from user_master where (user_status = 1 or user_status = 2) AND user_id=".$id[$ckuser]);



					  $Getvalue = mysql_fetch_assoc($Getemail);



					 



					   if($Getvalue['yes'] == 1){



					    $NewArray[] = $id[$ckuser];



						 } else { 



						 $bool= 'admin'; 



						 return $bool;



						 }



					}



					



					if(count($NewArray) > 1){



					$usrIds = implode(", ",$NewArray);



					}



					elseif(count($NewArray) == 1){



					$usrIds = $NewArray;



					}



					



					



		//Delete value from forum posts and tickets



	   for($numuser = 0; count($NewArray) > $numuser; $numuser++)



	   {



	   $Getemail   = mysql_query("select user_email,user_alt_email from user_master where user_id=".$NewArray[$numuser]);



	   $emailvalue = mysql_fetch_assoc($Getemail);

		//For avoiding partial deletion
		if(($emailvalue['user_email']	!=	$emailvalue['user_alt_email']))
		{
			$emailvalue['user_email']	=	$emailvalue['user_alt_email'];
		}



		$ForumAdmin = mysql_query("select user_type, user_id from forum_users where username='".$emailvalue['user_email']."'");



		$Ckadmin    = mysql_fetch_assoc($ForumAdmin); 



		



		    if($Ckadmin['user_type'] != 3)



		    {    



			$this->_deleteData('forum_posts', 'poster_id='.$Ckadmin['user_id']);  // delete forum posts



			$this->_deleteData('forum_users', 'username="'.$emailvalue['user_email'].'"'); // delete forum user



			}



			



			// delete ticket user.   



		   $SqlTicketid = mysql_query("select client_id from hdp_clients where email='".$emailvalue['user_email']."'");



		   $ResTicketid = mysql_fetch_assoc($SqlTicketid); 



		   		if($ResTicketid['client_id'] != '')



		   		{



			   		$this->_deleteData('hdp_ticket_replies', 'reporter_id='.$ResTicketid['client_id']);  // delete ticket reply



			   		$this->_deleteData('hdp_tickets', 'client_id='.$ResTicketid['client_id']); // delete ticket



			   		$this->_deleteData('hdp_clients', 'client_id='.$ResTicketid['client_id']); // delete user



				}	



				



				



			// Delete user pic	



			$sql = mysql_query("SELECT user_photo FROM user_master WHERE user_id=".$NewArray[$numuser]);



			$res = mysql_fetch_assoc($sql);



			    $usrPhoto=$res[$i]['user_photo'];



				if(file_exists("../uploads/users/".$usrPhoto))



				{



					unlink("../uploads/users/".$usrPhoto);



				}



				



			//Delete the value from option table.



			$sql = mysql_query("DELETE FROM user_options WHERE usermaster_id=".$NewArray[$numuser]);



			// Delete user from user_master table



			$sql = mysql_query("DELETE FROM user_master WHERE user_id=".$NewArray[$numuser]);



				



		} // end of deletion



					



					



					



					



				}	



			return $bool;



		}



		//get nike dtails



		public function _getNikeDetail($userId){



			



			//$selectQueryNike	=	"select * from nike where nike_userid=".$userId;

			$selectQueryNike	=	sprintf("SELECT SQL_SMALL_RESULT * FROM `nike` WHERE `nike_userid`='%s' LIMIT 1",mysql_real_escape_string($userId));

			$resultNike 		= 	$GLOBALS['db']->getRow($selectQueryNike,DB_FETCHMODE_ASSOC);



			return $resultNike;



		}



		//get all jobs 



		public function _getAllJobs($lanId){



		



				$langName		=	strtolower($this->_getLanName($lanId));



				$lang_job		=	"job_".$langName;		



				$sql_job		=	"select job_id, {$lang_job} from job";



				$res_job		=	$GLOBALS['db']->getAll($sql_job,DB_FETCHMODE_ASSOC);



				$jobArray		=	array();



				



				foreach($res_job as $key=>$val_job)



					{



						$jobArray[$val_job['job_id']]	=	$val_job[$lang_job];



					}	



					



				return $jobArray;



		}



		



		//get all Sports



		public function _getAllSports($lanId){



				



				$langName		=	strtolower($this->_getLanName($lanId));



				$lang_sport		=	"sport_".$langName;



				$sql_sport		=	"select sport_id,sport_{$langName} from sport";



				$res_sport		=	$GLOBALS['db']->getAll($sql_sport,DB_FETCHMODE_ASSOC);



				$sportArray		=	array();



				foreach($res_sport as $key=>$val_sport)



					{



						$sportArray[$val_sport['sport_id']]	=	$val_sport[$lang_sport];



					}



					



				return $sportArray;



		}



		



		//get language name



		public function _getLanName($lanId){



		



			//get language name



				$sql_lan		=	"select language_name from languages WHERE language_id = {$lanId}";



				$res_lan		= 	$GLOBALS['db']->getRow($sql_lan,DB_FETCHMODE_ASSOC);



				$lanName		=	$res_lan['language_name'];



			



			return $lanName;



		



		}



		//get user name



		public function _getUserName($uId){



		



			//get user name



				$sql_user		=	"select user_fname,user_lname from user_master WHERE user_id = {$uId}";



				$res_user		= 	$GLOBALS['db']->getRow($sql_user,DB_FETCHMODE_ASSOC);



				$return			=	array();



				$return['fname']=	$res_user['user_fname'];



				$return['lname']=	$res_user['user_lname'];



				$return['uemail']=	$res_user['user_email'];



			return $return;



		



		}



		



		//get user details by user id



		public function _getAllByUserId($userId){



			



			$selectQuery	=	"select * from user_master where user_id=".$userId; 



			$result 		= 	$GLOBALS['db']->getRow($selectQuery,DB_FETCHMODE_ASSOC);



			return $result;



		}



		// Function to update a member records



	public function _updateFullMemberDetails($userId,$insArray,$lanId,$sp){



		$elmts						= array_slice($insArray,0,3);		



		$elmts['user_fname']		= $insArray['user_fname'];



		$elmts['user_lname'] 		= $insArray['user_lname'];
		
		$elmts['user_email'] 		= $insArray['user_alt_email'];//For Making the User email and user alt email same

		$elmts['user_gender']		= $insArray['user_gender'];



		$elmts['user_address']		= $insArray['user_address'];



		$elmts['user_city']		    = $insArray['user_city'];



		$elmts['user_state']	    = $insArray['user_state'];



		$elmts['user_country']	    = $insArray['user_country'];



		$elmts['user_timezone']	    = $insArray['user_timezone'];



		$elmts['user_zip']	   		= $insArray['user_zip'];



		$elmts['user_language']	   	= $insArray['user_language'];



		$elmts['user_voice']	   	= $insArray['user_voice'];



        $elmts['user_photo']        = $insArray['user_photo'];



        $elmts['user_weight_value'] = $insArray['user_weight_value'];



        $elmts['user_weight_unit']  = $insArray['user_weight_unit'];



        $elmts['user_height_value'] = $insArray['user_height_value'];



        $elmts['user_height_unit']  = $insArray['user_height_unit'];



		$elmts['user_dob']			= $insArray['user_dob'];



		$elmts['user_voice']		= $insArray['user_voice'];		

		//For storing the email history if user changes the current email id starts
		//By 	:	Dileep.E
		//Date	:	25.11.11
		$emailQuery		=	"select user_email,email_history,user_alt_email from user_master WHERE user_id	=	".$userId;
		$res_email		= 	$GLOBALS['db']->getRow($emailQuery,DB_FETCHMODE_ASSOC);		
		if($elmts['user_alt_email']	!=	$res_email['user_alt_email'])
		{
			if($res_email['email_history']	!=	"")
			{
				$oldEmails	=	explode(",", $res_email['email_history']);
				if(!in_array(strtolower($res_email['user_alt_email']),$oldEmails))
				{
					$emailInsert['email_history']	=	$res_email['email_history'].",".strtolower($res_email['user_alt_email']);
					$this->_updateRecord("user_master",$emailInsert,"user_id = {$userId}");
				}
			}
			else
			{
				$emailInsert['email_history']	=	strtolower($res_email['user_alt_email']);
				$this->_updateRecord("user_master",$emailInsert,"user_id = {$userId}");
			}
		}
		//For storing the email history if user changes the current email id ends


		$this->_updateRecord("user_master",$elmts,"user_id = {$userId}");



		



		// Update nike data



		$elmtsNike					    = array();



		$elmtsNike['nike_login']		= $insArray['nike_login'];



		$elmtsNike['nike_password']		= base64_encode($insArray['nike_password']);



		



		$nikeQuery	=	"select * from nike where nike_userid = {$userId}"; 



		$resNike 		= 	$GLOBALS['db']->getRow($nikeQuery,DB_FETCHMODE_ASSOC);



		if(count($resNike)>0)



			$this->_updateRecord("nike",$elmtsNike,"nike_userid = {$userId}");



		else



		    $this->_insertRecord("nike",$elmtsNike);



		//Insert into user_options table



		$elmts					= array();



		global $siteMasterMenuConfig;



		$menuList				= $this->_getGenreMenus($siteMasterMenuConfig['GENRE_ID'],$lanId);



		$optionList				= $this->_getGenreMenus($siteMasterMenuConfig['USER_OPTIONAL_FIELDS'],$lanId);



		$elmts['usermaster_id']	= $userId;







			//delete the existing user options from the database



			$res = $GLOBALS['db']->query("DELETE FROM user_options WHERE usermaster_id=".$userId);



			//inserting the Genre



			foreach($menuList as $key=>$value){



				if($insArray['genre_'.$key]){



					$elmts['menu_id'] = $key;



					$this->_insertRecord("user_options",$elmts);



				}



			}



		



			//Inserting the optional fields



			foreach($optionList as $key => $data){



				if(trim($insArray['option_'.$key])!=""){



					$elmts['menu_id'] = $key;



					$elmts['menu_value'] = addslashes($insArray['option_'.$key]);



					$this->_insertRecord("user_options",$elmts);



				}



			}



		//print_r($sp);exit;



			if(count($sp) > 1){



									$elmts['menu_id'] = $siteMasterMenuConfig['SPORTSCAT'];



									$elmts['menu_value'] = implode(',',$sp);//print_r($elmts);exit;



									$this->_insertRecord("user_options",$elmts);



			}



			else{



					$elmts['menu_id'] = $siteMasterMenuConfig['SPORTSCAT'];



					$elmts['menu_value'] = $sp[0];



					$this->_insertRecord("user_options",$elmts);



			



			}	



	}	

   /////////////this function modified for tracking the unsubscribe history (jasmin:1-10-2010)

	public function _sentReqstMemUnSubscribe($userId)



	{



		$elmts								= array();



		$elmts['user_unsubscribed']			= 1;



		//$elmts['user_req_unsubscribe'] 		= date('m-d-Y');

		$elmts['user_req_unsubscribe'] 			= date('Y-m-d');



		$this->_updateRecord("user_master",$elmts,"user_id = {$userId}");

		

		$unsub_usr               =            array();

		$unsub_usr['user_id']			    = $userId;

		$unsub_usr['user_unsubscribed'] 		= 1;

		$unsub_usr['user_req_unsubscribe'] 			= date('Y-m-d');

		

		$this->_insertRecord("unsubscribe_details",$unsub_usr);

		

	}



	public function _unsubscribeUserMemebership($userId)



	{	$bool=0;



	



		if($userId != ""){



		



		$elmts							= array();



		//$elmts['user_status']			= 2;



		$elmts['user_free_period']		= 0;



		$elmts['user_discount_status']	= 0;



		$elmts['user_newsletter']		= 0;



		$elmts['user_unsubscribed']		= 2;



		$elmts['unsubscribe_date'] 		= date('m/d/Y');



		$this->_updateRecord("user_master",$elmts,"user_id IN($userId)");



		



		/*$elemtsPay						=	array();



		$elemtsPay['payment_expdate']	=	date('Y-m-d');



		



		$this->_updateRecord("payment",$elemtsPay,"payment_userid IN( $userId )");*/



		



		$bool=1;



		



		}



		return $bool;



	}



	public function _getUnsubscribeMembershipPeriod()



	{



		//get user name



		$sql_period		=	"select membership_unsubscribeperiod from settings WHERE settings_id=1";



		$res_period		= 	$GLOBALS['db']->getRow($sql_period,DB_FETCHMODE_ASSOC);



		return	$res_period;



		



	}



	public function _sendUnsubscribeMails($userId,$mailContents,$subject)



	{



		 $query		= "select * from settings";



		 $result	= $GLOBALS['db']->getAll($query,DB_FETCHMODE_ASSOC);	



		 foreach($result as $data){



			$CONTACT_MAIL	=	$data['contact_email'];



			$RETURN_MAIL	=	$data['return_email'];



			$BOUNCE_MAIL	=	$data['bounce_email'];



		}



		



		$sql_mail	=	"select user_id,user_alt_email AS user_email,user_fname,user_lname from user_master WHERE user_id IN($userId)";



		$res_mail	= 	$GLOBALS['db']->getAll($sql_mail,DB_FETCHMODE_ASSOC);



		foreach($res_mail as $key=>$value)



			{



				$uemail		=	$value['user_email'];



				$user_fname = 	$value['user_fname'];



				$user_lname = 	$value['user_lname'];

				

				$user_id    =   $value['user_id'];



				//$subject = "Jiwok Membership Unsubscription"; As per the client request we have changed the subject



				if($subject[$user_id]==''){$subject[$user_id] = "Votre désabonnement à Jiwok";}

				

				if(trim($mailContents[$user_id])==''){



					$msg  = "\n Hello ".$user_fname." ".$user_lname.",\n\n";

	

					$msg .= "Your jiwok membership has been unsubscribed.You couldnot create and use mp3 workouts afterwards.\n";

	

					$msg .= "\n\nThank you";

	

					$msg .= "\nJiwok Coach";

				}else{

					$msg  =$mailContents[$user_id];

				}



				$fromAddress = 'Jiwok Coach <coach@jiwok.com>';



				$headers  = 'Mime-version: 1.0' . "\r\n"; 



				$headers .= 'Content-type: text/plain; charset="UTF-8"' . "\r\n"; 



				//$headers .= 'Content-transfer-encoding: quoted-printable' . "\r\n"; 



				$headers .= "From: ".$fromAddress."\n";



				$headers .= "Return-Path: $RETURN_MAIL\n";



				$headers .= "Return-Receipt-To: $BOUNCE_MAIL\n";	

                //echo "<br>".$uemail.$subject.$msg."<br>";

				@mail($uemail,$subject[$user_id],$msg,$headers);



			}



			$bool=1;



			return $bool;



		



	}



	



	public function _sentReqstMemSubscribe($userId)



	{



		$elmts								= array();



		$elmts['user_unsubscribed']			= 0;



		//$elmts['user_req_unsubscribe'] 		= '';



		$userId	= $GLOBALS['db']->quoteSmart($userId);



		$this->_updateRecord("user_master",$elmts,"user_id = {$userId}");



	}



	



	public function prepareSearchKeyword($keyword, $surround = 0){



		$not_allowed_chars	= array("%","$","#","^","!");



		$search_keyword		= str_replace('&quot;', '"', $keyword);



		$search_keyword		= str_replace($not_allowed_chars, "_", $search_keyword);



		$search_keyword		= str_replace('*', '%', $search_keyword);



		if($surround==1) {



			$search_keyword	= '%'.$search_keyword.'%';



		}



		



		return $search_keyword;



	}



	



	public function prepareSearchQuery($keyword){



		$keyword	= $this->prepareSearchKeyword($keyword);



		$searchQuery	= '';



		if($keyword != ''){



			$exp_keywords	= explode(" ",$keyword, 2);



			$keyword = '%'.$keyword.'%';



			$keyword = $GLOBALS['db']->quoteSmart($keyword);



			if(sizeof($exp_keywords) == 2){



				$exp_keywords[0]    = '%'.$exp_keywords[0].'%';



				$exp_keywords[0]    = $GLOBALS['db']->quoteSmart($exp_keywords[0]);



				$exp_keywords[1]    = '%'.$exp_keywords[1].'%';



				$exp_keywords[1]    = $GLOBALS['db']->quoteSmart($exp_keywords[1]);



				$searchQuery	= " and  (user_fname like {$exp_keywords[0]} OR user_lname like {$exp_keywords[1]} OR user_email like {$keyword} OR user_alt_email like {$keyword})";	



			} else {



				$searchQuery	= " and  (user_fname like {$keyword} OR user_lname like {$keyword} OR user_email like {$keyword} OR user_alt_email like {$keyword})";	



			}



		}



		



		return $searchQuery;



	}



	



	public function getCount($searchQuery='', $user_type=1){



		$count			= 0;



		$query	= "SELECT count(*) FROM user_master WHERE user_type = '{$user_type}'" ;



		if($searchQuery != ''){



			$query			.= $searchQuery;



		}



		$count	= $GLOBALS['db']->getOne($query);



		



		return $count;



	}



	



	public function getPaymentStatus($user_id){



		$result	= array();



		$query	= "SELECT * FROM `payment` 



					WHERE payment_userid = ? AND payment_status = 1 



					ORDER BY payment_expdate DESC LIMIT 0, 1";



		$res	=& $GLOBALS['db']->getRow($query, array($user_id), DB_FETCHMODE_ASSOC);



		if(!PEAR::isError($res)){



			$result	= $res;



		} 



		



		return $result;



	}



	



	public function addNewPayment($user_id, $amount, $expiry_date){



		$query	= "INSERT INTO `payment` 



		(`payment_userid`, `payment_date`, `payment_status`, `payment_amount`, `payment_expdate`, `payment_error_code`) 



		VALUES 



		(?, CURDATE(), 1, ?, ?, '00000')";



		$res  = $GLOBALS['db']->query($query, array($user_id, $amount, $expiry_date));



		if(!PEAR::isError($res)){



			return true;



		} /*else {



			die($res->getDebugInfo());



		}*/



		



		return false;



	}



	



	public function updatePayment($payment_id, $userId, $expiry_date)
	{
		$res	= false;
		$last_payment_details	= 	$this->getPaymentStatus($userId);
		$croneStatus			=	$this->croneStatus($userId);
		if(sizeof($last_payment_details)==0)
		{
			$res	= $this->addNewPayment($userId, 0, $expiry_date);
			if($croneStatus)
			{
				$query    = "UPDATE `payment_cronjob` 
						 	SET payment_expiry_date = ? 
						 	WHERE user_id = ? AND status = 'VALID'";
				$update	  = $GLOBALS['db']->query($query, array($expiry_date, $userId));	
			}
			return $res;
		} 
		else 
		{
			$payment_id	= $last_payment_details['payment_id'];
			$query    = "UPDATE `payment` 
						 SET payment_expdate = ? 
						 WHERE payment_id = ? ";
			$res  = $GLOBALS['db']->query($query, array($expiry_date, $payment_id));
			if(!PEAR::isError($res))
			{
				if($croneStatus)
				{
					$query    = "UPDATE `payment_cronjob` 
						 	SET payment_expiry_date = ? 
						 	WHERE user_id = ? AND status = 'VALID'";
					$update	  = $GLOBALS['db']->query($query, array($expiry_date, $userId));	
				}
				return true;
			}
		}
	    return false;
	}
	
	//Added by Dileep.E,20.10.2011
	//For getting the payment crone staus of crone(if exist return true else false)
	public function croneStatus($user_id)
	{		
		$query			= "SELECT * FROM `payment_cronjob` 
					   	   WHERE user_id = '".$user_id."' AND status = 'VALID' 
		           		   ORDER BY payment_expiry_date DESC LIMIT 0, 1";
		$arrRow	 		=	$GLOBALS['db']->query($query);
		$arrRowCount	= $arrRow->numRows();
		if($arrRowCount	>	0)
			return true;
		else
			return false;	
	}
	//Added by Dileep.E,20.10.2011
	//For getting the newpayment transactions of user
	public function getNewPaymentReport($user_id,$trans="")
	{
		if($trans=="")
			$query		= "select * from payment_transactions WHERE user_id	=	'".$user_id."'";
		else
			$query		= "select * from payment_transactions WHERE user_id	=	'".$user_id."' AND status	=	'".$trans."'";	
		$result	= $GLOBALS['db']->getAll($query,DB_FETCHMODE_ASSOC);		
		return 	$result;
	}
	//Added by Dileep.E,24.10.2011
	//For getting the new paybox status
	public function getNewPayBoxStatus($user_id,$status="")
	{
		if($status=="")
			$query		= "select * from payment_paybox WHERE user_id	=	'".$user_id."'";
		else
			$query		= "select * from payment_paybox WHERE user_id	=	'".$user_id."' AND status	=	'".$status."'";	
		$result	= $GLOBALS['db']->getAll($query,DB_FETCHMODE_ASSOC);		
		return 	$result;
	}

	public function getCountryList($language_name) {



		if ($language_name == 'french') {



			$sql 	= "SELECT countries_id, countries_name_fr AS countries_name FROM countries ORDER BY countries_name_fr";



		} 
		elseif($language_name	==	'polish')
		{
			$sql 	= "SELECT countries_id, countries_name_pl AS countries_name FROM countries ORDER BY countries_name_pl";
		}
		
		else {



			$sql 	= "SELECT countries_id, countries_name FROM countries ORDER BY countries_name";



		}

		
		$countries = $GLOBALS['db']->getAll($sql, DB_FETCHMODE_ASSOC);



		



		return $countries;



	}

	

	////get all timezones

	public function getTimezoneList() {

          

		  $sql 	= "SELECT * FROM timezone ORDER BY time_id";

              $timezones = $GLOBALS['db']->getAll($sql, DB_FETCHMODE_ASSOC);

		return $timezones;

	}

	////get timezone name from time zone value

	public function _getTimezoneName($timezone,$lanId="")

	{

	  $sql 	= "SELECT * FROM timezone where   	

time_tz='$timezone'";

              $timezone = $GLOBALS['db']->getRow($sql, DB_FETCHMODE_ASSOC);

			  if($lanId==1) return $timezone['time_name'];

			  else return $timezone['gmt_timezone'];

	}

	//get user genre options



	public function _getAllGenre(){



	



		$genreQuery	= "SELECT * from genre where status=1";



	



			$res			= $GLOBALS['db']->getAll($genreQuery,DB_FETCHMODE_ASSOC);



			foreach($res as $k => $data){



					 $key[] 	= $data['name'];



					 



			}



			$result		=	$key;



			return $result;



	}



	//get the vocal from general table



	public function _getAllVocal($lanid){



	



		$genreQuery	= "SELECT * from general where table_name='vocal' and language_id=".$lanid;



	



			$res			= $GLOBALS['db']->getAll($genreQuery,DB_FETCHMODE_ASSOC);



			foreach($res as $k => $data){



					 $key[] 	= $data['item_name'];



					 



			}



			$result		=	$key;



			return $result;



	}



	



	



}







?>
