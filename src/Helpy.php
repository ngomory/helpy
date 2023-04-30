<?php

namespace Ngomory;

class Helpy
{

    static function formatDatetime(string $datetime, array $options = []): string
    {

        $format = $options['format'] ?? 'Y-m-d H:i:s';

        if (empty($datetime)) {
            return '';
        } else {
            return date($format, strtotime($datetime));
        }
    }

    static function formatNumber(float $number, array $option = [])
    {

        $decimals = (int)$option['decimals'] ?? 2;
        $decimal_separator = $option['decimal_separator'] ?? ',';
        $thousands_separator = $option['thousands_separator'] ?? ' ';

        return number_format($number, $decimals, $decimal_separator, $thousands_separator);
    }

    static function strRandom(int $length = 16, array $options = []): string
    {

        $characters = $options['characters'] ?? '';

        if (empty($characters)) {
            $characters = '0123456789';
            $characters .= 'abcdefghijklmnopqrstuvwxyz';
            $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
