<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Controller\AgendamentoController;

$res = AgendamentoController::atualizarAgendamento($_POST);

echo json_encode($res);

?>