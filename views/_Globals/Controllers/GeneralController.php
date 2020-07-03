<?php
namespace Views\_Globals\Controllers;
use Views\vendor\core\Controller;
use Views\vendor\core\Cookies;
use Views\vendor\core\Config;

class GeneralController extends Controller
{

    public $currentVersion = '';
    public $navBar;

    /**
     * GeneralController constructor.
     * @param $controllerName
     * @throws \Exception
     */
    public function __construct($controllerName)
    {
        parent::__construct($controllerName);

        $this->currentVersion = Config::get('version');

        $this->accessControl();
        $this->navBarController();

        $wp = _WORK_PLACE_ ? 'true' : 'false';
        $js = <<<JS
            const _WORK_PLACE_ = {$wp};
JS;
        $this->includeJS($js,[],$this->HEAD);
    }

    protected function accessControl()
    {
        $session = $this->session;
        //debug($session,'$session');

        if ( Cookies::getOne('meme_sessA') )
        {
            if ( !$session->getKey('access') )
            {
                $session->setKey('access', Cookies::getOne('meme_sessA') );
                if ( $assistCookies = Cookies::getOne('assist') )
                {
                    $assist = [];
                    foreach ( $assistCookies?:[] as $key => $value) $assist[$key] = $value;
                    $session->setKey('assist', $assist);
                }
            }
            if ( !$session->getKey('user') )
            {
                if ( $userCookies = Cookies::getOne('user') )
                {
                    $user = [];
                    foreach ($userCookies?:[] as $key => $value) $user[$key] = $value;
                    $session->setKey('user', $user);
                }
            }
        }

        //debug($_SESSION,'Session');
        $access = $session->getKey('access');
        $assist = $session->getKey('assist');
        //debug($access,'$access');
        //debug($assist,'$assist',1);

        if( $access !== true || $assist['update'] !== 7 ) $this->redirect('/auth/?a=exit');//header("location:". _glob_HTTP_ ."exit.php");
    }

    /**
     * @throws \Exception
     */
    protected function navBarController()
    {
        $navBar = [];

        $navBar['userid'] = $_SESSION['user']['id'];
        $navBar['userFio'] = $_SESSION['user']['fio'];
        $navBar['userAccess'] = $_SESSION['user']['access'];

        $navBar['glphsd'] = 'user';
        if ( $navBar['userFio'] == 'Участок ПДО' ) $navBar['glphsd'] = 'paperclip';

        $searchIn = (int)$_SESSION['assist']['searchIn'];
        if ( $searchIn === 1 ) $navBar['searchInStr'] = "В Базе";
        if ( $searchIn === 2 ) $navBar['searchInStr'] = "В Коллекции";

        $navBar['searchStyle']='style="margin-left:100px;"';
        $navBar['topAddModel'] = 'hidden';
        $navBar['navbarStatsShow'] = "hidden";
        $navBar['navbarStatsUrl'] = '';

        if ( $navBar['userAccess'] == 1 || $navBar['userAccess'] == 2 )
        {
            $navBar['searchStyle'] = '';
            $navBar['topAddModel'] = '';
            $navBar['navbarStatsUrl'] = _rootDIR_HTTP_ . "Statistic/";
            $navBar['navbarStatsShow'] = "";
        }

        $navBar['navbarDevShow'] = 'hidden';
        $navBar['navbarDevUrl'] = '';
        if ( $navBar['userid'] == 1 || $navBar['userid'] == 4 ) //быков дзюба
        {
            $navBar['navbarDevShow'] = '';
            $navBar['navbarDevUrl'] = _rootDIR_HTTP_ . 'hufdb-new';
        }

        function getCollections($coll_res)
        {
            $collectionListDiamond = [];
            $collectionListGold = [];
            $collectionListSilver = [];
            $collectionOther = [];

            foreach( $coll_res as &$collection )
            {
                $haystack = mb_strtolower($collection['name']);

                if ( stristr( $haystack, 'сереб' ) || stristr( $haystack, 'silver' ) )
                {
                    $collectionListSilver[ $collection['id'] ] = $collection['name'];
                    continue;
                }
                if ( stristr( $haystack, 'золото' ) || stristr( $haystack, 'невесомость циркон' ) || stristr( $haystack, 'невесомость с ситалами' ) || stristr( $haystack, 'gold' ) )
                {
                    $collectionListGold[$collection['id']] = $collection['name'];
                    continue;
                }
                if ( stristr( $haystack, 'брилл' ) || stristr( $haystack, 'diam' ) )
                {
                    $collectionListDiamond[$collection['id']] = $collection['name'];
                    continue;
                }
                $collectionOther[$collection['id']] = $collection['name'];
            }

            $res['silver'] = $collectionListSilver;
            $res['gold'] = $collectionListGold;
            $res['diamond'] = $collectionListDiamond;
            $res['other'] = $collectionOther;

            return $res;
        }

        $general = new \Views\_Globals\Models\General();
        $general->connectDBLite();

        $collections_arr = $general->findAsArray(" SELECT id,name FROM service_data WHERE tab='collections' ORDER BY name ");

        $navBar['collectionList'] = getCollections($collections_arr);

        $this->navBar = $navBar;
    }


}