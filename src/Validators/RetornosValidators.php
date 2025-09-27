<?php
namespace Validators;

class Retornos{
    public static function sucesso($messagem){
        return ["status" => true, "message" => $messagem];
    }

    public static function erro($messagem){
        return ["status" => false, "message" => $messagem];
    }
}

?>