<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\DespesasController;

$nome = $_POST["nome"];
$quantidade = $_POST["quantidade"];
$preco = $_POST["preco"];

$res = DespesasController::cadastar($nome, $quantidade, $preco);

echo json_encode($res);

?>