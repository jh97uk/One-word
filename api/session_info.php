<?php
	require_once("class/class_session.php");
	session_start();
	if(!isset($_SESSION['uid'])){
		echo json_encode(["error"=>"invalid_user"]);
		return;
	} elseif (!isset($_POST['session'])) {
		echo json_encode(["error"=>"needs_session"]);
		return;
	}

	$session = $_POST["session"];

	$sessionInfo = new Session();

	$players = $sessionInfo->getPlayers($session); // When we have session timeouts working, this will be changed
	echo json_encode(["host"=>$players["host"], "player"=>$players["player"]]);

?>