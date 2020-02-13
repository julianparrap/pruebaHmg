<?php
require 'plcolab.php';
use PLColab\ApiClient;

$RootUri = "https://plcolabbeta.azure-api.net";
$User = '860511055';
$Password = 'abc123$$';
$SubscriptionKey = 'A807D996-6734-42DF-9A87-AB0B01323EB6';

// tipo de documento a emitir: "FA", "NC", "ND", ...
$documentType = "FA";
$options = [];

//  EJEMPLO: el xml del documento debe ser generado por algun software
// WARNING: para efectos de la prueba, cada vez que emita, cambiar manualmente el numero de documento (nodo: /Factura/Cabecera/@Numero)
$xml = file_get_contents('./app-data/input/factura-ejemplo.xml');
// EJEMPLO: esta es una de las maneras de especificar adjuntos
// WARNING: también se podrían pasar como bytes (habría que alterar el plcolab.php)
$attachmentPaths = [ './app-data/input/attachments/Financial Report.pdf',
					 './app-data/input/attachments/Purchase Order.pdf' ];

$client = new ApiClient($RootUri, $User, $Password, $SubscriptionKey);

// *****************************************************************
// *** WARNING: enable to check request on Fiddler Http Debugger ***
//$client->useProxy();
// *****************************************************************

$json = $client->Issue($documentType, $xml, $options, $attachmentPaths);

$outputFilePath = './app-data/output/' . $json['documentNumber'] . '.json';
$formattedOutput = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

file_put_contents($outputFilePath, $formattedOutput);

echo "<br/>Response Saved to: " . $outputFilePath;
echo "<br/>RESPONSE (TokenIsCached: " . $client->getTokenIsCached() . "):<br/>";
?>
<textarea rows="20" cols="120">
<?php
	echo $formattedOutput;
?>
</textarea>
