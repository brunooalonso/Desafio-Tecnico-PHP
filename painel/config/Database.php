<?php

namespace Config;

class Database
{
    protected static $connectionPool;

    public function __construct()
    {
        $config = require 'settings.php';

        if (!isset($config['active_connection']) || empty($config['active_connection'])) {
            throw new \Exception('A chave "active_connection" não está definida ou está vazia no arquivo settings.');
        }

        $activeConnection = $config['active_connection'];

        if (!isset($config['databases'][$activeConnection])) {
            throw new \Exception('O banco de dados ativo definido no arquivo de configuração não existe.');
        }

        if (!self::$connectionPool) {
            self::$connectionPool = new ConnectionPool($config['databases'][$activeConnection]);
        }
    }

    public static function connection()
    {
        if (!self::$connectionPool) {
            new Database();
        }
        return self::$connectionPool->getConnection();
    }

    public static function releaseConnection($connection)
    {
        if (self::$connectionPool) {
            self::$connectionPool->releaseConnection($connection);
        }
    }
}
