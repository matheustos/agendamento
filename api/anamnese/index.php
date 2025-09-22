<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Controller\AnamneseController;

$res = AnamneseController::cadastrar($_POST);

echo json_encode($res);

?>