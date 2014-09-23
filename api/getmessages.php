<?php
	require_once("class/class_message.php");

	if(null == $_POST['session']){
		return;
	}

	$messages = new Message($_POST['session']);
	echo json_encode($messages->getAllMessagesFrom($_POST['id']));


?>