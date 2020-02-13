<?php
/**
 * Modulo de facturacion 
 * Por: Julian Parra
*/
require_once("../../cone/mysql.php");
class generaXml extends conectarBD{
	//$fechaActual = date("Y-m-d");
	//
	//Función para generar factura
	public function generarFactura() {
		extract($_POST);
		$fechaActual = date("Y-m-d");
		$horaActual = date("H:i:s");
		$sqlLiemMaes = $this->consultar("lima_idxx,lima_tore,clma_feec", "liem_maes JOIN mpedidos ON (lima_orco = idx OR lima_liem = pedido ) join clie_mae on (lima_clie=clma_codi) join copa_clie on (clma_pode=copc_codi) join ciud_mae on (clma_ciud=cima_idxx) join depa_mae on (cima_iddp=dema_idxx)", "lima_idxx=".$lima_idxx, 4);
		$datosLiemMaes = mysqli_fetch_array($sqlLiemMaes);
		//contador de la cantidad del detalle de la factura
		$sqlContDetaLiem = $this->consultar("deli_idle", "deta_liem", "deli_idle=".$lima_idxx, 2);

 		$nom_mae= "../../plcolab/app-data/input/factura-ejemplo.xml";
 		$morden = fopen($nom_mae,"w") or die("No se encontro la ruta para la exportacion");
 		$text = "<Factura> \n";
 		$text .= "<Cabecera  Numero='SETT400' OrdenCompra='".$datosLiemMaes["lima_liem"]."' FechaOrdenCompra='".$fechaActual."' FechaEmision='".$fechaActual."' Vencimiento='".$fechaActual."' HoraEmision='".$horaActual."' MonedaFactura='COP' Observaciones='A esta factura se le otorgo el 10% Dcto. a pie. Para pago ante de 30 día. Pague oportunamente y evite perder este descuento' TipoFactura='FACTURA-UBL' FormaDePago='2' LineasDeFactura='".$sqlContDetaLiem."' TipoOperacion='05' FormatoContingencia='Papel'/> \n";
 		$text .= "<NumeracionDIAN NumeroResolucion='18760000001' FechaInicio='2019-01-19' FechaFin='2030-01-19' PrefijoNumeracion='SETT' ConsecutivoInicial='1' ConsecutivoFinal='5000000'/> \n";
 		$text .= "<Notificacion Tipo='Mail' De='info@hermagu.com.co'> \n";
 		$text .= "<Para>sistemas@hermagu.com.co</Para> \n";
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
//preguntar tipo de persona,tiporegimen
 		$nombreComercial=($datosLiemMaes["clma_tipo"]=="NATURAL")?$datosLiemMaes["clma_noes"]:$datosLiemMaes["clma_raso"];
 		//$razonSocial=$datosLiemMaes["clma_repr"];
 		$text .= "<Cliente TipoPersona='1' TipoRegimen='49' TipoIdentificacion='31' NumeroIdentificacion='".$datosLiemMaes["clma_nitt"]."' DV='".$datosLiemMaes["clma_dive"]."' NombreComercial='".$nombreComercial."' RazonSocial='".$datosLiemMaes["clma_repr"]."'> \n";
 		$text .= "<Direccion CodigoMunicipio='05001' NombreCiudad='".$datosLiemMaes["cima_noci"]."' CodigoPostal='' NombreDepartamento='".$datosLiemMaes["dema_node"]."' CodigoDepartamento='".$datosLiemMaes["dema_codi"]."' Direccion='".$datosLiemMaes["cima_dire"]."' CodigoPais='CO' NombrePais='Colombia' IdiomaPais='es'/> \n";
 		$text .= "<ObligacionesCliente> \n";
 		//responsabilidades trivutarias
		$sqlMoviReti = $this->consultar("retm_codi", "movi_reti join retr_maes on (mret_idrt=retm_idxx)", "mret_idcl=".$datosLiemMaes["clma_idxx"], 4);
		while ($datosMoviReti = mysqli_fetch_array($sqlMoviReti)) {
 			$text .= "<CodigoObligacion>O-".$datosMoviReti["retm_codi"]."</CodigoObligacion> \n";
		}
 		$text .= "</ObligacionesCliente> \n";
 		$text .= "<Direccion CodigoMunicipio='05001' NombreCiudad='".$datosLiemMaes["cima_noci"]."' CodigoPostal='' NombreDepartamento='".$datosLiemMaes["dema_node"]."' CodigoDepartamento='".$datosLiemMaes["dema_codi"]."' Direccion='".$datosLiemMaes["cima_dire"]."' CodigoPais='CO' NombrePais='Colombia' IdiomaPais='es'/> \n";
 		$text .= "<TributoCliente CodigoTributo='01' NombreTributo='IVA'/> \n";
 		$text .= "</Cliente> \n";
 		$text .= "<MediosDePago CodigoMedioPago='ZZZ' FormaDePago='OTRO' Vencimiento='2020-02-06'> \n";
 		$text .= "</MediosDePago> \n";
//
 		//totales
 		$neto = $datosLiemMaes["lima_vtpe"]-$datosLiemMaes["lima_vdes"]
 		$baseImponible=$neto+$datosLiemMaes["lima_vfle"]-$datosLiemMaes["lima_vdfl"];
 		$iva = $baseImponible*0.19;
 		$brutoMasImpuestos = $baseImponible+$iva;
 		//totales
 		//asesor
		$sqlAsesoresWeb = $this->consultar("nombres,apellidos", "asesores_web", "idx=".$datosLiemMaes["idx_vend"], 4);
		$datosAsesoresWeb = mysqli_fetch_array($sqlAsesoresWeb);
 		$text .= "<Extensiones> \n";
 		$text .= "<DatosAdicionales> \n";
 		$text .= "<CampoAdicional Nombre='codigo' Valor='".$datosLiemMaes["clma_codi"]."'/> \n";
 		$text .= "<CampoAdicional Nombre='pedido N°' Valor='".$datosLiemMaes["lima_liem"]."'/> \n";
 		$text .= "<CampoAdicional Nombre='transportadora' Valor='".$datosLiemMaes["transport"]."'/> \n";
 		$text .= "<CampoAdicional Nombre='vendedor' Valor='".$datosAsesoresWeb["nombres"]."'/> \n";
 		$text .= "<CampoAdicional Nombre='condicionesdepago' Valor='".$datosLiemMaes["clma_copa"]."'/> \n";
 		//validar descuentos
 		$lima_vdes =($datosLiemMaes["copc_depf"]==0)?0:$datosLiemMaes["lima_vdes"];
 			//condicionado
 		if ($datosLiemMaes["copc_deco"]!=0) {
 			$porceCond = $datosLiemMaes["copc_deco"];
 			$valorCond = $datosLiemMaes["lima_vtpe"];
 			$pagueSoloCond = $brutoMasImpuestos-$valorCond;
 		}0
00  		//texto en letras
 		$textoEnLetras = numerotexto($brutoMasImpuestos);
 		$text .= "<CampoAdicional Nombre='valorenletras' Valor='".$textoEnLetras."'/> \n";
 		$text .= "<CampoAdicional Nombre='Dcto. por Pronto Pago' Valor='".$datosLiemMaes[""]."10%  $64934'/> \n";
 		$text .= "<CampoAdicional Nombre='paguesolamente' Valor='664719'/> \n";
 		$text .= "<CampoAdicional Nombre='canceleantes' Valor='06-02-2020'/> \n";
 		$text .= "<CampoAdicional Nombre='Observacion1' Valor='A esta factura se le otorgo el 10% Dcto a pie, para pago antes de 30 días. Pague oportunamete y evite perder este descuento.'/> \n";
 		$text .= "</DatosAdicionales> \n";
 		$text .= "</Extensiones> \n";
//
 		//<!--Impuestos generales de la factura-->
 		$text .= "<Totales Bruto='".$datosLiemMaes["lima_vtpe"]."' BaseImponible='".$baseImponible."' BrutoMasImpuestos='".$brutoMasImpuestos."' Cargos= '0' Descuentos='0' Impuestos='".$iva."' Retenciones='0' General='' Anticipo='0' Redondeo='0' TotalOtros1='".$datosLiemMaes["lima_vtpe"]."' Neto='".$neto."' Subtotal='".$baseImponible."' TotalDescuentosLineas='".$datosLiemMaes["lima_vdes"]."' TotalCargosLineas='0' Flete='".$datosLiemMaes["lima_vfle"]."'/>\n";
 		$text .= "<Impuestos> \n";
 		$text .= "<Impuesto Tipo='01' Nombre='IVA' Valor='".$iva."'> \n";
 		$text .= "<Subtotal ValorBase='".$baseImponible."'  Porcentaje='19'  Valor='".$iva."' CodigoUnidadMedidaBase='94'/> \n";
 		$text .= "</Impuesto> \n";
 		$text .= "</Impuestos> \n";


 		//<!-- Datos de referencias vendedidas al cliente -->
		$sqlDetaLiem = $this->consultar("*", "deta_liem", "deli_idle=".$lima_idxx, 4);
		while ($datosDetaLiem = mysqli_fetch_array($sqlDetaLiem)) {
 			$text .= "<Linea> \n";
 			$text .= "<Detalle NumeroLinea='1' Nota='' Cantidad='20' UnidadMedida='Unidad' SubTotalLinea='374976' Descripcion='LIQUIDO FRENO 1 Litro-DOT 4SL' CantidadXEmpaque='' Marca='' NombreModelo='' CantidadBase='20' UnidadCantidadBase='Unidad' PrecioUnitario='20832' ValorTotalItem='416640' /> \n";
			
		}


 		$text .= "<DatosAdicionales> \n";
 		$text .= "<CampoAdicional Nombre='linea' Valor='18'/> \n";
 		$text .= "<CampoAdicional Nombre='referencia' Valor='5802'/> \n";
 		$text .= "<CampoAdicional Nombre='equivalencia' Valor='LITRO DOT4'/> \n";
 		$text .= "</DatosAdicionales> \n";

 		//<!--Se adidiona el descunto que se ofrece PF-->
 		$text .= "<DescuentoOCargo ID='09' Indicador='false' Justificacion='Descuento directo' Porcentaje='10' ValorBase='416640' Valor='41664'  /> \n";
 		$text .= "<Impuestos> \n";
 		$text .= "<Impuesto Tipo='01' Nombre='IVA' Valor='71245.44'> \n";
 		$text .= "<Subtotal ValorBase='374976'  Porcentaje='19'  Valor='71245.44' CodigoUnidadMedidaBase='94'/> \n";
 		$text .= "</Impuesto> \n";
 		$text .= "</Impuestos> \n";
 		$text .= "</Linea> \n";



 		//<!-- Datos de referencias vendedidas al cliente 2-->
 		$text .= "<Linea> \n";
 		$text .= "<Detalle NumeroLinea='2' Nota='' Cantidad='24' UnidadMedida='Unidad' SubTotalLinea='209433.6' Descripcion='LIQUIDO FRENO Pinta 1/4 litro-DOT ASL' CantidadXEmpaque='' Marca='' NombreModelo='' CantidadBase='24' UnidadCantidadBase='Unidad' PrecioUnitario='9696' ValorTotalItem='232704' /> \n";

 		$text .= "<DatosAdicionales> \n";
 		$text .= "<CampoAdicional Nombre='linea' Valor='18'/> \n";
 		$text .= "<CampoAdicional Nombre='referencia' Valor='5808'/> \n";
 		$text .= "<CampoAdicional Nombre='equivalencia' Valor='1/4 LITRO'/> \n";
 		$text .= "</DatosAdicionales> \n";

 		//<!--Se adidiona el descunto que se ofrece PF-->
 		$text .= "<DescuentoOCargo ID='09' Indicador='false' Justificacion='Descuento directo' Porcentaje='10' ValorBase='232704' Valor='23270.4'  /> \n";
 		$text .= "<Impuestos> \n";
 		$text .= "<Impuesto Tipo='01' Nombre='IVA' Valor='39792.38'> \n";
 		$text .= "<Subtotal ValorBase='209433.6'  Porcentaje='19'  Valor='39792.38' CodigoUnidadMedidaBase='94'/> \n";
 		$text .= "</Impuesto> \n";
 		$text .= "</Impuestos> \n";
 		$text .= "</Linea> \n";

 		//<!-- Datos para la factura que lleva fletes -->
 		$text .= "<Linea> \n";
 		$text .= "<Detalle NumeroLinea='3' Cantidad='1' UnidadMedida='94' SubTotalLinea='28744' Descripcion='flete' CantidadBase='0' UnidadCantidadBase='94' PrecioUnitario='0' ValorTotalItem='0' Ocultar='true'/> \n";
 		$text .= "<precioreferencia ValorArticulo= '5461' CodigoTipoPrecio='03'> \n";
 		$text .= "</precioreferencia> \n";

 		$text .= "<Impuestos> \n";
 		$text .= "<Impuesto Tipo='01' Nombre='IVA' Valor='5461'> \n";
 		$text .= "<Subtotal ValorBase='28744' Valor='5461' Porcentaje='19' CodigoUnidadMedidaBase='94'/> \n";
 		$text .= "</Impuesto> \n";
 		$text .= "</Impuestos> \n";
 		$text .= "<CodificacionesEstandar> \n";
 		$text .= "<CodificacionEstandar CodigoArticulo='891000039'/> \n";
 		$text .= "</CodificacionesEstandar> \n";
 		$text .= "</Linea> \n";
 		$text .= "</Factura> \n";

 		$text .= " \n";
 		$text .= " \n";
		fputs($morden,$text."\r\n");
		//fclose($dorden); 
	}

	  

	 private function numerotexto ($numero) {
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
if(!isset($_SESSION["c_generaXml"])){
	$_SESSION["c_generaXml"] = new generaXml();
}

?>
