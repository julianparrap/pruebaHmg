// solo numeros
function soloNumeros(value){
	var key = window.Event ? value.which : value.keyCode
	return (key >= 44 && key <= 57)
}

// caracteres especiales
function caracEspec(e) {
    tecla = (document.all) ? e.keyCode : e.which;
    patron = /[A-Za-z0-9@ñÑ.,]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final);
}
// validar campos
function camposVacios(campos){
	alert("llega "+campos.length);
	for (var i = 0; i >= campos.length -1; i++) {
		alert("llega 1");
		if($("#"+campos[i]).val().length > 0){
			alert("pasa");
		}
		else{
			alert("no pasa");
		}
	}
}

//presionar enter
function presEnter(e,destino="") {
    if (e.keyCode === 13 && !e.shiftKey) {
      if (destino!="") {
      	$("#"+destino).focus();
      }
    }
}