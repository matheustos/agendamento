<?php

require_once __DIR__ . '/../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\FinanceiroController;
use Jwt\Token;

$headers = getallheaders(); // pega todos os headers da requisição
$authHeader = $headers['Authorization'] ?? ''; // pega o header Authorization

$verifica_token = Token::verificaToken($authHeader);

if($verifica_token["status"] === true){
    $dia = FinanceiroController::calcularPrecosDia();
    $semana = FinanceiroController::calcularPrecosSemana();
    $mes = FinanceiroController::calcularPrecosMes();
    $ano = FinanceiroController::calcularPrecosAno();

    $totais = array_merge($dia, $semana, $mes, $ano);

    echo json_encode($totais);
}else{
    echo json_encode($verifica_token);
}


?>