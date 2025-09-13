<?php
namespace Jwt;

require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

class Token{
    public static function geraToken($user_id, $acesso){
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $chave_secreta = $_ENV['JWT_SECRET'];

        $payload = [
            'iss' => 'localhost',          // emissor
            'aud' => 'localhost',          // destinatário
            'acesso' => $acesso,           // verifica permissão 
            'iat' => time(),               // criado em timestamp
            'exp' => time() + 3600,        // expira em 1 hora
            'user_id' => $user_id          // id do usuário
        ];

        $jwt = JWT::encode($payload, $chave_secreta, 'HS256');
        return $jwt;
    }

    public static function validaToken($token){

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $chave_secreta = $_ENV['JWT_SECRET'];

        try {
            $decoded = JWT::decode($token, new Key($chave_secreta, 'HS256'));
            //echo "Token válido!";
            return $decoded;
        } catch (\Firebase\JWT\ExpiredException $e) {
            //echo "Token expirado!";
            return ["status" => false, "message" => "Token expirado!"];
        } catch (\Exception $e) {
            //echo "Token inválido!";
            return ["status" => false, "message" => "Token inválido!"];
        }
    }

    public static function verificaToken($authHeader){
        if (!$authHeader) {
            http_response_code(401);
            return ["status" => false, "message" => "Token não enviado"];
        }

        // Remove a palavra "Bearer " do início
        $token = str_replace('Bearer ', '', $authHeader);

        $decoded = Token::validaToken($token);
        if (!isset($decoded->user_id)) {
            http_response_code(401);
            return ["status" => false, "message" => "Usuário não autorizado!"]; // Token inválido ou expirado
        }else{
            $user_id = $decoded->user_id; // token válido, pode usar o user_id*/
            return ["status" => true, "user_id" => $user_id, "acesso" => $decoded->acesso];
        }
    }
}



?>