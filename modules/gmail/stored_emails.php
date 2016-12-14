<?php
require_once("config.php");
if((int)$_GET["user"] == 0)
{
	die("Invalid User Account");
}
$mquery = "SELECT * FROM ".DBTableName::Users." WHERE id = ".(int)$_GET["user"];
$user = $db->fetchRow($mquery);
if(!$user)
{
	die("Invalid User Account");
}
$authUrl = $client->createAuthUrl();
$query = "SELECT et.*, (SELECT count(id) FROM ".DBTableName::Messages." WHERE thread_id = et.thread_id) as msgCount FROM ".DBTableName::Threads." et WHERE et.email = '".$user["email"]."' ORDER BY et.last_message_date DESC";
$rows = $db->fetchRows($query);
?>
<a href="<?php echo BASE_URL?>fetch.php?action=new&user=<?php echo (int)$_GET["user"];?>">Click Here Fetch New Emails</a> <br />
<a href="<?php echo $authUrl?>">Re-Authenticate</a> <br />
<a href="<?php echo BASE_URL?>">Back</a>
<table border="1">
<thead>
<th><span style="width: 150px; display: block;">From</span></th>
<th><span style="width: 150px; display: block;">Subject</span></th>
<th><span style="width: 150px; display: block;">Date</span></th>
</thead>
<?php
foreach($rows as $row){
?>
<tr>
<td>
<?php
$msgCountStr = "";
if((int)$row["msgCount"] > 1) $msgCountStr = " (".$row["msgCount"].")";
?>
<a href="<?php echo BASE_URL?>mail_content.php?thread=<?php echo $row["thread_id"];?>"><?php echo $row["from_email"];?><?php echo $msgCountStr;?></a>
</td>
<td>
<a href="<?php echo BASE_URL?>mail_content.php?thread=<?php echo $row["thread_id"];?>"><?php echo $row["snippet_text"];?></a>
</td>
<td>
<?php echo date("D d M, Y h:i:s A",strtotime($row["last_message_date"]));?>
</td>
</tr>
<?php
}
?>
</table>
<style type="text/css">
	a{
		text-decoration: none;
    	font-family: monospace;
	}
</style>
