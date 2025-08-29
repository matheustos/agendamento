<?php

namespace Controller;
use Model\Agendamento;
use Validators\AgendamentoValidators;
class AgendamentoController{

    public static function agendamento($dados){
        
        $data = $dados["data"] ?? null;
        $hora = $dados["horario"] ?? null;
        $nome = $dados["nome"] ?? null;
        $status = "agendado";
        $servico = $dados["servico"] ?? null;

        $dataIso = AgendamentoValidators::validacaoData($data); /* apenas para testes no insomnia, após concluido a fase de front, retirar os parametros horaIso e DataIso pois o formulario já manda corretmanete sem precisar formatar*/

        $horaIso = AgendamentoValidators::validacaoHora($hora); /* apenas para testes no insomnia, após concluido a fase de front, retirar os parametros horaIso e DataIso pois o formulario já manda corretmanete sem precisar formatar*/


        if(empty($hora) || empty($nome) || empty($servico)){
            return ["status" => false, "message" => "Insira todos os dados!"];
        }else{
            $validaData = AgendamentoValidators::validaData($dataIso);

            if($validaData["status"] === true){
                return $validaData;
            }else{
                $validacao = AgendamentoValidators::validacaoAgendamento($dataIso, $horaIso);

                if($validacao["status"] === true){
                    return $validacao;
                }else{
                    $res = Agendamento::agendar($dataIso, $horaIso, $nome, $status, $servico);
                    if($res){
                        return AgendamentoValidators::formatarRetorno("Agendamento efetuado com sucesso!", $res);
                    }else{
                        return AgendamentoValidators::formatarErro("Nenhum dado recebido.");
                    }
                }
            }
            
        }
    }

    public static function alterarStatus($status, $data, $hora, $nome){

        //var_dump($status, $data, $hora, $nome);
        if(empty($status) || empty($data) || empty($hora) || empty($nome)){
            return AgendamentoValidators::formatarErro("Informe todos os dados!");
        }else{
            $dataIso = AgendamentoValidators::validacaoData($data);
            $horaIso = AgendamentoValidators::validacaoHora($hora);

            $validacao = AgendamentoValidators::validacaoCancelamento($dataIso, $horaIso);
            if($validacao["status"] === true){
                return $validacao;
            }else{
                $res = Agendamento::updateStatus($status, $dataIso, $horaIso, $nome);

                if($res){
                    return AgendamentoValidators::formatarRetorno($res, []);
                }else{
                    return AgendamentoValidators::formatarErro("Erro ao alterar status!");
                }
            }
        }
    }

    public static function buscarPorDia($dia) {

        if (empty($dia)) {
            return AgendamentoValidators::formatarErro("Informe o dia!");
        }

        $res = Agendamento::buscar($dia);

        if (is_array($res)) {
            return $res;
        } else {
            return AgendamentoValidators::formatarErro("Erro ao consultar agenda.");
        }
    }

    public static function buscarPorMes($mes){
        if(empty($mes)){
            return AgendamentoValidators::formatarErro("Informe o mês");
        }

        $res = Agendamento::buscarMes($mes);

        if (is_array($res)) {
            if($res){
                return AgendamentoValidators::formatarRetorno("Registros encontrados!",$res);
            }else{
                return AgendamentoValidators::formatarErro("Não existem registros para esse mês!");
            }
        } else {
            return AgendamentoValidators::formatarErro("Erro ao consultar agenda.");
        }
    }

    public static function buscarHoje(){
        $data_hoje = date("Y-m-d");

        $res = Agendamento::buscar($data_hoje);

        if(is_array($res)){
            if($res){
                return AgendamentoValidators::formatarRetorno("Seus agendamentos para hoje são:", $res);
            }else{
                return AgendamentoValidators::formatarErro("Nenhum agendamento para hoje!");
            }
        }else{
            return AgendamentoValidators::formatarErro("Erro ao consultar agenda.");
        }
    }

    public static function cancelarAgendamento($dados){
        $data = $dados["data"];
        $hora = $dados["horario"];

        $dataIso = AgendamentoValidators::validacaoData($data);

        $horaIso = AgendamentoValidators::validacaoHora($hora);
        if(empty($dataIso) || empty($horaIso)){
            return AgendamentoValidators::formatarErro("Informe todos os dados!");
        }else{
            $validacao = AgendamentoValidators::validacaoCancelamento($dataIso, $horaIso);

            if($validacao["status"] === true){
                return $validacao;
            }else{
                $res = Agendamento::Cancelar($dataIso, $horaIso);

                if($res){
                    return $res;
                }else{
                    return AgendamentoValidators::formatarErro("Erro ao cancelar agendamento!");
                }
            }
        }
    }

    public static function atualizarAgendamento($dados){
        $data = $dados["data"];
        $hora = $dados["horario"];
        $nova_data = $dados["nova_data"];
        $nova_hora = $dados["nova_hora"];

        $dataConvert = AgendamentoValidators::validacaoData($data);
        $nova_dataConvert = AgendamentoValidators::validacaoData($nova_data);

        $horaConvert = AgendamentoValidators::validacaoHora($hora);
        $nova_horaConvert = AgendamentoValidators::validacaoHora($nova_hora);

        if(empty($data) || empty($hora) || empty($nova_data) || empty($nova_hora)){
            return AgendamentoValidators::formatarErro("Informe todos os dados!");
        }else{
            $validacao = AgendamentoValidators::validacaoCancelamento($dataConvert, $horaConvert);
            
            if($validacao["status"] === true){
                return $validacao;
            }else{
                $res = Agendamento::atualizar($dataConvert, $horaConvert, $nova_dataConvert, $nova_horaConvert);

                if($res){
                    return $res;
                }else{
                    return AgendamentoValidators::formatarErro("Erro ao atualizar o agendamento!");
                }
            }
        }
    }

}

?>

