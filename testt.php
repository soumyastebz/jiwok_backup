<?php
// requires php5
		define('UPLOAD_DIR', 'images/');
		$img = $_REQUEST['snapshot'];
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$data = base64_decode($img);
		$file = UPLOAD_DIR . uniqid() . '.png';
		$success = file_put_contents($file, $data);
		echo $success;
		print $success ? $file : 'Unable to save the file.';
	
	?>