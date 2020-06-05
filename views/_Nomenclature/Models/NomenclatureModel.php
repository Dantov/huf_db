<?php
namespace Views\_Nomenclature\Models;
use Views\_Globals\Models\General;

class NomenclatureModel extends General 
{
	public function __construct()
	{
		parent::__construct();
		$this->connectDBLite();
	}

	public function getData()
    {
    	$collections =     $this->findAsArray( "SELECT * FROM collections ORDER BY name ASC");
        $gems_names =      $this->findAsArray( "SELECT * FROM gems_names ORDER BY name ASC");
        $gems_cut =        $this->findAsArray( "SELECT * FROM gems_cut ORDER BY name ASC");
        $gems_color =      $this->findAsArray( "SELECT * FROM gems_color ORDER BY name ASC");
        $gems_size =       $this->findAsArray( "SELECT * FROM gems_sizes ORDER BY name ASC");
        $gems_author =     $this->findAsArray( "SELECT * FROM author ORDER BY name");
        $gems_modeller3D = $this->findAsArray( "SELECT * FROM modeller3D ORDER BY name");
        $jeweler =         $this->findAsArray( "SELECT * FROM jeweler_names ORDER BY name");
        $gems_model_type = $this->findAsArray( "SELECT * FROM model_type ORDER BY name");
        $gems_vc_names =   $this->findAsArray( "SELECT * FROM vc_names ORDER BY name");
        $users =           $this->findAsArray( "SELECT * FROM users ORDER BY fio");

        return compact([
        	'collections', 'gems_names','gems_cut','gems_color','gems_size',
        	'gems_author','gems_modeller3D','jeweler','gems_model_type','gems_vc_names','users',
        ]);
	}

	public function dell($quer_coll, $quer_id, $dell, $quer_val)
    {
    	$modelsQuery = $this->baseSql(" SELECT collections FROM stock WHERE collections='$quer_val' ");
		$count = $modelsQuery->num_rows;

		$this->baseSql(" UPDATE stock SET collections='-' WHERE collections='$quer_val' ");
		$this->baseSql(" DELETE FROM $quer_coll WHERE id='$quer_id' ");
		
		$arr['count'] = $count;
		$arr['dell'] = 1;
		return $arr;
    } 

    public function edit($quer_coll, $quer_id, $dell, $quer_val)
    {
    	// изменяет в самой коллекции
    	$arr['status'] = 0;
		$change = $this->baseSql(" UPDATE $quer_coll SET name='$quer_val' WHERE id='$quer_id' ");

		if ( $change ) $arr['status'] = 1;

    	if ( $quer_coll == 'collections' )
		{
			$oldName = $this->findOne( " SELECT name FROM $quer_coll WHERE id='$quer_id' ");
			//$oldName = mysqli_fetch_assoc($queryName);
			$oldName = $oldName['name'];

			$newCollectionName = $quer_val;

			if ( $newCollectionName === $oldName ) return $arr;

			// поменяем коллекции в моделях
			$stock = $this->findAsArray(" SELECT id,collections FROM stock WHERE collections LIKE '%$oldName%' ");
			if ( empty($stock) ) return $arr;

            $newCollectionsStrArr = [];
            foreach ($stock as $stockRow) {
            	$collArr = explode(';',$stockRow['collections']);
                foreach ( $collArr as &$coll )
                {
                    if ( $oldName == $coll )
                    {
                        $coll = $newCollectionName;
                        $newCollectionsStrArr[$stockRow['id']] = implode(';',$collArr);
                        continue;
                    }
                }
            }
      
            $collIdStr = 'VALUES ';
            foreach ( $newCollectionsStrArr as $idModel=>$newCollStr ) $collIdStr .= "('".$idModel."','".$newCollStr."'),";
            $collIdStr = trim($collIdStr,',');

            //INSERT INTO table (id,Col1,Col2) VALUES (1,1,1),(2,2,3),(3,9,3),(4,10,12)
            //ON DUPLICATE KEY UPDATE Col1=VALUES(Col1),Col2=VALUES(Col2);
            $queryString = " INSERT INTO stock (id,collections) $collIdStr
            ON DUPLICATE KEY UPDATE collections=VALUES(collections)";

            $queryUPDATEColl = $this->baseSql($queryString);
            if (!$queryUPDATEColl) printf( "Error: %s\n", mysqli_error($this->connection) );
		}
		
		return $arr;
    }


    public function add($quer_coll, $quer_id, $dell, $quer_val)
    {
    	$querFind = $this->baseSql(" SELECT * FROM $quer_coll WHERE name='$quer_val' ");
		
		// совпадение найдено т.е коллекция существует
		if ( $querFind->num_rows !== 0 ) 
		{
			$arr['status'] = -1;
			$arr['coll'] = $quer_coll;
			echo json_encode($arr);
			exit;
		}

		$date = date('Y-m-d');

		$quer = $this->baseSql(" INSERT INTO $quer_coll (name,date) VALUES ('$quer_val', '$date') ");
		if ( $quer )
		{
			$newId = $this->findOne(" SELECT id FROM $quer_coll WHERE name='$quer_val' ");
			//$newId = mysqli_fetch_assoc($querid);
			$arr['add'] = 1;
			$arr['id'] = $newId['id'];
			$arr['date'] = date_create( $date )->Format('d.m.Y');
			$arr['status'] = 1;

		} else {
			$arr['status'] = 0;
			printf("Error: %s\n", mysqli_error($this->connection) );
		}

		return $arr;
    }

}