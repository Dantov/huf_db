<?php

namespace Views\vendor\core;


class Crypt
{

	protected static $secretKey = "OMGSecretKey123!";
	protected static $algo1 = "sha1";
    protected static $algo2 = "sha3-256";

	public static function strEncode(string $unEncoded, string $key = '', string $algo1 = '', string $algo2 = '')
    {
        if ( empty($key) ) $key = self::$secretKey;
        if ( empty($algo1) ) $algo1 = self::$algo1;
        if ( empty($algo2) ) $algo2 = self::$algo2;

        //Шифруем
        //debug($unencoded,'origin');
        $string = base64_encode($unEncoded);
        //debug($string,'base64_decode');

        $arr = [];
        $newStr = '';
        for ( $x = 0; $x < strlen($string); $x++ )
        {
            $arr[$x] = hash( $algo1,$key . hash($algo2,$string[$x] . $key ) );
            //debug($arr[$x],'$arr[$x]');

            //Склеиваем символы
            $newStr .= $arr[$x][7].$arr[$x][4].$arr[$x][9].$arr[$x][2];
        }
        //debug($newStr,'$newStr');
        return $newStr;
    }

    public static function strDecode( string $encoded, string $key = '', string $algo1 = '', string $algo2 = '' )
    {
        if ( empty($key) ) $key = self::$secretKey;
        if ( empty($algo1) ) $algo1 = self::$algo1;
        if ( empty($algo2) ) $algo2 = self::$algo2;
        //Символы, из которых состоит base64-ключ
        $strOfSym="qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM=";
        for ( $x = 0; $x < strlen($strOfSym); $x++ )
        {
            // шифруем каждый символ
            //Хеш, который соответствует символу, на который его заменят.
            $tmp = hash($algo1, $key . hash($algo2,$strOfSym[$x] . $key) );
            //Заменяем №3,6,1,2 из хеша на символ
            $encoded = str_replace($tmp[7].$tmp[4].$tmp[9].$tmp[2], $strOfSym[$x], $encoded);
            //debug($encoded,'$encoded');
        }
        return base64_decode($encoded);
    }
}