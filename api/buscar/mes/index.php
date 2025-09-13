<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Controller\AgendamentoController;
use Controller\UsuariosController;
use Jwt\Token;

header('Content-Type: application/json');

$headers = getallheaders(); // pega todos os headers da requisição
$authHeader = $headers['Authorization'] ?? ''; // pega o header Authorization

$verifica_token = Token::verificaToken($authHeader);

if($verifica_token["status"] === true){
    if($verifica_token['acesso'] === "admin"){
        // Buscar todos os agendamentos do mês
        $res = AgendamentoController::buscarPorMes();

        echo json_encode($res);
    }

    if($verifica_token['acesso'] === "cliente"){
        $nome = UsuariosController::buscarNome($verifica_token['user_id']);
        if($nome){
            $res = AgendamentoController::buscarPorMesENome($nome);

            echo json_encode($res);
        }else{
            echo json_encode(["status" => false, "message" => "nenhum agendamento encontrado!"]);
        }
        
    }
    
}else{
    echo json_encode($verifica_token);
}

?>