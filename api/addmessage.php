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

if(isset($_SESSION["lasttime"]) and $_SESSION['lasttime'] + 0.2 > time()){
	return;
}

$_SESSION["lasttime"] = time();


$submitMessage = new NewMessage($uid, $_POST['message'], $session);

if(!$submitMessage->canMessage()){
	return;
}

$submitMessage->insertMessage();