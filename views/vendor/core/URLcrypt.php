<?php

namespace Views\vendor\core;


class URLCrypt
{

    private static $secretKey = "OMGSecretKey123!";
    public static $queryVar = '';
    public static $gerVars = [];


    protected function __construct()
    {
    }

    /**
     * @param string $queryVar
     * @param array $getVars
     * @return string
     */
    public static function encode( string $queryVar, array $getVars ) : string
    {
        if ( empty($getVars) || empty($queryVar) ) return '';

        $uri = '';
        ksort($getVars);
        foreach ( $getVars as $varName => $varValue ) $uri .= $varName . "=" . $varValue . "&";
        $uri = rtrim($uri,'&');

        $uri = self::myencode($uri, self::$secretKey);


        return "?" . $queryVar . "=" .$uri;
    }

    public static function decode( $ciphertext ) : string
    {

        return self::mydecode($ciphertext,self::$secretKey);
    }

    private static function myencode($unencoded, $key)
    {
        //Шифруем
        //debug($unencoded,'origin');
        $string = base64_encode($unencoded);
        //debug($string,'base64_decode');

        $arr = [];
        $newStr = '';
        for ( $x = 0; $x < strlen($string); $x++ )
        {
            $arr[$x] = hash( 'sha1',$key . hash('sha1',$string[$x] . $key ) );
            //debug($arr[$x],'$arr[$x]');

            //Склеиваем символы
            $newStr .= $arr[$x][7].$arr[$x][4].$arr[$x][9].$arr[$x][2];
        }
        //debug($newStr,'$newStr');
        return $newStr;
    }

    private static function mydecode( $encoded, $key )
    {
        //Символы, с которых состоит base64-ключ
        $strofsym="qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM=";
        for ( $x = 0; $x < strlen($strofsym); $x++ )
        {
            //Хеш, который соответствует символу, на который его заменят.
            $tmp = hash('sha1',$key . hash('sha1',$strofsym[$x] . $key) );
            //Заменяем №3,6,1,2 из хеша на символ
            $encoded = str_replace($tmp[7].$tmp[4].$tmp[9].$tmp[2], $strofsym[$x], $encoded);
            //debug($encoded,'$encoded');
        }
        return base64_decode($encoded);
    }

}