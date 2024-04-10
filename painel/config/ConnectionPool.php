<?php

namespace Config;

use PDO;

class ConnectionPool
{
    private $connections = [];
    private $config;
    private $maxConnections;

    public function __construct($config, $maxConnections = 10)
    {
        $this->config = $config;
        $this->maxConnections = $maxConnections;
    }

    public function getConnection()
    {
        // Se o pool já tiver conexões disponíveis, reutilize uma delas
        if (!empty($this->connections)) {
            return array_pop($this->connections);
        }

        // Se o pool ainda não atingiu o limite máximo de conexões, crie uma nova
        if (count($this->connections) < $this->maxConnections) {
            $connection = $this->createConnection();
            return $connection;
        }

        // Se o pool estiver cheio, aguarde até que uma conexão esteja disponível
        while (empty($this->connections)) {
            usleep(1000); // Espere 1 milissegundo (ou qualquer outro valor apropriado)
        }

        return array_pop($this->connections);
    }

    public function releaseConnection($connection)
    {
        $this->connections[] = $connection;
    }

    private function createConnection()
    {

        // Verifica se todas as configurações necessárias estão presentes
        if (!isset($this->config['driver'], $this->config['host'], $this->config['port'], $this->config['username'], $this->config['password'], $this->config['dbname'], $this->config['charset'])) {
            throw new \Exception("Configuração de banco de dados incompleta. Verifique se todas as configurações necessárias estão presentes.");
        }

        // Verifica se todas as configurações necessárias estão presentes e não estão vazias
        if (empty($this->config['driver']) || empty($this->config['host']) || empty($this->config['port']) || empty($this->config['username']) || empty($this->config['dbname'])) {
            throw new \Exception("Configuração de banco de dados incompleta. Verifique se todas as configurações necessárias estão presentes e não estão vazias.");
        }

        $DB_driver = trim($this->config['driver']);
        $DB_host = trim($this->config['host']);
        $DB_port = trim($this->config['port']);
        $DB_user = trim($this->config['username']);
        $DB_pass = trim($this->config['password']);
        $DB_name = trim($this->config['dbname']);
        $DB_charset = trim($this->config['charset']);

        $DB_schema = '';
        // Se o tipo de conexão for PostgreSQL e um schema estiver definido
        if ($DB_driver === 'pgsql' && isset($this->config['schema']) && !empty($this->config['schema'])) {
            // Obtenha o nome do schema da configuração
            $DB_schema = ";options='--search_path=" . trim($this->config['schema']) . "'";
        }

        try {
            $connection = new PDO("{$DB_driver}:host={$DB_host};port={$DB_port};dbname={$DB_name}{$DB_schema}", $DB_user, $DB_pass);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            // Verifica se o charset está definido e não está vazio antes de executar o comando SET NAMES
            if (!empty($DB_charset)) {
                $connection->exec("SET NAMES '{$DB_charset}'");
            }
            return $connection;
        } catch (\PDOException $ex) {
            throw new \Exception("Erro de conexão com o banco de dados: " . $ex->getMessage());
            exit();
        }
    }
}
