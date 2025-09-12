<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Validators\AgendamentoValidators;
use Model\Database;

function getAgendaDisponivel($diasAdiante = 30) {
    $conn = Database::conectar();

    $dias = [
        'Monday'    => 'segunda',
        'Tuesday'   => 'terca',
        'Wednesday' => 'quarta',
        'Thursday'  => 'quinta',
        'Friday'    => 'sexta',
        'Saturday'  => 'sabado',
        'Sunday'    => 'domingo'
    ];

    $agenda = [];

    for ($i = 0; $i < $diasAdiante; $i++) {
        $dateTime = new \DateTime("+$i day");
        $data = $dateTime->format("Y-m-d");
        $diaSemana = $dias[$dateTime->format("l")];

        // pega os horários fixos desse dia
        $sqlHorarios = "SELECT horario FROM horarios_semana WHERE dia_semana = ?";
        $stmt = $conn->prepare($sqlHorarios);
        $stmt->bind_param("s", $diaSemana);
        $stmt->execute();
        $result = $stmt->get_result();

        $horarios = [];
        while ($row = $result->fetch_assoc()) {
            $horarios[] = $row['horario'];
        }

        // remove os horários já agendados
        $sqlAgendados = "SELECT horario FROM agenda WHERE data = ? AND status = 'agendado' AND status = 'bloqueado'";
        $stmt2 = $conn->prepare($sqlAgendados);
        $stmt2->bind_param("s", $data);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        $ocupados = [];
        while ($row = $result2->fetch_assoc()) {
            $ocupados[] = $row['horario'];
        }

        $disponiveis = array_diff($horarios, $ocupados);

        $agenda[$data] = array_values($disponiveis); // garante JSON limpo
    }

    return $agenda;
}

// exemplo de uso
$disponiveis = getAgendaDisponivel(30); // próximos 15 dias
header('Content-Type: application/json');
echo json_encode($disponiveis);



?>