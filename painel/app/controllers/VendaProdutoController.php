<?php

namespace App\Controllers;

use PDO;
use Config\Database;
use Config\Utils;

class VendaProdutoController
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = ($db == null) ? Database::connection() : $db;
    }

    public function salvarItemProduto($idVenda, $listItensProdutos)
    {
        try {
            foreach ($listItensProdutos as $item) {
                $idVendaProduto = isset($item['id_venda_produto']) ? $item['id_venda_produto'] : '';
                $idProduto = isset($item['id_produto']) ? $item['id_produto'] : '';
                $quantidade = isset($item['quantidade_venda_produto']) ? $item['quantidade_venda_produto'] : '';
                $valorProduto = isset($item['valor_produto_venda_produto']) ? Utils::formatNumberForInsertion($item['valor_produto_venda_produto']) : '';
                $valorTotalProduto = isset($item['valor_total_produto_venda_produto']) ? Utils::formatNumberForInsertion($item['valor_total_produto_venda_produto']) : '';
                $valorImposto = isset($item['valor_imposto_venda_produto']) ? Utils::formatNumberForInsertion($item['valor_imposto_venda_produto']) : '';
                $valorTotalImposto = isset($item['valor_total_imposto_venda_produto']) ? Utils::formatNumberForInsertion($item['valor_total_imposto_venda_produto']) : '';

                // Se o id_venda_produto estiver vazio, verifica se o produto já está associado à venda
                if (empty($idVendaProduto)) {
                    $idVendaProduto = $this->produtoJaAssociadoAVenda($idVenda, $idProduto);
                }

                if (empty($idVendaProduto)) {
                    // Se não estiver associado, insere um novo item de produto
                    $this->inserirItemProduto($idVenda, $idProduto, $quantidade, $valorProduto, $valorTotalProduto, $valorImposto, $valorTotalImposto);
                } else {
                    // Se já estiver associado, atualiza os dados do item de produto
                    $this->atualizarItemProduto($idVendaProduto, $quantidade, $valorProduto, $valorTotalProduto, $valorImposto, $valorTotalImposto);
                }
            }

            // Remove os produtos da venda que não estão mais na lista de itens
            $this->removerProdutosExcedentes($idVenda, $listItensProdutos);

            return true;
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao salvar VendaProdutoController. Erro: " . $ex->getMessage());
        } finally {
            Database::releaseConnection($this->db);
        }
    }

    private function inserirItemProduto($idVenda, $idProduto, $quantidade, $valorProduto, $valorTotalProduto, $valorImposto, $valorTotalImposto)
    {
        $sql = "INSERT INTO venda_produto (id_venda, id_produto, quantidade_venda_produto, valor_produto_venda_produto, valor_total_produto_venda_produto, valor_imposto_venda_produto, valor_total_imposto_venda_produto) 
            VALUES (:id_venda, :id_produto, :quantidade_venda_produto, :valor_produto_venda_produto, :valor_total_produto_venda_produto, :valor_imposto_venda_produto, :valor_total_imposto_venda_produto);";

        $stmt = $this->db->prepare(trim($sql));
        $stmt->bindParam(":id_venda", $idVenda, Utils::paramType($idVenda));
        $stmt->bindParam(":id_produto", $idProduto, Utils::paramType($idProduto));
        $stmt->bindParam(":quantidade_venda_produto", $quantidade, Utils::paramType($quantidade));
        $stmt->bindParam(":valor_produto_venda_produto", $valorProduto, Utils::paramType($valorProduto));
        $stmt->bindParam(":valor_total_produto_venda_produto", $valorTotalProduto, Utils::paramType($valorTotalProduto));
        $stmt->bindParam(":valor_imposto_venda_produto", $valorImposto, Utils::paramType($valorImposto));
        $stmt->bindParam(":valor_total_imposto_venda_produto", $valorTotalImposto, Utils::paramType($valorTotalImposto));

        return $stmt->execute();
    }

    private function atualizarItemProduto($idVendaProduto, $quantidade, $valorProduto, $valorTotalProduto, $valorImposto, $valorTotalImposto)
    {
        $sql = "UPDATE venda_produto SET 
                quantidade_venda_produto = :quantidade_venda_produto,
                valor_produto_venda_produto = :valor_produto_venda_produto,
                valor_total_produto_venda_produto = :valor_total_produto_venda_produto,
                valor_imposto_venda_produto = :valor_imposto_venda_produto,
                valor_total_imposto_venda_produto = :valor_total_imposto_venda_produto
                WHERE id_venda_produto = :id_venda_produto;";

        $stmt = $this->db->prepare(trim($sql));
        $stmt->bindParam(":quantidade_venda_produto", $quantidade, Utils::paramType($quantidade));
        $stmt->bindParam(":valor_produto_venda_produto", $valorProduto, Utils::paramType($valorProduto));
        $stmt->bindParam(":valor_total_produto_venda_produto", $valorTotalProduto, Utils::paramType($valorTotalProduto));
        $stmt->bindParam(":valor_imposto_venda_produto", $valorImposto, Utils::paramType($valorImposto));
        $stmt->bindParam(":valor_total_imposto_venda_produto", $valorTotalImposto, Utils::paramType($valorTotalImposto));
        $stmt->bindParam(":id_venda_produto", $idVendaProduto, Utils::paramType($idVendaProduto));

        return $stmt->execute();
    }

    private function removerProdutosExcedentes($idVenda, $listItensProdutos)
    {
        $stmt = $this->db->prepare("SELECT id_produto FROM venda_produto WHERE id_venda = :id_venda");
        $stmt->bindParam(":id_venda", $idVenda, Utils::paramType($idVenda));
        $stmt->execute();
        $existentesProduto = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $listIdsProdutos = array_column($listItensProdutos, 'id_produto');

        // Determine quais registros devem ser excluídos
        $toDelete = array_diff($existentesProduto, $listIdsProdutos);

        // Exclua registros não selecionados
        foreach ($toDelete as $idProduto) {
            // Excluir o registro do banco de dados
            $this->deletarItemProduto($idVenda, $idProduto);
        }
    }

    private function deletarItemProduto($idVenda, $idProduto)
    {
        $sql = "DELETE FROM venda_produto
                WHERE id_venda = :id_venda AND id_produto = :id_produto;";

        $stmt = $this->db->prepare(trim($sql));
        $stmt->bindParam(":id_venda", $idVenda, Utils::paramType($idVenda));
        $stmt->bindParam(":id_produto", $idProduto, Utils::paramType($idProduto));

        //executa a instrução SQL
        return $stmt->execute();
    }

    private function produtoJaAssociadoAVenda($idVenda, $idProduto)
    {
        try {
            $stmt = $this->db->prepare("SELECT id_venda_produto FROM venda_produto WHERE id_venda = :id_venda AND id_produto = :id_produto");
            $stmt->bindParam(":id_venda", $idVenda, Utils::paramType($idVenda));
            $stmt->bindParam(":id_produto", $idProduto, Utils::paramType($idProduto));
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id_venda_produto'] : null;
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao verificar se o produto já está associado à venda. Erro: " . $ex->getMessage());
        }
    }

    public function listarVendaProduto($idVenda)
    {
        try {

            $sql = "SELECT vp.*, p.nome_produto, p.id_tipo  
                    FROM venda_produto vp
                        INNER JOIN venda v ON v.id_venda = vp.id_venda
                        INNER JOIN produto p ON p.id_produto = vp.id_produto
                    WHERE vp.id_venda = :id_venda 
                    ORDER BY vp.id_venda_produto DESC;";

            $stmt = $this->db->prepare(trim($sql));
            $stmt->bindParam(":id_venda", $idVenda, Utils::paramType($idVenda));
            $stmt->execute();
            return $stmt->fetchall(PDO::FETCH_ASSOC);
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao listarVendaProduto VendaProdutoController. Erro: " . $ex->getMessage());
            return false;
        } finally {
            Database::releaseConnection($this->db);
        }
    }
}
