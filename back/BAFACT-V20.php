<?php
/**
 * Modulo de facturacion 
 * Por: Julian Parra
*/
require_once("../../cone/mysql.php");
class facturacion extends conectarBD{
	//$fechaActual = date("Y-m-d");
	//
	public function mostrarContenido() {
		$fech = date("Y-m-d");
		$result = $this->consultar("idx,lima_idxx,fecha,lima_clie,estadowf,lima_orco,tp_entrega,lima_gude,cliente,count(idx) as cantidad,lima_vtpe,lima_vfle,lima_vdes,vriva", "mpedidos join liem_maes on(lima_liem=pedido or lima_orco=idx)", "(workflow='FINA' or lima_esta='FEL' or (workflow='FINA' and lima_orco!=0)) and lima_esta='REA' and DATE_FORMAT( `fecha` , '%Y-%m-%d' ) = '2020-01-17' GROUP BY (cliente) ORDER BY (fecha) limit 10", 4);
		//descomentarear cuando allán pedidos en estado de facturacion 
		//$result = $this->consultar("idx,fecha,lima_clie,estadowf,lima_orco,tp_entrega,lima_gude,cliente", "mpedidos join liem_maes on(lima_liem=pedido or lima_orco=idx)", "(workflow='FACT' or lima_esta='FEL' or (workflow='FINA' and lima_orco!=0)) and lima_esta='FAC' GROUP BY (cliente) ORDER BY (fecha) limit 100", 4);
		$pedi_manu = mysqli_num_rows($this->consultar("* ","liem_maes","lima_orco<'500000' and lima_esta='FAC' and cast(lima_fefa as DATE)='$fech'", 4));
		if($pedi_manu > 0){
			$img_fmanu="Fact_mp.png";
		}
		else{
			$img_fmanu="Fact_manual.png";
		}
		echo "
		<table class='table table-hover' style='width:500px'>
			<thead>
				<tr>
					<td style='width:170px;'><input type='text' class='form-control' name='clma_codi' id='clma_codi' placeholder='Codigo del cliente'></td>
					<td></td>
					<td><img src='../img/buscar.png' class='detalles' onclick='buscar()' title='Buscar cliente'></td>
					<td><img src='../img/exp_facturas.png' class='detalles' onclick='exportar()' title='Facturar'></td>
					<td><img src='../img/limpiar.png' class='detalles' onclick='contenido()' title='Limpiar'></td>
					<td><img src='../img/salir.png' class='detalles' onclick='javascrip:location.href=\"../../funciones/php/FSAL-V10.php\"' title='Salir'></td>
				</tr>
			</thead>
		</table>
		<hr>
		";
		echo "
			<form name='formulario' id='formulario'>
			<input type='hidden' name='pedidos' id='pedidos'>
			<div class='table-responsive-sm'>
			<table class='table table-striped'>
				<thead>
				<tr>
					<td><input type='checkbox' name='checkbox' id='todos' value='todos' onChange='seleccionar_todo()'></td>
					<td class='cabecera'>Codigo</td>
					<td class='cabecera'>Razon Social</td>
					<td class='cabecera'>Pedidos</td>
					<td class='cabecera'>Tiempo trans.</td>
					<td class='cabecera'>Tipo Entrega</td>
					<td class='cabecera'>Vr mercancía</td>
					<td class='cabecera'>Vr descuento</td>
					<td class='cabecera'>Vr flete</td>
					<td class='cabecera'>Vr iva</td>
					<td class='cabecera'>Vr total</td>
				</tr></thead><tbody>";
		while($datos = mysqli_fetch_array($result)){
			$sqlClieMaes = $this->consultar("clma_tipo,clma_noes,clma_raso,clma_codi", "clie_mae", "clma_esta='ACT' and clma_codi=".$datos["cliente"], 4);
			$datosClieMaes = mysqli_fetch_array($sqlClieMaes);
			
			if($datosClieMaes["clma_tipo"] == "NATURAL"){
				$nombre = $datosClieMaes["clma_noes"];
			}
			else{
				$nombre = $datosClieMaes["clma_raso"];
			}
			$causal = "Por Facturar";
			$estilo = "cuerpo";
			$estilo1 = "cuerpo_izq";
			$idxx = $datos["idx"];
			$codi = $datosClieMaes["clma_codi"];
			$fecha1 = new DateTime($datos["fecha"]);
			$fecha2 = new DateTime(date("Y-m-d H:i:s"));
			$fecha = $fecha1->diff($fecha2);
			$dias = $fecha->format('%D');
			$hora = $fecha->format('%H');
			$minu = $fecha->format('%I');
			if($dias > 0){
				$tiempo = "D ".$dias." ".$hora.":".$minu;
			}	 
			else{
				$tiempo = $hora.":".$minu;
			}	
			$lima_liem = $datos["lima_clie"];
			$lima_orco = $datos["lima_orco"];
			$estadowf = ($datos["estadowf"]=="Anulado" || $datos["estadowf"]=="Anuladoback")?"<td class='$estilo1' title='$causal'>".$datos["estadowf"]."</td>":"";
			$sqlCantidad = $this->consultar("pedido", "mpedidos join liem_maes on (pedido=lima_liem or idx=lima_orco) ", "workflow='FACT' and lima_esta='FAC' and estadowf!='Anulado' and cliente=".$datos["cliente"], 4);
			$total=($datos["lima_vtpe"]-$datos["lima_vtpe"])+$datos["lima_vfle"]+$datos["vriva"];
			echo "<tr>
					<td><input type='checkbox' name='checkbox' id='".$lima_liem."' value='".$lima_liem."' onChange='contar(1)'></td>
					<td class='$estilo' title='$causal'>".$datosClieMaes["clma_codi"]."</td>
					<td class='$estilo1' title='$causal'>".$nombre."</td>
					<td class='$estilo1' style='text-align:center' title='$causal'>".$datos["cantidad"]."</td>
					<td class='$estilo' title='$causal'>".$tiempo."</td>
					<td class='$estilo1' title='$causal'>".strtoupper($datos["tp_entrega"])."</td>
					<td class='$estilo' title='$causal'>".number_format($datos["lima_vtpe"])."</td>
					<td class='$estilo' title='$causal'>".number_format($datos["lima_vdes"])."</td>
					<td class='$estilo' title='$causal'>".number_format($datos["lima_vfle"])."</td>
					<td class='$estilo' title='$causal'>".number_format($datos["vriva"])."</td>
					<td class='$estilo' title='$causal'>".number_format($total)."</td>
				";
			/*if ($datos["lima_gude"]=="S"){
				echo"<td class='$estilo' title='$causal'>SI</td>";
			}
			else{
				echo"<td class='$estilo' title='$causal'>NO</td>";
			}*/
			$estadowf;
			if ($datos["cantidad"]==1) {
				echo "<td><img src='../img/cargar_flete.png' width='32px' class='detalles' onclick='agrgarFlete(".$datos["lima_idxx"].")' title='Agregar flete'></td>";			
			}
			if ($datos["lima_esta"]!='FEL') {
				if ($datos["cantidad"]!=1) {
					$LimaIdxx = "";
					$sqlLimaIdxx = $this->consultar("lima_idxx", "mpedidos join liem_maes on(lima_liem=pedido or lima_orco=idx)", "(workflow='FINA' or lima_esta='FEL' or (workflow='FINA' and lima_orco!=0)) and lima_clie=".$datos["lima_clie"]." and lima_esta='REA' and DATE_FORMAT( `fecha` , '%Y-%m-%d' ) = '2020-01-17'", 4);
					while ($datosLimaIdxx = mysqli_fetch_array($sqlLimaIdxx)) {
						$LimaIdxx = ($LimaIdxx=="")?$datosLimaIdxx["lima_idxx"]:$LimaIdxx."-".$datosLimaIdxx["lima_idxx"];
					}
					echo "<td><img src='../img/validar_dian.png' width='32px' class='detalles' onclick='valiFactElec(\"".$LimaIdxx."\",".$datos["cantidad"].",".$datos["lima_clie"].",\"$nombre\")' title='Facturar'></td>";
				}
				else{
					echo "<td><img src='../img/validar_dian.png' width='32px' class='detalles' onclick='valiFactElec(\"".$datos["lima_idxx"]."\",".$datos["cantidad"].",".$datos["lima_clie"].",\"$nombre\")' title='Facturar'></td>";			
				}
			}
			echo"</tr>";
		}
		echo "</tbody></table></div></form>";
	}

