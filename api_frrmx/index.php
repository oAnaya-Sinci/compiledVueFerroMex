<?php
require __DIR__ . "/inc/bootstrap.php";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

// if ((isset($uri[3]) && $uri[3] != 'maquinas') || !isset($uri[4])) {
if (!isset($uri[4])) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

// die( var_dump( $uri ) );

$objFeedController;

if($uri[3] == 'maquinas'){
    
    // $routeClassController = "/Controller/api/MaquinasController.php";

    require PROJECT_ROOT_PATH . "/Controller/api/MaquinasController.php";
    $objFeedController = new MaquinasController();

} else if($uri[3] == 'authuser'){
    
    // $routeClassController = "/Controller/api/AuthController.php";
    
    require PROJECT_ROOT_PATH . "/Controller/api/AuthController.php";
    $objFeedController = new AuthController();
}

// require PROJECT_ROOT_PATH . $routeClassController;
// $objFeedController = new MaquinasController();

$strMethodName = $uri[4];
$objFeedController->{$strMethodName}();