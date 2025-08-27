<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Controller\AgendamentoController;

$status = $_POST['status'] ?? null;
$data = $_POST['data'] ?? null;
$hora = $_POST['hora'];
$nome = $_POST['nome'] ?? null;

$res = AgendamentoController::alterarStatus($status, $data, $hora, $nome);

echo json_encode($res);

?>