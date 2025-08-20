<?php
namespace Controller;
use Model\usuarios;
use Validators\usuariosValidators;

class UsuariosController{
    public static function cadastro($dados){
        //cadastrar usuario
        $nome = $dados["nome"];
        $email = $dados["email"];
        $senha = $dados["senha"];
        $telefone = $dados["telefone"];
        $user_id = 0;
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $validacao = usuariosValidators::validacaoCadastro($nome, $email, $hash, $telefone, $user_id);

        if($validacao["status"] === true){

            $emailExistente = usuariosValidators::verificarEmailExistente($email);
            if(!$emailExistente["status"]){
                return $emailExistente; // Retorna erro se o email já estiver cadastrado
            }
            
            $res = usuarios::cadastrar($nome, $email, $hash, $telefone, $user_id);

            if(!$res){
                return ["status" => false, "message" => "Erro ao cadastrar usuário."];
            }else{
                return ["status" => true, "message" => "Usuário cadastrado com sucesso!", "data" => $res];
            }
        }else{
            return $validacao;
        }
    }
}