<?php 



	$fname   = $_POST['fname'];
	$lname   = $_POST['lname'];
	$company = $_POST['company'];
	$email   = $_POST['email'];
	$phone   = $_POST['phone'];
	$message = $_POST['message'];

	$to = "info@medicloud.sg";
	$subject = "New Inquery";

	$body  = "<h3>You have received a new inquery\n\n</h3>";

	$body .= "Name     : ".$fname." ".$lname."\n<br>";
	$body .= "Email    : ".$email." \n<br>";
	$body .= "Phone No : ".$phone." \n<br>";
	$body .= "Company  : ".$company." \n<br>";
	$body .= "Message  : ".$message;
	
	
	// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <medicloud@medicloud.com>' . "\r\n";
//$headers .= 'Cc: myboss@example.com' . "\r\n";
$mail = mail($to, $subject, $body, $headers);

if ($mail) {
	echo '1';
} else {
	echo '0';
}


 ?>
