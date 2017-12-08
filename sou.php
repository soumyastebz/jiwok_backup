<?php

define('IMAGE_PATH', 'assets/img/');
define('UPLOAD_PATH', 'uploads/');

error_reporting(E_ALL);
	ini_set('display_errors',1);
$data	=	array('imageWidth' => '1980','dbimage' => 'shutterstock_291732443.jpg','imageHeight' => '877','imageName' => 'shutterstock_291732443img-1980x877',
    'crop_user_id' => '229');
//$imageName = $data['imageName'];
$dbimage = $data['dbimage'];
$imageWidth = $data['imageWidth'];
$imageHeight = $data['imageHeight'];
$crop_user_id = $data['crop_user_id'];
//echo "<pre/>";print_r($data);exit;
function resize_image($file, $destination, $w, $h,$imageName) {
		

    //Get the original image dimensions + type
    list($source_width, $source_height, $source_type) = getimagesize($file);

    //Figure out if we need to create a new JPG, PNG or GIF
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if ($ext == "jpg" || $ext == "jpeg") {		
        $source_gdim=@imagecreatefromjpeg(IMAGE_PATH.$dbimage);
		//echo "bbb". $source_gdim."nnn";exit;
    } elseif ($ext == "png") {
        $source_gdim=imagecreatefrompng($file);
		//echo "ffff". $source_gdim."nnn";exit;
    } elseif ($ext == "gif") {
        $source_gdim=imagecreatefromgif($file);
    } else {
        //Invalid file type? Return.
        return;
    }

    //If a width is supplied, but height is false, then we need to resize by width instead of cropping
    if ($w && !$h) {
        $ratio = $w / $source_width;
        $temp_width = $w;
        $temp_height = $source_height * $ratio;

        $desired_gdim = imagecreatetruecolor($temp_width, $temp_height);
        imagecopyresampled(
            $desired_gdim,
            $source_gdim,
            0, 0,
            0, 0,
            $temp_width, $temp_height,
            $source_width, $source_height
            );
    } else {
        $source_aspect_ratio = $source_width / $source_height;
        $desired_aspect_ratio = $w / $h;

        if ($source_aspect_ratio > $desired_aspect_ratio) {
            /*
             * Triggered when source image is wider
             */
            $temp_height = $h;
            $temp_width = ( int ) ($h * $source_aspect_ratio);
        } else {
            /*
             * Triggered otherwise (i.e. source image is similar or taller)
             */
            $temp_width = $w;
            $temp_height = ( int ) ($w / $source_aspect_ratio);
        }

        /*
         * Resize the image into a temporary GD image
         */

        $temp_gdim = imagecreatetruecolor($temp_width, $temp_height);
        imagecopyresampled(
            $temp_gdim,
            $source_gdim,
            0, 0,
            0, 0,
            $temp_width, $temp_height,
            $source_width, $source_height
            );

        /*
         * Copy cropped region from temporary image into the desired GD image
         */

        $x0 = ($temp_width - $w) / 2;
        $y0 = ($temp_height - $h) / 2;
        $desired_gdim = imagecreatetruecolor($w, $h);
        imagecopy(
            $desired_gdim,
            $temp_gdim,
            0, 0,
            $x0, $y0,
            $w, $h
            );
    }

    /*
     * Render the image
     * Alternatively, you can save the image in file-system or database
     */

    if ($ext == "jpg" || $ext == "jpeg") {
        ImageJpeg($desired_gdim,$destination,100);
    } elseif ($ext == "png") {
        ImagePng($desired_gdim,$destination, 9);
    } elseif ($ext == "gif") {
        ImageGif($desired_gdim,$destination);
    } else {
        return;
    }

    ImageDestroy ($desired_gdim);
    unlink($file);
	 
	
}

resize_image(IMAGE_PATH.$dbimage, IMAGE_PATH.$dbimage, $imageWidth, $imageHeight, $imageName);



?>

