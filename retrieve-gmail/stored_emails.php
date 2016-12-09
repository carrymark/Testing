<?php
require_once("config.php");
if((int)$_GET["user"] == 0)
{
	die("Invalid Token");
}
$mquery = "SELECT * FROM ".DBTableName::Users." WHERE id = ".(int)$_GET["user"];
$user = $db->fetchRow($mquery);
$query = "SELECT et.*, (SELECT count(id) FROM ".DBTableName::Messages." WHERE thread_id = et.thread_id) as msgCount FROM ".DBTableName::Threads." et WHERE et.email = '".$user["email"]."' ORDER BY et.last_message_date DESC";
$rows = $db->fetchRows($query);
?>
<a href="fetch.php?action=new&user=<?php echo (int)$_GET["user"];?>">Click Here Fetch New Emails <span>(This action will be done in a Serverside Cron Job)</span></a>
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
<a href="mail_content.php?thread=<?php echo $row["thread_id"];?>"><?php echo $row["from_email"];?><?php echo $msgCountStr;?></a>
</td>
<td>
<a href="mail_content.php?thread=<?php echo $row["thread_id"];?>"><?php echo $row["snippet_text"];?></a>
</td>
<td>
<?php echo $row["last_message_date"];?>
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
