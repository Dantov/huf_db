<?php
require _globDIR_ . "classes/GeneralController.php";

class OptionsController extends GeneralController
{

    public $title = 'Опции ХЮФ 3Д';


    public function action()
    {
        require_once _viewsDIR_ . 'Options/classes/Options.php';

        $options = new Options($_SERVER);
        $options->connectToDB();

        $thisPage = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        if ( $thisPage !== $_SERVER["HTTP_REFERER"] ) {
            $_SESSION['prevPage'] = $_SERVER["HTTP_REFERER"];
        }

        $PushNoticeCheck = '';
        if ( $_SESSION['assist']['PushNotice'] == 1 ) {
            $PushNoticeCheck = 'checked';
        }
        $bgsImg = $options->scanBGFolder();

        for( $i = 0; $i < count($bgsImg); $i++ )
        {
            if ( $bgsImg[$i]['body'] == $_SESSION['assist']['bodyImg'] ) $bgsImg[$i]['checked'] = 'checked';
        }

        $compacts = compact([
            'PushNoticeCheck','bgsImg'
        ]);
        return $this->render('options', $compacts);
    }
}