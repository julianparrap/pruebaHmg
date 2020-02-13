<?php
include_once("../../back/BMENU-V20.php");
$accion = $_REQUEST['accion'];
switch($accion) {
	case "cargarMenu": // cargar menu
		$_SESSION["m_func_menu"]->cargarMenu();
	break;
}
?>