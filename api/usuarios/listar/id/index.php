<?php
require_once __DIR__ . '/../../../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\UsuariosController;
use Jwt\Token;

$headers = getallheaders(); // pega todos os headers da requisição
$authHeader = $headers['Authorization'] ?? ''; // pega o header Authorization

$verifica_token = Token::verificaToken($authHeader);

if($verifica_token["status"] === true){
    $id = $verifica_token['user_id'];
    // Buscar todos os bloqueios
    $res = UsuariosController::listarById($id);

    echo json_encode($res);
}else{
    echo json_encode($verifica_token);
}



?>