<?php

namespace Model;
use Model\Database;
use Validators\AgendamentoValidators;

class Agendamento {
    public static function agendar($data, $hora, $nome, $status, $servico, $obs, $telefone) {
        $conn = Database::conectar();
        $stmt = $conn->prepare("INSERT INTO agenda (`data`, `horario`, `nome`, `status`, `servico`, `obs`, `telefone`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $data, $hora, $nome, $status, $servico, $obs, $telefone);
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

        $status = "agendado";

        $sql = "SELECT * FROM agenda WHERE DATE(data) = ? AND status = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return AgendamentoValidators::formatarErro("Erro ao preparar a consulta.");
        }

        $stmt->bind_param("ss", $dia, $status);
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
        if($registros){
            return $registros;
        }
        
    }

    public static function getPorDataHora($data, $hora) {
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
        if($registros){
            return AgendamentoValidators::formatarRetorno("Registros:", $registros);
        }
        
    }

    public static function buscarAgend($data, $hora, $id) {
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        $sql = "SELECT * FROM agenda WHERE data = ? AND horario = ? AND id <> ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return AgendamentoValidators::formatarErro("Erro ao preparar a consulta.".$conn->error);
        }

        $stmt->bind_param("ssi", $data, $hora, $id); // ambos são strings
        $stmt->execute();

        $resultado = $stmt->get_result();
        $registros = [];

        while ($row = $resultado->fetch_assoc()) {
            $registros[] = $row;
        }

        $stmt->close();
        if($registros){
            return AgendamentoValidators::formatarRetorno("Registros:", $registros);
        }
        
    }

    public static function buscarAgenda($data, $hora) {
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
        if($registros){
            return AgendamentoValidators::formatarRetorno("Registros:", $registros);
        }
        
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

    public static function getEmail($nome){
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        $sql = "SELECT email FROM usuarios WHERE nome = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return AgendamentoValidators::formatarErro("Erro ao preparar a consulta.".$conn->error);
        }

        $stmt->bind_param("s", $nome); // ambos são strings
        $stmt->execute();

        $resultado = $stmt->get_result();
        $email = null;

        if ($row = $resultado->fetch_assoc()) {
            $email = $row['email'];
        }

        $stmt->close();

        return $email;
    }

    public static function buscarSemana($ano, $semana){
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        $status = "agendado";

        // Consulta usando YEAR() e WEEK()
        $sql = "SELECT * FROM agenda 
                WHERE status = '{$status}'AND YEAR(data) = $ano AND WEEK(data, 1) = $semana"; 
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

        $status = "agendado";
        $stat = "confirmado";

        $sql = "SELECT * FROM agenda 
        WHERE MONTH(data) = ? AND YEAR(data) = YEAR(CURDATE()) AND status = ? OR status = ?
        ORDER BY data, horario";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $mes, $status, $stat);
        $stmt->execute();

        // Pega os resultados
        $result = $stmt->get_result();
        $agendamentos = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $agendamentos;
    }

    public static function getBloqueio($mes){
        $status = "bloqueado";
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        $sql = "SELECT id, data, horario, status, obs FROM agenda WHERE MONTH(data) = ? AND YEAR(data) = YEAR(CURDATE()) AND status = ? 
        ORDER BY data, horario";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return AgendamentoValidators::formatarErro("Erro ao preparar a consulta.".$conn->error);
        }

        $stmt->bind_param("is", $mes, $status); // ambos são strings
        $stmt->execute();

        $resultado = $stmt->get_result();
        $bloqueios = [];
        while ($row = $resultado->fetch_assoc()) {
            $bloqueios[] = $row;
        }

        $stmt->close();

        return $bloqueios;
    }

    public static function bloquearTodosHorarios($data) {
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }
        
        // Lista de horários fixos
        $horarios = ["09:00", "10:30", "13:30", "15:00", "16:30", "18:00"];

        // Valor fixo para indicar bloqueio
        $status = "bloqueado"; // ou $bloqueado = 1; depende de como está sua tabela

        foreach ($horarios as $horario) {
            // Verifica se já existe agendamento neste dia e horário
            $stmt = $conn->prepare("SELECT id FROM agenda WHERE data = ? AND horario = ?");
            $stmt->bind_param("ss", $data, $horario);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Já existe agendamento, não insere
                $stmt->close();
                continue; // pula para o próximo horário
            }

            $stmt->close();

            // Insere o bloqueio
            $insert = $conn->prepare("INSERT INTO agenda (data, horario, status) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $data, $horario, $status);
            $insert->execute();
            $insert->close();
        }

        return true;
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

        $stmt->close();
        $conn->close();
        
        if ($stmt->execute()) {
            return AgendamentoValidators::formatarRetorno("Agendamento cancelado com sucesso!", null);
        } else {
            return AgendamentoValidators::formatarErro("Erro ao cancelar!".$stmt->error);
        }
    }

    public static function getAgendaDisponivel($diasAdiante = 30) {
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
            $sqlAgendados = "SELECT horario FROM agenda WHERE data = ? AND status = 'agendado'";
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

    public static function atualizar($nova_data, $nova_hora, $id, $nome, $telefone, $status){       

        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }
        
        // Prepara e executa a atualização
        $sql = "UPDATE agenda SET nome = ?, data = ? , horario = ?, telefone = ?, status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $nome, $nova_data, $nova_hora, $telefone, $status, $id);

        $stmt->close();
        $conn->close();

        if ($stmt->execute()) {
            return AgendamentoValidators::formatarRetorno("Agendamento atualizado com sucesso!", null);
        } else {
            return AgendamentoValidators::formatarErro( "Erro ao atualizar!".$stmt->error);
        }
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

        $stmt->close();
        $conn->close();

        if ($stmt->execute()) {
            return AgendamentoValidators::formatarRetorno("Status atualizado com sucesso!", null);
        } else {
            return AgendamentoValidators::formatarErro("Erro ao atualizar status!".$stmt->error);
        }
    }

    public static function atualizarStatus($status, $data, $hora, $nome, $servico){
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        // Prepara e executa a atualização
        $sql = "UPDATE agenda SET status = ?, nome= ?, servico = ? WHERE data = ? AND horario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $status, $nome, $servico, $data, $hora);


        if ($stmt->execute()) {
            return AgendamentoValidators::formatarRetorno("Status atualizado com sucesso!", null);
        }

        $stmt->close();
        $conn->close();
    }
}


?>