<?php

namespace Model;

class Database {

    public static function conectar() {
        $host = 'localhost';
        $user = 'root';
        $pass = '';
        $dbname = 'agendamento';

        $conn = new \mysqli($host, $user, $pass, $dbname);
        if ($conn->connect_error) {
            die("Erro de conexão: " . $conn->connect_error);
        }
        $conn->set_charset("utf8");
        return $conn;
    }
}


?>