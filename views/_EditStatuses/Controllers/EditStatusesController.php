<?php
namespace Views\_EditStatuses\Controllers;

use Views\_AddEdit\Models\Handler;
use Views\_EditStatuses\Models\EditStatusesModel;
use Views\_Globals\Controllers\GeneralController;
use Views\_Globals\Models\{
    ProgressCounter, PushNotice, SelectionsModel
};


class EditStatusesController extends GeneralController
{

    public $title = 'Изменить статусы';

    /**
     * @throws \Exception
     */
    public function beforeAction()
    {
        $request = $this->request;
        if ( $request->isAjax() )
        {
            if ( $request->isPost() && $request->post('save') ) $this->actionSaveStatuses();
            exit;
        }
    }

    /**
     * @throws \Exception
     */
    public function action()
    {
        $edit = new EditStatusesModel(false);

        $permittedFields = $edit->permittedFields();
        $prevPage = $edit->setPrevPage();

        $status = $edit->getStatus();

        $header = "Проставить статус для моделей: ";

        $models = $edit->modelsData();

        $compact = compact([
            'prevPage','status','header','models'
        ]);

        $this->includePHPFile('upDownSaveSideButtons.php','','',_viewsDIR_.'_AddEdit/includes/');
        $this->includePHPFile('resultModal.php','','',_viewsDIR_.'_AddEdit/includes/');
        $this->includeJSFile('ResultModal.js', ['defer','timestamp','path'=>_views_HTTP_.'_AddEdit/js/'] );
        $this->includeJSFile('sideButtons.js', ['defer','timestamp','path'=>_views_HTTP_.'_Globals/js/'] );
        $this->includeJSFile('statusesButtons.js', ['defer','timestamp','path'=>_views_HTTP_.'_Globals/js/'] );
        $this->includeJSFile('submitForm.js', ['defer','timestamp'] );

        return $this->render('edit', $compact);
    }

    /**
     * @throws \Exception
     */
    public function actionSaveStatuses()
    {
        $request = $this->request;
        $session = $this->session;
        $selectionMode = $session->getKey('selectionMode');

        $status = $request->post('status');
        $date = $request->post('date');
        if ( empty($selectionMode['models']) || empty($status) || empty($date) )
        {
            $result['done'] = false;
            echo json_encode($result);
            exit;
        }
        $progress = new ProgressCounter();
        if ( isset($_POST['userName']) && isset($_POST['tabID']) )
        {
            $progress->setProgress($request->post('userName'), $request->post('tabID'));
        }

        $pn = new PushNotice();
        $handler = new Handler(false);
        $handler->connectDBLite();

        $creator_name = $_SESSION['user']['fio'];
        $where = "WHERE id IN (";

        foreach ( $selectionMode['models'] as $model )
        {
            $statusT = [
                'pos_id' => $model['id'],
                'status' => $status,
                'creator_name' => $creator_name,
                'UPdate'   => $date
            ];
            $handler->addStatusesTable($statusT);

            $names = explode(' | ', $model['name']);

            //public function addPushNotice($id, $isEdit=1, $number_3d, $vendor_code, $model_type, $date, $status, $creator_name)
            $addPush = $pn->addPushNotice($model['id'], 2, $names[0], $names[1], $model['type'], $date, $status, $creator_name);
            if ( !$addPush )
            {
                $result['addPush'] = 'Error adding push notice';
            } else {
                $result['addPush'] = 'OK';
            }

            $where .= "{$model['id']},";
        }

        $where = trim($where,',');
        $where .= ")";
        $updateRow = "UPDATE stock SET status='$status', status_date='$date' $where";
        $query = $handler->baseSql($updateRow);

        $handler->closeDB();

        if ( $query )
        {
            $result['done'] = 1;
            $selection = new SelectionsModel($session);
            $selection->getSelectedModels();
        } else {
            $result['done'] = "false";
        }

        $progress->progressCount( 100 );

        echo json_encode($result);
        exit;
    }

}