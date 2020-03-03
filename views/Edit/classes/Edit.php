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

    public function createlinks()
    {
        $models = !isset($_SESSION['selectionMode']['models']) ? [] : $_SESSION['selectionMode']['models'];
        
        $strModels = '';
        foreach ($models as $model )
        {
            $quer = mysqli_query($this->connection, " SELECT img_name FROM images WHERE pos_id='{$model['id']}' AND main='1' ");
            $img = mysqli_fetch_assoc($quer);
            $number_3d = explode(' | ', $model['name'])[0];
            $strModels .= '<a imgtoshow="' . _stockDIR_HTTP_ . $number_3d.'/'.$model['id'].'/images/'.$img['img_name'].'" href="../ModelView/index.php?id='.$model['id'].'">'.$model['name'].'</a>' . " :: ";
        }
        $strModels = trim($strModels," :: ");
        
        
        return $strModels;
    }

}