<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Model\Produtos;

$res = Produtos::getTotalPorAno("2025");

echo json_encode($res);
?>