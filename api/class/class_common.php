<?php
require_once("class_users.php");

class Common{
	public function __constructer(){
	}
	public function isUserAuthenticated(){
		if(!session_id()){
			session_start();
		}
		if(isset($_SESSION['steamid'])){
			return true;	

		} else{
			return false;
		}
	}
}
?>