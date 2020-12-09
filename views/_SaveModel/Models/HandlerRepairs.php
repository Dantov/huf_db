<?php
/**
 * Date: 05.12.2020
 * Time: 21:32
 */

namespace Views\_SaveModel\Models;


use Views\_Globals\Models\User;

class HandlerRepairs extends Handler
{


    /**
     * HandlerRepairs constructor.
     * @param $id
     * @throws \Exception
     */
    public function __construct( $id = 0 )
    {
        parent::__construct($id);

        $this->connectDBLite();
    }

    /**
     * Главный метод, через него взаимодействуем с рем. и их прайсами
     * @param $repairsData
     * данные конкретной группы ремонтов ( 3d || Jew || Prod )
     * @return array
     * @throws \Exception
     */
    public function addRepairs( $repairsData )
    {
        $response = [];
        if ( !trueIsset($repairsData) ) return $response;

        $prices = $this->detachPrices( $repairsData );

        // Выделим список прайсов на удаление
        $toDell = [];
        if ( is_array($prices) )
            if ( array_key_exists('toDell',$prices) )
                $toDell = $prices['toDell'];

        // Формирование массива с данными ремонтов для пакетной вставки
        $repairRows = $this->makeBatchInsertRow($repairsData, $this->id, 'repairs');
        // Доп подготовка этих данных и вставка в базу
        $response['repairs'] = $this->touchRepairs($repairRows);


        // Формирование массива с данными прайсов для пакетной вставки
        $pricesRows = $this->makeBatchInsertRow($prices, $this->id, 'model_prices');
        $response['prices'] = $this->touchPrices($pricesRows,$repairRows);

        if ( !empty($toDell) )
            $this->deletePrices($toDell);

        return $response;
    }

    protected function detachPrices( &$repairsData )
    {
        $prices = [];
        if ( isset( $repairsData['prices'] ) )
        {
            $prices = $repairsData['prices'];
            unset ($repairsData['prices']);
        }
        return $prices;
    }

    /**
     * Доп подготовка данных ремонтов и вставка в базу
     * @param $repairRows
     * @return mixed
     * @throws \Exception
     */
    protected function touchRepairs( &$repairRows )
    {
        $rIDs = [];
        foreach ( $repairRows['insertUpdate'] as $key => &$repair )
        {
            $rID = $repair['id'];
            if ( $rID )
            {
                // Не принимаем данные ремонтов которые оплачены
                $sql = " SELECT paid FROM model_prices WHERE repair_id='$rID' AND paid > '0' ";
                if ( $this->findOne($sql,'paid') )
                {
                    unset($repairRows['insertUpdate'][$key]);
                    continue;
                }
            }

            // Изменим статус и метку в модели в соотв. со статусом ремонта
            switch ( (int)$repair['status'] )
            {
                case 1: // Новый
                    $repair['status'] = 2; // Ставит "Ожидает принятия"
                    break;
                case 3: // В работе
                    $this->updateRepairMark($repair['pos_id'], (int)$repair['which'], 3);
                    break;
                case 4: // Завершено
                    $this->updateRepairMark($repair['pos_id'], (int)$repair['which'], 4);
                    $rIDs[] = $rID; // какому ремонту зачислять прайсы
                    break;
            }

            // первый раз приходят пустые даты
            if ( empty($repair['status_date']) )
                $repair['status_date'] = $this->date;
            if ( empty($repair['date']) )
                $repair['date'] = $this->date;
        }


        // вставка данных ремонтов
        $response['repairsInsert'] = $this->insertUpdateRows($repairRows['insertUpdate'], 'repairs');

        $response['repairsDelete'] = $this->deleteRepairs($repairRows['remove']);

        // зачисление прайсов
        $this->enrollRepairPrices( $rIDs );

        return $response;
    }


