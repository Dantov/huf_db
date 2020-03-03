<?php
require_once _globDIR_ . "classes/GeneralController.php";

class AddEditController extends GeneralController
{


    public function action()
    {
        if ( filter_has_var(INPUT_GET, 'id') ) {
            $id = (int)$_GET['id'];
        } else {
            header("location:" . _views_HTTP_ . "index.php");
            exit;
        }

        $component = filter_has_var(INPUT_GET, 'component') ? (int)$_GET['component'] : false;
        $dellWD = filter_has_var(INPUT_GET, 'dellWD') ? (int)$_GET['dellWD'] : false; // старый параметр для стирания предыдущей сесии при чтении Ворд файла

        if ( $component < 1 || $component > 3 )  header("location:" . _views_HTTP_ . "index.php");

        require_once _viewsDIR_.'AddEdit/classes/AddEdit.php';
        $addEdit = new AddEdit($id, $_SERVER);
        $addEdit->connectToDB();
        if ( $id > 0 && !$addEdit->checkID() )  header("location:" . _views_HTTP_ . "index.php");


        $prevPage = $addEdit->setPrevPage();

        $data = $addEdit->getDataLi();
        $collLi        = $data['collections'];
        $authLi        = $data['author'];
        $mod3DLi       = $data['modeller3d'];
        $jewelerNameLi = $data['jeweler_names'];
        $modTypeLi     = $data['model_type'];

        $gems = $addEdit->getGemsLi();
        $gems_sizesLi = $gems['gems_sizes'];
        $gems_cutLi   = $gems['gems_cut'];
        $gems_namesLi = $gems['gems_names'];
        $gems_colorLi = $gems['gems_color'];

        $vc_namesLI = $addEdit->getNamesVCLi();

        // список разрешенных для ред полей
        $permittedFields = $addEdit->permittedFields();

        $ai_hide = 'hidden';
        $status = '';
        if ( $component === 1 )  // чистая форма
        {
            $this->title = 'Добавить новую модель';

            $haveStl = 'hidden';
            $haveAi = 'hidden';
            $gs_len = 0;
            $vc_Len = 0;

            unset($_SESSION['general_data']);
            // удаляем инфу из ворд сесии, если нажали добавить модель, с этой сессией
            if ( $dellWD ) $addEdit->unsetSessions();
            $collections_len = [];

            $wordData = $addEdit -> getWordData();
            $imgFromWord = $wordData['imgFromWord'];
            $stonesFromWord = $wordData['stonesFromWord'];
            $vcDopFromWord = $wordData['vcDopFromWord'];
            $stonesScript = $wordData['stonesScript'];
        }



        if ( $component === 2 )  // значит что мы в форме редактирования
        {
            unset($_SESSION['general_data']);

            $row = $addEdit->getGeneralData();

            $this->title = 'Редактировать ' . $row['number_3d'] . '-' . $row['model_type'];

            $stl_file = $addEdit->getStl();
            $haveStl = $stl_file['haveStl'];
            $noStl = $stl_file['noStl'];

            $collections_len = $_SESSION['general_data']['collection'] = explode(';',$row['collections']);
            // откроем блок для внесения ai файла, если коллекции соответствуют нижеперечисленным
            foreach ( $collections_len as $coll_len )
            {
                switch ( $coll_len )
                {
                    case "Серебро с Золотыми накладками":
                        $ai_hide = '';
                        break;
                    case "Серебро с бриллиантами":
                        $ai_hide = '';
                        break;
                    case "Золото ЗВ":
                        $ai_hide = '';
                        break;
                }
            }

            $ai_file = $addEdit->getAi();
            $haveAi = $ai_file['haveAi'];
            $noAi = $ai_file['noAi'];

            $repairs = $addEdit->getRepairs();
            $showRepairsBlock = $repairs['showRepairsBlock'];
            $repRow_Num = $repairs['repRow_Num'];
            $repRow_date = $repairs['repRow_date'];
            $repRow_descr = $repairs['repRow_descr'];

            $images  = $addEdit->getImages();
            $imgLen  = $images['imgLen'];
            $imgPath = $images['imgPath'];
            $imgStat = $images['imgStat'];

            $gems  = $addEdit -> getGems();
            $gs_len = $gems['gs_len'];
            $row_gems = $gems['row_gems'];

            $dopVC  = $addEdit -> getDopVC();
            $vc_Len = $dopVC['vc_Len'];
            $row_dop_vc = $dopVC['row_dop_vc'];

            $num3DVC_LI = $addEdit -> getNum3dVCLi( $vc_Len, $row_dop_vc );

            // это здесь для внесения первого статуса в таблицу статусов
            $status = $addEdit -> getStatus($_SESSION['general_data']);
            //$statuses = $addEdit->getStatuses($id, $status['stat_name'], $status['stat_date']);

            //  КОСТЫЛЬ!!!!
            // при добавлении новых моделей в stock status заходит ID
            // возьмём этот Id из статусов
            if ( $rowStatus = $addEdit->getStatusCrutch($row['status'],true) ) $row['status'] = $rowStatus;

        }

        $material = $addEdit->getMaterial($_SESSION['general_data']['model_material']);
        $covering = $addEdit->getCovering($_SESSION['general_data']['model_covering']);
        if ( empty($status) ) $status = $addEdit -> getStatus($_SESSION['general_data']);
        $labels = $addEdit -> getLabels($_SESSION['general_data']['labels']);



        if ( $component === 3 ) // для добавления комплекта
        {
            $this->title = 'Добавить комплекд для ' . $_SESSION['general_data']['number_3d'];

            $noStl = "";
            $haveStl = "hidden";
            $haveAi = 'hidden';
            $ai_hide = 'hidden';

            $collections_len = $_SESSION['general_data']['collection'];
            // откроем блок для внесения ai файла, если коллекции соответствуют нижеперечисленным
            foreach ( $collections_len as $coll_len )
            {
                switch ( $coll_len )
                {
                    case "Серебро с Золотыми накладками":
                        $ai_hide = '';
                        $noAi = '';
                        break;
                    case "Серебро с бриллиантами":
                        $ai_hide = '';
                        $noAi = '';
                        break;
                    case "Золото ЗВ":
                        $ai_hide = '';
                        $noAi = '';
                        break;
                }
            }

            $gems  = $addEdit->getGems();
            $gs_len = $gems['gs_len'];
            $row_gems = $gems['row_gems'];

            $dopVC  = $addEdit->getDopVC();
            $vc_Len = $dopVC['vc_Len'];
            $row_dop_vc = $dopVC['row_dop_vc'];

            $num3DVC_LI = $addEdit->getNum3dVCLi( $vc_Len, $row_dop_vc );

            $images  = $addEdit->getImages('sketch');
            $imgLen  = $images['imgLen'];
            $imgPath = $images['imgPath'];
            $imgStat = $images['imgStat'];

            $id = 0; // нужен 0 что бы добавилась новая модель

            // на проверку
            $_SESSION['general_data']['status'] = '';
            $status = $addEdit->getStatus($_SESSION['general_data']);

        }

        $header = $addEdit->printHeaderEditAddForm($component);


        $compact = compact([
            'id','component','dellWD','prevPage','collLi','authLi','mod3DLi','jewelerNameLi','modTypeLi','gems_sizesLi','gems_cutLi',
            'gems_namesLi','gems_colorLi','vc_namesLI','permittedFields','ai_hide','status','haveAi','noAi','vc_Len','collections_len',
            'imgFromWord','stonesFromWord','vcDopFromWord','stonesScript','row','stl_file','haveStl','noStl','ai_file','showRepairsBlock',
            'repRow_Num','repRow_date','repRow_descr','imgLen','imgPath','imgStat','gs_len','row_gems','row_dop_vc','num3DVC_LI',
            'rowStatus','material','covering','labels','header',
        ]);
        return $this->render('addEdit',$compact);
    }
}