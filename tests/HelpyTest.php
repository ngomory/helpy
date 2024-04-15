<?php

use Ngomory\Helpy;
use PHPUnit\Framework\TestCase;

class HelpyTest extends TestCase
{
    public function testDateFormat()
    {
        $datetime = '2022-01-01 12:34:56';

        // Test avec parametre par defaut
        $result = Helpy::dateFormat($datetime);
        $this->assertEquals('01/01/2022 12:34:56', $result);

        // Test avec $toFormat de sorti specifique
        $result = Helpy::dateFormat($datetime, 'd/m/Y');
        $this->assertEquals('01/01/2022', $result);

        // Test avec datime null
        $result = Helpy::dateFormat(null);
        $this->assertEquals('', $result);

        // Test avec datetime egale a now (datetime actuel)
        $result = Helpy::dateFormat('now');
        $this->assertEquals(date('d/m/Y H:i:s'), $result);

        // Test avec $fromFormat different du datetime
        $result = Helpy::dateFormat($datetime, 'd/m/Y H:i:s', 'Y-m-d');
        $this->assertEquals('', $result);
    }

    public function testDateToLocal()
    {
        $datetime = '2022-01-01 12:34:56';

        // Test avec les paramettre par defaut
        $result = Helpy::dateToLocal($datetime);
        $this->assertEquals('01/01/2022 à 12:34:56', $result);

        // Test avec $local fr et $showTime à false
        $result = Helpy::dateToLocal($datetime, 'fr', false);
        $this->assertEquals('01/01/2022', $result);

        // Test avec $local en et $showTime
        $result = Helpy::dateToLocal($datetime, 'en');
        $this->assertEquals('2022-01-01 at 12:34:56', $result);

        // Test avec $local en et $showTime à false
        $result = Helpy::dateToLocal($datetime, 'en', false);
        $this->assertEquals('2022-01-01', $result);

        // Test avec $datetime vide
        $result = Helpy::dateToLocal('');
        $this->assertEquals('', $result);

        // Tes avec $datetime null
        $result = Helpy::dateToLocal(null);
        $this->assertEquals('', $result);
    }

    public function testNumberFormat()
    {
        $number = 651234.5648;

        // Test avec les parametre par defaut
        $result = Helpy::numberFormat($number);
        $this->assertEquals('651 234,56', $result);
    }

    public function testNumberToLocal()
    {
        $number = 651234.5648;

        // Test with fr local
        $result = Helpy::numberToLocal($number, 'fr');
        $this->assertEquals('651 234,56', $result);

        // Test with en local
        $result = Helpy::numberToLocal($number, 'en');
        $this->assertEquals('651,234.56', $result);
    }

    public function testStrRandom()
    {
        $length = 10;

        // Test with default options
        $result = Helpy::strRandom($length);
        $this->assertEquals($length, strlen($result));

        // Test with custom options
        $options = [
            'characters' => 'ABC123',
            'include' => '!@#$',
            'excluded' => '123'
        ];
        $result = Helpy::strRandom($length, $options);
        $this->assertEquals($length, strlen($result));
    }

    public function teststrToEmoji()
    {
        $string = 'fr';
        $result = Helpy::strToEmoji($string);
        $this->assertEquals($result, '🇫🇷');
    }
}
