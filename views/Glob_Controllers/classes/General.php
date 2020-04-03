<?php

class General {
	
	public function __construct( $server=false ) {
		if ( isset($server) ) {
			$this->server = $server;
			$this->setDirs();
			//$this->IP_visiter = $_SERVER['HTTP_X_REAL_IP'];
			$this->IP_visiter = $_SERVER['REMOTE_ADDR'];
		}
	}
	
	protected $alphabet = array(
		"а"=>"a",
		"б"=>"b",
		"в"=>"v",
		"г"=>"g",
		"д"=>"d",
		"е"=>"e",
		"ё"=>"e",
		"ж"=>"j",
		"з"=>"z",
		"и"=>"i",
		"й"=>"i",
		"к"=>"k",
		"л"=>"l",
		"м"=>"m",
		"н"=>"n",
		"о"=>"o",
		"п"=>"p",
		"р"=>"r",
		"с"=>"s",
		"т"=>"t",
		"у"=>"y",
		"ф"=>"f",
		"х"=>"h",
		"ц"=>"c",
		"ч"=>"ch",
		"ш"=>"sh",
		"щ"=>"w",
		"ъ"=>"",
		"ы"=>"u",
		"ь"=>"",
		"э"=>"e",
		"ю"=>"u",
		"я"=>"ia",
		" "=>"_",
		"°"=>"_"
	);
	
	protected $server;
	protected $connection;
	protected static $connectObj;
	protected $rootDir;
	protected $stockDir;
    public $IP_visiter;

	public $user;
	public $users;
	public $statuses;
	public $labels;
	public $imageStatuses;

	public static $serviceArr;

	public $workingCentersDB;
	public $workingCentersSorted;
    //public $localSocket = 'tcp://192.168.0.245:1234';
    public $localSocket = 'tcp://127.0.0.1:1234';

	public function formatDate($date)
    {
        $fdate = is_int($date) ? '@'.$date : $date;
        return date_create( $fdate )->Format('d.m.Y');
    }

	protected function setDirs() {
		$this->rootDir = _rootDIR_; //'/HUF_DB';
		$this->stockDir = $this->rootDir.'Stock';
	}
    public function getUser()
    {
        if ( isset($this->user) ) return $this->user;
        session_start();
        $userQuery = mysqli_query($this->connection, " SELECT id,fio,fullFio,location,access FROM users WHERE id='{$_SESSION['user']['id']}' ");
        if ( !$userQuery->num_rows ) new ErrorException('Пользователь не найден!',404);

        $user = mysqli_fetch_assoc($userQuery);
        foreach ( $user as $key => $value ) $this->user[$key] = $value;
        $this->user['IP'] = $this->IP_visiter;
        return $this->user;
    }

    public function getUsers()
    {
        if ( isset($this->users) ) return $this->users;

        $usersQuery = mysqli_query($this->connection, " SELECT id,fio,fullFio,location,access FROM users ");
        if ( !$usersQuery->num_rows ) new ErrorException('Users not found at all!',500);
        while( $user = mysqli_fetch_assoc($usersQuery) )
        {
            $this->users[] = $user;
        }
        return $this->users;
    }


    public function permittedFields()
    {
        $this->getUser();
        $permittedFields = [
            'addComplect' => false,
            'number_3d' => false,
            'vendor_code' => false,
            'collections' => false,
            'author' => false,
            'modeller3d' => false,
            'jewelerName' => false,
            'model_type' => false,
            'model_weight' => false,
            'size_range' => false,
            'print_cost' => false,
            'model_cost' => false,
            'material' => false,
            'covering' => false,
            'stl' => false,
            'ai' => false,
            'images' => false,
            'dellImage' => false,
            'gems' => false,
            'vc_links' => false,
            'description' => false,
            'repairs' => false,
            'repairs3D' => false,
            'repairsJew' => false,
            'labels' => false,
            'statuses' => false,
            'dellModel' => false,
            'deleteImage' => false,
            'addModel' => false,
        ];

        switch ($this->user['access'])
        {
            case 1:
                foreach ( $permittedFields as &$field ) $field = true;
                break;
            case 2:
                foreach ( $permittedFields as &$field ) $field = true;
                $permittedFields['print_cost'] = false;
                $permittedFields['model_cost'] = false;
                $permittedFields['jewelerName'] = false;
                $permittedFields['repairsJew'] = false;
                break;
            case 3:
                $permittedFields['print_cost'] = true;
                $permittedFields['statuses'] = true;
                break;
            case 4:
                $permittedFields['vendor_code'] = true;
                $permittedFields['material'] = true;
                $permittedFields['covering'] = true;
                $permittedFields['gems'] = true;
                $permittedFields['vc_links'] = true;
                $permittedFields['description'] = true;
                $permittedFields['statuses'] = true;
                $permittedFields['labels'] = true;
                break;
            case 5:
                $permittedFields['images'] = true;
                $permittedFields['jewelerName'] = true;
                $permittedFields['gems'] = true;
                $permittedFields['model_cost'] = true;
                $permittedFields['description'] = true;
                $permittedFields['repairs'] = true;
                $permittedFields['repairsJew'] = true;
                $permittedFields['labels'] = true;
                $permittedFields['statuses'] = true;
                break;
            case 6:
                $permittedFields['images'] = true;
                $permittedFields['statuses'] = true;
                break;
        }

        return $permittedFields;
    }

