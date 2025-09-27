<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
header('Content-Type: application/json');

use Model\Despesas;
use Jwt\Token;

$headers = getallheaders(); // pega todos os headers da requisição
$authHeader = $headers['Authorization'] ?? ''; // pega o header Authorization

$verifica_token = Token::verificaToken($authHeader);
$user = $verifica_token["user_id"];
if($verifica_token["status"] === true){
    $mes = $_POST["mes"];
    $ano = $_POST["ano"];
    $res = Despesas::getDespesas($mes, $ano);

    echo json_encode($res);
}else{
    echo json_encode($verifica_token);
}
?>