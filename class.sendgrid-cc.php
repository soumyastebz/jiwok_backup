<?php
include_once("/home/sites_web/client/newdesign/Swift/lib/swift_required.php");
//include_once("/var/www/html/jiwokv3/Swift/lib/swift_required.php");

class sendgrid{

		public function send($subject,$from,$to,$htmlbody,$texts="",$marathon="",$form_kalenji="",$path='',$replyto=''){ 
		
					
		/*print_r($from); 
		print_r($subject);echo "subject<br>";
				print_r($from);echo "sendFrom<br>";
				print_r($to);echo "sendTo<br>";
				print_r($htmlbody);echo "htmlnnnnnnnnn<br>";exit;*/
		
								
								// Login credentials
								$username 	= 	'denis@jiwok.com';
								$password 	= 	'nike2000';
								
								//======================
								
								// Setup Swift mailer parameters
								$transport = Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 25);
								$transport->setUsername($username);
								$transport->setPassword($password);
								$swift = Swift_Mailer::newInstance($transport);
							
								// Create a message (subject)
								if($marathon)
								{
									 $message = new Swift_Message($subject,$htmlbody,'text/plain','iso-8859-1');
							        
								}else if($form_kalenji)
								{
									$message = new Swift_Message($subject,$htmlbody,'text/html','iso-8859-1');
								}
								else{ 
									$message = new Swift_Message($subject);
									$message->setBody($htmlbody, 'text/html');
							    }
								// attach the body of the email
								$message->setFrom($from);								
								$message->setTo($to);
								$message->setBcc(array("soumyareubro15@gmail.com" => "JiwokReubro"));								
								//$message->setCc("soumyaa.reubro@gmail.com");
								if($texts !="")
								{
									$message->addPart($text, 'text/plain');
								}
								if($path !="")
								{
									$attachment = Swift_Attachment::fromPath($path, 'application/pdf');	
									$message->attach($attachment);
									
								}	
								if($replyto	!="") 
								{
									$message->setReplyTo($replyto);
								}	 
										
							$recipients = $swift->send($message, $failures);
								/*print_r($recipients);
								print_r($failures);exit;*/
								//=========================
							//echo"sss";print_r($recipients);exit;
							
								return $recipients;
		}
}
