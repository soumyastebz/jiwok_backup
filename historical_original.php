<?php
session_start();

ob_start();

include_once('includeconfig.php');
include_once("includes/classes/class.programs.php");

if($_SESSION['language']['langId']=="") { $lanId=1;  } else { $lanId = $_SESSION['language']['langId']; }

$_SESSION['folder'] = 2;

$flag = 0;
$errorMsg = '';	 
$userid = $_SESSION['user']['userId'];	
$objGen     	= new General();
if($tz	=	$objGen->getTomeZonePHP($userid))	date_default_timezone_set($tz);

$objPgm     	= new Programs($lanId);
$parObj 		= new Contents('historical.php');

$headingData	= $parObj->_getTagcontents($xmlPath,'trainingprogram','pageHeading');
$returnData		= $parObj->_getTagcontents($xmlPath,'trainingprogram','label');
$arrayData		= $returnData['general'];

$todayDate =date('Y-m-d');
$error = "";
$today = date('Y-m-d');

$redirect_url = base64_encode('historical.php');

if(!($objPgm->_checkLogin())){
	header('location:login_failed.php?returnUrl='.$redirect_url);
}

if(trim($_REQUEST['ccess']) != ""){
  //Dileep  
  $_REQUEST['workoutFlexId'] = str_replace(' ','+',trim($_REQUEST['workoutFlexId']));
  //$work_flex = explode('@',trim($_REQUEST['workoutFlexId']));  
  //$_REQUEST['workoutFlexId'] = base64_decode(trim($work_flex[0]))."@".$work_flex[1];
  //Dileep
  $work_Flex 	= trim($_REQUEST['workoutFlexId']);
  $workoutF 	= explode('@',trim($_REQUEST['workoutFlexId']));
  $workFlex 	= $workoutF[0]."@@".$workoutF[1];
  $_REQUEST['workoutFlexId'] = $workoutF[0];
  $work_flex 	= $workoutF[0];
  $workoutDatesArray = $objPgm->_getTrainingCalWorkoutDates($userid);
  $pgmUser 	= $objPgm->_getUserTrainingProgram($userid);
  $flexid 	= stripslashes(trim($pgmUser['flex_id']));
  $pgm_id 	= stripslashes(trim($pgmUser['program_id']));
  $workoutOrderArray = $objPgm->_getWorkoutOrders($flexid);
  $newWorkFlex = $workoutF[0]."@@".($workoutF[1]-1);
  $dat = $workoutDatesArray[$newWorkFlex];
  $mon = date('n',strtotime($dat));
  $yr = date('Y',strtotime($dat));
  $day = date('j',strtotime($dat));
  $workoutOrder_nav =$workoutOrderArray[$workoutF[1]-1]; 
  if($dat<$todayDate)
	  $a = 'a';
  elseif($dat==$todayDate)
     $a = 'b';
  elseif($dat>$todayDate)
     $a = 'c';	 
}


if(isset($_POST['update']) && trim($_REQUEST['feedback_id'])!=""){
	 $desc = trim($_REQUEST['comment_text2']);
	 $feedback_id = trim($_REQUEST['feedback_id']);
	 $res = $objPgm->_updateFeedback("feedback",$feedback_id,$desc);
	 $workOrder = trim($_REQUEST['workOrder']);
	 $workDate = trim($_REQUEST['workDate']);
	
	 $workout_flexid_cal = str_replace(' ','+',trim($_REQUEST['workout_flexid_cal']));
	 
	 $commantTxt	=	$_REQUEST['comment_text2'];
	 $_SESSION["refComment"]	=	base64_encode($commantTxt);
	 
	 if($_REQUEST["postFB"]==1){
		$_SESSION["refCommentPost"]	=	"1";
	}else{
		$_SESSION["refCommentPost"]	=	"0";
	}
	 
	 
     $msg = $parObj->_getLabenames($arrayData,'msgupcomment','name');
	
	 header("Location:historical.php?action=commented&pgm_id=".base64_encode(trim($_REQUEST['program_id']))."&workoutFlexId=".$workout_flexid_cal."&ccess=Y29tc2Vzcw==&msg=".$msg);

}

