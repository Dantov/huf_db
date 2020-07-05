<?php
namespace Views\_PaymentManager\Models;
use Views\_UserPouch\Models\UserPouch;

class PaymentManager extends UserPouch
{
	
	public function __construct( string $paidTab='', int $worker = 0, int $month = 0, int $year = 0 )
	{
		parent::__construct( $paidTab, $worker, $month, $year );
	}


	/**
     * @return array
     * @throws \Exception
     */
	/*
    public function getModelPrices() : array
    {
        $modelPrices = [];
        $sql = "SELECT * FROM model_prices WHERE $this->worker $this->paidTab $this->date";
        $modelPricesQuery = $this->findAsArray($sql);

        //debug($sql,'$sql',1);

        // отделим оценки 3д от остальных
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
    }*/

    /**
     * @return array
     * @throws \Exception
     *//*
    public function getStockInfo() : array
    {
        $sqlStock = " SELECT s.id, s.number_3d, img.pos_id, img.img_name, s.vendor_code, s.model_type, s.status FROM stock as s 
                      LEFT JOIN images as img ON (s.id = img.pos_id AND img.main=1)
                      WHERE s.id IN (SELECT DISTINCT pos_id FROM model_prices WHERE $this->worker $this->paidTab $this->date)";
        //debug($sqlStock,'',1);
        return $this->findAsArray($sqlStock);
    }
*/
    public function getActiveUsers()
    {
    	//debug( $this->getUsers(),'getUsers' );
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
        //debug( $users,'users',1 );

        return $users;
    }



}