<?php
	require_once("class_database.php");
	class NewMessage{
		public function __construct($uid, $content, $date, $session){
			$content = html_entity_decode($content);
			$content = explode(" ", $content);

			$this->uid = $uid;
			$this->content = $content[0];
			$this->session = $session;
		}

		public function insertMessage(){
			$insertMessageQuery = new Database();
			$result = $insertMessageQuery->preparedQuery("INSERT INTO `messages` (`id`, `senderuid`, `content`, `sessionid`) VALUES (NULL, ?, ?, ?)", array($this->uid, $this->content, $this->session));
			if($result){
				echo json_encode(["status"=>"complete"]);
			}
		}
	}

	class Message{
		public function __construct($session){
			$this->session = $session;

		}

		public function  getAllMessagesFrom($id){
			$getMessagesQuery = new Database();
			$result = $getMessagesQuery->preparedQuery("SELECT * FROM  `messages` WHERE  `id` > ? AND `sessionid` LIKE  ?", array($id, $this->session))->fetchAll(PDO::FETCH_ASSOC);
			return $result;
		}

		public function getAllMessages(){
			$getAllMessagesQuery = new Database();
			$result = $getAllMessagesQuery->preparedQuery("SELECT * FROM `messages` WHERE sessionid = ?", array($this->session))->fetchAll(PDO::FETCH_ASSOC);
			return $result;
		}
	}
?>	