	//trae las facturas que se le han asignado al cliente 
	public function factCliente(){
		extract($_POST);
		$sqlMpedidos = $this->consultar("idx,fecha,lima_clie,estadowf,lima_orco,tp_entrega,lima_gude,lima_liem", "mpedidos join liem_maes on(lima_liem=pedido or lima_orco=idx)", "(workflow='FINA' or (workflow='FINA' and lima_orco!=0)) and lima_esta='REA' and DATE_FORMAT( `fecha` , '%Y-%m-%d' ) = '2020-01-17' and cliente=".$clma_codi." GROUP BY (cliente) ORDER BY (fecha)", 4);
		echo "<table class='table table-striped'>
				<thead>
				<tr>
					<td class='cabecera'>Num Pedido</td>
					<td class='cabecera'>Tiempo trans.</td>
					<td class='cabecera'>Tipo Entrega</td>
					<td class='cabecera'>Remision</td>
				</tr></thead><tbody>";
		while ($datosMpedidos = mysqli_fetch_array($sqlMpedidos)) {
			$causal = "Por Facturar";
			$estilo = "cuerpo";
			$estilo1 = "cuerpo_izq";
			$idxx = $datos["idx"];
			$codi = $datosClieMaes["clma_codi"];
			$fecha1 = new DateTime($datos["fecha"]);
			$fecha2 = new DateTime(date("Y-m-d H:i:s"));
			$fecha = $fecha1->diff($fecha2);
			$dias = $fecha->format('%D');
			$hora = $fecha->format('%H');
			$minu = $fecha->format('%I');
			if($dias > 0){
				$tiempo = "D ".$dias." ".$hora.":".$minu;
			}	 
			else{
				$tiempo = $hora.":".$minu;
			}	
			$lima_liem = $datos["lima_clie"];
			$lima_orco = $datos["lima_orco"];
			$estadowf = ($datos["estadowf"]=="Anulado" || $datos["estadowf"]=="Anuladoback")?"<td class='$estilo1' title='$causal'>".$datos["estadowf"]."</td>":"";
			$sqlCantidad = $this->consultar("pedido", "mpedidos join liem_maes on (pedido=lima_liem or idx=lima_orco) ", "workflow='FACT' and lima_esta='FAC' and estadowf!='Anulado' and cliente=".$datos["cliente"], 4);

			echo "<tr>
					<td class='$estilo' title='$causal'>".$datosMpedidos["lima_liem"]."</td>
					<td class='$estilo1' style='text-align:center' title='$causal'>".mysqli_num_rows($sqlCantidad)."</td>
					<td class='$estilo' title='$causal'>".$tiempo."</td>
					<td class='$estilo1' title='$causal'>".strtoupper($datos["tp_entrega"])."</td>
				";
			if ($datos["lima_gude"]=="S"){
				echo"<td class='$estilo' title='$causal'>SI</td>";
			}
			else{
				echo"<td class='$estilo' title='$causal'>NO</td>";
			}
			$estadowf;
			echo "<td><img src='../img/facturar.png' class='detalles' onclick='exportar(".$datos["idx"].",".$datos["cantidad"].",".$datos["lima_clie"].",\"$nombre\")' title='Facturar'></td>";	
			echo "<td><img src='../img/flete.png' width='32px' class='detalles' onclick='agrgarFlete(".$datos["lima_idxx"].")' title='Agregar flete'></td>";
			echo"</tr>";
		}
		echo "</tbody></table></form>";
	}

