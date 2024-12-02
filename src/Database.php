<?php

class Database
{
    private static $pdo;

    public static function connect()
    {
        if (!self::$pdo) {
            $config = include(__DIR__ . '/../config.php');
            self::$pdo = new PDO($config['db']['dsn']);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$pdo;
    }
}
