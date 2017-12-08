<?php

/********************************************************************** */

/*   Project Name	::> Jiwok 

/*   Module 		::> Client Side-General Session management

/*   Programmer		::> Ajith 

/*   Date			::> 09-02-2009

   

/*   DESCRIPTION::::>>>>

/*  This  code used to Manage the general session Variables .

/************************************************************************/

include_once('includes/classes/class.Languages.php');

$generalObj 	= new General(); 

$settingsObj	= new Settings ();

	

/*$pattern = "/(\/)(en)(\/)/";

$string = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];



$mat	=	preg_match($pattern,$string,$val);

if($mat == 1)

	$_SESSION['language']['langId'] = 1;

else

	$_SESSION['language']['langId'] = 2;





/*________________________DYNAMIC ASSIGNMENT FOR LANGUAGE ________________*/

/* ** Code is used to identify the request lang dynamicaly and also reassign 

	if(!isset($_SESSION['language']['langId'])){

		 $langId	=	2;

		 $settingsObj->_register();	

		 $settingsObj->_registerLang($langId);

}

else

{

        $langId	=	$_SESSION['language']['langId'];

		 $settingsObj->_register();	

		 $settingsObj->_registerLang($langId);

}

*/

$checkdomain	= 	explode(".", $_SERVER['HTTP_HOST']);

$domain 		= 	$checkdomain[0];





$ip			=	$_SERVER['REMOTE_ADDR'];

//$ip="59.93.39.43";

//$ip="19.68.79.120";

//uncomment for ipdetection rule

//$frenchArray	=	array("BE","SE","LU","MA","TN","DZ","GP","MQ","RE","GY","NC","FR");//french version countries



