<?php

//Setamos setting para carregar o autoload
require_once(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "constants.php");

header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Origin: " . BASE_URL);

if (!isset($_POST['param']) && empty($_POST['param'])) {
    echo "Acesso negado!";
    // Caso o parâmetro não tenha o valor esperado, não permita o acesso direto
    http_response_code(403); // Código de acesso proibido
    exit;
}

//Carregamos o autoload da controller
require_once(WWW_VENDOR . 'autoload.php');

use App\Controllers\ProdutoController;
use App\Library\CsrfLibrary;
use App\Library\ValidationLibrary as Validator;
use Config\Utils;

//Paramentro recebido da view para entrar no switch e case
$param = Validator::secureField('param', 'post', true);

//Verifica se a variavel não esta vazia
if ($param) {

    //Instanciar controller
    $produtoController = new ProdutoController();

    //De acordo com o parametro recebido, realizamos uma ação
    switch ($param) {
        case 'salvar':

            //Validar CSRF
            CsrfLibrary::verifyCsrfToken();

            //Tratar os campos
            $idTipo = Validator::secureField('id_tipo', 'post', false, true);
            $nomeProduto = Validator::secureField('nome_produto', 'post', true);
            $valorVendaProduto = Utils::formatNumberForInsertion(Validator::secureField('valor_venda_produto', 'post', true));

            //Array para validar os campos
            $fields = [
                'id_tipo' => ['required', 'numeric', 'fieldName' => 'Tipo produto'],
                'nome_produto' => ['required', 'fieldName' => 'Nome produto'],
                'valor_venda_produto' => ['required', 'fieldName' => 'Preço de venda'],
            ];

            // Função para validar os campos do formulário, e retornar caso tenha alguma divergência
            Validator::validateFields($fields, $_POST);

            //Verifica valor percentual existente
            if ($produtoController->verificarNomeExistente($nomeProduto)) {
                // Já existe, exiba uma mensagem de erro ao usuário
                Utils::jsonResponse(400, 'Atenção!', 'warning', "O produto: <b>$nomeProduto</b> já está cadastrado.");
            }

            //Salvar
            if ($produtoController->salvar($idTipo, $nomeProduto, $valorVendaProduto)) {
                Utils::jsonResponse(200, 'Sucesso!', 'success', 'Dados salvos com sucesso!');
            } else {
                Utils::jsonResponse(400, 'Erro!', 'error', 'Ocorreu um erro ao salvar os dados.');
            }
            break;
        case 'alterar':

            //Tratar os campos
            $idProduto = Validator::secureField('id_produto', 'post', false, true);
            $idTipo = Validator::secureField('id_tipo', 'post', false, true);
            $nomeProduto = Validator::secureField('nome_produto', 'post', true);
            $valorVendaProduto = Utils::formatNumberForInsertion(Validator::secureField('valor_venda_produto', 'post', true));

            //Array para validar os campos
            $fields = [
                'id_produto' => ['required', 'numeric', 'fieldName' => 'Produto'],
                'id_tipo' => ['required', 'numeric', 'fieldName' => 'Tipo produto'],
                'nome_produto' => ['required', 'fieldName' => 'Nome produto'],
                'valor_venda_produto' => ['required', 'fieldName' => 'Preço de venda'],
            ];

            // Função para validar os campos do formulário, e retornar caso tenha alguma divergência
            Validator::validateFields($fields, $_POST);

            //Verifica valor percentual existente
            if ($produtoController->verificarNomeExistente($nomeProduto, $idProduto)) {
                // Já existe, exiba uma mensagem de erro ao usuário
                Utils::jsonResponse(400, 'Atenção!', 'warning', "O produto: <b>$nomeProduto</b> já está cadastrado.");
            }

            //Alterar
            if ($produtoController->alterar($idProduto, $idTipo, $nomeProduto, $valorVendaProduto)) {
                Utils::jsonResponse(200, 'Sucesso!', 'success', 'Dados salvos com sucesso!');
            } else {
                Utils::jsonResponse(400, 'Erro!', 'error', 'Ocorreu um erro ao salvar os dados.');
            }
            break;
        case 'listar':
            // Carregar a listagem
            $resultadoDaConsulta = $produtoController->listar();

            // Personalize os dados antes de enviá-los para o front
            foreach ($resultadoDaConsulta as &$item) {
                $item['valor_venda_produto'] = Utils::numberFormart($item['valor_venda_produto']);
            }

            echo json_encode($resultadoDaConsulta, true);
            break;
        case 'carregarProduto':

            $idTipo = Validator::secureField('id_tipo', 'post', false, true);

            // Carregar a listagem
            $resultadoDaConsulta = $produtoController->carregarProduto($idTipo);

            // Personalize os dados antes de enviá-los para o front
            foreach ($resultadoDaConsulta as &$item) {
                $item['valor_venda_produto'] = Utils::numberFormart($item['valor_venda_produto']);
                $item['valor_percentual_imposto'] = Utils::numberFormart($item['valor_percentual_imposto']);
            }

            echo json_encode($resultadoDaConsulta, true);
            break;
        default:
            $response = ["status" => 400, "message" => "Requisição inválida."];
            echo json_encode($response);
            break;
    }
} else {
    $response = ["status" => 400, "message" => "Requisição inválida."];
    echo json_encode($response);
}
