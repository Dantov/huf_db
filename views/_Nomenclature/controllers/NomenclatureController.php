<?php
namespace Views\_Nomenclature\Controllers;
use Views\_Nomenclature\Models\NomenclatureModel;
use Views\_Globals\Controllers\GeneralController;

class NomenclatureController extends GeneralController
{

    /**
     * @throws \Exception
     */
    public function beforeAction()
    {
        $request = $this->request;

        if ( $request->isAjax() )
        {
            if ( trueIsset($request->post('val')) && trueIsset($request->post('tab')) )
            {
                $row_tab = $request->post('tab');
                $row_id = (int)$request->post('id');
                $dell = (int)$request->post('dell');
                $row_value = $request->post('val');

                if ( trueIsset($dell) ) $this->actionDell($row_id, $row_value, $row_tab);

                if ( trueIsset($row_id) ) {
                    $this->edit($row_id, $row_tab, $row_value);
                } else {
                    $this->add($row_value, $row_tab);
                }
            }
            exit;
        }
    }


    /**
     * @throws \Exception
     */
    public function action()
    {
        $nom = new NomenclatureModel();
        $data = $nom->getData();

        $this->includePHPFile('nom_incl.php');
        $this->includeJSFile('nomenclature.js',['defer','timestamp']);

        return $this->render('nomenclature', $data);
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

}