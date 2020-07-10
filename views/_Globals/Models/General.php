<?php
namespace Views\_Globals\Models;


use Matrix\Exception;

class General
{

	protected $alphabet = [];
	protected $server;
	protected $connection;
	protected static $connectObj;
	protected $rootDir;
	protected $stockDir;

    public static $serviceArr;

	public $user;
	public $users;
	public $statuses;
	public $labels;
	public $imageStatuses;
    public $IP_visiter;
	public $workingCentersDB;
	public $workingCentersSorted;
    public $localSocket = '';

    public function __construct( $server=false )
    {
        $this->server = $_SERVER;
        $this->setDirs();

        $this->IP_visiter = _WORK_PLACE_ ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER['REMOTE_ADDR'];
        $this->localSocket = _WORK_PLACE_ ? 'tcp://192.168.0.245:1234' : 'tcp://127.0.0.1:1234';

        $this->alphabet = alphabet();
    }

    public function connectDBLite()
    {
        if ( $this->connection ) return $this->connection;
        $dbConfig = require _CONFIG_ . "db_config.php";
        $connection = mysqli_connect($dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['dbname']);

        if (!$connection) {
            $errno = mysqli_connect_errno();
            $errtext = mysqli_connect_error();
            header("location: " . _views_HTTP_ . "errors/errMysqlConn.php?errno=$errno&errtext=$errtext");
            return false;
        }
        mysqli_set_charset($connection, $dbConfig['charset']);

        self::$connectObj = $connection;
        $this->connection = $connection;
        return $connection;
    }

    /**
     * @return bool|\mysqli
     * @throws \Exception
     */
    public function connectToDB()
    {
        $connection = $this->connectDBLite();
        $this->getUser();

        if ( !isset(self::$serviceArr) ) self::$serviceArr = self::getServiceArr();

        $this->statuses = $this->getStatLabArr('status');
        $this->labels = $this->getStatLabArr('labels');
        $this->getWorkingCentersDB();

        return $connection;
    }

    public function closeDB() {
        mysqli_close($this->connection);
    }

	public function formatDate($date)
    {
        $fdate = is_int($date) ? '@'.$date : $date;
        return date_create( $fdate )->Format('d.m.Y');
    }

	protected function setDirs() 
    {
		$this->rootDir  = _rootDIR_; //'/HUF_DB';
		$this->stockDir = _stockDIR_;//$this->rootDir.'Stock';
	}


    /**
     * @return mixed
     */
    public function getUser()
    {
        if ( isset($this->user) ) return $this->user;
        session_start();
        $userQuery = mysqli_query($this->connection, " SELECT id,fio,fullFio,location,access FROM users WHERE id='{$_SESSION['user']['id']}' ");
        if ( !$userQuery->num_rows ) new \Exception('Пользователь не найден!',404);

        $user = mysqli_fetch_assoc($userQuery);
        foreach ( $user as $key => $value ) $this->user[$key] = $value;
        $this->user['IP'] = $this->IP_visiter;
        return $this->user;
    }


    /**
     * @return mixed
     */
    public function getUsers( bool $full=false )
    {
        if ( isset($this->users) ) return $this->users;
        $logPass = '';
        if ( $full ) $logPass = "login, pass,";
        $usersQuery = mysqli_query($this->connection, " SELECT id, $logPass fio,fullFio,location,access FROM users ");
        if ( !$usersQuery->num_rows ) new \Exception('Users not found at all!',500);
        while( $user = mysqli_fetch_assoc($usersQuery) )
        {
            $this->users[] = $user;
        }
        return $this->users;
    }


    /**
     * сформируем массив разрешений для текущего пользователя
     * @return array
     * @throws \Exception
     */
    public function permittedFields() : array
    {
        /*
        $this->getUser();

        $permissions = $this->findAsArray("SELECT id,name,description FROM permissions");
        $userPermissions = $this->findAsArray("SELECT permission_id FROM user_permissions WHERE user_id='{$this->user['id']}'");
        foreach ( $userPermissions as $key => &$userPF ) $userPermissions[$key] = $userPF['permission_id'];

        $permittedFields = [];
        foreach ( $permissions as $permittedField )
        {
            $pfID = $permittedField['id'];
            if ( in_array( $pfID, $userPermissions ) )
            {
                $permittedFields[$permittedField['name']] = true;
            } else {
                $permittedFields[$permittedField['name']] = false;
            }
        }

        return $permittedFields;
        */
        return User::permissions();
    }


