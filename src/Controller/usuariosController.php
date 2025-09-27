<?php
namespace Controller;
use Model\Usuarios;
use Validators\AgendamentoValidators;
use Validators\UsuariosValidators;

class UsuariosController{
    public static function cadastro($dados){
        //cadastrar usuario
        $nome = $dados["nome"];
        $email = $dados["email"];
        $senha = $dados["senha"];
        $senha_confirm = $dados["senha_confirm"];
        $telefone = $dados["telefone"];
        $acesso = "cliente";

        if($senha === $senha_confirm){
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $validacao = usuariosValidators::validacaoCadastro($nome, $email, $hash, $telefone);

            if($validacao["status"] === true){

                $emailExistente = usuariosValidators::verificarEmailExistente($email);
                if(!$emailExistente["status"]){
                    return $emailExistente; // Retorna erro se o email já estiver cadastrado
                }
                
                $res = usuarios::cadastrar($nome, $email, $hash, $telefone, $acesso);

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

    public static function atualizarUser($dados, $user){
        $nome = $dados["nome"];
        $telefone = $dados["telefone"];
        $email = $dados["email"];

        $validacao = UsuariosValidators::validacaoAtualizar($nome, $telefone);

        if($validacao["status"] === true){
            $res = Usuarios::atualizar($nome, $telefone, $email, $user);
            if(!$res){
                return ["status" => false, "message" => "Erro ao atualizar dados."];
            }else{
                return ["status" => true, "message" => "Dados atualizados com sucesso!", "data" => $res];
            }
        }else{
            return $validacao;
        }

    }

    public static function removerUser($id){
        if(empty($id)){
            return ["status" => false, "message" => "Informe o usuário!"];
        }

        $res = Usuarios::removerUsuario($id);

        if($res){
            return ["status" => true, "message" => "Usuário removio com sucesso!"];
        }else{
            return ["status" => false, "message" => "Erro ao remover usuário!"];
        }
    }

 
    public static function gerarSenha($tamanho = 8){

        $maiusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $minusculas = 'abcdefghijklmnopqrstuvwxyz';
        $numeros    = '0123456789';
        $simbolos   = '!@#$%^&*()-_=+<>?';

        // Garante ao menos 1 de cada
        $senha  = '';
        $senha .= $maiusculas[random_int(0, strlen($maiusculas) - 1)];
        $senha .= $minusculas[random_int(0, strlen($minusculas) - 1)];
        $senha .= $numeros[random_int(0, strlen($numeros) - 1)];
        $senha .= $simbolos[random_int(0, strlen($simbolos) - 1)];

        // Junta todos os caracteres
        $todos = $maiusculas . $minusculas . $numeros . $simbolos;

        // Completa até o tamanho desejado
        for ($i = strlen($senha); $i < $tamanho; $i++) {
            $senha .= $todos[random_int(0, strlen($todos) - 1)];
        }

        // Embaralha a senha para não ficar previsível
        $senha = str_shuffle($senha);

        return $senha;
    }

    public static function updateSenha($email) {
        if(empty($email)){
            return ["status" => false, "message" => "Informe o email!"];
        }
        // Verificar se o email existe
        $emailExistente = usuariosValidators::buscarEmail($email);
        if ($emailExistente["status"] === false) {
            // Se o email não existir, retornar erro
            return ["status" => false, "message" => "Usuário não encontrado."];
        }else{
            $nova_senha = UsuariosController::gerarSenha(8);
            // Atualizar senha do usuário
            $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $res = usuarios::updateSenha($hash, $email);
            // Verificar se a atualização foi bem-sucedida
            if(!$res){
                return ["status" => false, "message" => "Erro ao atualizar senha."];
            }else{
                $user = usuarios::buscarPorEmail($email);
                $nome = $user["nome"];
                if($nome){
                    EmailController::resetSenha($email, $nova_senha, $nome);
                    return ["status" => true, "message" => "Senha atualizada com sucesso!"];
                }else{
                    $nome = "";
                    EmailController::resetSenha($email, $nova_senha, $nome);
                    return ["status" => true, "message" => "Senha atualizada com sucesso!"];
                }
            }
        }
    }

    public static function atualizar_senha($email, $nova_senha, $confirmar_senha){
        if(empty($email) || empty($nova_senha)|| empty($confirmar_senha)){
            return ["status" => false, "message" => "Insira todos os dados!"];
        }else{
            if($nova_senha != $confirmar_senha){
                return ["status" => false, "message" => "As senhas não coincidem!"];
            }else{
                // Atualizar senha do usuário
                $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $res = usuarios::updateSenha($hash, $email);
                if(!$res){
                    return ["status" => false, "message" => "Erro ao atualizar senha."];
                }else{
                    $user = usuarios::buscarPorEmail($email);
                    $nome = $user["nome"];
                    if($nome){
                        EmailController::atualizarSenha($email, $nome);
                        return ["status" => true, "message" => "Senha atualizada com sucesso!"];
                    }else{
                        $nome = "";
                        EmailController::atualizarSenha($email, $nome);
                        return ["status" => true, "message" => "Senha atualizada com sucesso!"];
                    }
                }
            }
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

    public static function listarById($id){
        if(empty($id)){
            return ["status" => false, "message" => "Informe o ID!"];
        }

        $res = Usuarios::buscarUserById($id);

        if($res){
            return $res;
        }else{
            return AgendamentoValidators::formatarErro("Nenhum usuário encontrado!");
        }
    }

    public static function buscarNome($id){
        $res = Usuarios::buscarUserById($id);

        if($res){
            $nome = $res[0]["nome"];
            return $nome;
        }
    }
}