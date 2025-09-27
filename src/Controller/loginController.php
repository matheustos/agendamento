<?php

namespace Controller;

use Jwt\Token;
use Model\Usuarios;
use Validators\RetornosValidators;

class LoginController{
    public static function login($email, $senha){
        $user = Usuarios::buscarPorEmail($email);

        if($user){
            $acesso = $user["acesso"];
            $senha_user = $user["senha"];
            if(password_verify($senha, $senha_user)){
                $token = Token::geraToken($user["id"], $acesso);
                return ["status" => true, "message" => "login efetuado com sucesso!", "token" => $token];
            }else{
                return RetornosValidators::erro("Email e/ou senha incorretos!");
            }
        }else{
            return RetornosValidators::erro("Email e/ou senha incorretos!");
        }
    }
}



?>