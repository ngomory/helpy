<?php

namespace Ngomory;

class Helpy
{

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
                $toFormat = 'd/m/Y' . ($showTime ? ' \à H:i:s' : '');
                return self::dateFormat($datetime, $toFormat);
                break;
            case 'en':
                $toFormat = 'Y-m-d' . ($showTime ? ' \a\t H:i:s' : '');
                return self::dateFormat($datetime, $toFormat);
                break;
            default:
                return '';
                break;
        }
    }

    /**
     * numberFormat
     *
     * @param float $number
     * @param integer $decimals Number of digits decimal
     * @param string $decimalSeparator
     * @param string $thousandSeparator
     * @return string
     */
    public static function numberFormat(float $number, int $decimals = 2, string $decimalSeparator = ',', string $thousandSeparator = ' '): string
    {
        return number_format($number, $decimals, $decimalSeparator, $thousandSeparator);
    }

    /**
     * numberToLocal
     *
     * @param float $number
     * @param string $local String local code fr|en
     * @param integer $decimals Number of digits decimal
     * @return string
     */
    public static function numberToLocal(float $number, string $local = 'fr', int $decimals = 2): string
    {
        switch ($local) {
            case 'fr':
                return self::numberFormat($number, $decimals);
                break;
            case 'en':
                return self::numberFormat($number, $decimals, '.', ',');
                break;
            default:
                return $number;
                break;
        }
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

    /**
     * defaultSchemaColumn
     *
     * @param [type] $table
     * @param string $group
     * @param integer $state
     * @return void
     */
    static function defaultSchemaColumn($table, string $group = 'defaut', int $state = 1)
    {
        switch ($group) {
            case 'state':
                // For state
                $table->boolean('state')->default($state)->nullable();
                $table->bigInteger('state_by')->nullable();
                $table->dateTime('state_at')->nullable();
                break;
            case 'timestamp':
                // For soft delete
                $table->bigInteger('created_by')->nullable();
                $table->dateTime('created_at')->nullable();
                $table->bigInteger('updated_by')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->bigInteger('deleted_by')->nullable();
                $table->dateTime('deleted_at')->nullable();
                break;
            default:
                // Default table columns
                $table->collation = 'utf8mb4_general_ci';
                $table->increments('id')->unsigned();
                $table->uuid('uuid')->unique()->nullable();
                break;
        }
    }
}
