<?php
include_once("mysql.php");

class Database{
	public function __construct(){
		$this->database = $GLOBALS['database'];
	}

	public function preparedQuery($query, $array){
		$prepQuery = $this->database->prepare($query);
		$prepQuery->execute($array);
		return $prepQuery;
	}

	public function fetchAll($query, $array){
		$database->prepare($query);
		$database->execute($array);
		return $database->fetchAll(PDO::FETCH_ASSOC); // might need to use $this->fetchAllQuery;
	}

	public function delete($table, $where, $equalTo){
		$database->prepare("DELETE FROM". $table ." WHERE ". $where ." = ?");
		$deleteQueryResult = $database->execute(array($equalTo));
		if($deleteQueryResult){
			return true;
		} else {
			return false;
		}
	}	
}