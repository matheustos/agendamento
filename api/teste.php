<?php

$inicioBloqueio = $_POST["inicio"];
$fimBloqueio = $_POST["fim"];
function gerarHorarios($inicio = "08:00", $fim = "17:00", $intervaloMinutos = 90) {
    $horarios = [];

    $inicioDate = DateTime::createFromFormat("H:i", $inicio);
    $fimDate = DateTime::createFromFormat("H:i", $fim);

    while ($inicioDate <= $fimDate) {
        $horarios[] = $inicioDate->format("H:i");
        $inicioDate->modify("+{$intervaloMinutos} minutes");
    }

    return $horarios;
}

function bloquearHorarios($horarios, $inicioBloqueio, $fimBloqueio) {
    $inicioBloqueioDate = DateTime::createFromFormat("H:i", $inicioBloqueio);
    $fimBloqueioDate = DateTime::createFromFormat("H:i", $fimBloqueio);

    $resultado = [];

    foreach ($horarios as $hora) {
        $horaDate = DateTime::createFromFormat("H:i", $hora);

        $resultado[] = [
            "hora" => $hora,
            "bloqueado" => ($horaDate >= $inicioBloqueioDate && $horaDate <= $fimBloqueioDate)
        ];
    }

    foreach($resultado as $item){
        if($item["bloqueado"] === true){
            $bloqueadosSomente[] = $item;
        }
    }

    return $bloqueadosSomente;
    
}

// Exemplo de uso:
$horarios = gerarHorarios();
$bloqueados = bloquearHorarios($horarios, $inicioBloqueio, $fimBloqueio);

echo json_encode($bloqueados);
