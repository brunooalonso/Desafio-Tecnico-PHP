<?php

namespace App\Controllers;

use PDO;
use Config\Database;
use Config\Utils;

class VendaController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function salvar($listItensProdutos, $totalQuantidade, $totalValor, $totalImposto, $dataVenda)
    {
        try {
            //Iníciar transação
            $this->db->beginTransaction();

            $sql = "INSERT INTO venda (quantidade_total_venda, valor_total_venda, valor_total_imposto_venda, data_venda) 
                    VALUES (:quantidade_total_venda, :valor_total_venda, :valor_total_imposto_venda, :data_venda);";

            $stmt = $this->db->prepare(trim($sql));
            $stmt->bindParam(":quantidade_total_venda", $totalQuantidade, Utils::paramType($totalQuantidade));
            $stmt->bindParam(":valor_total_venda", $totalValor, Utils::paramType($totalValor));
            $stmt->bindParam(":valor_total_imposto_venda", $totalImposto, Utils::paramType($totalImposto));
            $stmt->bindParam(":data_venda", $dataVenda, Utils::paramType($dataVenda));

            //executa a instrução SQL
            $v = $stmt->execute();
            $vp = false;
            //Pegamos o ultimo id que foi inserido
            $idVenda = $this->db->lastInsertId();

            // Salvar itens do produto
            if ($idVenda > 0 && $listItensProdutos) {
                $vendaProdutoController = new VendaProdutoController($this->db);
                $vp = $vendaProdutoController->salvarItemProduto($idVenda, $listItensProdutos);

                if (!$vp) {
                    throw new \Exception("Erro ao salvar venda produto.");
                }
            }

            if ($v && $vp) {
                //confirma a transação
                $this->db->commit();
                return true;
            } else {
                //Se retornou algum erro da um rollback
                $this->db->rollback();
                return false;
            }
        } catch (\PDOException $ex) {
            //Se retornou algum erro da um rollback
            $this->db->rollback();

            throw new \Exception("Erro ao salvar VendaController. Erro: " . $ex->getMessage());
            return false;
        } finally {
            Database::releaseConnection($this->db);
        }
    }

    public function alterar($idVenda, $listItensProdutos, $totalQuantidade, $totalValor, $totalImposto)
    {
        try {
            //Iníciar transação
            $this->db->beginTransaction();

            $sql = "UPDATE venda SET 
                    quantidade_total_venda = :quantidade_total_venda, 
                    valor_total_venda = :valor_total_venda,
                    valor_total_imposto_venda = :valor_total_imposto_venda
                    WHERE id_venda = :id_venda;";

            $stmt = $this->db->prepare(trim($sql));
            $stmt->bindParam(":quantidade_total_venda", $totalQuantidade, Utils::paramType($totalQuantidade));
            $stmt->bindParam(":valor_total_venda", $totalValor, Utils::paramType($totalValor));
            $stmt->bindParam(":valor_total_imposto_venda", $totalImposto, Utils::paramType($totalImposto));
            $stmt->bindParam(":id_venda", $idVenda, Utils::paramType($idVenda));

            //executa a instrução SQL
            $v = $stmt->execute();
            $vp = false;

            // Salvar itens do produto
            if ($idVenda > 0 && $listItensProdutos) {
                $vendaProdutoController = new VendaProdutoController($this->db);
                $vp = $vendaProdutoController->salvarItemProduto($idVenda, $listItensProdutos);

                if (!$vp) {
                    throw new \Exception("Erro ao salvar venda produto.");
                }
            }

            if ($v && $vp) {
                //confirma a transação
                $this->db->commit();
                return true;
            } else {
                //Se retornou algum erro da um rollback
                $this->db->rollback();
                return false;
            }
        } catch (\PDOException $ex) {
            //Se retornou algum erro da um rollback
            $this->db->rollback();

            throw new \Exception("Erro ao alterar VendaController. Erro: " . $ex->getMessage());
            return false;
        } finally {
            Database::releaseConnection($this->db);
        }
    }

    public function listar()
    {
        try {

            $sql = "SELECT id_venda, quantidade_total_venda, valor_total_venda, valor_total_imposto_venda, data_venda
                    FROM venda
                    ORDER BY id_venda DESC;";

            $stmt = $this->db->prepare(trim($sql));
            $stmt->execute();
            return $stmt->fetchall(PDO::FETCH_ASSOC);
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao listar VendaController. Erro: " . $ex->getMessage());
            return false;
        } finally {
            Database::releaseConnection($this->db);
        }
    }
}
