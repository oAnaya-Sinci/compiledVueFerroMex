<?php

class AuthController extends BaseController
{
    /**
     * "/maquinas/obtain data GPS" Endpoint - Get data of GPS
     */
    public function authLoginUser()
    {
        // $id = uniqid();
        // $id = bin2hex(openssl_random_pseudo_bytes(16));

        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
 
        if (strtoupper($requestMethod) == 'GET') {
            try {
                $authUser = new AuthModel();
 
                $isAuth = $authUser->authUser($_GET);
                $responseData = json_encode($isAuth);
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().' Something went wrong! Please contact support.';
                $strErrorHeader = ' HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
 
        // send output
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }
}