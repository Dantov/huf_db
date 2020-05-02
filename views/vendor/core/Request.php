<?php

namespace Views\vendor\core;

/**
 * Description of Request
 *
 * @author MA
 */
class Request {
    
    
    public $headers = [];

    public $post = [];
    public $get = [];

    public function __construct()
    {
        if ( $this->isPost() ) $this->post = $_POST;
        if ( $this->isGet() ) $this->get = $_GET;
    }
    
    /**
     * Заполняет массив заголовков
     * @return array - массив заголовков
     */
    public function getHeaders()
    {
        if ($this->headers === null) {

            if (function_exists('getallheaders')) {
                $headers = getallheaders();
                foreach ($headers as $name => $value) {
                    $this->headers[$name] = $value;
                }
            } elseif (function_exists('http_get_request_headers')) {
                $headers = http_get_request_headers();
                foreach ($headers as $name => $value) {
                    $this->headers[$name] = $value;
                }
            } else {
                foreach ($_SERVER as $name => $value) {
                    if (strncmp($name, 'HTTP_', 5) === 0) {
                        $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                        $this->headers[$name] = $value;
                    }
                }
            }
        }
        return $this->headers;
    }
    
    /**
     * Проверяет если данные из Ajax
     * @return bool
     */
    public function isAjax()
    {
//        foreach ( $this->headers as $name => $val ) {
//            if ( $name === 'X-Requested-With' && $val === 'XMLHttpRequest'  ) return true;
//        }
        if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' )
        {
            return true;
        }
        return false;
    }
    
    public function isPost()
    {
        if ( $_SERVER['REQUEST_METHOD'] === 'POST'  ) return true;
        return false;
    }
    
    public function isGet()
    {
        if ( $_SERVER['REQUEST_METHOD'] === 'GET'  ) return true;
        return false;
    }

    public function post($name)
    {
        if ( !$this->isPost() ) return false;
        if ( isset( $_POST[$name] ) ) return $_POST[$name];
        return false;
    }

    public function get($name)
    {
        if ( !$this->isGet() ) return false;
        if ( isset( $_GET[$name] ) ) return $_GET[$name];
        return false;
    }
    
}
