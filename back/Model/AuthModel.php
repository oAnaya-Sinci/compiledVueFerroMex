<?php
require_once PROJECT_ROOT_PATH . "/Model/DataBase.php";

class AuthModel extends DataBase
{
    public function authUser($params)
    {
        $query = "SELECT IF(COUNT(id) > 0, true, false) isAuthorized FROM usuarios WHERE nickname = '" . $params['user'] . "' AND password = '" . $params['password'] . "'";
        $authorized = $this->select($query)[0]['isAuthorized'];

        $tokenCreated = new tokenPassword();
        $token = $tokenCreated->encryptToken( date("Y-m-j H:i:s") );
        $objectResponse = [ "isAuthorized" => $authorized, "_token" => $token ];

        return json_encode($objectResponse);
    }

    // public function isLogenIn(){}
}