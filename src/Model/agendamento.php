<?php

namespace Model;
use Model\Database;
use Validators\AgendamentoValidators;

class Agendamento {
    public static function agendar($data, $hora, $nome, $status, $servico) {
        $conn = Database::conectar();
        $stmt = $conn->prepare("INSERT INTO agenda (`data`, `horario`, `nome`, `status`, `servico`) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $data, $hora, $nome, $status, $servico);
        return $stmt->execute();
    }
    
    public static function buscar($dia) {
        if (!is_string($dia)) {
            return AgendamentoValidators::formatarErro("Data inválida (não é string).");
        }

        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        $sql = "SELECT * FROM agenda WHERE DATE(data) = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return AgendamentoValidators::formatarErro("Erro ao preparar a consulta.");
        }

        $stmt->bind_param("s", $dia);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $agendamentos = [];

        while ($row = $resultado->fetch_assoc()) {
            $agendamentos[] = $row;
        }

        $stmt->close();

        return $agendamentos;
    }

    public static function buscarPorDataHora($data, $hora) {
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        $sql = "SELECT * FROM agenda WHERE data = ? AND horario = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return AgendamentoValidators::formatarErro("Erro ao preparar a consulta.".$conn->error);
        }

        $stmt->bind_param("ss", $data, $hora); // ambos são strings
        $stmt->execute();

        $resultado = $stmt->get_result();
        $registros = [];

        while ($row = $resultado->fetch_assoc()) {
            $registros[] = $row;
        }

        $stmt->close();
        
        return AgendamentoValidators::formatarRetorno("Registro encontrado.", $registros);
    }

    public static function getStatus($data, $hora){
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        $sql = "SELECT status FROM agenda WHERE data = ? AND horario = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return AgendamentoValidators::formatarErro("Erro ao preparar a consulta.".$conn->error);
        }

        $stmt->bind_param("ss", $data, $hora); // ambos são strings
        $stmt->execute();

        $resultado = $stmt->get_result();
        $status = null;

        if ($row = $resultado->fetch_assoc()) {
            $status = $row['status'];
        }

        $stmt->close();

        if ($status !== null) {
            return AgendamentoValidators::formatarRetorno("Status encontrado.", $status);
        } else {
            return AgendamentoValidators::formatarErro("Nenhum agendamento encontrado nesse horário.");
        }
    }

    public static function buscarSemana($ano, $semana){
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        // Consulta usando YEAR() e WEEK()
        $sql = "SELECT * FROM agenda 
                WHERE YEAR(data) = $ano AND WEEK(data, 1) = $semana"; 
        // O segundo parâmetro '1' indica que a semana começa na segunda-feira

        $result = $conn->query($sql);

        $agendamentos = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $agendamentos[] = $row; // adiciona cada linha ao array
            }
        }

        return $agendamentos;
    }

    public static function buscarMes($mes){
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        $sql = "SELECT * FROM agenda WHERE MONTH(data) = ? AND YEAR(data) = YEAR(CURDATE()) ORDER BY data, horario";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $mes);
        $stmt->execute();

        // Pega os resultados
        $result = $stmt->get_result();
        $agendamentos = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $agendamentos;
    }

    public static function Cancelar($data, $hora){
        $novo_status = "cancelado";        

        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        // Prepara e executa a atualização
        $sql = "UPDATE agenda SET status = ? WHERE data = ? AND horario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $novo_status, $data, $hora);

        if ($stmt->execute()) {
            return AgendamentoValidators::formatarRetorno("Agendamento cancelado com sucesso!", null);
        } else {
            return AgendamentoValidators::formatarErro("Erro ao cancelar!".$stmt->error);
        }

        $stmt->close();
        $conn->close();
    }

    public static function atualizar($data, $hora, $nova_data, $nova_hora){       

        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        // Prepara e executa a atualização
        $sql = "UPDATE agenda SET data = ? , horario = ? WHERE data = ? AND horario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nova_data, $nova_hora, $data, $hora);

        if ($stmt->execute()) {
            return AgendamentoValidators::formatarRetorno("Agendamento atualizado com sucesso!", null);
        } else {
            return AgendamentoValidators::formatarErro( "Erro ao atualizar!".$stmt->error);
        }

        $stmt->close();
        $conn->close();
    }

    public static function updateStatus($status, $data, $hora, $nome){
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        // Prepara e executa a atualização
        $sql = "UPDATE agenda SET status = ? WHERE data = ? AND horario = ? AND nome = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $status, $data, $hora, $nome);

        if ($stmt->execute()) {
            return AgendamentoValidators::formatarRetorno("Status atualizado com sucesso!", null);
        } else {
            return AgendamentoValidators::formatarErro("Erro ao atualizar status!".$stmt->error);
        }

        $stmt->close();
        $conn->close();
    }
}


?>