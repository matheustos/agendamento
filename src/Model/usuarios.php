<?php 

namespace Model;
use Model\Database;
use Validators\AgendamentoValidators;

class Usuarios{
    public static function cadastrar($nome, $email, $senha, $telefone, $user_id) {
        $conn = Database::conectar();
        $stmt = $conn->prepare("INSERT INTO usuarios (`nome`, `email`, `senha`, `telefone`, `user_id`) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nome, $email, $senha, $telefone, $user_id);
        return $stmt->execute();
    }

    public static function buscarPorEmail($email) {
        $conn = Database::conectar();
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
    }

    public static function updateSenha($hash, $email) {
        $conn = Database::conectar();
        $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
        $stmt->bind_param("ss", $hash, $email);
        return $stmt->execute();
    }
}
?>