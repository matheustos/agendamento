<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Model\Agendamento;
use Jwt\Token;

$headers = getallheaders(); // pega todos os headers da requisição
$authHeader = $headers['Authorization'] ?? ''; // pega o header Authorization

$verifica_token = Token::verificaToken($authHeader);
if($verifica_token["status"] === true){
    $res = Agendamento::getAgendaDisponivel();
    echo json_encode($res);
}else{
    echo json_encode($verifica_token);
}



?>