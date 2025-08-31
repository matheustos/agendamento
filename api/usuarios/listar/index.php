<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use Controller\UsuariosController;

$res = UsuariosController::listarUsuarios();

echo json_encode($res);


?>