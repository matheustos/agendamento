<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
ob_start(); // inicia buffer, previne que output acidental quebre o JSON
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header('Content-Type: application/json');

use Controller\AgendamentoController;
use Jwt\Token;

if ($_SERVER["REQUEST_METHOD"] === "GET") {

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
        // Buscar todos os bloqueios
        $res = AgendamentoController::buscarBloqueios();

        echo json_encode($res);
        exit;
    }
}

?>