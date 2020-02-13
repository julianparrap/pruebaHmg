<!DOCTYPE html>
<html lang="es">
<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Responsive sidebar template with sliding effect and dropdown menu based on bootstrap 3">
		<title><?php echo $tituloFavicon; ?></title>
		<link rel="stylesheet" href="../func/css/releases5.css" />
		<link rel="stylesheet" href="../func/css/styleMenu.css" />
 		<link rel="stylesheet" href="../func/css/sweetalert2.css">
		<link rel="stylesheet" href="../func/css/bootstrap.min.css" />
		<script type="text/javascript" src="../func/js/bootstrap.min.js"></script>
		<script src='../func/js/jquery.min.js'></script>
		
		<script src="../func/js/jquery.dataTables.min.js" type="text/javascript"></script>
		<script src="../func/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
		<script src="../func/js/sweetalert2.js"></script>
		<script src="../func/js/validaciones.js"></script>
</head>
<script type="text/javascript">

	function cargarMenu(){
		$.ajax({
			type :"POST",
			url : "../func/php/FMENU-V20.php",
			data : "accion=cargarMenu",
			success:function(data){
				$("#sidebar").html(data);
			}
		});
	}
</script>
