<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\DespesasController;
use Jwt\Token;

$headers = getallheaders(); // pega todos os headers da requisição
$authHeader = $headers['Authorization'] ?? ''; // pega o header Authorization

$verifica_token = Token::verificaToken($authHeader);
$user = $verifica_token["user_id"];
if($verifica_token["status"] === true){
    $nome = $_POST["nome"];
    $quantidade = $_POST["quantidade"];
    $preco = $_POST["preco"];

    $res = DespesasController::cadastar($nome, $quantidade, $preco);

    echo json_encode($res);
}else{
    echo json_encode($verifica_token);
}
?>