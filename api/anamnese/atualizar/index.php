<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Controller\AnamneseController;
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
$user = $verifica_token["user_id"];
if($verifica_token["status"] === true){
    $fichas = AnamneseController::atualizarAnamnese($_POST);

    echo json_encode($fichas);
}else{
    echo json_encode($verifica_token);
}

?>