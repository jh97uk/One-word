<?php
require_once("class/class_session.php");
session_start();

if(!isset($_SESSION['uid'])){
	echo json_encode(["error"=>"invalid_user"]);
	return;
} elseif(!isset($_POST['session'])){
	echo json_encode(["error"=>"needs_session"]);
	return;
}

$session = $_POST['session'];
$client = $_SESSION['uid'];

$sessionJoin = new Session();

$status = $sessionJoin->joinSession($client, $session);

if(!$status){
	echo json_encode(["error"=>"needs_session"]);
	return;
}

echo json_encode(["user_status"=>$status]);


