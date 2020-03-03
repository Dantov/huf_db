<?php
session_start();

require_once  _viewsDIR_ . 'Main/classes/Main.php';
require_once _vendorDIR_ . "autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style;

class ToExcel extends Main
{

    /**
     * массив с прогрессом создания пдф, для сокет сервера
     * @var array
     */
    public $progressResponse = [];

    /**
     * @var string
     * Имя пользов. который начал процесс создания ПДФ
     */
    public $userName = '';

    /**
     * @var string
     * Идентиф. вкладки с которой начат процесс создания ПДФ
     */
    public $tabID = '';

    /**
     * @var int
     */
    public $percent = 0;

    /**
     *
     */
    public $socketClientResource;

    function __construct()
    {
        parent::__construct($_SERVER, $_SESSION['assist'], false, $_SESSION['foundRow']);
        $this->connectToDB();

        $this->progressResponse = [
            'progressBarPercent' => 0,
            'user' => [
                'name'=> '',
                'tabID' => '',
            ],
            'message' => 'progressBarXLS' // флаг о том что идёт создание пдф
        ];
    }

    public function setProgress($userName=false, $tabID=false)
    {
        $this->userName = is_string($userName)?$userName:'';
        $this->tabID = is_string($tabID)?$tabID:'';

        if ( empty($this->userName) || empty($this->tabID) ) return;

        $this->progressResponse['user'] = [
            'name'=> $this->userName,
            'tabID' => $this->tabID,
        ];

        // выключает сообщения об ошибках
        set_error_handler(function(){return true;});
        $this->socketClientResource = @stream_socket_client($this->localSocket, $errNo, $errorMessage);
        restore_error_handler();

    }

    public function progressCount($newPercent)
    {
        if ( !isset($this->socketClientResource) ) return;

        $this->progressResponse['progressBarPercent'] = $newPercent;

        fwrite($this->socketClientResource, json_encode($this->progressResponse));
    }

    public function getBordersArray($switch)
    {
        $borders = [];
        switch ( $switch )
        {
            case "vertical":
                //вертикальные границы ячейки
                $borders = array(
                    'borders' => array(
                        'left' => array(
                            'borderStyle' => Style\Border::BORDER_THICK,
                            'color' => array('rgb' => '000000'),
                        ),
                        'right' => array(
                            'borderStyle' => Style\Border::BORDER_THIN,
                            'color' => array('rgb' => '000000'),
                        ),
                    ),
                );
                break;
            case "verLast":
                $borders = array(
                    'borders' => array(
                        'right' => array(
                            'borderStyle' => Style\Border::BORDER_THICK,
                            'color' => array('rgb' => '000000'),
                        ),
                    ),
                );
                break;
            case "horPerRow":
                $borders = array(
                    'borders' => array(
                        'bottom' => array(
                            'borderStyle' => Style\Border::BORDER_THIN,
                            'color' => array('rgb' => '000000'),
                        ),
                    ),
                );
                break;
        }

        return $borders;
    }

