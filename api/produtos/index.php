<?php
require_once __DIR__ . '/../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\ProdutosController;
use Jwt\Token;

$headers = getallheaders(); // pega todos os headers da requisição
$authHeader = $headers['Authorization'] ?? ''; // pega o header Authorization

$verifica_token = Token::verificaToken($authHeader);
if($verifica_token["status"] === true){
    $res = ProdutosController::buscarProdutos();
    echo json_encode($res);
}else{
    echo json_encode($verifica_token);
}
?>