<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Controller\AgendamentoController;
use Firebase\JWT\JWT;
use Jwt\Token;

$headers = getallheaders(); // pega todos os headers da requisição
$authHeader = $headers['Authorization'] ?? ''; // pega o header Authorization

/*if (!$authHeader) {
    http_response_code(401);
    echo json_encode(["status" => false, "message" => "Token não enviado"]);
    exit;
}

// Remove a palavra "Bearer " do início
$token = str_replace('Bearer ', '', $authHeader);

$decoded = Token::validaToken($token);
if (!isset($decoded->user_id)) {
    http_response_code(401);
    echo json_encode($decoded); // Token inválido ou expirado
    exit;
}else{
    $user_id = $decoded->user_id; // token válido, pode usar o user_id*/
$verifica_token = Token::verificaToken($authHeader);
if($verifica_token["status"] === true){
    $res = AgendamentoController::atualizarAgendamento($_POST);
    echo json_encode($res);
}else{
    echo json_encode($verifica_token);
}
//}

?>