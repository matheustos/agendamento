<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Controller\AgendamentoController;

header('Content-Type: application/json');

if (!isset($_POST["data"]) || empty($_POST["data"])) {
    echo json_encode(["status" => false, "message" => "Data não informada."]);
    exit;
}

$dataBr = $_POST["data"];
$dateObj = DateTime::createFromFormat('d/m/Y', $dataBr);

if (!$dateObj) {
    echo json_encode(["status" => false, "message" => "Formato de data inválido."]);
    exit;
}

$dataIso = $dateObj->format('Y-m-d');
$res = AgendamentoController::buscarPorDia($dataIso);

echo json_encode($res);

?>