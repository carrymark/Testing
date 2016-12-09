<?php
function getUpdatedClient($client, $user_id){
	global $db;
	try {
		$query = "SELECT * FROM ".DBTableName::Users." WHERE id = ".$db->escapeString((int)$user_id);
		$user_data = $db->fetchRow($query);
		$token = (array)json_decode($user_data["token"]);
		$client->setAccessToken($token);
		return $client;
	} catch (Exception $e){
	    print 'An error occurred: ' . $e->getMessage();
	}
}

function getRowDataById($table_name, $id){
	global $db;
	try {
		$query = "SELECT * FROM ".$table_name." WHERE id = ".$db->escapeString($id);
		return $db->fetchRow($query);
	} catch (Exception $e){
	    print 'An error occurred: ' . $e->getMessage();
	}
}

function getRowDataByField($table_name, $field, $value){
	global $db;
	try {
		$query = "SELECT * FROM ".$table_name." WHERE `".$field."` = '".$db->escapeString($value)."'";
		return $db->fetchRow($query);
	} catch (Exception $e){
	    print 'An error occurred: ' . $e->getMessage();
	}
}

function getRowsDataByField($table_name, $field, $value,$sort=''){
	global $db;
	try {
		$query = "SELECT * FROM ".$table_name." WHERE `".$field."` = '".$db->escapeString($value)."' ".$sort;
		return $db->fetchRows($query);
	} catch (Exception $e){
	    print 'An error occurred: ' . $e->getMessage();
	}
}

function getGmailService($client){
	try {
		return new Google_Service_Gmail($client);
	} catch (Exception $e){
	    print 'An error occurred: ' . $e->getMessage();
	}
}

function getAllThreads($service, $user_email){
	try {
		$threads = array();
		$threadsResponse = $service->users_threads->listUsersThreads($user_email);
		$threads = array_merge($threads, $threadsResponse->getThreads());
		return $threads;
	} catch (Exception $e){
	    print 'An error occurred: ' . $e->getMessage();
	}
}

function getThread($service, $user_email, $threadId) {
	try {
		$thread = $service->users_threads->get($user_email, $threadId);
		return $thread;
	} catch (Exception $e){
		print 'An error occurred: ' . $e->getMessage();
	}
}
?>