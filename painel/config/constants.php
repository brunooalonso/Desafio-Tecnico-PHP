<?php

define("WWW_CMS", dirname(__DIR__)); // Pasta dentro do CMS
define("DS", DIRECTORY_SEPARATOR); // Separador de Diretório

define("WWW_ROOT", dirname(WWW_CMS)); // Pasta raiz fora do CMS
define("WWW_CONFIGURATION", WWW_CMS . DS . "config" . DS); // Pasta configuration
define("WWW_CONTROLLER", WWW_CMS . DS . "app" . DS . "controllers" . DS); // Pasta Controller
define("WWW_VIEW", WWW_CMS . DS . "app" . DS . "views" . DS); // Pasta View
define("WWW_VENDOR", WWW_CMS . DS . "vendor" . DS); // Pasta vendor

//Montamos a base url do site
define('BASE_URL', (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']));