<?php

namespace Controller;

use Model\Despesas;
use Validators\RetornosValidators;

class DespesasController{

    public static function cadastar($nome, $quantidade, $preco){
        if(empty($nome) || empty($quantidade) || empty($preco)){
            return RetornosValidators::erro("Preencha todos os dados!");
        }
        date_default_timezone_set('America/Sao_Paulo');
        $data = date("Y-m-d");
        $valor = $quantidade * $preco;
        $despesa = Despesas::cadastrarDespesas($nome, $quantidade, $preco, $valor, $data);

        if($despesa){
            return RetornosValidators::sucesso("Despesa cadastrada com sucesso!");
        }else{
            return RetornosValidators::erro("Erro ao cadastrar produto");
        }
    }
}
?>