    /**
     * Зачисляет прайсы ремонтов
     * @param array $rIDs
     * ID ремонтов которым зачислить
     * @return bool
     * @throws \Exception
     */
    public function enrollRepairPrices( array $rIDs )
    {
        if ( empty($rIDs) ) return false;

        $updIDs = "";
        foreach ( $rIDs as $id )
        {
            if ( empty($id) ) continue;
            $updIDs .= "'" . $id . "',";
        }

        $updIDs = "(" . trim($updIDs,", ") . ")";
        $sql = "UPDATE model_prices SET status='1',status_date='$this->date' WHERE repair_id IN $updIDs AND status=0";
        if ( $this->baseSql($sql) )
            return true;

        return false;
    }


    /**
     * Добавим информацию в Stock что модель в ремонте
     * поставим статус в ремнте и метку
     * @param int $pos_id
     * @param int $which
     * @param int $repairStatus
     * @throws \Exception
     */
    protected function updateRepairMark( int $pos_id, int $which, int $repairStatus )
    {
        // найдет 40489 во вложенных массивах
        //$key = array_search(40489, array_column($userdb, 'uid'));
        $stockData = $this->findOne("SELECT labels,status FROM stock WHERE id='$pos_id' ");

        $oldStatus = (int)$stockData['status'];
        $newStatus = $oldStatus;
        $oldLabels = $stockData['labels'];
        $newLabels = '';

        switch ( $repairStatus )
        {
            case 3: // В работе ( поставим метку "Ремонт" и статус в ремонте )
                $oldLabelsArr = explode(';', $oldLabels);
                if ( !in_array('Ремонт', $oldLabelsArr) )
                    $newLabels = $oldLabels . ";Ремонт";

//                if ( $which === 0 ) $newStatus = 10;
//                if ( $which === 1 ) $newStatus = 48;

                break;
            case 4:
                $oldLabelsArr = explode(';', $oldLabels );
                if ( $key = array_search('Ремонт', $oldLabelsArr ) )
                {
                    unset( $oldLabelsArr[$key] );
                    $newLabels = implode(';',$oldLabelsArr);
                }
//                if ( $which === 0 ) $newStatus = 47;
//                if ( $which === 1 ) $newStatus = 6;

                break;
        }

        if ( !empty($newLabels) )
        {
            $this->baseSql("UPDATE stock SET labels='$newLabels' WHERE id='$pos_id' ");
        }


        /*
        if ( $oldStatus !== $newStatus )
        {
            $this->baseSql("UPDATE stock SET labels='$newLabels',status='$newStatus' WHERE id='$pos_id'");

            // добавляем в историю статусов
            $statusT['pos_id'] = $pos_id;
            $statusT['status'] = $newStatus;
            $statusT['creator_name'] = User::getFIO();
            $statusT['UPdate'] = $this->date;

            $this->addStatusesTable($statusT);
        }*/
    }

    /**
     * @param $pricesRows
     * @param $repairRows
     * @return bool|int
     * @throws \Exception
     */
    protected function touchPrices( &$pricesRows, &$repairRows )
    {
        if ( empty($pricesRows['insertUpdate']) ) return false;


        foreach ( $pricesRows['insertUpdate'] as &$price )
        {
            //Получим ИД юзера кому адресован ремонт
            foreach ( $repairRows['insertUpdate'] as $repair )
            {
                if ( $price['repair_id'] == $repair['id'] )
                {
                    $price['user_id'] = $this->getUserIDFromSurname( explode(" ", $repair['toWhom'])[0] );

                    // проверим не зачислить ли нам этот прайс
                    // по статусу ремонта Завершено
                    if ( $repair['status'] == 4 )
                    {
                        $price['status'] = 1;
                        $price['status_date'] = $this->date;
                    }
                }
            }

            // Получим имя ремонта по его ИД из grading_system
            $price['cost_name'] = $this->findOne("SELECT work_name as cost_name FROM grading_system WHERE id='{$price['gs_id']}' ",'cost_name');
            $price['date'] = $this->date;
        }
        //debugAjax($pricesRows,'$pricesRows',END_AB);

        return $this->insertUpdateRows($pricesRows['insertUpdate'], 'model_prices');
    }


