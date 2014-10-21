<?php
include('class/class_session.php');
session_start();

if(!isset($_SESSION['uid'])){
	echo json_encode(["error"=>"user_required"]);
	return;
} elseif (!isset($_POST['session'])) {
	echo json_encode(["error"=>"invalid_session"]);
	return;
}

$sessionid = $_POST['session'];

$session = new Session();

echo json_encode($session->getSessionStatus($sessionid));
