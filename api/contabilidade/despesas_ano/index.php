<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
header('Content-Type: application/json');

use Model\Despesas;

$ano = $_POST["ano"];

$res = Despesas::getDespesasAno($ano);
echo json_encode($res);

?>