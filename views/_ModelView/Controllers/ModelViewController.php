<?php
namespace Views\_ModelView\Controllers;
use Views\_ModelView\Models\{ModelView,DocumentPDF};
use Views\_Globals\Controllers\GeneralController;


class ModelViewController extends GeneralController
{

    public $title = 'ХЮФ 3Д Модель - ';
    public $stockID = null;

    /**
     * @throws \Exception
     */
    public function beforeAction()
    {
        $request = $this->request;
        if ( $request->isAjax() )
        {
            if ( (int)$request->post('zipExtract') === 1 )
            {
                $this->actionExtractStlFiles();
            }
            if ( (int)$request->post('zipDelete') === 1 )
            {
                $this->actionDellStlFiles();
            }
            if ( (int)$request->post('zipDelete') === 1 )
            {
                $this->actionDellStlFiles();
            }

            if ( $this->getQueryParam('document-pdf') )
            {
                ini_set('max_execution_time', 180); // макс. время выполнения скрипта в секундах
                ini_set('memory_limit','256M'); // -1 = может использовать всю память, устанавливается в байтах

                $docPdf = new DocumentPDF($request->post('id'), $request->post('userName'), $request->post('tabID'), $request->post('document'));

                if ( $request->post('document') === 'passport' )
                {
                    $docPdf->printPassport();
                    $fileName = $docPdf->exportToFile('passport');

                    echo json_encode($fileName);
                    exit;
                }
                if ( $request->post('document') === 'runner' )
                {
                    $docPdf->printRunner();
                    $fileName = $docPdf->exportToFile('runner');

                    echo json_encode($fileName);
                    exit;
                }
                if ( $request->post('document') === 'both' )
                {
                    $docPdf->printPassport();
                    $docPdf->printRunner();
                    $fileName = $docPdf->exportToFile('passportRunner');

                    echo json_encode($fileName);
                    exit;
                }
                if ( $request->post('document') === 'picture' )
                {
                    $docPdf->printPicture( (int)$request->post('pictID') );
                    $fileName = $docPdf->exportToFile('picture');
                    echo json_encode($fileName);
                    exit;
                }
            }
            exit;
        }

        $id = (int)$this->getQueryParam('id');
        if ( $id <= 0 || $id >= 99999 ) $this->redirect('/main/');
        $this->stockID = $id;
    }

    /**
     * @throws \Exception
     */
    public function action()
    {
        $id = $this->stockID;

        $modelView = new ModelView($id);
        if (!$modelView->checkID($id)) $this->redirect('/');

        $row = $modelView->row;
        $this->title .= $row['number_3d'] ." ". $row['model_type'];

        $coll_id = $modelView->getCollections();

        $button3D = '';
        if ( $stl_file = $modelView->getStl() )
        {
            $button3D = $stl_file['stl_name'];
            // ПРИМЕР!!
            //$path = _webDIR_HTTP_ . 'js_lib/';
            //$this->includeJSFile( 'three.min.js', ['path'=> $path] );
            $this->includePHPFile('3DViewPanels.php');
        }
        $ai_file = $modelView->getAi();
        $rhino_file = $modelView->get3dm();

        $matsCovers = $modelView->getModelMaterials();
        $complectes = $modelView->getComplectes();
        $images = $modelView->getImages();
        $mainImg = [];
        $mainIsset = false;
        //debug($images,'$images',1);

        // проверим наличие статусов в картинках
        // что б какую отобразить главной
        $setMainImg = function($which) use (&$mainIsset, &$images, &$mainImg)
        {
            foreach ( $images as &$image )
            {
                if ($image[$which] == 1 ) {
                    $mainImg['src'] = $image['img_name'];
                    $mainImg['id'] = $image['id'];
                    $image['active'] = 1;
                    $mainIsset = true;
                    break;
                }
            }
        };
        if ( !$mainIsset ) $setMainImg('main');
        if ( !$mainIsset ) $setMainImg('sketch');
        if ( !$mainIsset ) $setMainImg('onbody');
        if ( !$mainIsset ) {
            foreach ( $images as &$image )
            {
                $mainImg['src'] = $image['img_name'];
                $mainImg['id'] = $image['id'];
                $image['active'] = 1;
                break;
            }
        }

        $usedInModels =$modelView->usedInModels();

        $descriptions = $modelView->getDescriptions();
        $labels = $modelView->getLabels();
        $gemsTR = $modelView->getGems();
        $dopVCTr = $modelView->getDopVC();
        $repairs = $modelView->getRepairs();
        $statuses = $modelView->getStatuses();
        $currentStatus = $modelView->getStatus($row);

        //debug( $currentStatus,'',1);

        $stat_name = $currentStatus['stat_name'];
        $stat_date = $currentStatus['stat_date'];
        $stat_class = $currentStatus['class'];
        $stat_title = $currentStatus['title'];
        $stat_glyphi = 'glyphicon glyphicon-' . $currentStatus['glyphi'];


        $thisPage = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        if ( $thisPage !== $_SERVER["HTTP_REFERER"] ) {
            $_SESSION['prevPage'] = $_SERVER["HTTP_REFERER"];
        }

        $editBtn = false;
        if ( isset($_SESSION['user']['access']) && $_SESSION['user']['access'] > 0 )
        {
            $userAccess = (int)$_SESSION['user']['access'];
            if ( (int)$_SESSION['user']['access'] === 1 || (int)$_SESSION['user']['id'] === 33 ) { // весь доступ
                $editBtn = true;
            }
            if ( (int)$_SESSION['user']['access'] === 2 )  // доступ только где юзер 3д моделлер или автор
            {
                $userRowFIO = $_SESSION['user']['fio'];
                $authorFIO = $row['author'];
                $modellerFIO = $row['modeller3D'];
                if ( stristr($authorFIO, $userRowFIO) !== FALSE || stristr($modellerFIO, $userRowFIO) !== FALSE ) {
                    $editBtn = true;
                }
            }

            if ( (int)$_SESSION['user']['access'] === 3
                || (int)$_SESSION['user']['access'] === 4
                || (int)$_SESSION['user']['access'] === 5
                || (int)$_SESSION['user']['access'] === 6
            ) $editBtn = true;
        }

        $this->includeJSFile('show_pos_scrpt.js', ['defer','timestamp'] );
        $this->includeJSFile('imageViewer.js', ['timestamp'] );

        $imgEncode = json_encode($images,JSON_UNESCAPED_UNICODE);
        $js = <<<JS
        window.addEventListener('load',function() {
          new ImageViewer($imgEncode).init();
        }, false);
JS;
        $this->includeJS($js);

        $this->includePHPFile('imageWrapper.php');
        $this->includePHPFile('progressModal.php','','',_globDIR_.'includes/');

        $compacted = compact([
            'id','row','coll_id','getStl','button3D','dopBottomScripts','complectes','images','mainImg', 'labels', 'str_mat','str_Covering','gemsTR',
            'dopVCTr','stts','stat_name','stat_date','stat_class','stat_title','stat_glyphi','statuses','ai_file','stl_file','thisPage','editBtn',
            'btnlikes','repairs3D','repairsJew','repairs', 'matsCovers','rhino_file','usedInModels','descriptions','currentStatus']);

        return $this->render('modelView', $compacted);
    }

