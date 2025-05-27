<?php

namespace Controller;
use Model\Agendamento;
use Validators\AgendamentoValidators;
class AgendamentoController{

    public static function agendamento($dados){
        
        $data = $dados["data"];
        $hora = $dados["horario"];
        $nome = $dados["nome"];
        $status = "agendado";
        $servico = $dados["servico"];

        $dateObj = \DateTime::createFromFormat('d/m/Y', $data);
        $dataIso = $dateObj ? $dateObj->format('Y-m-d') : null;

        $horaObj = \DateTime::createFromFormat('H:i', $hora);
        $horaIso = $horaObj ? $horaObj->format('H:i:s') : null;


        if(empty($hora) || empty($nome) || empty($servico)){
            return ["status" => false, "message" => "Insira todos os dados!"];
        }else{
            $validacao = AgendamentoValidators::validacaoAgendamento($dataIso, $horaIso);

            if($validacao["status"] === true){
                return $validacao;
            }else{
                $res = Agendamento::agendar($dataIso, $horaIso, $nome, $status, $servico);
                if($res){
                    return ["status" => true, "message" => "Agendamento efetuado com sucesso!", "data" => $res];
                }else{
                    return ["status" => false, "message" => "Erro ao efetuar agendamento!"];
                }
            }
        }
    }

    public static function buscarPorDia($dia) {

        if (empty($dia)) {
            return [
                "status" => false,
                "message" => "Insira o dia!"
            ];
        }

        $res = Agendamento::buscar($dia);

        if (is_array($res)) {
            return $res;
        } else {
            return [
                "status" => false,
                "message" => "Erro ao consultar agenda."
            ];
        }
    }

    public static function cancelarAgendamento($dados){
        $data = $dados["data"];
        $hora = $dados["horario"];

        $dateObj = \DateTime::createFromFormat('d/m/Y', $data);
        $dataIso = $dateObj ? $dateObj->format('Y-m-d') : null;

        $horaObj = \DateTime::createFromFormat('H:i', $hora);
        $horaIso = $horaObj ? $horaObj->format('H:i:s') : null;

        if(empty($dataIso) || empty($horaIso)){
            return [
                "status" => false,
                "message" => "Informe todos os dados!"
            ];
        }else{
            $validacao = AgendamentoValidators::validacaoCancelamento($dataIso, $horaIso);

            if($validacao["status"] === true){
                return $validacao;
            }else{
                $res = Agendamento::Cancelar($dataIso, $horaIso);

                if($res){
                    return $res;
                }else{
                    return ["status" => false, "message" => "Erro ao cancelar agendamento!"];
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
            return [
                "status" => false,
                "message" => "Informe todos os dados!"
            ];
        }else{
            $validacao = AgendamentoValidators::validacaoCancelamento($dataConvert, $horaConvert);
            
            if($validacao["status"] === true){
                return $validacao;
            }else{
                $res = Agendamento::atualizar($dataConvert, $horaConvert, $nova_dataConvert, $nova_horaConvert);

                if($res){
                    return $res;
                }else{
                    return ["status" => false, "message" => "Erro ao atualizar o agendamento!"];
                }
            }
        }
    }

}

?>