	//agrgarFlete
	public function agrgarFlete(){
		extract($_POST);
		$this->actualizar("liem_maes", "lima_vfle='".$lima_vfle."',lima_vdfl='".$lima_vdfl."'", "lima_idxx=".$lima_idxx);
	}

	//exportar pedido a zeus
	public function exportar(){
		extract($_POST);
	 	$idx_pedido = ""; 
	 	$t_ped = 0;
	 	$t_ref = 0;
	 	//home/hmg/html/asesores/mysql/claseMysql-V20.php
	 	$morden = fopen("u:/mordencompra.txt","w") or die("No se encontro la ruta para la exportacion");
	 	$dorden = fopen("u:/dordencompra.txt","w") or die("No se encontro la ruta para la exportacion");
		$listas = explode("-", $cliente);
		$cantidad_listas = count($listas);
		$i = 0;
		for($carro=0; $carro<$cantidad_listas; $carro++){
		 	$result = $this->consultar("*","mpedidos join liem_maes on (pedido=lima_liem or idx=lima_orco)","cliente = ".$listas[$carro]." and (workflow='FACT' or (workflow='FINA' and lima_orco!=0 and lima_esta='FAC')) and lima_clie=".$listas[$carro]." and pedido != '0' order by (idx) desc",4);		
		 	$tclie = " "; 
		 	$cantPedidos = mysqli_num_rows($result);
		 	$cantPedidosAct =0;
		 	mysqli_data_seek($result,0);
			while($maestro = mysqli_fetch_array($result)){
				$cantPedidosAct = $cantPedidosAct+1;
				$codigo = $maestro['cliente'];
				$idx_pedido = $maestro['idx'];
						 
				if($maestro['cliente'] == $tclie){
					//$matriz[4][$i-1] = 99;
			 	}
			 
			 	$tclie = $maestro['cliente']; 
					
			 	$obser = $maestro["observacion"];
				if($obser == "No hay observaciones"){
					$observacion = " ";
				}
				else{
					$observacion = preg_replace("[\n|\r|\n\r]", ' ', $maestro["observacion"]);
				}
				$fecha = date("Ymd");

				$sqlLiemMaes = $this->consultar("*","liem_maes","lima_liem=".$maestro["pedido"]." or lima_orco=".$maestro["idx"]." order by(lima_idxx) desc",4);
				//$sqlLiemMaes = consultar("*","liem_maes","lima_liem=".$maestro["pedido"]." order by(lima_idxx) desc",1);
				$datosLiemMaes = mysqli_fetch_array($sqlLiemMaes);

				$matriz[0][$i] = $maestro['cliente'];
				$matriz[1][$i] = $datosLiemMaes["lima_idxx"];
				$matriz[2][$i] = $maestro["vend"];
				$matriz[3][$i] = $fecha;
				//tipo pedido
				if ($maestro["tp_entrega"] =="PedidoExpress") {
					$matriz[4][$i] = 1;
				}
				else{
					$matriz[4][$i] = 0;
				}	
				
				$matriz[5][$i] = $datosLiemMaes["lima_liem"];
				//codigo transportadora
				if ($maestro["tp_entrega"]=="PedidoExpress") {
					$matriz[6][$i] = $maestro["idx_transport"];
				}
				else{
					$sqlPesoLiemVali = $this->consultar("*","peso_liem","pesl_idle=".$datosLiemMaes["lima_idxx"],4);
					if (mysqli_num_rows($sqlPesoLiemVali)>0) {
						$transportadora = strtoupper($maestro["idx_transport"]);
						switch ($transportadora) {
							case 47:
								mysqli_data_seek($sqlPesoLiemVali,0);
								$datosPesoLiemVali = mysqli_fetch_array($sqlPesoLiemVali);
								$pesoTotalVali=intval($datosPesoLiemVali["pesl_pes1"]+$datosPesoLiemVali["pesl_pes2"]+$datosPesoLiemVali["pesl_pes3"]+$datosPesoLiemVali["pesl_pes4"]+$datosPesoLiemVali["pesl_pes5"]+$datosPesoLiemVali["pesl_pes6"]+$datosPesoLiemVali["pesl_pes7"]+$datosPesoLiemVali["pesl_pes8"]+$datosPesoLiemVali["pesl_pes9"]+$datosPesoLiemVali["pesl_pes10"]);

								if ($pesoTotalVali>=1 && $pesoTotalVali<=9) {
									$matriz[6][$i] = 47;
								}
								if ($pesoTotalVali>=10 && $pesoTotalVali<=19) {
									$matriz[6][$i] = 48;
								}
								if ($pesoTotalVali>=20 && $pesoTotalVali<=29) {
									$matriz[6][$i] = 49;
								}
								if ($pesoTotalVali>=30) {
									$matriz[6][$i] = 50;
								}
							break;
							default:
								$matriz[6][$i] = $maestro["idx_transport"];
							break;
						}
					}
					else{
						$matriz[6][$i] = 99;
					}
				}

				//forma de pago( cdigo de descuento copa_clie)
				$sqlCopaClie = $this->consultar("*","copa_clie","copc_idxx=".$maestro["descuento"],4);
				$datosCopaClie = mysqli_fetch_array($sqlCopaClie);
				$matriz[7][$i] = $datosCopaClie["copc_codi"];

					$matriz[8][$i] = 0;
					$pesoTotal=0;
					$pesoUno=0;
					$pesoDos=0;
					$pesoTre=0;
					$pesoCua=0;
					$pesoCin=0;
					$pesoSei=0;
					$pesoSie=0;
					$pesoOch=0;
					$pesoNue=0;
					$pesoDie=0;
				if ($maestro["tp_entrega"] !="PedidoExpress") {
				 	$sqlPesoLiem = $this->consultar("*","peso_liem","pesl_idle=".$datosLiemMaes["lima_idxx"],4);
					$datosPesoLiem = mysqli_fetch_array($sqlPesoLiem);
					$matriz[8][$i] = ($datosPesoLiem["pesl_nuca"]=="")?0:$datosPesoLiem["pesl_nuca"];
					//peso
					$pesoTotal=intval($datosPesoLiem["pesl_pes1"]+$datosPesoLiem["pesl_pes2"]+$datosPesoLiem["pesl_pes3"]+$datosPesoLiem["pesl_pes4"]+$datosPesoLiem["pesl_pes5"]+$datosPesoLiem["pesl_pes6"]+$datosPesoLiem["pesl_pes7"]+$datosPesoLiem["pesl_pes8"]+$datosPesoLiem["pesl_pes9"]+$datosPesoLiem["pesl_pes10"]);
					$pesoUno=intval($datosPesoLiem["pesl_pes1"]);
					$pesoDos=intval($datosPesoLiem["pesl_pes2"]);
					$pesoTre=intval($datosPesoLiem["pesl_pes3"]);
					$pesoCua=intval($datosPesoLiem["pesl_pes4"]);
					$pesoCin=intval($datosPesoLiem["pesl_pes5"]);
					$pesoSei=intval($datosPesoLiem["pesl_pes6"]);
					$pesoSie=intval($datosPesoLiem["pesl_pes7"]);
					$pesoOch=intval($datosPesoLiem["pesl_pes8"]);
					$pesoNue=intval($datosPesoLiem["pesl_pes9"]);
					$pesoDie=intval($datosPesoLiem["pesl_pes10"]);
				}
				$matriz[9][$i] = ($pesoTotal=="")?0:$pesoTotal;
				$matriz[10][$i] = 0;
				$matriz[11][$i] =($pesoUno=="")?0:$pesoUno;
				$matriz[12][$i] =($pesoDos=="")?0:$pesoDos;
				$matriz[13][$i] =($pesoTre=="")?0:$pesoTre;
				$matriz[14][$i] =($pesoCua=="")?0:$pesoCua;
				$matriz[15][$i] =($pesoCin=="")?0:$pesoCin;
				$matriz[16][$i] =($pesoSei=="")?0:$pesoSei;
				$matriz[17][$i] =($pesoSie=="")?0:$pesoSie;
				$matriz[18][$i] =($pesoOch=="")?0:$pesoOch;
				$matriz[19][$i] =($pesoNue=="")?0:$pesoNue;
				$matriz[20][$i] =($pesoDie=="")?0:$pesoDie;
				//cancelado
				
				$matriz[21][$i] = ($maestro["estadowf"]=="Anulado" || $maestro["estadowf"]=="Anuladoback")?1:0;
				$matriz[22][$i] = $idx_pedido;
			 	$i++;
				$t_ped++;
			 	$fech_exp = date("Y-m-d H:i:s");
			 	if ($datosLiemMaes["lima_orco"]==0) {
//			 		actualizar("mpedidos", "workflow='FINA',fechto_fina='$fech_exp'", "pedido=".$datosLiemMaes["lima_liem"]);
			 	}
			 	else{
//			 		actualizar("mpedidos", "workflow='FINA',fechto_fina='$fech_exp'", "idx=".$datosLiemMaes["lima_orco"]);
			 	}
//			 	actualizar("liem_maes", "lima_esta='REA',lima_fefa='$fech_exp'", "lima_idxx=".$datosLiemMaes["lima_idxx"]);
			}
		}
		 
		for($a = 0; $a < $i; $a++){
			$text = $matriz[1][$a]."@".$matriz[0][$a]."@".$matriz[2][$a]."@".$matriz[3][$a]."@".$matriz[4][$a]."@".$matriz[5][$a]."@".$matriz[6][$a]."@".$matriz[7][$a]."@".$matriz[8][$a]."@".$matriz[9][$a]."@".$matriz[10][$a]."@".$matriz[11][$a]."@".$matriz[12][$a]."@".$matriz[13][$a]."@".$matriz[14][$a]."@".$matriz[15][$a]."@".$matriz[16][$a]."@".$matriz[17][$a]."@".$matriz[18][$a]."@".$matriz[19][$a]."@".$matriz[20][$a]."@".$matriz[21][$a];
			fputs($morden,$text."\r\n");
		
			$item = 1;    
			$sqlDetaLiem = $this->consultar("deli_caso,deli_caap,deli_prec,deli_dife,deli_idxx,deli_idre","deta_liem ","deli_idle='".$matriz[1][$a]."'",4);
			while($datosDetaLiem = mysqli_fetch_array($sqlDetaLiem)){
				$sqlRefeMaes = $this->consultar("*","refe_maes","rema_idxx=".$datosDetaLiem["deli_idre"],4); 
				//$consult = consultar("*","dpedidos","pedido ='".$matriz[5][$a]."' and idx_refe=".$datosDetaLiem["deli_idre"],4); 
				$detalleRefeMaes = mysqli_fetch_array($sqlRefeMaes);
				$text = $matriz[1][$a]."@".$matriz[0][$a]."@".$item."@".$detalleRefeMaes["rema_line"]."@".trim($detalleRefeMaes["rema_oem"])."@".$datosDetaLiem["deli_caso"]."@".$datosDetaLiem["deli_prec"]."@".$datosDetaLiem["deli_caap"]."@".$datosDetaLiem["deli_dife"];
				fputs($dorden,$text."\r\n");
				$t_ref++;
			 	$item++;
			}
		}
		fclose($dorden); 
		fclose($morden);
		
		$fecha = date("Y-m-d H:i:s");
		if($idx_pedido != ""){
			/*$tabla="exportar_oc(idx_mpedido,fecha_exp,cant_oc,cant_refe)";
			$valores="(\"$idx_pedido\",\"$fecha\",\"$t_ped\",\"$t_ref\")";
			insertar($tabla,$valores);*/
			echo "Exportacion exitosa";
		}
		else{
			echo "No se encontraron registros para exportar";
		}
	}
}

