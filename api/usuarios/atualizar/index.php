<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use Controller\UsuariosController;

$res = UsuariosController::atualizarUser($_POST);

echo json_encode($res);


?>