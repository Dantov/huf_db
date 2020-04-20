<?php
    $id = (int)$_GET['id'];
	if ( $id < 0 || $id > 999999 ) exit('wrong ID');

	date_default_timezone_set('Europe/Kiev');
	ini_set('max_execution_time',600); // макс. время выполнения скрипта в секундах
	ini_set('memory_limit','256M'); // -1 = может использовать всю память, устанавливается в байтах

    require_once _globDIR_ .'classes/ProgressCounter.php';
    $progress = new ProgressCounter();
    if ( isset($_GET['userName']) && isset($_GET['tabID']) )
    {
        $progress->setProgress($_GET['userName'], $_GET['tabID']);
    }

    $overallProgress = 0;
    $complects_lenght = 11;
    $complectCounter = 0;

    //============= counter point ==============//
    $overallProgress = ceil(( ++$complectCounter * 100 ) / $complects_lenght);
    $progress->progressCount( $overallProgress );

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
    require_once _vendorDIR_.'TCPDF/tcpdf.php';
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
	
	$pdf->setJPEGQuality(75);
	$pdf->SetFillColor(239, 238, 210);
	
	$pdf->AddPage();
	
	//============= counter point ==============//
    $overallProgress = ceil(( ++$complectCounter * 100 ) / $complects_lenght);
    $progress->progressCount( $overallProgress );

	//------------исходные данные----------------//
	
	// $W_IMG = 60; // ширина картинки
	// $H_IMG = 60; // высота картинки
	// $style = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(121,121,121));


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
	$str_cov = $g1." ".$fill.$coma.$fill_prongs." ".$coma1.$g2." ".$coma2.$g3;
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



	//============= counter point ==============//
    $overallProgress = ceil(( ++$complectCounter * 100 ) / $complects_lenght);
    $progress->progressCount( $overallProgress );

    $pictsDir = _webDIR_HTTP_ . 'picts/'; 

	$labelImg = '';
	$arr_labels = explode(";",$row['labels']);
	$executive = false;
	$materialMM = 'Бронза';
	foreach( $arr_labels as &$value ) {
		if ( $value == "Срочное!" ) $labelImg .= '<img height="20" src="'.$pictsDir.'label_hot.png"/>&nbsp;';
		if ( $value == "Бриллианты" ) $labelImg .= '<img height="20" src="'.$pictsDir.'label_Brill.png"/>&nbsp;';
		if ( $value == "Литьё с камнями" ) $labelImg .= '<img height="20" src="'.$pictsDir.'label_Swst.png"/>&nbsp;';
        if ( $value == "Размеры в воске" ) $labelImg .= '<img height="20" src="'.$pictsDir.'label_waxSize.png"/>&nbsp;';
        if ( $value == "Прямое литьё из Воска" )
        {
            $labelImg .= '<img height="20" src="'.$pictsDir.'label_FrontSmelt.png"/>&nbsp;';
            $materialMM = '';
        }
        if ( $value == "Прямое литьё из Полимера" )
        {
            $labelImg .= '<img height="20" src="'.$pictsDir.'label_FrontSmeltPoly.png"/>&nbsp;';
            $materialMM = '';
        }

		if ( $value == "Эксклюзив" )
		{
			$executive = true;
			$labelImg .= '<img height="20" src="'.$pictsDir.'label_exec.png"/>&nbsp;';
		}
        if ( $value == "Эксперимент" ) $labelImg .= '<img height="20" src="'.$pictsDir.'label_exper.png"/>&nbsp;';
        if ( $value == "Ремонт" ) $labelImg .= '<img height="20" src="'.$pictsDir.'label_repair.png"/>&nbsp;';
	}
	$labelImgDIV = '';
	if ( $labelImg ) {
		$labelImgDIV .= '<span>';
		$labelImgDIV .= $labelImg;
		$labelImgDIV .= '</span>';
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
	$pdf->SetFont('dejavusans', '', 9, '', true);
	$pdf->setCellPaddings(0, 0, 0, 0);
	$pdf->writeHTMLCell(195, '', '', '', $header, 0, 1, 0, true, 'C', true);
	$pdf->setCellPaddings(0, 1, 0, 0);
	
	//============= counter point ==============//
    $overallProgress = ceil(( ++$complectCounter * 100 ) / $complects_lenght);
    $progress->progressCount( $overallProgress );

	$rowspans = 5;

	$gems_tr = '';
	if ( !empty($gems) ) {
		$gCountTot = count($gems);
		$rowspans += $gCountTot;
		foreach ( $gems as $row_gems )
		{
			$diam = '';
			if ( is_numeric($row_gems['gem_size']) ) $diam = 'Ø';
			$gems_tr .= '
			<tr>
				<td><b>'.$diam.$row_gems['gem_size'].' - '.$row_gems['gem_value'].'</b></td>
				<td><b>'.$row_gems['gem_name'].' '.$row_gems['gem_cut'].'</b></td>
				<td><b>'.$row_gems['gem_color'].'</b></td>
			</tr>
			';
		}
	}


    $size_range = trim($row['size_range']);
	$size_rangeTR = '';
	if ( !empty($size_range) ) {
		$size_rangeTR = '
			<tr >
				<td style="text-align:left;">Размерный ряд:</td>
				<td style="text-align:left;"><b>'.$size_range.'</b></td>
				<td ></td>
			</tr>
		';
		$rowspans++;
	}




	// -- создание картинок -- //
	$sketchimg = '';
	$mainimg = '';
	$onbodyimg = '';
	$imgPath = _stockDIR_ . $row['number_3d'].'/'.$id.'/images/';
	foreach ( $images as $img_str )
	{
        $imgPath = _stockDIR_ . explode('Stock/',$img_str['img_name'])[1];
		if ( $img_str['sketch'] == 1 ) $sketchimg = $imgPath;
		if ( $img_str['main'] == 1 )   $mainimg =   $imgPath;
		if ( $img_str['onbody'] == 1 ) $onbodyimg = $imgPath;
	}
	$realImgHeight = 100; // если нет эскиза - высота блока = 100пикс
	if ( !empty($sketchimg) ) {
		$befImgY = $pdf->GetY();
		$pdf->Image($sketchimg, 11, 21, 57, 50, '', '', '', true, 150, '', false, false, 0, 'CM', false, false);
		$afterImgY = $pdf->getImageRBY();
		$realImgHeight = ($afterImgY - $befImgY)*3.2;
	}
	
	//============= counter point ==============//
    $overallProgress = ceil(( ++$complectCounter * 100 ) / $complects_lenght);
    $progress->progressCount( $overallProgress );


    $matsCoversStr = '';
    foreach ( $matsCovers as $material )
    {
        $part = $material['part'] ? $material['part'] . ': ' : '';
        $handling = $material['handling'] ? ' - ' . $material['handling'] : '' ;

        $area = $material['area'] ? $material['area'] . ': ' : '';
        $covColor = $material['covColor'] ? ' - ' . $material['covColor']  : '' ;
        $matsCoversStr .= '
        <tr>
            <td colspan="2"><i>'.$part.'</i><b>'.$material['type'].' '.$material['probe'].' '.$material['metalColor'].'</b>'.$handling.'</td>
            <td><i>'.$area.'</i><b>'.$material['covering'].'</b>'.$covColor.'</td>
        </tr>
        ';
    }

	//--table 1--//
	$table1 = '
		<style>
			td {
				border: 1px solid grey;
			}
			table {
				border: 2px solid #333;
			}
		</style>
		<table cellpadding="2" width="100%">
			<tbody>
				<tr>
					<td width="30%" style="text-align:left;">Эскиз</td>
					<td width="70%" colspan="3" style="text-align:center;">Коллекции <b>&laquo;'.$row['collections'].'&raquo;</b></td>
				</tr>
				<tr>
					<td rowspan="'.$rowspans.'" style="text-align:center;"><img height="'.$realImgHeight.'" src="'.$pictsDir.'10x10.png"></td>
					<td colspan="2" width="35%" style="text-align:center;">Вставки</td>
					<td width="35%" style="text-align:center;">Цвет</td>
				</tr>
				'.$gems_tr.'
				<tr><td colspan="3" style="text-align:center;">Материалы</td></tr>
				'.$matsCoversStr.'
				<tr>
					<td>Вид модели</td>
					<td ><b>'.$row['model_type'].'</b></td>
					<td>Вес: <b>'.$row['model_weight'].' гр.</b></td>
				</tr>
				'.$size_rangeTR.'
				<tr>
					<td colspan="3">'.$labelImgDIV.'</td>
				</tr>
			</tbody>
		</table>
	';
	$pdf->SetFont('dejavusans', '', 8, '', true);
	$pdf->writeHTMLCell(195, '', '', '', $table1, 0, 1, 0, true, 'L', true);
	//--END table 1 --//

	//============= counter point ==============//
    $overallProgress = ceil(( ++$complectCounter * 100 ) / $complects_lenght);
    $progress->progressCount( $overallProgress );

	//--table 2--//
	$table2 = '
		<style>
			td {
				border: 1px solid grey;
			}
			table {
				border: 2px solid #333;
			}
		</style>
		<table cellpadding="3">
			<thead>
				<tr>
					<td>Автор</td>
					<td>3D Модельер</td>
					<td>3D Контроль</td>
					<td>Технолог</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><b>'.$row['author'].'</b><br/>&nbsp; </td>
					<td><b>'.$row['modeller3D'].'</b><br/>&nbsp; </td>
					<td><b>Быков В.А.</b><br/>&nbsp; </td>
					<td><b>Занин В.</b><br/>&nbsp; </td>
				</tr>

			</tbody>
		</table>
	';
	
	$pdf->setCellPaddings(0, 2, 0, 0);
	$pdf->SetFont('dejavusans', '', 9, '', true);
	$pdf->writeHTMLCell(195, '', '', '', $table2, 0, 1, 0, true, 'L', true);

	//============= counter point ==============//
    $overallProgress = ceil(( ++$complectCounter * 100 ) / $complects_lenght);
    $progress->progressCount( $overallProgress );

	$lastTableY = $pdf->GetY();
	
	// -- создание картинок -- //
	$imagesTD = '';
	$imgcolspan = 0;
	$widthMainImg = 96;
	if ( !empty($onbodyimg) ) {
		$widthMainImg = 48;
		$befImgY = $pdf->GetY();
		$pdf->Image($onbodyimg, 58.5, $lastTableY+8, 48, 48, '', '', 'C', true, 150, '', false, false, 0, 'CM', false, false);
		$afterImgY = $pdf->getImageRBY();
		$realImgHeight = ($afterImgY - $befImgY)*3.5;
		
		$imagesTD .='<td style="text-align:center;"><img height="'.$realImgHeight.'" src="'.$pictsDir.'10x10.png"></td>';
		$imgcolspan++;
	}
	if ( !empty($mainimg) ) {
		$befImgY = $pdf->GetY();
		$pdf->Image($mainimg, 11, $lastTableY+8, $widthMainImg, 48, '', '', '', true, 150, '', false, false, 0, 'CM', false, false);
		//$pdf->Rect(11, $lastTableY+8, $widthMainImg, 48, 'F', array(), array(128,255,128));
		$afterImgY = $pdf->getImageRBY();
		$realImgHeight = ($afterImgY - $befImgY)*3;
		
		$imagesTD .='<td style="text-align:center;" ><img height="'.$realImgHeight.'" src="'.$pictsDir.'10x10.png"></td>';
		$imgcolspan++;
	}
	// ---- //
	
	$image_table='
			<table cellpadding="2" style="border: 2px solid #333;">
				<tbody>
					<tr>
						<td colspan="'.$imgcolspan.'" style="text-align:center;border-bottom: 1px solid grey;">Рендеры 3D модели:</td>
					</tr>
					<tr>
						'.$imagesTD.'
					</tr>
				</tbody>
			</table>
	';
	$row_dop_vc_str = '';
	if ( !empty($dopVC) )
	{
		foreach ( $dopVC as $row_dop_vc )
		{
		$row_dop_vc_str .= '
			<tr style="">
				<td width="35%" style="background-color: AQUA;border-right: 1px solid grey;border-bottom: 1px solid grey;">'.$row_dop_vc['vc_names'].'</td>
				<td width="65%" style="text-align:left;border-bottom: 1px solid grey;" ><b>'.$row_dop_vc['vc_link'].'</b> '.$row_dop_vc['vc_descript'].'</td>
			</tr>
		';
		}
	}
	$dopVC_table='
		<table cellpadding="2" style="border: 2px solid #333;">
			<tbody>
				<tr>
					<td colspan="2" style="text-align:center; border-bottom: 1px solid grey;">Ссылки на доп. артикулы:</td>
				</tr>
				'.$row_dop_vc_str.'
			</tbody>
		</table>
	';
	
	$tableWrapp='
		<table width="100%" cellpadding="1">
			<tbody>
				<tr>
					<td width="50%" style="text-align:center;">'.$image_table.'</td>
					<td width="50%" style="text-align:center;">'.$dopVC_table.'</td>
				</tr>
			</tbody>
		</table>
	';
	
	$pdf->writeHTMLCell(195, '', '', '', $tableWrapp, 0, 1, 0, true, 'L', true);
	//--END table 2--//
	
	//============= counter point ==============//
    $overallProgress = ceil(( ++$complectCounter * 100 ) / $complects_lenght);
    $progress->progressCount( $overallProgress );

	//--END table 3--//
	$descr = trim($row['description']);
	$descrScol = '';
	if (!empty($descr)) $descrScol = 'style="background-color: lime;"';
	$descr_table='
		<style>
			table {
				border: 2px solid #333;
			}
		</style>
		<table cellspacing="1" cellpadding="2" width="100%">
			<tbody>
				<tr>
					<td colspan="2" style="text-align:left;"><span '.$descrScol.'>Примечания:</span></td>
				</tr>
				<tr>
					<td>'.$descr.'</td>
				</tr>
			</tbody>
		</table>
	';
	$pdf->writeHTMLCell(195, '', '', '', $descr_table, 0, 1, 0, true, 'L', true);


	$repairsTR = '';
	if ( $repairs )
	{
		foreach ( $repairs as $repRow )
        {
            $repName = '3D';
            if ( $repRow['which'] == 1 ) $repName = 'Моднльера-доработчика';
			$repairsTR .= '
				<tr >
					<td colspan="2" style="text-align:left;">
						<span style="background-color: BISQUE;">Ремонт '. $repName. ' №'.$repRow['rep_num'].' от - '.date_create( $repRow['date'] )->Format('d.m.Y').': </span>
						<span>'.$repRow['repair_descr'].'</span>
					</td>
				</tr>	
			';	
		}

		$rep_table='
			<style>
				table {
					border: 2px solid #333;
				}
			</style>
			<table cellspacing="1" cellpadding="2" width="100%">
				<tbody>
						'.$repairsTR.'
				</tbody>
			</table>
		';
		$pdf->writeHTMLCell(195, '', '', '', $rep_table, 0, 1, 0, true, 'L', true);
	}
	
	$table2='
		<style>
			td {
				border: 1px solid grey;
			}
			table {
				border: 2px solid #333;
			}
		</style>
		<table cellspacing="1" cellpadding="4" width="100%">
			<tr>
				<td width="25%">Материал мастер модели</td>
				<td width="25%">Модельер</td>
				<td width="25%">Уровень сложности</td>
				<td width="25%">Дата</td>
			</tr>
			<tr>
				<td><b>'.$materialMM.'</b></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>
	';
	if ( !$executive ) $pdf->writeHTMLCell(195, '', '', '', $table2, 0, 1, 0, true, 'L', true);

	//============= counter point ==============//
    $overallProgress = ceil(( ++$complectCounter * 100 ) / $complects_lenght);
    $progress->progressCount( $overallProgress );

	$table3='
		<style>
			td {
				border: 1px solid grey;
			}
			table {
				border: 2px solid #333;
			}
		</style>
		<table cellspacing="1" cellpadding="4" width="100%">
			<tr>
				<td width="15%">Участок</td>
				<td width="15%">Дата</td>
				<td width="15%">Подпись</td>
				<td width="55%">Примечания</td>
			</tr>
			<tr>
				<td>ОС<br></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>М. участок<br></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>Литейка<br></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>Бригады<br></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>
	';
	$pdf->writeHTMLCell(195, '', '', '', $table3, 0, 1, 0, true, 'L', true);
	
	include_once _globDIR_ . 'glob_Variables.php';
	include_once _globDIR_ . 'functs.php';
	$modTypeEn = translit($row['model_type'],$alphabet);
	$pdfname = $row['number_3d'].'-'.$modTypeEn.'_passport.pdf';
	
	//============= counter point ==============//
    $overallProgress = ceil(( ++$complectCounter * 100 ) / $complects_lenght);
    $progress->progressCount( $overallProgress );

	//$pdf->Output($path, 'F');
	
	$pdf_string = $pdf->Output('pdfname.pdf', 'S');

	if ( !file_exists( _rootDIR_.'Pdfs/') ) mkdir( _rootDIR_.'Pdfs/', 0777, true);

	file_put_contents(_rootDIR_.'Pdfs/'.$pdfname, $pdf_string);

    //============= counter point ==============//
    $progress->progressCount( 100 );

echo json_encode($pdfname);
