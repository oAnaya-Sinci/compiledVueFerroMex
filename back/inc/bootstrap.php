<?php
define("PROJECT_ROOT_PATH", __DIR__ . "/../");

// include main configuration file
require_once PROJECT_ROOT_PATH . "/inc/config.php";

// include the base controller file
require_once PROJECT_ROOT_PATH . "/Controller/api/BaseController.php";

// include the use model file
// require_once PROJECT_ROOT_PATH . "/Model/UserModel.php";

require_once PROJECT_ROOT_PATH . "/Model/MaquinasModel.php";

require_once PROJECT_ROOT_PATH . "/Model/AuthModel.php";

require_once PROJECT_ROOT_PATH . "/Model/tokenPassword.php";
