<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Controller\FinanceiroController;

$res = FinanceiroController::calcularPrecosAno();

echo json_encode($res);
?>