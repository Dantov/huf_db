<?php
require_once _globDIR_.'classes/GeneralController.php';

class EditController extends GeneralController
{

    public $title = 'Изменить статусы';

    public function action()
    {
        require_once _viewsDIR_.'Edit/classes/Edit.php';

        $edit = new Edit(false, $_SERVER);
        $edit->connectToDB();

        $permittedFields = $edit->permittedFields();
        $prevPage = $edit->setPrevPage();

        $status = $edit->getStatus();

        $header = "Проставить статус для моделей: ";

        $models = $edit->modelsData();

        $compact = compact([
            'prevPage','status','header','models'
        ]);
        
        return $this->render('edit', $compact);
    }

}