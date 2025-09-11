<?php

namespace Jwt;
require_once __DIR__ . '/../vendor/autoload.php';


use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

class Token{
    public static function geraToken($user_id){
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $chave_secreta = $_ENV['JWT_SECRET'];

        $payload = [
            'iss' => 'localhost',          // emissor
            'aud' => 'localhost',          // destinatário
            'iat' => time(),               // criado em timestamp
            'exp' => time() + 3600,        // expira em 1 hora
            'user_id' => $user_id                  // id do usuário
        ];

        $jwt = JWT::encode($payload, $chave_secreta, 'HS256');
        return $jwt;
    }
}

echo Token::geraToken(1);
?>