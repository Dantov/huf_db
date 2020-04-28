<?php
namespace Views\Glob_Controllers\classes;

class Search
{


    /**
     * @param @object $connection - объект соединения
     * @param @number $statusID - ID статуса по которому ищем
     * @param @array $searchedModels - массив моделей в которых ищем
     * @param $dates - массив с датами От и До, если они были заданы
     *
     * Производит дополнительный поиск в таблице статусов
     */
    public static function byStatusesHistory($connection, $statusID, &$searchedModels, $dates = [])
    {
        if ( !$connection ) return;
        if ( !$searchedModels ) return;
        if ( !$statusID ) return;

        $andDates = "";
        if ( !empty($dates) )
        {
            $from = '';
            $andDates = " AND ( ";
            if (!empty($dates['from'])) {
                $from = $dates['from'];
                $andDates .= " date>='$from' ";
            }
            if (!empty($dates['to'])) {
                $to = $dates['to'];
                if ( !empty($from) )  $andDates .= " AND ";
                $andDates .= " date<='$to' ";
            }
            $andDates .= " ) ";
        }

        $in = '(';
        foreach ( $searchedModels as &$model ) $in .= $model['id'] . ',';
        $in = trim($in,',') . ')';
        unset($model);

        $query = "SELECT pos_id,status,date FROM statuses WHERE status='$statusID' $andDates AND pos_id IN $in";
        $resultQuery = mysqli_query($connection, $query);
        $statuses = [];
        while( $resRow = mysqli_fetch_assoc($resultQuery) ) { $statuses[] = $resRow; }


        $resultModels = [];
        foreach ( $searchedModels as $model )
        {
            foreach ( $statuses as $status )
            {
                if ( $status['pos_id'] === $model['id'] )
                {
                    $resultModels[] = $model;
                    continue 2; // что б не выводил 2 одинаковых модели если было 2 одинаковых статуса
                }
            }
        }

        $searchedModels = $resultModels;
    }
}