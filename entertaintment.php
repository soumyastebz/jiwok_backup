<!DOCTYPE HTML>
<html>
<head>
<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Jiwok</title>
<!-- Internet Explorer HTML5 enabling code: -->
<!--[if IE]>
           <script src="js/html5.js"></script>

<![endif]-->

<link href="resources/style.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" type="image/ico" href="images/favicon.ico" />
<link href="css/main.css" rel="stylesheet">
<!---------------------------->
<script src="js/css3-mediaqueries.js"></script>
<script src="js/jquery.min.js"></script>
<script src="js/responsiveslides.min.js"></script>

<script>
    // You can also use "$(window).load(function() {"
    $(function () {

      // Slideshow 1
      $("#slider1").responsiveSlides({
        maxwidth: 800,
        speed: 800,
      });

      // Slideshow 2
      $("#slider2").responsiveSlides({
        auto: false,
        pager: true,
        speed: 300,
        maxwidth: 540
      });

      // Slideshow 3
      $("#slider3").responsiveSlides({
        auto: true,
        pager: true,
        nav: false,
        speed: 800,
        namespace: "callbacks",
        before: function () {
          $('.events').append("<li>before event fired.</li>");
        },
        after: function () {
          $('.events').append("<li>after event fired.</li>");
        }
      });

      // Slideshow 4
      $("#slider4").responsiveSlides({
        auto: true,
        pager: true,
        nav: false,
        speed: 500,
        namespace: "callbacks",
        before: function () {
          $('.events').append("<li>before event fired.</li>");
        },
        after: function () {
          $('.events').append("<li>after event fired.</li>");
        }
      });

    });
  </script>

<script>
equalheight = function(container){

var currentTallest = 0,
     currentRowStart = 0,
     rowDivs = new Array(),
     $el,
     topPosition = 0;
 $(container).each(function() {

   $el = $(this);
   $($el).height('auto')
   topPostion = $el.position().top;

   if (currentRowStart != topPostion) {
     for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
       rowDivs[currentDiv].height(currentTallest);
     }
     rowDivs.length = 0; // empty the array
     currentRowStart = topPostion;
     currentTallest = $el.height();
     rowDivs.push($el);
   } else {
     rowDivs.push($el);
     currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
  }
   for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
     rowDivs[currentDiv].height(currentTallest);
   }
 });
}

$(window).load(function() {
	
  equalheight('.height_equal');
});
$(window).resize(function(){
  equalheight('.height_equal');
});
</script>

<script>
	//paste this code under head tag or in a seperate js file.
	// Wait for window load
	$(window).load(function() {
		// Animate loader off screen
		$(".se-pre-con").fadeOut("slow");;
	});
</script>
</head>
<body>
<div class="se-pre-con"></div>
<header>
  <div class="frame">
  <h1 class="logo">
     <a href="index.html"><img src="images/logo.png" alt="Jiwok" title="Jiwok"></a>
  </h1>
  <hgroup>
     <a href="#" class="login_btn">LOGIN</a>
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
<div class="frame slider-first">
<div class="callbacks_container">
      <ul class="rslides" id="slider4">
        <li><img src="images/slide_05.jpg" alt="Slide 01"></li>
	    <li><img src="images/slide_06.jpg" alt="Slide 01"></li>
	    <li><img src="images/slide_03.jpg" alt="Slide 01"></li>
		<li><img src="images/slide_04.jpg" alt="Slide 01"></li>
      </ul>
    </div>
    
    
    <section class="entertain_grid">
       <nav  class="b_cumbs">
       <ul>
          <li>VOUS ÊTES ICI:</li>
          <li><a href="#">ACCUEIL</a></li> <li>&gt;</li>
          <li><a href="#">VOTRE NTRAÎNEMENT SUR MESURE</a></li> <li>&gt;</li>
          <li class="current-itom">PRÉPARER UN TRIATHLON AU FORMAT SPRINT OU COURTE DISTANCE</li>
       </ul>
    </nav>
    <p class="title_01">Préparer un triathlon 
