<?php

namespace Controller;
use Model\Produtos;
use Validators\RetornosValidators;

class ProdutosController{
    public static function buscarProdutos(){
        $produtos = Produtos::buscar();

        if($produtos){
            return RetornosValidators::sucessodata("Produtos encontrados", $produtos);
        }else{
            return RetornosValidators::erro("Nenhum produto encontrado!");
        }
    }

    public static function atualizarProdutos($dados){
        $nome = $dados["nome"];
        $quantidade = $dados["quantidade"];
        $preco = $dados["preco"];
        $id = $dados["id"];

        if(empty($nome) || empty($quantidade) || empty($preco) || empty($id)){
            return RetornosValidators::erro("Informe todos os dados!");
        }else{
            $produto = Produtos::atualizar($nome, $quantidade, $preco, $id);

            if($produto){
                return RetornosValidators::sucesso("Produto atualizado com sucesso!");
            }else{
                return RetornosValidators::erro("Erro ao atualizar produto!");
            }
        }
    }

    public static function cadastrarProdutos($dados){
        $nome = $dados["nome"];
        $quantidade = $dados["quantidade"];
        $preco = $dados["preco"];

        if(empty($nome) || empty($quantidade) || empty($preco)){
            return RetornosValidators::erro("Informe todos os dados!");
        }else{
            $cadastrar = Produtos::cadastrar($nome, $quantidade, $preco);

            if($cadastrar){
                return RetornosValidators::sucesso("Produto cadastrado com sucesso!");
            }else{
                return RetornosValidators::erro("Erro ao cadastrar produto.");
            }
        }
    }

    public static function buscarVendasAno($ano){
        if(empty($ano)){
            return RetornosValidators::erro("Digite o ano!");
        }

        $vendas = Produtos::getVendasPorAno($ano);

        if($vendas){
            return $vendas;
        }else{
            return RetornosValidators::erro("Erro ao buscar vendas!");
        }
    }

    public static function removeProduto($id){
        $remover = Produtos::remover($id);
        if($remover){
            return RetornosValidators::sucesso("Produto removido com sucesso!");
        }else{
            return RetornosValidators::erro("Erro ao remover produto!");
        }
    }

    public static function movimentacao($dados){
        $id =  $dados["id"];
        $tipo = $dados["tipo"];
        $quantidade = $dados["quantidade"];

        if(empty($quantidade) || empty($id) || empty($tipo)){
            return RetornosValidators::erro("Informe todos os dados!");
        }

        $quantia = Produtos::buscarQuantidade($id);
        $preco = Produtos::buscarPreco($id);

        if(!$quantia || !$preco){
            return RetornosValidators::erro("Produto não encontrado!");
        }

        $quantidade_banco = $quantia["quantidade"];
        $precoProduto = $preco["preco"];

        if($tipo === "entrada"){
            $entrada = $quantidade_banco + $quantidade;
            $atualizar = Produtos::atualizarQuantidade($entrada, $id);
            if($atualizar){
                return RetornosValidators::sucesso("Estoque atualizado com sucesso!");
            }else{
                return RetornosValidators::erro("Erro ao atualizar estoque!");
            }
        } else if($tipo === "saída"){
            if($quantidade > $quantidade_banco){
                return RetornosValidators::erro("Não é possível realizar uma venda com quantidade superior ao estoque!");
            }

            $saida = $quantidade_banco - $quantidade;
            $valor_vendido = $precoProduto * $quantidade;

            $atualizar = Produtos::atualizarQuantidade($saida, $id);
            if($atualizar){
                Produtos::registrarVenda($id, $quantidade, $precoProduto, $valor_vendido);
                return ["status" => true, "message" => "Estoque atualizado com sucesso!", "vendas" => $valor_vendido];
            } else{
                return RetornosValidators::erro("Erro ao atualizar estoque!");
            }
        }

        // retorno padrão caso $tipo não seja válido
        return RetornosValidators::erro("Tipo inválido!");
    }

}



?>