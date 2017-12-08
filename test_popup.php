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
<link href="resources/style_dev.css" rel="stylesheet" type="text/css">
<link rel="shortcut icon" type="image/ico" href="images/favicon.ico" />

<!---------------------------->
<script src="js/css3-mediaqueries.js"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script src="js/responsiveslides.min.js"></script>
<script type="text/javascript" src="js/jquery.bpopup.min.js"></script>

<script>
	;(function($) {
        $(function() {
            $('.view-popup').bind('click', function(e) {
                e.preventDefault();
                $('.pop').bPopup({
                speed: 2000,
                transition: 'slideDown'
               });
           });
        });
    })(jQuery);
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
	
	 <div style="float:left;align:left"> <!---added inline styles--->
      <a href="#" class="login_btn">LOGIN</a>
     </div>
     <div style="float:right" class="choose-language">
      <ul>      
		  <li>
		   <img src="images/jiwok_language-icon.png" alt="select language" />
		   <h5 style="color:#fff;margin:0 0 0 10px;display:inline-block;">FR</h5><img class="drop-arrow" src="images/lang-dropdown-arrow.png" alt="select language" />
			<ul>
            <li><a href="setLang.php?langId=2&path=cHJlc3NfdGVzdGltb25pYWxfZGV0YWlscy5waHA=&req_name=&rq="><img src="images/jiwok_06.png" title="French" alt="French"  /><h5>FRENCH</h5></a></li>
		    <li><a href="http://en.jiwok.com/en/index_eng.php"><img src="images/jiwok_03.png" alt="English" title="English"/><h5>ENGLISH</h5></a></li>
            <li><a href="setLang.php?langId=5&path=cHJlc3NfdGVzdGltb25pYWxfZGV0YWlscy5waHA=&req_name=&rq="><img src="images/jiwok_09.png" title="Polish" alt="Polish" /><h5>POLISH</h5></a></li>	
		    </ul>
		  </li>
     </ul>
     </div>
      
  </hgroup>
  </div>
</header>
<div class="frame slider-first">
	
	
	
<div class="callbacks_container">
	
      <ul class="rslides" id="slider4">
        <li><img src="images/slide_01.jpg" alt="Slide 01">
          <div class="caption"><span>JIWOK.</span>
                                le bon coaching sur la bonne musique</div>
                                <a href="#" class="link">je m'inscris</a>
        </li>
		        <li><img src="images/slide_02.jpg" alt="Slide 01">
          <div class="caption"><span>JIWOK.</span>
                                Des coachs olympiques<br> 
							    pour atteindre vos objectifs.</div>
                                <a href="#" class="link">je m'inscris</a>
        </li>
		        <li><img src="images/slide_03.jpg" alt="Slide 01">
          <div class="caption"><span>JIWOK.</span>
                                Vous travaillez sur la musique<br>
								que vous aimez.</div>
                                <a href="#" class="link">je m'inscris</a>
        </li>
		        <li><img src="images/slide_04.jpg" alt="Slide 01">
          <div class="caption"><span>JIWOK.</span>
                                Vous allez progresser.<br>
								JIWOK vous amènera plus loin.</div>
                                <a href="#" class="link">je m'inscris</a>
        </li>

      </ul>
    </div>
    </div>
    
 <a href="#" class="view-popup">Show popup</a>
  <section class="pop"> <img src="images/close.png" alt="close" class="close b-modal __b-popup1__">



          <div class="popbox">
           
          <h3>Sélectionnez votre choix pour la génération MP3</h3>
          <form action="#" method="get" accept-charset="utf-8">
          <p>            <label class="label_check" for="checkbox-01">
<input name="sample-checkbox-01" id="checkbox-01" value="1" type="checkbox" checked />  Voulez-vous utiliser de la musique gratuite pour votre séance ? <a href="#" class="help"><img src="images/help.png" alt=""></a>
</label></p>
          <p> 
           <label class="label_check" for="checkbox-02">
 <input name="sample-checkbox-02" id="checkbox-02" value="1" type="checkbox" /> Voulez-vous utiliser votre propre musique ? <a href="#" class="help"><img src="images/help.png" alt=""></a></label></p>
</form>


          <div align="center"><input type="submit" class="btn_pop ease" value="VALIDER"></div></div>
          </section>
          
          
    <section class="articles-main">
     <div class="in">
         <article>
            <figure><img src="images/icon_01.jpg" alt="MES SÉANCES"></figure>
            <h3>MES SÉANCES</h3>
            <hr>
            <p>Hent re modiatiae 
nessimus et min non 
porum volupic tatust, 
sit, optatur. </p>
         </article>  
         <article>
            <figure><img src="images/icon_02.jpg" alt="MES ALERTES"></figure>
            <h3>MES ALERTES</h3>
            <hr>
            <p>Endandunt aut placcus, 
