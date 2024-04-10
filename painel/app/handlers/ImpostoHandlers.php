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

use App\Controllers\ImpostoController;
use App\Library\CsrfLibrary;
use App\Library\ValidationLibrary as Validator;
use Config\Utils;

//Paramentro recebido da view para entrar no switch e case
$param = Validator::secureField('param', 'post', true);

//Verifica se a variavel não esta vazia
if ($param) {

    //Instanciar controller
    $impostoController = new ImpostoController();

    //De acordo com o parametro recebido, realizamos uma ação
    switch ($param) {
        case 'salvar':

            //Validar CSRF
            CsrfLibrary::verifyCsrfToken();

            //Tratar os campos
            $valorPercentualImposto = Utils::formatNumberForInsertion(Validator::secureField('valor_percentual_imposto', 'post', true));

            //Array para validar os campos
            $fields = [
                'valor_percentual_imposto' => ['required', 'fieldName' => 'Valor percentual'],
            ];

            // Função para validar os campos do formulário, e retornar caso tenha alguma divergência
            Validator::validateFields($fields, $_POST);

            //Verifica valor percentual existente
            if ($impostoController->verificarValorPercentualExistente($valorPercentualImposto)) {
                // Já existe, exiba uma mensagem de erro ao usuário
                Utils::jsonResponse(400, 'Atenção!', 'warning', "O valor percentual: <b>$valorPercentualImposto</b> já está cadastrado.");
            }

            //Salvar
            if ($impostoController->salvar($valorPercentualImposto)) {
                Utils::jsonResponse(200, 'Sucesso!', 'success', 'Dados salvos com sucesso!');
            } else {
                Utils::jsonResponse(400, 'Erro!', 'error', 'Ocorreu um erro ao salvar os dados.');
            }
            break;
        case 'alterar':

            //Tratar os campos
            $idImposto = Validator::secureField('id_imposto', 'post', false, true);
            $valorPercentualImposto = Utils::formatNumberForInsertion(Validator::secureField('valor_percentual_imposto', 'post', true));

            //Array para validar os campos
            $fields = [
                'valor_percentual_imposto' => ['required', 'fieldName' => 'Valor percentual'],
            ];

            // Função para validar os campos do formulário, e retornar caso tenha alguma divergência
            Validator::validateFields($fields, $_POST);

            //Verifica valor percentual existente
            if ($impostoController->verificarValorPercentualExistente($valorPercentualImposto, $idImposto)) {
                // Já existe, exiba uma mensagem de erro ao usuário
                Utils::jsonResponse(400, 'Atenção!', 'warning', "O valor percentual: <b>$valorPercentualImposto</b> já está cadastrado.");
            }

            //Alterar
            if ($impostoController->alterar($idImposto, $valorPercentualImposto)) {
                Utils::jsonResponse(200, 'Sucesso!', 'success', 'Dados salvos com sucesso!');
            } else {
                Utils::jsonResponse(400, 'Erro!', 'error', 'Ocorreu um erro ao salvar os dados.');
            }
            break;
        case 'listar':
            // Carregar a listagem
            $resultadoDaConsulta = $impostoController->listar();

              // Personalize os dados antes de enviá-los para o front
              foreach ($resultadoDaConsulta as &$item) {
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
