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
$emailForm = $_POST["email"];

$headers = getallheaders(); // pega todos os headers da requisição
$authHeader = $headers['Authorization'] ?? ''; // pega o header Authorization

$verifica_token = Token::verificaToken($authHeader);

if($verifica_token["status"] === true){
    // Buscar todos os bloqueios
    $res = AgendamentoController::agendamento($data, $hora, $nome, $servico, $obs, $telefone, $emailForm);

    echo json_encode($res);
}else{
    echo json_encode($verifica_token);
}

?>
