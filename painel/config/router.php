<?php

// Obtém o parâmetro da página da query string, ou define como "home" se não estiver presente
$pageParameter = isset($_GET["parameter"]) ? strip_tags(trim(filter_input(INPUT_GET, "parameter", FILTER_DEFAULT))) : "home";

// Lista de páginas que não podem ser chamadas na área de conteúdo
$pagesDenied = array("header", "footer");

// Verifica a quantidade de barras no parâmetro da página
$slashesParameter = substr_count($pageParameter, "/");

// Separa o parâmetro da página em um array usando a barra como delimitador
$pageParts = explode("/", $pageParameter);

// Verifica se a página solicitada existe e não está na lista de páginas negadas
function pageExists($page, $pagesDenied)
{
    return file_exists(WWW_VIEW . "$page.php") && !in_array($page, $pagesDenied);
}

// Define o nome da página com base nos argumentos do parâmetro
if ($slashesParameter > 0) {
    if (pageExists($pageParts[0], $pagesDenied)) {
        $namePage = $pageParts[0];
       
    } elseif (pageExists($pageParts[0] . "/" . $pageParts[1], $pagesDenied)) {
        $namePage = $pageParts[0] . "/" . $pageParts[1];
       
    } else {
        $namePage = "home";
    }
} else {
    if (pageExists($pageParameter, $pagesDenied)) {
        $namePage = $pageParameter;
        
    } else {
        $namePage = "home";
    }
}