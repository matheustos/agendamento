<?php

namespace Controller;
use Model\Agendamento;
use Model\Financeiro;
use Model\Produtos;
use Model\Despesas;

class FinanceiroController{
    public static function calcularPrecosDia(){
        date_default_timezone_set('America/Sao_Paulo');
        $dataHoje = date("Y-m-d"); // dia atual
        $status = "Concluído";

        // ---- Serviços concluídos ----
        $servicos = Agendamento::getServico($dataHoje, $status);
        $totalServicos = 0;

        foreach ($servicos as $servico) {
            $preco = Financeiro::getPreco($servico);
            $totalServicos += $preco;
        }

        // ---- Produtos vendidos ----
        $totalProdutos = Produtos::getProdutosDia($dataHoje);

        // ---- Receita total ----
        $totalDia = [
            "receitaHoje"     => $totalServicos + $totalProdutos
        ];

        return $totalDia;
    }


    public static function calcularPrecosData($data){
        $status = "Concluído";

        $servicos = Agendamento::getServico($data, $status);
        $servicosComPreco = [];

        foreach ($servicos as $servico) {
            $preco = Financeiro::getpreco($servico); // pega o preço do serviço
            $servicosComPreco[] = [
                "servico" => $servico,
                "preco" => $preco
            ];
        }
        $total = 0;
        foreach ($servicosComPreco as $item) {
            $total += $item['preco']; // soma cada preço
        }

        // ---- Produtos vendidos ----
        $totalProdutos = Produtos::getProdutosDia($data);

        $totalDia = $total + $totalProdutos;

        return $totalDia;
    }

    public static function calcularPrecosSemana(){
        date_default_timezone_set('America/Sao_Paulo');
        $dataHoje = date("Y-m-d"); 
        $inicio = date("Y-m-d", strtotime("monday this week", strtotime($dataHoje)));
        $fim    = date("Y-m-d", strtotime("sunday this week", strtotime($dataHoje)));
        $status = "Concluído";

        // ---- Serviços concluídos ----
        $servicos = Agendamento::getServicoPorIntervalo($inicio, $fim, $status);
        $totalServicos = 0;

        foreach ($servicos as $servico) {
            $preco = Financeiro::getPreco($servico);
            $totalServicos += $preco;
        }

        // ---- Produtos vendidos ----
        $produtosTotais = Produtos::getTotalPorIntervalo($inicio, $fim); // array de totais
        $totalProdutos = 0;

        foreach ($produtosTotais as $valor) {
            $totalProdutos += (float) $valor; // soma todos os totais
        }

        // ---- Receita total ----
        $totalSemana = [
            "receitaSemana" => $totalServicos + $totalProdutos
        ];

        return $totalSemana;
    }


    public static function calcularPrecosMes(){
        date_default_timezone_set('America/Sao_Paulo');
        $mes = date("m"); 
        $ano = date("Y");
        $status = "Concluído";

        $servicos = Agendamento::getServicoPorMes($mes, $ano, $status);
        
        $servicosComPreco = [];

        foreach ($servicos as $servico) {
            $preco = Financeiro::getpreco($servico); // pega o preço do serviço
            $servicosComPreco[] = [
                "servico" => $servico,
                "preco" => $preco
            ];
        }
        $totalServicos = 0;
        foreach ($servicosComPreco as $item) {
            $totalServicos += $item['preco']; // soma cada preço
        }

        // ---- Produtos vendidos ----
        $produtosTotais = Produtos::getTotalPorMes($mes, $ano); // array de totais
        $totalProdutos = 0;

        foreach ($produtosTotais as $valor) {
            $totalProdutos += (float) $valor; // soma todos os totais
        }

        // -----Despesas mês -------

        $despesas = Despesas::getValor($ano, $mes);

        $totalMes = ["receitaMes" => $totalServicos + $totalProdutos, "DespesasMes" => $despesas];
        return $totalMes;
    }

    public static function calcularMesEspecifico($mes, $ano){
        $status = "Concluído";

        $servicos = Agendamento::getServicoPorMes($mes, $ano, $status);
        
        $servicosComPreco = [];

        foreach ($servicos as $servico) {
            $preco = Financeiro::getpreco($servico); // pega o preço do serviço
            $servicosComPreco[] = [
                "servico" => $servico,
                "preco" => $preco
            ];
        }
        $totalServicos = 0;
        foreach ($servicosComPreco as $item) {
            $totalServicos += $item['preco']; // soma cada preço
        }

        // ---- Produtos vendidos ----
        $produtosTotais = Produtos::getTotalPorMes($mes, $ano); // array de totais
        $totalProdutos = 0;

        foreach ($produtosTotais as $valor) {
            $totalProdutos += (float) $valor; // soma todos os totais
        }

        // -----Despesas mês -------

        $despesas = Despesas::getValor($ano, $mes);

        $totalMes = [$totalServicos + $totalProdutos, "DespesasMes" => $despesas];
        return $totalMes;
    }

    public static function calcularPrecosAno(){
        date_default_timezone_set('America/Sao_Paulo');
        $ano = date("Y"); 
        $status = "Concluído";

        $servicos = Agendamento::getServicoPorAno($ano, $status);
        $servicosComPreco = [];

        foreach ($servicos as $servico) {
            $preco = Financeiro::getpreco($servico); // pega o preço do serviço
            $servicosComPreco[] = [
                "servico" => $servico,
                "preco" => $preco
            ];
        }
        $totalServicos = 0;
        foreach ($servicosComPreco as $item) {
            $totalServicos += $item['preco']; // soma cada preço
        }

        // ---- Produtos vendidos ----
        $produtosTotais = Produtos::getTotalPorAno($ano); // array de totais
        $totalProdutos = 0;

        foreach ($produtosTotais as $valor) {
            $totalProdutos += (float) $valor; // soma todos os totais
        }

        $totalAno = ["receitaAno" => $totalServicos + $totalProdutos];
        return $totalAno;
    }

    public static function calcularAnoEspecifico($ano){
        $status = "Concluído";

        $servicos = Agendamento::getServicoPorAno($ano, $status);
        $servicosComPreco = [];

        foreach ($servicos as $servico) {
            $preco = Financeiro::getpreco($servico); // pega o preço do serviço
            $servicosComPreco[] = [
                "servico" => $servico,
                "preco" => $preco
            ];
        }
        $totalServicos = 0;
        foreach ($servicosComPreco as $item) {
            $totalServicos += $item['preco']; // soma cada preço
        }

        // ---- Produtos vendidos ----
        $produtosTotais = Produtos::getTotalPorAno($ano); // array de totais
        $totalProdutos = 0;

        foreach ($produtosTotais as $valor) {
            $totalProdutos += (float) $valor; // soma todos os totais
        }
        
        // -----Despesas mês -------

        $despesas = Despesas::getValorAno($ano);

        $totalAno = [$totalServicos + $totalProdutos, "DespesasMes" => $despesas];
        return $totalAno;
    }
}


?>