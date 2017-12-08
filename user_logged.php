<?php  
session_start();

if($_SESSION['user']['userId'] != ""){
    $ms = base64_encode("confirm");
	if($_REQUEST["test_tmp"]){
		echo "<pre>";
		print_r($_SERVER);
		die("Gud Battle");
	}
	
	header("location:userArea.php?conf=".$ms,true,301);
	exit();
}
?>
