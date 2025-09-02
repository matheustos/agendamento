<?php

namespace Controller;
use Model\Agendamento;
use Validators\AgendamentoValidators;
class AgendamentoController{

    public static function agendamento($data, $hora, $nome, $servico, $email){
        $status = "agendado";

        $dataIso = AgendamentoValidators::validacaoData($data); /* apenas para testes no insomnia, após concluido a fase de front, retirar os parametros horaIso e DataIso pois o formulario já manda corretmanete sem precisar formatar*/

        $horaIso = AgendamentoValidators::validacaoHora($hora); /* apenas para testes no insomnia, após concluido a fase de front, retirar os parametros horaIso e DataIso pois o formulario já manda corretmanete sem precisar formatar*/


        if(empty($hora) || empty($nome) || empty($servico) || empty($data) || empty($email)){
            return ["status" => false, "message" => "Insira todos os dados!"];
        }else{
            $validacao = AgendamentoValidators::validarBloqueio($data, $hora);
            if($validacao){
                return $validacao;
            }else{
                $validaData = AgendamentoValidators::validaData($data);

                if($validaData["status"] === true){
                    return $validaData;
                }else{
                    $validacao = AgendamentoValidators::validacaoAgendamento($data, $hora);

                    if($validacao["status"] === true){
                        return $validacao;
                    }else{
                        $res = Agendamento::agendar($data, $hora, $nome, $status, $servico);
                        if($res){
                            EmailController::enviar($email, $data, $hora, $nome, $servico);
                            return AgendamentoValidators::formatarRetorno("Agendamento efetuado com sucesso!", $res);
                        }else{
                            return AgendamentoValidators::formatarErro("Nenhum dado recebido.");
                        }
                    }
                }
            }
        }
    }

    public static function alterarStatus($status, $data, $hora, $nome){

        //var_dump($status, $data, $hora, $nome);
        if(empty($status) || empty($data) || empty($hora)){
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

    public static function bloquearAgenda($dados){
        $servico = null;
        $status = "bloqueado";
        $nome = null;
        $data = $dados["data"];
        $hora = $dados["hora"];

        if(empty($data) || empty($hora)){
            return AgendamentoValidators::formatarErro("Informe data e hora!");
        }else{
            
            $dataIso = AgendamentoValidators::validacaoData($data); /* apenas para testes no insomnia, após concluido a fase de front, retirar os parametros horaIso e DataIso pois o formulario já manda corretamente sem precisar formatar*/

            $horaIso = AgendamentoValidators::validacaoHora($hora); /* apenas para testes no insomnia, após concluido a fase de front, retirar os parametros horaIso e DataIso pois o formulario já manda corretamente sem precisar formatar*/

            $getStatus = Agendamento::getStatus($dataIso, $horaIso);
            //var_dump($getStatus);
            if(isset($getStatus['data']) && $getStatus["data"] === "bloqueado"){
                return AgendamentoValidators::formatarErro("Agenda já se encontra bloqueada");
            }else{
                $validacao = AgendamentoValidators::verificarBloqueio($dataIso, $horaIso);
                if($validacao["status"] === true){
                    $res = Agendamento::agendar($dataIso, $horaIso, $nome, $status, $servico);

                    if($res){
                        return AgendamentoValidators::formatarRetorno("Agenda bloqueada com sucesso!", []);
                    }else{
                        return AgendamentoValidators::formatarErro("Erro ao bloquear agenda!");
                    }
                }else{
                    return $validacao;
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
        $email = $dados["email"];

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
                    //busca informações nome e servico no banco de dados para enviar o email com tais informações.
                    $buscar = Agendamento::buscarPorDataHora($dataIso, $horaIso);
                    $nome = $buscar['data'][0]['nome'];
                    EmailController::cancelamento($email, $nome);
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
        $email = $dados["email"];

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
                    //busca informações nome e servico no banco de dados para enviar o email com tais informações.
                    $buscar = Agendamento::buscarPorDataHora($nova_dataConvert, $nova_horaConvert);
                    $nome = $buscar['data'][0]['nome'];
                    $servico = $buscar['data'][0]['servico'];
                    EmailController::atualizar($email, $nova_data, $nova_hora, $nome, $servico);
                    return $res;
                }else{
                    return AgendamentoValidators::formatarErro("Erro ao atualizar o agendamento!");
                }
            }
        }
    }

}

?>

