<?php
require _globDIR_ . "classes/GeneralController.php";
/**
 */

class ModelViewController extends GeneralController
{

    public $title = 'ХЮФ 3Д Модель - ';

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

        $complStr = $modelView->getComplects();
        $images = $modelView->getImages();
        $labels = $modelView->getLabels($row['labels']);
        $str_mat = $modelView->getModelMaterial();
        $str_Covering = $modelView->getModelCovering();
        $gemsTR = $modelView->getGems();
        $dopVCTr = $modelView->getDopVC();

        $rep_Query = $modelView->rep_Query;

        $stts = $modelView->getStatus($row);
        $stat_name = $stts['stat_name'];
        $stat_date = $stts['stat_date'];
        $stat_class = $stts['class'];
        $stat_title = $stts['title'];

        if ( $stts['glyphi'] == 'glyphicons-ring' ) {
            $stat_glyphi = $stts['glyphi'];
        } else {
            $stat_glyphi = 'glyphicon glyphicon-' . $stts['glyphi'];
        }

        $statuses = $modelView->getStatuses();

        $stillNo = !empty($row['vendor_code']) ? $row['vendor_code'] : "Еще нет";

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
            if (
                (int)$_SESSION['user']['access'] === 3
                || (int)$_SESSION['user']['access'] === 4
                || (int)$_SESSION['user']['access'] === 5
            ) $editBtn = true;
        }

        $btnlikes = 'btnlikes';
        if ( $modelView->checklikePos() ) $btnlikes = 'btnlikesoff';

        $compacted = compact([
            'row','coll_id','getStl','button3D','dopBottomScripts','complStr','images', 'labels', 'str_mat','str_Covering','gemsTR',
            'dopVCTr','stts','stat_name','stat_date','stat_class','stat_title','statuses','stillNo','ai_file','thisPage','editBtn',
            'btnlikes','rep_Query']);

        return $this->render('modelView', $compacted);
    }

}