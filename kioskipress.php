<?php
ob_start();
session_start();
if($_SESSION['user']['userId'] == "")
			{ 
				header("location:https://www.jiwok.com",true,301);exit;
			}
			else
			{
				if(isset($_SESSION['user']['token']))
				{
					$token = $_SESSION['user']['token'];
					header("location:http://jiwok.kioskpress.fr/#/?token=".$token);exit;
				}
			}
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
           <link rel="stylesheet" href="kioskipress/css/font-awesome.min.css">
           <link href="kioskipress/style_kioskipress.css" rel="stylesheet" type="text/css" />


           <!---------------------------->
       </head>
       <body>
       	<header>
       		<div class="frame">
       			<h1 class="logo">
       				<a href="index.html"><img src="images/logo.png" alt="Jiwok" title="Jiwok"></a>
       			</h1>
       			<hgroup>
       				<ul class="user"> 
       				<!-- 	<li><a href="#">Vos magazines gratuits&nbsp;&nbsp;</a></li>

       					<li><a href="#">Déconnexion</a></li> -->
       					<li><a>
                        <i class="fa fa-home fa-2x"></i>&nbsp;&nbsp;Mon Kiosque
                    </a>
</li>
<li><a><i class="fa fa-power-off fa-2x" aria-hidden="true"></i>&nbsp;&nbsp;Déconnexion</a>
</li>
       				</ul>


       			</hgroup>
       		</div>

       	</div>
       </header>

     	<section class="plan">
     		<div class="frame3">
<div class="sec-heading-area">
 <ul>
 	<li class="arrow-bg"><img src="kioskipress/images/bg_latest.png"></li>
 	<li><h4>À la une</h4></li>
</ul>


                       
                    </div>
     			<div class="payment-outer">
     			
     					<div class="single-product-item">
     						<img src="kioskipress/images/1.jpg">
     					</div>
     				

     				
     				<div class="single-product-item">
     						<img src="kioskipress/images/2.jpg">
     					</div>
     			<div class="single-product-item">
     						<img src="kioskipress/images/3.jpg">
     					</div>
     			
     			</div>

     		</div> 

     	</section>

<!-- -------------------------------------footer------------------------------------------ -->

     	<footer>
     		<div class="frame">
     			<nav class="col-01">
     				<a class="logo" href="#"><img src="kioskipress/images/logo-pp.png" /></a>
     			
     				<!-- <a class="find" href="#">RETROUVEZ NOUS<br> 
     					SUR GOOGLE +</a> -->
     				</nav>
     				<nav class="col-02">
     					<h2>CONTACTEZ-NOUS</h2>
     					<ul class="footnav_02">
     						<li><a href="#">15 Rue Claude Tillier, 75012 Paris</a></li>
     						<li><a href="#">+33 (0)1 53 27 34 00</a></li>
     						<li><a href="#">Contact@Propress.Fr</a></li>
     					</ul>
     				
     				</nav>
     				<nav class="col-03">
     					<h2>INFORMATION</h2>
     			    			
     						<ul class="footnav_02">
     							<li><a href="#">Conditions Générales De Vente</a></li>
     							<li><a href="#">Contact</a></li>
     					</ul>
     			
     				</nav>  
     			</div>
    
     		</footer>
     		
     		<div class="footer-bottom-copyright">
        
           
                    <!-- copyright start -->
                    <div class="copyright">
                        <p><a href="#">ProPress Conseil</a> © 2016. Tous droits réservés.</p>
                    </div>
                    <!-- copyright end -->
            
           
        

    </div>


     				
     				</div>






<div class="remodal-overlay">
<div class="remodal-wrapper">
    <h3>
       <a href="https://www.jiwok.com/payment_new.php">Pour bénéficier de cette nouvelle fonctionnalité, vous devez basculer vers notre nouveau système de pass ></a>
    </h3><br><br><br>
    <h1>Non Active</h1>
</div>
</div>

     			</body>
     			</html>











