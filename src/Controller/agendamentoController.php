<?php

namespace Controller;
use Model\Agendamento;
class AgendamentoController{

    public static function agendamento($dados){
        
        $data = $dados["data"];
        $hora = $dados["horario"];

        if(isset($data) or isset($hora)){
            $res = Agendamento::agendar($data, $hora);
            if($res){
                return ["status" => true, "message" => "Agendamento efetuado com sucesso!"];
            }else{
                return ["status" => false, "message" => "Erro ao efetuar agendamento!"];
            }
        }else{
            return ["status" => false, "message" => "Insira todos os dados!"];
        }
    }
}

?>

