<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Controller\UsuariosController;

$res = UsuariosController::updateSenha($_POST["email"]);

echo json_encode($res);

?>