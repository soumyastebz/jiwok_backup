<?php
if (isset($_POST['toto'])) {
   echo "Method : ";
 print_r(strtolower($_SERVER['REQUEST_METHOD']));
   echo "\n";
 echo "post";
 print_r($_POST);
 echo "\n";
 echo "get";
 print_r($_REQUEST);
  die();}


 echo "Method : ";
 print_r(strtolower($_SERVER['REQUEST_METHOD']));


exit;

?>