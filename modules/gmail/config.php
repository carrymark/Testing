<?php
error_reporting(E_ALL);
ini_set('display_errors', isset($_GET["debug"])?1:0);
define("BASE_URL", "/gmail/");
define("SITEURL", "http://miscapp.in".BASE_URL);
define("DOCROOT", $_SERVER['DOCUMENT_ROOT'].BASE_URL);
class GmailConfig{
	const ClientID 			= "783145787038-krvrme9r9p5mbobm0bghn9l1n8kd1d04.apps.googleusercontent.com";
	const ProjectID 		= "gmail-beijing";
	const ClientSecret		= "03n3TYvBWeX650qB0uoxxtqF";
	const RedirectURI		= SITEURL."callback.php";
	const JavaScriptOrigins	= SITEURL;
}

class DBConfig{
	const Host = "localhost";
	const Name = "miscapp_gmail";
	const Pass = "gmail@DB435";
	const User = "miscapp_gmailusr";
}

class DBTableName{
	const Users 		= "gmail_users";
	const Threads	 	= "gmail_threads";
	const Messages	 	= "gmail_messages";
	const Attachments 	= "gmail_attachments";
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
define("PATH_CLIENT_SECRET", "client_id.json");
require_once(DOCROOT."gmail_functions.php");
require_once(DOCROOT."libs/google-api-php-client-2.1.0/vendor/autoload.php");

$client = new Google_Client();
$client->setAuthConfig(PATH_CLIENT_SECRET);
$client->addScope(array("https://mail.google.com/"));
?>