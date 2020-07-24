<?php
namespace Views\_AddEdit\Controllers;

use Views\_AddEdit\Models\AddEdit;
use Views\_AddEdit\Models\Handler;
use Views\_Globals\Controllers\GeneralController;
use Views\_Globals\Models\User;

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

        // список разрешенных для ред полей
        $permittedFields = $addEdit->permittedFields();

        $prevPage = $addEdit->setPrevPage();

        $dataTables = $addEdit->getDataTables();

        // Списки добавлений
        $data = $addEdit->getDataLi();
        $collLi        = $data['collections'];
        $authLi        = $data['author'];
        $mod3DLi       = $data['modeller3d'];
        $jewelerNameLi = $data['jeweler'];
        $modTypeLi     = $data['model_type'];
        $gems = $addEdit->getGemsLi();
        $gems_sizesLi = $gems['gems_sizes'];
        $gems_cutLi   = $gems['gems_cut'];
        $gems_namesLi = $gems['gems_names'];
        $gems_colorLi = $gems['gems_color'];
        $vc_namesLI = $addEdit->getNamesVCLi();

        $dataArrays = [
            'imgStat' => $addEdit->getStatLabArr('image'),
            'materialsData' => $this->parseMaterialsData($dataTables),
        ];
        $materialsData = $dataArrays['materialsData']['materials'];
        $coveringsData = $dataArrays['materialsData']['coverings'];
        $handlingsData = $dataArrays['materialsData']['handlings'];

        $modelPrices = [];

        
        if ( $component === 1 )  // чистая форма
        {
            if ( !User::permission('addModel') ) $this->redirect('/main/');
            $this->title = 'Добавить новую модель';
            $haveStl = 'hidden';
            $haveAi = 'hidden';
            $statusesWorkingCenters = $addEdit->getStatus();
            $labels = $addEdit->getLabels();
        }

        if ( $component === 2 )  // редактирование
        {
            if ( $id > 0 )
            if ( !$addEdit->checkID($id) ) $this->redirect('/main/');
            $row = $addEdit->getGeneralData();

            $editPerm = false;
            if ( User::permission('editModel') )
            {
                $editPerm = true;
            } elseif ( User::permission('editOwnModels') ) {
                $userRowFIO = $_SESSION['user']['fio'];
                $authorFIO = $row['author'];
                $modellerFIO = $row['modeller3D'];
                $jewelerName = $row['jewelerName'];
                if ( stristr($authorFIO, $userRowFIO)   !== FALSE ||
                     stristr($modellerFIO, $userRowFIO) !== FALSE ||
                     stristr($jewelerName, $userRowFIO) !== FALSE )
                    $editPerm = true;
            }
            if (!$editPerm) $this->redirect('/main/');

            $this->title = 'Редактировать ' . $row['number_3d'] . '-' . $row['model_type'];

            $complected = $addEdit->getComplected($component);
            $stl_file = $addEdit->getStl();
            $rhino_file = $addEdit->get3dm();
            $ai_file = $addEdit->getAi();

            $materials = $addEdit->getMaterials();
            $repairs = $addEdit->getRepairs();
            $notes = $addEdit->getDescriptions();

            $images  = $addEdit->getImages();
            //debug($images,'',1);
            $mainImage = '';
            foreach ( $images as $image )
            {
                if ( trueIsset($image['main']) )
                {
                    $mainImage = $image['imgPath'];
                    break;
                }
            }

            $gemsRow  = $addEdit -> getGems();
            $dopVCs  = $addEdit -> getDopVC();

            $num3DVC_LI = $addEdit->getNum3dVCLi( $dopVCs );

            $labels = $addEdit->getLabels($row['labels']);

            $statusesWorkingCenters = $addEdit->getStatus($row['status']['id']??0);

            $modelPrices = $addEdit->getModelPrices();
        }


        if ( $component === 3 ) // добавление комплекта
        {
            if ( !User::permission('addComplect') ) $this->redirect('/main/');

            $row = $addEdit->getGeneralData();
            $complected = $addEdit->getComplected($component);

            $this->title = 'Добавить комплект для ' . $row['number_3d'];

            $noStl = "";
            $haveStl = "hidden";
            $haveAi = 'hidden';
            $ai_hide = 'hidden';

            $materials = $addEdit->getMaterials();
            $gemsRow  = $addEdit->getGems();
            $dopVCs  = $addEdit->getDopVC();

            $num3DVC_LI = $addEdit->getNum3dVCLi( $dopVCs );

            $images  = $addEdit->getImages(true);
            $labels = $addEdit->getLabels($row['labels']);

            $id = 0; // нужен 0 что бы добавилась новая модель

            // на проверке
            $row['status'] = $addEdit->getStatusCrutch(1, true);
            $statusesWorkingCenters = $addEdit->getStatus();
        }

        /* ===== JS includes ===== */
        $this->includeJSFile('ResultModal.js', ['defer','timestamp'] );
        $this->includeJSFile('deleteModal.js', ['defer','timestamp'] );
        $this->includeJSFile('add_edit.js', ['defer','timestamp'] );
        $this->includeJSFile('sideButtons.js', ['defer','timestamp','path'=>_views_HTTP_.'_Globals/js/'] );
        $this->includeJSFile('statusesButtons.js', ['defer','timestamp','path'=>_views_HTTP_.'_Globals/js/'] );
        $this->includeJSFile('submitForm.js', ['defer','timestamp'] );
        if ( $permittedFields['files'] )
        {
            $this->includeJSFile('HandlerFiles.js', ['defer','timestamp'] );
            $fileTypes = ["image/jpeg", "image/png", "image/gif"]; //".3dm", ".stl", ".ai"
            if ( $permittedFields['rhino3dm'] && empty($rhino_file) ) $fileTypes[] = ".3dm";
            if ( $permittedFields['stl'] && empty($stl_file) ) $fileTypes[] = ".stl";
            if ( $permittedFields['ai'] && empty($ai_file) ) 
            {
                $fileTypes[] = ".ai";   
                $fileTypes[] = ".aI";   
                $fileTypes[] = ".Ai";   
                $fileTypes[] = ".AI";
            }
            $fileTypes = json_encode($fileTypes,JSON_UNESCAPED_UNICODE);

            $js = <<<JS
            let handlerFiles;
            window.addEventListener('load',function() {
              handlerFiles = new HandlerFiles( document.getElementById('drop-area'),document.getElementById('addImageFiles'),$fileTypes);
            },false);
JS;
            $this->includeJS($js);
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


        $gradingSystem = $addEdit->gradingSystem();
        if ( User::permission('MA_modeller3D') )
        {
            $gradingSystem3D = $addEdit->gradingSystem(1);
            $this->includePHPFile('grade3DModal.php', compact(['gradingSystem3D']) );
        }
        if ( User::permission('modelAccount') ) $this->includeJSFile('gradingSystem.js', ['defer','timestamp'] );


        /*
        // Сделал привязку к конкретной дате, старые модели сделаны до неё не будут проверены на наличие нужных статусов
        // это сделано что бы участки могли принять старые модели в ремонт, в случае чего
        $comp_date =  new \DateTime($row['date']) < new \DateTime("2020-07-01") ? false : true;
        $toShowStatuses = true;
        if ( $comp_date && ($component !== 1) )
        {
            // Что бы предотвратить изменения статусов у модели, если не поставлен статус сдачи пред. участка
            // при постановке нужных статусов, некоторым людям начислятся деньги в кошельке работника

            // user access => status id
            //Пример: 2 => 89 - 3д моделлер может менять статус только после Утверждения дизайна
            $changeStatusesAccess = [ 2 => 89, 3 => 2, 5 => 5, 11 => 89 ];
            if ( User::getAccess() !== 1 )
                $toShowStatuses = array_key_exists(User::getAccess(),$changeStatusesAccess) && $addEdit->isStatusPresent( (int)$changeStatusesAccess[User::getAccess()] );
        }*/

        $toShowStatuses = $addEdit->statusesChangePermission($row['date'], $component);
        /*
        $changeStatusesAccess = [ 2 => 89, 3 => 2, 5 => 5, 11 => 89 ];
        if ( User::getAccess() !== 1 )
            $toShowStatuses = array_key_exists(User::getAccess(),$changeStatusesAccess) && $addEdit->isStatusPresent( (int)$changeStatusesAccess[User::getAccess()] );
*/
        
        $compact2 = compact([
            'id','component','dellWD','prevPage','collLi','authLi','mod3DLi','jewelerNameLi','modTypeLi','gems_sizesLi','gems_cutLi','toShowStatuses',
            'gems_namesLi','gems_colorLi','vc_namesLI','permittedFields','collections_len','mainImage','notes','modelPrices','gradingSystem',
            'row','stl_file','rhino_file','ai_file','repairs','images','materials', 'gemsRow','dopVCs','num3DVC_LI',
            'dataArrays','materialsData','coveringsData','handlingsData', 'statusesWorkingCenters','material','covering','labels','complected',
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
        $result['dell'] = 'Ошибка при удалении!';
        if ( !User::permission('dellModel') )
        {
            echo json_encode($result);
            exit;
        }

        if ( $modelID > 0 && $modelID < 999999 )
        {
            $handler = new Handler($modelID);
            $handler->connectToDB();
            if ( !$handler->checkID($modelID) )
            {
                echo json_encode($result);
                exit;
            }

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

    /**
     * Проверим оценку на зачисление. Что бы не изменять оценки после зачисления
     * @param array $modelPrices
     * @param int $gradeType
     * @return bool
     */
    protected function isCredited( array $modelPrices = [], int $gradeType ) : bool
    {
        //debug($modelPrices,'$modelPrices',1);
        foreach ( $modelPrices as $modelPrice )
        {
            if ( (int)$modelPrice['is3d_grade'] !== $gradeType ) continue;
            if ( $modelPrice['status'] ) return true;
        }
        return false;
    }

}