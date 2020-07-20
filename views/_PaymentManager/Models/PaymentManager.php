<?php
namespace Views\_PaymentManager\Models;
use Views\_UserPouch\Models\UserPouch;
use Views\vendor\core\Registry;

class PaymentManager extends UserPouch
{
	
	public function __construct( string $paidTab='', int $worker = 0, int $month = 0, int $year = 0 )
	{
		parent::__construct( $paidTab, $worker, $month, $year );
	}

    public function getActiveUsers()
    {
        $allUsers = $this->getUsers();

        // ID раб. участков из которых нужны юзеры
        $areas = [28,1,2,3,4]; 
        $users = [];
        foreach ($allUsers as &$user) 
        {
            $user['location'] = explode(',', $user['location']);
            foreach ($user['location'] as $location) 
            {
                if ( in_array($location, $areas) ) 
                {
                    $users[] = $user;
                    continue 2;
                }
            }
        }
        return $users;
    }

    /**
     * @param array $pricesIDs
     * @param int $posID
     * @return array
     * @throws \Exception
     */
    public function getPricesByID( array $pricesIDs, int $posID ) : array
    {
        $in = "";
        foreach ( $pricesIDs as $pID )
            if ( !empty($pID) ) $in .= (int)$pID . ',';
        if ( !empty($in) ) $in = "(" . rtrim($in,',') . ")";

        $sql = "
            SELECT mp.id as pID, mp.pos_id as posID, mp.user_id as uID, mp.gs_id as gsID, mp.is3d_grade as is3dGrade, mp.cost_name as costName, mp.value as value, mp.status as status,
              mp.paid as paid, mp.pos_id as posID, mp.date as date, i.img_name as imgName, u.fio, st.number_3d as number_3d, st.vendor_code as vendorCode, st.model_type as modelType
                FROM model_prices as mp
                  LEFT JOIN images as i ON mp.pos_id = i.pos_id AND i.main='1'
                  LEFT JOIN users as u ON mp.user_id = u.id
                  LEFT JOIN stock as st ON mp.pos_id = st.id
			          WHERE mp.id IN $in AND mp.status='1' AND mp.pos_id='$posID'";

        $prices = [];
        try {
            $prices = $this->findAsArray($sql);
        } catch (\Exception $e) {
            $codes = Registry::init()->appCodes;
            return ['error'=>$codes->getCodeMessage($codes::SERVER_ERROR)];
        } finally {
            foreach ( $prices as &$price ) $price['date'] = $this->formatDate($price['date']);

            $imagePath = $prices[0]['number_3d'].'/'.$prices[0]['posID'].'/images/'.$prices[0]['imgName'];
            $prices[0]['imgName'] = _stockDIR_HTTP_ . $imagePath;
            if ( !file_exists(_stockDIR_ . $imagePath) ) $prices[0]['imgName'] = _stockDIR_HTTP_."default.jpg";

            return $prices;
        }
    }

    /**
     * @param array $pricesIDs
     * @param array $modelsID
     * @return array
     * @throws \Exception
     */
    public function getPricesByIDAll(array $pricesIDs, array $modelsID ) : array
    {
        $inPrices = "";
        foreach ( $pricesIDs as $pID )
            if ( !empty($pID) ) $inPrices .= (int)$pID . ',';
        if ( !empty($inPrices) ) $inPrices = "(" . rtrim($inPrices,',') . ")";

        $inModels = "";
        foreach ( $modelsID as $mID )
            if ( !empty($mID) ) $inModels .= (int)$mID . ',';
        if ( !empty($inModels) ) $inModels = "(" . rtrim($inModels,',') . ")";

        $stockSql = "SELECT i.img_name as imgName, st.id as id, st.number_3d as number_3d, st.vendor_code as vendorCode, st.model_type as modelType
                    FROM stock as st
                      LEFT JOIN images as i ON i.pos_id = st.id AND i.main='1'
                          WHERE st.id IN $inModels";

        $pricesSql = "SELECT mp.id as pID, mp.pos_id as posID, mp.user_id as uID, mp.gs_id as gsID, mp.is3d_grade as is3dGrade, mp.cost_name as costName, 
                             mp.value as value, mp.status as status, mp.paid as paid, mp.pos_id as posID, mp.date as date, u.fio
                        FROM model_prices as mp
                          LEFT JOIN users as u ON mp.user_id = u.id
                              WHERE mp.id IN $inPrices AND mp.status='1' AND mp.pos_id IN $inModels";
        $prices = [];
        $stock = [];
        try {
            $stock = $this->findAsArray($stockSql);
            $prices = $this->findAsArray($pricesSql);
        } catch (\Exception $e) {
            $codes = Registry::init()->appCodes;
            return ['error'=>$codes->getCodeMessage($codes::SERVER_ERROR)];
        } finally {
            foreach ( $stock as &$model )
            {
                $imagePath = $model['number_3d'].'/'.$model['id'].'/images/'.$model['imgName'];
                $model['imgName'] = _stockDIR_HTTP_ . $imagePath;
                if ( !file_exists(_stockDIR_ . $imagePath) ) $model['imgName'] = _stockDIR_HTTP_."default.jpg";

                foreach ( $prices as $price )
                    if ( $price['posID'] == $model['id'] ) $model['prices'][] = $price;
            }

            return $stock;
        }
    }

}