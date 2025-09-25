<?php
require_once __DIR__ . '/../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\ProdutosController;

$res = ProdutosController::buscarProdutos();

echo json_encode($res);
?>