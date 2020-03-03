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

        $prevPage = $edit->setPrevPage();

        $status = $edit->getStatus();

        $header = "Проставить статус для моделей: ";

        $strModels = $edit->createlinks();


        $compact = compact([
            'prevPage','status','header','strModels'
        ]);
        return $this->render('edit', $compact);
    }

}