<?php
namespace Views\_Nomenclature\Controllers;

use Views\_Nomenclature\Models\NomenclatureModel;
use Views\_Nomenclature\Models\GradingSystemModel;
use Views\_Nomenclature\Models\UsersModel;

use Views\_Globals\Controllers\GeneralController;

class NomenclatureController extends GeneralController
{
    protected $view = 'nomenclature';
    protected $tab = 1;


    /**
     * @throws \Exception
     */
    public function beforeAction()
    {
        $request = $this->request;

        if ($request->isAjax()) {
            if (trueIsset($request->post('val')) && trueIsset($request->post('tab'))) {
                $row_tab = $request->post('tab');
                $row_id = (int)$request->post('id');
                $dell = (int)$request->post('dell');
                $row_value = $request->post('val');

                if (trueIsset($dell)) $this->actionDell($row_id, $row_value, $row_tab);

                if (trueIsset($row_id)) {
                    $this->edit($row_id, $row_tab, $row_value);
                } else {
                    $this->add($row_value, $row_tab);
                }
            }

            // GS
            if (trueIsset($request->post('gsShow')) && trueIsset($request->post('showGSID')))
            {
                echo json_encode( $this->actionShowGSPos( (int)$request->post('showGSID') ) );
            }
            if (trueIsset($request->post('gsEdit')) && trueIsset($request->post('editGS_ID')))
            {
                echo json_encode( $this->actionEditGSPos() );
            }

            // users
            if (trueIsset($request->post('userShow')) && trueIsset($request->post('showUserID')))
                echo json_encode( $this->actionShowUserPos( (int)$request->post('showUserID') ) );
            if (trueIsset($request->post('userEdit')) && trueIsset($request->post('editUser_ID')) )
                echo json_encode( $this->actionEditUserPos() );
            if ( trueIsset($request->post('userAdd')) )
                echo json_encode( $this->actionAddUserPos() );
            if ( trueIsset($request->post('userDell')) && trueIsset($request->post('userID')) )
                echo json_encode( $this->actionDellUserPos( (int)$request->post('userID') ) );

            exit;
        }

        if ($request->get('tab') === 'gs')
        {
            $this->view = 'grading_system';
            $this->tab = 5; // система оценок
        }
        if ($request->get('tab') === 'users')
        {
            $this->view = 'users';
            $this->tab = 6; // Users
        }
    }


    /**
     * @throws \Exception
     */
    public function action()
    {
        $compact = [];

        switch ($this->tab)
        {
            case 1:
                $compact = $this->actionNom();
                break;
            case 5:
                $compact = $this->actionGS();
                break;
            case 6:
                $compact = $this->actionUsers();
                break;
        }

        return $this->render($this->view, $compact);
    }

    /**
     * Номенклатура
     * @throws \Exception
     */
    protected function actionNom()
    {
        $nom = new NomenclatureModel();

        $nom->permittedFields();

        $compact = $nom->getData();


        $this->includePHPFile('nom_incl.php');
        $this->includeJSFile('nomenclature.js',['defer','timestamp']);

        return $compact;
    }

    /**
     * Система оценок
     * @throws \Exception
     *
     */
    protected function actionGS()
    {
        $gs = new GradingSystemModel();
        $data = $gs->getData();

        $this->includeJSFile('gs.js',['defer','timestamp']);
        $this->includePHPFile('gsEditModal.php');

        return compact('data');
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function actionUsers() : array
    {
        $usersModel = new UsersModel();

        $users = $usersModel->sortUsersLocations();
        $workingCentersDB = $usersModel->getWorkingCentersDB();

        $this->includeJSFile('users.js', ['defer','timestamp']);
        $this->includePHPFile('userEditModal.php',compact(['workingCentersDB']));

        $workingCentersDBJS = json_encode($workingCentersDB,JSON_UNESCAPED_UNICODE);
        $js = <<<JS
        let workingCentersDB = $workingCentersDBJS;
JS;
        $this->includeJS($js);

        return compact('users', 'workingCentersDB');
    }

    /**
     * @param $row_id
     * @param $row_val
     * @param $row_tab
     * @throws \Exception
     */
    protected function actionDell($row_id, $row_val, $row_tab)
    {
        $nom = new NomenclatureModel();
        $arr = $nom->dell($row_id, $row_val, $row_tab);

        echo json_encode($arr);
        exit;
    }

    /**
     * @param $row_id
     * @param $row_tab
     * @param $row_value
     * @throws \Exception
     */
    protected function edit($row_id, $row_tab, $row_value)
    {
        $nom = new NomenclatureModel();
        $arr = $nom->edit($row_id, $row_tab, $row_value);

        echo json_encode($arr);
        exit;
    }

    /**
     * @param $row_value
     * @param $row_tab
     * @throws \Exception
     */
    protected function add($row_value, $row_tab)
    {
        $nom = new NomenclatureModel();
        $arr = $nom->add($row_value, $row_tab);

        echo json_encode($arr);
        exit;
    }

    /**
     * Выдача данных оценки
     * @param int $id
     * @return array
     * @throws \Exception
     */
    protected function actionShowGSPos( int $id ) : array
    {
        $gs = new GradingSystemModel();

        return $gs->getGSRowByID($id);
    }

    /**
     * Редактирование оценки
     * @return array
     * @throws \Exception
     */
    protected function actionEditGSPos() : array
    {
        $request = $this->request;
        $gs = new GradingSystemModel();
        return $gs->editGSPos( $request->post('description'), (float)$request->post('basePercent'), $request->post('examples'), (int)$request->post('editGS_ID'));
    }

    /**
     * Редактирование юзера
     * @return array
     * @throws \Exception
     */
    protected function actionShowUserPos( int $showUserID ) : array
    {
        $usersModel = new UsersModel();

        foreach ( $usersModel->sortUsersLocations() as &$user ) if ( $showUserID == $user['id'] ) return $user;

        return [];
    }
    /**
     * Редактирование юзера
     * @return array
     * @throws \Exception
     */
    protected function actionEditUserPos() : array
    {
        $request = $this->request;

        $usersModel = new UsersModel();
        return $usersModel->editUserData($request->post);
    }

    /**
     * Редактирование юзера
     * @return array
     * @throws \Exception
     */
    protected function actionAddUserPos() : array
    {
        $request = $this->request;

        $usersModel = new UsersModel();
        return $usersModel->addUserData($request->post);
    }

    /**
     * Редактирование юзера
     * @param int $userID
     * @return array
     */
    protected function actionDellUserPos( int $userID ) : array
    {
        $usersModel = new UsersModel();
        return $usersModel->dellUserData( $userID );
    }

}