    /**
     * рабочие центры из БД
     * @return mixed
     * @throws \Exception
     */
    public function getWorkingCentersDB()
    {
        if ( isset($this->workingCentersDB) ) return $this->workingCentersDB;

        $query = mysqli_query($this->connection, " SELECT id,name,descr,user_id FROM working_centers ");

        if ( $query === false ) throw new \Exception('Error in working centers query.',500);
        if ( !$query->num_rows ) throw new \Exception('Working Centers not found at all!',500);

        while( $centerRow = mysqli_fetch_assoc($query) )
        {
            $this->workingCentersDB[ $centerRow['name'] ][ $centerRow['id'] ] = $centerRow;
        }
        return $this->workingCentersDB;
    }

    /**
     *
     * Выберем все участки, отсортируем их
     * и подставим им статусы start end
     * @return array
     * @throws \Exception
     */
    public function getWorkingCentersSorted()
    {
        if ( isset($this->workingCentersSorted) ) return $this->workingCentersSorted;

        $this->getStatLabArr('status');

        $query = mysqli_query($this->connection, " SELECT * FROM working_centers ORDER BY sort_id");
        if ( !$query->num_rows ) new \Exception('Working Centers not found at all!',500);

        while( $centerRow = mysqli_fetch_assoc($query) )
        {
            $wcID = (int)$centerRow['id'];

            foreach ( $this->statuses as $status )
            {
                $location = (int)$status['location'];
                $type = $status['type'];
                if ( $location === $wcID )
                {
                    if ( $type === 'start' ) $centerRow['statuses']['start'] = $status;
                    if ( $type === 'end'  ) $centerRow['statuses']['end'] = $status;
                }
            }

            $this->workingCentersSorted[ $centerRow['sort_id'] ] = $centerRow;
        }
        return $this->workingCentersSorted;
    }



    /**
     * возвращает строку в транслите.
     * @param $str
     * @return string
     */
    public function translit($str)
    {
		$str = mb_strtolower($str,'UTF-8');
		$chars = preg_split('//u',$str,-1,PREG_SPLIT_NO_EMPTY);

		foreach ($chars as $key => $value) {
			$ff = false;
			foreach ($this->alphabet as $alph_key => $alph_value) {
				if ( $value == $alph_key ) {
					$eng_arrmt[] = $alph_value;
					$ff = true;
					continue;
				}
			}
			if ( !$ff ) $eng_arrmt[] = $value;
		}
		return implode($eng_arrmt?:[]);
	}
	
