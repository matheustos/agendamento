<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Model\Agendamento;

$res = Agendamento::getEmail($_POST["email"]);

echo json_encode($res);



?>