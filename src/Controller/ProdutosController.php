<?php

namespace Controller;
use Model\Produtos;

class ProdutosController{
    public static function buscarProdutos(){
        $produtos = Produtos::buscar();

        if($produtos){
            return ["status" => true, "message" => "Produtos encontrados", "data" => $produtos];
        }else{
            return ["status" => false, "message" => "Nenhum produto encontrado!"];
        }
    }

    public static function atualizarProdutos($dados){
        $nome = $dados["nome"];
        $quantidade = $dados["quantidade"];
        $preco = $dados["preco"];
        $id = $dados["id"];

        if(empty($nome) || empty($quantidade) || empty($preco) || empty($id)){
            return ["status" => false, "message" => "Informe todos os dados!"];
        }else{
            $produto = Produtos::atualizar($nome, $quantidade, $preco, $id);

            if($produto){
                return ["status" => true, "message" => "Produto atualizado com sucesso!"];
            }else{
                return ["status" => false, "message" => "Erro ao atualizar produto!"];
            }
        }
    }

    public static function cadastrarProdutos($dados){
        $nome = $dados["nome"];
        $quantidade = $dados["quantidade"];
        $preco = $dados["preco"];

        if(empty($nome) || empty($quantidade) || empty($preco)){
            return ["status" => false, "message" => "Informe todos os dados!"];
        }else{
            $cadastrar = Produtos::cadastrar($nome, $quantidade, $preco);

            if($cadastrar){
                return ["status" => true, "message" => "Produto cadastrado com sucesso!"];
            }else{
                return ["status" => false, "message" => "Erro ao cadastrar produto."];
            }
        }
    }

    public static function removeProduto($id){
        $remover = Produtos::remover($id);
        if($remover){
            return ["status" => true, "message" => "Produto removido com sucesso!"];
        }else{
            return ["status" => false, "message" => "Erro ao remover produto!"];
        }
    }
}



?>