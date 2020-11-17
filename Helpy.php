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
}