if(isset($_POST['add'])){
	$workOrder = trim($_REQUEST['workOrder']);
	$workDate = trim($_REQUEST['workDate']);
	
	$workout_flexid_cal = str_replace(' ','+',trim($_REQUEST['workout_flexid_cal']));
	
	$insArray = array();
	$insArray['feedback_id']			= '';
    $insArray['feedback_subject']		= '';
	$insArray['feedback_desc'] 		= addslashes(trim($_REQUEST['comment_text1']));
	$insArray['feedback_datetime'] 	= date('Y-m-d H:i:s');
	$insArray['program_id'] 			= addslashes(trim($_REQUEST['program_id']));
	
	$wrkFlx  = str_replace(' ','+',trim($_REQUEST['workout_flexid']));
	
	$insArray['workout_flex_id'] 		= addslashes(trim($wrkFlx));
	$insArray['user_id'] 				= $userid;
	$insArray['public_status'] 		= '2';
	$insArray['lang_id'] 		= $_REQUEST['lang_cid'];
	
	$commantTxt	=	$_REQUEST['comment_text1'];
	$_SESSION["refComment"]	=	base64_encode($commantTxt);
	
	if($_REQUEST["postFB"]==1){
		$_SESSION["refCommentPost"]	=	"1";
	}else{
		$_SESSION["refCommentPost"]	=	"0";
	}
	
	$res = $objPgm->_insertDetails($insArray,"feedback");
	$msg = $parObj->_getLabenames($arrayData,'msgaddcomment','name');;
	
	header("Location:historical.php?action=commented&pgm_id=".base64_encode(trim($_REQUEST['program_id']))."&workoutFlexId=".$workout_flexid_cal."&ccess=Y29tc2Vzcw==&msg=".$msg);

}


$feedbacks = $objPgm->_getUserFeedbacks($userid);

?>

<!DOCTYPE HTML>
<html>
<head>
<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Jiwok</title>
<link rel="shortcut icon" type="image/ico" href="images/favicon.ico" />

<!-- Internet Explorer HTML5 enabling code: -->
<!--[if IE]>
           <script src="js/html5.js"></script>

<![endif]-->

<link href="resources/style.css" rel="stylesheet" type="text/css" />

		<link href="css/main.css" rel="stylesheet">

<!---------------------------->
</head>
<body>
<header>
  <div class="frame">
  <h1 class="logo">
     <a href="index.html"><img src="images/logo.png" alt="Jiwok" title="Jiwok"></a>
  </h1>
  <hgroup>
     <!--<input type="submit" value="LOGIN" class="login_btn">-->
     <a href="#" class="login_btn">LOGIN</a>
     <!--<span class="log"><input name="" type="checkbox" value=""> Se souvenir de moi | <a href="#">Mot de passe oublié ?</a> | </span>
     <span class="lang">
         <a href="#"><img src="images/FR.png" alt="Frunch" title="Frunch"></a>
         <a href="#"><img src="images/us.png" alt="US" title="English"></a>
         <a href="#"><img src="images/german.png" alt="German" title="Polish"></a>
     </span>
     <ul class="login">
         <li class="fb"><a href="#"><img src="images/fb-connect.png" alt="FB connect" title="Login with Facebook"></a></li>
         <li><input type="text" value=""></li>
         <li><input type="password"></li>
         <li><input type="submit" value="GO"></li>
     </ul>-->
  </hgroup>
  </div>
