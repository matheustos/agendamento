<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\FinanceiroController;
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

$tipo = $_POST["tipo"];


if($verifica_token["status"] === true){

    if ($tipo === "dia") {
        $valor = $_POST["valor"];
        $res = FinanceiroController::calcularPrecosData($valor);
    } elseif ($tipo === "mes") {
        $mes = $_POST["mes"];
        $ano = $_POST["ano"];
        $res = FinanceiroController::calcularMesEspecifico($mes, $ano);
    } elseif ($tipo === "ano") {
        $valor = $_POST["valor"];
        $res = FinanceiroController::calcularAnoEspecifico($valor);
    } else {
        $res = ["erro" => "Tipo de filtro inválido"];
    }

    echo json_encode($res);
}else{
    echo json_encode($verifica_token);
}

?>