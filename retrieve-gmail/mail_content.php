<?php
require_once("config.php");
if(!$_GET["thread"]){
	die("Invalid Thread");
}
$thread_data = getRowDataByField(DBTableName::Threads, "thread_id", $_GET["thread"]);
if(empty($thread_data)){
	die("Invalid Thread");
}
$messages = getRowsDataByField(DBTableName::Messages, "thread_id", $_GET["thread"],' order by message_date desc');

foreach($messages as $message){
	$attcountQuery = "SELECT count(*) as attCount FROM ".DBTableName::Attachments." WHERE message_id = '".$message["message_id"]."'";
	$attCountData = $db->fetchRow($attcountQuery);
?>
<message>
<date-field><?php echo $message["message_date"];?></date-field>
<from-field>From: <?php echo $message["from_email"];?></from-field>
<to-field>To: <?php echo $message["to_email"];?></to-field>
<subject-field>Subject: <?php echo $message["subject"];?></subject-field>
<hr />
<message-content>
<?php echo str_replace("\r\n", "<br/>", base64_decode($message["mail_content"]));?>	
</message-content>
<attachements>
<?php
if((int)$attCountData['attCount'] > 0){
	?>
	<br/>
	<h5>Attachments</h5>
	<?php
	$attQuery = "SELECT * FROM ".DBTableName::Attachments." WHERE message_id = '".$message["message_id"]."'";
	$attRows = $db->fetchRows($attQuery);
	foreach($attRows as $attRow){
		$parted = explode(".", $attRow["file_name"]);
		if(in_array($parted[count($parted) - 1], array("png", "jpg", "jpeg", "gif", "bmp"))){
			?>
			<a target="_blank" href="attachments/<?php echo $attRow["message_id"]."_".$attRow["file_name"];?>"><img width="100" src="attachments/<?php echo $attRow["message_id"]."_".$attRow["file_name"];?>"/></a><br/>
			<?php
		}
		else
		{
	?>
		<a target="_blank" href="attachments/<?php echo $attRow["message_id"]."_".$attRow["file_name"];?>"><?php echo $attRow["file_name"];?></a><br/>
	<?php
		}	
	}
}
?>
</attachements>
</message>
<hr /><hr />
<?php
}
?>
<style>
message, date-field, from-field, to-field, subject-field, message-content{
	display: block;
}
date-field, subject-field, from-field{
	height: auto;
}
message{
	border-bottom: 1px lightgray;
	border-style: outset;
	padding-bottom: 10px;
}
</style>