    protected function actionExtractStlFiles()
    {
        $request = $this->request;
        $zip_name = $request->post('zip_name');
        $zip_id = (int)$request->post('zip_id');
        $zip_num3d = $request->post('zip_num3d');

        $resp_arr['done'] = false;
        if ( empty($zip_name) || empty($zip_id) || empty($zip_num3d) )
        {
            $resp_arr['errMessage'] = 'ExtractZip: Incoming data not valid.';
            echo json_encode($resp_arr);
            exit;
        }
//        debug($zip_name,'$zip_name');
//        debug($zip_id,'$zip_id');
//        debug($zip_num3d,'$zip_num3d',1);

        $path = $zip_num3d .'/'.$zip_id.'/stl/';
        $pathHTTP = _stockDIR_HTTP_.$path;
        $pathAbsolute = _stockDIR_.$path;
        $filePath = $pathAbsolute.$zip_name;
        if ( !file_exists($filePath) )
        {
            $resp_arr['errMessage'] = 'ExtractZip: Zip archive '.$zip_name.' not found.';
            echo json_encode($resp_arr);
            exit;
        }

        $zip = new \ZipArchive();
        $res = $zip->open($filePath);
        if ( $res )
        {
            $zip->extractTo($pathAbsolute);

            $names = [];
            for ($i = 0; $i < $zip->numFiles; $i++) $names[$i] = $zip->getNameIndex($i);

            $resp_arr['names'] = $names;
            $resp_arr['zip_path'] = $pathHTTP;
            $resp_arr['done'] = true;

        } else {
            $resp_arr['errMessage'] = 'ExtractZip: Can\'t open zip archive.';
        }

        echo json_encode($resp_arr);
        exit;
    }

    protected function actionDellStlFiles()
    {
        $request = $this->request;
        $dell_names = $request->post('dell_name');
        if ( is_array($dell_names) && !empty($dell_names) )
        {
            foreach ( $dell_names as $name )
            {
                $stockPath = explode('/', $name);
                unset($stockPath[0],$stockPath[1],$stockPath[2],$stockPath[3]);
                $absPath = _stockDIR_ . implode('/',$stockPath);
                $fileName = basename($name);
                if ( file_exists($absPath) )
                {
                    unlink($absPath);
                    $arr['files'][] = $fileName;
                }
            }
            echo json_encode($arr);
        }
        exit;
    }

}