quia ipicipiendae volorit 
quibero tem. </p>
         </article>  
         <article>
            <figure><img src="images/icon_03.jpg" alt="MES DOCS D’ENTRAÎNEMENT"></figure>
            <h3>MES DOCS D’ENTRAÎNEMENT</h3>
            <hr>
            <p>Quistis ape ommolup 
tatemqui num endunt 
veliquaerion. </p>
         </article>  
         <article>
            <figure><img src="images/icon_04.jpg" alt="MON SUPPORT 7/7"></figure>
            <h3>MON SUPPORT 7/7</h3>
            <hr>
            <p>Moleniaessita sanihil 
luptatia dolorerum 
comnis eiumquiae sequi 
videri con re vent.</p>
         </article>  
         <article>
            <figure><img src="images/icon_05.jpg" alt="L’APPLI / LE CAL"></figure>
            <h3>L’APPLI / LE CAL</h3>
            <hr>
            <p>Tatemque con pora 
cus se cum lanis alique 
et aut doluptatis 
doluptatur? </p>
         </article> 
         </div> 
    </section>
    <div class="frame grid_first">
       <section class="training">
          <p>
            JIWOK.<br> 
            Un entraînement 
            pour chacun.
          </p>
          <a href="#" class="btn_orng">TOUS LES ENTRAÎNEMENTS</a>
       
       </section>
      <section class="form">
      <div>
      <p>
         <label>QUEL EST VOTRE OBJECTIF?</label>
         <input type="text" value="CHOISISSEZ S’IL VOUS PLAIT">
      </p> 
       <p>
         <label>QUEL EST VOTRE NIVEAU?</label>
         <input type="text" value="CHOISISSEZ S’IL VOUS PLAIT">
      </p> 
       <p>
         <label>QUEL EST VOTRE CHOIX DE PRATIQUE?</label>
         <input type="text" value="CHOISISSEZ S’IL VOUS PLAIT">
      </p>   
      </div>
       <a href="#" class="btn_blu ease">VALIDER VOTRE RECHERCHE</a>
      </section>
    
    </div>
    <div class="grid_second frame">
        <figure>
           <img src="images/corner.png" class="corner">
        <img src="images/img-jiwok_01.jpg" alt="Jiwok"></figure>
        <article>
           <h2>J’ai perdu 3 kilos...
...en 27 chansons</h2>
<p class="second-line">JEAN</p>
<p class="third-line">Cette fois, c’est sûr... je suis addict 
     à la course à pied! Qui l’aurait cru 
    il y a 2 mois encore? 
  Pas moi en tout cas!</p>
        <p align="right"><a href="#" class="btn_orng_2 ease">TOUS LES TÉMOIGNAGES</a></p>
        </article>
    </div>
     <div class="frame top-entry">
         <h2>le TOP 10 des entraînements</h2>
         <ul> 
           <li><div>
               <span class="count">10</span>
               <span class="text">PROGRESSER<br> 
EN NATATION</span>
           </div></li>
         </ul>
         
         <ul> 
           <li><div>
               <span class="count">9</span>
               <span class="text">COURSE ET 
RÉCUPÉRATION</span>
           </div></li>
            <li><div>
               <span class="count">8</span>
               <span class="text">TAPIS ET FORME</span>
           </div></li>
         </ul>
         
         <ul> 
           <li><div>
               <span class="count">7</span>
               <span class="text">PERDRE 5KG 
EN ELLIPTIQUE</span>
           </div></li>
            <li><div>
               <span class="count">6</span>
               <span class="text">COURIR UN 
MARATHON</span>
           </div></li>
           <li><div>
               <span class="count">5</span>
               <span class="text">DÉBUTER LE 
RUNNING</span>
           </div></li>
         </ul>
         <ul> 
           <li><div>
               <span class="count">4</span>
               <span class="text">PERDRE 5KG 
EN ELLIPTIQUE</span>
           </div></li>
            <li><div>
               <span class="count">3</span>
               <span class="text">COURIR UN 
MARATHON</span>
           </div></li>
           <li><div>
               <span class="count">2</span>
               <span class="text">COURIR 
PLUS VITE</span>
           </div></li>
           <li><div>
               <span class="count">1</span>
               <span class="text">DÉBUTER LE 
RUNNING</span>
           </div></li>
         </ul>
     </div>
     <div class="frame slider-second">
       <div class="callbacks_container">
        <ul id="slider3" class="rslides callbacks callbacks1">
			   <li id="callbacks1_s0" style="display: list-item; float: none; position: absolute; opacity: 0; z-index: 1; transition: opacity 800ms ease-in-out 0s;" class="">
	    <div class="image">
        <img class="corner" src="images/corner.png">
        <img alt="Slide 01" src="images/slide-second_01.jpg"></div>
        <div class="caption"> 
			  <p class="second-line">Pascal Choisel</p>
			  <p class="third-line">
			  Coordonnateur du pôle France de la fédération française de Triathlon.<br>