@session_start(); 
if(!isset($_SESSION["c_facturacion"])){
	$_SESSION["c_facturacion"] = new facturacion();
}

		/*$pedi_manu = mysql_num_rows(consultar("* ","liem_maes","lima_orco<'500000' and lima_esta='FAC' and cast(lima_fefa as DATE)='$fech'", 4));
		if($pedi_manu > 0){
			$img_fmanu="Fact_mp.png";
		}
		else{
			$img_fmanu="Fact_manual.png";
		}
		echo "<table>
				<tr>
					<td>Codigo del cliente</td>
			<td></td>
			<td style='width:50px;'><input type='text' name='clma_codi' id='clma_codi'></td>
			<td></td>
			<td><img src='../../images/buscar.png' class='detalles' onclick='buscar()' title='Buscar cliente'></td>
			<td><img src='../../images/limpiar.png' class='detalles' onclick='contenido()' title='Limpiar'></td>
			<td><img src='../../images/facturar.png' class='detalles' onclick='exportar()' title='Facturar'></td>
			
			<td><img src='../../images/salir.png' class='detalles' onclick='javascrip:location.href=\"../../funciones/php/FSAL-V10.php\"' title='Salir'></td>
		</tr>
			</table><hr>
		";
		echo "<form name='formulario' id='formulario'>
				<input type='hidden' name='pedidos' id='pedidos'>
				<table>
					<tr>
			<td><input type='checkbox' name='checkbox' id='todos' value='todos' onChange='seleccionar_todo()'></td>
			<td class='cabecera'>Codigo</td>
			<td class='cabecera'>Razon Social</td>
			<td class='cabecera'>No listas empaque</td>
			<td class='cabecera'>Tiempo trans.</td>
			<td class='cabecera'>Tipo Entrega</td>
			<td class='cabecera'>Remision</td>
			</tr>";
		while($datos = mysql_fetch_array($result)){
			$sqlClieMaes = consultar("clma_tipo,clma_noes,clma_raso,clma_codi", "clie_mae", "clma_esta='ACT' and clma_codi=".$datos["cliente"], 4);
			$datosClieMaes = mysql_fetch_array($sqlClieMaes);
			
			if($datosClieMaes["clma_tipo"] == "NATURAL"){
				$nombre = $datosClieMaes["clma_noes"];
			}
			else{
				$nombre = $datosClieMaes["clma_raso"];
			}
			$causal = "Por Facturar";
			$estilo = "cuerpo";
			$estilo1 = "cuerpo_izq";
			$idxx = $datos["idx"];
			$codi = $datosClieMaes["clma_codi"];
			$fecha1 = new DateTime($datos["fecha"]);
			$fecha2 = new DateTime(date("Y-m-d H:i:s"));
			$fecha = $fecha1->diff($fecha2);
			$dias = $fecha->format('%D');
			$hora = $fecha->format('%H');
			$minu = $fecha->format('%I');
			if($dias > 0){
				$tiempo = "D ".$dias." ".$hora.":".$minu;
			}	 
			else{
				$tiempo = $hora.":".$minu;
			}	
			$lima_liem = $datos["lima_clie"];
			$lima_orco = $datos["lima_orco"];
			$estadowf = ($datos["estadowf"]=="Anulado" || $datos["estadowf"]=="Anuladoback")?"<td class='$estilo1' title='$causal'>".$datos["estadowf"]."</td>":"";
			$sqlCantidad = consultar("pedido", "mpedidos join liem_maes on (pedido=lima_liem or idx=lima_orco) ", "workflow='FACT' and lima_esta='FAC' and estadowf!='Anulado' and cliente=".$datos["cliente"], 4);

			echo "<tr>
					<td><input type='checkbox' name='checkbox' id='".$lima_liem."' value='".$lima_liem."' onChange='contar(1)'></td>
					<td class='$estilo' title='$causal'>".$datosClieMaes["clma_codi"]."</td>
					<td class='$estilo1' title='$causal'>".$nombre."</td>
					<td class='$estilo1' style='text-align:center' title='$causal'>".mysql_num_rows($sqlCantidad)."</td>
					<td class='$estilo' title='$causal'>".$tiempo."</td>
					<td class='$estilo1' title='$causal'>".strtoupper($datos["tp_entrega"])."</td>
				";
			if ($datos["lima_gude"]=="S"){
				echo"<td class='$estilo' title='$causal'>SI</td>";
			}
			else{
				echo"<td class='$estilo' title='$causal'>NO</td>";
			}
			$estadowf;
			echo"</tr>";
		}
		echo "</table></form>";*/
























