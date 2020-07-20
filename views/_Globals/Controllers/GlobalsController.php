<?php
namespace Views\_Globals\Controllers;
use Views\_AddEdit\Models\Handler;
use Views\vendor\core\Controller;


class GlobalsController extends Controller
{


    /**
     * @throws \Exception
     */
    public function action()
    {
        $request = $this->request;
        $session = $this->session;

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

        // ******* SEARCH ******** //
        if ( $this->getQueryParam('search') === 'resetSearch' )
        {
//            $session->dellKey('searchFor');
//            $session->dellKey('foundRow');
//            $session->dellKey('countAmount');
//            $session->setKey('re_search', false);
//            //return;
//            $this->redirect('/main/');
            $this->resetSearch();
        }
        if ( !empty($request->post('searchFor')) || !empty($request->get('searchFor')) || $session->getKey('re_search') )
        {
            $session->dellKey('foundRow');
            $session->dellKey('countAmount');
            $searchFor = $request->post('searchFor') ? $request->post('searchFor') : $request->get('searchFor');
            $session->setKey('searchFor', $searchFor);

            //$search = new Search($session);
            //$this->foundRows = $search->search($searchFor);
            //$search->search($searchFor);
            //$session->setKey('searchFor',$searchFor);

            $this->redirect('/main/');
        } elseif ( $request->isPost() && empty($request->post('searchFor')) )
        {
            //$this->redirect('/globals/?search=resetSearch'); //'/main/?search=resetSearch'
            $this->resetSearch();
        }
    }

    protected function resetSearch() : void
    {
        $session = $this->session;
        $session->dellKey('searchFor');
        $session->dellKey('foundRow');
        $session->dellKey('countAmount');
        $session->setKey('re_search', false);
        $this->redirect('/main/');
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

    /**
     * @throws \Exception
     */
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