</header>

    
    
    <div class="top-nav">
        <nav class="nav">    
					<ul class="nav-list">
						<li class="nav-item"><a href="?=home">ACCUEIL</a></li>
						<li class="nav-item"><a href="?=home">VOTRE ENTRAINEMENT SUR MESURE</a></li>
                        <li class="nav-item"><a href="?=home">LES COACHS</a></li>
                        <li class="nav-item"><a href="?=home">BLOG</a></li>
                        <li class="nav-item"><a href="?=home">FORUM</a></li>
                        <li class="nav-item"><a href="?=home">CONTACT</a></li>
                        <li class="nav-item"><a href="?=home">AIDE</a></li>
					</ul>
				</nav>
     </div>
    <div class="frame_inner">
    <div class="row-1"><div class="return">
            <a href="<?=$backButtonLink?>"><?=$parObj->_getLabenames($arrayData,'newBckTxt','name');?></a>
         </div>
      
         <div class="title">
           <h3>Les bons cadeaux Jiwok </h3>
           iwok s'est associé à de nombreux partenaires afin de vous offrir le meilleur du meilleur.
         </div>
         
         </div>
       <div class="clear"></div>
       
       <div class="panel-left">
          <section class="calender">
             <h3>mon calendrier</h3>
             space for calander plug
          </section>
          <section class="comments">
             <h3>  <?=$parObj->_getLabenames($arrayData,'newMyCmtTxt','name');?> <?=$parObj->_getLabenames($arrayData,'newMyCmtEgTxt','name');?> </h3>
     <textarea name="" cols="" rows=""></textarea>
          </section>
          <div align="right"><a href="#" class="btn-orng">VALIDER</a></div>
       </div>
       <div class="panel-rite">
           <section class="session">
             <div class="title">Brûler des calories et perdre du poids en natation, 2 séances par semaine pendant 10 semaines.</div>
             <div class="content">
                  <div class="row">
                      <figure>
                        <img src="images/corner-blu.png" class="corner">
                      <img src="images/swim.jpg" alt="swim"></figure>
                      <article>
                         <h3>Séance 12 I 24 Juin</h3>
                         <div class="text">La séance consiste en 4 fois :2 minutes de nage en crawl, brasse ou dos à une intensité modérée,puis, 3 minutes de jambes en crawl avec planche sans palmes à une intensité moyenne, puis, 3 minutes de nage à intensité moyenne, principalement en crawl, enfin, 2 minutes de repos passif au </div>
                      </article>
                  </div>
                  
                  <div class="clear"></div>
                  <p>Les conseils du coach pour cette séance Veillez à vous équiper d'une bonne paire de lunettes, bien étanche. Respectez bien les consignes d'allures, de manière à bien rester concentré(e) sur votre technique gestuelle ainsi que sur les conseils donnés. Si vous n'êtes pas arrivé(e) au mur à la fin de l'exercice, continuez tranquillement pour rejoindre le mur avant le début de la séquence de travail suivante. 
</p>
<p>Si votre dernier repas remonte à plus de trois heures, essayez de manger une barre de céréales 45 minutes avant votre séance.Bon courage </p>
             </div>
           </section>
       </div>
     </div>
     
     
     
     
     <div class="foot-nav frame">
                                                                                                                                                              
        <ul class="nav_03">
          <li><a href="#">aide</a></li>
          <li><a href="#">plan du site</a></li>
          <li><a href="#">qui sommes nous?</a></li>
          <li><a href="#">termes et conditions</a></li>
        </ul>
     </div>
     <footer>
          <div class="frame">
             <nav class="col-01">
                <a class="logo" href="#"><img src="images/logo-footer.png" alt="Jiwok"></a>
                <ul class="footnav_01">
                   <li><a href="#">LES TÉMOIGNAGES</a></li>
                   <li><a href="#">LA PRESSE</a></li>
                   <li><a href="#">LES PASS JIWOK</a></li>
                </ul>
                <a class="find" href="#">RETROUVEZ NOUS<br> 
SUR GOOGLE +</a>
             </nav>
              <nav class="col-02">
                <h2><span>CE QUE JIWOK VOUS APPORTE</span></h2>
                    <ul class="footnav_02">
                      <li><a href="#">Courir plus vite</a></li>
                      <li><a href="#">Améliorer sa VMA</a></li>
                      <li><a href="#">Débuter en course à pied</a></li>
                    </ul>
                    <h2><span>LES COACHS</span></h2>
              </nav>
              <nav class="col-03">
                <h2><span>VOTRE ENTRAÎNEMENT SUR MESURE</span></h2>
                <div class="clear"></div>
                <div class="colums">
                <ul class="footnav_02">
                      <li><a href="#">Carte cadeau sport running Courir plus</a></li>
                      <li><a href="#">vite et améliorer sa vma</a></li>
                      <li><a href="#">débuter en course à pied</a></li>
                      <li><a href="#">Entrainement la parisienne</a></li>
                      <li><a href="#">Entrainement Marathon Paris</a></li>
                      <li><a href="#">Entrainement marche</a></li>
                      <li><a href="#">Entrainement marche nordique</a></li>
                      <li><a href="#">Entrainement marche sur tapis</a></li>
                      <li><a href="#">Entrainement marche sur tapis roulant</a></li>
                      <li><a href="#">Entrainement Mud Day - Spartan -</a></li>
                      <li><a href="#">Fappading - Course d’obstacles</a></li>
                      <li><a href="#">Entrainement Natation</a></li>
                    </ul>
                     <ul class="footnav_02">
                      <li><a href="#">Entrainement tapis de course</a></li>
                      <li><a href="#">Entrainement tapis roulant</a></li>
                      <li><a href="#">Entrainement triathlon</a></li>
                      <li><a href="#">Entrainement Ultra Trail</a></li>
                      <li><a href="#">Entrainement velo appartement</a></li>
                    </ul>
                   </div>
                   
                   <div class="colums">
                <ul class="footnav_02">
                      <li><a href="#">Entrainement vélo d’appartement</a></li>
                      <li><a href="#">Entrainement velo elliptique</a></li>
                      <li><a href="#">Entrainement velo interieur</a></li>
                      <li><a href="#">Perdre du poids 10 kg</a></li>
                      <li><a href="#">Perdre du poids 5 kg</a></li>
                      <li><a href="#">Plan entrainement 10 km</a></li>
                    </ul>
                    
                     <ul class="footnav_02">
                      <li><a href="#">Plan entrainement 20 km Paris</a></li>
                      <li><a href="#">Plan entrainement marathon new york</a></li>
                      <li><a href="#">Plan entrainement semi-marathon</a></li>
                      <li><a href="#">Plan Entrainement Trail</a></li>
                      <li><a href="#">Preparer le test de coope</a></li>
                    </ul>
                   </div>
              </nav>  
          </div>
     </footer>
     <div class="block_foot">
        <h4>Débuter le jogging, running, course à pied, vélo appartement, elliptique, marche, trail</h4>
    
