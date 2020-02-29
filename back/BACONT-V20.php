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
		$result = $this->consultar("lima_liem,lima_idxx,fac.fecha,lima_clie,lima_orco,lima_gude,fac.cliente,fac.BRUTO,fac.FLETES,fac.iva,lima_vafe", "FACT as fac join liem_maes on(lima_liem=OC)", " DATE_FORMAT( `fecha` , '%Y-%m-%d' ) > '2020-01-01' ORDER BY (fecha) limit 10", 4);
		//$result = $this->consultar("idx,lima_idxx,fecha,lima_clie,estadowf,lima_orco,tp_entrega,lima_gude,cliente,count(idx) as cantidad,lima_vtpe,lima_vfle,lima_vdes,vriva,lima_vafe", "mpedidos join liem_maes on(lima_liem=pedido or lima_orco=idx)", "(workflow='FINA' or lima_esta='FEL' or (workflow='FINA' and lima_orco!=0)) and lima_esta='REA' and DATE_FORMAT( `fecha` , '%Y-%m-%d' ) = '2020-01-17' GROUP BY (cliente) ORDER BY (fecha) limit 10", 4);
		echo "
		<table class='table table-hover' style='width:500px'>
			<thead>
				<tr>
					<td style='width:170px;'><input type='text' class='form-control' name='clma_codi' id='clma_codi' placeholder='Codigo del cliente'></td>
					<td></td>
					<td><img src='../img/validar_dian.png' width='32px' class='detalles' onclick='valiContMasivo()' title='Validar'></td>			
					
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
					<td class='cabecera'>Pedido</td>
					<td class='cabecera'>Razon Social</td>
					<td class='cabecera'>Fecha</td>
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
			$lima_idxx = $datos["lima_idxx"];
			$lima_orco = $datos["lima_orco"];
			$total=($datos["BRUTO"]-$datos["DESC"])+$datos["FLETES"]+$datos["IVA"];
			echo "<tr>
					<td><input type='checkbox' name='checkbox' id='".$lima_idxx."' value='".$lima_idxx."' onChange='contar(1)'></td>
					<td class='$estilo' title='$causal'>".$datosClieMaes["clma_codi"]."</td>
					<td class='$estilo' title='$causal'>".$datos["lima_liem"]."</td>
					<td class='$estilo1' title='$causal'>".$nombre."</td>
					<td class='$estilo' title='$causal'>".$datos["fecha"]."</td>
					<td class='$estilo' title='$causal'>".number_format($datos["BRUTO"])."</td>
					<td class='$estilo' title='$causal'>".number_format($datos["DESC"])."</td>
					<td class='$estilo' title='$causal'>".number_format($datos["FLETES"])."</td>
					<td class='$estilo' title='$causal'>".number_format($datos["iva"])."</td>
					<td class='$estilo' title='$causal'>".number_format($total)."</td>
				";
			echo "<td><img src='../img/validar_dian.png' width='32px' class='detalles' onclick='valiFactCont(".$datos["lima_idxx"].")' title='Validar'></td>";			
			echo"</tr>";
		}
		echo "</tbody></table></div></form>";
	}

	//trae las facturas que se le han asignado al cliente 
	public function factCliente(){
		extract($_POST);
		$sqlMpedidos = $this->consultar("idx,fecha,lima_clie,estadowf,lima_orco,tp_entrega,lima_gude,lima_liem,lima_vafe,lima_idxx", "mpedidos join liem_maes on(lima_liem=pedido)", "(workflow='FINA' or lima_esta='FEL' or (workflow='FINA' and lima_orco!=0)) and lima_esta='REA' and DATE_FORMAT( `fecha` , '%Y-%m-%d' ) = '2020-01-17' and cliente=".$clma_codi." ORDER BY (fecha) limit 10", 4);
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
			$idxx = $datosMpedidos["idx"];
			$codi = $datosMpedidos["lima_clie"];
			$fecha1 = new DateTime($datosMpedidos["fecha"]);
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
			$lima_liem = $datosMpedidos["lima_clie"];
			$lima_orco = $datosMpedidos["lima_orco"];
			$estadowf = ($datosMpedidos["estadowf"]=="Anulado" || $datosMpedidos["estadowf"]=="Anuladoback")?"<td class='$estilo1' title='$causal'>".$datosMpedidos["estadowf"]."</td>":"";
			$sqlCantidad = $this->consultar("pedido", "mpedidos join liem_maes on (pedido=lima_liem or idx=lima_orco) ", "workflow='FACT' and lima_esta='FAC' and estadowf!='Anulado' and cliente=".$datosMpedidos["lima_clie"], 4);

			echo "<tr>
					<td class='$estilo' title='$causal'>".$datosMpedidos["lima_liem"]."</td>
					<td class='$estilo1' style='text-align:center' title='$causal'>".mysqli_num_rows($sqlCantidad)."</td>
					<td class='$estilo' title='$causal'>".$tiempo."</td>
					<td class='$estilo1' title='$causal'>".strtoupper($datosMpedidos["tp_entrega"])."</td>
				";
			if ($datosMpedidos["lima_gude"]=="S"){
				echo"<td class='$estilo' title='$causal'>SI</td>";
			}
			else{
				echo"<td class='$estilo' title='$causal'>NO</td>";
			}
			echo "<td><img src='../img/cargar_flete.png' width='32px' class='detalles' onclick='agrgarFlete(".$datosMpedidos["lima_idxx"].")' title='Agregar flete'></td>";			
			$estadowf;
			switch ($datosMpedidos["lima_vafe"]) {
				case 'S':
					echo "<td><img src='../img/revisado.png' width='32px' class='detalles' onclick='valiFactElec(\"".$datosMpedidos["lima_idxx"]."\",1,".$datosMpedidos["lima_clie"].",\"$nombre\")' title='Facturar'></td>";
					break;
				case 'N':
					echo "<td><img src='../img/validar_dian.png' width='32px' class='detalles' onclick='valiFactElec(\"".$datosMpedidos["lima_idxx"]."\",1,".$datosMpedidos["lima_clie"].",\"$nombre\")' title='Validar DIAN'></td>";
					break;
				case 'C':
					echo "<td><img src='../img/exportar.png' width='32px' class='detalles' onclick='valiFactElec(\"".$datosMpedidos["lima_idxx"]."\",1,".$datosMpedidos["lima_clie"].",\"$nombre\")' title='Contingencia'></td>";
					break;
				case 'E':
					echo "<td><img src='../img/error-icon.png' width='32px' class='detalles' onclick='valiFactElec(\"".$datosMpedidos["lima_idxx"]."\",1,".$datosMpedidos["lima_clie"].",\"$nombre\")' title='Error'></td>";
					break;
			}
			echo"</tr>";
		}
		echo "</tbody></table></form>";
	}

	//generarContingencia
	public function generarContingencia(){
		extract($_POST);
		$fechaActual = date("Y-m-d");
		$horaActual = date("H:i:s");
		$sqlLiemMaes = $this->consultar("fecha,lima_liem,clma_feec,clma_tipo,clma_noes,clma_raso,clma_nitt,clma_dive,clma_repr,cima_noci,cima_codi,dema_node,clma_idxx,dema_codi,clma_dire,FLETES,DESC_ESP,VEN,clma_codi,TRANSPOR,clma_copa,lima_vdes,copc_deco,BRUTO,copc_pldi,copc_depf,copc_codi,clma_cel1,clma_cor1", "liem_maes JOIN FACT ON (lima_liem = OC) join clie_mae on (lima_clie=clma_codi) join copa_clie on (CPAGO=copc_codi) join ciud_mae on (clma_ciud=cima_idxx) join depa_mae on (cima_iddp=dema_idxx)", "lima_idxx=".$lima_idxx, 4);


		$datosLiemMaes = mysqli_fetch_array($sqlLiemMaes);
		//contador de la cantidad del detalle de la factura
		$sqlContDetaLiem = $this->consultar("deli_idle", "deta_liem", "deli_idle=".$lima_idxx, 2);

		echo $datosLiemMaes["lima_liem"];
		$formaDePago = ($datosLiemMaes["copc_pldi"]==0)?1:2;
 		$nom_mae= "../../xml/contingencia/factura-ejemplo".$datosLiemMaes["lima_liem"].".xml";
 		$morden = fopen($nom_mae,"w") or die("No se encontro la ruta para la exportacion");
		$vencimiento = date("Y-m-d",strtotime($fechaActual."+ ".$datosLiemMaes["copc_pldi"]." days")); 
 		$text = "<Factura> \n";
 		$text .= "<Cabecera  Numero='SETT".rand(5, 715)."' OrdenCompra='".$datosLiemMaes["lima_liem"]."' FechaOrdenCompra='".substr($datosLiemMaes["fecha"], 0, 10)."' FechaEmision='".$fechaActual."' Vencimiento='".$vencimiento."' HoraEmision='".$horaActual."' MonedaFactura='COP' TipoFactura='FACTURA-UBL' FormaDePago='".$formaDePago."' LineasDeFactura='".$sqlContDetaLiem."' TipoOperacion='10' FormatoContingencia='Papel'/> \n";
 		//$text .= "<Cabecera  Numero='SETT".$datosLiemMaes["lima_liem"]."' OrdenCompra='".$datosLiemMaes["lima_liem"]."' FechaOrdenCompra='".substr($datosLiemMaes["fecha"], 0, 10)."' FechaEmision='".$fechaActual."' Vencimiento='".$vencimiento."' HoraEmision='".$horaActual."' MonedaFactura='COP' TipoFactura='FACTURA-UBL' FormaDePago='".$formaDePago."' LineasDeFactura='".$sqlContDetaLiem."' TipoOperacion='10' FormatoContingencia='Papel'/> \n";
 		$text .= "<NumeracionDIAN NumeroResolucion='18760000001' FechaInicio='2019-01-19' FechaFin='2030-01-19' PrefijoNumeracion='SETT' ConsecutivoInicial='1' ConsecutivoFinal='5000000'/> \n";
 		$text .= "<Notificacion Tipo='Mail' De='info@hermagu.com.co'> \n";
 		//$text .= "<Para>sistemas@hermagu.com.co</Para> \n";
 		$text .= "<Para>auxsistemas@hermagu.com.co</Para> \n";
 		//$text .= "<Para>".$datosLiemMaes["clma_feec"]."</Para> \n";
 		$text .= "</Notificacion> \n";
 		$text .= "<Emisor TipoPersona='1' TipoRegimen='48' TipoIdentificacion='31' NumeroIdentificacion='860511055' DV='8' RazonSocial='HERMAGU S.A.' NumeroMatriculaMercantil='183263' NombreComercial= 'HERMAGU S.A.' > \n";
 		$text .= "<CodigosCIIU> \n";
 		$text .= "<CIIU>4530</CIIU> \n";
 		$text .= "</CodigosCIIU> \n";
 		$text .= "<Direccion CodigoMunicipio='11001' NombreCiudad='BOGOTA D.C.' CodigoPostal='' NombreDepartamento='BOGOTA D.C.' CodigoDepartamento='11' Direccion='Calle 17 No 28A - 29' /> \n";
 		$text .= "<ObligacionesEmisor> \n";
 		$text .= "<CodigoObligacion>O-03</CodigoObligacion> \n";
 		$text .= "<CodigoObligacion>O-05</CodigoObligacion> \n";
 		$text .= "<CodigoObligacion>O-07</CodigoObligacion> \n";
 		$text .= "<CodigoObligacion>O-09</CodigoObligacion> \n";
 		$text .= "<CodigoObligacion>O-10</CodigoObligacion> \n";
 		$text .= "<CodigoObligacion>O-14</CodigoObligacion> \n";
 		$text .= "<CodigoObligacion>O-42</CodigoObligacion> \n";
 		$text .= "<CodigoObligacion>O-48</CodigoObligacion> \n";
 		$text .= "</ObligacionesEmisor> \n";
 		$text .= "<DireccionFiscal CodigoMunicipio='11001' NombreCiudad='BOGOTA D.C.' CodigoPostal='' NombreDepartamento='BOGOTA D.C.' CodigoDepartamento='11' Direccion='Calle 17 No 28A - 29'/> \n";
 		$text .= "<TributoEmisor CodigoTributo='01' NombreTributo='IVA'/> \n";
 		$text .= "</Emisor> \n";
 		//responsabilidades trivutarias
 		$tiporegimen = 49;
		$sqlMoviReti = $this->consultar("retm_codi", "movi_reti join retr_maes on (mret_idrt=retm_idxx)", "mret_idcl=".$datosLiemMaes["clma_idxx"], 4);
		while ($datosMoviReti = mysqli_fetch_array($sqlMoviReti)) {
			if ($datosMoviReti["retm_codi"]==48) {
 				$tiporegimen = 48;
			}
		}
 		$nombreComercial=($datosLiemMaes["clma_tipo"]=="NATURAL")?$datosLiemMaes["clma_noes"]:$datosLiemMaes["clma_raso"];
 		$tipoPersona=($datosLiemMaes["clma_tipo"] == "NATURAL")?2:1;
 		$tipoIdentificacion=($datosLiemMaes["clma_tipo"] == "NATURAL")?13:31;
 		$text .= "<Cliente TipoPersona='".$tipoPersona."' TipoRegimen='".$tiporegimen."' TipoIdentificacion='".$tipoIdentificacion."' NumeroIdentificacion='".$datosLiemMaes["clma_nitt"]."' DV='".$datosLiemMaes["clma_dive"]."' NombreComercial='".$nombreComercial."' RazonSocial='".$datosLiemMaes["clma_repr"]."'> \n";
 		$text .= "<Direccion CodigoMunicipio='".$datosLiemMaes["dema_codi"]."".str_pad($datosLiemMaes["cima_codi"], 5, "0", STR_PAD_LEFT)."' NombreCiudad='".$datosLiemMaes["cima_noci"]."' CodigoPostal='' NombreDepartamento='".$datosLiemMaes["dema_node"]."' CodigoDepartamento='".$datosLiemMaes["dema_codi"]."' Direccion='".$datosLiemMaes["clma_dire"]."' CodigoPais='CO' NombrePais='Colombia' IdiomaPais='es'/> \n";
 		//$text .= "<Contacto Nombre='GUSTAVO ADOLFO GALLO' Telefono='2635244' Telfax='' Email='gallonetrepuestos@hotmail.com' Notas='Representante legal'/> \n";
 		$text .= "<Contacto Nombre='".$datosLiemMaes["clma_repr"]."' Telefono='".$datosLiemMaes["clma_cel1"]."' Telfax='' Email='".$datosLiemMaes["clma_cor1"]."' Notas='Representante legal'/> \n";
 		$text .= "<ObligacionesCliente> \n";
 		//responsabilidades trivutarias
 		mysqli_data_seek($sqlMoviReti, 0);
		while ($datosMoviReti = mysqli_fetch_array($sqlMoviReti)) {
 			$text .= "<CodigoObligacion>O-".$datosMoviReti["retm_codi"]."</CodigoObligacion> \n";
		}
 		$text .= "</ObligacionesCliente> \n";
 		$text .= "<Direccion CodigoMunicipio='".$datosLiemMaes["dema_codi"]."".str_pad($datosLiemMaes["cima_codi"], 5, "0", STR_PAD_LEFT)."' NombreCiudad='".$datosLiemMaes["cima_noci"]."' CodigoPostal='' NombreDepartamento='".$datosLiemMaes["dema_node"]."' CodigoDepartamento='".$datosLiemMaes["dema_codi"]."' Direccion='".$datosLiemMaes["clma_dire"]."' CodigoPais='CO' NombrePais='Colombia' IdiomaPais='es'/> \n";
 		$text .= "<TributoCliente CodigoTributo='01' NombreTributo='IVA'/> \n";
 		$text .= "</Cliente> \n";
 		$text .= "<MediosDePago CodigoMedioPago='ZZZ' FormaDePago='OTRO' Vencimiento='".$vencimiento."'> \n";
 		$text .= "</MediosDePago> \n";
//
 		//asesor
		$sqlAsesoresWeb = $this->consultar("nombres,apellidos", "codvend as cod join asesores_web as ase on (cod.index_aw=ase.idx)", "cod.tipo_vend='local' and cod.vend=".$datosLiemMaes["VEN"], 4);
		//$sqlAsesoresWeb = $this->consultar("nombres,apellidos", "asesores_web", "idx=".$datosLiemMaes["idx_vend"], 4);
		$datosAsesoresWeb = mysqli_fetch_array($sqlAsesoresWeb);
 		$text .= "<Extensiones> \n";
 		$text .= "<DatosAdicionales> \n";
 		$text .= "<CampoAdicional Nombre='codigo' Valor='".$datosLiemMaes["clma_codi"]."'/> \n";
 		$text .= "<CampoAdicional Nombre='pedido N°' Valor='".$datosLiemMaes["lima_liem"]."'/> \n";
 		$text .= "<CampoAdicional Nombre='Transportador - Guia N°' Valor='".$datosLiemMaes["TRANSPOR"]."'/> \n";
 		$text .= "<CampoAdicional Nombre='vendedor' Valor='".$datosAsesoresWeb["nombres"]."'/> \n";
 		$text .= "<CampoAdicional Nombre='condicionesdepago' Valor='".$datosLiemMaes["clma_copa"]."'/> \n";
 		//validar descuentos
 		//$lima_vdes =($datosLiemMaes["copc_depf"]==0)?0:$datosLiemMaes["lima_vdes"];
 			//condicionado
 		if ($datosLiemMaes["copc_deco"]!=0) {
 			$porceCond = $datosLiemMaes["copc_deco"];
 			$valorCond = $datosLiemMaes["BRUTO"]*($datosLiemMaes["copc_deco"]/100);
 			$pagueSoloCond = $brutoMasImpuestos-$valorCond;
 			$fecha_actual = date("Y-m-d");
			$canceleAntes = date("Y-m-d",strtotime($fecha_actual."+ ".$datosLiemMaes["copc_pldi"]." days")); 

	 		//texto en letras
	 		$textoEnLetras = $this->numerotexto(round(intval($brutoMasImpuestos),0));
	 		$text .= "<CampoAdicional Nombre='ValorLetras' Valor='".strtoupper($textoEnLetras)." MCT'/> \n";
	 		$text .= "<CampoAdicional Nombre='Dcto. por Pronto Pago' Valor='".$porceCond."%  $".number_format($valorCond)."'/> \n";
	 		$text .= "<CampoAdicional Nombre='paguesolamente' Valor='".$pagueSoloCond."'/> \n";
	 		$text .= "<CampoAdicional Nombre='canceleantes' Valor='".$canceleAntes."'/> \n";
	 		$text .= "<CampoAdicional Nombre='Observacion1' Valor='A esta factura se le otorgo el ".$porceCond."% Dcto a pie, para pago antes de ".$datosLiemMaes["copc_pldi"]." días. Pague oportunamete y evite perder este descuento.'/> \n";
		}
 		$text .= "</DatosAdicionales> \n";
 		$text .= "</Extensiones> \n";
//
		$sqlDetaLiem = $this->consultar("deli_caap,deli_prec,rema_desc,rema_apci,rema_line,rema_oem,rema_popu", "deta_liem join refe_maes on (deli_idre=rema_idxx)", "deli_caap!=0 and deli_idle=".$lima_idxx, 4);
		$deta_prec =0;
		while ($datosDetaLiem = mysqli_fetch_array($sqlDetaLiem)) {
			$deta_prec = $deta_prec+($datosDetaLiem["deli_caap"]*$datosDetaLiem["deli_prec"]);
		}
 		//totales
 		$descuento = $deta_prec*($datosLiemMaes["copc_depf"]/100);
 		$neto = $deta_prec-$descuento;
 		$baseImponible=$neto+($datosLiemMaes["FLETES"]-$datosLiemMaes["DESC_ESP"]);
 		$iva = $baseImponible*0.19;
 		$brutoMasImpuestos = $baseImponible+$iva;
 		//totales
 		//<!--Impuestos generales de la factura-->
 		$text .= "<Totales Bruto='".round($baseImponible,0)."' BaseImponible='".round($baseImponible,0)."' BrutoMasImpuestos='".round($brutoMasImpuestos,0)."' Cargos= '0' Descuentos='0' Impuestos='".round($iva,0)."' Retenciones='0' General='' Anticipo='0' Redondeo='0' TotalOtros1='".round($datosLiemMaes["BRUTO"],0)."' Neto='".round($neto,0)."' Subtotal='".round($baseImponible,0)."' TotalDescuentosLineas='".round($descuento,0)."' TotalCargosLineas='0' Flete='".round($datosLiemMaes["FLETES"],0)."'  DescuentoEnFlete='".round($datosLiemMaes["DESC_ESP"],0)."'/>\n";
 		$text .= "<Impuestos> \n";
 		$text .= "<Impuesto Tipo='01' Nombre='IVA' Valor='".round($iva,0)."'> \n";
 		$text .= "<Subtotal ValorBase='".round($baseImponible,0)."'  Porcentaje='19'  Valor='".round($iva,0)."' CodigoUnidadMedidaBase='94'/> \n";
 		$text .= "</Impuesto> \n";
 		$text .= "</Impuestos> \n";

 		$contador=1;
 		//<!-- Datos de referencias vendedidas al cliente -->
 		mysqli_data_seek($sqlDetaLiem,0);
		while ($datosDetaLiem = mysqli_fetch_array($sqlDetaLiem)) {
			$subTotal = $datosDetaLiem["deli_caap"]*$datosDetaLiem["deli_prec"];
			$vrDescuento = $subTotal*($datosLiemMaes["copc_depf"]/100);
			$valorTotal = $subTotal - $vrDescuento;
			$vrIva = $valorTotal*0.19;
 			$text .= "<Linea> \n";
 			$text .= "<Detalle NumeroLinea='".$contador."' Nota='' Cantidad='".$datosDetaLiem["deli_caap"]."' UnidadMedida='Unidad' SubTotalLinea='".$valorTotal."' Descripcion='".substr($datosDetaLiem["rema_desc"]." ".$datosDetaLiem["rema_apci"], 0, 70)."' CantidadXEmpaque='' Marca='' NombreModelo='' CantidadBase='".$datosDetaLiem["deli_caap"]."' UnidadCantidadBase='Unidad' PrecioUnitario='".$datosDetaLiem["deli_prec"]."' ValorTotalItem='".$subTotal."' /> \n";
 			$text .= "<DatosAdicionales> \n";
 			$text .= "<CampoAdicional Nombre='linea' Valor='".$datosDetaLiem["rema_line"]."'/> \n";
 			$text .= "<CampoAdicional Nombre='referencia' Valor='".$datosDetaLiem["rema_oem"]."'/> \n";
 			$text .= "<CampoAdicional Nombre='equivalencia' Valor='".$datosDetaLiem["rema_popu"]."'/> \n";
 			$text .= "</DatosAdicionales> \n";
 			//<!--Se adidiona el descunto que se ofrece PF-->
			if ($datosLiemMaes["copc_depf"]!=0) {
 				$text .= "<DescuentoOCargo ID='".$datosLiemMaes["copc_codi"]."' Indicador='false' Justificacion='Descuento directo' Porcentaje='".$datosLiemMaes["copc_depf"]."' ValorBase='".$subTotal."' Valor='".$vrDescuento."'  /> \n";
			}
 			$text .= "<Impuestos> \n";
 			$text .= "<Impuesto Tipo='01' Nombre='IVA' Valor='".$vrIva."'> \n";
 			$text .= "<Subtotal ValorBase='".$valorTotal."'  Porcentaje='19'  Valor='".$vrIva."' CodigoUnidadMedidaBase='94'/> \n";
 			$text .= "</Impuesto> \n";
	 		$text .= "</Impuestos> \n";
	 		$text .= "</Linea> \n";
			$contador ++;
		}


		if ($datosLiemMaes["DESC_ESP"]!=0) {
			$porc_desc = round(($datosLiemMaes["DESC_ESP"]*100)/$datosLiemMaes["FLETES"],2);
			$vrFlete =  $datosLiemMaes["FLETES"]-$datosLiemMaes["DESC_ESP"];
			$iva_flete = $vrFlete*0.19;
		}
		else{
			$vrFlete =  $datosLiemMaes["FLETES"];
			$iva_flete = $vrFlete*0.19;
		} 

 		//<!-- Datos para la factura que lleva fletes -->
		if ($datosLiemMaes["FLETES"]!=0) {
	 		$text .= "<Linea> \n";
	 		$text .= "<Detalle NumeroLinea='".$contador."' Cantidad='1' UnidadMedida='94' SubTotalLinea='".$vrFlete."' Descripcion='flete' CantidadBase='0' UnidadCantidadBase='94' PrecioUnitario='0' ValorTotalItem='".$datosLiemMaes["FLETES"]."' Ocultar='true'/> \n";
	 		$text .= "<precioreferencia ValorArticulo= '".$vrFlete."' CodigoTipoPrecio='03'> \n";
	 		$text .= "</precioreferencia> \n";
			if ($datosLiemMaes["DESC_ESP"]!=0) {
				$text .= "<DescuentoOCargo ID='99' Indicador='false' Justificacion='Descuento flete' Porcentaje='".$porc_desc."' ValorBase='".$datosLiemMaes["FLETES"]."' Valor='".$vrFlete."'  /> \n";
			}
	 		$text .= "<Impuestos> \n";
	 		$text .= "<Impuesto Tipo='01' Nombre='IVA' Valor='".round($iva_flete,0)."'> \n";
	 		$text .= "<Subtotal ValorBase='".$vrFlete."' Valor='".round($iva_flete,0)."' Porcentaje='19' CodigoUnidadMedidaBase='94'/> \n";
	 		$text .= "</Impuesto> \n";
	 		$text .= "</Impuestos> \n";
	 		$text .= "<CodificacionesEstandar> \n";
	 		$text .= "<CodificacionEstandar CodigoArticulo='891000039'/> \n";
	 		$text .= "</CodificacionesEstandar> \n";
	 		$text .= "</Linea> \n";
		}
 		$text .= "</Factura> \n";
 		$text .= " \n";
 		$text .= " \n";
		fputs($morden,$text."\r\n");
		//fclose($dorden); 
	}

	//pasa los numeros en formato de texto
	function numerotexto ($numero) {
    // Primero tomamos el numero y le quitamos los caracteres especiales y extras
    // Dejando solamente el punto "." que separa los decimales
    // Si encuentra mas de un punto, devuelve error.
    // NOTA: Para los paises en que el punto y la coma se usan de forma
    // inversa, solo hay que cambiar la coma por punto en el array de "extras"
    // y el punto por coma en el explode de $partes
    
    $extras= array("/[\$]/","/ /","/,/","/-/");
    $limpio=preg_replace($extras,"",$numero);
    $partes=explode(".",$limpio);
    if (count($partes)>2) {
      return "Error, el n&uacute;mero no es correcto";
      exit();
    }
    
    // Ahora explotamos la parte del numero en elementos de un array que
    // llamaremos $digitos, y contamos los grupos de tres digitos
    // resultantes
    
    $digitos_piezas=chunk_split ($partes[0],1,"#");
    $digitos_piezas=substr($digitos_piezas,0,strlen($digitos_piezas)-1);
    $digitos=explode("#",$digitos_piezas);
    $todos=count($digitos);
    $grupos=ceil (count($digitos)/3);
    
    // comenzamos a dar formato a cada grupo
    $unidad = array   ('un','dos','tres','cuatro','cinco','seis','siete','ocho','nueve');
    $decenas = array ('diez','once','doce', 'trece','catorce','quince');
    $decena = array   ('dieci','veinti','treinta','cuarenta','cincuenta','sesenta','setenta','ochenta','noventa');
    $centena = array   ('ciento','doscientos','trescientos','cuatrocientos','quinientos','seiscientos','setecientos','ochocientos','novecientos');
    $resto=$todos;
    
    for ($i=1; $i<=$grupos; $i++) {
      // Hacemos el grupo
      if ($resto>=3) {
        $corte=3; } else {
        $corte=$resto;
      }
      $offset=(($i*3)-3)+$corte;
      $offset=$offset*(-1);
      // la siguiente seccion es una adaptacion de la contribucion de cofyman y JavierB
      $num=implode("",array_slice ($digitos,$offset,$corte));
      $resultado[$i] = "";
      $cen = (int) ($num / 100);              //Cifra de las centenas
      $doble = $num - ($cen*100);             //Cifras de las decenas y unidades
      $dec = (int)($num / 10) - ($cen*10);    //Cifra de las decenas
      $uni = $num - ($dec*10) - ($cen*100);   //Cifra de las unidades
      if ($cen > 0) {
        if ($num == 100) $resultado[$i] = "cien";
        else $resultado[$i] = $centena[$cen-1].' ';
      }//end if
      if ($doble>0) {
        if ($doble == 20) {
          $resultado[$i] .= " veinte";
        }
        elseif (($doble < 16) and ($doble>9)) {
           $resultado[$i] .= $decenas[$doble-10];
        }
        else {
          $resultado[$i] .=' '. $decena[$dec-1];
        }//end if
        if ($dec>2 and $uni<>0) $resultado[$i] .=' y ';
        if (($uni>0) and ($doble>15) or ($dec==0)) {
          if ($i==1 && $uni == 1) $resultado[$i].="uno";
          elseif ($i==2 && $num == 1) $resultado[$i].="";
          else $resultado[$i].=$unidad[$uni-1];
        }
      }
      // Le agregamos la terminacion del grupo
      switch ($i) {
        case 2:
        	$resultado[$i].= ($resultado[$i]=="") ? "" : " mil ";
        break;
        case 3:
        	$resultado[$i].= ($num==1) ? " millón " : " millones ";
        break;
    	}
        $resto-=$corte;
    }
    // Sacamos el resultado (primero invertimos el array)
    $resultado_inv= array_reverse($resultado, TRUE);
    $final="";
    foreach ($resultado_inv as $parte){
      $final.=$parte;
    }
    return $final;
	}

}

@session_start(); 
if(!isset($_SESSION["c_facturacion"])){
	$_SESSION["c_facturacion"] = new facturacion();
}
?>
