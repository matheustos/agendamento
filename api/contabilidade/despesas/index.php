<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
header('Content-Type: application/json');

use Model\Despesas;

$mes = $_POST["mes"];
$ano = $_POST["ano"];
$res = Despesas::getDespesas($mes, $ano);

echo json_encode($res);

?>