<?php

namespace App\Controllers;

use PDO;
use Config\Database;
use Config\Utils;

class ImpostoController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function salvar($valorPercentualImposto)
    {
        try {
            //Iníciar transação
            $this->db->beginTransaction();

            $sql = "INSERT INTO imposto (valor_percentual_imposto) VALUES (:valor_percentual_imposto);";

            $stmt = $this->db->prepare(trim($sql));
            $stmt->bindParam(":valor_percentual_imposto", $valorPercentualImposto, Utils::paramType($valorPercentualImposto));

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

            throw new \Exception("Erro ao salvar ImpostosController. Erro: " . $ex->getMessage());
            return false;
        } finally {
            Database::releaseConnection($this->db);
        }
    }

    public function alterar($idImposto, $valorPercentualImposto)
    {
        try {
            //Iníciar transação
            $this->db->beginTransaction();

            $sql = "UPDATE imposto SET valor_percentual_imposto = :valor_percentual_imposto WHERE id_imposto = :id_imposto;";

            $stmt = $this->db->prepare(trim($sql));
            $stmt->bindParam(":valor_percentual_imposto", $valorPercentualImposto, Utils::paramType($valorPercentualImposto));
            $stmt->bindParam(":id_imposto", $idImposto, Utils::paramType($idImposto));

            //executa a instrução SQL
            $stmt->execute();
            //confirma a transação
            $this->db->commit();

            return true;
        } catch (\PDOException $ex) {
            //Se retornou algum erro da um rollback
            $this->db->rollback();

            throw new \Exception("Erro ao alterar ImpostosController. Erro: " . $ex->getMessage());
            return false;
        } finally {
            Database::releaseConnection($this->db);
        }
    }

    public function listar()
    {
        try {
            $stmt = $this->db->prepare(trim("SELECT * FROM imposto ORDER BY id_imposto DESC;"));
            $stmt->execute();
            return $stmt->fetchall(PDO::FETCH_ASSOC);
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao listar ImpostosController. Erro: " . $ex->getMessage());
            return false;
        } finally {
            Database::releaseConnection($this->db);
        }
    }

    public function verificarValorPercentualExistente($valorPercentualImposto, $idImposto = null)
    {
        try {

            $where = $idImposto ? "AND id_imposto <> :id_imposto" : '';

            $sql = "SELECT COUNT(1) AS count 
                    FROM imposto 
                    WHERE valor_percentual_imposto = :valor_percentual_imposto $where;";

            $stmt = $this->db->prepare(trim($sql));
            $stmt->bindParam(":valor_percentual_imposto", $valorPercentualImposto, Utils::paramType($valorPercentualImposto));
            if ($idImposto) {
                $stmt->bindParam(":id_imposto", $idImposto, Utils::paramType($idImposto));
            }
            $stmt->execute();
            $count = $stmt->fetchColumn();

            return $count > 0;
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao verificar Valor Percentual Existente ImpostosController. Erro: " . $ex->getMessage());
            return false;
        } finally {
            Database::releaseConnection($this->db);
        }
    }
}