	public function rrmdir($src) {
		$dir = opendir($src);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				$full = $src . '/' . $file;
				if ( is_dir($full) ) {
					$this->rrmdir($full);
				}
				else {
					unlink($full);
				}
			}
		}
		closedir($dir);
		rmdir($src);
	}
	
	public function unsetSessions() {
		// удаляем автозаполнение при возврате на главную
		//if ( isset($_SESSION['general_data']) ) unset($_SESSION['general_data']);
	}
	
	public function getStatus($row=[], $selMode='')
    {
		$result = array();
		if ( !empty($row['status']) )
		{
            $statuses = $this->statuses;

            //  КОСТЫЛЬ!!!!
            // при добавлении новых моделей в stock status заходит ID
            // возьмём этот Id из статусов
            $result['stat_name'] = $row['status'];
            if ( $rowStatus = $this->getStatusCrutch($row['status']) ) $result['stat_name'] = $rowStatus;

			$result['stat_date'] = ($row['status_date'] == "0000-00-00") ? "" : date_create( $row['status_date'] )->Format('d.m.Y')."&#160;";

            foreach ( $statuses as $status )
            {
                if ( $result['stat_name'] === $status['name_ru'] )
                {
                    $result['class'] = $status['class'];
                    $result['classMain'] = $status['name_en'];
                    $result['glyphi'] = $status['glyphi'];
                    $result['title'] = $status['title'];
                    break;
                }
            }
		}
		return $result;
	}

    //  КОСТЫЛЬ!!!!
    // при добавлении новых моделей в stock status заходит ID
    // возьмём этот Id из статусов
    public function getStatusCrutch($stockStatus, $index=false)
    {
        if ( $stockStatus = (int)$stockStatus )
        {
            foreach ( $this->statuses as $status )
            {
                if ( (int)$status['id'] === $stockStatus )
                {
                    if ($index) return $status;
                    return $status['name_ru'];
                }
            }
        }
        return false;
    }
        
    public function addStatusesTable($statusT = [])
    {
        //04,07,19 - вносим новый статус в таблицу statuses
        if ( empty($statusT) ) return;

        $pos_id = $statusT['pos_id'];
        $status = $statusT['status'];
        $statuses = $this->statuses;
        foreach ( $statuses as $statusArr )
        {
            if ( $status === $statusArr['name_ru'] )
            {
                $status = $statusArr['id'];
                break;
            }
        }

        $name = isset($statusT['creator_name']) ? $statusT['creator_name'] : "";
        $date = isset($statusT['UPdate']) ? $statusT['UPdate'] : "0000-00-00";
        $queryStr = "INSERT INTO statuses (pos_id,status,name,date) VALUES('$pos_id','$status','$name','$date')";

        mysqli_query($this->connection, $queryStr );
	}

    /**
     * Метод нужен только для внесения первого статуса в табл. statuses при посешении старых моделей
     * используется только в AddEdit_Controller
     * @param $id
     * @param $status_name
     * @param $status_date
     */
    public function getStatuses($id, $status_name, $status_date )
    {
        $statsQuery = mysqli_query($this->connection, " SELECT status,name,date FROM statuses WHERE pos_id='$id' ");

        if ( mysqli_num_rows($statsQuery) ) return;

        $statusT = [];
        $statusT['pos_id'] = $id;
        $statusT['status'] = $status_name;
        $statusT['creator_name'] = "";
        $statusT['UPdate'] = $status_date;
        $this->addStatusesTable($statusT);
    }

    /**
     * @param $str
     * @return array
     * @throws \Exception
     */
    public function getLabels($str)
    {
		$result = array();
		if ( isset($str) && !empty($str) )
		{
			$labels = $this->getStatLabArr('labels');
			$arr_labels = explode(";",$str);
			$c = 0;
			for ( $i = 0; $i < count($arr_labels); $i++ )
			{
				for ( $j = 0; $j < count($labels); $j++ )
				{
					if ( $arr_labels[$i] == $labels[$j]['name'] )
					{
                        $result[$c] = $labels[$j];
						$result[$c]['check'] = "checked";
						$c++;
					}
				}
			}
		}
		return $result;
	}

    /**
     * @return mixed
     * @throws \Exception
     */
    private static function getServiceArr()
    {
        if ( trueIsset(self::$serviceArr) ) return self::$serviceArr;
        $serviceQuery = mysqli_query(self::$connectObj,"SELECT * FROM service_arr");

        if ( $serviceQuery === false ) throw new \Exception('Tables "labels" or "Statuses" not found',500);

        while ( $data = mysqli_fetch_assoc($serviceQuery) ) self::$serviceArr[] = $data;
        //debug('serviceArr empty');

        return self::$serviceArr;
    }

    /**
     * @param $query
     * @param string $location
     * @return bool | mixed
     * @throws \Exception
     */
    public function getStatLabArr($query, $location = '')
    {

        //статусы
		if ( $query == 'status' )
		{
		    if ( !empty($location) && trueIsset($this->statuses) )
            {
                if ( !is_int($location) ) throw new \Exception('location must be integer',500);
                $arrStatuses = [];
                foreach ( $this->statuses as $status )
                {
                    if ( $status['location'] == $location && $status['tab'] == 'status' ) $arrStatuses[] = $status;
                }
                return $arrStatuses;
            }

            if( trueIsset($this->statuses)  )
            {
                return $this->statuses;
            }

            foreach ( self::getServiceArr() as $status )
            {
                if ( $status['tab'] == 'status' ) $this->statuses[] = $status;
            }

            return $this->statuses;
		}

		//метки
		if ( $query == 'labels' )
		{
            if ( trueIsset($this->labels) ) return $this->labels;

            $c = 0;
            foreach ( self::getServiceArr() as $status )
            {
                if ( $status['tab'] == 'label' )
                {
                    $this->labels[$c]['id'] = $status['name_en'];
                    $this->labels[$c]['name'] = $status['name_ru'];
                    $this->labels[$c]['class'] = $status['class'];
                    $this->labels[$c]['info'] = $status['title'];
                    $this->labels[$c]['check'] = '';
                    $c++;
                }
            }
            return $this->labels;
		}

		// статусы картинок
		if ( $query == 'image' )
		{
            if ( isset($this->imageStatuses) ) return $this->imageStatuses;

            $c = 0;
            foreach ( self::$serviceArr as $imageStatus )
            {
                if ( $imageStatus['tab'] == 'status_image' )
                {
                    $this->imageStatuses[$c]['id'] = $imageStatus['id'];
                    $this->imageStatuses[$c]['name'] = $imageStatus['name_ru'];
                    $this->imageStatuses[$c]['name_en'] = $imageStatus['name_en'];
                    $this->imageStatuses[$c]['selected'] = '';
                    if ( $imageStatus['id'] == 27 ) $this->imageStatuses[$c]['selected'] = 1;
                    $c++;
                }
            }
            return $this->imageStatuses;
		}

		return false;
	}

	public function backup($maxAllowedFiles = 10)
	{
		$localtime = localtime(time(), true);
		// бэкапимся только с 4х до 6
		if ( ($localtime['tm_hour']+1) < 16 || ($localtime['tm_hour']+1) >= 18 ) return;
		
		$today = date('Y-m-d');
		
		$row = mysqli_fetch_assoc( mysqli_query($this->connection, " SELECT lastdate FROM backup " ) );
		$lastDate = explode(' ', $row['lastdate'])[0];
		
		if ( strtotime($lastDate) < strtotime($today)  )
		{
			$backupDatabase = new Backup_Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, CHARSET);
			
			$this->checkBackupFiles( (int)$maxAllowedFiles, BACKUP_DIR);
			
			$result = $backupDatabase->backupTables(TABLES) ? 'OK' : 'KO';
			$backupDatabase->obfPrint('Backup result: ' . $result, 1);

			if ( $backupDatabase->done === true )
			{
				$ddddate = new \DateTime('+1 hour');
				$ddmmii = $ddddate->format('Y-m-d H:i:s');

				mysqli_query($this->connection, " UPDATE backup SET lastdate='$ddmmii' ");
			}

		}
	}
	
	protected function checkBackupFiles($maxAllowedFiles, $backupDir)
	{
		$dir = opendir( $backupDir );
		$count = 0;
		// массив с последними датами изменения файлов
		$filesMTime = [];
		while($file = readdir($dir))
		{
			if( $file == '.' || $file == '..' || is_dir($backupDir . "/" . $file) )
			{
				continue;
			}
			$filesMTime[$count]["time"] = filectime($backupDir . "/" . $file);
			$filesMTime[$count]["name"] = $file;
			$count++;
		}
		
		if ( $count >= $maxAllowedFiles  )
		{
			$min = $filesMTime[0]["time"];
			$name = $filesMTime[0]["name"];
			foreach ( $filesMTime as $val  )
			{
				if ( $val["time"] < $min ) 
				{
					$min = $val["time"];
					$name = $val["name"];
				}
			}
			unlink($backupDir . "/" . $name);
		}
		
		//debug( $name . " " . date("F d Y H:i:s.", $min) , "ggggggg");
	}


    /**
     *  Проверим на существование конкретной модели
     * @param int $id
     * @param string $table
     * @param string $column
     * @return bool
     */
    public function checkID( int $id, string $table='stock', string $column='id' ) : bool
    {
        if ( empty($id) || !is_int($id) ) return false;
        $query = mysqli_query($this->connection, " select 1 from $table where $column='$id' limit 1 ");
        if ( $query->num_rows ) return true;
        return false;
    }


    /**
     * @param $sqlStr
     * @return bool|\mysqli_result
     * @throws \Exception
     */
    public function baseSql($sqlStr)
    {
        if ( !is_string($sqlStr) || empty($sqlStr) ) throw new \Exception('Query string not valid!', 555);
        $query = mysqli_query( $this->connection, $sqlStr );
        //if ( !$query ) throw new \Exception("Error in baseSql() " . mysqli_error($this->connection), 555);

        return $query;
    }

    /**
     * @param $sqlStr
     * @return bool
     * @throws \Exception
     */
    public function sql($sqlStr)
    {
        $query = $this->baseSql( $sqlStr );
        if ( !$query ) throw new \Exception(__METHOD__ . " Error: " . mysqli_error($this->connection), 555);

        return $this->connection->insert_id ? $this->connection->insert_id : -1;
    }

    /**
     * @param $sqlStr
     * @return array
     * @throws \Exception
     */
	public function findAsArray($sqlStr)
    {
        $query = $this->baseSql( $sqlStr );

        if ( !$query ) throw new \Exception(__METHOD__ . " Error: " . mysqli_error($this->connection), 555);
        if ( !$query->num_rows ) return [];

        $result = [];
        while ( $data = mysqli_fetch_assoc($query) ) $result[] = $data;
        return $result;
    }

    /**
     * @param $sqlStr
     * @return array|bool
     * @throws \Exception
     */
    public function findOne(string $sqlStr) : array
    {
        if ( !is_string($sqlStr) || empty($sqlStr) ) throw new \Exception('Query string not valid!', 555);

        $result = [];

        $query = $this->baseSql($sqlStr . " LIMIT 1");
        if ( !$query ) throw new \Exception(__METHOD__ . " Error: " . mysqli_error($this->connection), 555);

        while ( $data = mysqli_fetch_assoc($query) ) $result[] = $data;
        if ( !empty($result) ) return $result[0];

        return [];
    }

    /**
     * @param $tableName
     * @return array|bool
     * @throws \Exception
     */
    public function getTableSchema($tableName)
    {
        if ( !is_string($tableName) || empty($tableName) ) throw new \Exception('Table name not valid!', 555);
        
        $query = $this->baseSql('DESCRIBE ' . $tableName);
        if ( !$query ) return [ 'error' => mysqli_error($this->connection) ];

        $result = [];

        while($row = mysqli_fetch_assoc($query)) $result[] = $row['Field'];

        return $result;
    }
	
}