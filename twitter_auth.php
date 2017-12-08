<?php
		$ch = curl_init();
        $TOKEN = "1545176754-KY43gpzgtG2DFY276dFBWmjNI1iZYmokd7qgzsd";
   $url1 = "https://api.twitter.com/oauth/authenticate";
         $post = array(
               //  "path" => $path_dis
                 
                     );
         $link = json_encode($post); 
         curl_setopt($ch,CURLOPT_URL,$url1);
         curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
         curl_setopt($ch,CURLOPT_POSTFIELDS,$link);
         curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
         $headers = array();
         $headers[] = 'Accept: application/json';
         $headers[] = 'Content-Type: application/json';
        $headers[] = "Authorization: Bearer ".$TOKEN;
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);     
         $response1 = curl_exec($ch); 
        
         $sharelink  = json_decode($response1,true);print_r($response1);exit;
?>
