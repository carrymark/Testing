<?php
require_once("config.php");
$query = "SELECT * FROM ".DBTableName::Users." group by email order by created desc";
$rows = $db->fetchRows($query);
?>
<table border="1">
<thead>
<th>Select User</th>
</thead>
<?php
foreach($rows as $row){
?>
<tr>
<td>
<a href="stored_emails.php?user=<?php echo $row["id"];?>"><?php echo $row["email"];?></a>
</td>
</tr>
<?php
}
?>
</table>