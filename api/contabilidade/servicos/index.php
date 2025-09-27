<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\AgendamentoController;

$mes = $_POST["mes"];
$ano = $_POST["ano"];
if($mes === ""){
    $res = AgendamentoController::buscarConcluidosAno($ano);

    echo json_encode($res);
}else{
    $res = AgendamentoController::buscarConcluidos($mes, $ano);

    echo json_encode($res);
}

?>