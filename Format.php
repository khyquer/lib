<?php

namespace App\Cp\Edi\Util;

use ZendX\Util\Format as ParentFormat;

/**
 * Classe para controle de string
 * @package Edi
 * @subpackage Util
 */
class Format extends ParentFormat
{

    /**
     * Formata string para o formato CPF ###.###.###-##
     * @param type $cpf
     * @return string CPf formatado ###.###.###-##
     */
    public static function cpf($cpf)
    {
        $cpf = str_pad($cpf, 9, '0', STR_PAD_LEFT);
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }

    /**
     * Formatar string para o formato CNPJ ##.###.###/####-##
     * @param string $cnpj String numerica
     * @return string CNPJ formatado 
     */
    public static function cnpj($cnpj)
    {
        $cnpj = str_pad($cnpj, 14, '0', STR_PAD_LEFT);
        return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
    }

    /**
     * Remove caracteres especiais da string
     * @param string $string
     * @return string String sem caracteres especiais
     */
    public static function specialCharacters($string)
    {
        $from = 'ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ';
        $to = 'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy';

        return strtr(utf8_decode($string), utf8_decode($from), utf8_decode($to));
    }

    /**
     * Decodifica um valor de data
     * @param string $dateTime Valor codificado da data
     * @param string $tipoData Define o tipo de dado
     * @return \DateTime
     */
    public static function dateTime($dateTime, $formatImput = 'ymdHis', $formatOutput = 'd/m/Y H:i:s')
    {
        // Se a string for totalmente composta por zeros
        if(!((integer)$dateTime))
        {
            return null;
        }

        // Criar objeto dateTime
        $dateTime = parent::dateTime($dateTime, $formatImput);

        // Retornar string de data formatada
        return $dateTime;
    }

    /**
     * Decodifica um valor de data
     * @param \DateTime $dateTime Valor codificado da data
     * @param string $tipoData Define o tipo de dado
     * @return string String de data formatada
     */
    public static function decodeDateTime($dateTime)
    {
        return self::dateTime($dateTime);
    }

    /**
     * Decodifica um valor de data
     * @param \DateTime $dateTime Valor codificado da data
     * @param string $tipoData Define o tipo de dado
     * @return string String de data formatada
     */
    public static function encodeDateTime(\DateTime $dateTime)
    {
        // Retornar objeto 
        return $dateTime->format('ymdHis');
    }

    /**
     * Decodifica um valor de data a partir de uma string numerica
     * @param string $data Valor codificado da data
     * @return \DateTime
     */
    public static function decodeDate($data)
    {
        $data = self::dateTime($data, 'ymd', 'd/m/Y');

        // Se o objeto de data for criado, remover as informações de hora
        if(!is_null($data))
        {
            $data->setTime(0, 0, 0);
        }

        return $data;
    }

    /**
     * Decodifica um valor de data
     * @param \DateTime $dateTime Valor codificado da data
     * @param string $tipoData Define o tipo de dado
     * @return string String de data formatada
     */
    public static function encodeDate(\DateTime $dateTime)
    {
        // Formata objeto de data corretamente
        return $dateTime->format('ymd');
    }

    /**
     * Decodifica um valor de data a partir de uma string numerica
     * @param string $hour Valor codificado da data
     * @return \DateTime
     */
    public static function decodeHour($hour)
    {
        $hour = self::dateTime($hour, 'Hi', 'H:i:0');

        // Se o objeto de data for criado, remover as informações de data
        if(!is_null($hour))
        {
            $hour->setDate(0, 0, 0);
        }

        return $hour;
    }

    /**
     * Decodifica um valor de data
     * @param \DateTime $dateTime Valor codificado da data
     * @param string $tipoData Define o tipo de dado
     * @return string String de data formatada
     */
    public static function encodeHour(\DateTime $dateTime)
    {
        // Formata objeto de data corretamente
        return $dateTime->format('Hi');
    }

    /**
     * Decodifica um valor de data a partir de uma string numerica
     * @param string $time Valor codificado da data
     * @return string
     */
    public static function decodeTime($time)
    {
        $time = self::dateTime($time, 'His', 'H:i:s');
        // Se o objeto de data for criado, remover as informações de hora
        if(!is_null($time))
        {
            $time->setDate(0, 0, 0);
        }

        return $time;
    }

    /**
     * Decodifica um valor de data
     * @param \DateTime $dateTime Valor codificado da data
     * @param string $tipoData Define o tipo de dado
     * @return string String de data formatada
     */
    public static function encodeTime(\DateTime $dateTime)
    {
        // Formata objeto de data corretamente
        return $dateTime->format('His');
    }

    /**
     * Codifica um valor float para string numerica
     * @param float $valor Valor numerico com casas decimais
     * @param integer $precisao Quantidade e casas após a vírgula
     * @return string
     */
    public static function encodeFloat($valor, $precisao)
    {
        return (string)(round($valor, $precisao) * pow(10, $precisao));
    }

    /**
     * Decodifica um valor de string numerica para um float com casas decimais
     * @param string $valor String numerica
     * @param integer $precisao Quantidade e casas após a vírgula
     * @return float
     */
    public static function decodeFloat($valor, $precisao)
    {
        return (float)substr_replace($valor, '.', -1 * $precisao, 0);
    }

    /**
     * Codifica um valor float para string numerica
     * @param float $valor Valor numerico com casas decimais
     * @param integer $precisao Quantidade e casas após a vírgula
     * @return string
     */
    public static function encodeString($valor)
    {
        return (string)self::specialCharacters($valor);
    }

    /**
     * Decodifica um valor de string numerica para um float com casas decimais
     * @param string $valor String numerica
     * @param integer $precisao Quantidade e casas após a vírgula
     * @return float
     */
    public static function decodeString($valor)
    {


        return (string)$valor;
    }

    /**
     * Codifica um valor float para string numerica
     * @param float $valor Valor numerico com casas decimais
     * @param integer $precisao Quantidade e casas após a vírgula
     * @return string
     */
    public static function encodeInteger($valor)
    {
        return (string)round($valor);
    }

    /**
     * Decodifica um valor de string numerica para um float com casas decimais
     * @param string $valor String numerica
     * @param integer $precisao Quantidade e casas após a vírgula
     * @return float
     */
    public static function decodeInteger($valor)
    {
        return (int)round($valor);
    }

}
