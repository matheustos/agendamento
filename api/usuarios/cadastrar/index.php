<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use Controller\UsuariosController;

$res = UsuariosController::cadastro($_POST);

echo json_encode($res);


?>