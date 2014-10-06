<?php
	require_once("class/class_database.php");

	$session_id = md5(uniqid());

	$newSessionQuery = new Database();
	$newSessionQuery->preparedQuery("INSERT INTO `sessions` (`id`, `sessionid`, `password`, `date`, `host`, `hostuid`, `finished`) VALUES (NULL ,  ?,  '',  '2014-09-04 00:00:00',  ?,  ?, '0');", array($session_id, "", ""));

	echo json_encode(["status"=>"complete", "sessionid"=>$session_id]);
?>