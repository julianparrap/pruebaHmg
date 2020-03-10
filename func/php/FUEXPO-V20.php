<?php 
	error_reporting(0);
	include_once("../../back/BAEXPO-V20.php");
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
		//exportar pedido a zeus
		case "exportar":
			$_SESSION["c_facturacion"]->exportar();
		break;
	}
?>
