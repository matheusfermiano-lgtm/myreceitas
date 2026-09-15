<?php
define('DB_HOST', 'tini.click'); 
define('DB_USER', 'my_receitas'); 
define('DB_PASS', '289cccb49646af8507be477db28d9867'); 
define('DB_NAME', 'my_receitas');

class database {
    private static $conexao = null;

    public static function getConexao() {
        if (self::$conexao === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                
                self::$conexao = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die("Erro de conexão: " . $e->getMessage());
            }
        }
        return self::$conexao;
    }
}

/*
define('DB_HOST', 'tini.click');
define('DB_USER', 'my_receitas');
define('DB_PASS', '289cccb49646af8507be477db28d9867');
define('DB_NAME', 'myreceitas_db');
*/