/*

//error_reporting(0);
class facturacion
 {
	var $num_productos;
 //------------------------------------------------------------------------------------------
 // CONTADOR
	function facturacion() 
	 {
		$this->num_productos=0;
	 }
	
 //------------------------------------------------------------------------------------------
 // 
		function contenido() {
			$fech = date("Y-m-d");
			$result = consultar("idx,fecha,lima_clie,estadowf,lima_orco,tp_entrega,lima_gude,cliente", "mpedidos join liem_maes on(lima_liem=pedido or lima_orco=idx)", "(workflow='FACT' or (workflow='FINA' and lima_orco!=0)) and lima_esta='FAC' GROUP BY (cliente) ORDER BY (fecha) limit 100", 4);

			$pedi_manu = mysql_num_rows(consultar("* ","liem_maes","lima_orco<'500000' and lima_esta='FAC' and cast(lima_fefa as DATE)='$fech'", 4));
			if($pedi_manu > 0){
				$img_fmanu="Fact_mp.png";
			}
			else{
				$img_fmanu="Fact_manual.png";
			}
			echo "<table>
					<tr>
						<td>Codigo del cliente</td>
				<td></td>
				<td style='width:50px;'><input type='text' name='clma_codi' id='clma_codi'></td>
				<td></td>
				<td><img src='../../images/buscar.png' class='detalles' onclick='buscar()' title='Buscar cliente'></td>
				<td><img src='../../images/limpiar.png' class='detalles' onclick='contenido()' title='Limpiar'></td>
				<td><img src='../../images/facturar.png' class='detalles' onclick='exportar()' title='Facturar'></td>
				
				<td><img src='../../images/salir.png' class='detalles' onclick='javascrip:location.href=\"../../funciones/php/FSAL-V10.php\"' title='Salir'></td>
			</tr>
				</table><hr>
			";
			echo "<form name='formulario' id='formulario'>
					<input type='hidden' name='pedidos' id='pedidos'>
					<table>
						<tr>
				<td><input type='checkbox' name='checkbox' id='todos' value='todos' onChange='seleccionar_todo()'></td>
				<td class='cabecera'>Codigo</td>
				<td class='cabecera'>Razon Social</td>
				<td class='cabecera'>No listas empaque</td>
				<td class='cabecera'>Tiempo trans.</td>
				<td class='cabecera'>Tipo Entrega</td>
				<td class='cabecera'>Remision</td>
				</tr>";
			while($datos = mysql_fetch_array($result)){
				$sqlClieMaes = consultar("clma_tipo,clma_noes,clma_raso,clma_codi", "clie_mae", "clma_esta='ACT' and clma_codi=".$datos["cliente"], 4);
				$datosClieMaes = mysql_fetch_array($sqlClieMaes);
				
				if($datosClieMaes["clma_tipo"] == "NATURAL"){
					$nombre = $datosClieMaes["clma_noes"];
				}
				else{
					$nombre = $datosClieMaes["clma_raso"];
				}
				$causal = "Por Facturar";
				$estilo = "cuerpo";
				$estilo1 = "cuerpo_izq";
				$idxx = $datos["idx"];
				$codi = $datosClieMaes["clma_codi"];
				$fecha1 = new DateTime($datos["fecha"]);
				$fecha2 = new DateTime(date("Y-m-d H:i:s"));
				$fecha = $fecha1->diff($fecha2);
				$dias = $fecha->format('%D');
				$hora = $fecha->format('%H');
				$minu = $fecha->format('%I');
				if($dias > 0){
					$tiempo = "D ".$dias." ".$hora.":".$minu;
				}	 
				else{
					$tiempo = $hora.":".$minu;
				}	
				$lima_liem = $datos["lima_clie"];
				$lima_orco = $datos["lima_orco"];
				$estadowf = ($datos["estadowf"]=="Anulado" || $datos["estadowf"]=="Anuladoback")?"<td class='$estilo1' title='$causal'>".$datos["estadowf"]."</td>":"";
				$sqlCantidad = consultar("pedido", "mpedidos join liem_maes on (pedido=lima_liem or idx=lima_orco) ", "workflow='FACT' and lima_esta='FAC' and estadowf!='Anulado' and cliente=".$datos["cliente"], 4);

				echo "<tr>
						<td><input type='checkbox' name='checkbox' id='".$lima_liem."' value='".$lima_liem."' onChange='contar(1)'></td>
						<td class='$estilo' title='$causal'>".$datosClieMaes["clma_codi"]."</td>
						<td class='$estilo1' title='$causal'>".$nombre."</td>
						<td class='$estilo1' style='text-align:center' title='$causal'>".mysql_num_rows($sqlCantidad)."</td>
						<td class='$estilo' title='$causal'>".$tiempo."</td>
						<td class='$estilo1' title='$causal'>".strtoupper($datos["tp_entrega"])."</td>
					";
				if ($datos["lima_gude"]=="S"){
					echo"<td class='$estilo' title='$causal'>SI</td>";
				}
				else{
					echo"<td class='$estilo' title='$causal'>NO</td>";
				}
				$estadowf;
				echo"</tr>";
			}
			echo "</table></form>";
		}	
 //------------------------------------------------------------------------------------------
 // 
		function buscar($clma_codi) {
			$result = consultar("*", "mpedidos join clie_mae on(cliente=clma_codi on lima_orco=idx) join liem_maes on(lima_orco=pedido)", "(workflow='FACT' or (workflow='FINA' and lima_orco!=0)) and lima_esta='FAC' and cliente='$clma_codi' and clma_esta='ACT'  order by (fecha) asc limit 20", 4);
			echo "<table>
					<tr>
						<td>Codigo del cliente</td>
						<td></td>
						<td style='width:50px;'><input type='text' name='clma_codi' id='clma_codi'></td>
				<td></td>
				<td><img src='../../images/buscar.png' class='detalles' onclick='buscar()' title='Buscar cliente'></td>
				<td><img src='../../images/limpiar.png' class='detalles' onclick='contenido()' title='Limpiar'></td>
				<td><img src='../../images/prev.png' class='detalles' onclick='exportar()' title='Facturar'></td>
				<td><img src='../../images/salir.png' class='detalles' onclick='javascrip:location.href=\"../../funciones/php/FSAL-V10.php\"' title='Salir'></td>
					</tr>		
				</table><hr>
			";
			echo "<form name='formulario' id='formulario'> <input type='hidden' name='pedidos' id='pedidos'>
			<table>
				<tr>
				<td><input type='checkbox' name='checkbox' id='todos' value='todos' onChange='seleccionar_todo()'></td>
				<td class='cabecera'>Codigo</td>
				<td class='cabecera'>Razon Social</td>
				<td class='cabecera'>Orden de compra</td>
				<td class='cabecera'>Lista de empaque</td>
				<td class='cabecera'>Tiempo trans.</td>
				<td class='cabecera'>Tipo Entrega</td>
				<td class='cabecera'>Guia impresa</td>
					</tr>";
				while($datos = mysql_fetch_array($result)){
				if($datos["clma_tipo"] == "NATURAL"){
					$nombre = $datos["clma_noes"];
				}
				else{
					$nombre = $datos["clma_raso"];
				}
				$causal = "Por Facturar";
				$estilo = "cuerpo";
				$estilo1 = "cuerpo_izq";
				$idxx = $datos["idx"];
				$codi = $datos["clma_codi"];
				$fecha1 = new DateTime($datos["fecha_exp"]);
				$fecha2 = new DateTime(date("Y-m-d H:i:s"));
				$fecha = $fecha1->diff($fecha2);
				$dias = $fecha->format('%D');
				$hora = $fecha->format('%H');
				$minu = $fecha->format('%I');
				if($dias > 0){
					$tiempo = "D ".$dias." ".$hora.":".$minu;
				}	 
				else{
					$tiempo = $hora.":".$minu;
				}	
				$lima_liem = $datos["lima_liem"];
				$lima_orco = $datos["lima_orco"];
				echo "<tr>
						<td><input type='checkbox' name='checkbox' id='".$lima_liem."' value='".$lima_liem."' onChange='contar(1)'></td>
						<td class='$estilo' title='$causal'>".$datos["clma_codi"]."</td>
						<td class='$estilo1' title='$causal'>".$nombre."</td>
						<td class='$estilo' title='$causal'>".$datos["pedido"]."</td>
						<td class='$estilo' title='$causal'>".$datos["lima_liem"]."</td>
						<td class='$estilo' title='$causal'>".$tiempo."</td>
						<td class='$estilo1' title='$causal'>".strtoupper($datos["tp_entrega"])."</td>";
				if ($datos["lima_gude"]=="S") {
					echo"<td class='$estilo' title='$causal'>SI</td>";
				}
				else{
					echo"<td class='$estilo' title='$causal'>NO</td>";
				}
				echo"</tr>";
			}
			echo "</table></form>";
		}  	
 //------------------------------------------------------------------------------------------
 // 
	function facturar($lima_liem) {
		$listas = explode("-", $lima_liem);
		$cantidad_listas = count($listas);
		for($carro=0; $carro<$cantidad_listas; $carro++){
			if(file_exists("../../../bodega/importar/facturado/".$listas[$carro]."f.txt")) {
				$archivo = fopen("../../../bodega/importar/facturado/".$listas[$carro]."f.txt","rb");
				while(feof($archivo) == false ){
					$datos = explode("",fgets($archivo));
					if(trim($datos[0]) != ""){
						$list_empa = $datos[0];
						$list_vlrm = $datos[1];
						$list_vlrd = $datos[2];
						$list_vlrf = $datos[3];
						$lima_fefa = date("Y-m-d H:i:s");
						actualizar("liem_maes", "lima_vtpe='".$list_vlrm."',lima_vfle='".$list_vlrf."',lima_vdes='".$list_vlrd."',lima_fefa='".$lima_fefa."',lima_esta='REA'", "lima_liem='".$list_empa."'");
						$datos = mysql_fetch_array(consultar("*", "liem_maes", "lima_liem='".$list_empa."'", 4));
						actualizar("mpedidos", "workflow='FINA'", "pedido='".$datos["lima_orco"]."'");
					}
				}
				fclose($archivo);
				shell_exec("rm -rf ../../../bodega/importar/facturado/".$listas[$carro]."f.txt"); // ELIMINAR ARCHIVO
			}	 
		}	
	}  	
}*/
//------------------------------------------------------------------------------------------- 

?>
