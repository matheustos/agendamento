<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\UsuariosController;

$id = $_POST["id"];

$res = UsuariosController::removerUser($id);

echo json_encode($res);
?>