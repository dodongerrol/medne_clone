<?php 

 // the message
$message = "Line 1\r\nLine 2\r\nLine 3";

// In case any of our lines are larger than 70 characters, we should use wordwrap()
$message = wordwrap($message, 70, "\r\n");
$to = 'nwnhemantha@gmail.com';
$subject = 'testm';
// Send
$mail = mail($to, $subject, $message);

if ($mail) {
	echo '1';
} else {
	echo '0';
}


 ?>