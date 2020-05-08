<?php
namespace Views\_Globals\Controllers;
use Views\_AddEdit\Models\Handler;
use Views\vendor\core\Controller;
/**
 * Description of AjaxController
 *
 * @author MA
 */
class GlobalsController extends Controller
{
    
    public function action() 
    {
        $request = $this->request;
        
        if ( $request->isAjax() )
        {
            if ( $searchInNum = (int)$request->post('searchInNum') ) 
            {
                $this->searchIn($searchInNum);
            }
            if ( (int)$request->post('PushNotice') === 1 )
            {
                $this->actionPushNotice();
            }
            if ( (int)$request->post('isPDF') === 1 )
            {
                $handler = new Handler();
                $arr['success'] = 0;
                $pdfName = $request->post('pdfName');
                if ( $pdfName ) $arr['success'] = $handler->deletePDF($pdfName);
                echo json_encode($arr);
                exit;
            }
            exit;
        }
    }
    
    /**
     * Смена режима поиска в нав. баре вверху
     * @param $searchInNum number
     */
    protected function searchIn($searchInNum) 
    {
        $session = $this->session;
        $assist = $session->getKey('assist');
        
        $resp = "";
        if ( $searchInNum === 1 ) {
                $assist['searchIn'] = 1;
                $resp = "В Базе ";
        }
        if ( $searchInNum === 2 ) {
                $assist['searchIn'] = 2;
                $resp = "В Коллекции ";
        }
        $session->setKey('assist', $assist);
        echo $resp;
    }

    protected function actionPushNotice()
    {
        $request = $this->request;

        $pn = new \Views\_Globals\Models\PushNotice();

        if ( $noticeID = (int)$request->post('closeNotice') )
        {
            $arr['done'] = $pn->addIPtoNotice( $noticeID );
            echo json_encode($arr);
            exit;
        }

        if ( (int)$request->post('closeAllPN') )
        {
            $notIDs = $request->post('closeById');
            $arr['done'] = false;
            if ( is_array($notIDs) || !empty($notIDs) )
            {
                $arr['done'] = $pn->addIPtoALLNotices($notIDs);
            }
            echo json_encode($arr);
            exit;
        }

        if ( (int)$request->post('checkNotice') )
        {
            $arr = $pn->checkPushNotice();
            echo json_encode($arr);
            exit;
        }
    }
    
}
