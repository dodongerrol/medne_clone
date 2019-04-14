<?php

// include ('DB_Config.php');

//  	if (isset($_POST["email"])){

//  		$email = $_POST["email"];
//  		$time = time();

// 		$connection = new createConnection();

//     	$connection->connectToDatabase(); 

//     	$sql = "INSERT INTO subscribe_users values('', '$email', '$time', 1 )";

// 		if (mysqli_query($connection->myconn, $sql)) {

// 		    echo 1;

// 		} else {

// 		    echo "Error: " . $sql . " - " . mysqli_error($connection->myconn);
// 		}


// 	}
// 	else {

// 		echo 0;
// 	}


	$email   = $_POST['email'];

	$to = "info@medicloud.sg";
	$subject = "Subscriber";

	$body  = "<h3>New subscriber:\n\n</h3>";
	$body .= "Email    : ".$email." \n<br>";
	
	
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