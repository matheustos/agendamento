<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Controller\AgendamentoController;

header('Content-Type: application/json');

$res = AgendamentoController::buscarHoje();

echo json_encode($res);

?>