<?php

namespace Controller;

use Model\Despesas;

class DespesasController{

    public static function cadastar($nome, $quantidade, $preco){
        if(empty($nome) || empty($quantidade) || empty($preco)){
            return ["status" => false, "message" => "Preencha todos os dados!"];
        }
        date_default_timezone_set('America/Sao_Paulo');
        $data = date("Y-m-d");
        $valor = $quantidade * $preco;
        $despesa = Despesas::cadastrarDespesas($nome, $quantidade, $preco, $valor, $data);

        if($despesa){
            return ["status" => true, "message" => "Despesa cadastrada com sucesso!"];
        }else{
            return ["status" => false, "message" => "Erro ao cadastrar produto"];
        }
    }
}
?>