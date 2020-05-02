<?php
namespace Views\_AddEdit\Controllers;

use Views\_AddEdit\Models\AddEdit;
use Views\_AddEdit\Models\Handler;
use Views\_Globals\Controllers\GeneralController;

class AddEditController extends GeneralController
{

    public $stockID = null;
    public $component = null;



    /**
     * @throws \Exception
     */
    public function beforeAction()
    {
        $request = $this->request;
        if ( $request->isAjax() )
        {
            if ( $request->isPost() && $modelsTypeRequest = $request->post('modelsTypeRequest') ) 
            {
                $this->actionVendorCodeNames($modelsTypeRequest);
            }
            if ( $request->isPost() && ( (int)$request->post('paid') === 1) )
            {
                $this->actionPaidRepair($request->post);
            }

            if ( $request->post('deleteFile') )
            {
                $fileName = $request->post('fileName');
                $id = (int)$request->post('id');
                $fileType = $request->post('fileType');
                if ( !empty($fileName) && !empty($id) && !empty($fileType) )
                {
                    $this->actionDeleteFile($id, $fileName, $fileType);
                }
            }

            if ( $request->post('dellPosition') )
            {
                if ( $id = (int)$request->post('id') )
                {
                    $this->actionDeletePosition($id);
                }
            }

            if ( $request->isPost() && $request->post('save') )
            {
                $this->actionFormController();
            }
            exit;
        }



        $id = (int)$this->getQueryParam('id');
        if ( $id < 0 || $id >= 99999 ) $this->redirect('/main/');
        $component = (int)$this->getQueryParam('component');
        if ( $component < 1 || $component > 3 ) $this->redirect('/main/');

        $this->stockID = $id;
        $this->component = $component;
    }

    /**
     * Action of this Controller
     * @throws \Exception
     */
    public function action()
    {
        $id = $this->stockID;
        $component = $this->component;
        $addEdit = new AddEdit($id);
        if ( $id > 0 )
            if ( !$addEdit->checkID($id) )
                $this->redirect('/main/');

        // список разрешенных для ред полей
        $permittedFields = $addEdit->permittedFields();

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

        $dataTables = $addEdit->getDataTables();

        $dataArrays = [
            'imgStat' => $addEdit->getStatLabArr('image'),
            'materialsData' => $this->parseMaterialsData($dataTables),
        ];
        $materialsData = $dataArrays['materialsData']['materials'];
        $coveringsData = $dataArrays['materialsData']['coverings'];
        $handlingsData = $dataArrays['materialsData']['handlings'];

        //$ai_hide = 'hidden';
        $status = '';
        if ( $component === 1 )  // чистая форма
        {
            $this->title = 'Добавить новую модель';

            $haveStl = 'hidden';
            $haveAi = 'hidden';
            $gs_len = 0;
            $vc_Len = 0;

            unset($_SESSION['general_data']);
            $collections_len = [];
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

            /*
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
            }*/

            $ai_file = $addEdit->getAi();
            $haveAi = $ai_file['haveAi'];
            $noAi = $ai_file['noAi'];

            $materials = $addEdit->getMaterials();
            $repairs = $addEdit->getRepairs();
            $images  = $addEdit->getImages();

            $gems  = $addEdit -> getGems();
            $gs_len = $gems['gs_len'];
            $row_gems = $gems['row_gems'];

            $dopVC  = $addEdit -> getDopVC();
            $vc_Len = $dopVC['vc_Len'];
            $row_dop_vc = $dopVC['row_dop_vc'];

            $num3DVC_LI = $addEdit->getNum3dVCLi( $vc_Len, $row_dop_vc );

            // это здесь для внесения первого статуса в таблицу статусов
            $status = $addEdit -> getStatus($_SESSION['general_data']);
            //$statuses = $addEdit->getStatuses($id, $status['stat_name'], $status['stat_date']);

            //  КОСТЫЛЬ!!!!
            // при добавлении новых моделей в stock status заходит ID
            // возьмём этот Id из статусов
            if ( $rowStatus = $addEdit->getStatusCrutch($row['status'],true) ) $row['status'] = $rowStatus;

        }

        //$material = $addEdit->getMaterial($_SESSION['general_data']['model_material']);
        //$covering = $addEdit->getCovering($_SESSION['general_data']['model_covering']);
        if ( empty($status) ) $status = $addEdit -> getStatus($_SESSION['general_data']);
        $labels = $addEdit -> getLabels($_SESSION['general_data']['labels']);

        if ( $component === 3 ) // для добавления комплекта
        {
            $row = $addEdit->getGeneralData();

            $this->title = 'Добавить комплект для ' . $_SESSION['general_data']['number_3d'];

            $noStl = "";
            $haveStl = "hidden";
            $haveAi = 'hidden';
            $ai_hide = 'hidden';


            $gems  = $addEdit->getGems();
            $gs_len = $gems['gs_len'];
            $row_gems = $gems['row_gems'];

            $dopVC  = $addEdit->getDopVC();
            $vc_Len = $dopVC['vc_Len'];
            $row_dop_vc = $dopVC['row_dop_vc'];

            $num3DVC_LI = $addEdit->getNum3dVCLi( $vc_Len, $row_dop_vc );

            $images  = $addEdit->getImages(true);

            $id = 0; // нужен 0 что бы добавилась новая модель

            // на проверку
            $_SESSION['general_data']['status'] = '';
            if ( $rowStatus = $addEdit->getStatusCrutch(1,true) ) $row['status'] = $rowStatus;
            $status = $addEdit->getStatus($row);
        }

        $header = $addEdit->printHeaderEditAddForm($component);

        /* ===== JS includes ===== */
        $this->includeJSFile('ResultModal.js', ['defer','timestamp'] );
        $this->includeJSFile('deleteModal.js', ['defer','timestamp'] );
        $this->includeJSFile('add_edit.js', ['defer','timestamp'] );
        $this->includeJSFile('sideButtons.js', ['defer','timestamp','path'=>_views_HTTP_.'_Globals/js/'] );
        $this->includeJSFile('statusesButtons.js', ['defer','timestamp','path'=>_views_HTTP_.'_Globals/js/'] );
        $this->includeJSFile('submitForm.js', ['defer','timestamp'] );
        if ( $permittedFields['images'] )
        {
            $this->includeJSFile('HandlerFiles.js', ['defer','timestamp'] );
        } else {
            $js = <<<JS
            let handlerFiles;
JS;
            $this->includeJS($js);
        }

        $compact1 = compact([
            'gems_sizesLi','gems_cutLi','gems_namesLi','gems_colorLi','vc_namesLI','num3DVC_LI','materialsData','coveringsData','handlingsData',
        ]);
        /* ===== PHP includes ===== */
        $this->includePHPFile('resultModal.php');
        $this->includePHPFile('deleteModal.php');
        $this->includePHPFile('num3dVC_input_Proto.php', $compact1);
        $this->includePHPFile('protoGemsVC_Rows.php', $compact1);
        $this->includePHPFile('upDownSaveSideButtons.php');
        
        $compact2 = compact([
            'id','component','dellWD','prevPage','collLi','authLi','mod3DLi','jewelerNameLi','modTypeLi','gems_sizesLi','gems_cutLi',
            'gems_namesLi','gems_colorLi','vc_namesLI','permittedFields','ai_hide','status','haveAi','noAi','vc_Len','collections_len',
            'row','stl_file','haveStl','noStl','ai_file','repairs','images','materials', 'gs_len','row_gems','row_dop_vc','num3DVC_LI',
            'dataArrays','materialsData','coveringsData','handlingsData', 'rowStatus','material','covering','labels','header',
        ]);
        return $this->render('addEdit', $compact2);
    }

