<?php
/*--------------------------------------------------*/

// Project 		: Jiwok

// Created on	: 207-07-2015

// Created by	: Soumya.A

// Purpose		: New Design Integration - Footer 

/*--------------------------------------------------*/

//collecting data from the xml for the static contents
/*$lanId	=2 ;//need to change
$page_name = 'index-sou.php';//need to change
$ftrObj 	= new footerLinks();*/
$returnDataFooter		= $parObj->_getTagcontents($xmlPath,'footer','label');

$arrayDataFooter		= $returnDataFooter['general'];
$coachUrl = "coach-sportif"; 

switch($lanId){

	

	case	1 : $coachUrl 		= 	"coach-athlete";

				$goalrun  		= 	"training/run+faster-1#";

				$goalbeginrun  	= 	"training/start+running-10";

				break;

	case	2 : $coachUrl 		=	"coach-sportif";

				$goalrun  		=   "entrainement/courir-plus-vite-1";

				$goalbeginrun  	=   "entrainement/debuter-en-course-a-pied-10";

				//$goalrun	=	str_replace('+',"-",$goalrun);

				break;

	case	3 : $coachUrl 		=   "entrenador-atleta";

				$goalrun  		=   "entrainement/courir+plus+vite-1";

				$goalbeginrun  	=   "entrainement/debuter+en+course+a+pied-10";

				break;

	case	4 : $coachUrl 		=   "allenatore-atleta";

				$goalrun  		=   "entrainement/courir+plus+vite-1";

				$goalbeginrun  	=   "entrainement/debuter+en+course+a+pied-10";

				break;

	case	5 : $coachUrl 		=   "allenatore-atleta";

				$goalrun  		=   "entrainement/courir-plus-vite-1";

				$goalbeginrun  	=   "entrainement/debuter-en-course-a-pied-10";

				break;			

}


if(($page_name != 'userreg1.php') && ($page_name != 'userreg2.php'))
{
include("menu_footer.php"); 
}

?>
     <footer>
          <div class="frame">
             <nav class="col-01">
                <a class="logo" href="#"><img src="<?=ROOT_FOLDER?>images/logo-footer.png" alt="Jiwok">
                <?php
                /*if($lanId	!=	5)
				{

				 	echo $parObj->_getLabenames($arrayDataFooter,'logocontact','name').": info@jiwok.com";
			   }
  			    else
				{?>
					<span style="font-size:11px;">

					<?=$parObj->_getLabenames($arrayDataFooter,'logocontact','name').": <a href='mailto:kontakt@jiwok.pl' style='color:#B5B5B5;'>kontakt@jiwok.pl</a>";?>
        			</span>
                     <?php
                }*/?>
               </a>
                <ul class="footnav_01">
                 <li><a href="<?=ROOT_JWPATH?>testimonial_details.php"><?=$parObj->_getLabenames($arrayDataFooter,'testimonials','name');?></a></li>
                   <li><a href="<?=ROOT_JWPATH?>press"><?=$parObj->_getLabenames($arrayDataFooter,'talkabout','name');?></a></li>
                   <li><a href="<?=ROOT_JWPATH?>plan.php"><?=$parObj->_getLabenames($arrayDataFooter,'plan','name');?></a></li>                   
                </ul>
                
                <a class="find"  rel="publisher" target="_blank" href="https://plus.google.com/105832920719073640933">RETROUVEZ NOUS <br>SUR GOOGLE +</a>
