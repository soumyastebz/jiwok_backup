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
define('IMAGE_PATH', 'assets/img/');

/* 
 * Upload directory for cropped images (should have 777 permission) 
 * Trailing slash required *
 */
define('UPLOAD_PATH', 'uploads/');

// Image resolutions for cropping
$IMAGE_RESOLUTIONS =
	array(
		array(
			'name'  => 'Square 1024x1024',
			'width' => '1024',
			'height'=> '1024'
			),
		array(
			'name'  => 'Landscape 1024x512',
			'width' => '1024',
			'height'=> '512'
			),
		array(
			'name'  => 'Portrait 512x1024',
			'width' => '512',
			'height'=> '1024'
			),
		array(
			'name'  => 'Portrait 256x512',
			'width' => '256',
			'height'=> '512'
			)
		);
