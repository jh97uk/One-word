<?php
	require_once("class/class_session.php");

	$session = new Session();
	$newSession = $session->createSession();
	
	echo json_encode(["status"=>"complete", "sessionid"=>$newSession]);
?>