<?php
require_once("config.php");
?>
<pre>
Authenticate with your gmail account
</pre>
<?php
$authUrl = $client->createAuthUrl();
?>
<pre>
<a href="<?php echo $authUrl;?>">Authenticate Here</a>
</pre>