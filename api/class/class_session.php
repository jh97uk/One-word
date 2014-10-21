<?php
	require_once("class_database.php");
	class Session{
	
		function createSession(){
			date_default_timezone_set('GMT'); 

			$session_id = md5(uniqid());

			$newSessionQuery = new Database();

			$date = date("Y-m-d h:m:s");

			$newSessionQuery->preparedQuery("INSERT INTO `sessions` (`id`, `sessionid`, `date`, `host`, `hostuid`, `finished`) VALUES (NULL, ?, ?, ?, ?, '0');", array($session_id, $date, "", ""));

			return $session_id;
		}

		function getSession($session){
			$fetchAllDB = new Database();
			$result = $fetchAllDB->preparedQuery("SELECT * FROM sessions WHERE sessionid = ?", array($session))->fetchAll(PDO::FETCH_ASSOC);
			return $result;
		}

		function getSessionStatus($session){
			$fetchAllDB = new Database();
			$result = $fetchAllDB->preparedQuery("SELECT * FROM sessions WHERE sessionid=?", array($session))->fetchAll(PDO::FETCH_ASSOC);

			if($result){
				foreach ($result as $key) {
					if($key['playeruid'] != ""){
						$status = ["status"=>"1"];
					} else{
						$status = ["status"=>"0"];
					}
				}
			} else {
				$status = ["error"=>"unknown"];
			}

			return $status;
		}

		function setHostOrClient($session){
			if(getSession($session)){
				foreach ($result as $key) {
					if($key['hostuid'] == "" or $key['hostuid'] == $client){
						$setHostQuery = $fetchAllDB->preparedQuery("UPDATE sessions SET hostuid = ? WHERE sessionid = ?", array($client, $session));
						return "test";
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
		}
		
		function joinSession($uid, $session){
			$fetchAllDB = new Database();
			$result = $fetchAllDB->preparedQuery("SELECT * FROM sessions WHERE sessionid = ?", array($session))->fetchAll(PDO::FETCH_ASSOC);
			
			$status = "";
			
			if($result){
				foreach ($result as $key) {
					if($key['hostuid'] == "" or $key['hostuid'] == $uid){
						$setHostQuery = $fetchAllDB->preparedQuery("UPDATE sessions SET hostuid = ? WHERE sessionid = ?", array($uid, $session));
						$status = "host";
					} elseif($key['playeruid'] == "" or $key['playeruid'] == $uid){
						$setClientQuery = $fetchAllDB->preparedQuery("UPDATE sessions SET playeruid = ?, started = 1 WHERE sessionid = ?", array($uid, $session));
						$status = "player";
					} else{
						$status = "spectator";
					}
				}
			} else {
				return false;	
			}

			return $status;
		}

		function getPlayers($session){
			$fetch = new Database(); // Bad name. Like all the databases in this file...
			$playersQuery = $fetch->preparedQuery("SELECT hostuid, playeruid FROM sessions WHERE sessionid = ?", array($session))->fetchAll(PDO::FETCH_ASSOC);
			$players = [];

			foreach ($playersQuery as $key) {
				$players["host"] = $playersQuery[0]['hostuid'];
				$players["player"] = $playersQuery[0]['playeruid'];
			}

			return $players;
		}
	}