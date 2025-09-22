<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Controller\AnamneseController;

use Jwt\Token;

$headers = getallheaders(); // pega todos os headers da requisição
$authHeader = $headers['Authorization'] ?? ''; // pega o header Authorization

$verifica_token = Token::verificaToken($authHeader);
$user = $verifica_token["user_id"];
if($verifica_token["status"] === true){
    $res = AnamneseController::cadastrar($_POST);

    echo json_encode($res);
}else{
    echo json_encode($verifica_token);
}

?>