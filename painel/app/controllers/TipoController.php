<?php

namespace App\Controllers;

use PDO;
use Config\Database;
use Config\Utils;

class TipoController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function salvar($idImposto, $nomeTipo)
    {
        try {
            //Iníciar transação
            $this->db->beginTransaction();

            $sql = "INSERT INTO tipo (id_imposto, nome_tipo) VALUES (:id_imposto, :nome_tipo);";

            $stmt = $this->db->prepare(trim($sql));
            $stmt->bindParam(":id_imposto", $idImposto, Utils::paramType($idImposto));
            $stmt->bindParam(":nome_tipo", $nomeTipo, Utils::paramType($nomeTipo));

            //executa a instrução SQL
            $stmt->execute();
            //Pegamos o ultimo id que foi inserido
            $id = $this->db->lastInsertId();
            //confirma a transação
            $this->db->commit();

            return true;
        } catch (\PDOException $ex) {
            //Se retornou algum erro da um rollback
            $this->db->rollback();

            throw new \Exception("Erro ao salvar TipoController. Erro: " . $ex->getMessage());
            return false;
        } finally {
            Database::releaseConnection($this->db);
        }
    }

    public function alterar($idTipo, $idImposto, $nomeTipo)
    {
        try {
            //Iníciar transação
            $this->db->beginTransaction();

            $sql = "UPDATE tipo SET 
            id_imposto = :id_imposto, 
            nome_tipo = :nome_tipo 
            WHERE id_tipo = :id_tipo;";

            $stmt = $this->db->prepare(trim($sql));
            $stmt->bindParam(":id_imposto", $idImposto, Utils::paramType($idImposto));
            $stmt->bindParam(":nome_tipo", $nomeTipo, Utils::paramType($nomeTipo));
            $stmt->bindParam(":id_tipo", $idTipo, Utils::paramType($idTipo));

            //executa a instrução SQL
            $stmt->execute();
            //confirma a transação
            $this->db->commit();

            return true;
        } catch (\PDOException $ex) {
            //Se retornou algum erro da um rollback
            $this->db->rollback();

            throw new \Exception("Erro ao alterar TipoController. Erro: " . $ex->getMessage());
            return false;
        } finally {
            Database::releaseConnection($this->db);
        }
    }

    public function listar()
    {
        try {

            $sql = "SELECT t.*, i.valor_percentual_imposto
                    FROM tipo t
                        INNER JOIN imposto i ON i.id_imposto = t.id_imposto
                    ORDER BY t.id_tipo DESC;";

            $stmt = $this->db->prepare(trim($sql));
            $stmt->execute();
            return $stmt->fetchall(PDO::FETCH_ASSOC);
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao listar TipoController. Erro: " . $ex->getMessage());
            return false;
        } finally {
            Database::releaseConnection($this->db);
        }
    }

    public function verificarNomeExistente($nome, $idTipo = null)
    {
        try {

            $where = $idTipo ? "AND id_tipo <> :id_tipo" : '';

            $sql = "SELECT COUNT(1) AS count 
                    FROM tipo 
                    WHERE nome_tipo = :nome_tipo $where;";

            $stmt = $this->db->prepare(trim($sql));
            $stmt->bindParam(":nome_tipo", $nome, Utils::paramType($nome));
            if ($idTipo) {
                $stmt->bindParam(":id_tipo", $idTipo, Utils::paramType($idTipo));
            }
            $stmt->execute();
            $count = $stmt->fetchColumn();

            return $count > 0;
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao verificar verificarNomeExistente TipoController. Erro: " . $ex->getMessage());
            return false;
        } finally {
            Database::releaseConnection($this->db);
        }
    }
}