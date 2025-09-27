<?php

require_once __DIR__ . '/../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\AgendamentoController;
use Jwt\Token;

if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$key] = $value;
            }
        }
        return $headers;
    }
}

$headers = getallheaders(); // pega todos os headers da requisição
$authHeader = $headers['Authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? ''); // pega o header Authorization

$verifica_token = Token::verificaToken($authHeader);

if($verifica_token["status"] === true){
    if($verifica_token['acesso'] === "admin"){
        // Buscar todos os agendamentos do mês
        $res = AgendamentoController::buscarTodosAgendamentos();

        echo json_encode($res);
    }

    if($verifica_token['acesso'] === "cliente"){
        $user = $verifica_token['user_id'];
        if($user){
            $res = AgendamentoController::buscarPorMesEUser($user);

            echo json_encode($res);
        }else{
            echo json_encode(["status" => false, "message" => "nenhum agendamento encontrado!"]);
        }
        
    }
    
}else{
    echo json_encode($verifica_token);
}

?>