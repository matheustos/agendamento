<?php

namespace Model;
use Model\Database;
class Agendamento {
    public static function agendar($data, $hora, $nome, $status) {
        $conn = Database::conectar();
        $stmt = $conn->prepare("INSERT INTO agenda (data, horario, nome, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $data, $hora, $nome, $status);
        return $stmt->execute();
    }
    
    public static function buscar($dia) {
        if (!is_string($dia)) {
            return ["status" => false, "message" => "Data inválida (não é string)."];
        }

        $conn = Database::conectar();

        if (!$conn) {
            return ["status" => false, "message" => "Erro na conexão com o banco de dados."];
        }

        $sql = "SELECT * FROM agenda WHERE DATE(data) = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return ["status" => false, "message" => "Erro ao preparar a consulta."];
        }

        $stmt->bind_param("s", $dia);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $agendamentos = [];

        while ($row = $resultado->fetch_assoc()) {
            $agendamentos[] = $row;
        }

        $stmt->close();

        return [
            "status" => true,
            "data" => $agendamentos,
            "message" => count($agendamentos) > 0 ? "Registros encontrados." : "Nenhum registro encontrado."
        ];
    }

    public static function buscarPorDataHora($data, $hora) {
        $conn = Database::conectar();

        if (!$conn) {
            return ["status" => false, "message" => "Erro na conexão com o banco de dados."];
        }

        $sql = "SELECT * FROM agenda WHERE data = ? AND horario = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return ["status" => false, "message" => "Erro ao preparar a consulta.".$conn->error];
        }

        $stmt->bind_param("ss", $data, $hora); // ambos são strings
        $stmt->execute();

        $resultado = $stmt->get_result();
        $registros = [];

        while ($row = $resultado->fetch_assoc()) {
            $registros[] = $row;
        }

        $stmt->close();
        
        return [
            "status" => true,
            "data" => $registros,
            "message" => "Registro encontrado."
        ];
    }
}


?>