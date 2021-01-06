<?php
/**
 * Date: 05.01.2021
 * Time: 22:22
 */

namespace Views\vendor\libs\classes;

use Matrix\Exception;
use Views\vendor\core\db\Database;

class Validator
{

    protected $connection;

    protected static $lastError = '';
    protected static $errors = [];

    protected $fieldRules = [];

    protected $badChars = ['\'', '.', ',', '\\', '/', '"', '%','&','?','*','|','^', '<', '>', ':',';','`'];

    /**
     * Validator constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->connection = Database::instance()->getConnection();
    }

    public function getAllErrors()
    {
        return self::$errors;
    }
    public function getLastError()
    {
        return self::$lastError;
    }
    /**
     * Удаляем всю инф. об ошибках
     * что бы валидировать новые поля
     * @return bool
     */
    public function reset() : bool
    {
        self::$lastError = '';
        self::$errors = [];
        if ( empty(self::$lastError) && empty(self::$errors) )
            return true;

        return false;
    }
    /**
     * Описаны возможные правила
     * @param string $ruleName
     * @param string $field
     * @param string $value
     * @return array
     */
    protected function rulesErrorText(  string $ruleName, string $field, string $value )
    {
        $rules = [
            'min' => "Значение поля '" . $field . "' не должно быть меньше " . $value . ". ",
            'max' => "Значение поля '" . $field . "' не должно быть больше " . $value . ". ",
            'minLength' => "Кол-во символов в поле '" . $field . "' не должно быть меньше " . $value . ". ",
            'maxLength' => "Кол-во символов в поле '" . $field . "' не должно быть больше " . $value . ". ",
            'required' => "Поле '" . $field . "' обязательно к заполнению. ",
            'readonly' => "Поле '" . $field . "' нельзя изменять. ",
            'int' => "Значение поля '" . $field . "' должно быть целое число. ",
            'double' => "Значение поля '" . $field . "' должно быть дробное число. ",
            'forbiddenChars' => "Значение поля '" . $field . "' содержит не допустимые символы. ",
            'acceptedChars' => "Значение поля '" . $field . "' содержит не . ",

            'type' => ['string','int','double','array'], // тип значения
            'elemType' => ['string','int','double','array'],
            'trim' => true,
            'strip_tags' => true, // применить striptags()
            'escape_string' => true, // применить realescapestring()
        ];

        if ( array_key_exists($ruleName, $rules) )
            return $rules[$ruleName];

        return $rules;
    }

    /**
     *
     * @param string $field
     * @return array
     * @throws Exception
     */
    protected function fieldRules( string $field = '' )
    {
        if ( !empty($this->fieldRules) )
        {
            if ( $field )
            {
                if ( array_key_exists($field,$this->fieldRules) )
                    return $this->fieldRules[$field];
            } else {
                throw new Exception('Field  "' . $field . '" not found in rules!',213);
            }
        }

        $this->fieldRules = [
            'number_3d'=> [
                'name'=> '№ 3Д',
                'rules' => [
                    'required' => true,
                    'minLength' => 7,
                    'maxLength' => 24,
                    'forbiddenChars' => true,
                ],
            ],
            'vendor_code' => [
                'name'=> 'Артикул',
                'rules' => [
                    'maxLength' => 64,
                ],
            ],
            'author' => [
                'name'=> 'Автор',
                'rules' => [
                    'required' => true,
                    'maxLength' => 3,//64
                ],
            ],
            'modeller3d' => [
                'name'=> '3д-модельер',
                'rules' => [
                    'required' => true,
                    'maxLength' => 64,
                ],
            ],
            'jewelerName' => [
                'name'=> 'Модельер-доработчик',
                'rules' => [
                    'maxLength' => 64,
                ],
            ],
            'model_type' => [
                'name'=> 'Тип модели',
                'rules' => [
                    'required' => true,
                    'maxLength' => 64,
                ],
            ],
            'size_range' => [
                'name'=> 'Размерный ряд',
                'rules' => [
                    'maxLength' => 128,
                ],
            ],
            'model_weight' => [
                'name'=> 'Вес',
                'rules' => [
                    'required' => true,
                    'double' => true,
                    'min' => 0.05,
                    'max' => 100,
                ],
            ],
            'print_cost' => [
                'name'=> 'Стоимость печати',
                'rules' => [
                    'min' => 1,
                    'max' => 1000,
                ],
            ],
            'model_cost' => [
                'name'=> 'Стоимость модели',
                'rules' => [
                    'min' => 1,
                    'max' => 1000,
                ],
            ],
            'description' => [
                'name'=> 'Описание',
                'rules' => [
                    'maxLength' => 2048,
                ],
            ],
            'status' => [
                'name'=> 'Статус',
                'rules' => [
                    'min' => 1,
                    'max' => 106,
                ],
            ],
            // Массивы
            'labels' => [
                'name'=> 'Метки',
                'rules' => [
                    'maxLength' => 64,
                ],
            ],
            'collection' => [
                'name'=> 'Коллекции',
                'rules' => [
                    'required' => true,
                    'type' => 'array',
                    'maxLength' => 128,
                    'countMax' => 10,
                ],
            ],
            'mats' => [
                'name'=> 'Материалы',
                'rules' => [
                    'required' => true,
                    'type' => 'array',
                    'maxLength' => 128,
                    'countMax' => 20,
                ],
            ],
        ];

        if ( $field )
        {
            if ( array_key_exists($field,$this->fieldRules) )
                return $this->fieldRules[$field];
        } else {
            throw new Exception('Field  "' . $field . '" not found in rules!',213);
        }

        return $this->fieldRules;
    }

