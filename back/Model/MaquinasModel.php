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
        $query = "SELECT id, latitud, longitud, fechaGps, nivel_tanque_porc AS tnq_porc, nivel_tanque, arranque, operacion, UNIX_TIMESTAMP( fechaPLC ) AS timestamp_PLC, fechaPLC FROM registrosmaquinarias";
        $query .= " WHERE DATE_FORMAT(fechaPLC, '%Y-%m-%d') = '" . $params['startDate'] ."' AND idMaquina = " . $params['idMachine'];

        $query .=  " AND arranque = 1";

        $query .= " ORDER BY id ASC";

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
        $query .= " AND nivel_tanque > 473";
        // $query .=  " AND arranque = 1";
        $query .= " ORDER BY DATE_FORMAT(fechaPLC, ". $formatDate .")". $order .";";

        // $query = "SELECT";
        // $query .= " AVG(nivel_tanque) AS STATUS_TANQUE, DATE_FORMAT(fechaPLC, ". $formatDate .") AS Date_flag, ".$typeGroup." AS GROUPER";
        // $query .= ", arranque, operacion";
        // $query .= " FROM registrosmaquinarias WHERE DATE_FORMAT(fechaPLC, ". $formatDate2 .") = '".$newDate."' AND idmaquina = ". $params['idMachine'];
        // $query .= " AND nivel_tanque > 473";
        // $query .= " GROUP BY DATE_FORMAT(fechaPLC, ". $formatDate ."), " . $typeGroup . ", operacion, arranque";
        // $query .= " ORDER BY DATE_FORMAT(fechaPLC, ". $formatDate .")". $order .";";

        // die( var_dump( $query ) );

        $nivelDiesel = $this->select($query);

        $query = "SELECT DISTINCT";
        $query .= " DATE_FORMAT(fechaPLC, '%Y-%m-%d %H') AS Date_flag, operacion";
        $query .= " FROM registrosmaquinarias WHERE DATE_FORMAT(fechaPLC, ". $formatDate2 .") = '".$newDate."' AND operacion = 1 AND idmaquina = ". $params['idMachine'];
        $query .= " GROUP BY DATE_FORMAT(fechaPLC, '%Y-%m-%d %H');";

        $operacionData = $this->select($query);

        return [$nivelDiesel, $ultimoNivelTanque, $operacionData];
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
      
      $query = "SELECT";
      // $query = " ( ". $ultimoNivelTanque ." - AVG(nivel_tanque) ) AS STATUS_TANQUE, ". $typeGroup . " AS GROUPER";
      $query .= " AVG(nivel_tanque) AS STATUS_TANQUE, ". $typeGroup . " AS GROUPER, IF(operacion = 1, 'Motor On, Opreacion On', 'Motor On, Operacion Off') AS operacion, operacion AS oprc";
      // $query = " ( MAX(nivel_tanque) - MIN(nivel_tanque) ) AS STATUS_TANQUE, ". $typeGroup . " AS GROUPER";
      $query .= " FROM registrosmaquinarias WHERE DATE_FORMAT(fechaPLC, ". $formatDate2 .") = '".$newDate."' AND idmaquina = ". $params['idMachine'];
      $query .=  " AND arranque = 1";
      $query .=  " AND nivel_tanque > 473";
      $query .= " GROUP BY " . $typeGroup . ", operacion";
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
        return $this->select("SELECT id, codigoError AS codigo, mensajeError AS mensaje, tipoError AS tipo, idMaquina AS maquina, fechaRegistro AS fecha, fechaMaquina AS fecha_plc FROM alarmsLogs");
        // return $this->select("SELECT * FROM registrosmaquinarias");
    }
    
    public function setRegistroNotificacion($params)
    {
      // $data = json_decode( $params, true )['data'];
      $data = json_decode( $params, true );

      $dataMessage = $data['dataError'];
      $dataFromDB = $data['dataError']['dataDB'];

      // die( var_dump( $data ) );
      
      $query = "INSERT INTO alarmsLogs(codigoError, mensajeError, tipoError, idMaquina, fechaRegistro, fechaMaquina)";
      $query .= "VALUES( '".$dataMessage['codigoError']."', '".$dataMessage['message']."', '".$dataMessage['typeError']."', ". $dataFromDB['idMaquina'] .", NOW(), '". $dataFromDB['fecha'] ."' )";
  
      // die( var_dump( $query ) );

      return $this->insert($query);
    }

    public function getDecensoDiesel($params)
    {
        return $this->select("SELECT AVG( nivel_tanque ) AS TOT_NIVEL, idMaquina, fechaPLC FROM registrosmaquinarias WHERE DATE_FORMAT(fechaPLC, '%Y-%m-%d %H') = '" . $params['date'] . "' GROUP BY fechaPLC, idMaquina; ");
    }

    public function getDataTable($params){

      $query = "SELECT";
      // $query .= " ( MAX(nivel_tanque) - MIN(nivel_tanque) ) AS consumo, HOUR(fechaPLC) AS hora , IF(operacion = 1, 'Motor On, Opreacion On', 'Motor On, Operacion Off') AS operacion, operacion AS oprc";
      $query .= " AVG(nivel_tanque) AS STATUS_TANQUE, HOUR(fechaPLC) AS GROUPER , IF(operacion = 1, 'Motor On, Opreacion On', 'Motor On, Operacion Off') AS operacion, operacion AS oprc";
      $query .= " FROM registrosmaquinarias";
      $query .= " WHERE DATE_FORMAT(fechaPLC, '%Y-%m-%d') = '". $params['date'] ."' ";
      // $query .= " WHERE DATE_FORMAT(fechaPLC, '%Y-%m-%d') = '2022-09-02' ";
      $query .= " AND idmaquina = ". $params['idMachine'] ." AND nivel_tanque > 473 AND arranque = 1";
      $query .= " GROUP BY HOUR(fechaPLC), operacion";
      $query .= " ORDER BY HOUR(fechaPLC)";

      // die( var_dump( $query ) );

      $ultimoNivelTanque = $this->getLastLevenTank("'%Y-%m-%d'", $params['date'], $params['idMachine'], 'HOUR(fechaPLC)')[0]['nivel_tanque'];

      return [$this->select( $query ), $ultimoNivelTanque];
      // return $this->select( $query );
    }
}