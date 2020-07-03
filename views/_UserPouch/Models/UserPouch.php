<?php
namespace Views\_UserPouch\Models;
use Views\_Main\Models\Main;

class UserPouch extends Main
{

    public $paidTab;

    public function __construct( string $paidTab='' )
    {
        parent::__construct();

        switch ( $paidTab )
        {
            case "all": $this->paidTab = ""; break;
            case "paid": $this->paidTab = "AND paid='1'"; break;
            case "notpaid": $this->paidTab = "AND paid='0' AND status='1'"; break;
            default : $this->paidTab = ""; break;
        }
    }


    /**
     * @return array
     * @throws \Exception
     */
    public function getModelPrices() : array
    {
        $modelPrices = [];
        $modelPricesQuery = $this->findAsArray("SELECT * FROM model_prices WHERE user_id={$this->user['id']} {$this->paidTab} ");

        $grades3D = [];
        foreach ( $modelPricesQuery as &$mp )
        {
            if ( $mp['is3d_grade'] == 1 )
            {
                $grades3D[$mp['pos_id']][] = $mp;
                continue;
            }
            $modelPrices[$mp['pos_id']][] = $mp;
        }

        // сливаем массивы оценок 3д в один массив
        foreach ( $grades3D as $modelID => $grades )
        {
            $totalValue = 0;
            foreach ( $grades as  $grade ) $totalValue += $grade['value'];
            $grades[0]['cost_name'] = '3D Моделирование';
            $grades[0]['value'] = $totalValue;

            $modelPrices[$modelID][] = $grades[0];
        }

        //debug($grades3D, '$grades3D');
        //debug($modelPrices, '$modelPrices',1);
        unset($grades3D);
        return $modelPrices;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getStockInfo() : array
    {
        $sqlStock = " SELECT s.id, s.number_3d, img.pos_id, img.img_name, s.vendor_code, s.model_type, s.status FROM stock as s 
                      LEFT JOIN images as img ON (s.id = img.pos_id AND img.main=1)
                      WHERE s.id IN (SELECT DISTINCT pos_id FROM model_prices WHERE user_id={$this->user['id']} $this->paidTab )";
        return $this->findAsArray($sqlStock);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getStatistic() : array
    {
        $result = [
            'paid' => 0,
            'notpaid' => 0,
            'waiting' => 0,
        ];
        $modelPricesQuery = $this->findAsArray("SELECT * FROM model_prices WHERE user_id={$this->user['id']}");
        //debug($modelPricesQuery, '$modelPricesQuery');
        foreach ( $modelPricesQuery as $price )
        {
            if ( $price['paid'] == 1 ) $result['paid'] += $price['value'];
            //if ( $price['paid'] == 0 ) $result['notpaid'] += $price['value'];
            if ( $price['status'] == 0 )
            {
                $result['waiting'] += $price['value'];
            } elseif ( $price['paid'] == 0 ) {
                $result['notpaid'] += $price['value'];
            }
        }

        //debug($result, '$result',1);

        return $result;
    }

}