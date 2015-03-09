/* 
 * Autor: Nelson López
 * Administración de fechas y horas
 */


function comparaFecha(fecha,fecha1){ //=0, >1, <-1
    fec=fecha.split(":");
    fec1=fecha1.split(":");
    if(fec[0]>fec1[0]){
        return 1;
    }
    else if(fec[0]<fec1[0]){
        return -1;
    }
    else{
        if(fec[1]>fec1[1]){
            return 1;
        }
        else if(fec[1]<fec1[1]){
            return -1;
        }
        else{
            if(fec[2]>fec1[2]){
                return 1;
            }
            else if(fec[2]<fec1[2]){
                return -1;
            }
            else{
                return 0;
            }
        }
    }
}


function addTimeToDate(time,unit,objDate,dateReference){
    var dateTemp=(dateReference)?objDate:new Date(objDate);
    switch(unit){
        case 'y':
            dateTemp.setFullYear(objDate.getFullYear()+time);
            break;
        case 'M':
            dateTemp.setMonth(objDate.getMonth()+time);
            break;
        case 'w':
            dateTemp.setTime(dateTemp.getTime()+(time*7*24*60*60*1000));
            break;
        case 'd':
            dateTemp.setTime(dateTemp.getTime()+(time*24*60*60*1000));
            break;
        case 'h':
            dateTemp.setTime(dateTemp.getTime()+(time*60*60*1000));
            break;
        case 'm':
            dateTemp.setTime(dateTemp.getTime()+(time*60*1000));
            break;
        case 's':
            dateTemp.setTime(dateTemp.getTime()+(time*1000));
            break;
        default :
            dateTemp.setTime(dateTemp.getTime()+time);
            break;
    }
    return dateTemp;
}

function padNmb(nStr, nLen){
    var sRes = String(nStr);
    var sCeros = "0000000000";
    return sCeros.substr(0, nLen - sRes.length) + sRes;
}

function stringToSeconds(tiempo){
    var sep1 = tiempo.indexOf(":");
    var sep2 = tiempo.lastIndexOf(":");
    var hor = tiempo.substr(0, sep1);
    var min = tiempo.substr(sep1 + 1, sep2 - sep1 - 1);
    var sec = tiempo.substr(sep2 + 1);
    return (Number(sec) + (Number(min) * 60) + (Number(hor) * 3600));
}

function secondsToTime(secs){
    var hor = Math.floor(secs / 3600);
    var min = Math.floor((secs - (hor * 3600)) / 60);
    var sec = secs - (hor * 3600) - (min * 60);
    return padNmb(hor, 2) + ":" + padNmb(min, 2) + ":" + padNmb(sec, 2);
}

function substractTimes(t1, t2){
    var secs1 = stringToSeconds(t1);
    var secs2 = stringToSeconds(t2);
    var secsDif = secs1 - secs2;
    return secondsToTime(secsDif);
}

function sumarHoras(t1, t2){
    var secs1 = stringToSeconds(t1);
    var secs2 = stringToSeconds(t2);
    var secsDif = secs1 + secs2;
    return secondsToTime(secsDif);
}

function sumarHoras2(t1, t2, t3){
    var secs1 = stringToSeconds(t1);
    var secs2 = stringToSeconds(t2);
    var secs3 = stringToSeconds(t3);
    var secsDif = secs1 + secs2 + secs3;
    return secondsToTime(secsDif);
}
