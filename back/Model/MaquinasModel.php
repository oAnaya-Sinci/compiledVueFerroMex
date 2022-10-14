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

    public function getLastLevenTank($formatDate, $newDate, $idMachine, $typeGroup="noMonth")
    {
      $query = "";
      
      if( $typeGroup == "DAY(fechaPLC)" ){
       
        $query = "SELECT AVG(nivel_tanque) AS STATUS_TANQUE, ". $typeGroup ." AS GROUPER FROM registrosmaquinarias";
        $query .= " WHERE DATE_FORMAT(fechaPLC, ".$formatDate.") = '" . $newDate ."' AND idMaquina = " . $idMachine;
        // $query .= " AND nivel_tanque > 473";
        $query .= " GROUP BY " . $typeGroup;
        $query .= " ORDER BY " . $typeGroup;
        // $query .= " LIMIT 1";
      }else{
       
        $query = "SELECT nivel_tanque FROM registrosmaquinarias";
        $query .= " WHERE DATE_FORMAT(fechaPLC, ".$formatDate.") = '" . $newDate ."' AND idMaquina = " . $idMachine;
        // $query .= " AND arranque = 1";
        // $query .= " AND nivel_tanque > 473";
        // $query .= " GROUP BY " . $typeGroup;
        $query .= " ORDER BY nivel_tanque DESC";
        // $query .= " LIMIT 1";
      }

        return $this->select($query);
    }

    public function getDataGPS($params)
    {
        $query = "SELECT id, latitud, longitud, fechaGps, nivel_tanque_porc AS tnq_porc, nivel_tanque, arranque, operacion, UNIX_TIMESTAMP( fechaPLC ) AS timestamp_PLC, fechaPLC FROM registrosmaquinarias";
        $query .= " WHERE DATE_FORMAT(fechaPLC, '%Y-%m-%d') = '" . $params['startDate'] ."' AND idMaquina = " . $params['idMachine'];

        $params['allData'] != 'false' ? "" : $query .=  " AND arranque = 1";

        $query .= " ORDER BY id ASC";

        if(!$params['firstRead'])
          $query .= " LIMIT 2";

        return $this->select($query);
    }

    public function getAcumuladoDiesel($params)
    {
        // $auxdate = explode(" ", $params['startDate'])[0];

        // $newDate = explode("-", $auxdate);
        // $typeGroup = "";
        // $formatDate = "";
        // $formatDate2 = "";
        // $order = "";
        // switch($params['type']){

        //     case "hour":
        //       $newDate = $params['startDate'];
        //       $typeGroup = "MINUTE(fechaPLC)";
        //       $formatDate = "'%Y-%m-%d %H:%i'";
        //       $formatDate2 = "'%Y-%m-%d %H'";
        //       // $order = ", STATUS_TANQUE ASC";
        //       $order = ", STATUS_TANQUE DESC";
        //       break;

        //     case "day":
        //       $newDate = $auxdate;
        //       $typeGroup = "HOUR(fechaPLC)";
        //       $formatDate = "'%Y-%m-%d %H'";
        //       $formatDate2 = "'%Y-%m-%d'";
        //       // $order = ", STATUS_TANQUE ASC";
        //       $order = ", STATUS_TANQUE DESC";
        //       break; 

        //     case "month":
        //       $newDate = $newDate[0] . "-" . $newDate[1];
        //       $typeGroup = "DAY(fechaPLC)";
        //       $formatDate = "'%Y-%m-%d'";
        //       $formatDate2 = "'%Y-%m'";
        //       // $order = ", STATUS_TANQUE ASC";
        //       $order = ", STATUS_TANQUE DESC";
        //       break;

        //     case "year":
        //       $newDate = $newDate[0];
        //       $typeGroup = "MONTH(fechaPLC)";
        //       $formatDate = "'%Y-%m'";
        //       $formatDate2 = "'%Y'";
        //       // $order = ", STATUS_TANQUE ASC";
        //       $order = ", STATUS_TANQUE DESC";
        //       break;
        // }

        // $ultimoNivelTanque = $this->getLastLevenTank($formatDate2, $newDate, $params['idMachine'])[0]['nivel_tanque'];

        // $query = "SELECT DISTINCT";
        // $query .= " nivel_tanque AS STATUS_TANQUE, DATE_FORMAT(fechaPLC, ". $formatDate .") AS Date_flag, ".$typeGroup." AS GROUPER";
        // $query .= ", arranque, operacion";
        // $query .= " FROM registrosmaquinarias WHERE DATE_FORMAT(fechaPLC, ". $formatDate2 .") = '".$newDate."' AND idmaquina = ". $params['idMachine'];
        // $query .= " AND nivel_tanque > 473";
        // $query .= " ORDER BY DATE_FORMAT(fechaPLC, ". $formatDate .")". $order .";";
        
        // $nivelDiesel = $this->select($query);

        // $query = "SELECT DISTINCT";
        // $query .= " DATE_FORMAT(fechaPLC, '%Y-%m-%d %H') AS Date_flag, operacion";
        // $query .= " FROM registrosmaquinarias WHERE DATE_FORMAT(fechaPLC, ". $formatDate2 .") = '".$newDate."' AND operacion = 1 AND idmaquina = ". $params['idMachine'];
        // $query .= " GROUP BY DATE_FORMAT(fechaPLC, '%Y-%m-%d %H');";

        // $operacionData = $this->select($query);

        // return [$nivelDiesel, $ultimoNivelTanque, $operacionData];

        $auxdate = explode(" ", $params['startDate'])[0];
        $auxdate = explode("-", $auxdate);

        $GROUPER = "";
        $formatDate = "";
        $newDate = "";
        switch($params['type']){
          case "day":
            $GROUPER = " HOUR(fechaPLC)";
            $formatDate = "%Y-%m-%d";
            $newDate = $auxdate[0] . "-" . $auxdate[1] . "-" . $auxdate[2];
            break; 

          case "month":
            $GROUPER = " DAY(fechaPLC)";
            $formatDate = "%Y-%m";
            $newDate = $auxdate[0] . "-" . $auxdate[1];
            break;
        }

        $query = "SELECT";
        $query .= " nivel_tanque AS STATUS_TANQUE,";
        $query .= $GROUPER . " AS GROUPER";
        $query .= " FROM registrosmaquinarias ";
        $query .= " WHERE id IN(";
        // $query .= " SELECT MAX( id ) FROM registrosmaquinarias";
        $query .= " SELECT MIN( id ) FROM registrosmaquinarias";
        $query .= " WHERE DATE_FORMAT( fechaPLC, '" . $formatDate . "' ) = '". $newDate ."'";
        // $query .= " AND arranque = 1";
        $query .= " AND nivel_tanque > 473";
        $query .= " GROUP BY MONTH(fechaPLC), DAY(fechaPLC), HOUR(fechaPLC)";
        $query .= " ORDER BY MONTH(fechaPLC), DAY(fechaPLC), HOUR(fechaPLC) )"; 
        $query .= " ORDER BY GROUPER;"; 

        $nivelDiesel = $this->select($query);

        $query = "SELECT";
        $query .= " nivel_tanque AS STATUS_TANQUE,";
        $query .= $GROUPER . " AS GROUPER";
        $query .= " FROM registrosmaquinarias ";
        $query .= " WHERE id IN(";
        // $query .= " SELECT MAX( id ) FROM registrosmaquinarias";
        $query .= " SELECT MIN( id ) FROM registrosmaquinarias";
        $query .= " WHERE DATE_FORMAT( fechaPLC, '" . $formatDate . "' ) = '". $newDate ."'";
        $query .= " AND arranque = 1";
        $query .= " AND nivel_tanque > 473";
        $query .= " GROUP BY MONTH(fechaPLC), DAY(fechaPLC), HOUR(fechaPLC)";
        $query .= " ORDER BY MONTH(fechaPLC), DAY(fechaPLC), HOUR(fechaPLC) )"; 
        $query .= " ORDER BY GROUPER;"; 

        $nivelDieselArranque = $this->select($query);

        $query = "SELECT HOUR(fechaPLC) AS GROUPER, operacion";
        $query .= " FROM registrosmaquinarias";
        $query .= " WHERE DATE_FORMAT(fechaPLC, '" . $formatDate . "') =  '". $newDate ."'";
        $query .= " AND nivel_tanque > 473";
        $query .= " AND arranque = 1";
        $query .= " GROUP BY HOUR(fechaPLC), operacion";
        $query .= " HAVING operacion = 1";
        $query .= " ORDER BY HOUR(fechaPLC);";

        $nivelDieselOperacion = $this->select($query);

        return[$nivelDiesel, $nivelDieselArranque, $nivelDieselOperacion];
    }

    public function getAcumuladoKilometers()
    {
        return $this->select("SELECT * FROM registrosmaquinarias");
    }
    
    public function getAVGDiesel($params)
    {
      $auxdate = explode(" ", $params['startDate'])[0];
      $auxdate = explode("-", $auxdate);

      $query = "SELECT";
      $query .= " N_MAX AS nivel_max,";
      $query .= " N_MIN AS nivel_min,";
      $query .= " (N_MAX - N_MIN) AS tot_nivel,";
      $query .= " DAY_MAX AS DAY,";
      $query .= " HOUR_MAX AS HOUR";
      // $query .= " , (SELECT GROUP_CONCAT(latitud) FROM registrosmaquinarias WHERE id BETWEEN ID_MAX AND ID_MIN ) AS latitud";
      // $query .= " , (SELECT GROUP_CONCAT(longitud) FROM registrosmaquinarias WHERE id BETWEEN ID_MAX AND ID_MIN ) AS longitud";
      $query .= " FROM (";
      $query .= " (SELECT id AS ID_MAX, nivel_tanque AS N_MAX, DAY(fechaPLC) AS DAY_MAX, HOUR(fechaPLC) AS HOUR_MAX FROM registrosmaquinarias WHERE id IN(";
      $query .= " SELECT MIN( id ) AS MAX_ID FROM registrosmaquinarias";
      $query .= " WHERE arranque = 1 AND DATE_FORMAT( fechaPLC, '%Y-%m' ) = '". $auxdate[0]."-". $auxdate[1] ."' AND nivel_tanque > 473";
      $query .= " AND idMaquina = " . $params['idMachine'];
      $query .= " GROUP BY DAY(fechaPLC), HOUR(fechaPLC)";
      $query .= " ORDER BY DAY(fechaPLC), HOUR(fechaPLC) )) AS MAX_VAL,";
      $query .= " (SELECT id AS ID_MIN, nivel_tanque AS N_MIN, DAY(fechaPLC) AS DAY_MIN, HOUR(fechaPLC) AS HOUR_MIN FROM registrosmaquinarias WHERE id IN (";
      $query .= " SELECT MAX( id ) AS MIN_ID FROM registrosmaquinarias";
      $query .= " WHERE arranque = 1 AND DATE_FORMAT( fechaPLC, '%Y-%m' ) = '". $auxdate[0]."-". $auxdate[1] ."' AND nivel_tanque > 473";
      $query .= " AND idMaquina = " . $params['idMachine'];
      $query .= " GROUP BY DAY(fechaPLC), HOUR(fechaPLC)";
      $query .= " ORDER BY DAY(fechaPLC), HOUR(fechaPLC) )) AS MIN_VAL ) ";
      $query .= " WHERE DAY_MAX = DAY_MIN AND HOUR_MAX = HOUR_MIN";
      $query .= " HAVING ( N_MAX - N_MIN ) >= 0";
      $query .= " ORDER BY DAY_MAX, HOUR_MAX";

      return $this->select($query);
    }

    public function getAVGKilometers()
    {
        return $this->select("SELECT * FROM registrosmaquinarias");
    }
    
    public function getRegistroNotificacion()
    {
        return $this->select("SELECT id, codigoError AS codigo, mensajeError AS mensaje, tipoError AS tipo, idMaquina AS maquina, fechaRegistro AS fecha, fechaMaquina AS fecha_plc FROM alarmsLogs ORDER BY id DESC");
    }
    
    public function setRegistroNotificacion($params)
    {
      $data = json_decode( $params, true );

      $dataMessage = $data['dataError'];
      $dataFromDB = $data['dataError']['dataDB'];

      $query = "INSERT INTO alarmsLogs(codigoError, mensajeError, tipoError, idMaquina, fechaRegistro, fechaMaquina)";
      $query .= "VALUES( '".$dataMessage['codigoError']."', '".$dataMessage['message']."', '".$dataMessage['typeError']."', ". $dataFromDB['idMaquina'] .", NOW(), '". $dataFromDB['fecha'] ."' )";

      return $this->insert($query);
    }

    public function getDecensoDiesel($params)
    {
        return $this->select("SELECT AVG( nivel_tanque ) AS TOT_NIVEL, idMaquina, fechaPLC FROM registrosmaquinarias WHERE DATE_FORMAT(fechaPLC, '%Y-%m-%d %H') = '" . $params['date'] . "' GROUP BY fechaPLC, idMaquina; ");
    }

    public function getDataTable($params){

      $auxdate = explode(" ", $params['date'])[0];
      $auxdate = explode("-", $auxdate);

      $query = "SELECT";
      $query .= " N_MAX AS nivel_max,";
      $query .= " N_MIN AS nivel_min,";
      $query .= " (N_MAX - N_MIN) AS tot_nivel,";
      $query .= " DAY_MAX AS DAY,";
      $query .= " HOUR_MAX AS HOUR,";
      $query .= " operacion";
      $query .= " , (SELECT GROUP_CONCAT(latitud) FROM registrosmaquinarias WHERE id BETWEEN ID_MAX AND ID_MIN ) AS latitud";
      $query .= " , (SELECT GROUP_CONCAT(longitud) FROM registrosmaquinarias WHERE id BETWEEN ID_MAX AND ID_MIN ) AS longitud";
      $query .= " FROM (";
      $query .= " (SELECT id AS ID_MAX, nivel_tanque AS N_MAX, DAY(fechaPLC) AS DAY_MAX, HOUR(fechaPLC) AS HOUR_MAX, operacion FROM registrosmaquinarias WHERE id IN(";
      $query .= " SELECT MIN( id ) AS MAX_ID FROM registrosmaquinarias";
      $query .= " WHERE arranque = 1 AND DATE_FORMAT( fechaPLC, '%Y-%m' ) = '". $auxdate[0]."-". $auxdate[1] ."' AND nivel_tanque > 473";
      $query .= " GROUP BY DAY(fechaPLC), HOUR(fechaPLC)";
      $query .= " ORDER BY DAY(fechaPLC), HOUR(fechaPLC) )) AS MAX_VAL,";
      $query .= " (SELECT id AS ID_MIN, nivel_tanque AS N_MIN, DAY(fechaPLC) AS DAY_MIN, HOUR(fechaPLC) AS HOUR_MIN FROM registrosmaquinarias WHERE id IN (";
      $query .= " SELECT MAX( id ) AS MIN_ID FROM registrosmaquinarias";
      $query .= " WHERE arranque = 1 AND DATE_FORMAT( fechaPLC, '%Y-%m' ) = '". $auxdate[0]."-". $auxdate[1] ."' AND nivel_tanque > 473";
      $query .= " AND idMaquina = " . $params['idMachine'];
      $query .= " GROUP BY DAY(fechaPLC), HOUR(fechaPLC)";
      $query .= " ORDER BY DAY(fechaPLC), HOUR(fechaPLC) )) AS MIN_VAL ) ";
      $query .= " WHERE DAY_MAX = DAY_MIN AND HOUR_MAX = HOUR_MIN";
      $query .= " HAVING ( N_MAX - N_MIN ) >= 0";
      $query .= " ORDER BY operacion, DAY_MAX, HOUR_MAX";

      return $this->select( $query );
    }
}