    /**
     * @param $repairs
     * @return array
     * @throws \Exception
     */
    public function deleteRepairs( $repairs )
    {
        if ( empty($repairs) )
            return [];

        $result = [];
        $IDs = '';
        foreach ( $repairs as &$repair )
        {
            if ( !trueIsset($repair['id']) ) continue;
            $IDs .= "'" . $repair['id'] . "',";
        }

        if ( $IDs = trim($IDs,", ") )
        {
            $IDs = "(" . $IDs . ")";
            $result['repairs_dell'] = $this->baseSql("DELETE FROM repairs WHERE id IN $IDs");
            // удалим не оплаченные прайсы
            $result['prices_dell'] = $this->baseSql("DELETE FROM model_prices WHERE repair_id IN $IDs AND paid=0");
        }

        return $result;
    }


    /**
     * @param array $toDell
     * @throws \Exception
     */
    public function deletePrices( array $toDell )
    {
        if ( empty($toDell) ) return;

        $dellIDs = "";
        foreach ( $toDell as $id )
        {
            if ( empty($id) ) continue;
            $dellIDs .= "'" . $id . "',";
        }

        $dellIDs = "(" . trim($dellIDs,", ") . ")";
        $this->baseSql("DELETE FROM model_prices WHERE id IN $dellIDs AND paid=0");
    }


    /*
    public function addRepairsOLD( $repairs )
    {
        if ( !is_array($repairs) || empty($repairs) ) return [];
        $repairs = $this->parseRecords($repairs);

        $deletions = [];
        $updates = [];
        $insertions = [];

        foreach ( $repairs as $repair )
        {
            $repID = (int)$repair['id'];
            $repDescr = trim($repair['description']);

            if ( $repID > 0 )
            {
                $repQuery = mysqli_query($this->connection, " SELECT COUNT(1) FROM repairs WHERE id='$repID' ");
                if ( $repQuery->num_rows && (empty($repDescr) || $repDescr == -1) ) { // кандидат на удаление
                    $deletions[] = $repair;
                } elseif ($repQuery->num_rows)
                {
                    $updates[] = $repair;
                }
            }
            if ( $repID === 0 )
            {
                $insertions[] = $repair;
                continue;
            }
        }

        //        debug($deletions,'$deletions');
        //        debug($updates,'$updates');
        //        debug($insertions,'$insertions');

        $result = [];
        if ( !empty($deletions) )
        {
            $dellIds = '(';
            foreach ( $deletions as $deletion ) $dellIds .= $deletion['id'] . ',';
            $dellIds = trim($dellIds,',') . ')';

            $dellQuery = mysqli_query($this->connection, " DELETE FROM repairs WHERE id in $dellIds ");
            if ($dellQuery) {
                $result['deletions'] = $dellIds . ' - deleted.';
            } else {
                printf( "Error Delete repairs: %s\n", mysqli_error($this->connection) );
                $result['deletions'] = 'error';
            }
        }
        if ( !empty($updates) )
        {
            foreach ( $updates as $update )
            {
                $id = $update['id'];
                $description = $update['description'];
                $which = $update['which'];
                $cost = '';
                if ( isset($update['cost']) ) $cost = ", cost='{$update['cost']}'";

                $queryStr = " UPDATE repairs SET repair_descr='$description', which='$which' $cost WHERE id='$id' ";

                $updQuery = mysqli_query($this->connection, $queryStr);
                if ($updQuery) {
                    $result['updates'][] = $id . ' - success.';
                } else {
                    printf( "Error Update repairs: %s\n", mysqli_error($this->connection) );
                    $result['updates'][] = $id . ' - update error!';
                }
            }
        }
        if ( !empty($insertions) )
        {
            foreach ( $insertions as $insertion )
            {
                $num = $insertion['num'];
                $description = $insertion['description'];
                $cost = $insertion['cost'];
                $which = $insertion['which'];
                $insertQuery = mysqli_query($this->connection, " INSERT INTO repairs (rep_num, repair_descr, cost, which, date, pos_id) 
		                                                                 VALUES ('$num','$description','$cost','$which','$this->date','$this->id') ");
                if ($insertQuery) {
                    $result['insertions'][] = $id . ' - success.';
                } else {
                    printf( "Error Insert repairs: %s\n", mysqli_error($this->connection) );
                    $result['insertions'][] = ' Insert error!';
                }
            }
        }

        return $result;
    }
    */

}