    /*
     * рабочие центры из БД
     */
    public function getWorkingCentersDB()
    {
        if ( isset($this->workingCentersDB) ) return $this->workingCentersDB;

        $query = mysqli_query($this->connection, " SELECT id,name,descr,user_id FROM working_centers ");

        if ( $query === false ) throw new Error('Error in working centers query.',500);
        if ( !$query->num_rows ) throw new Error('Working Centers not found at all!',500);

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
     */
    public function getWorkingCentersSorted()
    {
        if ( isset($this->workingCentersSorted) ) return $this->workingCentersSorted;

        $query = mysqli_query($this->connection, " SELECT * FROM working_centers ORDER BY sort_id");
        if ( !$query->num_rows ) new ErrorException('Working Centers not found at all!',500);

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

	public function connectToDB()
    {
        if ( $this->connection ) return $this->connection;
		$dbConfig = require _globDIR_ . "db_config.php";
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
	
	public function unsetSessions($path=false) {
		// удаляем автозаполнение при возврате на главную
		if ( isset($_SESSION['general_data']) ) unset($_SESSION['general_data']);
		//удаляем инфу из ворд файла и сами файлы
		if ( isset($_SESSION['fromWord_data']) ) { 
			$this->rrmdir( _rootDIR_ .$_SESSION['fromWord_data']['tempDirName']);
			unset($_SESSION['fromWord_data']);
		}
		if ( isset($_SESSION['id_progr']) ) unset($_SESSION['id_progr']); //сессия id пдф прогресс бара
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

	private static function getServiceArr()
    {
        if ( isset(self::$serviceArr) ) return self::$serviceArr;
        $serviceQuery = mysqli_query(self::$connectObj,"SELECT * FROM service_arr");

        if ( $serviceQuery === false ) throw new Error('No labels or Statuses found',500);

        while ( $data = mysqli_fetch_assoc($serviceQuery) ) self::$serviceArr[] = $data;
        //debug('serviceArr empty');

        return self::$serviceArr;
    }

	public function getStatLabArr($query, $location = '')
    {

        //статусы
		if ( $query == 'status' )
		{
		    if ( !empty($location) && isset($this->statuses) )
            {
                if ( !is_int($location) ) new Error('location must be integer',500);
                $arrStatuses = [];
                foreach ( $this->statuses as $status )
                {
                    if ( $status['location'] == $location && $status['tab'] == 'status' ) $arrStatuses[] = $status;
                }
                return $arrStatuses;
            }

            if( isset($this->statuses) )
            {
                return $this->statuses;
            }

            foreach ( self::$serviceArr as $status )
            {
                if ( $status['tab'] == 'status' ) $this->statuses[] = $status;
            }

            return $this->statuses;
		}

		//метки
		if ( $query == 'labels' )
		{
            if ( isset($this->labels) ) return $this->labels;

            $c = 0;
            foreach ( self::$serviceArr as $status )
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
                    $this->imageStatuses[$c]['id'] = $imageStatus['name_en'];
                    $this->imageStatuses[$c]['name'] = $imageStatus['name_ru'];
                    $this->imageStatuses[$c]['check'] = '';
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
			include_once 'myphp-backup.php';
			$backupDatabase = new Backup_Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, CHARSET);
			
			$this->checkBackupFiles( (int)$maxAllowedFiles, BACKUP_DIR);
			
			$result = $backupDatabase->backupTables(TABLES, BACKUP_DIR) ? 'OK' : 'KO';
			$backupDatabase->obfPrint('Backup result: ' . $result, 1);

			if ( $backupDatabase->done === true )
			{
				$ddddate = new DateTime('+1 hour');
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
	
}