<?php

namespace Controller;
use Model\Anamnese;

class AnamneseController{

    public static function cadastrar(array $dados){
        $obrigatorios = ["nome", "data_nascimento", "telefone", "email", "sexo", "endereco", "queixa"];

        // Verifica obrigatórios
        foreach ($obrigatorios as $campo) {
            if (empty($dados[$campo])) {
                return [
                    "status" => false,
                    "message" => "O campo {$campo} é obrigatório!"
                ];
            }
        }

        // Defina os opcionais
        $opcionais = [
            "profissao", "condicoes", "alergias", "medicamentos", "cirurgias",
            "marcapasso", "gestante", "podologico", "calcados", "higiene",
            "exame", "conduta", "obs", "profissional", "data"
        ];

        // Se vier vazio, seta como null
        foreach ($opcionais as $campo) {
            if (empty($dados[$campo])) {
                $dados[$campo] = null;
            }
        }

       $res = Anamnese::cadastro($dados);

       if($res){
            return ["status" => true, "message" => "Ficha de anamnese cadastrada com sucesso!"];
       }else{
        return ["status" => false, "message" => "Erro ao salvar dados!"];
       }
    }

}




?>