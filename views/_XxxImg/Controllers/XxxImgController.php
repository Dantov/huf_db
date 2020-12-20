<?php
namespace Views\_XxxImg\Controllers;


use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
//use PhpOffice\PhpSpreadsheet\Style;

use Views\_Globals\Controllers\GeneralController;
use Views\_Globals\Models\General;


class XxxImgController extends GeneralController
{

    public $title = 'ХЮФ IMGS - ';


    /**
     * @throws \Exception
     */
    public function action()
    {

        $path = _viewsDIR_ . "_XxxImg/includes/123.xls";

        $reader = new Xls();
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($path);


        // Get the value from cell A1
        $cellValue = $spreadsheet->getActiveSheet()->getCell('C4')->getValue();

        $dataArray = $spreadsheet->getActiveSheet()
            ->rangeToArray(
                'B4:D10',     // The worksheet range that we want to retrieve
                NULL,        // Value that should be returned for empty cells
                TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                TRUE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                TRUE         // Should the array be indexed by cell row and cell column
            );

        //debug($dataArray,'$dataArray',1);

        $modelType = '';
        $vc = 0;
        $code77 = 0;
        foreach ( $dataArray as $lineData )
        {
            $modelType = explode(' ', $lineData['B'])[0];
            $vc = (int)$lineData['C'];
            $code77 = (int)$lineData['D'];

            break;
//            debug($modelType,'$modelType');
//            debug($vc,'$vc');
//            debug($code77,'$code77',1);
        }

        $g = new General();
        $g->connectDBLite();
        $sql = "SELECT st.id, st.number_3d, i.img_name FROM stock as st 
                  LEFT JOIN images as i ON st.id = i.pos_id AND i.main='1'
                  WHERE st.vendor_code LIKE '%$vc%' AND st.model_type='$modelType' ";


        $modelData = $g->findOne($sql);

        //$modelData['img_name']
        //$modelData['id']
        //$modelData['number_3d']
        $img_nameCode = $code77;

        debug($modelData,'$img_name',1);

        $compacted = compact([
            'cellValue',
        ]);
        return $this->render('xxxImg', $compacted);
    }
}