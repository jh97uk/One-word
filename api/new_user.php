<?php
session_start();

if(isset($_SESSION['uid'])){
	echo json_encode(["uid"=>$_SESSION['uid']]);
	return;
}
	$userid = uniqid();

	$_SESSION['uid'] = $userid;

	echo json_encode(["status"=>"success", "uid"=>$userid]);

?>