de format sprint ou courte distance</p>
   <section class="chart">
       <div class="colums">
           <div>DURÉE </div>
           <div><span>8 semaines</span></div>
       </div>
       
       <div class="colums">
           <div>RYTHME</div>
           <div><span>3 fois / semaine</span></div>
       </div>
       
       <div class="colums">
           <div>NOMBRE DE S ÉANCE</div>
           <div><span>24</span></div>
       </div>
       
       <div class="colums">
           <div>NIVEAU</div>
           <div><span>POUR</span></div>
       </div>
       
       <div class="colums">
           <div>DURÉE </div>
           <div><img src="images/star.png" alt="rating">&nbsp;<img src="images/star.png" alt="rating">&nbsp;<img src="images/star.png" alt="rating">
                      &nbsp;<img src="images/star.png" alt="rating">&nbsp;<img src="images/star.png" alt="rating"></div>
       </div>
   
   </section>
       <nav>
          <a href="#" class="btn-return">RETOUR</a>
           <a href="#" class="btn-sign">S’INSCRIRE GRATUITEMENT</a>
       </nav>
       
    </section>
    </div>
    <section class="goal">
      <div class="frame">
         <p class="title">OBJECTIF</p>
         <p class="description">Optimiser ses compétences physiques pour réaliser un triathlon en compétition.</p>
      </div>
    </section>
    
    <section class="ent_description">
      <div class="frame">
         <p class="title">DESCRIPTION</p>
         <p class="description">Votre pratique sportive est irrégulière mais vous aimez nager, faire du vélo et courir. Votre 
objectif est de participer et terminer un triathlon. Pour cela, vous décidez d’être régulier(e) 
en vous inscrivant à un programme spécifique de préparation.</p><a href="#" class="read">LIRE LA SUITE</a>
      </div>
      
    </section>

    <section class="advice">
      <div class="frame">
         <p class="title">LES CONSEILS DU COACH</p>
         <p class="description">VTout d’abord, pour vos entrainements natation, prevoyez un lecteur MP3 aquatique. 
Munissez vous d’une planche, d’un pull boy et d’une paire de plaquettes, d’une bonne paire 
de lunettes et d’un bonnet.</p><a href="#" class="read">LIRE LA SUITE</a>
      </div>
    </section>
     
      <section class="seance_1 mid-wrapper">
         <div class="left height_equal"><img src="images/corner.png" alt="image" class="corner">
           <div class="title">SÉANCE 1</div>
           <figure><img src="images/img-jiwok_02.jpg" alt="image"></figure>
         </div>
        <article class="content height_equal">
            <span class="line"></span>
            <h3>Séance d’endurance fondamentale en aisance respiratoire.</h3>
            <p>Lors de cette séance, vous effectuerez :</p>
            <p>30 minutes courues autour de 70% de FCM en prenant du plaisir + 2 
minutes de marche de récupération.
Puis, 6 fois : 1 ligne droite de 15 secondes en courant vite (autour de 90% 
FCM) + 45 secondes de récupération en marchant.
Enfin, vous finirez par 2 minutes de marche.
Cette séance durera 40 minutes.</p>
<p>Lors de la réalisation de votre séance vous pourrez prendre une marge de 
+/- 3% par rapport au pourcentage de FCM indiqué.</p>

<h3>Les conseils du coach pour cette séance
</h3> <p> Séance facile avec quelques accélérations en fin de séance.
Ce ne sont pas des sprints, mais des accélérations progressives où vous 
chercherez à allonger votre foulée et maintenir un rythme assez soutenu
</p>
<div>
    <a href="#" class="btn lft">QU’EST-CE QUE LE FCM?</a>
    <a href="#" class="btn rit">SÉANCE SUIVANTE</a>
</div>
        </article>
      </section>
     
     
     
     

     
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
     
     <script src="js/flaunt.js"></script>
	
		<!-- Demo Analytics -->
		<script>
			var _gaq=[['_setAccount','UA-20440416-10'],['_trackPageview']];
			(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
			g.src='//www.google-analytics.com/ga.js';
			s.parentNode.insertBefore(g,s)})(document,'script');
		</script>
</body>
</html>
