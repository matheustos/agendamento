<?php

namespace Validators;
use Model\Agendamento;

class AgendamentoValidators{

    public static function validacaoAgendamento($data, $hora) {
        $consulta = Agendamento::buscarPorDataHora($data, $hora);

        // Nenhum agendamento encontrado
        if (empty($consulta['data'])) {
            return ["status" => false]; // livre para agendar
        }

        // Percorrer os registros encontrados na data e hora
        foreach ($consulta['data'] as $registro) {
            if ($registro['status'] === "agendado") {
                return ["status" => true, "message" => "Já existe agendamento para essa data e hora!"];
            }
        }

        // Se passou pelo loop sem encontrar "agendado", então permite agendar
        return ["status" => false];
    }

    public static function validacaoData($data){

        $dateObj = \DateTime::createFromFormat('d/m/Y', $data);
        $dataIso = $dateObj ? $dateObj->format('Y-m-d') : null;

        return $dataIso;
    }

    public static function validacaoHora($hora){

        $horaObj = \DateTime::createFromFormat('H:i', $hora);
        $horaIso = $horaObj ? $horaObj->format('H:i:s') : null;

        return $horaIso;
    }


}




?>