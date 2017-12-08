
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
<script type="text/javascript" src="js/jquery.1.9.1.min.js"></script>
<script type="text/javascript" src="js/jquery.bpopup.min.js"></script>
<script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
<style type="text/css">

</style>


<!--<script src="js/jquery.min.js"></script>-->
<script type="text/javascript">
    ;(function($) {
        $(function() {
            $('.view-popup').bind('click', function(e) {
                e.preventDefault();
                $('.pop').bPopup({
	    easing: 'easeOutBounce', 
            speed: 2000,
            transition: 'slideDown'
        });
            });
        });

    })(jQuery);
	
	;(function($) {
        $(function() {
            $('.view-popup1').bind('click', function(e) {
                e.preventDefault();
                $('.pop1').bPopup({
	    easing: 'easeOutBounce', 
            speed: 2000,
            transition: 'slideDown'
        });
            });
        });

    })(jQuery);
	</script>

</head>
<body>

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
          
          
          
       <script>
    var d = document;
    var safari = (navigator.userAgent.toLowerCase().indexOf('safari') != -1) ? true : false;
    var gebtn = function(parEl,child) { return parEl.getElementsByTagName(child); };
    onload = function() {
        
        var body = gebtn(d,'body')[0];
        body.className = body.className && body.className != '' ? body.className + ' has-js' : 'has-js';
        
        if (!d.getElementById || !d.createTextNode) return;
        var ls = gebtn(d,'label');
        for (var i = 0; i < ls.length; i++) {
            var l = ls[i];
            if (l.className.indexOf('label_') == -1) continue;
            var inp = gebtn(l,'input')[0];
            if (l.className == 'label_check') {
                l.className = (safari && inp.checked == true || inp.checked) ? 'label_check c_on' : 'label_check c_off';
                l.onclick = check_it;
            };
            if (l.className == 'label_radio') {
                l.className = (safari && inp.checked == true || inp.checked) ? 'label_radio r_on' : 'label_radio r_off';
                l.onclick = turn_radio;
            };
        };
    };
    var check_it = function() {
        var inp = gebtn(this,'input')[0];
        if (this.className == 'label_check c_off' || (!safari && inp.checked)) {
            this.className = 'label_check c_on';
            if (safari) inp.click();
        } else {
            this.className = 'label_check c_off';
            if (safari) inp.click();
        };
    };
    var turn_radio = function() {
        var inp = gebtn(this,'input')[0];
        if (this.className == 'label_radio r_off' || inp.checked) {
            var ls = gebtn(this.parentNode,'label');
            for (var i = 0; i < ls.length; i++) {
                var l = ls[i];
                if (l.className.indexOf('label_radio') == -1)  continue;
                l.className = 'label_radio r_off';
            };
            this.className = 'label_radio r_on';
            if (safari) inp.click();
        } else {
            this.className = 'label_radio r_off';
            if (safari) inp.click();
        };
    };
</script> 
</body>
</html>

