<?php

class ExpiredCorrection
{

    /**
     * заполняется методом adjust()
     * @expiredCenter array - все данные последнего участка на котором есть "просрочено"
     */
    public static $expiredCenter;


    /**
     * найдет последнюю поставленную дату
     * @param array $wCenters
     */
    protected static function findLastMove(&$wCenters = [])
    {
        foreach ( array_reverse($wCenters) as &$wCenter )
        {
            if ( isset($wCenter['end']['date']) && is_string($wCenter['end']['date']) )
            {
                self::$expiredCenter = $wCenter;
                return;
            }
            if ( isset($wCenter['start']['date']) && is_string($wCenter['start']['date']) )
            {
                self::$expiredCenter = $wCenter;
                return;
            }
        }
    }


    /**
     * @param array $wCenters - массив участков
     * проверим наличие дат, после "просрочено"
     * изменяет оригинальный массив
     */
    public static function adjust(&$wCenters = [])
    {

        /* @var &$wCenterEndDate
         * указатель! на ['end']['date'] элемента массива
         * ставим ему пустую строку, если дальше есть даты
         */
        $wCenterEndDate = '';

        /* @var boolean $flag
         *  флаг просроченной даты
         */
        $flag = false;

        /**
         * @var $centerName
         * имя каждого участка
         *
         * @var &$wCenter
         * ссылка на массив участка
         */
        foreach ( $wCenters as $centerName => &$wCenter )
        {
            if ( $flag )
            {
                // если есть даты на след. участках
                // то сотрем "просрочено"
                if ( is_string($wCenter['start']['date']) || is_string($wCenter['end']['date']) )
                {
                    $wCenterEndDate = '';
                    $flag = false;
                }
            }

            if ( $wCenter['end']['date'] == -1 )
            {
                $flag = true;
                $wCenterEndDate = &$wCenter['end']['date'];
                self::$expiredCenter = $wCenter;
                continue;
            }

        }
    }

    /**
     * Сотрем 'просторено' если на предыдущих участках есть даты свежее чем дата принятия
     * из-за которой выпало 'просрочено'
     * @param array $wCenters
     */
    public static function adjust2(&$wCenters = [])
    {
        if ( empty(self::$expiredCenter) ) self::findLastMove($wCenters); // нет просроченных - найдем последнюю дату

        $expiredCenter = self::$expiredCenter; // участок на котором висит просрочено

        // дата принятия из-за которой все просрочено
        $expiredStartStatusDate = 0;
        if ( isset( $expiredCenter['end']['date'] ) && is_string($expiredCenter['end']['date']) )
        {
            $expiredStartStatusDate = strtotime($expiredCenter['end']['date']);
        } elseif ( isset( $expiredCenter['start']['date'] ) && is_string($expiredCenter['start']['date']) )
        {
            $expiredStartStatusDate = strtotime($expiredCenter['start']['date']);
        }


        $wCenterStartDate = 0;
        $wCenterEndDate = 0;
        $arrayNeedle = [];

        // начинаем смотреть с конца, для того что бы найти первую(с конца) свежую дату
        foreach ( array_reverse($wCenters) as &$wCenter )
        {
            if ( $wCenter['end']['date'] === -1 ) continue; // этот же просроченный участок пропустим
            if ( isset($wCenter['start']['date']) ) $wCenterStartDate = strtotime($wCenter['start']['date']);
            if ( isset($wCenter['end']['date']) ) $wCenterEndDate = strtotime($wCenter['end']['date']);

            if ( $wCenterEndDate > $expiredStartStatusDate || $wCenterStartDate > $expiredStartStatusDate )
            {
                // если нашли такую дату - запомним этот участок
                $arrayNeedle = $wCenter;
                break;
            }
        }

        // вычислим ID участка с которого надо начинать стирать даты
        $id = 987;
        foreach ( $wCenters as $arrID => $wCenter ) if ( $wCenter === $arrayNeedle ) { $id = $arrID; break; }

        // сотрем дату принятия на этом участке, если она старая
        if ( isset($wCenters[$id]['end']['date']) )
        {
            if ( strtotime($wCenters[$id]['end']['date']) < $expiredStartStatusDate )
            {
                $wCenters[$id]['end'] = [];
            }
        }
        $id++;

        // стираем все даты на след. участках (они старые)
        for ($i = $id; $i < count($wCenters); $i++)
        {
            $wCenter = &$wCenters[$i];
            $wCenter['end'] = $wCenter['start'] = [];
        }

    }

    /**
     * если есть дата сдачи но нет даты принятия в течении 2х суток - поставим просрочено!
     * @param array $wCenters
     * @param $lastStatus
     * Последний статус у этой модели из таблице Stock
     */
    public static function adjust3(&$wCenters = [], $lastStatus)
    {

        $lastCheckedCenter = [];

        // Если нашли дату сдачи - запомним этот участок.
        // Чтоб посмотреть дальше есть ли даты поступления
        foreach ( array_reverse($wCenters) as &$wCenter )
        {
            if ( isset($wCenter['end']['date']) && is_string($wCenter['end']['date']) )
            {
                $lastCheckedCenter = $wCenter;
                break;
            }
        }

        $id = 987; //ID этого участка в массиве
        foreach ( $wCenters as $arrID => $wCenter ) if ( $wCenter === $lastCheckedCenter ) { $id = $arrID; break; }

        $id++; // перейдем на след участок
        for ($j = $id; $j < count($wCenters); $j++)
        {
            // если дальше по участкам есть даты принятия, то здесь делать больше нечего.
            if ( isset( $wCenters[$j]['start']['date'] ) && is_string($wCenters[$j]['start']['date']) ) return;
        }


        // Если мы еще здесь, после всех манипуляций!!!
        // значит след. участки пусты.
        // проверим сколько прошло времени и поставим просрочено.

        // договоренности
        $lastCheckedCenterDate = strtotime($lastCheckedCenter['end']['date']);
        $plusDay = 2 * 24 * 60 * 60; // +сутки в раб. день // 1 дней; 24 часа; 60 минут; 60 секунд
        if ( date("w", $lastCheckedCenterDate) == 5 ) $plusDay = 4 * 24 * 60 * 60; // +4 суток с пятницы
        if ( date("w", $lastCheckedCenterDate) == 6 ) $plusDay = 3 * 24 * 60 * 60; // +3 суток с субботы

        debug($plusDay,'$plusDay');

        if ( time() > $plusDay && isset($wCenters[$id]) )
        {
            $nextWCenter = &$wCenters[$id];

            if ( $lastStatus == 11 || $lastStatus == 88) return;
            $nextWCenter['start']['date'] = -1;
        }
    }

    /**
     * стираем всё для след. модели
     */
    public static function clear()
    {
        self::$expiredCenter = [];
    }

    
}