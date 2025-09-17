<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\UsuariosController;
use Jwt\Token;

$headers = getallheaders(); // pega todos os headers da requisição
$authHeader = $headers['Authorization'] ?? ''; // pega o header Authorization

$verifica_token = Token::verificaToken($authHeader);
if($verifica_token["status"] === true){
    
    $email = $_POST["email"];
    $nova_senha = $_POST["nova_senha"];
    $confirmar_senha = $_POST["confirmar_senha"];

    $res = UsuariosController::atualizar_senha($email, $nova_senha, $confirmar_senha);
    echo json_encode($res);
}else{
    echo json_encode($verifica_token);
}

?>