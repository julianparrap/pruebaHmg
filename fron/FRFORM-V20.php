<?php
	$tituloFavicon = "Facturación - Redsa";
	$tituloPagina = "FACTURACIÓN";
	require_once("FRHEAD-V20.php");
?>
	<!-- inicio-javascript" -->
	<script type="text/javascript">
		function contenido(){
			var accion = "mostrarContenido";
			$.ajax({
				type :"POST",
				url : "../func/php/FUFACT-V20.php",
				data: "accion="+accion,
				success:function(data){
					$("#bloquea").css({'display':'none'});	
					$("#contenido").html(data);
					$("#clma_codi").focus();
				}
			});
		}   
	 
		function buscar(){
			var caso = "buscar";
			var clma_codi = $("#clma_codi").val();
			$.ajax({
				type :"POST",
				url : "../../funciones/php/FFAC-V20.php",
				data: "caso="+caso+"&clma_codi="+clma_codi,
				beforeSend :function(){
					$("#bloquea").css({'display':'block'});
				},
				success:function(data){
					$("#bloquea").css({'display':'none'});
					$("#contenido").html(data);
					$("#clma_codi").focus();
				}
			});
		}  
	 
		function seleccionar_todo(){
			var checkboxes = document.getElementById("formulario").checkbox; //Array que contiene los checkbox
			for(var x=1; x < checkboxes.length; x++){
				if(checkboxes[0].checked){
					checkboxes[x].checked = true;
				}
				else{
					checkboxes[x].checked = false;
				} 
			}
			contar(0);
		}
 
 
		function contar(ind){
			var checkboxes = document.getElementById("formulario").checkbox; //Array que contiene los checkbox
			var valores = "";
			if(ind == "1"){
				checkboxes[0].checked = false;
			}
			if(checkboxes.length > 1){
				for(var x=1; x < checkboxes.length; x++){
					if(checkboxes[x].checked){
						if(valores == ""){
							valores = checkboxes[x].value;
						}
						else{
							valores = valores + "-" + checkboxes[x].value;
						}
					}
				}
			}
			else{
				if(checkboxes.checked){
					valores = checkboxes.value;
				}
			}
			$("#pedidos").val(valores);
		}
	 
		function facturar(){
			var caso = 3;
			var lima_liem = $("#pedidos").val();
			if(lima_liem == ""){
				jAlert("No ha seleccionado ningun pedido.", "PEDIDOS POR FACTURAR");
				return;
			}	 
			$.ajax({
				type :"POST",
				url : "../../funciones/php/FFAC-V20.php",
				data: "caso="+caso+"&lima_liem="+lima_liem,
				beforeSend :function(){
					$("#bloquea").css({'display':'block'});
				},
				success:function(data){
					$("#bloquea").css({'display':'none'});
					$("#clma_codi").focus();
					contenido();
				} 
			});
		}

		function exportar(){
			var cliente = $("#pedidos").val();
			var accion = "exportar";
			if (cliente == 0 || cliente == "") {
				swal("No se encontraron pedidos a exportar");
				return;
			}
			$.ajax({
				type :"POST",
				url : "../func/php/FUFACT-V20.php",
				data : "cliente="+cliente+"&accion="+accion,
				beforeSend :function(){
					$("#bloquea").css({'display':'block'});
				},
				success:function(data){
					if(data == "No se encontraron registros para exportar"){
						jAlert(data,"EXPORTAR PEDIDOS");
						$("#bloquea").css({'display':'none'});
					}
					else{
						jAlert(data,"EXPORTAR PEDIDOS");
						$("#bloquea").css({'display':'none'});
						location.reload();
					} 
				}
			});
		}

		function valiFactElec(lima_idxx,cantidad,clma_codi,clma_nomb){
			/*if (cantidad==1) {
				//ajax para generar el xml de la factura 
				$.ajax({
					type :"POST",
					url : "../func/php/FUXML-V20.php",
					data: "accion=generarFactura&lima_idxx="+matr_lima_idxx,
					beforeSend :function(){
						$("#bloquea").css({'display':'block'});
					},
					success:function(data){
						$("#bloquea").css({'display':'none'});
						//ajax para correr el xml de la facturacion electronica             
						//ejecutarXmlFactElect(data);
					} 
				});
				
			}
			else{*/
				var matr_lima_idxx = lima_idxx.split("-");
				for (var i = 0; i < matr_lima_idxx.length; i++) {
					//ajax para generar el xml de la factura 
					$.ajax({
						type :"POST",
						url : "../func/php/FUXML-V20.php",
						data: "accion=generarFactura&lima_idxx="+matr_lima_idxx[i],
						beforeSend :function(){
							$("#bloquea").css({'display':'block'});
						},
						success:function(data){
							$("#bloquea").css({'display':'none'});
							if (data=="precio") {
								swal("Referencias con precio en cero (0), porfavor revisar detalle del pedido.");
							}
							else{
								//ajax para correr el xml de la facturacion electronica
								$.ajax({
									type :"POST",
									url : "../plcolab/index.php",
									data: "accion=generarFactura&lima_idxx="+data+"&origen=FACT",
									beforeSend :function(){
										$("#bloquea").css({'display':'block'});
									},
									success:function(data){
										$("#bloquea").css({'display':'none'});
										//funcion para guardar la respuesta del json en la base de datos
										//validarJson();
										contenido();
									} 
								}); 
							}
							//ejecutarXmlFactElect(data);
						} 
					});
				}
			//}
		}

	function ventFactElec(clma_codi,clma_nomb){
		$.ajax({
			type :"POST",
			url : "../func/php/FUFACT-V20.php",
			data: "accion=factCliente&clma_codi="+clma_codi,
			beforeSend :function(){
				$("#bloquea").css({'display':'block'});
			},
			success:function(data){
				$("#bloquea").css({'display':'none'});
				swal({
					html:"<center><h3>FACTURAS <br> "+clma_codi+" - "+clma_nomb+"</h3></center><div>"+data+"</div>",
					width:"800px",
					showCancelButton: true,
					allowOutsideClick: false,
					cancelButtonText:"Cancelar"
				}).then(function () {

				});
			} 
		});
	}

	//funcion para correr el xml de la facturacion electronica 
	function ejecutarXmlFactElect(lima_liem){
		$.ajax({
			type :"POST",
			url : "../plcolab/index.php",
			data: "accion=generarFactura&lima_idxx="+lima_idxx+"&lima_liem="+lima_liem,
			beforeSend :function(){
				$("#bloquea").css({'display':'block'});
			},
			success:function(data){
				$("#bloquea").css({'display':'none'});
				//funcion para guardar la respuesta del json en la base de datos
				validarJson();
			} 
		});
	}

	//funcion para guardar la respuesta del json en la base de datos
	function validarJson(){
		alert("llega");
		$.ajax({
			type :"POST",
			url : "../func/php/FUXML-V20.php",
			data: "accion=validarJson",
			beforeSend :function(){
				$("#bloquea").css({'display':'block'});
			},
			success:function(data){
				$("#bloquea").css({'display':'none'});
			} 
		});
	}

	 //Agregar fletes la pedido
		function agrgarFlete(lima_idxx=0){	
			swal({
				title: 'Cargue de fletes',
				html: '<h3 style="text-align:left">Fletes</h3><input type="text" class="form-control" name="lima_vfle" id="lima_vfle" placeholder="Valor en fletes" style="width:200px" onkeypress="event soloNumeros()"><hr><h3 style="text-align:left">Descuento en fletes</h3><input type="text" class="form-control" name="lima_vdfl" id="lima_vdfl" placeholder="Desuento en fletes" style="width:200px">', 
				inputAttributes: {
					autocapitalize: 'off'
				},
				width:'350px',
				showCancelButton: true,
				confirmButtonText: 'Guardar',
				showLoaderOnConfirm: true
			}).then((result) => {
				var lima_vfle = $("#lima_vfle").val();
				//alert(new Intl.NumberFormat("en-IN").format(lima_vfle));
				var lima_vdfl = $("#lima_vdfl").val();
				if (lima_vfle === false) return false;
				if (lima_vfle === "" || isNaN(lima_vfle)) {
					swal({
						text:'Debe agregar el valor de los fletes.',
						type: 'success'
					});
//			    swal("Debe agregar el valor de los fletes.");
					return false;
				}
				$.ajax({
					type: "POST",
					url : "../func/php/FUFACT-V20.php",
					data: "accion=agrgarFlete&lima_idxx="+lima_idxx+"&lima_vfle="+lima_vfle+"&lima_vdfl="+lima_vdfl,
					beforeSend :function(){
						$("#bloquea").css({'display':'block'});
					},
					success:function(data){
						$("#bloquea").css({'display':'none'});
						contenido();
					} 
				});
			});  	
		}

		//
		function formatomunerico(input){
			var valor = new Intl.NumberFormat("en-IN").format($("#"+input).val());

			$("#"+input).val(valor);
		}
	</script>
	<!-- fin-javascript" -->
<body onload="cerrarMenu();contenido();">
<?php require_once("FRMENU_V20.php"); ?>
	<!-- inicio-contenido" -->
	<div id='contenido' name='contenido'>
	
	</div>
	<!-- fin-contenido" -->
<?php require_once("FRFOOTER-V20.php"); ?>