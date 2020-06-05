<?php
    $id = $_GET['id'];
    if ( $id < 0 || $id > 99999 ) exit('No ID');

	date_default_timezone_set('Europe/Kiev');
	ini_set('max_execution_time',600); // макс. время выполнения скрипта в секундах
	ini_set('memory_limit','256M'); // -1 = может использовать всю память, устанавливается в байтах
	
	include_once(_globDIR_.'db.php');

    $overalProgress = 0;
	$complects_lenght = 9;
	$complectCounter = 0;

    require_once _globDIR_ .'classes/ProgressCounter.php';
    $progress = new ProgressCounter();
    if ( isset($_GET['userName']) && isset($_GET['tabID']) )
    {
        $progress->setProgress($_GET['userName'], $_GET['tabID']);
    }

    //============= counter point ==============//
    $overallProgress = floor(( ++$complectCounter * 100 ) / $complects_lenght);
    $progress->progressCount( (int)$overallProgress );

    require(_viewsDIR_ . 'ModelView/classes/ModelView.php');
    $modelView = new ModelView($id, $_SERVER);

    $row = $modelView->row;
    $coll_id = $modelView->getCollections();
    $matsCovers = $modelView->getModelMaterials();
    $complected = $modelView->getComplects();
    $images = $modelView->getImages();
    $gems = $modelView->getGems();
    $dopVC = $modelView->getDopVC();
    $repairs = $modelView->getRepairs();

    $date = date_create( $row['date'] )->Format('d.m.Y');

	// create new PDF document
    require_once(_vendorDIR_.'TCPDF/tcpdf.php');
	$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('');
	$pdf->SetTitle('');
	$pdf->SetSubject('');
	$pdf->SetKeywords('');

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(10, 3, 5, 5); // это отступы до самого контента минуя хидер и футер

	// set auto page breaks
	$pdf->SetAutoPageBreak(false, 10);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/rus.php')) {
		require_once(dirname(__FILE__).'/lang/rus.php');
		$pdf->setLanguageArray($l);
	}

	// set default font subsetting mode
	$pdf->setFontSubsetting(true);
	// Set font
	// dejavusans is a UTF-8 Unicode font

	$pdf->SetFont('dejavusans', '', 9, '', true);
	// set text shadow effect
	//$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
	
	$pdf->setJPEGQuality(75);
	//$pdf->setCellPaddings(1, 1, 1, 1);
	$pdf->SetFillColor(239, 238, 210);
	
	$pdf->AddPage();
	// ---- //

    //============= counter point ==============//
    $overallProgress = floor(( ++$complectCounter * 100 ) / $complects_lenght);
    $progress->progressCount( (int)$overallProgress );
	
	//------------исходные данные----------------//
	$size_range = trim($row['size_range']);
	
	$W_IMG = 60; // ширина картинки
	$H_IMG = 60; // высота картинки
	$style = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(121,121,121));




	// ---- //
    /*
	$str_mod_cov_arr = explode(";",$row['model_covering']);
	foreach ( $str_mod_cov_arr as &$value ) {
		if ( "Родирование" == $value )     { $g1 = $value;}
		if ( "Золочение" == $value )       { $g2 = $value;}
		if ( "Чернение" == $value )        { $g3 = $value;}
		if ( "Полное" == $value )          { $fill = $value; }
		if ( "Частичное" == $value )       { $fill = $value; }
		if ( "По крапанам" == $value )     { $fill_prongs = $value; }
		if ( "Отдельные части" == $value ) { $among_parts = "Отд.Части"; }
	}
	
	$coma = $fill_prongs ? ", " : "";
	$coma1 = $g2 ? ", " : "";
	$coma2 = $g3 ? ", " : "";
	$str_cov = $g1." ".$fill.$coma.$fill_prongs.$coma1.$g2.$coma2.$g3;
	$str_mod_priv_arr = explode("-",$row['model_covering']);
	if ( $str_mod_priv_arr[1] ) $str_priv = $among_parts.": ".$str_mod_priv_arr[1];
	$pointComa = $str_priv ? "; " : "";
	$str_cov .= $pointComa.$str_priv;
	if ( !$g1 && !$g2 && !$g3 ) $str_cov = "Нет";
	
	$str_material_arr = explode(";",$row['model_material']);
	foreach ( $str_material_arr as &$value ) {
		if ( "585" == $value )          { $g585 = ' '.$value.'&deg;';}
		if ( "750" == $value )          { $g750 = ' '.$value.'&deg;';}
		if ( "Золото" == $value )       { $g = $value;}
		if ( "Серебро" == $value )      { $g = '<span style="background-color:GAINSBORO;">'.$value.'</span>';}
		if ( "Белое" == $value )        { $colorG1 = ', <span style="background-color:AQUAMARINE;">'.$value.'</span>';}
		if ( "Красное" == $value )      { $colorG2 = ', <span style="background-color:LIGHTSALMON;">'.$value.'</span>';}
		if ( "Желтое(евро)" == $value ) { $colorG3 = ', <span style="background-color:gold;">'.$value.'</span>';}
	}
	$str_mat = $g.$g750.$g585.$colorG1.$colorG2.$colorG3;
	*/




	// ---- //
    $mainimg = '';
    $schemeImg = '';
    foreach ( $images as $img_str )
    {
        $imgPath = _stockDIR_ . explode('Stock/',$img_str['img_name'])[1];
        if ( $img_str['scheme'] == 1 ) $schemeImg = $imgPath;
        if ( $img_str['main'] == 1 )   $mainimg =   $imgPath;
    }

    $pictsDir = _webDIR_HTTP_ . 'picts';

    //============= counter point ==============//
    $overallProgress = floor(( ++$complectCounter * 100 ) / $complects_lenght);
    $progress->progressCount( (int)$overallProgress );
	
	// ---- //
	$labelImgDIV = '';
	$arr_labels = explode(";",$row['labels']);
	foreach( $arr_labels as &$value )
	{
		if ( $value == "Срочное!" ) $labelImg[] = '<img height="20" src="'.$pictsDir.'/label_hot.png"/>&nbsp;';
		if ( $value == "Бриллианты" ) $labelImg[] = '<img height="20" src="'.$pictsDir.'/label_Brill.png"/>&nbsp;';
		if ( $value == "Литьё с камнями" ) $labelImg[] = '<img height="20" src="'.$pictsDir.'/label_Swst.png"/>&nbsp;';
		if ( $value == "Эксклюзив" ) $labelImg[] = '<img height="20" src="'.$pictsDir.'/label_exec.png"/>&nbsp;';
        if ( $value == "Эксперимент" ) $labelImg[] = '<img height="20" src="'.$pictsDir.'/label_exper.png"/>&nbsp;';
        if ( $value == "Ремонт" ) $labelImg[] = '<img height="20" src="'.$pictsDir.'/label_repair.png"/>&nbsp;';
        if ( $value == "Размеры в воске" ) $labelImg[] = '<img height="20" src="'.$pictsDir.'/label_waxSize.png"/>&nbsp;';
        if ( $value == "Прямое литьё из Воска" ) $labelImg[] = '<img height="20" src="'.$pictsDir.'/label_FrontSmelt.png"/>&nbsp;';
        if ( $value == "Прямое литьё из Полимера" ) $labelImg[] = '<img height="20" src="'.$pictsDir.'/label_FrontSmeltPoly.png"/>&nbsp;';
	}
	
	if ( count($labelImg?:[]) )
	{
		$len_labelImg = count($labelImg?:[]);
		$labelImgDIV = '<div>';
		for ( $i=1; $i < $len_labelImg+1; $i++ ) {
			$labelImgDIV .= $labelImg[$i-1];
			if ( ( ($i % 2) == 0 ) && ( $len_labelImg != $i ) ) $labelImgDIV .= '<br>';
		}
		$labelImgDIV .= '</div>';
	}
	//------------конец исходные данные----------------//

	
	$header='
		<style>
			td {
				/*border: 1px solid grey;*/
			}
		</style>
		<table cellpadding="2" style="" width="100%">
			<tbody>
				<tr>
					<td width="50%" style="text-align:left;">Фабричный Артикул: <b>'.$row['vendor_code'].'</b></td>
					<td width="50%" style="text-align:right;">Дата: <b>'.$date.'</b></td>
				</tr>
				<tr>
					<td style="text-align:left;">Номер 3Д: <b>'.$row['number_3d'].'-'.$row['model_type'].'</b></td>
					<td style="text-align:right;">В комплекте: <b>'.$complected.'</b></td>
				</tr>
			</tbody>
		</table>
	';
	$pdf->setCellPaddings(0, 0, 0, 0);
	$pdf->writeHTMLCell(195, '', '', '', $header, 0, 1, 0, true, 'C', true);
	$pdf->setCellPaddings(0, 1, 0, 0);
	
	$pdf->Line( 10, 13, 205.5, 13, $style);