    public function getXlsx()
    {
        // уходим если нет моделей для вывода
        if ( isset($_SESSION['nothing']) )
        {
            debug($_SESSION['nothing']);
            return;
        }

        $workingCenters = $this->getWorkingCentersSorted();
        foreach ( $workingCenters as &$workingCenter )
        {
            $workingCenter['title'] = $workingCenter['descr'];
            $workingCenter['deadline'] = "Срок 1 день (". $workingCenter['perf_day'] . " Артикула/день)";
            $workingCenter['statuses']['start'] = $workingCenter['statuses']['start']['id'];
            $workingCenter['statuses']['end'] = $workingCenter['statuses']['end']['id'];
        }
        unset($workingCenter);
        //debug($workingCenters,'',1);

        $spreadsheet = new Spreadsheet();

        try
        {
            $sheet = $spreadsheet->getActiveSheet();
        } catch (Exception $e) {
            echo 'Ошибка при создании getActiveSheet()' . $e->getMessage();
            exit();
        }

        $startRow = 2;

        $sheet->setCellValue('A'.$startRow, 'Артикул');
        $sheet->setCellValue('B'.$startRow, 'Кол-во');
        $sheet->setCellValue('C'.$startRow, 'Р-Ряд');


        //$columnIndex1, $row1, $columnIndex2, $row2
        $wcCount = count( $workingCenters ); //23
        for ( $columnIndex1 = 4; $columnIndex1 < ($wcCount*2)+4; $columnIndex1++ )
        {
            $columnIndex1Plus = $columnIndex1 + 1;
            try {
                $sheet->mergeCellsByColumnAndRow($columnIndex1,$startRow,$columnIndex1Plus,$startRow);
                $sheet->mergeCellsByColumnAndRow($columnIndex1,$startRow+1,$columnIndex1Plus,$startRow+1);
                $sheet->mergeCellsByColumnAndRow($columnIndex1,$startRow+2,$columnIndex1Plus,$startRow+2);
            } catch (Exception $e) {
                echo "/n Error in mergeCellsByColumnAndRow() " . $e->getMessage() . "/n" . "Line: ". $e->getLine();
            }
        }

        $columnIndex = 4;
        foreach ( $workingCenters as $workingCenter )
        {
            $sheet->setCellValueByColumnAndRow($columnIndex, $startRow, $workingCenter['name']);
            $sheet->setCellValueByColumnAndRow($columnIndex, $startRow+1, $workingCenter['title']);
            $sheet->setCellValueByColumnAndRow($columnIndex, $startRow+2, $workingCenter['deadline']);
            $columnIndex = $columnIndex+2;
        }

        // рисуем Постутило/сдано
        for ( $columnIndex = 4; $columnIndex < ($wcCount*2)+4; $columnIndex++ )
        {
            $sheet->setCellValueByColumnAndRow($columnIndex+0, $startRow+3, 'Поступило');
            $sheet->setCellValueByColumnAndRow($columnIndex+1, $startRow+3, 'Сдано');
            ++$columnIndex;
        }


        $sheet->getRowDimension($startRow)->setRowHeight(20);
        $sheet->getRowDimension($startRow+1)->setRowHeight(22);
        $sheet->getRowDimension($startRow+2)->setRowHeight(15);

        for ( $columnIndex = 4; $columnIndex < ($wcCount*2)+4; $columnIndex++ )
        {
            $sheet->getColumnDimensionByColumn($columnIndex)->setWidth(13);
        }

        $fontStyleTop = array(
            'name'      	=> 'Calibri',
            'size'     	    => 12,
            'bold'      	=> true,
            'italic'    	=> false,
            //'underline' 	=> Style\Font::UNDERLINE_DOUBLE,
            'strike'    	=> false,
            'superScript' 	=> false,
            'subScript' 	=> false,
            'color'     	=> array(
                'rgb' => 'E0FFFF'
            )
        );
        $fontStyleRow3 = [
            'size'  => 9,
            'color' => [
                'rgb' => '2F4F4F'
            ]
        ];

        // кол-во получившихся колонок
        $allAvailableColumns = ($wcCount*2)+3;
        try
        {
            $sheet->getStyleByColumnAndRow(1, $startRow, $allAvailableColumns, $startRow)->getFont()->applyFromArray($fontStyleTop);
            $sheet->getStyleByColumnAndRow(1, $startRow+1, $allAvailableColumns, $startRow+3)->getFont()->applyFromArray($fontStyleRow3);
        } catch (Exception $e) {}


        $borderStyleArray = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
                'horizontal' => array(
                    'borderStyle' => Style\Border::BORDER_THIN,//BORDER_THICK
                    'color' => array('rgb' => '000000'),
                ),
                'vertical' => array(
                    'borderStyle' => Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );

        // Границы 2 - 4 строки
        $sheet->getStyleByColumnAndRow(1, $startRow+1, $allAvailableColumns, $startRow+3)->applyFromArray($borderStyleArray);

        // горизонтальное выравнивание 4х первых строк
        $sheet->getStyleByColumnAndRow(1, $startRow, $allAvailableColumns, $startRow+3)
            ->getAlignment()
            ->setWrapText(true)
            ->setHorizontal(Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(Style\Alignment::VERTICAL_CENTER);

        // фоновый цвет первой строки
        $sheet->getStyleByColumnAndRow(1, $startRow, $allAvailableColumns, $startRow)
            ->getFill()
            ->setFillType(Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('4682B4');

        // фоновый цвет второй строки
        $sheet->getStyleByColumnAndRow(1, $startRow+1, $allAvailableColumns, $startRow+1)
            ->getFill()
            ->setFillType(Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('7FFFD4');

        // фоновый цвет Пост Сдано строки
        $sheet->getStyleByColumnAndRow(4, $startRow+3, $allAvailableColumns, $startRow+3)
            ->getFill()
            ->setFillType(Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('2ae2f8');



        if ( !isset($_SESSION['foundRow']) || empty($_SESSION['foundRow']) )
        {
            $this->getModelsFormStock();
            $collectionName = $_SESSION['assist']['collectionName'];
        } else {
            $collectionName = (int)$_SESSION['assist']['searchIn'] === 1 ? $_SESSION['searchFor'] : $_SESSION['assist']['collectionName'].'" Поиск по :'.$_SESSION['searchFor'].':';
        }

        $sheet->setTitle('Отчёт рабочих центров');

        $ModelRow = $this->getRow();
        $countModelRow = count(is_array( $ModelRow ) ? $ModelRow : [] );

        // Запишем вверху дату и коллекцию
        $topText1 = 'Общее кол-во выведенных изделий:  '.$countModelRow;
        $topText2 = 'Коллекция:  "'. $collectionName.'"';
        $topText3 = 'Дата:  '. date('d.m.Y');
        $sheet->setCellValue('D1', $topText1);
        $sheet->setCellValue('H1', $topText2);
        $sheet->setCellValue('L1', $topText3);
        $sheet->getStyleByColumnAndRow(1, 1, $allAvailableColumns, 1)
            ->getFill()
            ->setFillType(Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('FFFACD');

        $rowS = $startRow+4; // с какой строки начинать
        $rowFILLIndex = $rowS;

        $bottomBorderPerCell = $this->getBordersArray('horPerRow');
        $verticalBordersPerCell = $this->getBordersArray('vertical');
        $verticalBorderLast = $this->getBordersArray('verLast');

        // Внешний цикл выводит строки
        for ( $i = 0; $i < $countModelRow; $i++ )
        {
            $xlsxRow = $this->drawXlsxRow( $ModelRow[$i] );
            $wCenters = $xlsxRow['wCenters'];
            $sizeRange = $xlsxRow['sizeRange'];
            $trFill = $xlsxRow['trFill'];

            // колонки первые 3
            $vendor_code = trim($ModelRow[$i]['vendor_code']);
            $sheet->setCellValueByColumnAndRow(1, $rowS, $vendor_code ?: $ModelRow[$i]['number_3d']);

            // Ссылка
            //$uri = 'http://192.168.0.245/Views/ModelView/index.php?id='.$ModelRow[$i]['id'];
            //$sheet->getCellByColumnAndRow(1, $rowS)->getHyperlink()->setUrl($uri);

            $sheet->getStyleByColumnAndRow(1, $rowS)
                ->getAlignment()
                ->setHorizontal(Style\Alignment::VERTICAL_CENTER)
                ->setVertical(Style\Alignment::VERTICAL_CENTER);

            // горизонтальная черта под первыми тремя столбцами
            $sheet->getStyleByColumnAndRow(1, $rowS,3, $rowS)->applyFromArray( $bottomBorderPerCell );

            $sheet->setCellValueByColumnAndRow(3, $rowS, $sizeRange);

            // в комменте полный список размеров
            if ( !empty($ModelRow[$i]['size_range']) )
            {
                try {
                    $sheet->getCommentByColumnAndRow(3, $rowS)->getText()->createTextRun($ModelRow[$i]['size_range']);
                } catch (Exception $e) {
                    echo "/n". "Error in createTextRun " . $e->getMessage() . "/n";
                }
            }

            //колонки остальные
            $columnIndex = 4; // начиная с 4й колонки
            $borderColumnIndex = $columnIndex;
            //$columnFILLIndex = $columnIndex;


            foreach ( is_array($wCenters)?$wCenters:[] as $wCenterL )
            {
                /* выделить рабочие участки цветами
                $sheet->getStyleByColumnAndRow( $columnFILLIndex, $rowFILLIndex )
                    ->getFill()
                    ->setFillType(Style\Fill::FILL_SOLID )
                    ->getStartColor()
                    ->setRGB('00FFFF');
                $columnFILLIndex = $columnFILLIndex+2;
                */

                $endDate = $wCenterL['end']['date'];

                $sheet->setCellValueByColumnAndRow($columnIndex, $rowS, $wCenterL['start']['date']);
                $columnIndex++;

                if ( $endDate === -1 ) { //просрачено
                    $sheet->setCellValueByColumnAndRow($columnIndex, $rowS, "Просрочено");
                    $sheet->getStyleByColumnAndRow($columnIndex, $rowS)
                        ->getFill()
                        ->setFillType(Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB('3c510c');

                    try {
                        $sheet->getStyleByColumnAndRow($columnIndex, $rowS)
                            ->getFont()
                            ->applyFromArray([
                                'color' => [
                                    'rgb' => 'FFFFFF'
                                ]
                            ]);
                    } catch (Exception $e){ echo "/n Error getStyleByColumnAndRow()". $e->getMessage() . "/n"; }

                } else {
                    $sheet->setCellValueByColumnAndRow($columnIndex, $rowS, $wCenterL['end']['date']);
                }

                // горизонтальная черта под строкой
                $sheet->getStyleByColumnAndRow($borderColumnIndex, $rowS, $columnIndex, $rowS)->applyFromArray( $bottomBorderPerCell );

                // вертикальные рамки на каждый участок
                $sheet->getStyleByColumnAndRow($borderColumnIndex, $rowS)->applyFromArray( $verticalBordersPerCell );
                $borderColumnIndex = $borderColumnIndex+2;

                $columnIndex++;
            }

            // отложено/снято с произв.
            if ( $trFill ) $sheet->getStyleByColumnAndRow(1, $rowS, $allAvailableColumns, $rowS)
                ->getFill()
                ->setFillType(Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('f89891');

            $rowFILLIndex = $rowFILLIndex+2;
            $rowS++;

            $this->progressCount( ceil( ( $i * 100 ) / $countModelRow ) );
        }
        $this->closeDB();

        // рисуем последнюю верт четру
        $sheet->getStyleByColumnAndRow($allAvailableColumns, $startRow+4, $allAvailableColumns, $rowS-1)->applyFromArray( $verticalBorderLast );

        $sheet->getColumnDimensionByColumn(1)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(3)->setAutoSize(true);
        $sheet->getStyleByColumnAndRow(3, $startRow+1, 3, $rowS)
            ->getAlignment()
            ->setHorizontal(Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(Style\Alignment::VERTICAL_CENTER);


        //вертикальные границы ячейки
        $borderBottom = array(
            'borders' => array(
                'top' => array(
                    'borderStyle' => Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );
        $sheet->getStyleByColumnAndRow(1, $rowS, $allAvailableColumns, $rowS)->applyFromArray($borderBottom);


        //$sheet->mergeCells('C1:F1');
        //$sheet->setCellValueByColumnAndRow(2, 2, 'Hello World !22');
        //$sheet->mergeCellsByColumnAndRow(4,1,5,1);

        /*
        $sheet->getStyleByColumnAndRow(1,1)
            ->getAlignment()
            ->setHorizontal(Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(Style\Alignment::VERTICAL_CENTER);
        */

        /*
        $sheet->getStyle('D3:F6')
            ->getFill()
            ->setFillType(Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('FF69B4');
        */

        //$sheet->getStyle('A2:Z2')->applyFromArray($borderStyleArray);

        $writer = new Xlsx($spreadsheet);
        ob_start();
        try {
            $writer->save('php://output');
        } catch (Exception $e) {
            echo json_encode(['error' => ['message'=>$e->getMessage(), 'file'=>$e->getFile(), 'line' => $e->getLine()], 'message'=>'Error in save() XLSX']);
        }

        $xlsData = ob_get_contents();
        ob_end_clean();

        $this->progressCount(100);
        echo json_encode('data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($xlsData));
    }


}