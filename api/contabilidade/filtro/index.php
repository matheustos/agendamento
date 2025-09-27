<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\FinanceiroController;
use Jwt\Token;

$headers = getallheaders(); // pega todos os headers da requisição
$authHeader = $headers['Authorization'] ?? ''; // pega o header Authorization

$verifica_token = Token::verificaToken($authHeader);
$user = $verifica_token["user_id"];
if($verifica_token["status"] === true){
    // Pega os valores enviados via POST
    $mes = isset($_POST["mes"]) ? $_POST["mes"] : "";
    $ano = isset($_POST["ano"]) ? $_POST["ano"] : null;

    // Se não houver ano, retorna zero
    if (!$ano) {
        echo json_encode(["0" => 0, "DespesasMes" => 0]);
        exit;
    }

    // Se mês for vazio, retorna todos os dados do ano
    if ($mes === "") {
        $res = FinanceiroController::calcularAnoEspecifico($ano);
    } else {
        // Se mês for específico, retorna dados daquele mês
        $res = FinanceiroController::calcularMesEspecifico($mes, $ano);
    }

    echo json_encode($res);
}else{
    echo json_encode($verifica_token);
}
