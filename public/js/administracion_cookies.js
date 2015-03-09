/* 
 * Administracion de cookies
 * Sirve para activar el boton siguiente para el tipo de calificacion
 * B: Pantalla con 4 botones
 * C: Pantalla matriz
 */


function CojerValorCookie(indice) {
    //indice indica el comienzo del valor
    var galleta = document.cookie
    //busca el final del valor, dado por ;, a partir de indice
    var finDeCadena = galleta.indexOf(";", indice)
    //si no existe el ;, el final del valor lo marca la longitud total de la cookie
    if (finDeCadena == -1)
        finDeCadena = galleta.length
    return unescape(galleta.substring(indice, finDeCadena))
}

function CojerCookie(nombre) {
    var galleta = document.cookie
    //construye la cadena con el nombre del valor
    var arg = nombre + "="
    var alen = arg.length			//longitud del nombre del valor
    var glen = galleta.length		//longitud de la cookie
    var i = 0
    while (i < glen) {
        var j = i + alen			//posiciona j al final del nombre del valor
        if (galleta.substring(i, j) == arg)	//si en la cookie estamo ya en nombre del valor
            return CojerValorCookie(j)	//devuleve el valor, que esta a partir de j
        i = galleta.indexOf(" ", i) + 1		//pasa al siguiente
        if (i == 0)
            break				//fin de la cookie
    }
    return null					//no se encuentra el nombre del valor
}

function GuardarCookie(nombre, valor, caducidad) {
    if(!caducidad)
        caducidad = Caduca(0)
    //crea la cookie: incluye el nombre, la caducidad y la ruta donde esta guardada
    //cada valor esta separado por ; y un espacio
    document.cookie = nombre + "=" + escape(valor) + "; expires=" + caducidad + "; path=/"
}

function Caduca(dias) {
    var hoy = new Date()					//coge la fecha actual
    var msEnXDias = eval(dias) * 24 * 60 * 60 * 1000	//pasa los dias a mseg.
    hoy.setTime(hoy.getTime() + msEnXDias)			//fecha de caducidad: actual + caducidad
    return (hoy.toGMTString())
}

function MostrarCookie(nombre) {
    if(CojerCookie(nombre) != null)
        return (CojerCookie(nombre))
    else
        return null
}

   
