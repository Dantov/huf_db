<?php
	
	date_default_timezone_set('Europe/Kiev');
	ini_set('max_execution_time',600); // макс. время выполнения скрипта в секундах
	ini_set('memory_limit','256M'); // -1 = может использовать всю память, устанавливается в байтах
	
	$overalProgress = 0;
	$id_progr = time();
	$uploaddir = _stockDIR_HTTP_;
	$uploaddirST = _stockDIR_;
	session_start();
	$_SESSION['id_progr'] = $id_progr;
	session_write_close();
	
	require_once( _viewsDIR_ .'Main/classes/CollectPDF.php');
	require_once( _vendorDIR_.'TCPDF/tcpdf.php');
	
	$collectPDF = new CollectPDF($_SERVER, $_SESSION['assist'], $_SESSION['user'], $_SESSION['foundRow'], $_SESSION['searchFor'], $_SESSION['assist']['collectionName'] );
	if ( !$connection = $collectPDF->connectToDB() ) exit;
	
	$add = mysqli_query($connection,  " INSERT INTO progress (
								idd,
								status,
								overalProgress
								) 
								VALUES (
								'$id_progr',
								'Подготовка',
								'$overalProgress'
								) 
	");
	if ( !$add ) {
		printf("Errormessage: %s\n", mysqli_error($connection));
	}
	
	$trans_str = '';
	if ( empty($collectPDF->foundRow) ) 
	{
		$trans_str = $collectPDF->collectionName;
	} else {
	    //костыль. если ищем по дате
        $trans_str = str_ireplace("::", "", $collectPDF->searchFor);
		//$trans_str = $collectPDF->searchFor;
	}
	if ( empty($trans_str) ) $trans_str = 'Выделенное_';
	
	//$trans_str = empty($_SESSION['foundRow']) ? $_SESSION['assist']['collectionName'] : $_SESSION['searchFor'];
	
	$trans_strEN = $collectPDF->translit($trans_str);
	$pdfname = $trans_strEN.'_'.date_create( date('Y-m-d') )->Format('d-m-Y').'.pdf';
	
	if ( empty($collectPDF->getRow()) )
	{
		$collectPDF->getModelsFormStock();
	}
	
	//$row = isset($_SESSION['foundRow']) ? $_SESSION['foundRow'] : $collectPDF->getModelsFormStock();
	//$collectPDF->getModelsFormStock();
	
	// разбиваем модели по комплектам, в многомерный ассоц. массив $complects
	$complects = $collectPDF->countComplects(); 
	$complects_lenght = count($complects); // общее кол-во комплектов
	debug($complects);
	$overalProcesses = $complects_lenght + 1;
	
