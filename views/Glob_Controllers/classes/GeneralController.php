<?php
require_once "Controller.php";
/**
 */

class GeneralController extends Controller
{

    public $navBar;

    public function __construct($controllerName='')
    {
        $this->accessControl();
        $this->navBarController();

        if ( !empty($controllerName) ) $this->controllerName = $controllerName;
    }

    protected function accessControl()
    {
        session_start();
        if ( isset( $_COOKIE['meme_sessA'] ) )
        {
            if ( !isset($_SESSION['access']) || empty($_SESSION['access']) ) {

                $_SESSION['access'] = $_COOKIE['meme_sessA'];

                foreach ($_COOKIE['assist'] as $key => $value) {
                    $_SESSION['assist'][$key] = $value;
                }
            }
            if ( !isset($_SESSION['user']) || empty($_SESSION['user']) ) {

                foreach ($_COOKIE['user'] as $key => $value) {
                    $_SESSION['user'][$key] = $value;
                }
            }
        }

        $access = isset( $_SESSION['access']) ? $_SESSION['access'] : false;
        if( $access !== true || $_SESSION['assist']['update'] !== 7 ) header("location:". _glob_HTTP_ ."exit.php");
    }

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

        // флаг для удаления ворд сесии, если нажали добавить модель, с этой сессией
        $dell = '';
        if ( isset($_SESSION['fromWord_data']) ) $dell = "&dellWD=1";

        $navBar['searchStyle']='style="margin-left:100px;"';
        $navBar['topAddModel'] = 'hidden';
        $navBar['navbarStatsShow'] = "hidden";
        $navBar['navbarStatsUrl'] = '';

        if ( $navBar['userAccess'] == 1 || $navBar['userAccess'] == 2 )
        {
            $navBar['searchStyle'] = '';
            $navBar['topAddModel'] = '';
            $navBar['navbarStatsUrl'] = _views_HTTP_ . "Statistic/index.php";
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

        require_once _globDIR_ . "/db.php";
        $collections_arr = [];
        $coll_res = mysqli_query($connection, " SELECT * FROM collections ORDER BY name");
        while( $coll_row = mysqli_fetch_assoc($coll_res) ) $collections_arr[] = $coll_row;

        $navBar['collectionList'] = getCollections($collections_arr);

        $this->navBar = $navBar;
    }


}