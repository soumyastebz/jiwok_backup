<?php

//if($_POST){

 
$ldap['user']   = 'soumya.reubro@gmail.com';

$ldap['pass']   = 'soumya';

$ldap['host']   = 'http://10.0.0.8/jiwokv3/';

$ldap['port']   = 389;

 
$ldap[‘conn’] = ldap_connect( $ldap[‘host’], $ldap[‘port’] );

 

$ldap[‘bind’] = ldap_bind($ldap[‘conn’], $ldap[‘user’], $ldap[‘pass’]);

 

if( !$ldap[‘bind’] )

{

echo ldap_error( $ldap[‘conn’] );

exit;

}

 

echo '<p>';

echo ($ldap[‘bind’])? "Valid Login" : "Login Failed";

echo '</p><br />';

ldap_close( $ldap[‘conn’] );

//}

//~ else
//~ 
//~ {
//~ 
//~ echo '
//~ 
//~ <!DOCTYPE HTML >
//~ 
//~ <html>
//~ 
//~ <head>
//~ 
//~ <title>LDAP Login Test</title>
//~ 
//~ </head>
//~ 
//~ <body>
//~ 
//~ <h1>  Login Here</h1>
//~ 
//~ <form method=”POST” action=”your page url”>
//~ 
//~ <p>
//~ 
//~ User Name:
//~ 
//~ <input type=”text” name=”user” >
//~ 
//~ <br />
//~ 
//~ Password:
//~ 
//~ <input type=”password” name=”pass” >
//~ 
//~ <br />
//~ 
//~ <input type=”submit” name=”submit” value="Submit">
//~ 
//~ </p>
//~ 
//~ </form>
//~ 
//~ </body>
//~ 
//~ </html>
//~ 
//~ ';
//~ 
//~ }

?>
