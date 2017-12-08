<?php
//Carregar imagem
/*$rImg = ImageCreateFromJPEG("images/coupon-2.jpg");
 
//Definir cor
$cor = imagecolorallocate($rImg, 255, 255, 255);
 
//Escrever nome
imagestring($rImg,5,126,22,"o",$cor);

//imagefill($rImg,5,126,22,"o",$cor);
 
//Header e output
ob_end_clean();
header('Content-type: image/jpeg');
imagejpeg($rImg,NULL,100);*/

//================
$im = ImageCreateFromJPEG("images/coupon-2.jpg");
  $xcoords = "126";
  $ycoords = "22";
 ob_end_clean();
    drawMe($blue,$im);
   $red = imagecolorallocate($im, 255, 0, 0);
$green = imagecolorallocate($im, 0, 128, 0);
$blue = imagecolorallocate($im, 0, 0, 255);
function drawMe($color,$im)
{
 
  imagesetpixel($im, $xcoords, $ycoords - 1, $color);
  imagesetpixel($im, $xcoords + 1, $ycoords - 1, $color);
  imagesetpixel($im, $xcoords + 1, $ycoords, $color);
  imagesetpixel($im, $xcoords + 1, $ycoords + 1, $color);
  imagesetpixel($im, $xcoords, $ycoords, $color); // main dot !!!
  imagesetpixel($im, $xcoords, $ycoords + 1, $color);
  imagesetpixel($im, $xcoords - 1, $ycoords + 1, $color);
  imagesetpixel($im, $xcoords - 1, $ycoords, $color);
  imagesetpixel($im, $xcoords - 1, $ycoords - 1, $color);
  
}
?>