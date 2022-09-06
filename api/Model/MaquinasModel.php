<?php
require_once PROJECT_ROOT_PATH . "/Model/DataBase.php";
 
class MaquinasModel extends DataBase
{

    public function getLocomotoras(){

      $query = "SELECT * FROM maquinas;";

      return $this->select($query);
    }

    public function getFirstLocation($params)
    {
        $query = "SELECT id, latitud, longitud, nivel_tanque_porc AS tnq_porc, nivel_tanque FROM registrosmaquinarias";
        $query .= " WHERE DATE_FORMAT(fechaPLC, '%Y-%m-%d') = '" . $params['startDate'] ."' AND idMaquina = " . $params['idMachine'];

        $query .= " ORDER BY id ASC";

        $query .= " LIMIT 1";

        return $this->select($query);
    }

    public function getLastLevenTank($formatDate, $newDate, $idMachine, $typeGroup)
    {
        $query = "SELECT AVG(nivel_tanque) AS nivel_tanque FROM registrosmaquinarias";
        $query .= " WHERE DATE_FORMAT(fechaPLC, ".$formatDate.") = '" . $newDate ."' AND idMaquina = " . $idMachine;

        $query .= " GROUP BY " . $typeGroup;

        // $query .= " ORDER BY id ASC";
        $query .= " ORDER BY nivel_tanque DESC";

        $query .= " LIMIT 1";

        return $this->select($query);
    }

    public function getDataGPS($params)
    {
        $query = "SELECT id, latitud, longitud, fechaGps, nivel_tanque_porc AS tnq_porc, nivel_tanque FROM registrosmaquinarias";
        $query .= " WHERE DATE_FORMAT(fechaPLC, '%Y-%m-%d') = '" . $params['startDate'] ."' AND idMaquina = " . $params['idMachine'];

        $query .=  " AND arranque = 1";

        $query .= " ORDER BY id DESC";

        if(!$params['firstRead'])
          $query .= " LIMIT 2";

        return $this->select($query);
    }

    public function getAcumuladoDiesel($params)
    {
        $auxdate = explode(" ", $params['startDate'])[0];

        $newDate = explode("-", $auxdate);
        $typeGroup = "";
        $formatDate = "";
        $formatDate2 = "";
        $order = "";
        switch($params['type']){

            case "hour":
              $newDate = $params['startDate'];
              $typeGroup = "MINUTE(fechaPLC)";
              $formatDate = "'%Y-%m-%d %H:%i'";
              $formatDate2 = "'%Y-%m-%d %H'";
              // $order = ", STATUS_TANQUE ASC";
              $order = ", STATUS_TANQUE DESC";
              break;

            case "day":
              $newDate = $auxdate;
              $typeGroup = "HOUR(fechaPLC)";
              $formatDate = "'%Y-%m-%d %H'";
              $formatDate2 = "'%Y-%m-%d'";
              // $order = ", STATUS_TANQUE ASC";
              $order = ", STATUS_TANQUE DESC";
              break;

            case "month":
              $newDate = $newDate[0] . "-" . $newDate[1];
              $typeGroup = "DAY(fechaPLC)";
              $formatDate = "'%Y-%m-%d'";
              $formatDate2 = "'%Y-%m'";
              // $order = ", STATUS_TANQUE ASC";
              $order = ", STATUS_TANQUE DESC";
              break;

            case "year":
              $newDate = $newDate[0];
              $typeGroup = "MONTH(fechaPLC)";
              $formatDate = "'%Y-%m'";
              $formatDate2 = "'%Y'";
              // $order = ", STATUS_TANQUE ASC";
              $order = ", STATUS_TANQUE DESC";
              break;
        }

        $ultimoNivelTanque = $this->getLastLevenTank($formatDate2, $newDate, $params['idMachine'], $typeGroup)[0]['nivel_tanque'];

        $query = "SELECT DISTINCT";
        $query .= " nivel_tanque AS STATUS_TANQUE, DATE_FORMAT(fechaPLC, ". $formatDate .") AS Date_flag, ".$typeGroup." AS GROUPER";
        $query .= ", arranque, operacion";
        $query .= " FROM registrosmaquinarias WHERE DATE_FORMAT(fechaPLC, ". $formatDate2 .") = '".$newDate."' AND idmaquina = ". $params['idMachine'];
        $query .=  " AND nivel_tanque > 473";
        // $query .=  " AND arranque = 1";
        $query .= " ORDER BY DATE_FORMAT(fechaPLC, ". $formatDate .")". $order .";";

        // die( var_dump( $query ) );

        return [$this->select($query), $ultimoNivelTanque];
    }

    public function getAcumuladoKilometers()
    {
        return $this->select("SELECT * FROM registrosmaquinarias");
    }
    
    public function getAVGDiesel($params)
    {
      // $ultimoNivelTanque = $this->getLastLevenTank($params)[0]['nivel_tanque'];

      $auxdate = explode(" ", $params['startDate'])[0];

      $newDate = explode("-", $auxdate);
      $typeGroup = "";
      $formatDate = "";
      $formatDate2 = "";
      switch($params['type']){

          case "hour":
            $newDate = $params['startDate'];
            $typeGroup = "MINUTE(fechaPLC)";
            $formatDate = "'%Y-%m-%d %H:%i'";
            $formatDate2 = "'%Y-%m-%d %H'";
            break;

          case "day":
            $newDate = $auxdate;
            $typeGroup = "HOUR(fechaPLC)";
            $formatDate = "'%Y-%m-%d %H'";
            $formatDate2 = "'%Y-%m-%d'";
            break;

          case "month":
            $newDate = $newDate[0] . "-" . $newDate[1];
            $typeGroup = "DAY(fechaPLC)";
            $formatDate = "'%Y-%m-%d'";
            $formatDate2 = "'%Y-%m'";
            break;

          case "year":  
            $newDate = $newDate[0];
            $typeGroup = "MONTH(fechaPLC)";
            $formatDate = "'%Y-%m'";
            $formatDate2 = "'%Y'";
            break;
      }

      $ultimoNivelTanque = $this->getLastLevenTank($formatDate2, $newDate, $params['idMachine'], $typeGroup)[0]['nivel_tanque'];
      
      // $query = "SELECT ( ". $ultimoNivelTanque ." - AVG(nivel_tanque) ) AS STATUS_TANQUE, ". $typeGroup . " AS GROUPER";
      $query = "SELECT AVG(nivel_tanque) AS STATUS_TANQUE, ". $typeGroup . " AS GROUPER";
      // $query = "SELECT ( MAX(nivel_tanque) - MIN(nivel_tanque) ) AS STATUS_TANQUE, ". $typeGroup . " AS GROUPER";
      $query .= " FROM registrosmaquinarias WHERE DATE_FORMAT(fechaPLC, ". $formatDate2 .") = '".$newDate."' AND idmaquina = ". $params['idMachine'];
      $query .=  " AND arranque = 1";
      $query .=  " AND nivel_tanque > 473";
      $query .= " GROUP BY " . $typeGroup;
      $query .= " ORDER BY " . $typeGroup;

      // die( var_dump( $query ) );

      return [$this->select($query), $ultimoNivelTanque];
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