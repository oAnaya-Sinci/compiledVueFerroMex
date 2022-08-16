<?php
require_once PROJECT_ROOT_PATH . "/Model/DataBase.php";
 
class MaquinasModel extends DataBase
{
    public function getDataGPS($params)
    {
        $query = "SELECT latitud, longitud FROM registrosmaquinarias WHERE fechaRegistro BETWEEN '" . $params['startDate'] ."' AND '" . $params['endedDate'] . "' AND idMaquina = " . $params['machineSelected'] . ";";
        return $this->select($query);
    }

    public function getAcumuladoDiesel($params)
    {
        $query = "SELECT SUM(nivel_tanque) AS suma_ FROM registrosmaquinarias WHERE idmaquina = ". $params["idMaquina"] ." GROUP BY ". $params["valueTypeTime"] . ";";
        return $this->select($query);
    }

    public function getAcumuladoKilometers()
    {
        return $this->select("SELECT * FROM registrosmaquinarias");
    }
    
    public function getAVGDiesel($params)
    {
        $query = "SELECT AVG(nivel_tanque) AS prom_nt FROM registrosmaquinarias WHERE idmaquina = ". $params["idMaquina"] ." GROUP BY ". $params["valueTypeTime"] . ";";
        return $this->select($query);
    }

    public function getAVGKilometers()
    {
        return $this->select("SELECT * FROM registrosmaquinarias");
    }
    
    public function getRegistroNotificacion()
    {
        return $this->select("SELECT * FROM registrosmaquinarias");
    }
    
    public function setRegistroNotificacion($params)
    {
        return $this->select("INSERT INTO alarmsLogs() VALUES()");
    }
}