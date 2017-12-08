<?php
$a = "http://10.0.0.15/digidom_ver4/se-connecter/";
$b1 = explode( '/', $a );
$b = explode( '/', $a );
end($b);         // move the internal pointer to the end of the array
$key = key($b);
print_r($b1[$key-1]);exit;
?>
