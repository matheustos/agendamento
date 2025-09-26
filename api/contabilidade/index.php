<?php
require_once __DIR__ . '/../../vendor/autoload.php';
header('Content-Type: application/json');

use Model\Produtos;

$mes = $_POST["mes"];
$ano = $_POST["ano"];
$res = Produtos::getVendasPorMes($mes, $ano);

echo json_encode($res);

?>