<?php 

namespace Model;
use Model\Database;
use Validators\AgendamentoValidators;

class Usuarios{
    public static function cadastrar($nome, $email, $senha, $telefone, $acesso) {
        $conn = Database::conectar();
        $stmt = $conn->prepare("INSERT INTO usuarios (`nome`, `email`, `senha`, `telefone`, `acesso`) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nome, $email, $senha, $telefone, $acesso);
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
        }else{
            return false;
        }
    }

    public static function updateSenha($hash, $email) {
        $conn = Database::conectar();
        $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
        $stmt->bind_param("ss", $hash, $email);
        return $stmt->execute();
    }

    public static function atualizar($nome, $telefone, $nomeUpdate){
        $conn = Database::conectar();
        $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, telefone = ? WHERE nome = ?");
        $stmt->bind_param("sss", $nomeUpdate, $telefone, $nome);
        return $stmt->execute();
    }

    public static function listar(){
        $conn = Database::conectar();
        $stmt = $conn->prepare("SELECT id, nome, email, telefone FROM usuarios");
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_all(MYSQLI_ASSOC);
        }
    }

    public static function buscarUserById($id){
        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        $sql = "SELECT nome FROM usuarios 
        WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Pega os resultados
        $result = $stmt->get_result();
        $nome = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $nome;
    }
}
?>