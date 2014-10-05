<?php
	require_once("class/class_database.php");
	if(!isset($_POST['data'])) {
		echo json_encode(["error"=>"invalid_data"]);
		return;
	}
	$data = $_POST['data'];

	$session_id = md5(uniqid());
	$session_password = $data['password'];

	$newSessionQuery = new Database();
	$newSessionQuery->preparedQuery("INSERT INTO `sessions` (`id`, `sessionid`, `password`, `date`, `host`, `hostuid`, `finished`) VALUES (NULL ,  ?,  ?,  '2014-09-04 00:00:00',  ?,  ?, '0');", array($session_id, $session_password, "", ""));

	echo json_encode(["status"=>"complete", "sessionid"=>$session_id]);
?>