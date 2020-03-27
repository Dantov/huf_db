<?php

class Search
{


    public static function byStatusesHistory($connection, $statusID, &$searchedModels, $dates = [])
    {
        if ( !$connection ) return;
        if ( !$searchedModels ) return;
        if ( !$statusID ) return;

        $andDates = "";
        if ( !empty($dates) )
        {
            $from = '';
            $to = '';
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
        //debug($query,'',1);
        $resultQuery = mysqli_query($connection, $query);
        $statuses = [];
        while( $resRow = mysqli_fetch_assoc($resultQuery) ) { $statuses[] = $resRow; }

        //debug($statuses,'$statuses',1);

        $resultModels = [];
        foreach ( $searchedModels as $model )
        {
            foreach ( $statuses as $status )
            {
                if ( $status['pos_id'] === $model['id'] )
                {
                    $resultModels[] = $model;
                    continue 2;
                }
            }
        }

        $searchedModels = $resultModels;
    }
}