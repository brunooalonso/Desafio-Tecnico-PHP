<?php

namespace Config;

use App\Library\ValidationLibrary;
use PDO;

class Utils
{
    /**
     * Debug
     * Like laravel dd
     *
     * @param mixed $array
     * @return string
     */
    public static function debug($array): void
    {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
        die;
    }

    /**
     * euaToBr
     * Converte a data do formato 'Y-m-d' (en) para 'd/m/Y' (br)
     *
     * @param mixed $date
     * @return string
     */
    public static function euaToBr($date = '')
    {
        if ($date == '') {
            return date('d/m/Y');
        } else {
            $dateParts = explode('-', $date);
            if (count($dateParts) === 3) {
                return $dateParts[2] . '/' . $dateParts[1] . '/' . $dateParts[0];
            }
        }
    }

    /**
     * euaToBrDateTime
     * Converte a data do formato 'Y-m-d H:i' (en) para 'd/m/Y H:i' (br)
     *
     * @param mixed $date
     * @return string
     */
    public static function euaToBrDateTime($date = '')
    {
        if ($date == '') {
            return date('Y-m-d H:i');
        } else {
            // Extrai as partes da data e hora
            $dateTimeParts = explode(' ', $date);
            $dateParts = explode('-', $dateTimeParts[0]);
            $timePart = isset($dateTimeParts[1]) ? ' ' . $dateTimeParts[1] : '';

            if (count($dateParts) === 3) {
                // Retorna a data no formato brasileiro com a hora, se disponível
                return $dateParts[2] . '/' . $dateParts[1] . '/' . $dateParts[0] . $timePart;
            }
        }
    }

    /**
     * brToEua
     * Converte a data do formato 'd/m/Y' (br) para 'Y-m-d' (en)
     *
     * @param mixed $date
     * @return string
     */
    public static function brToEua($date = '')
    {
        if ($date == '') {
            return date('Y-m-d');
        } else {
            $dateParts = explode('/', $date);
            if (count($dateParts) === 3) {
                return $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
            }
        }
    }

    /**
     * getDateBr
     *
     * @param string $date
     * @return data
     */
    public static function getDateBr($date = '')
    {
        return empty($date) ? date('d/m/Y') : $date;
    }

    /**
     * getDateEua
     *
     * @param string $date
     * @return data
     */
    public static function getDateEua($date = '')
    {
        return empty($date) ? date('Y-m-d') : $date;
    }
    /**
     * getDateTimeEua
     *
     * @param string $date
     * @return data
     */
    public static function getDateTimeEua()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * friendlyUrl
     *
     * @param mixed $text
     * @return string
     */
    public static function friendlyUrl($text)
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        if (empty($text)) {
            return '';
        }
        return $text;
    }

    public static function redirectTo($url, $param = null)
    {
        $redirect = $url . $param;

        header("Location: $redirect");
        exit();
    }

    public static function numberFormart($number)
    {
        //Formate o número com duas casas decimais e separador de milhares
        return !empty($number) ? number_format($number, 2, ',', '.') : '';
    }

    public static function formatNumberForInsertion($number)
    {
        if (!empty($number)) {
            // Verificar se o número contém uma vírgula
            if (strpos($number, ',') !== false) {
                // Remover pontos de milhar
                $number = str_replace('.', '', $number);

                // Substituir a vírgula por ponto
                $number = str_replace(',', '.', $number);
            }
        }

        return !empty($number) ? $number : null;
    }

    /**
     * jsonResponse, retornar um alerta para o cliente
     *
     * @param [type] $status
     * @param [type] $title
     * @param [type] $type
     * @param [type] $message
     * @param array $additionalFields
     * @return void
     */
    public static function jsonResponse($status, $title, $type, $message, $additionalFields = [])
    {
        $response = [
            'status' => $status,
            'title' => $title,
            'type' => $type,
            'message' => $message,
        ];

        // Adicionar campos adicionais, se fornecidos
        $response = array_merge($response, $additionalFields);

        echo json_encode($response);
        exit;
    }


    public static function paramType($value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_numeric($value):
                    // Tratar números decimais como strings
                    $type = strpos($value, '.') !== false ? PDO::PARAM_STR : PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        } else {
            // Caso o tipo seja especificado, retorne-o diretamente
            // a menos que seja null, nesse caso, retorne PARAM_NULL
            $type = is_null($value) ? PDO::PARAM_NULL : $type;
        }
        return $type;
    }
}
