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

use App\Controllers\VendaController;
use App\Controllers\VendaProdutoController;
use App\Library\CsrfLibrary;
use App\Library\ValidationLibrary as Validator;
use Config\Utils;

//Paramentro recebido da view para entrar no switch e case
$param = Validator::secureField('param', 'post', true);

//Verifica se a variavel não esta vazia
if ($param) {

    //Instanciar controller
    $vendaController = new VendaController();

    //De acordo com o parametro recebido, realizamos uma ação
    switch ($param) {
        case 'salvar':

            //Validar CSRF
            CsrfLibrary::verifyCsrfToken();

            //Tratar os campos
            $listItensProdutos = $_POST['listItensProdutos'];
            $totalQuantidade = Validator::secureField('totalQuantidade', 'post', false, true);
            $totalValor = Utils::formatNumberForInsertion(Validator::secureField('totalValor', 'post', true));
            $totalImposto = Utils::formatNumberForInsertion(Validator::secureField('totalImposto', 'post', true));
            $dataVenda = Utils::getDateTimeEua();

            //Salvar
            if ($vendaController->salvar($listItensProdutos, $totalQuantidade, $totalValor, $totalImposto, $dataVenda)) {
                Utils::jsonResponse(200, 'Sucesso!', 'success', 'Dados salvos com sucesso!');
            } else {
                Utils::jsonResponse(400, 'Erro!', 'error', 'Ocorreu um erro ao salvar os dados.');
            }
            break;
        case 'alterar':

            //Tratar os campos
            $idVenda = Validator::secureField('id_venda', 'post', false, true);
            $listItensProdutos = $_POST['listItensProdutos'];
            $totalQuantidade = Validator::secureField('totalQuantidade', 'post', false, true);
            $totalValor = Utils::formatNumberForInsertion(Validator::secureField('totalValor', 'post', true));
            $totalImposto = Utils::formatNumberForInsertion(Validator::secureField('totalImposto', 'post', true));

            //Alterar
            if ($vendaController->alterar($idVenda, $listItensProdutos, $totalQuantidade, $totalValor, $totalImposto)) {
                Utils::jsonResponse(200, 'Sucesso!', 'success', 'Dados salvos com sucesso!');
            } else {
                Utils::jsonResponse(400, 'Erro!', 'error', 'Ocorreu um erro ao salvar os dados.');
            }
            break;
        case 'listar':
            // Carregar a listagem
            $resultadoDaConsulta = $vendaController->listar();

            // Personalize os dados antes de enviá-los para o front
            foreach ($resultadoDaConsulta as &$item) {
                $item['valor_total_venda'] = Utils::numberFormart($item['valor_total_venda']);
                $item['valor_total_imposto_venda'] = Utils::numberFormart($item['valor_total_imposto_venda']);
                $item['data_venda'] = Utils::euaToBrDateTime($item['data_venda']);
            }

            echo json_encode($resultadoDaConsulta, true);
            break;
        case 'listarVendaProduto':
            //Instanciar controller
            $vendaProdutoController = new VendaProdutoController();

            $idVenda = Validator::secureField('id_venda', 'post', false, true);

            // Carregar a listagem
            $resultadoDaConsulta = $vendaProdutoController->listarVendaProduto($idVenda);

            // Personalize os dados antes de enviá-los para o front
            foreach ($resultadoDaConsulta as &$item) {
                $item['valor_produto_venda_produto'] = Utils::numberFormart($item['valor_produto_venda_produto']);
                $item['valor_total_produto_venda_produto'] = Utils::numberFormart($item['valor_total_produto_venda_produto']);
                $item['valor_imposto_venda_produto'] = Utils::numberFormart($item['valor_imposto_venda_produto']);
                $item['valor_total_imposto_venda_produto'] = Utils::numberFormart($item['valor_total_imposto_venda_produto']);
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
