<?php

namespace App\myClass;

class Helpy
{

    static function formatDatetime(string $datetime, string $format = 'd/m/Y à H:i:s')
    {
        if (empty($datetime)) {
            return null;
        } else {
            return date($format, strtotime($datetime));
        }
    }
    
    static function formatNumber(float $number, string $local = 'fr', array $option = ['decimals' => 2, 'dec_point' => ',', 'thousands_sep' => ' '])
    {

        $decimals = (int)$option['decimals'] ?? 2;
        $dec_point = $option['dec_point'] ?? ',';
        $thousands_sep = $option['thousands_sep'] ?? ' ';

        return number_format($number, $decimals, $dec_point, $thousands_sep);
        
    }
}
