<?php

namespace Controller;
use Model\Agendamento;
use Validators\AgendamentoValidators;
use Validators\Retornos;
class AgendamentoController{
    // lógica e validações para fazer o agendamento
    public static function agendamento($data, $hora, $nome, $servico, $obs, $telefone, $emailForm, $user){
        $status = "agendado";

        // verifica se os campos estão vazios, se sim, retorna erro
        if(empty($hora) || empty($nome) || empty($servico) || empty($data) || empty($telefone)){
            return Retornos::erro("Insira todos os dados!");
        }else{
            // permite que a observação seja nula, caso não seja passada via form
            if(empty($obs)){
                $obs = null;
            }

            if(empty($emailForm)){
                $emailForm = null;
            }
            // verifica se existe bloqueio na data escolhida
            $validacao = AgendamentoValidators::validarBloqueio($data, $hora);
            if($validacao){
                return $validacao;
            }else{
                // verifica se a data informada é anterior à data atual
                $validaData = AgendamentoValidators::validaData($data);

                if($validaData["status"] === true){
                    return $validaData;
                }else{
                    // verifica se já existe agendamento para aquela data/hora
                    $validacao = AgendamentoValidators::buscaAgendamento($data, $hora);

                    if($validacao["status"] === true){
                        return $validacao;
                    }else{
                        // se não houver, agenda
                        $res = Agendamento::agendar($data, $hora, $nome, $status, $servico, $obs, $telefone, $user, $emailForm);
                        if($res){
                            if($emailForm){
                                // envia email de confirmação do agendamento
                                EmailController::enviar($emailForm, $data, $hora, $nome, $servico, $obs);
                                return ["status" => true, "message" => "Agendamento efetuado com sucesso!"];
                            }else{
                                if($user != "1"){
                                    // pega o email do user no bd
                                    $email = Agendamento::getEmail($user);
                                    if($email){
                                        // envia confirmação de agendamento no email informado via form, caso não haja email no bd
                                        EmailController::enviar($email, $data, $hora, $nome, $servico, $obs);
                                    }
                                    return ["status" => true, "message" => "Agendamento efetuado com sucesso!"];
                                }
                                // agenda sem enviar email caso não seja encontrado/informado nenhum email
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

    // lógica e validações para fazer bloqueio de agenda
    public static function bloquearAgenda($dados){
        $servico = null;
        $status = "bloqueado";
        $nome = null;
        $data = $dados["data"];
        $hora = $dados["horario"];
        $obs = $dados["obs"];

        // verifica se os dados essenciais para o bloqueio foram informadas
        if(empty($data) || empty($hora)){
            return AgendamentoValidators::formatarErro("Informe data e hora!");
        }else{
            // permite que a observação seja nula se não for informada
            if(empty($obs) || !isset($obs)){
                $obs = null;
            }
            // pega status do registro que consta na data/hora do bloqueio
            $getStatus = Agendamento::getStatus($data, $hora);
            if(isset($getStatus['data'])){
                $retorno = $getStatus["data"];
                if($retorno === "bloqueado"){
                    return AgendamentoValidators::formatarErro("Agenda já se encontra bloqueada");
                }else{
                    if($retorno === "cancelado"){
                        $res = Agendamento::atualizarStatus($status, $data, $hora, $nome, $servico, $obs);

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
                                $res = Agendamento::agendar($data, $hora, $nome, $status, $servico, $obs, null, null, null);

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
                    $res = Agendamento::agendar($data, $hora, $nome, $status, $servico, $obs, null, null, null);

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
        date_default_timezone_set('America/Sao_Paulo');
        $dataHoje = date('Y-m-d');

        $mes = date("m");
        $res = Agendamento::getBloqueio($mes);

        $bloqueios = [];

        if($res){
            //filtra e pega apenas as datas bloqueadas que sejam iguais ou maiores que o dia atual
            foreach($res as $data){
                if($data['data'] >= $dataHoje){
                    $bloqueios[] = $data;
                }
            }
            return $bloqueios;
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

    public static function buscarPorStatus(){
        $agendado = Agendamento::buscarAgendadoPorData();
        $confirmado = Agendamento::buscarConfirmadoPorData();
        $concluido = Agendamento::buscarConcluidoPorData();

        $dados = array_merge($agendado, $confirmado, $concluido);
        return $dados;
    }

    public static function buscarConcluidos($mes, $ano){
        if(empty($mes) || empty($ano)){
            $mes = date("m");
            $ano = date("Y");
        }
        $status = "Concluído";

        $agendamentos = Agendamento::getAgendaMes($mes, $ano, $status);

        if($agendamentos){
            return $agendamentos;
        }else{
            return ["status" => false, "message" => "Nenhum agendamento encontrado!"];
        }
    }

    public static function buscarConcluidosAno($ano){
        $status = "Concluído";

        $agendamentos = Agendamento::getAgendaAno($ano, $status);

        if($agendamentos){
            return $agendamentos;
        }else{
            return ["status" => false, "message" => "Nenhum agendamento encontrado!"];
        }
    }

    public static function buscarTodosAgendamentos(){
        $res = Agendamento::buscarAgendamentos();

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

    public static function buscarPorMesEUser($user){
        $mes = date("m");
        $res = Agendamento::buscarMesUser($mes, $user);

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

    public static function cancelarAgendamento($dados, $user){
        $data = $dados["data"];
        $hora = $dados["horario"];

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
                    if($user != "1"){
                        $email = $buscar['data'][0]['email'];
                        if($email){
                            EmailController::cancelamento($email, $nome, $data, $hora);
                            return $res;
                        }
                    }
                    return $res;
                }else{
                    return AgendamentoValidators::formatarErro("Erro ao cancelar agendamento!");
                }
            }
        }
    }

    public static function atualizarAgendamento($dados, $user){
        $id = $dados["id"];
        $data = $dados["data"];
        $hora = $dados["horario"];
        $nome = $dados["nome"];
        $telefone = $dados["telefone"];
        $status = $dados["status"];
        $emailForm = $dados["email"];

        date_default_timezone_set('America/Sao_Paulo');
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
                if($status === "Selecione o Status"){
                    $status = "agendado";
                }
                $validacao = AgendamentoValidators::validacaoAgendamento($data, $hora, $id);
                if($validacao["status"] === true){
                    return [
                        "status" => "error", // sempre "error"
                        "message" => $validacao["message"]
                    ];
                }else{
                    if(empty($status)){
                        $status = "agendado";
                        $res = Agendamento::atualizar($data, $hora, $id, $nome, $telefone, $status);

                        if($res){
                            //busca informações nome e servico no banco de dados para enviar o email com tais informações.
                            $buscar = Agendamento::buscarPorDataHora($data, $hora);

                            $nome = $buscar[0]['nome'];
                            $servico = $buscar[0]['servico'];

                            if($emailForm){
                                EmailController::atualizar($emailForm, $data, $hora, $nome, $servico);
                                return ["status" => "success", "data" => $res];
                            }else{
                                if($user != "1"){
                                    $email = Agendamento::getEmail($user);
                                    if($email){
                                        EmailController::atualizar($email, $data, $hora, $nome, $servico);
                                    }
                                }
                                return ["status" => "success", "data" => $res];
                            }
                        }else{
                            return AgendamentoValidators::formatarErro("Erro ao atualizar o agendamento!");
                        }
                    }else{
                        if($status === "Confirmado"){
                            $res = Agendamento::atualizar($data, $hora, $id, $nome, $telefone, $status);

                            if($res){
                                //busca informações nome e servico no banco de dados para enviar o email com tais informações.
                                $buscar = Agendamento::buscarPorDataHora($data, $hora);

                                $nome = $buscar[0]['nome'];
                                $servico = $buscar[0]['servico'];

                                if($emailForm){
                                    EmailController::confirmar($emailForm, $data, $hora, $nome, $servico);
                                    return ["status" => "success", "data" => $res];
                                }else{
                                    if($user != "1"){
                                        $email = Agendamento::getEmail($user);
                                        if($email){
                                            EmailController::confirmar($email, $data, $hora, $nome, $servico);
                                        }
                                    }
                                    return ["status" => "success", "data" => $res];
                                }
                            }else{
                                return AgendamentoValidators::formatarErro("Erro ao atualizar o agendamento!");
                            }
                        }else{
                            $res = Agendamento::atualizar($data, $hora, $id, $nome, $telefone, $status);

                            if($res){
                                return ["status" => "success", "data" => $res];
                            }else{
                                return AgendamentoValidators::formatarErro("Erro ao atualizar o agendamento!");
                            }
                        }
                    }
                    
                }
            }
        }
    }

}

?>

