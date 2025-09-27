<?php

namespace Model;
use Model\Database;
use Validators\AgendamentoValidators;

class Produtos{

    public static function buscar(){
        $conn = Database::conectar();
        $stmt = $conn->prepare("SELECT * FROM produtos");
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_all(MYSQLI_ASSOC);
        }
    }

    public static function buscarQuantidade($id){
        $conn = Database::conectar();
        $stmt = $conn->prepare("SELECT quantidade FROM produtos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
    }

    public static function buscarPreco($id){
        $conn = Database::conectar();
        $stmt = $conn->prepare("SELECT preco FROM produtos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
    }

    public static function atualizar($nome, $quantidade, $preco, $id){
        $conn = Database::conectar();
        $stmt = $conn->prepare("UPDATE produtos SET nome = ?, quantidade = ?, preco = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nome, $quantidade, $preco, $id);
        return $stmt->execute();
    }

    public static function atualizarQuantidade($quantidade, $id){
        $conn = Database::conectar();
        $stmt = $conn->prepare("UPDATE produtos SET quantidade = ? WHERE id = ?");
        $stmt->bind_param("si", $quantidade,  $id);
        return $stmt->execute();
    }

    public static function cadastrar($nome, $quantidade, $preco) {
        $conn = Database::conectar();
        $stmt = $conn->prepare("INSERT INTO produtos (`nome`, `quantidade`, `preco`) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $quantidade, $preco);
        return $stmt->execute();
    }

    public static function remover($id) {
        $conn = Database::conectar(); 
        $sql = "DELETE FROM produtos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public static function registrarVenda($id, $quantidade, $preco, $total){
        date_default_timezone_set('America/Sao_Paulo');
        $data = date("Y-m-d");
        $conn = Database::conectar();
        $stmt = $conn->prepare("INSERT INTO vendas_produtos (`produto_id`, `quantidade`, `preco`, `total`, `data`) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $id, $quantidade, $preco, $total, $data);
        return $stmt->execute();

    }

    public static function getProdutosDia($data){
        $conn = Database::conectar();

        $stmt = $conn->prepare("SELECT SUM(total) AS total_vendido FROM vendas_produtos WHERE DATE(data) = ?");
        $stmt->bind_param("s", $data);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($row = $resultado->fetch_assoc()) {
            return $row['total_vendido'] ?? 0; // retorna 0 se não houver vendas
        }

        return 0;
    }

    public static function getTotalPorIntervalo($dataInicio, $dataFim) {
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        // Consulta para pegar todos os serviços entre as datas
        $sql = "SELECT total FROM vendas_produtos WHERE data BETWEEN ? AND ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return AgendamentoValidators::formatarErro("Erro ao preparar a consulta. ".$conn->error);
        }

        $stmt->bind_param("ss", $dataInicio, $dataFim);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $total = [];

        while ($row = $resultado->fetch_assoc()) {
            $total[] = $row['total']; // só pega o nome do serviço
        }

        $stmt->close();

        return $total; // retorna array de strings com os nomes dos serviços
    }

    public static function getTotalPorMes($mes, $ano) {
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        // Consulta para pegar todos os serviços entre as datas
        $sql = "SELECT total FROM vendas_produtos WHERE MONTH(data) = ? AND YEAR(data) = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return AgendamentoValidators::formatarErro("Erro ao preparar a consulta. ".$conn->error);
        }

        $stmt->bind_param("ii", $mes, $ano);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $total = [];

        while ($row = $resultado->fetch_assoc()) {
            $total[] = $row['total']; // só pega o nome do serviço
        }

        $stmt->close();

        return $total; // retorna array de strings com os nomes dos serviços
    }

    public static function getVendasPorMes($mes, $ano) {
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        // Consulta somente o campo necessário
        $sql = "SELECT *
                FROM vendas_produtos 
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
                "id" => $row["produto_id"],
                "descricao" => "Venda",
                "nome" => $row["nome"],
                "data"      => date("d/m/Y", strtotime($row["data"])),
                "quantidade" => $row["quantidade"],
                "preço unitário" => $row["preco"],
                "valor"     => (float)$row["total"]
            ];
        }

        $stmt->close();

        return $registros; // retorna array de arrays
    }

    public static function getVendasPorAno($ano) {
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        // Consulta somente o campo necessário
        $sql = "SELECT *
                FROM vendas_produtos 
                WHERE YEAR(data) = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return AgendamentoValidators::formatarErro("Erro ao preparar a consulta. ".$conn->error);
        }

        $stmt->bind_param("i", $ano);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $registros = [];

        while ($row = $resultado->fetch_assoc()) {
            // monta cada registro como array associativo (pra usar no front depois)
            $registros[] = [
                "id" => $row["produto_id"],
                "descricao" => "Venda",
                "nome" => $row["nome"],
                "data"      => date("d/m/Y", strtotime($row["data"])),
                "quantidade" => $row["quantidade"],
                "preço unitário" => $row["preco"],
                "valor"     => (float)$row["total"]
            ];
        }

        $stmt->close();

        return $registros; // retorna array de arrays
    }



    public static function getTotalPorAno($ano) {
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        // Consulta para pegar todos os serviços entre as datas
        $sql = "SELECT total FROM vendas_produtos WHERE YEAR(data) = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return AgendamentoValidators::formatarErro("Erro ao preparar a consulta. ".$conn->error);
        }

        $stmt->bind_param("i", $ano);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $total = [];

        while ($row = $resultado->fetch_assoc()) {
            $total[] = $row['total']; // só pega o nome do serviço
        }

        $stmt->close();

        return $total; // retorna array de strings com os nomes dos serviços
    }

}


?>