<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Controller\AgendamentoController;
use Validators\AgendamentoValidators;

header('Content-Type: application/json');

$mes = $_POST["mes"];

$res = AgendamentoController::buscarPorMes($mes);

echo json_encode($res);

?>