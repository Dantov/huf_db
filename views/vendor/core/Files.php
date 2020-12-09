<?php
/**
 * Date: 04.12.2020
 * Time: 18:38
 */

namespace Views\vendor\core;

/**
 * Обработка поступивших файлов
 * Singleton
 */
class Files
{

    public static $instance;

    public $files = [];

    protected function __construct()
    {
        if ( $this->has() )
            $this->files = $this->get();
    }

    public static function instance()
    {
        if ( self::$instance instanceof self )
            return self::$instance;

        return self::$instance = new self;
    }

    public function get( string $name='' ) : array
    {
        if ( $name )
            if ( $this->has($name) )
                return $_FILES[$name];

        return $_FILES;
    }

    public function has( string $name='' ) : bool
    {
        if ( $name )
            return isset($_FILES[$name]['name'][0]);

        return !empty($_FILES);
    }

    public function count( string $name='' ) : int
    {
        if ( $name )
            return count( $this->get($name)['name']??[] );

        if ( $this->get() )
        {
            $totalCount = 0;
            foreach ( $this->get() as $files )
                $totalCount += count($files['name']);
            return $totalCount;
        }
        return 0;
    }

    public function copy( string $pathFrom, string $pathTo ) : bool
    {
        $fileNameFrom = basename($pathFrom);
        if ( !$this->check_file_uploaded_name($fileNameFrom) )
            return false;
        if ( !$this->check_file_uploaded_length($fileNameFrom) )
            return false;

        $fileNameTo = basename($pathTo);
        if ( !$this->check_file_uploaded_name($fileNameTo) )
            return false;
        if ( !$this->check_file_uploaded_length($fileNameTo) )
            return false;

        return copy($pathFrom, $pathTo);
    }

    /**
     * @param string $tmpName
     * @param string $destination
     * @param array $allowedExt
     * @return bool
     * @throws \Exception
     */
    public function upload(string $tmpName, string $destination, array $allowedExt = [] ) : bool
    {
        $fileName = basename($destination);
        if ( !$this->check_file_uploaded_name($fileName) )
            //throw new \Exception("NOT check_file_uploaded_name !!! " . $fileName, 13);
            return false;
        if ( !$this->check_file_uploaded_length($fileName) )
            //throw new \Exception("NOT check_file_uploaded_length !!! " . $fileName ." = ". mb_strlen($fileName,"UTF-8"), 14);
            return false;

        if ( $allowedExt )
        {
            $info = new \SplFileInfo($fileName);
            $extension = mb_strtolower( pathinfo($info->getFilename(), PATHINFO_EXTENSION) );

            if ( !in_array( $extension, $allowedExt ) )
                throw new \Exception("NOT !!! ", 15);
                //return false;
        }

        return move_uploaded_file( $tmpName, $destination );
    }

    public function delete( string $destination ) : bool
    {
        if ( file_exists($destination) )
            return unlink($destination);
        return false;
    }

    /**
     * make sure the file name in English characters, numbers and (_-.) symbols, For more protection.
     * @param $filename
     * @return bool
     */
    protected function check_file_uploaded_name($filename) : bool
    {
        return (bool) ((preg_match("`^[-0-9A-Z_\.]+$`i",$filename)) ? true : false);
    }

    /**
     * make sure that the file name not bigger than 250 characters.
     * @param $filename
     * @return bool
     */
    protected function check_file_uploaded_length($filename)
    {
        return ((mb_strlen($filename,"UTF-8") > 225) ? false : true);
    }

    /**
     * Human Understand Array
     * Makes $_FILES[fieldName] array easy to manipulate
     * @param string $name
     * @return array
     */
    public function makeHUA( string $name ) : array
    {
        $sorted = [];

        if ( !$this->has($name) )
            return $sorted;

        $fArr = $this->get($name);
        foreach ( $fArr as $key => $field )
        {
            $cf = count($field);
            for( $i = 0; $i < $cf; $i++ )
                $sorted[$i][$key] = $field[$i];
        }

        return $sorted;
    }

}