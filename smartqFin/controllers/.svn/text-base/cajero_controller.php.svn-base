<?php
class CajeroController extends ApplicationController {

    public $id_turno;
    public $fecha_inicio_atencion;
    public $hora_inicio_atencion;
    public $carpeta;

    public function initialize() {
        $this->setPersistance(true);
        $empresa= new Empresa();
        $buscaEmpresa= $empresa->findFirst("columns: carpeta");
        $this->carpeta= $buscaEmpresa->getCarpeta();
    }
    public function indexAction() {
        //busco el id del usuario que ha iniciado la sesión
        $dataUsuario = SessionNamespace::get('datosUsuarioSMC');
        $idsesion = $dataUsuario->getId();
        $usuario= $dataUsuario->getNombre();

        $sql=new ActiveRecordJoin(array("entities"=>array ("Usuario","Caja","Usercaja"),
                        "fields"=>array ("{#Caja}.id","{#Caja}.numero_caja","{#Caja}.descripcion"),
                        "conditions"=>"{#Usercaja}.usuario_id=$idsesion"));
        foreach ($sql->getResultSet() as $result) {
            $idcaja= $result->getId();
            $numcaja1= $result->getNumeroCaja();
        }

        $turnoactual=0;
        $turnoespera=0;
        Tag::displayTo('actual1', $turnoactual);
        Tag::displayTo('espera1', $turnoespera);
        Tag::displayTo('numcaja', $numcaja1);
        Tag::displayTo('idcaja', $idcaja);
        Tag::displayTo('usuario', $usuario);

    }

    public function salirAction() {
        //            $dataUsuario = SessionNamespace::get('datosUsuario');
        //            $dat = Session::unsetData();
        //            print(Session::getId());die();

        SessionNamespace::drop("datosUsuarioSMC");
        //$this->routeTo("action: index");
        $this->routeTo("controller: login");
    }

    /**
     * Funci�n que permite guardar el turno para los cajeros y no los operadores
     */
    public $fecha_inicio_atencion_caja;
    public $hora_inicio_atencion_caja;
    public function siguientecajaAction() {
        $this->setResponse("json");
        $numcaja= $this->getPostParam('caja');
        $idcaja= $this->getPostParam('idcaja');

        $fecha_hoy=date("Y-m-d");
        $hora_hoy=date("H:i:s");

        //INICIO ENVIAR VALOR 1 A TIMBRE PARA CAJEROS
        $pantalla = new Pantalla();
        $condicion ="tipo_pantalla= 'Pantalla Cajero'";
        $pantalla->updateAll("timbre=1","conditions: $condicion");
        //FIN ENVIAR VALOR 1 A TIMBRE PARA CAJEROS

        //INICIO ENVIAR A DISPLAYCAJAS
        //Sirve para ver en la pantalla los turnos de los cajeros
        $displaycaja= new Displaycajas();
        $displaycaja->setCajanumero($numcaja);
        $displaycaja->setUbicacion('1');
        $displaycaja->save();
        //FIN ENVIAR A DISPLAYCAJAS

        //INICIO PRIMERO GUARDAR LA COLA ATENDIDA
        //--inicio calcula la duración
        $fun = new Funciones();
        $fi=$this->fecha_inicio_atencion_caja." ".$this->hora_inicio_atencion_caja;
        $ff=date("Y-m-d")." ".date("H:i:s");
        $duracion= $fun->difFecha($fi, $ff);
        //--fin calcula la duración
        $fecha_fin_atencion=date("Y-m-d");
        $hora_fin_atencion=date("H:i:s");
        $colas = new Colas();
        $colas->updateAll("atendido= 1, fecha_fin_atencion= '$fecha_fin_atencion', hora_fin_atencion= '$hora_fin_atencion', duracion= '$duracion'","caja_id=$idcaja AND atendido=0 AND fecha_inicio_atencion= '$fecha_hoy'");
        //FIN PRIMERO GUARDAR LA COLA ATENDIDA

        //SEGUNDO INSERTA EL REGISTRO DE CAJERO QUE LLAMA
        $colas = new Colas();
        $id=null;
        $colas->setId($id);
        $colas->setCajaId($idcaja);
        $colas->setPorAtender(1);
        $colas->setAtendido(0);
        $colas->setFechaInicioAtencion(date("Y-m-d"));
        $colas->setHoraInicioAtencion(date("H:i:s"));
        $colas->setFechaFinAtencion("0000-00-00");
        $colas->setHoraFinAtencion("00:00:00");
        $colas->setDuracion("00:00:00");
        $colas->setCalificacion("");
        //$colas->setCreacionAt();
        $colas->save();
        //FIN SEGUNDO INSERTA EL REGISTRO DE CAJERO QUE LLAMA

        $this->fecha_inicio_atencion_caja=date("Y-m-d");    //para el siguiente registro cuando actualice
        $this->hora_inicio_atencion_caja=date("H:i:s");     //para el siguiente registro cuando actualice
    }

    public function terminarcolaAction() {
        $this->setResponse("json");
        $numcaja= $this->getPostParam('caja');
        $idcaja= $this->getPostParam('idcaja');

        //INICIO PRIMERO GUARDAR LA COLA ATENDIDA
        /*inicio calcula la duración*/
        $fecha_hoy=date("Y-m-d");
        $hora_hoy=date("H:i:s");
        $fecha_fin_atencion=date("Y-m-d");
        $hora_fin_atencion=date("H:i:s");
        $fun = new Funciones();

        $fi=$this->fecha_inicio_atencion_caja." ".$this->hora_inicio_atencion_caja;
        $ff=$fecha_hoy." ".$hora_hoy;
        $duracion= $fun->difFecha($fi, $ff);
        //echo $fi."   ".$ff; die();
        //$duracion='00:02:00';
        /*fin calcula la duración*/
        $colas = new Colas();
        //$buscaTurno=$turno->findFirst("caja= $caja_id AND por_atender= 1");
        //$id_turno_por_atender=$buscaTurno->getId();
        $colas->updateAll("atendido= 1, fecha_fin_atencion= '$fecha_fin_atencion', hora_fin_atencion= '$hora_fin_atencion', duracion= '$duracion'","caja_id=$idcaja AND atendido=0 AND fecha_inicio_atencion= '$fecha_hoy'");
        //FIN PRIMERO GUARDAR LA COLA ATENDIDA
    }
}
?>
