<?php

namespace dtw\db;

use dtw\DtwObject;

/*
 * реализует базовые SQL запросы
 * так же реализует общий функционал для поиска/записи в БД
 */
class BaseSQL
{

    /*
     * $MySQLi объект соединения
     */
    protected $mySQL;

    /*
    * @var array $tableRelations - оригинальный массив таблиц связей, пришел из модели
     * ['files' => 'parent_id', 'repairs' => 'parent_id']
     * // столбец parent_id таблицы files указывает на id таблицы $tableName
    */
    public $tableRelations = [];

    /*
     * @var array $tableRelationsSchema
     * информация о связанных таблицах
     * примари ключи, ключи связей и т.д.
     * Пример:
     * testform1 => [
            [primaryKey] => id
        ]
       files => [
            [primaryKey] => id
            [relationKey] => parent_id
        ]
     */
    public $tableRelationsSchema = [];

    /*
    * @var string - имя этой таблицы
    */
    protected $tableName = '';

    /*
     * @var object
     * содержит список столбцов этой таблицы
     */
    protected $field;

    /*
     * @var string - главный ключ в этой таблице (id)
     */
    protected $primaryKey = '';

    /*
     * @var int - значение ключа $primaryKey ( конкретная запись, нужна для корректной работы update )
     * если он есть - обновляет запись в таблице, иначе добавляет
     */
    protected $primaryKeyVal = '';

    public function __construct($tableName, $tableRelations=[])
    {
        $this->MySQLi = DataBase::connect();

        if ( !empty($tableRelations) ) $this->tableRelations = $tableRelations;

        $this->tableName = $tableName;
        $this->getColumns();
        $this->setRelationsSchema();

        //debug($tableName,'$tableName ');
        $this->primaryKey = $this->tableRelationsSchema[$tableName]['primaryKey'];

        //debug($this->tableRelationsSchema,'$this->tableRelationsSchema - '.$tableName);
    }

    /*
     * простой низкоуровневый запрос
     * return object
     */
    public function query($query)
    {
        //debug($query,'$query');
        if ( is_string($query) && !empty($query) )
        {
            return $this->MySQLi->query($query);
        }
        return null;
    }

    /*
     * делает запрос в БД и возвращает массив затронутых строк, id последней, или ошибки
     * return array
     */
    public function sqlQuery( $query )
    {
        $res = [];

        if ( $this->query($query) ) {

            $res['affected_rows'] = $this->MySQLi->affected_rows;
            $res['insert_id'] = $this->MySQLi->insert_id;
            $res['query'] = $query;

        } else {
            $res['error'] = $this->MySQLi->error;
            $res['errno'] = $this->MySQLi->errno;
            $res['query'] = $query;
        }
        return $res;
    }

    /*
     * Общий функционал
     */

    /*
     * дублир. кода
     */
    private function inTables($tblName='')
    {
        if ( empty($tblName) ) $tblName = $this->tableName;
        $result = [];
        $resQuery = $this->query("SHOW COLUMNS FROM $tblName");
        if ( $resQuery ) {
            while ( $obj = $resQuery->fetch_assoc() )
            {
                if ($obj['Extra'] === 'auto_increment' && $obj['Key'] === 'PRI' )
                {
                    $this->tableRelationsSchema[$tblName]['primaryKey'] = $obj['Field'];
                }
                $this->tableRelationsSchema[$tblName][$obj['Field']] = '';
                $result[$obj['Field']] = '';
            }
        }
        return $result;
    }
    /*
     * @var string $tableName - имя таблицы
     * getColumns() - выберем имена колонок из текущей таблицы
     * вернет объект колонок
     * return object;
     */
    protected function getColumns()
    {
        $this->field = new DtwObject( $this->inTables() );
    }

    /*
     * получение информации о связанных таблицах
     * relationKey - столбец который === $this->primaryKey этой табл
     */
    protected function setRelationsSchema()
    {
        foreach ( $this->tableRelations as $table => $relationKey )
        {
            $this->inTables($table);
            $this->tableRelationsSchema[$table]['relationKey'] = $relationKey;
        }
    }


    /*
     * add_Upd_Record()
     *  $tcf
     * @var strings $table - имя таблицы
     * @var strings $columns - сформированная строка столбцов
     * @var strings $fields - - сформированная строка данных
     * return array id последней записи | ошибку
     */
    protected function add_Upd_Record( $tcf=[], $operation='' ) {
        $res = [];
        $table = $tcf[0];
        $columns = $tcf[1];
        switch (strtolower($operation))
        {
            case 'insert':
                $fields = $tcf[2];
                $query = "INSERT into $table ($columns) VALUES $fields";
                break;
            case 'update':
                $where = $tcf[2];
                $query = "UPDATE $table SET $columns WHERE $where";
                break;
            default:
                return $res['error'] = "wrong Operation";
        }

        return $this->sqlQuery($query);
    }


}