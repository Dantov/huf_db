<?php
require _globDIR_ . "classes/GeneralController.php";
/**
 */

class ModelViewController extends GeneralController
{

    public $title = 'ХЮФ 3Д Модель - ';

    public function beforeAction()
    {

    }

    public function action()
    {
        if ( filter_has_var(INPUT_GET, 'id') ) {
            $id = (int)$_GET['id'];
        } else {
            header("location: "._views_HTTP_."index.php");
        }

        if ( isset($_SESSION['id_progr']) ) unset($_SESSION['id_progr']); // сессия id пдф прогресс бара

        //$this->varBlock['activeMenu'] = 'active';

        require(_viewsDIR_ . $this->controllerName.'/classes/ModelView.php');

        $modelView = new ModelView($id, $_SERVER, $_SESSION['user']);
        $modelView->connectToDB();

        $modelView->unsetSessions();

        $modelView->dataQuery();

        $row = $modelView->row;
        $coll_id = $modelView->getCollections();

        $getStl = $modelView->getStl();
        $button3D = $getStl['button3D'];
        $dopBottomScripts = $getStl['dopBottomScripts'];

        $matsCovers = $modelView->getModelMaterials();

        $complStr = $modelView->getComplects();
        $images = $modelView->getImages();
        $mainImg = [];
        foreach ( $images as $image )
        {
            if ( $image['main'] == 1 )
            {
                $mainImg['src'] = $image['img_name'];
                $mainImg['id'] = $image['id'];
                break;
            }
        }

        $labels = $modelView->getLabels($row['labels']);
        //$str_mat = $modelView->getModelMaterial();
        //$str_Covering = $modelView->getModelCovering();
        $gemsTR = $modelView->getGems();
        $dopVCTr = $modelView->getDopVC();


        $rep_Query = $modelView->rep_Query;
        $repairs = [];
        if ( $rep_Query->num_rows > 0 ) while($repRow = mysqli_fetch_assoc($rep_Query)) $repairs[] = $repRow;
        $isView = true;
        $isRepairProto = false;
        $repairs3D = '';
        $repairsJew = '';
        ob_start();
        foreach ( $repairs as $repair )
        {
            if ( !$whichRepair = $repair['which'] ? true : false )
            {
                require _viewsDIR_ . "AddEdit/includes/protoRepair.php";
                $repairs3D .= ob_get_contents();
                ob_clean();
            } else {
                require _viewsDIR_ . "AddEdit/includes/protoRepair.php";
                $repairsJew .= ob_get_contents();
                ob_clean();
            }
        }
        ob_end_clean();


        $stts = $modelView->getStatus($row);
        $stat_name = $stts['stat_name'];
        $stat_date = $stts['stat_date'];
        $stat_class = $stts['class'];
        $stat_title = $stts['title'];
        $stat_glyphi = 'glyphicon glyphicon-' . $stts['glyphi'];

        //debug($stts);

        $statuses = $modelView->getStatuses();

        $stillNo = !empty($row['vendor_code']) ? $row['vendor_code'] : "Нет";

        $ai_file = '';
        foreach ( $coll_id as $coll )
        {
            switch ( $coll['name'] )
            {
                case "Серебро с Золотыми накладками":
                    $ai_file = $modelView->getAi();
                    if (!$ai_file) $ai_file = 'Нет';
                    break;
                case "Серебро с бриллиантами":
                    $ai_file = $modelView->getAi();
                    if (!$ai_file) $ai_file = 'Нет';
                    break;
                case "Золото ЗВ":
                    $ai_file = $modelView->getAi();
                    if (!$ai_file) $ai_file = 'Нет';
                    break;
            }
        }

        $thisPage = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        if ( $thisPage !== $_SERVER["HTTP_REFERER"] ) {
            $_SESSION['prevPage'] = $_SERVER["HTTP_REFERER"];
        }

        $editBtn = false;
        if ( isset($_SESSION['user']['access']) && $_SESSION['user']['access'] > 0 )
        {
            $userAccess = (int)$_SESSION['user']['access'];
            if ( (int)$_SESSION['user']['access'] === 1 || (int)$_SESSION['user']['id'] === 33 ) { // весь доступ
                $editBtn = true;
            }
            if ( (int)$_SESSION['user']['access'] === 2 )  // доступ только где юзер 3д моделлер или автор
            {
                $userRowFIO = $_SESSION['user']['fio'];
                $authorFIO = $row['author'];
                $modellerFIO = $row['modeller3D'];
                if ( stristr($authorFIO, $userRowFIO) !== FALSE || stristr($modellerFIO, $userRowFIO) !== FALSE ) {
                    $editBtn = true;
                }
            }

            if ( (int)$_SESSION['user']['access'] === 3
                || (int)$_SESSION['user']['access'] === 4
                || (int)$_SESSION['user']['access'] === 5
                || (int)$_SESSION['user']['access'] === 6
            ) $editBtn = true;
        }

        $btnlikes = 'btnlikes';
        if ( $modelView->checklikePos() ) $btnlikes = 'btnlikesoff';

        $compacted = compact([
            'id','row','coll_id','getStl','button3D','dopBottomScripts','complStr','images','mainImg', 'labels', 'str_mat','str_Covering','gemsTR',
            'dopVCTr','stts','stat_name','stat_date','stat_class','stat_title','stat_glyphi','statuses','stillNo','ai_file','thisPage','editBtn',
            'btnlikes','repairs3D','repairsJew', 'matsCovers']);

        return $this->render('modelView', $compacted);
    }

}