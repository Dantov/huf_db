<?php
namespace Views\vendor\core\db;


/*
 * Базовый класс БД
 * соединяет, разъединяет, проверяет подключение.
 */
class DataBase
{
    private static $mysqli;

    protected static $instance;
    protected static $dbConfig;

    private static $host;
    private static $dbname;
    private static $username;
    private static $password;
    private static $charset;

    protected static function getDBConfig($db)
    {
		if ( !is_array($db) ) throw new \Exception('No db config comes!');
        
        if ( !isset($db['dsn']) ) 
            self::$host = $db['dsn'];
        if ( isset($db['dbname']) ) self::$dbname = $db['dbname'];
        if ( isset($db['username']) ) self::$username = $db['username'];
        if ( isset($db['password']) ) self::$password = $db['password'];
        if ( isset($db['charset']) ) self::$charset = $db['charset'];
        
    }

    protected function __construct()
    {

        $this->connect();
    }

    public static function instance()
    {
        if ( !is_object(self::$instance) ) return new self;

        return self::$instance;
    }

    /*
     * попытка подключения к БД
     * return true| array[errors]
     */
    protected function connect()
    {
        if ( self::isConnected() ) return self::$mysqli;

        self::getDBConfig();
        self::$mysqli = new \mysqli(self::$host, self::$username, self::$password, self::$dbname);

        /* проверка соединения */
        if (self::$mysqli->connect_errno) 
        {
            return [
                'message' => "Не удалось подключиться!",
                'error' => self::$mysqli->error,
                'errno' => self::$mysqli->errno,
            ];
        }
        
        if (!self::$mysqli->set_charset(self::$charset)) {
            return [
                'message' => "Ошибка при загрузке набора символов!",
                'error' => self::$mysqli->error,
                'errno' => self::$mysqli->errno,
            ];
        }

        return self::$mysqli;
    }

    /*
     * проверяем есть ли соединение с БД
     */
    public function isConnected()
    {
        if ( !is_object(self::$mysqli) ) return false;

        if (self::$mysqli->ping())
        {
            return true;
        } else {
           return false;
        }
    }

    /*
     * закрываем соед.
     */
    public function closeConnection()
    {
        if ( !self::isConnected() ) return false;

        if ( self::$mysqli->close() ) {
            self::$mysqli = null;
            return true;
        } else {
            return false;
        }
    }

}