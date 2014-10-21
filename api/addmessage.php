<?php
require_once("class/class_message.php");
session_start();

if(!isset($_SESSION['uid'])){
	echo json_encode(["error"=>"invalid_user"]);
	return;
} elseif (!isset($_POST['message'])) {
	echo json_encode(["error"=>"invalid_message"]);
	return;
}

$session = $_POST['session'];
$uid = $_SESSION['uid'];
$date = date("Y-m-d");

$submitMessage = new NewMessage($uid, $_POST['message'], $date, $session); // Move the date to the session table and _not_ the message table...

if(!$submitMessage->canMessage()){
	return;
}

$submitMessage->insertMessage();