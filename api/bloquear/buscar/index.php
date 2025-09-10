<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
ob_start(); // inicia buffer, previne que output acidental quebre o JSON
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header('Content-Type: application/json');

use Controller\AgendamentoController;

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Buscar todos os bloqueios
    $res = AgendamentoController::buscarBloqueios();

    echo json_encode($res);
    exit;
}

?>