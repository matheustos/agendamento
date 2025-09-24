<?php

namespace Model;
use Model\Database;
use Validators\AgendamentoValidators;
class Financeiro{
    public static function getpreco($servico){
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        $sql = "SELECT preco FROM servicos WHERE servico = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return AgendamentoValidators::formatarErro("Erro ao preparar a consulta.".$conn->error);
        }

        $stmt->bind_param("s", $servico); 
        $stmt->execute();

        $resultado = $stmt->get_result();
        $preco = null;

        if ($row = $resultado->fetch_assoc()) {
            $preco = $row['preco'];
        }

        $stmt->close();

        return $preco;
    }


}


?>