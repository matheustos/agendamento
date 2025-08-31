<?php

namespace Validators;
use Controller\usuariosController;
use Model\usuarios;
class UsuariosValidators{

    public static function validacaoCadastro($nome, $email, $senha, $telefone){
        if(empty($nome) || empty($email) || empty($senha) || empty($telefone)){
            return ["status" => false, "message" => "Preencha todos os campos!"];
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return ["status" => false, "message" => "Email inválido!"];
        }

        if(strlen($senha) < 6){
            return ["status" => false, "message" => "A senha deve ter no mínimo 6 caracteres!"];
        }

        return ["status" => true, "message" => "Validação bem-sucedida!"];
    }

    public static function validacaoAtualizar($nome, $telefone){
        if(empty($nome) || empty($telefone)){
            return ["status" => false, "message" => "Preencha todos os campos!"];
        }

        return ["status" => true, "message" => "Validação bem-sucedida!"];
    }

    public static function verificarEmailExistente($email) {
        $usuario = usuarios::buscarPorEmail($email);
        if ($usuario) {
            return ["status" => false, "message" => "Email já cadastrado!"];
        }
        else {
            return ["status" => true];
        }
    }

    public static function buscarEmail($email) {
        $usuario = usuarios::buscarPorEmail($email);
        if ($usuario) {
            return ["status" => true];
        } else {
            return ["status" => false];
        }
    }
}
?>