<?php

namespace Model;
use Validators\AgendamentoValidators;

class Anamnese{
    public static function cadastro($dados){
        $nome = $dados["nome"];
        $data_nascimento = $dados["data_nascimento"];
        $telefone = $dados["telefone"];
        $email = $dados["email"];
        $sexo = $dados["sexo"];
        $profissao = $dados["profissao"];
        $endereco = $dados["endereco"];
        $condicoes = isset($_POST['condicoes']) ? implode(',', $_POST['condicoes']) : null;
        $alergias = $dados["alergias"];
        $medicamentos = $dados["medicamentos"];
        $cirurgias = $dados["cirurgias"];
        $marcapasso = $dados["marcapasso"];
        $gestante = $dados["gestante"];
        $queixa = $dados["queixa"];
        $podologico = isset($_POST['podologico']) ? implode(',', $_POST['podologico']) : null;
        $calcados = $dados["calcados"];
        $higiene = $dados["higiene"];
        $exame = $dados["exame"];
        $conduta = $dados["conduta"];
        $obs = $dados["obs"];
        $profissional = $dados["profissional"];
        $data = $dados["data"];

        $conn = Database::conectar();

        $stmt = $conn->prepare("INSERT INTO anamnese (`nome`, `data_nascimento`, `telefone`, `email`, `sexo`, `profissao`, `endereco`, `condicoes`, `alergias`, `medicamentos`, `cirurgias`, `marcapasso`, `gestante`, `queixa`, `podologico`, `calcados`, `higiene`, `exame`,`conduta`, `obs`, `profissional`, `data`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssssssssssssss", $nome, $data_nascimento, $telefone, $email, $sexo, $profissao, $endereco, $condicoes, $alergias, $medicamentos, $cirurgias, $marcapasso, $gestante, $queixa, $podologico, $calcados, $higiene, $exame, $conduta, $obs, $profissional, $data);
        
        return $stmt->execute();
    }

    public static function buscarAnamnese(){
        $conn = Database::conectar();
        $stmt = $conn->prepare("SELECT * FROM anamnese");
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_all(MYSQLI_ASSOC);
        }
    }

    public static function atualizar($dados){
        $id = $dados["id"];
        $nome = $dados["nome"];
        $data_nascimento = $dados["data_nascimento"];
        $telefone = $dados["telefone"];
        $email = $dados["email"];
        $sexo = $dados["sexo"];
        $profissao = $dados["profissao"];
        $endereco = $dados["endereco"];
        $condicoes = $dados["condicoes"];
        $alergias = $dados["alergias"];
        $medicamentos = $dados["medicamentos"];
        $cirurgias = $dados["cirurgias"];
        $marcapasso = $dados["marcapasso"];
        $gestante = $dados["gestante"];
        $queixa = $dados["queixa"];
        $podologico = $dados["podologico"];
        $calcados = $dados["calcados"];
        $higiene = $dados["higiene"];
        $exame = $dados["exame"];
        $conduta = $dados["conduta"];
        $obs = $dados["obs"];
        $profissional = $dados["profissional"];
        $data = $dados["data"];

        $conn = Database::conectar();

        if (!$conn) {
            return AgendamentoValidators::formatarErro("Erro na conexão com o banco de dados.");
        }

        // Prepara e executa a atualização
        $sql = "UPDATE anamnese SET nome = ?, data_nascimento = ?, telefone = ?, email = ?, sexo = ?, profissao = ?, endereco = ?, condicoes = ?, alergias = ?, medicamentos = ?, cirurgias = ?, marcapasso = ?, gestante = ?, queixa = ?, podologico = ?, calcados = ?, higiene = ?, exame = ?, conduta = ?, obs = ?, profissional = ?, data = ? WHERE id= ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssssssssssssssi", $nome, $data_nascimento, $telefone, $email, $sexo, $profissao, $endereco, $condicoes, $alergias, $medicamentos, $cirurgias, $marcapasso, $gestante, $queixa, $podologico, $calcados, $higiene, $exame, $conduta, $obs, $profissional, $data, $id);


        if ($stmt->execute()) {
            return AgendamentoValidators::formatarRetorno("Ficha atualizada com sucesso!", null);
        }

        $stmt->close();
        $conn->close();
    }
}


?>