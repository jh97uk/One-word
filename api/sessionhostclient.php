<?php
include('class/class_database.php');
if(!isset($_SESSION)){
	session_start();
}
if(!isset($_SESSION['uid'])){
	return;
}
if(!isset($_POST['session'])){
	echo json_encode(["error"=>"needs_session"]);
	return;
}
$session = $_POST['session'];
$client = $_SESSION['uid'];

$fetchAllDB = new Database();
$result = $fetchAllDB->preparedQuery("SELECT * FROM sessions WHERE sessionid = ?", array($session))->fetchAll(PDO::FETCH_ASSOC);
if($result){
	foreach ($result as $key) {
		if($key['hostuid'] == ""){
			$setHostQuery = $fetchAllDB->preparedQuery("UPDATE sessions SET hostuid = ? WHERE sessionid = ?", array($client, $session));
			echo json_encode(["user_status"=>"host"]);
		} else{
			$setClientQuery = $fetchAllDB->preparedQuery("UPDATE sessions SET playeruid = ?, started = 1 WHERE sessionid = ?", array($client, $session));
			echo json_encode(["user_status"=>"player"]);
		}
	}
} else {
	echo json_encode(["error"=>"invalid"]);
}


