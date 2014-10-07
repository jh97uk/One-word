<?php
	require_once("class_database.php");
	class Session{
	
		function createSession(){
			$session_id = md5(uniqid());

			$newSessionQuery = new Database();
			$newSessionQuery->preparedQuery("INSERT INTO `sessions` (`id`, `sessionid`, `password`, `date`, `host`, `hostuid`, `finished`) VALUES (NULL ,  ?,  '',  '2014-09-04 00:00:00',  ?,  ?, '0');", array($session_id, "", ""));

			return $session_id;
		}

		function getSession($session){
			$fetchAllDB = new Database();
			$result = $fetchAllDB->preparedQuery("SELECT * FROM sessions WHERE sessionid = ?", array($session))->fetchAll(PDO::FETCH_ASSOC);
			return $result;
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
	}