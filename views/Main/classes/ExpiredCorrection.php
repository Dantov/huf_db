<?php

class ExpiredCorrection
{

    /**
     * @param array $wCenters - массив участков
     * проверим наличие дат, после "просрочено"
     * изменяет оригинальный массив
     */
    public static function adjust(&$wCenters = [])
    {

        /* @var &$wCenterEndDate
         * указатель на ['end']['date'] элемента массива
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
         * ссылка на массив участка из workingCenters.php
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
                continue;
            }

        }
    }

}