<?php
/**
 * User: Admin
 * Date: 30.04.2020
 * Time: 23:42
 */

namespace Views\_Globals\Models;


class SelectionsModel extends General
{
    public $session;

    public function __construct($session)
    {
        parent::__construct();
        $this->session = $session;
    }

    public function selectionModeToggle($selToggle)
    {
        $selectionMode = $this->session->getKey('selectionMode');
        $resp = 0;

        if ( $selToggle === 1 ) {
            $selectionMode['activeClass'] = "btnDefActive";
            $resp = 'on';
        }
        if ( $selToggle === 2 ) {
            $selectionMode['activeClass'] = "";
            $resp = 'off';
        }

        $selectionMode['models'] = [];

        $this->session->setKey('selectionMode', $selectionMode);
        echo json_encode($resp);
        exit;
    }

    public function checkBoxToggle($checkBox)
    {
        $id = (int)$_POST['modelId'];
        $name = isset($_POST['modelName']) ? $_POST['modelName'] : "";
        $type = isset($_POST['modelType']) ? $_POST['modelType'] : "";
        if ( $checkBox === 1 ) {
            $_SESSION['selectionMode']['models'][$id] = array(
                'id' => $id,
                'name' => $name,
                'type' => $type
            );
        }
        if ( $checkBox === 2 ) {
            unset($_SESSION['selectionMode']['models'][$id]);
        }

        $resp['checkBox'] = $checkBox;
        $resp['id'] = $_POST['modelId'];
        $resp['name'] = $_POST['modelName'];
        $resp['type'] = $type;

        echo json_encode($resp);
        exit;
    }

    public function checkSelectedModels()
    {
        echo json_encode($_SESSION['selectionMode']['models']);
        exit;
    }

    /**
     * @throws \Exception
     */
    public function getSelectedModels()
    {
        if ( empty($_SESSION['selectionMode']['models']) )
        {
            echo json_encode('false');
            exit;
        }

        $selectedModels = $_SESSION['selectionMode']['models'];
        unset($_SESSION['foundRow']);

        $orderBy = $_SESSION['assist']['reg'];
        $sortDirect = $_SESSION['assist']['sortDirect'];

        $statQuery = "";
        if ( isset($_SESSION['assist']['regStat']) && $_SESSION['assist']['regStat'] != "Нет" )
        {
            $regStat = $_SESSION['assist']['regStat'];
            $statQuery = "AND status='$regStat'";
        }

        $modelIds = '(';
        foreach( $selectedModels as $model ) $modelIds .= $model['id'] .',';
        $modelIds = trim($modelIds,',') . ')';
        $selectRow = "SELECT * FROM stock WHERE id IN $modelIds $statQuery ORDER BY $orderBy $sortDirect";

        //debug($selectRow);

        $this->connectDBLite();
        $_SESSION['foundRow'] = $this->findAsArray($selectRow);

        //debug($_SESSION['foundRow'],'foundRow=',1);

        $_SESSION['countAmount'] = count($_SESSION['foundRow']);
        $_SESSION['assist']['page'] = 0;
        $_SESSION['assist']['startfromPage'] = 0;
        $_SESSION['re_search'] = false;

        $_SESSION['assist']['collectionName'] = 'Выделенное';
        $_SESSION['assist']['collection_id'] = -1;
    }
}