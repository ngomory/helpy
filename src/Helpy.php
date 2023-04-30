<?php

namespace App\myClass;

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
}