    protected function parseMaterialsData($dataTables)
    {
        $res = [];
        $materials = [];
        foreach ( $dataTables['metal_color'] as $metalColor )
        {
            $materials['colors'][] = $metalColor['name'];
        }
        $materials['colors'][] = "Нет";

        foreach ( $dataTables['model_material'] as $modelMaterials )
        {
            $namesProbes = explode(';', $modelMaterials['name']);
            $name = $namesProbes[0];
            $probe = $namesProbes[1];

            $materials['names'][$name] = $name;
            if ( !empty( $probe ) )
            {
                $materials['probes'][$name][] = $probe;
            } else {
                $materials['probes'][$name] = [];
            }
        }
        $materials['probes']['none'][] = "Нет";
        $res['materials'] = $materials;

        $coverings = [];
        foreach ( $dataTables['model_covering'] as $modelCoverings )
        {
            $namesCovers = explode(';', $modelCoverings['name']);
            $name = $namesCovers[0];
            $area = $namesCovers[1];

            $coverings['names'][$name] = $name;

            if ( !empty( $area ) )
            {
                $coverings['areas'][$area] = $area;
            }
        }
        $res['coverings'] = $coverings;
        $res['handlings'] = $dataTables['handling'];

        return $res;
    }

    protected function actionVendorCodeNames($modelsTypeRequest)
    {
        $handler = new Handler();
        $handler->connectDBLite();

        $resp = $handler->getModelsByType($modelsTypeRequest);

        $handler->closeDB();

        echo json_encode($resp);
    }

    protected function actionPaidRepair($post)
    {
        $repairPaid = (int)$post['paid'];
        $repairID = (int)$post['repairID'];
        $repairCost = (int)$post['cost'];

        if ($repairPaid === 1 && ($repairID < 0 || $repairID > 983495) ) {
            if ( $repairCost < 0 || $repairCost > 983495 )
            {
                echo json_encode(['error'=>'Wrong incoming data']);
            }
        }

        $handler = new Handler();
        $handler->connectDBLite();
        $handler->setDate( date('Y-m-d') );

        $result['done'] = $handler->setRepairPaid($repairID, $repairCost);

        echo json_encode($result);
    }

    /**
     * @param $modelID integer
     * @param $fileName string
     * @param $fileType string
     * @throws \Exception
     */
    protected function actionDeleteFile( $modelID, $fileName, $fileType )
    {
        if ( $modelID > 0 && $modelID < 999999 )
        {
            $handler = new Handler($modelID);
            $handler->connectDBLite();

            $result['id'] = $modelID;
            $result['fileName'] = $fileName;

            if ( !$result['text'] = $handler->deleteFile($fileName, $fileType) ) $result['text'] = 'Ошибка при удалении файла: ';

            $handler->closeDB();
            $this->session->setKey('re_search', true);

            echo json_encode($result);
        }
    }

    /**
     * @param $modelID
     * @throws \Exception
     */
    protected function actionDeletePosition($modelID)
    {
        if ( $modelID > 0 && $modelID < 999999 )
        {
            $handler = new Handler($modelID);
            $handler->connectToDB();

            $resultDell = $handler->deleteModel();

            $pn = new \Views\_Globals\Models\PushNotice();
            $pn->addPushNotice($modelID, 3, $resultDell['number_3d'], $resultDell['vendor_code'], $resultDell['model_type'], $handler->date, false, $handler->user['fio']);

            $handler->closeDB();
            $this->session->setKey('re_search', true);

            $result['dell'] = $resultDell['dell'];
            echo json_encode($result);
        }



    }

    protected function actionFormController()
    {
        require_once "formController.php";
    }
}