<br>
By Denis Dhekaier : Find me on
<a class="find" rel="author" target="_blank" href="https://plus.google.com/u/0/111032778999033787390?rel=author" >Google+</a>
<br>
             </nav>
              <nav class="col-02">
                <h2><span><?=strtoupper($parObj->_getLabenames($arrayDataFooter,'whatbrings','name'));?></span></h2>
                    <ul class="footnav_02">
                     <?php if($lanId!=5) { ?>
                    <li><a href="<?=ROOT_JWPATH?><?=$goalrun;?>"><?=$parObj->_getLabenames($arrayDataFooter,'runfaster','name');?></a></li>
         			<li><a href="<?=ROOT_JWPATH?><?=$goalrun;?>"><?=$parObj->_getLabenames($arrayDataFooter,'improvevma','name');?></a></li>
					<li><a href="<?=ROOT_JWPATH?><?=$goalbeginrun;?>"><?=$parObj->_getLabenames($arrayDataFooter,'beginbyrun','name');?></a></li>
         		<?php } else {

	   ?>
		<li><a href="<?=ROOT_JWPATH?><?=$goalbeginrun;?>"><?=$parObj->_getLabenames($arrayDataFooter,'beginbyrun','name');?></a></li>
         <li><a href="<?=ROOT_JWPATH?><?=$goalrun;?>"><?=$parObj->_getLabenames($arrayDataFooter,'improvevma','name');?></a></li>
         <li><a href="<?=ROOT_JWPATH?><?=$goalrun;?>"><?=$parObj->_getLabenames($arrayDataFooter,'runfaster','name');?></a></li>
          <?php } ?>
                      
                    </ul>
                    <h2><span><?=strtoupper($parObj->_getLabenames($arrayDataFooter,'coaches','name'));?></span></h2>
                    <ul class="footnav_02">

         <li><a href="<?=ROOT_JWPATH?><?php echo $coachUrl; ?>"><?=$parObj->_getLabenames($arrayDataFooter,'fitness','name');?></a></li>
         <li><a href="<?=ROOT_JWPATH?><?php echo $coachUrl; ?>"><?=$parObj->_getLabenames($arrayDataFooter,'swimming','name');?></a></li>
         <li><a href="<?=ROOT_JWPATH?><?php echo $coachUrl; ?>"><?=$parObj->_getLabenames($arrayDataFooter,'running','name');?></a></li>

       </ul>

              </nav>
              <nav class="col-03">
                <h2><span><?=strtoupper($parObj->_getLabenames($arrayDataFooter,'workouttailor','name'));?></span></h2>
                <div class="clear"></div>
                <div class="colums">
                <ul class="footnav_02">                
				<?php         
                $arrayLinks = $ftrObj->_getFooterArray($lanId);        
                if($arrayLinks)        
                foreach($arrayLinks as $arrayKey => $arrayLink)        
                    {        
                        $footerName  = $arrayLink['footer_name'];        
                        $link        = $arrayLink['link'];        
                        $link        = strtolower($link);//Change the case of link for SEO  
						if($_SERVER['HTTP_HOST'] == "10.0.0.8")
						{       
                            $link 		 =	str_replace("http://www.jiwok.com","http://10.0.0.8/jiwokv3",$link);
						}
                        else if($_SERVER['HTTP_HOST'] == "beta.jiwok.com")
						{     
                            $link 		 =	str_replace("http://www.jiwok.com","",$link);
							 
						}       
                ?>
                
            
               
              <li> <a target="_blank" href="<?php echo $link;?>"><?php echo $footerName;?></a></li>
      	 		
                 <?php	

	   			if($lanId	!=	5)

				{

	   				echo ((++$arrayKey%11)==0) ? '</ul></div><div class="colums"><ul class="footnav_02">':'';

				}

				else

				{

					echo ((++$arrayKey%10)==0) ? '</ul></div><div class="colums"><ul class="footnav_02">':'';

				}
                
              }       
                ?>                    
                    </ul>
                   </div>
              </nav>  
          </div>
     </footer>
    
     <?php
 if(($lanId	==	2) && ($page_name == 'index.php')) {?>
  <div class="block_foot">
<h1>Débuter le jogging, running, course à pied, vélo appartement, elliptique, marche, trail</h1>
<p>Si vous désirez commencer à courir, faire du sport, débuter elliptique ou vous remettre au sport, Jiwok est le service qu'il vous faut !
Tous les débutant pourront ainsi rapidement progresser.
Vous serez coacher et vous pourrez progresser afin de rester en forme, retouver votre ligne et perdre du poids ( de 3 à 10 kilos).</p><h2>Plan marathon, semi marathon, 10 km, trail</h2>
<p>Jiwok propose également des plan d'entrainement marathon, semi marathon, 10 km et trail pour les débutants et les confirmées. Des objectifs de 1 h 30 à 4 h 30 afin de vous permettre de progresser, de courir plus et d'amioler votre temps de couse
Vous pourrez également améliorer votre vma.</p>
<h2>Perdre du poids en faisant du sport</h2>
<p>Avec les conseils de votre coach jiwok, vous pourrez perdre du poids en courant, nageant ou pedélant grâce au jogging, running, course à pied, marche, tapis de course, vélo d'appartement, elliptique et natation
Retrouver la forme rapidement et progressivement avec des séances de sports adaptés à votre niveau physique</p>
<h2>courir et faire du sport en musique</h2>
<p>Suivez les séances et les playlist Jiwok pour courir en musique. Les musiques selectionnés par jiwok vous permettront de courir, jogging, running avec des morceaux de musiques adaptés au sport</p>
<h2>application ihpone et android sport</h2>
<p>Installez notre application Iphone et Android pour suivre les séances de coaching Jiwok : séance running, jogging, course à pied, natation, marche, elliptique, vélo, tapis de course, marathon, semi marathon, 10 km, trail, débutant en sport, perte de poids</p>
 </div>
 <?php } ?>
 
  
  <ul class="foot_links">

   <li><a href="<?=JIWOK_URL?>about-us"><?=$parObj->_getLabenames($arrayDataFooter,'aboutus','name');?></a></li>

   <li>|</li>

   <li> <a href="<?=JIWOK_URL?>sitemap.php"><?=$parObj->_getLabenames($arrayDataFooter,'sitemap','name');?></a></li>

   <li>|</li>

   <li> <a href="<?=JIWOK_URL?>contact-us"><?=$parObj->_getLabenames($arrayDataFooter,'contact','name');?></a></li>

  
   <li>|</li>

   <li> <a href="<?=JIWOK_URL?>terms-and-conditions"><?=$parObj->_getLabenames($arrayDataFooter,'terms','name');?></a></li>

   <li>|</li>

   <li> <a href="<?=JIWOK_URL?>press"><?=$parObj->_getLabenames($arrayDataFooter,'press','name');?></a></li>

   <li>|</li>

   <li> <a href="http://www.jiwok.com/jobs"><?=$parObj->_getLabenames($arrayDataFooter,'job','name');?></a></li>

   <li>|</li>
   <?php if($lanId!=5){?> 
   <li> <a href="<?=JIWOK_URL?>partners"><?=$parObj->_getLabenames($arrayDataFooter,'partnersbottom','name');?></a></li>
   <li>|</li><?php }?>
   <li> <a href="<?=JIWOK_URL?>faq"><?=$parObj->_getLabenames($arrayDataFooter,'help','name');?></a></li>
   </ul>  
   <p class="copyright"><?=$parObj->_getLabenames($arrayDataFooter,'copyrights','name');?> <?=date("Y");?> &nbsp;| &nbsp;<?=$parObj->_getLabenames($arrayDataFooter,'poweredby','name');?>&nbsp; <a style=" color:#9a9b9e;" href="http://reubro.com/" target="_blank">Reubro International Debugging</a> </p> 
   </div>




