<?php
namespace Views\_Nomenclature\Controllers;
use Views\_Nomenclature\Models\NomenclatureModel;
use Views\_Globals\Controllers\GeneralController;

class NomenclatureController extends GeneralController
{

    public function beforeAction()
    {
        $request = $this->request;

        if ( $request->isAjax() )
        {
            if ( trueIsset($request->post('val')) && trueIsset($request->post('coll')) )
            {
                $quer_coll = $request->post('coll');
                $quer_id = (int)$request->post('id');
                $dell = (int)$request->post('dell');
                $quer_val = $request->post('val');

                if ( trueIsset($dell) ) $this->actionDell($quer_coll, $quer_id, $dell, $quer_val);
                if ( trueIsset($quer_id) ) {
                    $this->edit($quer_coll, $quer_id, $dell, $quer_val);
                } else {
                    $this->add($quer_coll, $quer_id, $dell, $quer_val);
                }
            }
            exit;
        }
    }

    public function action()
    {
        $nom = new NomenclatureModel();
        $data = $nom->getData();

        $this->includePHPFile('nom_incl.php');
        $this->includeJSFile('nomenclature.js',['defer','timestamp']);

        return $this->render('nomenclature', $data);
    }

    protected function actionDell($quer_coll, $quer_id, $dell, $quer_val)
    {
        $nom = new NomenclatureModel();
        $arr = $nom->dell($quer_coll, $quer_id, $dell, $quer_val);

        echo json_encode($arr);
        exit;
    }
    protected function edit($quer_coll, $quer_id, $dell, $quer_val)
    {
        $nom = new NomenclatureModel();
        $arr = $nom->edit($quer_coll, $quer_id, $dell, $quer_val);

        echo json_encode($arr);
        exit;
    }
    protected function add($quer_coll, $quer_id, $dell, $quer_val)
    {
        $nom = new NomenclatureModel();
        $arr = $nom->add($quer_coll, $quer_id, $dell, $quer_val);

        echo json_encode($arr);
        exit;
    }

}