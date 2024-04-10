<?php

namespace App\Library;

use Config\Utils;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class CsrfLibrary
{
    // Função para gerar um token CSRF
    public static function generateCsrfToken()
    {
        // Regenerar o token CSRF a cada solicitação
        $csrfToken = bin2hex(random_bytes(32));

        $_SESSION['csrf_token'] = $csrfToken;

        return $csrfToken;
    }

    // Função para verificar o token CSRF
    public static function verifyCsrfToken()
    {
        $token = isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : '';
        // Verifica se o token CSRF não está vazio e se está definido na sessão
        if (empty($token) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            Utils::jsonResponse(400, 'Atenção!', 'warning', 'Acesso negado devido a erro no token.'.$token);
        }
    }
}
