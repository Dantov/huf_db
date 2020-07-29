<?php

namespace Views\vendor\core\Errors\Exceptions;
use Views\vendor\libs\classes\AppCodes;

/**
 * Class DBConnectException
 * Исключения связанные с БД
 * @package Views\vendor\core\Errors\Exceptions
 */
class DBConnectException extends \Exception
{
    /**
     * DBConnectException constructor.
     * @param string $message
     * @param int $code
     * @throws \Exception
     */
    public function __construct( $code )
    {
        $message = [];
        switch ( $code )
        {
            case AppCodes::DB_CONFIG_EMPTY:
                {
                    $message = AppCodes::getMessage(AppCodes::DB_CONFIG_EMPTY);
                } break;
            case AppCodes::USER_DB_CONFIG_EMPTY:
                {
                    $message = AppCodes::getMessage(AppCodes::USER_DB_CONFIG_EMPTY);
                } break;
            case AppCodes::DB_CONFIG_ACCESS_FIELD_EMPTY:
                {
                    $message = AppCodes::getMessage(AppCodes::DB_CONFIG_ACCESS_FIELD_EMPTY);
                } break;
        }

        parent::__construct( $message['message'], $message['code'] );
    }

}