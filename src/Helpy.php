<?php

namespace Ngomory;

class Helpy
{

    /**
     * dateToLocal
     *
     * @param string|null $datetime String datetime value or null. can have the value 'now'.
     * @param string $local String local code fr|en.
     * @param boolean $time Display or not the date.
     * @return string
     */
    public static function dateToLocal($datetime, string $local = 'fr', bool $showTime = true): string
    {
        switch ($local) {
            case 'fr':
                $toFormat = 'd/m/Y' . ($showTime ? ' à H:i:s' : '');
                return self::dateFormat($datetime, $toFormat);
                break;
            case 'en':
                $toFormat = 'Y-m-d' . ($showTime ? ' at H:i:s' : '');
                return self::dateFormat($datetime, $toFormat);
                break;
            default:
                return '';
                break;
        }
    }

    /**
     * dateFormat
     *
     * @param string|null $datetime String datetime value or null. can have the value 'now'.
     * @param string $fromFormat
     * @param string $toFormat
     * @return string
     */
    public static function dateFormat($datetime, $toFormat = 'd/m/Y H:i:s', $fromFormat = 'Y-m-d H:i:s'): string
    {
        $datetime = ($datetime == 'now') ? date('Y-m-d H:i:s') : $datetime;
        $date = \DateTime::createFromFormat($fromFormat, $datetime);
        return $date ? $date->format($toFormat) : '';
    }

    public static function formatNumber(float $number, array $option = [])
    {

        $decimals = (int)$option['decimals'] ?? 2;
        $decimal_separator = $option['decimal_separator'] ?? ',';
        $thousands_separator = $option['thousands_separator'] ?? ' ';

        return number_format($number, $decimals, $decimal_separator, $thousands_separator);
    }

    /**
     * strRandom
     *
     * @param integer $length The length of the result
     * @param array $options Possible options include | excluded
     * @return string
     */
    public static function strRandom(int $length = 16, array $options = ['include' => '', 'excluded' => '']): string
    {

        $characters = $options['characters'] ?? '';
        if (empty($characters)) {
            $characters = '0123456789';
            $characters .= 'abcdefghijklmnopqrstuvwxyz';
            $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $characters = str_split($characters);

        $characters = array_merge($characters, str_split($options['include'] ?? ''));
        $characters = array_diff($characters, str_split($options['excluded'] ?? ''));
        shuffle($characters);

        $charactersLength = count($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
