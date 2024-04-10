<?php

namespace App\Library;

use Config\Utils;
use DateTime;
use InvalidArgumentException;

class ValidationLibrary
{
    public static function validarCPF($cpf)
    {
        // Extrai apenas os números
        $cpf = preg_replace('/[^0-9]/is', '', $cpf);

        // Verifica se o CPF tem 11 caracteres
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se nenhum dígito foi repetido
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Validação do primeiro dígito verificador
        for ($i = 9, $j = 0, $soma = 0; $i > 0; $i--, $j++) {
            $soma += $cpf[$j] * $i;
        }
        $resto = $soma % 11;
        $dv1 = $resto < 2 ? 0 : 11 - $resto;

        // Validação do segundo dígito verificador
        for ($i = 10, $j = 0, $soma = 0; $i > 1; $i--, $j++) {
            $soma += $cpf[$j] * $i;
        }
        $soma += $dv1 * 2;
        $resto = $soma % 11;
        $dv2 = $resto < 2 ? 0 : 11 - $resto;

        // Verifica se os dígitos verificadores estão corretos
        if ($cpf[9] != $dv1 || $cpf[10] != $dv2) {
            return false;
        }

        return true;
    }

    public static function validarCNPJ($cnpj)
    {
        // Extrai apenas os números
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);

        // Verifica se o CNPJ tem 14 caracteres
        if (strlen($cnpj) != 14) {
            return false;
        }

        // Validação do primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        $dv1 = $resto < 2 ? 0 : 11 - $resto;

        // Validação do segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        $dv2 = $resto < 2 ? 0 : 11 - $resto;

        // Verifica se os dígitos verificadores estão corretos
        if ($cnpj[12] != $dv1 || $cnpj[13] != $dv2) {
            return false;
        }