class HufDB_PDF extends TCPDF
{
    //Page header
    public function Header() {
		$date = date_create( date('Y-m-d') )->Format('d.m.Y');
		$coll_name = '';
		if ( empty($_SESSION['foundRow']) ) 
		{
			$coll_name = 'Коллекция: '.$_SESSION['assist']['collectionName'].'_'.$date;
		} elseif ( !empty($_SESSION['searchFor']) ) {
			 $coll_name = 'Найдено: '. $_SESSION['searchFor'] . ' - ' .$date;
		} else {
			$coll_name = 'Выделенное - '.$date;
		}
		
		//$trans_str = empty($_SESSION['foundRow']) ? 'Коллекция: '.$_SESSION['assist']['collectionName'] : $_SESSION['searchFor'];
		//$coll_name = empty($trans_str) ? 'Выделенное - '.$date : 'Найдено: '. $trans_str . ' - ' .$date;
		
        // Set font
        $this->SetFont('dejavusans', '', 12);
		$this->SetTextColor( 167,167,167 ); // серый
		$this->setTextShadow(array('enabled'=>false, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
        // Title
        $this->Cell(0, 10, $coll_name, 0, false, 'L', 0, '', 0, false, 'M', 'M');
		$this->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
    }
	public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-12);
        // Set font
        $this->SetFont('dejavusans', 'I', 9);
        // Page number
        $this->Cell(308, 10, ''.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// create new PDF document
$pdf = new HufDB_PDF('L', 'mm', 'A4', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Вадим Быков');
$pdf->SetTitle('');
$pdf->SetSubject('');
$pdf->SetKeywords('');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
//$pdf->setHeaderFont(Array(PDF_FONT_NAME_collectPDF, '', PDF_FONT_SIZE_collectPDF));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(10, 10, 5, 10); // это отступы до самого контента минуя хидер и футер
$pdf->SetHeaderMargin(7); // отступ от верхнего края до хидера
$pdf->SetFooterMargin(5); // отступ от нижнего края до футера

// set auto page breaks
$pdf->SetAutoPageBreak(false, 10);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/rus.php')) {
    require_once(dirname(__FILE__).'/lang/rus.php');
    $pdf->setLanguageArray($l);
}
	
// ---------------------------------------------------------
	
	// set default font subsetting mode
	$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.

	$pdf->SetFont('dejavusans', '', 10, '', true);
// set text shadow effect
	$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
	
	$pdf->setJPEGQuality(75);
	$pdf->setCellPaddings(1, 1, 1, 1);
	$pdf->SetFillColor(239, 238, 210);
	$style2 = array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 0));
	$style_Vert = array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 0));
	
	$pdf->AddPage();
	//$pdf->Rect(10, 10, 140, 170, 'D', array(), array());
	
	//------------исходные данные----------------//
	
	$complectCounter = 0;
	$pageIter = 1;         // счетчик отрисованных страниц
	$pageRowsIter = 0;     // счетчик отрисованных строк на всей странице
	$modelsIter = 0;       // счетчик отрисованных моделей в комплекте
	$model_Img_Iter = 0;   // счетчик отрисованных картинок для всех моделей в строке
	
	$max_RowsPerPage = 8;
	$max_RowsPer_Half_Page = 4;
	
	$max_modelsPerRow = 4;
	$max_ImagesPerRow = 4;
	
	// проверять псле каждой отрисованной картинки не заходит ли она за max_ImagesPerRow
	$W_IMG = 34.5; // ширина картинки
	$H_IMG = 34.5; // высота картинки
	
	$Xcell = 11;
	$Info_Cells_X = array($Xcell,$Xcell+10,$Xcell+97); //начальные коорд. 3х ячеек по Х
	$Ycell = 10;         					  //начальные коорд. ячеек по Y
	
	$X_line_bott = 10; //начальные коорд. нижней линии по Х
	$Y_line_bott = 10; //начальные коорд. нижней линии по Y
	
	$X_Img = 11; //начальные коорд. картинки по Х
	$Y_Img = 17; //начальные коорд. картинки по Y
	//------------конец исходные данные----------------//

	mysqli_query($connection, " UPDATE progress SET status='Идет создание PDF документа - $trans_str',filename='$pdfname' WHERE idd='$id_progr' ");

	// верхний (главный) цикл - отрисовывает строки и страницы
	for ( $i = 0; $i <= $complects_lenght; $i++ )
	{
		if (!$complects[$i]['modeller3D']) continue;
		
		if ( $pageRowsIter == $max_RowsPer_Half_Page ) { // печатаем на след полустранице
			$collectPDF -> nextPage_HalfPage( $Xcell, $Ycell, $X_line_bott, $Y_line_bott, $X_Img, $Y_Img, $Info_Cells_X, false, true );
			$pdf->Line(150.5,10,150.5,180, $style_Vert);
		}
		
		if ( $pageRowsIter == $max_RowsPerPage ) { // печатаем на след. странице
		
			$collectPDF -> nextPage_HalfPage( $Xcell, $Ycell, $X_line_bott, $Y_line_bott, $X_Img, $Y_Img, $Info_Cells_X, true );
		
			$pdf->AddPage();
			$pdf->Line(150.5,10,150.5,180, $style_Vert);
			$pageIter++;
			$pageRowsIter = 0;
		}

		$model_Img_Iter = 0; // сбрасываем кол-во отрисованных картинок
		
		$thisVC = !empty($complects[$i]['vendor_code']) ? " - Арт: {$complects[$i]['vendor_code']}" : "";
		$complIterShow++; // счетчик/номер слева вверху строки
		
		$pdf->MultiCell(10, '', $complIterShow.'.',                               0, 'L', 1, 0, $Info_Cells_X[0], $Ycell, true);
		$pdf->MultiCell(87, '', '№3D: '.$complects[$i]['number_3d'].''.$thisVC,    0, 'C', 1, 0, $Info_Cells_X[1], $Ycell, true);
		$pdf->MultiCell(42, '', $complects[$i]['modeller3D'], 0, 'R', 1, 0, $Info_Cells_X[2], $Ycell, true);
		
		//достаем модели из комплекта
		$modelTypes = $complects[$i]['model_type'];
		
		foreach( $modelTypes as $modelId => $imgDopVC )
		{ 
			// $imgDopVC['images'] массив картинок
			$modWeight = $imgDopVC['model_weight'];
			$modStatus = $imgDopVC['status'];
			
			if ( $imgDopVC['model_type'] == 'Серьги' ) {
				foreach( $imgDopVC['dop_VC'] as $k => $v ){
					$det_name = $k;
				}
				unset($k,$v);
				$det = $imgDopVC['dop_VC']['Швенза'];
			}
			
			if ( $imgDopVC['model_type'] == 'Пусеты' ) {
				foreach( $imgDopVC['dop_VC'] as $k => $v ){
					$det_name = $k;
				}
				unset($k,$v);
				$det = $imgDopVC['dop_VC']['Закрутка'];
			}
			
			// рисуем картинки для каждой модели
			for ( $im_c = 0; $im_c < count($imgDopVC['images']); $im_c++ ) {
				
				if ( $model_Img_Iter == $max_ImagesPerRow ) { // переходим на след строку когда картинок ==4 
					$X_Img = ($pageRowsIter >= 4) ? 152 : 11;
					$Y_Img += 42.5;
					$Y_line_bott += 42.5;
					$Ycell += 42.5;

					$pageRowsIter++;
					$model_Img_Iter = 0;
				}
				if ( $pageRowsIter == $max_RowsPer_Half_Page ) { // печатаем на след полустранице
					$Xcell = 151;
					$Info_Cells_X = array($Xcell,$Xcell+10,$Xcell+10+87); // координаты MultiCell по X
					$Ycell = 10; // координаты MultiCell по Y
					$Y_Img = 17;
					$X_line_bott = 150.5;
					$Y_line_bott = 10;
				}
		
				if ( $pageRowsIter == $max_RowsPerPage ) { // печатаем на след. странице
					$collectPDF->nextPage_HalfPage( $Xcell, $Ycell, $X_line_bott, $Y_line_bott, $X_Img, $Y_Img, $Info_Cells_X, true );
					$pdf->AddPage();
					$pageIter++;
					$pageRowsIter = 0;
				}

				$_src = $complects[$i]['number_3d'].'/'.$complects[$i]['model_type'][$modelId]['id'].'/images/'.$imgDopVC['images'][$im_c];
                $img_src = file_exists(_stockDIR_.$_src) ? _stockDIR_HTTP_.$_src : _stockDIR_HTTP_."default.jpg";

				$pdf->Image($img_src, $X_Img, $Y_Img, $W_IMG, $H_IMG, '', '', '', true, 150, '', false, false, 0, true, false, false);
				$X_Img += $W_IMG;
				
				// меняем шрифт для вывода швенз
				$pdf->SetFont('dejavusans', '', 8, '', true); 
				// выводим швензу или закрутку если есть
				if ( isset($det) && $im_c == 0 ) { 
					$pdf->MultiCell(30, '', $det_name.': '.$det, 0, 'C', 0, 0, $X_Img-$W_IMG, $Y_Img+23, true);
				}
				//выведем статус
				//$pdf->SetFillColor(255, 255, 233);
				$pdf->MultiCell(35, '', $modStatus, 0, 'R', 0, 0, $X_Img-$W_IMG, $Y_Img+4, true);
				
				// меняем шрифт для ВЕС!!!
				$pdf->SetFont('dejavusans', '', 9, '', true); 
				// выведем вес
				$pdf->MultiCell(30, '', 'Вес: '.$modWeight, 0, 'L', 0, 0, $X_Img-$W_IMG, $Y_Img, true);
				
				
				$pdf->Line($X_Img,$Y_Img,$X_Img,$Y_Img+34.5, $style_Vert);
				
				$model_Img_Iter++; // посчитали картинку
			}
			
			if ( isset($det) ) unset($det,$det_name);
		}
		$pdf->SetFont('dejavusans', '', 10, '', true);// меняем шрифт обратно
		//нарисовали линию внизу комплекта
		$pdf->Line($X_line_bott,$Y_line_bott+42.5,$X_line_bott+140.5,$Y_line_bott+42.5, $style2);
		
		// добавили коорд. для след. комплекта
		$Y_line_bott += 42.5;
		$Ycell += 42.5;
		$Y_Img += 42.5;
		$X_Img = ($pageRowsIter >= 4) ? 152 : 11; // смотрит на какой части стр. рисовать след картинку



		$pageRowsIter++; // посчитали отрисованный комплект
		$complectCounter++;
		$overalProgress = ceil( ( $complectCounter * 100 ) / $overalProcesses );
		mysqli_query($connection, " UPDATE progress SET overalProgress='$overalProgress' WHERE idd='$id_progr' ");
	
	} // END верхний (главный) цикл 
	
	
	//$pdf->Output($pdfname, 'D');
	
	$pdf_string = $pdf->Output('pdfname.pdf', 'S');
	file_put_contents( _rootDIR_ . 'Pdfs/'.$pdfname, $pdf_string);
	
	//============= counter point ==============//
	mysqli_query($connection, " UPDATE progress SET status='Готово!',overalProgress='100' WHERE idd='$id_progr' ");
	mysqli_close($connection);
	//============= counter point ==============//