//============= counter point ==============//
$overallProgress = floor(( ++$complectCounter * 100 ) / $complects_lenght);
$progress->progressCount( (int)$overallProgress );

$rowspans = 3;

    $matsCoversStr = '';
    foreach ( $matsCovers as $material )
    {
        $part = $material['part'] ? $material['part'] . ': ' : 'Материал изделия: ';
        $handling = $material['handling'] ? ' - ' . $material['handling'].', ' : '' ;

        $area = $material['area'] ? $material['area'] . ': ' : '';
        $covColor = $material['covColor'] ? ' - ' . $material['covColor']  : '' ;

        $matsCoversStr .= '<tr>
                        <td style="text-align:left;"><i>'.$part.'</i></td>
                        <td style="text-align:right;"><b>'.$material['type'].' '.$material['probe'].' '.$material['metalColor'].',</b>'.$handling.'<i> </i><b>'.$material['covering'].' '.$material['area'].'</b>'.$covColor.'</td>
                    </tr>';
        $rowspans++;
    }
    //debug($matsCoversStr,'$matsCoversStr',1);
	

	$size_rangeTR = '';
	if ( !empty($size_range) ) {
		$size_rangeTR = '
			<tr >
				<td style="text-align:left;">Размерный ряд:</td>
				<td style="text-align:right;"><b>'.$size_range.'</b></td>
			</tr>
		';
		$rowspans++;
	}

	$txt = '';
	if ( !empty($gems) ) {
		$gCountTot = count($gems);
		$gCount = 0;
		$txt = '<hr/>Вставки:<br>';
        foreach ( $gems as $row_gems )
        {
			$diam = '';
			$gCount++;
			if ( is_numeric($row_gems['gem_size']) ) $diam = 'Ø';
			if ( $gCount < $gCountTot ) $br = '<br>';
			$txt .= '<b>'.$diam.$row_gems['gem_size']." - ".$row_gems['gem_value']." ".$row_gems['gem_cut']." - ".$row_gems['gem_name']." - ".$row_gems['gem_color']."</b>".$br;
			$br = '';
		}
	}

	if ( !empty($dopVC) ) {
		$gCountTot = count($dopVC);
		$gCount = 0;
		$txt .= '<hr/>';
        foreach ( $dopVC as $row_dop_vc )
        {
			$gCount++;
			if ( $gCount < $gCountTot ) $br = '<br>';
			$txt .= '<span style="background-color: AQUA;">'.$row_dop_vc['vc_names'].': <b>'.$row_dop_vc['vc_link'].'</b> '.$row_dop_vc['vc_descript'].'</span>'.$br;
			$br = '';
		}
	}
	

	$descr = trim($row['description']);
	if (!empty($descr)) {
		$txt .= '<hr/>';
		$txt .= '<span style="background-color: lime;">Примечания: </span><b>'.$row['description'].'</b>';
	}
	if ( !empty($descr) || !empty($dopVC) || !empty($gems) ) {
		$gemsTR = '
			<tr >
				<td colspan="2" style="text-align:left;">'.$txt.'</td>
			</tr>
		';
		$rowspans++;
	}


	$repairsTR = '';
	if ( !empty($repairs) ) {
        foreach ( $repairs as $repRow )
        {
			$repairsTR .= '
				<tr >
					<td colspan="2" style="text-align:left;">
						<span style="background-color: BISQUE;">Ремонт №'.$repRow['rep_num'].' от - '.date_create( $repRow['date'] )->Format('d.m.Y').': </span>
						<span>'.$repRow['repair_descr'].'</span>
					</td>
				</tr>	
			';
			$rowspans++;		
		}
	}
	$pdfImgY = 15;
	if ( !empty($labelImgDIV) ) $pdfImgY += 3;
	$befImgY = $pdf->GetY();
	$pdf->Image($mainimg, 146, $pdfImgY, 61, 60, '', '', '', true, 150, '', false, false, 0, 'CM', false, false);
	$afterImgY = $pdf->getImageRBY();
	$realImgHeight = ($afterImgY - $befImgY)*3.4;

    //============= counter point ==============//
    $overallProgress = floor(( ++$complectCounter * 100 ) / $complects_lenght);
    $progress->progressCount( (int)$overallProgress );

	
	$top_txt = '
		<style>
			td {
				/*border: 1px solid grey;*/
			}
		</style>
		<table cellpadding="2" style="" width="100%">
			<tbody>
				<tr>
					<td width="18%" style="text-align:left;">Автор:</td>
					<td width="52%" style="text-align:right;"><b>'.$row['author'].'</b></td>
					<td width="30%" rowspan="'.$rowspans.'" style="text-align:center;">
						'.$labelImgDIV.'<img height="'.$realImgHeight.'" src="'.$pictsDir.'/10x10.png"/>
					</td>
				</tr>
				<tr>
					<td style="text-align:left;">Коллекция:</td>
					<td style="text-align:right;"><b>&laquo;'.$row['collections'].'&raquo;</b></td>
				</tr>
				<tr>
					<td style="text-align:left;">Вес изделия:</td>
					<td style="text-align:right;"><b>'.$row['model_weight'].' гр.</b></td>
				</tr>'.$matsCoversStr.$size_rangeTR.$gemsTR.$repairsTR.'
			</tbody>
		</table>
	';
	
	$pdf->writeHTMLCell(195, '', '', '', $top_txt, 0, 1, 0, true, 'L', true);
	$pdf->setCellPaddings(0, 2, 0, 0);

