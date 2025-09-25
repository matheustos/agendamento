<?php

namespace Model;
use Model\Database;

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

    public static function atualizar($nome, $quantidade, $preco, $id){
        $conn = Database::conectar();
        $stmt = $conn->prepare("UPDATE produtos SET nome = ?, quantidade = ?, preco = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nome, $quantidade, $preco, $id);
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


}


?>