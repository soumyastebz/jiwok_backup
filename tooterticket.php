$content = str_replace('{footer_content}', '  <footer>
          <div class="frame">
             <nav class="col-01">
                <a class="logo" href="#"><img src="../images/logo-footer.png" alt="Jiwok"><br>'.$contact_name.'</a>
                <ul class="footnav_01">
                 <li><a href="http://'.$sitePath.'/'.$suppotcode.'{{testimonialurl_pl}}.php">{{testimonial_pl}}</a></li>
				<li><a href="http://'.$sitePath.'/'.$suppotcode.'press">{{talk_about_jiwok}}</a></li>
				<li><a href="http://'.$sitePath.'/'.$suppotcode.'plan.php">{{jiwok_plan_pl}}</a></li>
                </ul>
               
             </nav>
              <nav class="col-02">
                <h2><span>{{what_brings}}</span></h2>
                    <ul class="footnav_02">
                     <li><a href="http://'.$sitePath.'/'.$suppotcode.'services_details.php">{{run_faster}}</a></li>
				<li><a href="http://'.$sitePath.'/'.$suppotcode.'services_details.php">{{improve_VMA}}</a></li>
				<li><a href="http://'.$sitePath.'/'.$suppotcode.'services_details.php">{{begin_run}}</a></li>
                    </ul>
                    <h2><span>{{the_coach}}</span></h2>
                    <ul class="footnav_02">
					<li><a href="http://'.$sitePath.'/'.$suppotcode.'coaches.php">{{fitness}}</a></li>
				<li><a href="http://'.$sitePath.'/'.$suppotcode.'coaches.php">{{swimming}}</a></li>
				<li><a href="http://'.$sitePath.'/'.$suppotcode.'coaches.php">{{running}}</a></li>
					</ul>
              </nav>
              
      
              
               <nav class="col-03">
                <h2><span>VOTRE ENTRAÎNEMENT SUR MESURE</span></h2>
               <div class="colums">
                <ul class="footnav_02">   
               '.$supp_obj->htmlReplaceEncode($footerLink).'
				  </ul>
                   </div>
              </nav>  
              
              
              
               </div>
     </footer>',$content);
