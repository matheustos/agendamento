<?php

namespace Model;
use Model\Database;
class Agendamento {
    public static function agendar($data, $hora) {
        $conn = Database::conectar();
        $stmt = $conn->prepare("INSERT INTO data_horario (data, horario) VALUES (?, ?)");
        $stmt->bind_param("ss", $data, $hora);
        return $stmt->execute();
    }
}


?>