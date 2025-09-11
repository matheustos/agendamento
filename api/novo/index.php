<?php
require_once __DIR__ . '/../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\AgendamentoController;
use Jwt\Token;

$data = $_POST["data"];
$hora = $_POST["horario"];
$nome = $_POST["nome"];
$servico = $_POST["servico"];
$obs = $_POST["obs"];
$telefone = $_POST["telefone"];

$headers = getallheaders(); // pega todos os headers da requisição
$authHeader = $headers['Authorization'] ?? ''; // pega o header Authorization

if (!$authHeader) {
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
    $user_id = $decoded->user_id; // token válido, pode usar o user_id

    $res = AgendamentoController::agendamento($data, $hora, $nome, $servico, $obs, $telefone);

    echo json_encode($res);
}

?>
