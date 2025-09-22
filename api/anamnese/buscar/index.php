<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Controller\AnamneseController;

$fichas = AnamneseController::getAnamnese();

echo json_encode($fichas);


?>