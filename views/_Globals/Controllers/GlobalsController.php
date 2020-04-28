<?php
namespace Views\_Globals\Controllers;
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

            exit;
        }
        
        if ( $request->isPost() )
        {
            
        }
    }
    
    
    /**
     * Смена режима поиска в нав. баре вверху
     * @param type $searchInNum number
     */
    protected function searchIn($searchInNum) 
    {
        $session = $this->session;
        $assist = $session->getKey('assist');
        
        $resp = "";
        if ( $searchInNum === 1 ) {
                $$assist['searchIn'] = 1;
                $resp = "В Базе ";
        }
        if ( $searchInNum === 2 ) {
                $assist['searchIn'] = 2;
                $resp = "В Коллекции ";
        }
        $session->setKey('assist', $assist);
        echo $resp;
    }
    
    
}
