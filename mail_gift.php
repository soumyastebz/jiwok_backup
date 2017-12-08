<?
include_once "includes/classes/class.sendgrid.php";
function mail_attachment($filename, $path,$from_name, $mailto, $from_mail, $replyto, $subject, $message) 

	{

		

   $file = $path.$filename;

 /*   $file_size = filesize($file);

    $handle = fopen($file, "r");

    $content = fread($handle, $file_size);

    fclose($handle);

    $content = chunk_split(base64_encode($content));

    $unid = md5(uniqid(time()));*/
	$mailto  = array($mailto => '');
	$from    = array($from_mail => $from_name);
	$replyto = array($replyto => "");
	
	$sendg = new sendgrid();
	echo $subject;
	echo $from;
		echo $mailto .$message;
		echo $file;exit;
 	//$recipients = $sendg->send($subject,$from,$mailto,$message,$text='',$marathon='',$iso='',$file,$replyto);
 	return $recipients;

   }

   ?>