if(strtolower($domain)	==	'www'){//If jiwok.com then both languages should be available pl,en and fr



		$pattern 		= "/(\/)(en|es|pl)(\/)/";

		$patternEn 	= "/(\/)(en)(\/)/";

		$patternEs 		= "/(\/)(es)(\/)/";
		$patternPl 		= "/(\/)(pl)(\/)/";

		$string 			= (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : 

								"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

						

		$mat				=	preg_match($pattern,$string,$val);//Matching if en/ or esp/ is in URL

		$matchEn		=	preg_match($patternEn,$string,$val);//Matching if en/ is in URL

		$matchEs		=	preg_match($patternEs,$string,$val);//Matching if esp/ is in URL
		
		$matchPl		=	preg_match($patternPl,$string,$val);//Matching if pl/ is in URL

		

		if($matchEn	== 1)	$_SESSION['language']['langId'] = 1;//If Match then lang = en 

		if($matchEs 	== 1)	$_SESSION['language']['langId'] = 3;//If Match then lang = esp 
		
		if($matchPl 	== 1)	$_SESSION['language']['langId'] = 5;//If Match then lang = esp 

		/*if($_REQUEST["test_reubro"]){

			echo $_SESSION['language']['langId'];

		}*/

		if(!$_SESSION['language']['langId']){//If lang not set

				//$runningCountry	=	getIPCountry($ip);

				//////////for 404 rule automatic version name

				/*

				uncomment for ipdetection rule

				if(trim(end(explode("/",$_SERVER["SCRIPT_FILENAME"])))=="search.php")

					{

						if($runningCountry["country_code"])

							{

								if(in_array(trim($runningCountry["country_code"]),$frenchArray))

									{

										$_SESSION['language']['langId']	=	2;

										$url_redirect_Rule	=	"http://www.jiwok.com/entertainement";

									}

								else

									{

										$_SESSION['language']['langId']	=	1;

										$url_redirect_Rule	=	"http://www.jiwok.com/en/training";

									}

							}

					}

				else

					{

						if($runningCountry["country_code"])

							{

								if(in_array(trim($runningCountry["country_code"]),$frenchArray))

									{

										$_SESSION['language']['langId']	=	2;

									}

								else

									{

										$_SESSION['language']['langId']	=	1;

									}

							}

					}

				if($url_redirect_Rule)

					{

						$settingsObj->_register();	

						$settingsObj->_registerLang($_SESSION['language']['langId']);

						header("location:$url_redirect_Rule");

						exit;

					}*/

					//comment this for ipdetection rule

					$_SESSION['language']['langId']=2;//Set Language to fr

					

		}

		else if($_SESSION['language']['langId']!='2' && $mat!='1'){//If lang not En/esp and not set

		

			 	$_SESSION['language']['langId']	=	2;//Default Lang as French

		

		}

		if($_SESSION['language']['langId'] == ''){//If lang not Set

		

				$_SESSION['language']['langId']	=	2;//Default Lang as French

		

		}

		$settingsObj->_register();	

		$settingsObj->_registerLang($_SESSION['language']['langId']);

	}

	

	$checkDomain	=	array("parismarathon","semideparis","domyos","nabaiji");//Check array for brands

	

	if(in_array(strtolower($domain),$checkDomain)){//Check If brands parismarathon or semideparis
	

		$pattern 		= "/(\/)(en)(\/)/";

		$patternEs 		= "/(\/)(es)(\/)/";
		
		

		$string 	= (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : 

						"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

						

		$mat				=	preg_match($pattern,$string,$val);//Matching if en/  is in URL

		$matchEs		=	preg_match($patternEs,$string,$val);//Matching if esp/ is in URL

		//if($mat 			== 1)	$_SESSION['language']['langId'] = 1;//If Match then lang = en 


		if($mat == 1)	$_SESSION['language']['langId'] = 1;//If Match then lang = en 

		if($matchEs 	== 1)	$_SESSION['language']['langId'] = 3;//If Match then lang = esp 
		

		if(!$_SESSION['language']['langId']){//If lang not set

		

			$_SESSION['language']['langId']=2;//Set Language to fr

		

		}

		else if($_SESSION['language']['langId']!='2' && $mat!='1' && $matchEs!='1'){//If lang not Fr and not set

			 	

			$_SESSION['language']['langId']	=	2;//Set Language to fr

		

		}

		if($_SESSION['language']['langId'] == ''){//If lang not Set

		

				$_SESSION['language']['langId']	=	2;//Default Lang as French

		

		}
		
		$settingsObj->_register();	

		$settingsObj->_registerLang($_SESSION['language']['langId']);

	}

	else{

		if(strtolower($domain)	!=	'www'){	

		

			$pattern 		= "/(\/)(en|es)(\/)/";

			$patternEn 	= "/(\/)(en)(\/)/";

			$patternEs 		= "/(\/)(es)(\/)/";
			$patternPl 		= "/(\/)(pl)(\/)/";

			$string 			= (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : 

									"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

							

			$mat				=	preg_match($pattern,$string,$val);//Matching if en/ or esp/ is in URL

			$matchEn		=	preg_match($patternEn,$string,$val);//Matching if en/ is in URL

			$matchEs		=	preg_match($patternEs,$string,$val);//Matching if esp/ is in URL
			$matchPl		=	preg_match($patternPl,$string,$val);//Matching if pl/ is in URL

		

		if($matchEn	== 1)	$_SESSION['language']['langId'] = 1;//If Match then lang = en 

		if($matchEs 	== 1)	$_SESSION['language']['langId'] = 3;//If Match then lang = esp 
		
		if($matchPl 	== 1)	$_SESSION['language']['langId'] = 5;//If Match then lang = esp 

if($_SESSION['language']['langId'] == ''){//If lang not Set

		

				$_SESSION['language']['langId']	=	2;//Default Lang as French

		

		}

			$settingsObj->_register();	

			$settingsObj->_registerLang($_SESSION['language']['langId']);

		}

	}

	/*if($_SERVER['HTTP_HOST']=='192.168.0.8'){

	    $pattern 	= "/(\/)(en)(\/)/";

		$string 	= (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : 

						"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		$mat		=	preg_match($pattern,$string,$val);

		if($mat == 1)	$_SESSION['language']['langId'] = 1; else $_SESSION['language']['langId'] = 2;

	    $settingsObj->_register();	

		$settingsObj->_registerLang($_SESSION['language']['langId']);

	}*/

	



	function getIPCountry($ip){

			$query	=	"SELECT * FROM ip_to_country_all 

						WHERE 	ip_n_from <= inet_aton('$ip') 

						AND 	ip_n_to >= inet_aton('$ip')";

			$result = $GLOBALS['db']->getRow($query, DB_FETCHMODE_ASSOC);

			return $result;

	}



/*________________________MANUAL ASSIGNMENT FOR LANGUAGE ________________*/



	if($_REQUEST['langChange']){

		$langId	=	$_REQUEST['langChange'];

		$settingsObj->_register();	

		$settingsObj->_registerLang($langId);

	}





/*________________________ASSIGNMENT THE SESSION VALUES FOR PAGE VARIABLES_______________*/



	$xmlPath				=	$_SESSION['language']['xml'];//xml file path for the different language

	

	

	$lanId					=	$_SESSION['language']['langId'];

	

	$brandUrl	=	array("parismarathon.jiwok.com","semideparis.jiwok.com");



	if($_SERVER['HTTP_HOST'] == "www.jiwok.com"){

		$urlvar	=	"http://".$_SERVER['HTTP_HOST']."/";

	}

	if($_SERVER['HTTP_HOST'] == "en.jiwok.com"){

		$urlvar	=	"http://".$_SERVER['HTTP_HOST']."/";

	}

	if($_SERVER['HTTP_HOST'] == "parismarathon.jiwok.com"){

		$urlvar	=	"http://".$_SERVER['HTTP_HOST']."/";

	}

	if($_SERVER['HTTP_HOST'] == "semideparis.jiwok.com"){

		$urlvar	=	"http://".$_SERVER['HTTP_HOST']."/";

	}
	if($_SERVER['HTTP_HOST'] == "domyos.jiwok.com"){

		$urlvar	=	"http://".$_SERVER['HTTP_HOST']."/";

	}
	if($_SERVER['HTTP_HOST'] == "nabaiji.jiwok.com"){

		$urlvar	=	"http://".$_SERVER['HTTP_HOST']."/";

	}
	if($_SERVER['HTTP_HOST'] == "http://beta.jiwok.com/"){

		$urlvar	=	"http://".$_SERVER['HTTP_HOST']."/";

	}

	if($_SERVER['HTTP_HOST'] == "10.0.0.8"){

		$urlvar	=	 "http://".$_SERVER['HTTP_HOST'].	"/jiwokv3/";

	}



		$pattern 		= "/(\/)(en|es|pl)(\/)/";

		$patternEn 	= "/(\/)(en)(\/)/";

		$patternEs	 	= "/(\/)(es)(\/)/";
		
		$patternPl	 	= "/(\/)(pl)(\/)/";

		$string 	= (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : 

						"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

						

		$mat				=	preg_match($pattern,$string,$val);//Matching if en/ or esp/ is in URL

		$matchEn		=	preg_match($patternEn,$string,$val);//Matching if en/ is in URL

		$matchEs		=	preg_match($patternEs,$string,$val);//Matching if esp/ is in URL
		
		$matchPl		=	preg_match($patternPl,$string,$val);//Matching if esp/ is in URL

	
	//echo $lanId;
	if($lanId != 2){//Language check
		
		$lanVar	=	"es";

		if($lanId	==	1)
			$lanVar	=	"en";
		elseif($lanId	==	5)
			$lanVar	=	"pl";
		$bb	=	preg_match($pattern,$string,$val);

		

			

		if($bb != 1)

			{		
			
				

				if($_SERVER['HTTP_HOST'] == "www.jiwok.com")

					{

						$pagnm	=	$_SERVER['REQUEST_URI'];

						define("ROOT_JWPATH", $urlvar.$lanVar."/"); //this needs to be changed to '/' when taken live

						/*header("location:".$urlvar.$lanVar.$pagnm); 

							exit;*/

						

						$ua = $_SERVER['HTTP_USER_AGENT'];

						$bots = array('GoogleBot', 'Microsoft', 'inktomi', 'yahoo', 'slurp');						

						$isbot = false;

						foreach($bots as $b){

							if(stristr($ua, $b)){

								$isbot = true;

								break;								

							}

						}

						if($isbot === false){
							
							header("location:".$urlvar.$lanVar.$pagnm); 

							exit;

						}					

						

					}
					else if($_SERVER['HTTP_HOST'] == "beta.jiwok.com/")
					{
						

						$pagnm	=	$_SERVER['REQUEST_URI'];

						define("ROOT_JWPATH", $urlvar.$lanVar."/"); //this needs to be changed to '/' when taken live

						/*header("location:".$urlvar.$lanVar.$pagnm); 

							exit;*/

						

						$ua = $_SERVER['HTTP_USER_AGENT'];

						$bots = array('GoogleBot', 'Microsoft', 'inktomi', 'yahoo', 'slurp');						

						$isbot = false;

						foreach($bots as $b){

							if(stristr($ua, $b)){

								$isbot = true;

								break;								

							}

						}

						if($isbot === false){
							
							header("location:".$urlvar.$lanVar.$pagnm); 

							exit;

						}					

						
					}
				else if($_SERVER['HTTP_HOST'] == "en.jiwok.com")

					{

						$pagnm	=	$_SERVER['REQUEST_URI'];

						define("ROOT_JWPATH", $urlvar.$lanVar."/"); //this needs to be changed to '/' when taken live

						/*header("location:".$urlvar.$lanVar.$pagnm); 

							exit;*/

						

						$ua = $_SERVER['HTTP_USER_AGENT'];

						$bots = array('GoogleBot', 'Microsoft', 'inktomi', 'yahoo', 'slurp');						

						$isbot = false;

						foreach($bots as $b){

							if(stristr($ua, $b)){

								$isbot = true;

								break;								

							}

						}

						if($isbot === false){
							
							header("location:".$urlvar.$lanVar.$pagnm); 

							exit;

						}					

						

					}

				else if($_SERVER['HTTP_HOST']	==	'10.0.0.8'){

						$pagnm	=	explode("jiwokv3",$_SERVER['REQUEST_URI']);

						define("ROOT_JWPATH", $urlvar.$lanVar."/"); //this needs to be changed to '/' when taken live

						/*header("location:".$urlvar.$lanVar.$pagnm[1]); 

							exit;*/

						$ua = $_SERVER['HTTP_USER_AGENT'];

						$bots = array('GoogleBot', 'Microsoft', 'inktomi', 'yahoo', 'slurp');						

						

						$isbot = false;

						foreach($bots as $b){

							if(stristr($ua, $b)){

								$isbot = true;

								break;								

							}

						}

						if($isbot === false){

							header("location:".$urlvar.$lanVar.$pagnm[1]); 

							exit;

						}	

						

				}

				else if($_SERVER['HTTP_HOST'] == "parismarathon.jiwok.com"){

						$pagnm	=	$_SERVER['REQUEST_URI'];

						define("ROOT_JWPATH", $urlvar."en/"); //this needs to be changed to '/' when taken live

						header("location:".$urlvar."en".$pagnm); 

						exit;

				}

				else if($_SERVER['HTTP_HOST'] == "semideparis.jiwok.com"){

						$pagnm	=	$_SERVER['REQUEST_URI'];

						define("ROOT_JWPATH", $urlvar."en/"); //this needs to be changed to '/' when taken live

						header("location:".$urlvar."en".$pagnm); 

						exit;

				}
				else if($_SERVER['HTTP_HOST'] == "nabaiji.jiwok.com"){

						$pagnm	=	$_SERVER['REQUEST_URI'];

						define("ROOT_JWPATH", $urlvar."es/"); //this needs to be changed to '/' when taken live

						header("location:".$urlvar."es".$pagnm); 

						exit;

				}
				else if($_SERVER['HTTP_HOST'] == "domyos.jiwok.com"){
				
				
						$pagnm	=	$_SERVER['REQUEST_URI'];

						define("ROOT_JWPATH", $urlvar."es/"); //this needs to be changed to '/' when taken live

						header("location:".$urlvar."es".$pagnm); 

						exit;

				}

			}

		else{

			define("ROOT_JWPATH", $urlvar.$lanVar."/");

		}

		

	}

	else{

	//echo "nnn";exit;

		$bb	=	preg_match($pattern, $string,$val);

		if($bb)

			{

				if($_SERVER['HTTP_HOST'] == "www.jiwok.com"){

						$urlReplace	=	preg_replace('/\/(en|es|pl)\//', '/', $_SERVER['REQUEST_URI']);

						$pagnm		=	$_SERVER['REQUEST_URI'];		 

						//$pagnm2	=	explode("en/",$pagnm);

						$urlsl			=	 $urlReplace; 

						//header("location:".$urlsl);exit;

						$ua = $_SERVER['HTTP_USER_AGENT'];

						$bots = array('GoogleBot', 'Microsoft', 'inktomi', 'yahoo', 'slurp');

						$isbot = false;

						foreach($bots as $b){

							if(stristr($ua, $b)){

								$isbot = true;

								break;

							}

						}

						if($isbot === false){

							header("location:".$urlsl);exit;

						}						

				}
				else if($_SERVER['HTTP_HOST'] == "beta.jiwok.com"){
					

						$urlReplace	=	preg_replace('/\/(en|es|pl)\//', '/', $_SERVER['REQUEST_URI']);

						$pagnm		=	$_SERVER['REQUEST_URI'];		 

						//$pagnm2	=	explode("en/",$pagnm);

						$urlsl			=	 $urlReplace; 

						//header("location:".$urlsl);exit;

						$ua = $_SERVER['HTTP_USER_AGENT'];

						$bots = array('GoogleBot', 'Microsoft', 'inktomi', 'yahoo', 'slurp');

						$isbot = false;

						foreach($bots as $b){

							if(stristr($ua, $b)){

								$isbot = true;

								break;

							}

						}

						if($isbot === false){

							header("location:".$urlsl);exit;

						}						

				
					
				}

				else if($_SERVER['HTTP_HOST']	==	'10.0.0.8'){

						$urlReplace	=	preg_replace('/\/(en|es)\//', '/', $_SERVER['REQUEST_URI']);

						$pagnm	=	explode("jiwok",$_SERVER['REQUEST_URI']);	

						$pagnm2	=preg_replace('/\/(en|es)\//', '', $pagnm[1]);	 

						//$pagnm2	=	explode("en/",$pagnm[1]);

						$urlsl	=	 $urlvar.$pagnm2; 

						$ua = $_SERVER['HTTP_USER_AGENT'];

						$bots = array('GoogleBot', 'Microsoft', 'inktomi', 'yahoo', 'slurp');

						$isbot = false;

						foreach($bots as $b){

							if(stristr($ua, $b)){

								$isbot = true;

								break;

							}

						}

						if($isbot === false){

							header("location:".$urlReplace);exit;

						}		

						

				}

				else if($_SERVER['HTTP_HOST'] == "parismarathon.jiwok.com"){

						$pagnm	=	$_SERVER['REQUEST_URI'];		 

						$pagnm2	=	explode("en/",$pagnm);

						$urlsl	=	 $urlvar.$pagnm2[1]; 

						header("location:".$urlsl);exit;

				}

				else if($_SERVER['HTTP_HOST'] == "semideparis.jiwok.com"){

						$pagnm	=	$_SERVER['REQUEST_URI'];		 

						$pagnm2	=	explode("en/",$pagnm);

						$urlsl	=	 $urlvar.$pagnm2[1]; 

						header("location:".$urlsl);exit;

				}
					else if($_SERVER['HTTP_HOST'] == "domyos.jiwok.com"){

						$pagnm	=	$_SERVER['REQUEST_URI'];		 
						
						$pagnm2	=	explode("es/",$pagnm);

						$urlsl	=	 $urlvar.$pagnm2[1];
						header("location:".$urlsl);exit;
						

				}
					else if($_SERVER['HTTP_HOST'] == "nabaiji.jiwok.com"){

						$pagnm	=	$_SERVER['REQUEST_URI'];		 

						$pagnm2	=	explode("es/",$pagnm);

						$urlsl	=	 $urlvar.$pagnm2[1]; 

						header("location:".$urlsl);exit;

				}

			}

		 define("ROOT_JWPATH",$urlvar); //this needs to be changed to '/' when taken live

	}
	$cmnRootPath=ROOT_JWPATH;
	
	$_SESSION["pdfBrand"]	=	$domain;
	
	//Ticket Variables ---------------------
	
	$ticketLanId	=	$_SESSION['language']['langId'];
	$ticketBrandName	=	"";
	
	if($_SESSION['user']['userId']!=""){
		$ticketUserId	=	$_SESSION['user']['userId'];
	}else{
		$ticketUserId	=	0;
	}
	if($_SESSION["ticketTest"]==true){
		$ticketLanId	=	5;
		$_SESSION['language']['langId']	=	5;
	}
	if($_SESSION['user']['userId']==64602){
		$ticketLanId	=	5;
		$_SESSION['language']['langId']	=	5;
	}
	//echo "1=".$_SESSION['language']['langId'];
	//print_r($_SESSION['language']);

?>
