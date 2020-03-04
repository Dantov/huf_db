<?php

/**
 * Class ProgressCounter
 * Считаем процент выполнения задач, для разных контроллеров
 */


class ProgressCounter
{

    /**
     * массив с прогрессом создания пдф, для сокет сервера
     * @var array
     */
    public $progressResponse = [];

    /**
     * @var string
     * Имя пользов. который начал процесс создания ПДФ
     */
    public $userName = '';

    /**
     * @var string
     * Идентиф. вкладки с которой начат процесс создания ПДФ
     */
    public $tabID = '';

    /**
     * @var int
     */
    public $percent = 0;

    /**
     * @var string - адрес сокет сервера
     */
    public $localSocket = 'tcp://192.168.0.245:1234';
    //public $localSocket = 'tcp://127.0.0.1:1234';

    /**
     *
     */
    public $socketClientResource;

    function __construct()
    {

        $this->progressResponse = [
            'progressBarPercent' => 0,
            'user' => [
                'name'=> '',
                'tabID' => '',
            ],
            'message' => 'progressBarPDF' // флаг о том что идёт создание пдф
        ];
    }

    public function setProgress($userName=false, $tabID=false)
    {
        $this->userName = is_string($userName)?$userName:'';
        $this->tabID = is_string($tabID)?$tabID:'';

        if ( empty($this->userName) || empty($this->tabID) ) return;

        $this->progressResponse['user'] = [
            'name'=> $this->userName,
            'tabID' => $this->tabID,
        ];

        // выключает сообщения об ошибках
        set_error_handler(function(){return true;});
        $this->socketClientResource = @stream_socket_client($this->localSocket, $errNo, $errorMessage);
        restore_error_handler();

    }

    public function progressCount($newPercent)
    {
        if ( !isset($this->socketClientResource) ) return;

        $this->progressResponse['progressBarPercent'] = $newPercent;

        fwrite($this->socketClientResource, json_encode($this->progressResponse));
    }

}