<?php

namespace Controller;
use Model\Agendamento;
use Model\Financeiro;


class FinanceiroController{
    public static function calcularPrecosDia(){
        date_default_timezone_set('America/Sao_Paulo');
        $dataHoje = date("Y-m-d"); // dia atual
        $status = "Concluído";

        $servicos = Agendamento::getServico($dataHoje, $status);
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

        $totalDia = ["receitaHoje" => $total];
        return $totalDia;
    }

    public static function calcularPrecosSemana(){
        date_default_timezone_set('America/Sao_Paulo');
        $dataHoje = date("Y-m-d"); // dia atual
        $inicio = date("Y-m-d", strtotime("monday this week", strtotime($dataHoje)));;
        $fim    = date("Y-m-d", strtotime("sunday this week", strtotime($dataHoje)));
        $status = "Concluído";

        $servicos = Agendamento::getServicoPorIntervalo($inicio, $fim, $status);
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

        $totalSemana = ["receitaSemana" => $total];
        return $totalSemana;
    }

    public static function calcularPrecosMes(){
        date_default_timezone_set('America/Sao_Paulo');
        $mes = date("m"); 
        $status = "Concluído";

        $servicos = Agendamento::getServicoPorMes($mes, $status);
        
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

        $totalMes = ["receitaMes" => $total];
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
        $total = 0;
        foreach ($servicosComPreco as $item) {
            $total += $item['preco']; // soma cada preço
        }
        $totalAno = ["receitaAno" => $total];
        return $totalAno;
    }

}


?>