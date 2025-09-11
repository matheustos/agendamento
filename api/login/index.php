<?php

require_once __DIR__ . '/../../vendor/autoload.php';
header('Content-Type: application/json');

use Controller\LoginController;

// Pega os dados JSON enviados pelo fetch
$data = json_decode(file_get_contents("php://input"), true);

// Usa valores ou fallback vazio
$email = $data['email'] ?? '';
$senha = $data['senha'] ?? '';

$res = LoginController::login($email, $senha);

echo json_encode($res);
?>