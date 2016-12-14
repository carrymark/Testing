<?php
require_once("config.php");
$authUrl = $client->createAuthUrl();
$query = "SELECT * FROM ".DBTableName::Users." group by email order by created desc";
$rows = $db->fetchRows($query);
if($rows) {
?>
<b>GMAIL Accounts</b>
<table border="1">
    <thead>
	    <th>Select User</th>
    </thead>
    <tbody>
	<?php
    foreach($rows as $row){
    ?>
        <tr>
            <td>
            <a href="<?php echo BASE_URL?>stored_emails.php?user=<?php echo $row["id"];?>"><?php echo $row["email"];?></a>
            </td>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>
<?php }?>
<p><a href="<?php echo $authUrl;?>">Authenticate New Gmail Account Here</a></p>