    public function validateFields( array $fields ) : bool
    {

        return true;
    }

    /**
     * @param string $fieldName
     * @param string $fieldValue
     * @return string
     * @throws Exception
     */
    public function validateField( string $fieldName, string $fieldValue )
    {
        $fieldValue = trim($fieldValue);
        $fieldValue = strip_tags($fieldValue);
        $fieldValue = mysqli_real_escape_string($this->connection, $fieldValue);

        $rules = $this->fieldRules($fieldName);

        $this->validate($fieldValue, $rules);

        return $fieldValue;
    }

    protected function validate( $fieldValue, array $rules ) : bool
    {
        //debugAjax($fieldValue, 'validate: $fieldValue');
        //debugAjax($rules, 'validate: $rules');

        foreach ( $rules['rules'] as $rule => $value )
        {
            switch ($rule)
            {
                case "required":
                    {
                        if ( $value ) // Установлено в true
                            if ( empty($fieldValue) )
                                $this->setErrorText($rule, $rules['name'], $value);
                    } break;
                case "double":
                    {
                        $fieldValue = (double)$fieldValue;
                    } break;
                case "int":
                    {
                        $fieldValue = (int)$fieldValue;
                    } break;
                case "min":
                    {
                        if ( $fieldValue < $value )
                            $this->setErrorText($rule, $rules['name'], $value);
                    } break;
                case "max":
                    {
                        if ( $fieldValue > $value )
                            $this->setErrorText($rule, $rules['name'], $value);
                    } break;
                case "minLength":
                    {
                        if ( mb_strlen($fieldValue) < $value )
                            $this->setErrorText($rule, $rules['name'], $value);
                    } break;
                case "maxLength":
                    {
                        if ( mb_strlen($fieldValue) > $value )
                            $this->setErrorText($rule, $rules['name'], $value);
                    } break;
                case "forbiddenChars":
                    {
                        // проверить каждый символ поля
                        $symbols = $arrChars = preg_split('//u',$fieldValue,-1,PREG_SPLIT_NO_EMPTY);
                        foreach ( $symbols as $symbol )
                        {
                            if ( in_array($symbol, $this->badChars) )
                            {
                                $this->setErrorText($rule, $rules['name'], $value);
                                break;
                            }
                        }
                    } break;
            }
        }

        return true;
    }

    private function setErrorText( string $rule, string $ruleName, $value )
    {
        self::$lastError = $this->rulesErrorText($rule, $ruleName, $value);
        self::$errors[] = self::$lastError;
    }

















}