<?php
require_once("config.php");
if(strtolower($_GET["action"]) == "new" && (int)$_GET["user"] != 0){
	$user_data = getRowDataById(DBTableName::Users, $_GET["user"]);
	if(empty($user_data)){
		die("Invalid User");
	}
	$client = getUpdatedClient($client, (int)$_GET["user"]);
	$service = getGmailService($client);
	$threads = getAllThreads($service, $user_data["email"]);
	//echo "<pre>";print_r($threads);echo "</pre><br><hr><br>";//exit;
	
	$threads_data = array();

	foreach($threads as $thread){
		$thread_data = getThread($service, $user_data["email"], $thread->getId());
		//if($thread->getId()<>"158ceb3c858fcb93" && $thread->getId()<>"158ceafc566fefb4") continue;
		//echo "<pre>";print_r($thread_data);echo "</pre><br><hr><br>";continue;
		//$threads_data[] = $thread_data;
		
		$existed = getRowDataByField(DBTableName::Threads, "thread_id", $thread->getId());
		if(empty($existed)){
			$query = "INSERT INTO ".DBTableName::Threads." SET thread_id = '".$thread->getId()."', snippet_text = '".$thread->getSnippet()."', history_id = '".$thread->getHistoryId()."', email = '".$user_data["email"]."'";
			$db->getResult($query);
			$existed = getRowDataByField(DBTableName::Threads, "thread_id", $thread->getId());
		}

		$messages = $thread_data->getMessages();
		//echo "Messages Count: ".count($messages).'-'.$thread->getId();
		echo "Messages Count: ".count($messages);
		foreach($messages as $msg){
			$msgExist = getRowDataByField(DBTableName::Messages, "message_id", $msg->getId());
			if(!empty($msgExist)){
				echo "Existed<br/>";
				continue;
			}

			$optParamsGet = [];
			$optParamsGet['format'] = 'full';
			$message = $service->users_messages->get('me',$msg->getId(),$optParamsGet);
			$headers = $message->getPayload()->getHeaders();
			$parts = $message->getPayload()->getParts();

			$email_date = "";
			$subject = "";
			$from = "";
			$index = 0;
			$fromIndex = -1;
			$to = "";
			$toIndex = -1;
			foreach($headers as $header){
				if($header->name == "Date") $email_date = $header->value;
				if($header->name == "Subject") $subject = $header->value;
				if($header->name == "From") 
				{
					$fromIndex = $index;
					$from = $header->value;
				}
				if($header->name == "To") 
				{
					$toIndex = $index;
					$to = $header->value;
				}
				$index++;
			}
			if($fromIndex > -1)
			{
				$fromData = $message->payload["modelData"]["headers"][$fromIndex]["value"];
				$from = htmlentities($fromData, ENT_QUOTES);
			}
			if($toIndex > -1)
			{
				$toData = $message->payload["modelData"]["headers"][$toIndex]["value"];
				$to = htmlentities($toData, ENT_QUOTES);
			}

			if(strtotime($email_date) > strtotime($existed["last_message_date"]))
			{
				//$th_update_query = "UPDATE ".DBTableName::Threads." SET snippet_text = '".$db->escapeString($subject)." - ".$thread->getSnippet()."', from_email = '".$from."', last_message_date = '".date("Y-m-d H:i:s", strtotime($email_date))."' WHERE thread_id = '".$thread->getId()."'";
				$th_update_query = "UPDATE ".DBTableName::Threads." SET snippet_text = '".$db->escapeString($subject)."', from_email = '".$from."', last_message_date = '".date("Y-m-d H:i:s", strtotime($email_date))."' WHERE thread_id = '".$thread->getId()."'";
				$db->getResult($th_update_query);
			}
			else
			{
				$th_update_query = "UPDATE ".DBTableName::Threads." SET from_email = '".$from."' WHERE thread_id = '".$thread->getId()."'";
				$db->getResult($th_update_query);
			}
			$parts2 = $parts['parts'];
			//echo "1. <pre>";print_r($parts);echo "</pre>-----------**********------<hr>";
			//echo "1. <pre>";print_r($parts2);echo "</pre>-----------**********------<hr>";
			$body = "";	$attachments = array();
			for($i = 0; $i < count($parts); $i++){
				//echo "2. <pre>";print_r($parts[$i]);echo "</pre><hr>";
				//echo "<br>3. MT: ".$parts[$i]->mimeType;
				if($parts[$i]->mimeType != "text/plain" && $parts[$i]->mimeType != "text/html" && $parts[$i]->mimeType != "multipart/alternative")
				{
					$attachments[] = $parts[$i]; 
					//echo "<br>4. AMT: ".$parts[$i]->mimeType;
				}
				if($parts[$i]->mimeType == "text/html" || $parts[$i]->mimeType == "text/plain")
				{
					$body = $parts[$i]['body'];
					//echo "<br>5. BMT: ".$parts[$i]->mimeType;
				}
				if($parts[$i]->mimeType == "multipart/alternative")
				{
					$subParts = $parts[$i][parts];
					//echo "SB. <pre>";print_r($subParts);echo "</pre><br><hr><br>SB";
					//exit;
					foreach($subParts as $sbpart) {
						if($sbpart->mimeType == "text/plain")
						{
							$body = $sbpart['body'];
							//echo "<br>5. BMT: ".$parts[$i]->mimeType;
						}
						//echo "<pre>";print_r($sbpart);echo "</pre>";
						//echo "P:- <pre>";echo (base64_decode(strtr($sbpart['body']->data,'-_', '+/')));echo "</pre>";		
					}//exit;
					//$body = $parts[$i]['body'];
					//echo "<br>5. BMT: ".$parts[$i]->mimeType;
				}
				
				//echo "P:- <pre>";echo (base64_decode(strtr($parts[$i]['body']->data,'-_', '+/')));echo "</pre>";
			}
			//continue;
			//echo "6. <pre>";print_r($body);echo "</pre><br><hr><br>";
			//echo "7. <pre>";print_r($attachments);echo "</pre><br><hr><br>";
			$rawData = $body->data;
			$sanitizedData = strtr($rawData,'-_', '+/');
			if(!$sanitizedData){
				$sanitizedData = strtr($message->payload['modelData']['body']['data'],'-_', '+/');
			}

			//echo base64_decode($sanitizedData);

			foreach($attachments as $attachment){
				$existed_att = $db->fetchRow("SELECT * FROM ".DBTableName::Attachments." WHERE message_id='".$msg->getId()."' AND file_name = '".$attachment['filename']."'");
				if(!empty($existed_att)){
					echo "Existed Attachment";
					continue;
				}
				$attachmentData = $service->users_messages_attachments->get('me', $msg->getId(), $attachment['body']['attachmentId']);
				$decodedData = strtr($attachmentData['data'], array('-' => '+', '_' => '/'));
				$output_file = "attachments/".$msg->getId()."_".$attachment['filename'];
				$ifp = fopen($output_file, "wb"); 
				fwrite($ifp, base64_decode($decodedData));
				fclose($ifp);
				$db->getResult("INSERT INTO ".DBTableName::Attachments." SET message_id = '".$msg->getId()."', file_name = '".$attachment['filename']."', email = '".$user_data["email"]."', attachment_id= '".$attachment['body']['attachmentId']."'");
			}

			$queryU =  "INSERT INTO ".DBTableName::Messages." SET thread_id = '".$thread->getId()."', message_id = '".$msg->getId()."', `message_date` = '".date("Y-m-d H:i:s", strtotime($email_date))."', mail_content='".$db->escapeString($sanitizedData)."', `from_email` = '".$db->escapeString($from)."', to_email = '".$db->escapeString($to)."', subject = '".$db->escapeString($subject)."'";
			$db->getResult($queryU);
		}
		echo "<br/>-----------------Thread Processed ID: ".$thread->getId()."------------------------------<br/>";
	}
	
} 
exit;

echo "<meta http-equiv='refresh' content='0;url=http://usonmoon.com/fm/stored_emails.php?user=".(int)$_GET['user']."'>";
?>