        return true;
    }

    public static function validateFields($fields, $data)
    {
        $errors = '';

        foreach ($fields as $fieldName => $rules) {

            // Obtenha o nome do campo personalizado, se definido
            $fieldNameDisplay = isset($rules['fieldName']) ? $rules['fieldName'] : $fieldName;

            foreach ($rules as $rule) {
                if ($rule === 'fieldName') {
                    continue;
                } // Ignorar a regra 'fieldName'

                $parts = explode(':', $rule);
                $ruleName = $parts[0];
                $ruleValue = isset($parts[1]) ? $parts[1] : null;

                switch (mb_strtolower($ruleName)) {
                    case 'required':
                        if (empty($data[$fieldName])) {
                            $errors .= "O campo <b>{$fieldNameDisplay}</b> é obrigatório.<br>";
                        }
                        break;
                    case 'numeric':
                        if (!empty($data[$fieldName]) && !is_numeric($data[$fieldName])) {
                            $errors .= "O campo <b>{$fieldNameDisplay}</b> deve ser numérico.<br>";
                        }
                        break;
                    case 'date':
                        // Verificar se o campo não está vazio antes de tentar validar a data
                        if (!empty($data[$fieldName])) {
                            // Tente criar um objeto DateTime a partir do formato esperado 'Y-m-d'
                            $dateTime = DateTime::createFromFormat('Y-m-d', $data[$fieldName]);

                            // Verificar se a data foi criada corretamente e se corresponde à data original
                            if (!$dateTime || $dateTime->format('Y-m-d') !== $data[$fieldName]) {
                                // Adicionar mensagem de erro se a data for inválida
                                $errors .= "O campo <b>{$fieldNameDisplay}</b> deve conter uma data válida.<br>";
                            }
                        }
                        break;
                    case 'email':
                        if (!empty($data[$fieldName]) && !filter_var($data[$fieldName], FILTER_VALIDATE_EMAIL)) {
                            $errors .= "O campo <b>{$fieldNameDisplay}</b> deve ser um endereço de e-mail válido.<br>";
                        }
                        break;
                    case 'url':
                        if (!empty($data[$fieldName]) && !filter_var($data[$fieldName], FILTER_VALIDATE_URL)) {
                            $errors .= "O campo <b>{$fieldNameDisplay}</b> deve ser um endereço de URL válida.<br>";
                        }
                        break;
                    case 'min': //Ex: min:5
                        $minLength = $ruleValue;
                        if (!empty($data[$fieldName]) && mb_strlen($data[$fieldName]) < intval($minLength)) {
                            $errors .= "O campo <b>{$fieldNameDisplay}</b> deve ter no mínimo {$minLength} caracteres.<br>";
                        }
                        break;
                    case 'max': //Ex: max:255
                        $maxLength = $ruleValue;
                        if (!empty($data[$fieldName]) && mb_strlen($data[$fieldName]) > intval($maxLength)) {
                            $errors .= "O campo <b>{$fieldNameDisplay}</b> deve ter no máximo {$maxLength} caracteres.<br>";
                        }
                        break;
                    case 'exact': // Regra para verificar se um valor é igual ao outro. Ex: 'different:11' ou 'different:tiaum'
                        if (!empty($data[$fieldName])) {
                            $exactLength = $ruleValue;
                            if (is_numeric($exactLength)) {
                                // Se for numérico, verifica o comprimento
                                if (mb_strlen($data[$fieldName]) == intval($exactLength)) {
                                    $errors .= "O campo <b>{$fieldNameDisplay}</b> deve ter exatamente igual a {$exactLength} caracteres.<br>";
                                }
                            } else {
                                // Se for uma string, verifica o comprimento
                                if ($data[$fieldName] == $exactLength) {
                                    $errors .= "O campo <b>{$fieldNameDisplay}</b> deve ser exatamente igual a {$exactLength}.<br>";
                                }
                            }
                        }
                        break;
                    case 'exact_length': // Regra para verificar se o comprimento do valor do campo é exatamente igual ao parâmetro passado. Ex: 'exact_length:11'
                        if (!empty($data[$fieldName]) && mb_strlen($data[$fieldName]) !== intval($ruleValue)) {
                            $errors .= "O campo <b>{$fieldNameDisplay}</b> deve ter exatamente <b>{$ruleValue} caracteres</b>.<br>";
                        }
                        break;
                    case 'different': // Regra para verificar se um valor é diferente de outro. Ex: 'different:11' ou 'different:tiaum'
                        if (!empty($data[$fieldName])) {
                            if (is_numeric($ruleValue)) {
                                // Se for numérico, verifica o comprimento
                                if (mb_strlen($data[$fieldName]) != intval($ruleValue)) {
                                    $errors .= "O campo <b>{$fieldNameDisplay}</b> deve ser diferente de {$ruleValue}.<br>";
                                }
                            } else {
                                // Se for uma string, verifica o comprimento
                                if ($data[$fieldName] != $ruleValue) {
                                    $errors .= "O campo <b>{$fieldNameDisplay}</b> deve ser diferente de {$ruleValue}.<br>";
                                }
                            }
                        }
                        break;
                    case 'less': //Regra para verificar se um valor é menor a um determinado valor. Ex: 'less:11'
                        if (!empty($data[$fieldName]) && mb_strlen($data[$fieldName]) < intval($ruleValue)) {
                            $errors .= "O campo <b>{$fieldNameDisplay}</b> deve ser menor do que {$ruleValue}.<br>";
                        }
                        break;
                    case 'greater': //Regra para verificar se um valor é maior a um determinado valor.Ex: 'greater:11'
                        if (!empty($data[$fieldName]) && mb_strlen($data[$fieldName]) > intval($ruleValue)) {
                            $errors .= "O campo <b>{$fieldNameDisplay}</b> deve ser maior do que {$ruleValue}.<br>";
                        }
                        break;
                    case 'less_than_or_equal': //Regra para verificar se um valor é menor ou igual a um determinado valor. Ex: 'less_than_or_equal:11'
                        if (!empty($data[$fieldName]) && mb_strlen($data[$fieldName]) <= intval($ruleValue)) {
                            $errors .= "O campo <b>{$fieldNameDisplay}</b> deve ser menor ou igual do que {$ruleValue}.<br>";
                        }
                        break;
                    case 'greater_than_or_equal': //Regra para verificar se um valor é maior ou igual a um determinado valor. Ex: 'greater_than_or_equal:11'
                        if (!empty($data[$fieldName]) && mb_strlen($data[$fieldName]) >= intval($ruleValue)) {
                            $errors .= "O campo <b>{$fieldNameDisplay}</b> deve ser maior ou igual do que {$ruleValue}.<br>";
                        }
                        break;
                    case 'cpf':
                        if (!empty($data[$fieldName]) && !ValidationLibrary::validarCPF($data[$fieldName])) {
                            $errors .= "O <b>CPF</b> informado parece ser inválido. Por favor, verifique o número e tente novamente.<br>";
                        }
                        break;
                    case 'cnpj':
                        if (!empty($data[$fieldName]) && !ValidationLibrary::validarCNPJ($data[$fieldName])) {
                            $errors .= "O <b>CNPJ</b> informado parece ser inválido. Por favor, verifique o número e tente novamente.<br>";
                        }
                        break;
                        // Adicione mais casos para outras regras de validação, se necessário
                }
            }
        }

        return !empty($errors) ? Utils::jsonResponse(400, 'Atenção!', 'warning', $errors) : null;
    }

    public static function secureField($field, $type = 'post', $stripTags = false, $toInt = false)
    {
        $inputTypes = [
            mb_strtolower('get') => INPUT_GET,
            mb_strtolower('post') => INPUT_POST,
            // Adicione outros tipos de entrada conforme necessário
        ];

        if (!isset($inputTypes[$type])) {
            return null; // Tipo de entrada inválido
        }

        // Defina o filtro padrão para campos não numéricos
        $filter = $toInt ? FILTER_VALIDATE_INT : FILTER_DEFAULT;

        // Obtenha o valor do campo do superglobal correspondente e aplique o filtro
        $value = filter_input($inputTypes[$type], $field, $filter);

        // Verifique se o valor não é nulo antes de chamar trim()
        if ($value !== null) {

            // Aplicar trim para remover os espaçamentos do inicio e o fim
            $value = trim($value);

            // Aplicar strip_tags se solicitado
            if ($stripTags) {
                $value = strip_tags($value);
            }
        }

        return !empty($value) ? $value : null;
    }
}
