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

use App\Library\CsrfLibrary;

//Paramentro recebido da view para entrar no switch e case
$param = strip_tags(trim(filter_input(INPUT_POST, 'param', FILTER_DEFAULT)));

//Verifica se a variavel não esta vazia
if ($param) {

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    //De acordo com o parametro recebido, realizamos uma ação
    switch ($param) {
        case 'getCsrfToken':
            echo json_encode(['csrf_token' => CsrfLibrary::generateCsrfToken()]);
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
