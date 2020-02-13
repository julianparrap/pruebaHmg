<?php
require 'plcolab.php';
use PLColab\ApiClient;

$RootUri = "https://plcolabbeta.azure-api.net";
$User = '821002693';
$Password = 'abc123$$';
$SubscriptionKey = 'de5da094f12c47d38a8aa676f4c59a93';

$client = new ApiClient($RootUri, $User, $Password, $SubscriptionKey);
//$json = $client->login();
$token = $client->findToken();

echo "<br/>RESPONSE (TokenIsCached: " . $client->getTokenIsCached() . "):<br/>";
?>
<textarea rows="20" cols="120">
<?php
	echo $token;
    /*
	echo json_encode(
		$json,
		JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	);*/
?>
</textarea>