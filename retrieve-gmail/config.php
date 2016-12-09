<?php
error_reporting(E_ALL);
ini_set('display_errors', ($_GET["debug"] == "9"));
require_once("functions.php");
class GmailConfig{
	const ClientID 			= "783145787038-krvrme9r9p5mbobm0bghn9l1n8kd1d04.apps.googleusercontent.com";
	const ProjectID 		= "gmail-beijing";
	const ClientSecret		= "03n3TYvBWeX650qB0uoxxtqF";
	const RedirectURI		= "http://usonmoon.com/fm/callback.php";
	const JavaScriptOrigins	= "http://usonmoon.com";
}

class DBConfig{
	const Host = "localhost";
	const Name = "usonmoon_gmaildb";
	const Pass = "az456789AZ";
	const User = "usonmoon_gmaildb";
}

class DBTableName{
	const Users 		= "users";
	const Threads	 	= "threads";
	const Messages	 	= "messages";
	const Attachments 	= "attachments";

}

class DB{
	var $con;
	function openConnection(){
		$this->con = mysqli_connect(DBConfig::Host, DBConfig::User, DBConfig::Pass, DBConfig::Name);
	}

	function getResult($query){
		return mysqli_query($this->con, $query);
	} 

	function fetchRow($query){
		$res = $this->getResult($query);
		$row = mysqli_fetch_assoc($res);
		return $row;
	}

	function fetchRows($query){
		$res = $this->getResult($query);
		$rows = array();
		while($row = mysqli_fetch_assoc($res)){
			$rows[] = $row;
		}
		return $rows;
	}

	function escapeString($string){
		return mysqli_real_escape_string($this->con, $string);
	}
}
$db = new DB();
$db->openConnection();
//define("PATH_CLIENT_SECRET", "client_secret_9596312.json");
define("PATH_CLIENT_SECRET", "client_id.json");
require_once("libs/google-api-php-client-2.1.0/vendor/autoload.php");

$client = new Google_Client();
$client->setAuthConfig(PATH_CLIENT_SECRET);
$client->addScope(array("https://mail.google.com/"));
?>