<?php 
 if($pg_name=='userArea'){ ?>



	<?php if($_REQUEST["origin"]=="registration"){ ?>

    <!-- Google Code for inscrption Conversion Page -->

    <script type="text/javascript">

    /* <![CDATA[ */

    var google_conversion_id = 1001500373;

    var google_conversion_language = "fr";

    var google_conversion_format = "2";

    var google_conversion_color = "ffffff";

    var google_conversion_label = "O8luCIug5QMQ1d3G3QM";

    var google_conversion_value = 0;

    /* ]]> */

    </script>

    <script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js"> 

    </script>

    <noscript>

    <div style="display:inline;">

    <img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1001500373/?label=O8luCIug5QMQ1d3G3QM&guid=ON&script=0"/> 

    </div>

    </noscript>

    <?php } ?>

    

    <?php if($_REQUEST["origin"]=="payment"){ ?>

    	<!-- Google Code for Abonnement Conversion Page -->

		<script type="text/javascript">

        /* <![CDATA[ */

        var google_conversion_id = 1001500373;

        var google_conversion_language = "fr";

        var google_conversion_format = "2";

        var google_conversion_color = "ffffff";

        var google_conversion_label = "Su1gCOuj5QMQ1d3G3QM";

        var google_conversion_value = 0;

        /* ]]> */

        </script>

        <script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js"> 

        </script>

        <noscript>

        <div style="display:inline;">

        <img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1001500373/?label=Su1gCOuj5QMQ1d3G3QM&guid=ON&script=0"/> 

        </div>

        </noscript>

    <?php } ?>





<?php } ?>




<!-- HitTail Code -->

<script type="text/javascript">

        (function(){ var ht = document.createElement('script');ht.async = true;

          ht.type='text/javascript';ht.src = '//102800.hittail.com/mlt.js';

          var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ht, s);})();

</script>



<?php if($lanId == 1) { ?>





<script type="text/javascript">



  var _gaq = _gaq || [];

  _gaq.push(['_setAccount', 'UA-5361429-9']);

  _gaq.push(['_trackPageview']);



  (function() {

    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;

    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';

    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);

  })();



</script>

<?php } elseif($lanId == 5) { ?>

<script type="text/javascript">



  var _gaq = _gaq || [];

  _gaq.push(['_setAccount', 'UA-5361429-13']);

  _gaq.push(['_trackPageview']);



  (function() {

    var ga = document.createElement('script'); ga.type = 



'text/javascript'; ga.async = true;

    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 



'http://www') + '.google-analytics.com/ga.js';

    var s = document.getElementsByTagName('script')[0]; 



s.parentNode.insertBefore(ga, s);

  })();



</script> <?php }



		

else { ?>





<script type="text/javascript">



  var _gaq = _gaq || [];

  _gaq.push(['_setAccount', 'UA-5361429-3']);

  _gaq.push(['_trackPageview']);



  (function() {

    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;

    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';

    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);

  })();



</script>
<?php if(($pg_name!='userreg1')&&($pg_name!='userreg2')){ ?>





<!-- Start of Zopim Live Chat Script -->

<!--<script type="text/javascript">

document.write(unescape("%3Cscript src='" + document.location.protocol + 

    "//zopim.com/?XtSjrfYl9ZDh53EXLa6HOLLsvTdrasUs' charset='utf-8' " + 

 "type='text/javascript'%3E%3C/script%3E"));

</script>-->

<!-- End of Zopim Live Chat Script -->



<!--<script>

  $zopim.livechat.set({

    language: 'fr',

    name: 'username',

    email: 'useremail@mail.com'

  });

</script>-->



<?php } ?>





<?php } ?>

</body>

</html>

