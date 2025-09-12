<?php

namespace Controller;
use Model\Agendamento;
use Validators\AgendamentoValidators;
class AgendamentoController{

    public static function agendamento($data, $hora, $nome, $servico, $obs, $telefone){
        $status = "agendado";

        $dataIso = AgendamentoValidators::validacaoData($data); /* apenas para testes no insomnia, após concluido a fase de front, retirar os parametros horaIso e DataIso pois o formulario já manda corretmanete sem precisar formatar*/

        $horaIso = AgendamentoValidators::validacaoHora($hora); /* apenas para testes no insomnia, após concluido a fase de front, retirar os parametros horaIso e DataIso pois o formulario já manda corretmanete sem precisar formatar*/


        if(empty($hora) || empty($nome) || empty($servico) || empty($data) || empty($telefone)){
            return ["status" => false, "message" => "Insira todos os dados!"];
        }else{
            if(empty($obs)){
                $obs = null;
            }
            $validacao = AgendamentoValidators::validarBloqueio($data, $hora);
            if($validacao){
                return $validacao;
            }else{
                $validaData = AgendamentoValidators::validaData($data);

                if($validaData["status"] === true){
                    return $validaData;
                }else{
                    $validacao = AgendamentoValidators::buscaAgendamento($data, $horaIso);

                    if($validacao["status"] === true){
                        return $validacao;
                    }else{
                        $res = Agendamento::agendar($data, $hora, $nome, $status, $servico, $obs, $telefone);
                        if($res){
                            $email = Agendamento::getEmail($nome);
                            if($email){
                                EmailController::enviar($email, $data, $hora, $nome, $servico);
                                return ["status" => true, "message" => "Agendamento efetuado com sucesso!"];
                            }else{
                                return ["status" => true, "message" => "Agendamento efetuado com sucesso!"];
                            }
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
        $hora = $dados["horario"];
        $obs = $dados["obs"];

        if(empty($data) || empty($hora)){
            return AgendamentoValidators::formatarErro("Informe data e hora!");
        }else{
            if(empty($obs)){
                $obs = null;
            }
            $getStatus = Agendamento::getStatus($data, $hora);
            if(isset($getStatus['data'])){
                $retorno = $getStatus["data"];
                if($retorno === "bloqueado"){
                    return AgendamentoValidators::formatarErro("Agenda já se encontra bloqueada");
                }else{
                    if($retorno === "cancelado"){
                        $res = Agendamento::atualizarStatus($status, $data, $hora, $nome, $servico);

                        if($res){
                            return AgendamentoValidators::formatarRetorno("Agenda bloqueada com sucesso!", []);
                        }else{
                            return AgendamentoValidators::formatarErro("Erro ao bloquear agenda!");
                        }
                    }else{
                        $validacao = AgendamentoValidators::verificarBloqueio($data, $hora);
                        if($validacao["status"] === true){
                            if($hora === "Todos os horários"){
                                $res = Agendamento::bloquearTodosHorarios($data);
                                if($res === true){
                                    return AgendamentoValidators::formatarRetorno("Agenda bloqueada com sucesso!", []);
                                }
                            }else{
                                $res = Agendamento::agendar($data, $hora, $nome, $status, $servico, $obs, null);

                                if($res){
                                    return AgendamentoValidators::formatarRetorno("Agenda bloqueada com sucesso!", []);
                                }else{
                                    return AgendamentoValidators::formatarErro("Erro ao bloquear agenda!");
                                }
                            }
                            
                        }else{
                            return $validacao;
                        }
                    }    
                }
            }else{
                if($hora === "Todos os horários"){
                    $res = Agendamento::bloquearTodosHorarios($data);
                    if($res === true){
                        return AgendamentoValidators::formatarRetorno("Agenda bloqueada com sucesso!", []);
                    }
                }else{
                    $res = Agendamento::agendar($data, $hora, $nome, $status, $servico, $obs, null);

                    if($res){
                        return AgendamentoValidators::formatarRetorno("Agenda bloqueada com sucesso!", []);
                    }else{
                        return AgendamentoValidators::formatarErro("Erro ao bloquear agenda!");
                    }
                }
            }
        }
    }

    public static function buscarBloqueios(){
        $mes = date("m");
        $res = Agendamento::getBloqueio($mes);

        if($res){
            return $res;
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

    public static function buscarPorMes(){
        $mes = date("m");
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

    public static function buscarPorSemana(){
        $ano = date('Y');
        $semana = date('W');

        $res = Agendamento::buscarSemana($ano, $semana);

        if(is_array($res)){
            return AgendamentoValidators::formatarRetorno("Agendamentos:", $res);
        }else{
            return AgendamentoValidators::formatarErro("Não foi possível consultar os agendamentos da semana");
        }
    }

    public static function buscarHoje(){
        $data_hoje = date("Y-m-d");

        $res = Agendamento::buscar($data_hoje);
        if($res){
            return AgendamentoValidators::formatarRetorno("Seus agendamentos para hoje são:", $res);
        }else{
            return AgendamentoValidators::formatarErro("Nenhum agendamento para hoje!");
        }
    }

    public static function cancelarAgendamento($dados){
        $data = $dados["data"];
        $hora = $dados["horario"];

        /*$dataIso = AgendamentoValidators::validacaoData($data);

        $horaIso = AgendamentoValidators::validacaoHora($hora);*/
        if(empty($data) || empty($hora)){
            return AgendamentoValidators::formatarErro("Informe todos os dados!");
        }else{
            $validacao = AgendamentoValidators::validacaoCancelamento($data, $hora);

            if($validacao["status"] === true){
                return $validacao;
            }else{
                $res = Agendamento::Cancelar($data, $hora);

                if($res){
                    //busca informações nome e servico no banco de dados para enviar o email com tais informações.
                    $buscar = Agendamento::getPorDataHora($data, $hora);
                    $nome = $buscar['data'][0]['nome'];
                    $email = Agendamento::getEmail($nome);
                    if($email){
                        EmailController::cancelamento($email, $nome, $data, $hora);
                        return $res;
                    }else{
                        return $res;
                    }
                }else{
                    return AgendamentoValidators::formatarErro("Erro ao cancelar agendamento!");
                }
            }
        }
    }

    public static function atualizarAgendamento($dados){
        $id = $dados["id"];
        $data = $dados["data"];
        $hora = $dados["horario"];
        $nome = $dados["nome"];
        $telefone = $dados["telefone"];
        $status = $dados["status"];
        if(empty($status)){
            $status = "agendado";
        }

        $data_hoje = date("Y-m-d");

        if(empty($hora) || empty($data) || empty($id) || empty($nome) || empty($telefone)){
            return AgendamentoValidators::formatarErro("Informe todos os dados!");
        }else{
            if($data < $data_hoje){
                return [
                    "status" => "error",
                    "message" => "Não é possível agendar para uma data passada!"
                ];
            }else{
                $validacao = AgendamentoValidators::validacaoAgendamento($data, $hora, $id);
                if($validacao["status"] === true){
                    return [
                        "status" => "error", // sempre "error"
                        "message" => $validacao["message"]
                    ];
                }else{
                    $res = Agendamento::atualizar($data, $hora, $id, $nome, $telefone, $status);

                    if($res){
                        //busca informações nome e servico no banco de dados para enviar o email com tais informações.
                        $buscar = Agendamento::buscarPorDataHora($data, $hora);

                        $nome = $buscar[0]['nome'];
                        $servico = $buscar[0]['servico'];

                        $email = Agendamento::getEmail($nome);
                        if($email){
                            EmailController::atualizar($email, $data, $hora, $nome, $servico);
                            return ["status" => "success", "data" => $res];
                        }else{
                            return ["status" => "success", "data" => $res];
                        }
                    }else{
                        return AgendamentoValidators::formatarErro("Erro ao atualizar o agendamento!");
                    }
                }
            }
        }
    }

}

?>

