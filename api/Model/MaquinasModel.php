<?php
require_once PROJECT_ROOT_PATH . "/Model/DataBase.php";
 
class MaquinasModel extends DataBase
{
    public function getFirstLocation($params)
    {
        $query = "SELECT id, latitud, longitud, nivel_tanque_porc AS tnq_porc, nivel_tanque FROM registrosmaquinarias";
        $query .= " WHERE DATE_FORMAT(fechaRegistro, '%Y-%m-%d') = '" . $params['startDate'] ."' AND idMaquina = " . $params['machineSelected'];

        $query .= " ORDER BY id ASC";

        $query .= " LIMIT 1";

        // die( var_dump( $query ) );

        return $this->select($query);
    }

    public function getDataGPS($params)
    {
        $query = "SELECT id, latitud, longitud, fechaGps, nivel_tanque_porc AS tnq_porc, nivel_tanque FROM registrosmaquinarias";
        $query .= " WHERE DATE_FORMAT(fechaRegistro, '%Y-%m-%d') = '" . $params['startDate'] ."' AND idMaquina = " . $params['machineSelected'];

        $query .= " AND id >= " . $params['id']-1;

        $query .= " ORDER BY id DESC";

        // $query .= " LIMIT 2";

        // die( var_dump( $query ) );

        return $this->select($query);
    }

    public function getAcumuladoDiesel($params)
    {        
        $newDate = explode("-", $params['startDate']);;
        $typeGroup = "";
        $formatDate = "";
        switch($params['type']){

            case "day":
              $newDate = $params['startDate'];
              $typeGroup = "HOUR(fechaRegistro)";
              $formatDate = "'%Y-%m-%d'";
              break;

            case "month":
              $newDate = $newDate[0] . "-" . $newDate[1];
              $typeGroup = "DAY(fechaRegistro)";
              $formatDate = "'%Y-%m'";
              break;

            case "year":
              $newDate = $newDate[0];
              $typeGroup = "MONTH(fechaRegistro)";
              $formatDate = "'%Y'";
              break;
        }

        $query = "SELECT SUM(nivel_tanque) AS STATUS_TANQUE, ". $typeGroup . " AS GROUPER";
        $query .= " FROM registrosmaquinarias WHERE DATE_FORMAT(fechaRegistro, ". $formatDate .") = '".$newDate."' AND idmaquina = ". $params['idMachine'];
        $query .= " GROUP BY " . $typeGroup;
        $query .= " ORDER BY " . $typeGroup;
// die( var_dump( $query ) );
        return $this->select($query);
    }

    public function getAcumuladoKilometers()
    {
        return $this->select("SELECT * FROM registrosmaquinarias");
    }
    
    public function getAVGDiesel($params)
    {
      $newDate = explode("-", $params['startDate']);;
      $typeGroup = "";
      $formatDate = "";
      switch($params['type']){

          case "day":
            $newDate = $params['startDate'];
            $typeGroup = "HOUR(fechaRegistro)";
            $formatDate = "'%Y-%m-%d'";
            break;

          case "month":
            $newDate = $newDate[0] . "-" . $newDate[1];
            $typeGroup = "DAY(fechaRegistro)";
            $formatDate = "'%Y-%m'";
            break;

          case "year":
            $newDate = $newDate[0];
            $typeGroup = "MONTH(fechaRegistro)";
            $formatDate = "'%Y'";
            break;
      }

      $query = "SELECT AVG(nivel_tanque) AS STATUS_TANQUE, ". $typeGroup . " AS GROUPER";
      $query .= " FROM registrosmaquinarias WHERE DATE_FORMAT(fechaRegistro, ". $formatDate .") = '".$newDate."' AND idmaquina = ". $params['idMachine'];
      $query .= " GROUP BY " . $typeGroup;
      $query .= " ORDER BY " . $typeGroup;

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