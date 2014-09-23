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
$message = html_entity_decode($_POST['message']);
$message = explode(" ", $message);
$submitMessage = new NewMessage($uid, $message[0], "", $session);
$submitMessage->insertMessage();



?>
