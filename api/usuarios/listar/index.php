<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json');

use Controller\UsuariosController;
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
    $res = UsuariosController::listarUsuarios();

    echo json_encode($res);
}else{
    echo json_encode($verifica_token);
}

?>