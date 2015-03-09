// Creaci�n del objeto
function objetoAjax(){
	var xmlhttp=false;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			xmlhttp = false;
  		}
	}

	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}
// Funci�n que recibe los par�metros enviados por grafico1.php, desde los enlaces de cada barra.
// Env�a esos datos a grafico2.php para que los procese.
// Una vez procesados esos datos, se despliega en pantalla el gr�fico detalle en el DIV "detalle_chart" haciendose ahora visible.
function detalleAnios(modulos, total) {
	detalleDiv = document.getElementById('detalle_chart');
	detalleDiv.innerHTML = "";
	ajax = objetoAjax();
	ajax.open("POST", "Graficador2");
	ajax.onreadystatechange = function() {
		if(ajax.readyState == 4) {
				detalleDiv.innerHTML = ajax.responseText
				detalleDiv.style.display="block";
		}
	}
//        anio.split(",");
//        var arrFruta = anio.split("");
        var modulo=new Array();
        var totales=new Array();
        var pos=modulos.lastIndexOf(' ');
        var cort=modulos.substring(pos+1);
        var cort1=total.substring(5);
        var cadm="";
        var cadt="";
        modulo=cort.split(',');
        totales=cort1.split(',');
        for(var i in modulo){
          cadm+=modulo[i]+','
          cadt+=totales[i]+','
        } 
        //alert(cadt);
        var f=cadt.lastIndexOf(',')
        var fi=cadm.lastIndexOf(',')
        var cadtot=cadt.substring(0,f)
        var cadmt=cadm.substring(0,fi)
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajax.send("calcular="+cadtot+"&moduloId="+cadmt)
}