<p>Si vous désirez commencer à courir, faire du sport, débuter elliptique ou vous remettre au sport, Jiwok est le service qu'il vous faut !
Tous les débutant pourront ainsi rapidement progresser.
Vous serez coacher et vous pourrez progresser afin de rester en forme, retouver votre ligne et perdre du poids ( de 3 à 10 kilos).</p>

<h4>Plan marathon, semi marathon, 10 km, trail</h4>
<p>Jiwok propose également des plan d'entrainement marathon, semi marathon, 10 km et trail pour les débutants et les confirmées. Des objectifs de 1 h 30 à 4 h 30 afin de vous permettre de progresser, de courir plus et d'amioler votre temps de couse
Vous pourrez également améliorer votre vma.</p>
<h4>Perdre du poids en faisant du sport</h4>
<p>Avec les conseils de votre coach jiwok, vous pourrez perdre du poids en courant, nageant ou pedélant grâce au jogging, running, course à pied, marche, tapis de course, vélo d'appartement, elliptique et natation
Retrouver la forme rapidement et progressivement avec des séances de sports adaptés à votre niveau physique</p>
<h4>courir et faire du sport en musique</h4>
<p>Suivez les séances et les playlist Jiwok pour courir en musique. Les musiques selectionnés par jiwok vous permettront de courir, jogging, running avec des morceaux de musiques adaptés au sport</p>
<h4>application ihpone et android sport</h4>
<p>Installez notre application Iphone et Android pour suivre les séances de coaching Jiwok : séance running, jogging, course à pied, natation, marche, elliptique, vélo, tapis de course, marathon, semi marathon, 10 km, trail, débutant en sport, perte de poids</p>
 </div>
  
  <ul class="foot_links">

   <li><a href="http://www.jiwok.com/about-us">Qui sommes-nous?</a></li>

   <li>|</li>

   <li> <a href="http://www.jiwok.com/sitemap.php">Plan du site</a></li>

   <li>|</li>

   <li> <a href="http://www.jiwok.com/contact-us">Contact</a></li>

  
   <li>|</li>

   <li> <a href="http://www.jiwok.com/terms-and-conditions">Termes et conditions</a></li>

   <li>|</li>

   <li> <a href="http://www.jiwok.com/press">Presse</a></li>

   <li>|</li>

   <li> <a href="http://www.jiwok.com/jobs">Job</a></li>

   <li>|</li>

    

   <li> <a href="http://www.jiwok.com/partners">Partenaires</a></li>

   <li>|</li>
   <li> <a href="http://www.jiwok.com/faq">Aide</a></li>
   </ul>  
    
   <p class="copyright">Copyrights JIWOK 2015  |  powered by Reubro International Debugging</p> 
     </div>
     <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
     <script src="js/flaunt.js"></script>
	<script type='text/javascript' src="<?=ROOT_FOLDER?>includes/js/training_calendar_historical.js"></script>
	
		<!-- Demo Analytics -->
		<script>
			var _gaq=[['_setAccount','UA-20440416-10'],['_trackPageview']];
			(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
			g.src='//www.google-analytics.com/ga.js';
			s.parentNode.insertBefore(g,s)})(document,'script');
		</script>
</body>
</html>

