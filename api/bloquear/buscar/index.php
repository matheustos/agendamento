<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
ob_start(); // inicia buffer, previne que output acidental quebre o JSON
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
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

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $headers = getallheaders(); // pega todos os headers da requisição
    $authHeader = $headers['Authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? ''); // pega o header Authorization

    $verifica_token = Token::verificaToken($authHeader);

    if($verifica_token["status"] === true){
        // Buscar todos os bloqueios*/
        $res = AgendamentoController::buscarBloqueios();

        echo json_encode($res);
    }else{
        echo json_encode($verifica_token);
    }
}
?>