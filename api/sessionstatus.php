<?php
include('class/class_database.php');
session_start();

if(!isset($_SESSION['uid'])){
	echo json_encode(["error"=>"user_required"]);
	return;
} elseif (!isset($_POST['session'])) {
	echo json_encode(["error"=>"invalid_session"]);
	return;
}

$session = $_POST['session'];

$fetchAllDB = new Database();
$result = $fetchAllDB->preparedQuery("SELECT * FROM sessions WHERE sessionid=?", array($session))->fetchAll(PDO::FETCH_ASSOC);

if($result){
	foreach ($result as $key) {
		if($key['playeruid'] != ""){
			echo json_encode(["status"=>"1"]);
		} else{
			echo json_encode(["status"=>"0"]);
		}
	}
} 
