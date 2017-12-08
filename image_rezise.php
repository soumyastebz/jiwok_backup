<?php
/* For resizing the photo */

   include('includes/classes/image_resize.php'); 
   $Objimage 		= new SimpleImage();
   $imgpath  		= "uploads/programs/".$image;
   $imgresizepath  	= "uploads/programs/newsize/".$image;
  
   $Objimage->load($imgpath);
   //new design photo w-200,h-135
   if($Objimage->getWidth($imgpath) > 200 && $Objimage->getHeight($imgpath) > 135)
	{	$Objimage->resize(200,135); }
		
   else if($Objimage->getWidth($imgpath) > 200)
	{	$Objimage->resizeToWidth(200); }
		
   else if($Objimage->getHeight($imgpath) > 135)
	{	$Objimage->resizeToHeight(135); }
   
   else
		 //$Objimage->resize($Objimage->getWidth($imgpath),$Objimage->getHeight($imgpath));
		 $Objimage->save($imgresizepath);
		 
   $Objimage->save($imgresizepath);
?>