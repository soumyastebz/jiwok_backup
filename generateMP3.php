<?php



session_start();



include_once('includeconfig.php');



include_once("includes/classes/class.programs.php");
//include_once("includes/trak_analysis.php");



if($lanId=="")



     $lanId=1;



$errorMsg = '';	 



$userid		= $_SESSION['user']['userId'];	



$objGen     	= new General();



$objPgm     	= new Programs($lanId);



$parObj 		= new Contents();
//$trakObj		=	new trakAnalysis();



$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');



$arrayData		= $returnData['general'];







$pgmFlex	= trim(stripslashes($_REQUEST['pgmFlex']));



$workFlex	= trim(stripslashes($_REQUEST['workFlex']));



$wFlex  = str_replace('%20',' ',trim($workFlex));

$wFlex  = str_replace('%2B','+',$wFlex);



//echo $wFlex;



$genreList  = trim(stripslashes($_REQUEST['genList']));



$rem		= trim($_REQUEST['rem']);



$vocal_type	= ( (int) trim($_REQUEST['vocal']) ) == 1 ? 1 : 2; // vocal_type, if checked 1 else 2



$genreArray = array();







$genreIdArray=array();















if($genreList!="")



{



 $res	= $objPgm->_getSelectedGenres($genreList,$userid);



}



else



{



$res		= $objPgm->_getSelectedGenres('',$userid);



}







if(count($res)>0)



{



for($i=0;$i<count($res);$i++)



{



	$genreArray[] = trim(stripslashes($res[$i]['genre_name']));



	



	



}



$selected_genre = implode(',',$genreArray);



}



else



{ $selected_genre = ''; }











$user = $objPgm->_getUserDetails($userid);



$user_email 	= $user['user_email'];







//if($rem==1){



//if($userid==7475){//For implementing additional options without disturbing other users



	$dayTime	= date('Y-m-d H:i:s',time());



	$tmpUserMemoryArr	= $objPgm->_getUserMemoryGenres($userid);



	$memoryGenre		= "";



	if(trim($genreList)==''){



			$random_genre_status	= 1;



	}



	if(trim($genreList)!=''){



		$random_genre_status		= 0;



		$memoryGenre				= $selected_genre;



	}



	



	if(count($tmpUserMemoryArr)==0){



		/*$memInsArr	= array('username'=>addslashes(trim($user_email)), 'user_id'=>addslashes($userid), 'genre_name_memory'=>addslashes($selected_genre), 'last_updated'=>$dayTime, 'vocal_coach_status'=>$vocal_type, 'random_genre_status'=>$random_genre_status, 'remember_status'=>$rem);



		$objPgm->_insertDetails($insArray,'user_memory_wrk_genre');*/



		$objPgm->_insertInToUserSongMemory($user_email, $userid, $dayTime, $vocal_type, $random_genre_status, $rem, $memoryGenre);



	}else{



		$objPgm->_updateMemoryGenreStatus($memoryGenre, $vocal_type, $random_genre_status, $rem, $userid);



	}



//}//if($userid==7475)	



//}















$workoutOrderNumber	= $_GET['pageNum'];







$invoke_time = date('Y-m-d H:i:s ');



//$wFlex  = str_replace(' ','+',trim($workFlex));

//$wFlex		=	trim($workFlex);



$insArray   = array('user_name'=>addslashes(trim($user_email)),'user_id'=>addslashes($userid),'program_flex_id'=>addslashes($pgmFlex),'workout_flex_id'=>addslashes($wFlex),'invoke_time'=>addslashes($invoke_time),'status'=>1,



					'selected_genre'=>addslashes(trim($selected_genre)), 'vocal_type'=>$vocal_type, 'workoutOrderNumber'=>$workoutOrderNumber,'workout_lang_selected'=>$lanId);







$objPgm->_insertDetails($insArray,'program_queue');
///for traking tagoffline workout generation
$workoutDetail 		= $objPgm->_getWorkoutDetailAll(trim(str_replace(' ','',$wFlex)),$lanId);
$workout_title 		= trim(stripslashes($workoutDetail['workout_title']));
$property	=	array("Source" =>"Tagoffline","Title"=>$workout_title, "email"=>$user_email,"Section No"=>$workoutOrderNumber);
//$response 	=	$trakObj->trakWorkOutGeneration($user_email,$property);



if($rem==1)



	{ $update = $objPgm->_updateGenreStatus($genreList,$userid); }


echo "success";



?>
