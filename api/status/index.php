<?php
require_once __DIR__ . '/../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\AgendamentoController;

$res = AgendamentoController::buscarPorStatus();

echo json_encode($res);