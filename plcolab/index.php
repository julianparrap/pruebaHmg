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
	echo $formattedOutput;
	//funcion para guardar la respuesta del json en la base de datos
	function validarJson(){
		$fechaActual = date("Y-m-d H:i:s");
 		if (file_exists("../../plcolab/app-data/output/".$numeroDocumento.".json")) {
		//if (file_exists("../json/facturacion/SETT".$mcon_cons.".json")) {
			$output = file_get_contents("../../plcolab/app-data/output/".$numeroDocumento.".json");
			$arreglo = json_decode($output, true);
			//consultar cofa_maes
			//$sqlCofaMaes= $BDmysql->consultar("cofa_maes join movi_cons on(cofm_idxx=mcon_iddo)","cofm_idxx,cofm_idcp,mcon_idxx","mcon_idtc=14 and mcon_esta=2 and mcon_cons=".$mcon_cons,1);
			$datosCofaMaes= mysqli_fetch_array($sqlCofaMaes);
			$redl_idli= $arreglo["requestId"];

			$redc_reid= $arreglo["requestId"];
			$redc_cufe= $arreglo["UUID"];
			$redc_ufac= $arreglo["URL"];
			$redc_updf=	$arreglo["UrlPdf"];
			$redc_uxml=	$arreglo["UrlXml"];
			$redc_uack=	$arreglo["AckXml"];
			$redc_fpdf=	$arreglo["pdfFileName"];
			$redc_fxml=	$arreglo["xmlFileName"];
			$BDmysql->insertar("redi_cofa","redc_idem,redc_idco,redc_idcl,redc_idcf,redc_reid,redc_cufe,redc_ufac,redc_updf,redc_uxml,redc_uack,redc_fpdf,redc_fxml,redc_fech,redc_idus","".$_SESSION["empm_idxx"].",".$datosCofaMaes["cofm_idxx"].",".$datosCofaMaes["cofm_idcp"].",".$datosCofaMaes["mcon_idxx"].",'".$redc_reid."','".$redc_cufe."','".$redc_ufac."','".$redc_updf."','".$redc_uxml."','".$redc_uack."','".$redc_fpdf."','".$redc_fxml."',".$_SESSION["usum_idxx"]);
			$BDmysql->actualizar("cofa_maes","cofm_vafe='S'","cofm_idxx=".$datosCofaMaes["cofm_idxx"]);
		}
		else{
			$sqlCofaMaes= $BDmysql->consultar("cofa_maes join movi_cons on(cofm_idxx=mcon_iddo)","cofm_idxx,cofm_idcp,mcon_idxx","mcon_idtc=14 and mcon_esta=2 and mcon_cons=".$mcon_cons,1);
		$datosCofaMaes= mysqli_fetch_array($sqlCofaMaes);
		$BDmysql->actualizar("cofa_maes","cofm_vafe='E'","cofm_idxx=".$datosCofaMaes["cofm_idxx"]);
	}	





	function validarJson(){
		extract($_POST);
 		if (file_exists("../../plcolab/app-data/output/".$numeroDocumento.".json")) {
			$output = file_get_contents("../../plcolab/app-data/output/SETT4.json");
			$arreglo = json_decode($output, true);
			echo "string".$arreglo["UUID"];
			echo "<br>".$arreglo["UrlPdf"];
 		}
 		else{
			echo "Error a la hora de generar el documento."; 			
 		}	
	}
?>
</textarea>
