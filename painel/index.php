<?php

ob_start("ob_gzhandler");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
header("X-XSS-Protection: 1; mode=block");
header("Accept-Encoding: gzip, compress, br");
header("X-Frame-Options: DENY");

require_once(__DIR__ . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "constants.php");

//Carregar o autoload
require_once(WWW_VENDOR . 'autoload.php');
//Carregar o que faz a url amigavel
require_once(WWW_CONFIGURATION . "router.php");

require_once(WWW_VIEW . 'header.php');
require_once(WWW_VIEW . 'menu.php');
require_once(WWW_VIEW . $namePage . '.php');
require_once(WWW_VIEW . 'footer.php');

ob_end_flush();
exit();
