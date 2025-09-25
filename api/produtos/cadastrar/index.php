<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\ProdutosController;

$res = ProdutosController::cadastrarProdutos($_POST);

echo json_encode($res);

?>