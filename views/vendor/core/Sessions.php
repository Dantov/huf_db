<?php

namespace Views\vendor\core;



class Sessions
{

    const SESSION_STARTED = TRUE;
    const SESSION_NOT_STARTED = FALSE;

    private $sessionState = self::SESSION_NOT_STARTED;

    private $sessionStock = [];

    public function __construct()
    {
        $this->startSession();
    }

    /**
     * @throws \Exception
     */
    private function __clone()
    {
        throw new \Exception('Can\'t clone sessions', 888);
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        if ( !is_string($name) ) throw new \Exception('Session name must be string type', 589);
        if ( array_key_exists($name, $_SESSION) ) return $_SESSION[$name];
        return false;
    }

    /**
     * @param $name
     * @param $value
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        if ( !is_string($name) || empty($name) ) throw new \Exception('Session name must be string type and not empty', 589);
        $_SESSION[$name] = $value;
    }

    protected function startSession()
    {
        if ($this->sessionState == self::SESSION_NOT_STARTED) {
            $this->sessionState = session_start();
        }
        return $this->sessionState;
    }

    public function destroySession()
    {
        if ( $this->sessionState == self::SESSION_STARTED )
        {
            $this->sessionState = !session_destroy();
            unset( $_SESSION );

            return !$this->sessionState;
        }

        return FALSE;
    }

    public function setKey($name, $value)
    {
        $this->startSession();
        $_SESSION[$name] = $value;
        return true;
    }

    public function getKey($name)
    {
       $this->startSession();
        if ( isset( $_SESSION[$name] ) ) return $_SESSION[$name];
        return false;
    }

    public function hasKey($name) : bool
    {
        $this->startSession();
        return trueIsset( $_SESSION[$name] );
    }

    public function getAll()
    {
        $this->startSession();
        return $_SESSION;
    }

    public function dellKey($name)
    {
        $this->startSession();
        if ( isset( $_SESSION[$name] ) )
        {
            unset($_SESSION[$name]);
            return true;
        }
        return false;
    }

    public function setFlash($key, $value)
    {
        if ( isset($key) && !empty($key) )
        {
            return $this->setKey("_flash_".$key, $value);
        }
        return false;
    }

    public function hasFlash($key)
    {
        if ( !empty($key) )
        {
            $this->startSession();

            if ( isset( $_SESSION["_flash_".$key] ) ) return true;
        }
        return false;
    }

    public function getFlash($key)
    {
        if ( isset($key) && !empty($key) )
        {
            if ($res = $this->getKey("_flash_".$key))
            {
                $this->dellKey("_flash_".$key);
                return $res;
            }
        }
        return false;
    }

}