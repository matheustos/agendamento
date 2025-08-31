<?php
namespace Controller;
use Model\usuarios;
use Validators\AgendamentoValidators;
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

    public static function atualizarUser($dados){
        $nome = $dados["nome"];
        $telefone = $dados["telefone"];
        $nomeUpdate = $dados["nomeUpdate"];

        $validacao = UsuariosValidators::validacaoAtualizar($nome, $telefone);

        if($validacao["status"] === true){
            $res = Usuarios::atualizar($nome, $telefone, $nomeUpdate);
            if(!$res){
                return ["status" => false, "message" => "Erro ao atualizar dados."];
            }else{
                return ["status" => true, "message" => "Dados atualizados com sucesso!", "data" => $res];
            }
        }else{
            return $validacao;
        }

    }

    public static function updateSenha($dados) {
        $email = $dados["email"];
        $senha = $dados["senha"];
        // Verificar se o email existe
        $emailExistente = usuariosValidators::buscarEmail($email);
        if ($emailExistente["status"] === false) {
            // Se o email não existir, retornar erro
            return ["status" => false, "message" => "Usuário não encontrado."];
        }
        // Atualizar senha do usuário
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $res = usuarios::updateSenha($hash, $email);
        // Verificar se a atualização foi bem-sucedida
        if(!$res){
            return ["status" => false, "message" => "Erro ao atualizar senha."];
        }else{
            return ["status" => true, "message" => "Senha atualizada com sucesso!"];
        }
    }

    public static function listarUsuarios(){
        $res = Usuarios::listar();

        if($res){
            return AgendamentoValidators::formatarRetorno("Usuários encontrados: ", $res);
        }else{
            return AgendamentoValidators::formatarErro("Nenhum usuário encontrado!");
        }

    }
}