<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\ProdutosController;

$ano = $_POST["ano"];

$res = ProdutosController::buscarVendasAno($ano);
echo json_encode($res);

?>