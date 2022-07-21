<?php
require_once PROJECT_ROOT_PATH . "/Model/DataBase.php";
 
class AuthModel extends DataBase
{
    public function authUser($params)
    {
        $query = "SELECT * /*IF(COUNT(id) > 0, true, false) isAuthorized*/ FROM usuarios WHERE nickname = '" . $params['user'] . "' AND password = '" . $params['pass'] . "'";
        return $this->select($query);
    }
}