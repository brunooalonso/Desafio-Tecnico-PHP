<?php

namespace App\Controllers;

use PDO;
use Config\Database;
use Config\Utils;

class ProdutoController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function salvar($idTipo, $nomeProduto, $precoVendaProduto)
    {
        try {
            //Iníciar transação
            $this->db->beginTransaction();

            $sql = "INSERT INTO produto (id_tipo, nome_produto, valor_venda_produto) VALUES (:id_tipo, :nome_produto, :valor_venda_produto);";

            $stmt = $this->db->prepare(trim($sql));
            $stmt->bindParam(":id_tipo", $idTipo, Utils::paramType($idTipo));
            $stmt->bindParam(":nome_produto", $nomeProduto, Utils::paramType($nomeProduto));
            $stmt->bindParam(":valor_venda_produto", $precoVendaProduto, Utils::paramType($precoVendaProduto));

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

            throw new \Exception("Erro ao salvar ProdutoController. Erro: " . $ex->getMessage());
            return false;
        } finally {
            Database::releaseConnection($this->db);
        }
    }

    public function alterar($idProduto, $idTipo, $nomeProduto, $precoVendaProduto)
    {
        try {
            //Iníciar transação
            $this->db->beginTransaction();

            $sql = "UPDATE produto SET 
                    id_tipo = :id_tipo,
                    nome_produto = :nome_produto,
                    valor_venda_produto = :valor_venda_produto
                    WHERE id_produto = :id_produto;";

            $stmt = $this->db->prepare(trim($sql));
            $stmt->bindParam(":id_tipo", $idTipo, Utils::paramType($idTipo));
            $stmt->bindParam(":nome_produto", $nomeProduto, Utils::paramType($nomeProduto));
            $stmt->bindParam(":valor_venda_produto", $precoVendaProduto, Utils::paramType($precoVendaProduto));
            $stmt->bindParam(":id_produto", $idProduto, Utils::paramType($idTipo));

            //executa a instrução SQL
            $stmt->execute();
            //confirma a transação
            $this->db->commit();

            return true;
        } catch (\PDOException $ex) {
            //Se retornou algum erro da um rollback
            $this->db->rollback();

            throw new \Exception("Erro ao alterar ProdutoController. Erro: " . $ex->getMessage());
            return false;
        } finally {
            Database::releaseConnection($this->db);
        }
    }

    public function listar()
    {
        try {

            $sql = "SELECT p.*, t.nome_tipo
                    FROM produto p
                        INNER JOIN tipo t ON t.id_tipo = p.id_tipo
                    ORDER BY p.id_produto DESC;";

            $stmt = $this->db->prepare(trim($sql));
            $stmt->execute();
            return $stmt->fetchall(PDO::FETCH_ASSOC);
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao listar ProdutoController. Erro: " . $ex->getMessage());
            return false;
        } finally {
            Database::releaseConnection($this->db);
        }
    }

    public function verificarNomeExistente($nome, $idProduto = null)
    {
        try {

            $where = $idProduto ? "AND id_produto <> :id_produto" : '';

            $sql = "SELECT COUNT(1) AS count 
                    FROM produto 
                    WHERE nome_produto = :nome_produto $where;";

            $stmt = $this->db->prepare(trim($sql));
            $stmt->bindParam(":nome_produto", $nome, Utils::paramType($nome));
            if ($idProduto) {
                $stmt->bindParam(":id_produto", $idProduto, Utils::paramType($idProduto));
            }
            $stmt->execute();
            $count = $stmt->fetchColumn();

            return $count > 0;
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao verificar verificarNomeExistente ProdutoController. Erro: " . $ex->getMessage());
            return false;
        } finally {
            Database::releaseConnection($this->db);
        }
    }

    public function carregarProduto($idTipo)
    {
        try {

            $where = intval($idTipo) > 0 ? "WHERE p.id_tipo = :id_tipo" : "";

            $sql = "SELECT p.id_produto, p.id_tipo, p.nome_produto, p.valor_venda_produto, i.valor_percentual_imposto
                    FROM produto p
                        INNER JOIN tipo t ON t.id_tipo = p.id_tipo
                        INNER JOIN imposto i ON i.id_imposto = t.id_imposto
                    $where
                    ORDER BY p.nome_produto;";

            $stmt = $this->db->prepare(trim($sql));
            if (intval($idTipo) > 0) {
                $stmt->bindParam(":id_tipo", $idTipo, Utils::paramType($idTipo));
            }
            $stmt->execute();
            return $stmt->fetchall(PDO::FETCH_ASSOC);
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao carregarProduto ProdutoController. Erro: " . $ex->getMessage());
            return false;
        } finally {
            Database::releaseConnection($this->db);
        }
    }
}
