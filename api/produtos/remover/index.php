<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\ProdutosController;

$id = $_POST["id"];

$res = ProdutosController::removeProduto($id);

echo json_encode($res);

?>