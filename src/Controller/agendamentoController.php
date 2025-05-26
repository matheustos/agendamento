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

        $dateObj = \DateTime::createFromFormat('d/m/Y', $data);
        $dataIso = $dateObj ? $dateObj->format('Y-m-d') : null;

        $horaObj = \DateTime::createFromFormat('H:i', $hora);
        $horaIso = $horaObj ? $horaObj->format('H:i:s') : null;


        if(empty($data) || empty($hora) || empty($nome)){
            return ["status" => false, "message" => "Insira todos os dados!"];
        }else{
            $validacao = AgendamentoValidators::validacao($dataIso, $horaIso);

            if($validacao["status"] != false){
                return $validacao;
            }else{
                $res = Agendamento::agendar($dataIso, $horaIso, $nome, $status);
                if($res){
                    return ["status" => true, "message" => "Agendamento efetuado com sucesso!"];
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

}

?>