Entraîneur de sportifs(ves) de haut niveau au pôle triathlon de Montpellier.<br>
Pascal et Stéphanie ont entrainé et entrainent les Champion de France junior , Champion du Monde Universitaires, Vice Champion de France junior , Vice Champion de France moins de 23<a href="coaches.php">...</a></p>
		</div>
        </li>
        	   <li id="callbacks1_s1" style="display: list-item; float: none; position: absolute; opacity: 0; z-index: 1; transition: opacity 800ms ease-in-out 0s;" class="">
	    <div class="image">
        <img class="corner" src="images/corner.png">
        <img alt="Slide 01" src="images/slide-second_01.jpg"></div>
        <div class="caption"> 
			  <p class="second-line">Stéphanie Gross</p>
			  <p class="third-line">
			  Coordonnateur Préparation Olympique filles horizon 2012 de l\'équipe de france.<br>
Entraîneurs de sportifs(ves) de haut niveau au pôle triathlon de Montpellier.<br>
Pascal et Stéphanie ont entrainé et entrainent les Champion de France junior , Champion du Monde Universitaires, Vice Champion de France junior , Vice Champion de France mo<a href="coaches.php">...</a></p>
		</div>
        </li>
        	   <li id="callbacks1_s2" style="display: list-item; float: left; position: relative; opacity: 1; z-index: 2; transition: opacity 800ms ease-in-out 0s;" class="callbacks1_on">
	    <div class="image">
        <img class="corner" src="images/corner.png">
        <img alt="Slide 01" src="images/slide-second_01.jpg"></div>
        <div class="caption"> 
			  <p class="second-line">Cédric Deanaz</p>
			  <p class="third-line">
			  Professeur d\'EPS.<br>
5 ans en équipe de France de Triathlon.<br>
3 titres de champion du monde militaire.<br>
2 podiums en coupe du monde 2002 et 2004.<br>
victoire sur coupe du monde 2004 à Rio.<br>
remporte 5 fois la coupe de france des clubs.</p>
		</div>
        </li>
              </ul>
              <li><a class="ease" id="catId_0" href="javascript:selectCat('3');">&gt; Les Coachs</a></li>
              <li><a class="ease" id="catId_0" href="javascript:selectCat('3');">&gt; Les Coachs</a></li>
              <li><a class="ease" id="catId_0" href="javascript:selectCat('3');">&gt; Les Coachs</a></li>
    </div>
     </div> 
     <section class="frame press">
         <div class="box">
        <ul>
           <li>
             <span class="press_ID"><img src="images/press_01.jpg" alt="press"></span>
             <span class="count">8"</span>
           </li>
           <li>
             <span class="press_ID"><img src="images/press_02.jpg" alt="press"></span>
             <span class="count">15"</span>
           </li>
           <li>
             <span class="press_ID"><img src="images/press_03.jpg" alt="press"></span>
             <span class="count">19"</span>
           </li>
           <li>
             <span class="press_ID"><img src="images/press_04.jpg" alt="press"></span>
             <span class="count">32"</span>
           </li>
           <li>
             <span class="press_ID"><img src="images/press_05.jpg" alt="press"></span>
             <span class="count">1'19"</span>
           </li>
            <li>
             <span class="press_ID"><img src="images/press_06.jpg" alt="press"></span>
             <span class="count">3'30"</span>
           </li>
        </ul>
        </div>
        
         <div class="box">
        <ul>
           <li>
             <span class="press_ID"><img src="images/press_07.jpg" alt="press"></span>
             <span class="count">132m</span>
           </li>
           <li>
             <span class="press_ID"><img src="images/press_08.jpg" alt="press"></span>
             <span class="count">146m</span>
           </li>
           <li>
             <span class="press_ID"><img src="images/press_09.jpg" alt="press"></span>
             <span class="count">347m</span>
           </li>
           <li>
             <span class="press_ID"><img src="images/press_10.jpg" alt="press"></span>
             <span class="count">678m</span>
           </li>
        </ul>
        <a href="#" class="btn_orng3 ease">TOUTE LA PRESSE</a>
        </div>
     </section>
     
     <div class="foot-nav frame">
        <ul class="nav_01">
          <li><a href="#">ACCUEIL</a></li>
          <li><a href="#">VOTRE ENTRAINEMENT SUR MESURE</a></li>
          <li><a href="#">LES COACHS</a></li>
          <li><a href="#">BLOG</a></li>
          <li><a href="#">FORUM</a></li>
          <li><a href="#">CONTACT</a></li>
          <li><a href="#">AIDE</a></li>
        </ul>
     </div>
     
     <div class="foot-nav frame">
        <ul class="nav_02">
          <li><a href="#">LE BLOG</a></li>
          <li><a href="#">LE FORUM</a></li>
          <li><a href="#">LES FORFAITS</a></li>
          <li><a href="#">CONTACT</a></li>
        </ul>
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
</body>
</html>
