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

    public static function getAnamnese() {
        // Pega todas as fichas
        $fichas = Anamnese::buscarAnamnese();

        // Verifica se retornou registros
        if (!empty($fichas) && is_array($fichas)) {
            foreach ($fichas as &$ficha) {
                // Formata data de nascimento
                if (!empty($ficha['data_nascimento'])) {
                    $dataNascimento = new \DateTime($ficha['data_nascimento']);
                    $ficha['data_nascimento'] = $dataNascimento->format("d/m/Y");
                }

                // Formata data atual
                if (!empty($ficha['data'])) {
                    $dataAtual = new \DateTime($ficha['data']);
                    $ficha['data'] = $dataAtual->format("d/m/Y");
                }
            }
            unset($ficha); // boa prática
        }

        return [
            "status" => true,
            "message" => "Fichas encontradas",
            "data" => $fichas
        ];
    }



    public static function atualizarAnamnese($dados){
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

        $atualizar = Anamnese::atualizar($dados);

        if($atualizar){
            return $atualizar;
        }else{
            return ["status" => false, "message" => "Erro ao atualizar ficha!"];
        }
    }

}




?>