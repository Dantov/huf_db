<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 20.07.2020
 * Time: 13:36
 */

namespace Views\vendor\libs\classes;


class AppCodes
{

    private $STARTED = false;


    /** ERRORS **/
    const SUCH_APP_CODE_NOT_PRESENT = 1002;
    const SERVER_ERROR = 999;
    const NOTHING_DONE = 998;

    const MODEL_DOES_NOT_EXIST = 2001;
    const PRICE_DOES_NOT_EXIST = 2002;
    const WRONG_PRICE = 2003;
    const NO_PERMISSION = 2004;
    const PRICE_NOT_CREDITED = 2005;
    const NO_PERMISSION_TO_PAY = 611;
    const PAYING_ERROR = 612;


    /** SUCCESS **/
    const PAY_SUCCESS = 610;
    const PRICE_CREDITED = 3001;



    /** MESSAGES ARRAY **/
    const MESSAGES = [
        self::NOTHING_DONE => [
            'code' => self::NOTHING_DONE,
            'message'=>'Nothing done.'
        ],
        self::SUCH_APP_CODE_NOT_PRESENT => [
            'code' => self::SUCH_APP_CODE_NOT_PRESENT,
            'message'=>'No such code'
        ],
        self::SERVER_ERROR => [
            'code' => self::SERVER_ERROR,
            'message'=>'Ошибка на сервере. Попробуйте позже.'
        ],
        self::PAY_SUCCESS => [
            'code' => self::PAY_SUCCESS,
            'message'=>'Оплата успешно зачислена!'
        ],
        self::NO_PERMISSION_TO_PAY => [
            'code' => self::NO_PERMISSION_TO_PAY,
            'message'=>'Ошибка! Нет прав для зачисления оплаты.'
        ],
        self::PAYING_ERROR => [
            'code' => self::PAYING_ERROR,
            'message'=>'Ошибка зачисления оплаты. Попробуйте позже.'
        ],
        self::MODEL_DOES_NOT_EXIST => [
            'code' => self::MODEL_DOES_NOT_EXIST,
            'message'=>'Ошибка. Такой модели в базе нет!.'
        ],
        self::PRICE_DOES_NOT_EXIST => [
            'code' => self::PRICE_DOES_NOT_EXIST,
            'message'=>'Ошибка. Нет такой стоимости!.'
        ],
        self::WRONG_PRICE => [
            'code' => self::WRONG_PRICE,
            'message'=>'Ошибка! Нет возможности зачислить оплату этой стоимости.'
        ],
        self::NO_PERMISSION => [
            'code' => self::NO_PERMISSION,
            'message'=>'Ошибка! Не достаточно прав для совершения операции.'
        ],
        self::PRICE_CREDITED => [
            'code' => self::PRICE_CREDITED,
            'message'=>'Стоимость успешно начислена!'
        ],
        self::PRICE_NOT_CREDITED => [
            'code' => self::PRICE_NOT_CREDITED,
            'message'=>'Ошибка! Стоимость не зачислена. Попробуйте позже.'
        ],
    ];

    public function __construct()
    {
        $this->STARTED = true;
    }

    public function isStarted()
    {
        return $this->STARTED;
    }

    /**
     * @param int $code
     * @return array
     * @throws \Exception
     */
    public function getCodeMessage(int $code) : array
    {
        if ( array_key_exists($code, self::MESSAGES) ) return self::MESSAGES[$code];
        throw new \Exception( "class: " . __CLASS__ .
            " method: " . __METHOD__. " " .
            self::MESSAGES[self::SUCH_APP_CODE_NOT_PRESENT]['message'] . " '" . $code . "'",
            self::SUCH_APP_CODE_NOT_PRESENT);
    }

    /**
     * @param int $code
     * @return array
     * @throws \Exception
     */
    public static function getMessage(int $code) : array
    {
        if ( array_key_exists($code, self::MESSAGES) ) return self::MESSAGES[$code];

        throw new \Exception( "class: " . __CLASS__ .
            " method: " . __METHOD__. " " .
            self::MESSAGES[self::SUCH_APP_CODE_NOT_PRESENT]['message'] . " '" . $code . "'",
            self::SUCH_APP_CODE_NOT_PRESENT);
    }
}