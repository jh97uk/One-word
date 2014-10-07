<?php
	require_once("class/class_message.php");

	if(!isset($_POST['session'])){
		echo json_encode(["error"=>"invalid_session"]);
		return;
	} elseif (!isset($_POST['id'])) {
		echo json_encode(["error"=>"invalid_mess_id"]);
		return;
	}

	$messages = new Message($_POST['session']);
	echo json_encode($messages->getAllMessagesFrom($_POST['id']));


?>