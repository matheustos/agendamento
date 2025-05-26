<?php

namespace Validators;
use Model\Agendamento;

class AgendamentoValidators{

    public static function validacao($data, $hora){

        $consulta = Agendamento::buscarPorDataHora($data, $hora);

        if ($consulta['status'] && count($consulta['data']) > 0) {
            return ["status" => true, "message" => "Já existe agendamento para essa data e hora!"];
        }

        return ["status" => false];
    }
}




?>