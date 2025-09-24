<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\FinanceiroController;

$tipo = $_POST["tipo"];
$valor = $_POST["valor"];


if ($tipo === "dia") {
    $res = FinanceiroController::calcularPrecosData($valor);
} elseif ($tipo === "mes") {
    $res = FinanceiroController::calcularMesEspecifico($valor);
} elseif ($tipo === "ano") {
    $res = FinanceiroController::calcularAnoEspecifico($valor);
} else {
    $res = ["erro" => "Tipo de filtro inválido"];
}

echo json_encode($res);

?>