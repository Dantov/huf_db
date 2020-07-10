<?php
namespace Views\_Nomenclature\Models;
use Views\_Globals\Models\General;

class GradingSystemModel extends General
{

    const WRONG_ID = 33;
    const SQL_ERROR = 44;
    const SUCCESS = 1;

	public function __construct()
	{
		parent::__construct();
		$this->connectDBLite();
	}

    /**
     * @return array
     * @throws \Exception
     */
    public function getData()
    {

        $gs = $this->findAsArray("select * from grading_system");

        //debug($gs,'',1);

        $result = [];
        foreach ( $gs as $row ) $result[$row['work_name']][] = $row;

        //debug($result,'',1);
        return $result;
	}

    /**
     * @param int $id
     * @return array
     * @throws \Exception
     */
    public function getGSRowByID( int $id ) : array
    {
        return $this->findOne("select * from grading_system WHERE id='$id'");
    }

    /**
     * @param string $description
     * @param float $percent
     * @param string $examples
     * @param int $editGS_ID
     * @throws \Exception
     * @return array
     */
    public function editGSPos( string $description, float $percent, string $examples, int $editGS_ID ) : array
    {
        if ( !$this->findOne("select 1 from grading_system WHERE id='$editGS_ID' ") ) return ['error'=>self::WRONG_ID];

        $basePoints = (float)$this->findOne("select points from grading_system WHERE id='1' ")['points'];
        $newPoints =  round(($basePoints * $percent) / 100, 2);

        $sql = "UPDATE grading_system SET description='$description', examples='$examples', percent='$percent', points='$newPoints' WHERE id='$editGS_ID'";
        if ( $this->baseSql($sql) )
        {
            return ['success'=>self::SUCCESS,'error'=>0];
        } else {
            return ['error'=>self::SQL_ERROR, 'sql'=> $sql];
        }
    }

}