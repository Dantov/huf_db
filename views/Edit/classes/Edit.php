<?php
require_once(_viewsDIR_ . 'AddEdit/classes/AddEdit.php');
class Edit extends AddEdit
{
    public function setPrevPage()
    {
        $pp = '';
        $thisPage = 'http://'.$this->server['HTTP_HOST'].$this->server['REQUEST_URI'];
        if ( $thisPage !== $this->server["HTTP_REFERER"] ) {
            $_SESSION['prevPage'] = $this->server["HTTP_REFERER"];
            $pp = $_SESSION['prevPage'];
        }
        return $pp;
    }

    public function getStatus($row=[], $selMode = '')
    {
        return parent::getStatus($row, 'selectionMode');
    }

    public function modelsData()
    {
        $selectedModels = !isset($_SESSION['selectionMode']['models']) ? [] : $_SESSION['selectionMode']['models'];
        
        // debug($selectedModels,'models');
        $ids = '(';
        foreach ($selectedModels as $model ) $ids .= $model['id'] . ',';
        $ids = trim($ids,',') . ')';

        $images = $this->findAsArray(" SELECT img_name,pos_id FROM images WHERE main='1' AND pos_id IN $ids ");
        $stockModels = $this->findAsArray(" SELECT * FROM stock WHERE id IN $ids ");

        // debug($images,'images');
        // debug($stockModels,'stockModels');

        foreach ($stockModels as &$stockModel )
        {
            $modelID = $stockModel['id'];
            $statusID = $stockModel['status'];
            foreach ($images as $image )
            {
                if ( $image['pos_id'] === $modelID ) {
                    $stockModel['img_name'] = $image['img_name'];
                    //continue 2;
                }
            }
            foreach ($this->statuses as $status )
            {
                if ( $status['id'] === $statusID ) {
                    $stockModel['status'] = $status;
                    continue 2;
                }
            }
        }

        //debug($this->statuses,'statuses');
        //debug($stockModels,'stockModels',1);
 
        return $stockModels;
    }

}