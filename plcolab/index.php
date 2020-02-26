<?php
require 'plcolab.php';
use PLColab\ApiClient;

$RootUri = "https://plcolabbeta.azure-api.net";
$User = '860511055';
$Password = 'abc123$$';
$SubscriptionKey = 'de5da094f12c47d38a8aa676f4c59a93';

// tipo de documento a emitir: "FA", "NC", "ND", ...
$documentType = "FACTURA-UBL";
$lima_idxx = $_POST["lima_idxx"];
//$lima_idxx = $_POST["lima_idxx"];
$options = [];

//  EJEMPLO: el xml del documento debe ser generado por algun software
// WARNING: para efectos de la prueba, cada vez que emita, cambiar manualmente el numero de documento (nodo: /Factura/Cabecera/@Numero)
//$xml = file_get_contents('./app-data/input/factura-ejemplo602191.xml');
$xml = file_get_contents('./app-data/input/factura-ejemplo'.$lima_idxx.'.xml');
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
if ($json['documentNumber']=="") {
	$numeroDocumento = "error";
}
else{
	$numeroDocumento = $json['documentNumber'];
}
echo "string".$numeroDocumento;
$outputFilePath = './app-data/output/' . $numeroDocumento . '.json';
$formattedOutput = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

file_put_contents($outputFilePath, $formattedOutput);

echo "<br/>Response Saved to: " . $outputFilePath;
echo "<br/>RESPONSE (TokenIsCached: " . $client->getTokenIsCached() . "):<br/>";
?>
<textarea rows="20" cols="120">


<?php
	require_once("../cone/mysql.php");
	echo $formattedOutput;
	$obj_queryDB = new conectarBD();
	//funcion para guardar la respuesta del json en la base de datos
	$fechaActual = date("Y-m-d H:i:s");
	if (file_exists("app-data/output/".$numeroDocumento.".json") && $numeroDocumento != "error") {
		$output = file_get_contents("app-data/output/".$numeroDocumento.".json");
		$arreglo = json_decode($output, true);
		//consultar liem_maes
		$sqlLiemMaes = $obj_queryDB->consultar("lima_idxx, ped.idx as pedIdx, con.idx as conIdx", "liem_maes JOIN mpedidos as ped ON (lima_orco = ped.idx OR lima_liem = ped.pedido) join conse_web as con on (ped.idx_vend=con.idx)", "lima_liem=".$lima_idxx, 4);
		$datosLiemMaes= mysqli_fetch_array($sqlLiemMaes);
		$redl_idli= $datosLiemMaes["lima_idxx"];
		$redl_idmp= $datosLiemMaes["pedIdx"];
		$redl_idcf= $datosLiemMaes["conIdx"];
		$redl_reid= $arreglo["requestId"];
		$redl_cufe= $arreglo["UUID"];
		$redl_ufac= $arreglo["URL"];
		$redl_updf= $arreglo["UrlPdf"];
		$redl_uxml= $arreglo["UrlXml"];
		$redl_uack= $arreglo["AckXml"];
		$redl_fpdf= $arreglo["pdfFileName"];
		$redl_fxml= $arreglo["xmlFileName"];
		$redl_fech= $fechaActual;
		$redl_idus= 1;//$_SESSION["usum_idxx"];

		$obj_queryDB->insertar("redi_liem (redl_idli,redl_idmp,redl_idcf,redl_reid,redl_cufe,redl_ufac,redl_updf,redl_uxml,redl_uack,redl_fpdf,redl_fxml,redl_fech,redl_idus)","(".$redl_idli.",".$redl_idmp.",".$redl_idcf.",'".$redl_reid."','".$redl_cufe."','".$redl_ufac."','".$redl_updf."','".$redl_uxml."','".$redl_uack."','".$redl_fpdf."','".$redl_fxml."','".$redl_fech."',".$redl_idus.")");
		$obj_queryDB->actualizar("liem_maes","lima_vafe='S'","lima_idxx=".$datosLiemMaes["lima_idxx"]);
	}
	else{
		$obj_queryDB->actualizar("liem_maes","lima_vafe='E'","lima_liem=".$lima_idxx);
	}	
?>
</textarea>
