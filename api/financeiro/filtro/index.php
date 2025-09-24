<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\FinanceiroController;
use Jwt\Token;

$headers = getallheaders(); // pega todos os headers da requisição
$authHeader = $headers['Authorization'] ?? ''; // pega o header Authorization

$verifica_token = Token::verificaToken($authHeader);

$tipo = $_POST["tipo"];
$valor = $_POST["valor"];

if($verifica_token["status"] === true){

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
}else{
    echo json_encode($verifica_token);
}

?>