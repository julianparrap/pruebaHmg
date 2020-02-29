<?php 
	error_reporting(0);
	include_once("../../back/BACONT-V20.php");
	$accion = trim($_REQUEST['accion']);
	
	switch($accion){
		case "mostrarContenido":
			$_SESSION["c_facturacion"]->mostrarContenido();
		break;
		case "factCliente":
			$_SESSION["c_facturacion"]->factCliente();
		break;
		case "agrgarFlete":
			$_SESSION["c_facturacion"]->agrgarFlete();
		break;
		case "buscar":
			$clma_codi = $_REQUEST["clma_codi"];
	    $_SESSION["c_facturacion"]->buscar($clma_codi);
		break;
		//exportar pedido a zeus
		case "exportar":
			$_SESSION["c_facturacion"]->exportar();
		break;
		case "generarContingencia":
			$_SESSION["c_facturacion"]->generarContingencia();
		break;
	}
?>