//============= counter point ==============//
$overallProgress = floor(( ++$complectCounter * 100 ) / $complects_lenght);
$progress->progressCount( (int)$overallProgress );
	
	//--table 1--//
	$table1 = "
		<style>
			tr {
			}
			td {
				border: 1px solid grey;
			}
		</style>
		<table cellpadding=\"3\" style=\"border: 2px solid grey;\">
					<thead>
						<tr>
							<td width=\"18%\">Участок</td>
							<td>Получил(дата, подпись)</td>
							<td>Сдал(дата, подпись)</td>
							<td width=\"32%\">Примечания(размеры, баллы допуски)</td>
						</tr>
					</thead>
					<tbody class=\"gem_rows_print\">
						<tr>
							<td width=\"18%\">3-Д моделирование</td>
							<td><b>{$row['modeller3D']}</b></td>
							<td>$date</td>
							<td width=\"32%\"></td>
						</tr>
						<tr>
							<td>Доработка модели</td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td>Отд ПДО</td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td>Восковка</td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td>Отд ПДО</td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td>Литейка</td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td>Комплектация</td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td>Монтировка</td>
							<td></td>
							<td></td>
							<td>размеры</td>
						</tr>

						<tr>
							<td>Закрепка</td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td>Кладовая</td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
					</tbody>
				</table>
	";
	$pdf->SetFont('dejavusans', '', 8, '', true);
	$pdf->writeHTMLCell(195, '', '', '', $table1, 0, 1, 0, true, 'L', true);

