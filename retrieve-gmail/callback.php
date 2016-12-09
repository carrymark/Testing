<?php
require_once("config.php");

if(!isset($_GET["code"])){
	die("<pre>Token Not Found</pre>");
}
$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
$client->setAccessToken($token);

$service = new Google_Service_Gmail($client);
$userProfile = $service->users->getProfile("me");

$muquery = "SELECT * FROM ".DBTableName::Users." WHERE email = '".$userProfile->emailAddress."'";
$userData = $db->fetchRow($muquery);

if(!empty($userData)) $db->getResult("UPDATE ".DBTableName::Users." SET token = '".json_encode($token)."' WHERE email='".$userProfile->emailAddress."'");
else $db->getResult("INSERT INTO ".DBTableName::Users." SET token = '".json_encode($token)."', email='".$userProfile->emailAddress."'");

$userData = $db->fetchRow($muquery);
echo "<meta http-equiv='refresh' content='0;url=http://usonmoon.com/fm/fetch.php?action=new&user=".(int)$userData['id']."'>";
?>