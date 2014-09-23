<?php
require_once("class/class_message.php");
	session_start();
if(!session_status()){
	session_start();
	json_encode(["error"=>"invalid_user"]);
	return;
} 

$session = $_POST['session'];
$uid = $_SESSION['uid'];
$message = htmlentities($_POST['message']);


$submitMessage = new NewMessage($uid, $message, "", $session);
$submitMessage->insertMessage();



?>
