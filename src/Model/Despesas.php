<?php

namespace Model;
use Model\Database;
use Validators\AgendamentoValidators;

class Despesas{
    public static function cadastrarDespesas($nome, $quantidade, $preco, $valor, $data){
        $conn = Database::conectar();
        $stmt = $conn->prepare("INSERT INTO despesas (`nome`, `quantidade`, `preco`, `valor`, `data`) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $nome, $quantidade, $preco, $valor, $data);
        return $stmt->execute();
    }

    public static function getDespesas($mes, $ano) {
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        // Consulta somente o campo necessário
        $sql = "SELECT *
                FROM despesas 
                WHERE MONTH(data) = ? AND YEAR(data) = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return AgendamentoValidators::formatarErro("Erro ao preparar a consulta. ".$conn->error);
        }

        $stmt->bind_param("ii", $mes, $ano);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $registros = [];

        while ($row = $resultado->fetch_assoc()) {
            // monta cada registro como array associativo (pra usar no front depois)
            $registros[] = [
                "id" => $row["id"],
                "descricao" => "Despesa",
                "nome" => $row["nome"],
                "data"      => date("d/m/Y", strtotime($row["data"])),
                "quantidade" => $row["quantidade"],
                "preço unitário" => $row["preco"],
                "valor"     => (float)$row["valor"]
            ];
        }

        $stmt->close();

        return $registros; // retorna array de arrays
    }

    public static function getValor($ano, $mes) {
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        $sql = "SELECT SUM(valor) as total
                FROM despesas 
                WHERE MONTH(data) = ? AND YEAR(data) = ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return AgendamentoValidators::formatarErro("Erro ao preparar a consulta. " . $conn->error);
        }

        $stmt->bind_param("ii", $mes, $ano);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();

        // Se não houver despesas, retorna 0
        $total = $row['total'] !== null ? (float)$row['total'] : 0;

        $stmt->close();

        return $total;
    }

    public static function getValorAno($ano) {
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        $sql = "SELECT SUM(valor) as total
                FROM despesas 
                WHERE YEAR(data) = ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return AgendamentoValidators::formatarErro("Erro ao preparar a consulta. " . $conn->error);
        }

        $stmt->bind_param("i",$ano);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();

        // Se não houver despesas, retorna 0
        $total = $row['total'] !== null ? (float)$row['total'] : 0;

        $stmt->close();

        return $total;
    }

}