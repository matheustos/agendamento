<?php
require_once __DIR__ . '/../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\AgendamentoController;

$data = $_POST["data"];
$hora = $_POST["horario"];
$nome = $_POST["nome"];
$servico = $_POST["servico"];
$res = AgendamentoController::agendamento($data, $hora, $nome, $servico);

echo json_encode($res);

?>
