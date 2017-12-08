<?php
/*********************
 * Cropping Tool v1.0
 * Author: Deepak
 * Date: 06/08/2015
 *********************/

/*
 * Path to original image (Directory)
 * Trailing slash required
 */
define('IMAGE_PATH', 'uploads/users/');

/* 
 * Upload directory for cropped images (should have 777 permission) 
 * Trailing slash required *
 */
define('UPLOAD_PATH', 'uploads/users/');

// Image resolutions for cropping
$IMAGE_RESOLUTIONS =
	array(
		array(
			'name'  => 'Portrait 320 x 480',
			'width' => '320',
			'height'=> '320'
			)
		
		);
