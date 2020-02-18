<?php 
	//error_reporting(0);
	require_once("../../back/BAXML-V20.php");
	$accion = trim($_REQUEST['accion']);
	
	switch($accion){
		//Función para generar factura
		case "generarFactura":
			$_SESSION["c_generaXml"]->generarFactura();
		break;
		//funcion para guardar la respuesta del json en la base de datos
		case "validarJson":
			//$_SESSION["c_generaXml"]->validarJson();
		break;
	}
?>