//============= counter point ==============//
$overallProgress = floor(( ++$complectCounter * 100 ) / $complects_lenght);
$progress->progressCount( (int)$overallProgress );
	
	$tableMiddl = "
		<style>
			tr {
				border-bottom: 1px solid grey;
			}
			td {
			}
		</style>
		<table class=\"tableMid\" cellpadding=\"3\">
			<tbody>
				<tr>
					<td style=\"border-bottom: 1px solid grey;\">
						<b>Причина ремонта:</b>
					</td>
				</tr>
				<tr>
					<td style=\"border-bottom: 1px solid grey;\">&nbsp;</td>
				</tr>
				<tr>
					<td style=\"border-bottom: 1px solid grey;\">&nbsp;</td>
				</tr>
				<tr>
					<td style=\"border-bottom: 1px solid grey;\"><b>закрыть доступ ДА, НЕТ (необходимое обвести кружком)</b></td>
				</tr>
				<tr>
					<td style=\"border-bottom: 1px solid grey;\"><b>ремонт утвердил технолог (дата,подпись)</b></td>
				</tr>
				<tr>
					<td style=\"border-bottom: 1px solid grey;\">после ремонта необходимо перерезать резинку ( накладку, шинку, белое, красное, все )</td>
				</tr>
				<tr>
					<td style=\"border-bottom: 1px solid grey;\">после ремонта необходимость сигнала (кол-во шт) </td>
				</tr>
			</tbody>
		</table>
	";
	
	$pdf->SetFont('dejavusans', '', 9, '', true);
	$pdf->writeHTMLCell(195, '', '', '', $tableMiddl, 0, 1, 0, true, 'L', true);
	
	include_once(_globDIR_.'glob_Variables.php');
	include_once(_globDIR_.'functs.php');
	$modTypeEn = translit($row['model_type'],$alphabet);
	$pdfname = $row['number_3d'].'-'.$modTypeEn.'_runner.pdf';

    //============= counter point ==============//
    $overallProgress = floor(( ++$complectCounter * 100 ) / $complects_lenght);
    $progress->progressCount( (int)$overallProgress );
	
	$pdf->SetFont('dejavusans', '', 8, '', true);
	$pdf->writeHTMLCell(195, '', '', '', $table1, 0, 1, 0, true, 'L', true);
	
	/*
	if ( $schemeImg ) {
		$pdf->AddPage();
		$pdf->Image($schemeImg, 3, 10, 204, '', 'JPG', '', '', true, 150, '', false, false, 0, false, false, false);
		//$pdf->writeHTMLCell(195, '', '', '', $top_txt, 0, 1, 0, true, 'L', true);
	}*/
	
	// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
	$pdf_string = $pdf->Output('pdfname.pdf', 'S');
	if ( !file_exists( _rootDIR_.'Pdfs/') ) mkdir( _rootDIR_.'Pdfs/', 0777, true);
	file_put_contents(_rootDIR_.'Pdfs/'.$pdfname, $pdf_string);

    //============= counter point ==============//
    $progress->progressCount( 100 );

echo json_encode($pdfname);