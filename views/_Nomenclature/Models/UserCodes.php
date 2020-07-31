<?php
/**
 * Date: 29.07.2020
 * Time: 15:02
 */

namespace Views\_Nomenclature\Models;
use Views\vendor\libs\classes\AppCodes;

class UserCodes extends AppCodes
{

    /**
     * Errors 300-399
     */
    const LOGIN_EMPTY = 300;
    const PASSWORD_EMPTY = 301;
    const FIRST_NAME_EMPTY = 302;
    const NO_SUCH_USER = 303;
    const LOGIN_MATCH = 304;
    const WORKING_CENTER_NOT_FOUND = 305;

    const USER_INSERT_UPDATE_FAIL = 306;
    const PERMISSION_INSERT_UPDATE_FAIL = 3478;

    const INSERT_UPDATE_FAIL = 347;

    /**
     * SUCCESS 400-499
     */
    const USER_ADD_SUCCESS = 400;
    const USER_EDIT_SUCCESS = 401;
    const PERMISSIONS_ADD_SUCCESS = 402;
    const PERMISSIONS_EDIT_SUCCESS = 403;
    const USER_PERMISSIONS_EDIT_SUCCESS = 404;
    const USER_PERMISSIONS_ADD_SUCCESS = 405;
    const NOTHING_DONE = 406;


    /** MESSAGES ARRAY **/
    protected static $MESSAGES = [
        self::NOTHING_DONE => [
            'code' => self::NOTHING_DONE,
            'message'=>'Изменений в данных не было.'
        ],
        self::PERMISSION_DENIED => [
            'code' => self::PERMISSION_DENIED,
            'message'=>'Доступ запрещен.'
        ],
        self::LOGIN_EMPTY => [
            'code' => self::LOGIN_EMPTY,
            'message'=>'Логин не должен быть пуст.'
        ],
        self::PASSWORD_EMPTY => [
            'code' => self::PASSWORD_EMPTY,
            'message'=>'Пароль не должен быть пуст.'
        ],
        self::FIRST_NAME_EMPTY => [
            'code' => self::FIRST_NAME_EMPTY,
            'message'=>'Поле "Фамилия" пользователя, не должно быть пустым.'
        ],
        self::NO_SUCH_USER => [
            'code' => self::NO_SUCH_USER,
            'message'=>'Пользователь не найден.'
        ],
        self::LOGIN_MATCH => [
            'code' => self::LOGIN_MATCH,
            'message'=>'Ошибка! Такой логин уже существует.'
        ],
        self::USER_ADD_SUCCESS => [
            'code' => self::USER_ADD_SUCCESS,
            'message'=>'Новый пользователь добавлен.'
        ],
        self::USER_EDIT_SUCCESS => [
            'code' => self::USER_EDIT_SUCCESS,
            'message'=>'Данные пользователя изменены.'
        ],
        self::WORKING_CENTER_NOT_FOUND => [
            'code' => self::WORKING_CENTER_NOT_FOUND,
            'message'=>'Неизвестный рабочий участок, к которому вы относите пользователя.'
        ],
        self::USER_INSERT_UPDATE_FAIL => [
            'code' => self::USER_INSERT_UPDATE_FAIL,
            'message'=>'Ошибка! Данные пользователя не добавлены. Попробуйте позже.'
        ],
        self::PERMISSION_INSERT_UPDATE_FAIL => [
            'code' => self::PERMISSION_INSERT_UPDATE_FAIL,
            'message'=>'Ошибка! Разрешения для пользователя не добавлены. Попробуйте позже.'
        ],
        self::USER_PERMISSIONS_EDIT_SUCCESS => [
            'code' => self::USER_PERMISSIONS_EDIT_SUCCESS,
            'message'=>'Данные пользователя изменены. Разрешения изменены.'
        ],
        self::USER_PERMISSIONS_ADD_SUCCESS => [
            'code' => self::USER_PERMISSIONS_ADD_SUCCESS,
            'message'=>'Новый пользователь добавлен. Разрешения добавлены.'
        ],
        self::PERMISSIONS_EDIT_SUCCESS => [
            'code' => self::PERMISSIONS_EDIT_SUCCESS,
            'message'=>'Разрешения изменены.'
        ],
    ];
}