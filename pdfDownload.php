<?php
include_once('includeconfig.php');
if(!isset($_SESSION['user']['userId']))
exit("Invalid operation ");
if($_GET['from']==1)
{	
	$path = "/home/sites_web/client/newdesign/pdfgenerate/user pdf/";// change the path to fit your websites document structure
	if(!file_exists($path.$_GET['file']))
		$path =	"/home/sites_web/client/www.jiwok.com/pdfgenerate/user pdf/";
	//'/home/sites_web/client/newdesign/includes/classes/Payment/pdfgenerate/user pdf/';
}
else
{
	$path = "/home/sites_web/client/newdesign/includes/classes/Payment/pdfgenerate/user pdf/";	
	if(!file_exists($path.$_GET['file']))
		$path = "/home/sites_web/client/www.jiwok.com/includes/classes/Payment/pdfgenerate/user pdf/";
}
$fullPath = $path.$_GET['file'];
 
if ($fd = fopen ($fullPath, "r")) {
    $fsize = filesize($fullPath);
    $path_parts = pathinfo($fullPath);
    $ext = strtolower($path_parts["extension"]);
    switch ($ext) {
        case "pdf":
        header("Content-type: application/pdf"); // add here more headers for diff. extensions
        header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
        break;
        default;
        header("Content-type: application/octet-stream");
        header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
    }
    header("Content-length: $fsize");
    header("Cache-control: private"); //use this to open files directly
    while(!feof($fd)) {
        $buffer = fread($fd, 2048);
        echo $buffer;
    }
}
fclose ($fd);
exit;?>
