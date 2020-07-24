<?php

namespace Views\vendor\libs\classes;


class URLCrypt
{

    private static $secretKey = "OMGSecretKey123!";
    public static $queryVar = '';
    public static $gerVars = [];


    protected function __construct()
    {
    }

    /**
     * URLCrypt::encode('pm',["tab"=>$tabID, "worker"=>0, "month"=>$monthID, "year"=>$yearID])
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

        $uri = self::strEncode($uri, self::$secretKey);


        return "?" . $queryVar . "=" .$uri;
    }

    public static function decode( $cipherText ) : string
    {

        return self::strDecode($cipherText,self::$secretKey);
    }

    public static function strEncode(string $unEncoded, string $key = '')
    {
        if ( empty($key) ) $key = self::$secretKey;
        //Шифруем
        //debug($unencoded,'origin');
        $string = base64_encode($unEncoded);
        //debug($string,'base64_decode');

        $arr = [];
        $newStr = '';
        for ( $x = 0; $x < strlen($string); $x++ )
        {
            $arr[$x] = hash( 'sha1',$key . hash('sha3-256',$string[$x] . $key ) );
            //debug($arr[$x],'$arr[$x]');

            //Склеиваем символы
            $newStr .= $arr[$x][7].$arr[$x][4].$arr[$x][9].$arr[$x][2];
        }
        //debug($newStr,'$newStr');
        return $newStr;
    }

    public static function strDecode( string $encoded, string $key = '' )
    {
        if ( empty($key) ) $key = self::$secretKey;
        //Символы, из которых состоит base64-ключ
        $strOfSym="qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM=";
        for ( $x = 0; $x < strlen($strOfSym); $x++ )
        {
            // шифруем каждый символ
            //Хеш, который соответствует символу, на который его заменят.
            $tmp = hash('sha1',$key . hash('sha3-256',$strOfSym[$x] . $key) );
            //Заменяем №3,6,1,2 из хеша на символ
            $encoded = str_replace($tmp[7].$tmp[4].$tmp[9].$tmp[2], $strOfSym[$x], $encoded);
            //debug($encoded,'$encoded');
        }
        return base64_decode($encoded);
    }

}