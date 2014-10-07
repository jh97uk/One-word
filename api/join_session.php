<?php
include('class/class_database.php');
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

$fetchAllDB = new Database();
$result = $fetchAllDB->preparedQuery("SELECT * FROM sessions WHERE sessionid = ?", array($session))->fetchAll(PDO::FETCH_ASSOC);

if($result){
	foreach ($result as $key) {
		if($key['hostuid'] == "" or $key['hostuid'] == $client){
			$setHostQuery = $fetchAllDB->preparedQuery("UPDATE sessions SET hostuid = ? WHERE sessionid = ?", array($client, $session));
			echo json_encode(["user_status"=>"host"]);
		} elseif($key['playeruid'] == "" or $key['playeruid'] == $client){
			$setClientQuery = $fetchAllDB->preparedQuery("UPDATE sessions SET playeruid = ?, started = 1 WHERE sessionid = ?", array($client, $session));
			echo json_encode(["user_status"=>"player"]);
		} else{
			echo json_encode(["user_status"=>"spectator"]);
		}
	}
} else {
	echo json_encode(["error"=>"invalid"]);
}


