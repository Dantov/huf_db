<?php
namespace Views\_Globals\Models;

class PushNotice extends General
{

    /**
     * PushNotice constructor.
     * @throws \Exception
     */
    function __construct()
    {
        parent::__construct($_SERVER);
        $this->connectToDB();
        $this->getUser();

        $this->userID = $this->user['id'];
    }

    public $userID;

    public function checkPushNotice()
    {
        $noticesResult = [];

        $userId = 'a'.$this->userID.'a';
        $query = mysqli_query($this->connection, " SELECT * FROM pushnotice where ip not like '%$userId%'" );
        // уходим если нет новых нотаций
        if ( !$query->num_rows ) return $userId;

        $c = 0;
        while( $pushRows = mysqli_fetch_assoc($query) )
        {
            $noticesResult[$c]['date'] = $pushRows['date'];
            $noticesResult[$c]['not_id'] = $pushRows['id'];
            $noticesResult[$c]['pos_id'] = $pushRows['pos_id'];
            $noticesResult[$c]['number_3d'] = $pushRows['number_3d'];
            $noticesResult[$c]['vendor_code'] = $pushRows['vendor_code'];
            $noticesResult[$c]['model_type'] = $pushRows['model_type'];
            $noticesResult[$c]['addEdit'] = $pushRows['addedit'];
            $noticesResult[$c]['fio'] = $pushRows['name'];
            $noticesResult[$c]['img_src'] = $pushRows['image'];

            foreach ( $this->statuses as $status )
            {
                if ( $status['id'] == $pushRows['status'] )
                {
                    $noticesResult[$c]['status'] = $status;
                    break;
                }
            }
            $c++;
        }

        return $noticesResult;
    }

    /**
     * @param int $id
     * @param int $isEdit
     * @param null $number_3d
     * @param null $vendor_code
     * @param null $model_type
     * @param null $date
     * @param null $status
     * @param null $creator_name
     * @return bool|int
     * @throws \Exception
     */
    public function addPushNotice(int $id, $isEdit=1, $number_3d=null, $vendor_code=null, $model_type=null, $date=null, $status=null, $creator_name=null)
    {
        if (!$id) return false;
        if (!$date) $date = date('Y-m-d');
        if (!$creator_name) $creator_name = User::getFIO();

        $sql = "SELECT s.number_3d as number_3d, s.vendor_code as vendor_code, s.model_type as model_type, 
                       s.status as status, i.main as img_main, i.sketch as img_sketch, i.detail as img_detail, 
                       i.onbody as img_onbody, i.pos_id as pos_id, i.img_name as img_name
                  FROM stock as s
                  LEFT JOIN images as i ON s.id = i.pos_id
                   WHERE s.id='$id' ";
        $stockQuery = $this->findAsArray( $sql );

        // порядок выбора элементов
        $statusIMGOrder = ['img_main','img_sketch','img_detail','img_onbody'];
        $modelData = [];
        foreach ( $statusIMGOrder as $statusName )
        {
            foreach ( $stockQuery as $stockData )
            {
                if ( (int)$stockData[$statusName] === 1 )
                {
                    $modelData = $stockData;
                    break(2);
                }
            }
        }
        if ( empty($modelData) ) $modelData = $stockQuery[0];

        //debug($modelData,'$stockQuery',1,1);

        if ( !$status ) $status = $modelData['status'];
        if ( !$number_3d ) $number_3d = $modelData['number_3d'];
        if ( !$vendor_code ) $vendor_code = $modelData['vendor_code'];
        if ( !$model_type ) $model_type = $modelData['model_type'];

        // полезем за картинкой
        $pathToImg='';
        if ( trueIsset($modelData['img_name']) )
        {
            $file = $number_3d.'/'.$id.'/images/'.$modelData['img_name'];

            $pathToImg = _WORK_PLACE_ ? "http://192.168.0.245/Stock/" . $file : _stockDIR_HTTP_ . $file ;

            if ( !file_exists(_stockDIR_ . $file) ) $pathToImg = _stockDIR_HTTP_."default.jpg";
        }

        // Добавляем в базу
        $query = "INSERT INTO pushnotice ( pos_id,number_3d,vendor_code,model_type,image, status, name, addedit, date) 
                  VALUES('$id','$number_3d','$vendor_code','$model_type','$pathToImg','$status','$creator_name','$isEdit','$date')";

        $addPush = mysqli_query($this->connection, $query);
        $not_id = mysqli_insert_id($this->connection); // last insert ID

        // возьмем массив данных статуса по его ID
        $statusData = [];
        for( $i = 0; $i < count($this->statuses); $i++ )
        {
            if ( $this->statuses[$i]['id'] == $status )
            {
                $statusData = $this->statuses[$i];
                break;
            }
        }

        $message = [
            'newPushNotice' => [
                'date' => $date,
                'not_id' => $not_id,
                'pos_id' => $id,
                'number_3d' => $number_3d,
                'vendor_code' => $vendor_code,
                'model_type' => $model_type,
                'addEdit' => $isEdit,
                'fio' => $creator_name,
                'img_src' => $pathToImg,
                'status' => $statusData,
            ],
        ];

        if ( $addPush ) // отправляем сообщение
        {
            //$this->closeDB();
            //$oldErrorReporting = error_reporting(); // save error reporting level
            //error_reporting($oldErrorReporting & ~E_WARNING); // disable warnings
            set_error_handler(function(){return true;});
            $instance = @stream_socket_client($this->localSocket, $errNo, $errorMessage);
            restore_error_handler();
            //error_reporting($oldErrorReporting);
            if ( !$instance )
            {
                return false;
                //throw new Exception("addPushNotice() Can't connect to socket server! \n Error $errNo: " . $errorMessage);
            }

            $toUser = 'toAll';
            if ( _DEV_MODE_ ) $toUser = 'Быков В.А.';
            return fwrite($instance, json_encode(['user' => $toUser, 'message' => $message]) . "\n");

        } else {
            printf("addPushNotice() error message: %s\n", mysqli_error($this->connection));
            //$this->closeDB();
            return false;
        }
    }

    /**
     * @param $id
     * @return bool or string - client IP
     *
     * Добавляет IP посетителя в столбец уведомления, что бы не показывать это увед. ему снова
     */
    public function addIPtoNotice($id)
    {
        $newIpRow = 'a'.$this->userID.'a;';

        $addIPShowed = mysqli_query($this->connection, " UPDATE pushnotice SET ip=CONCAT(ip,'$newIpRow') WHERE id='$id' ");

        if (!$addIPShowed)
        {
            printf( "addIPtoNotice() error: %s\n", mysqli_error($this->connection) );
            return false;
        }
        return $this->userID;
    }


    /**
     * @param $not_id - array массив уведомлений которые нужно закрыть
     * @return bool
     */
    public function addIPtoALLNotices($not_id)
    {
        //$where = "WHERE ";
        $in = '(';
        for( $i = 0; $i < count($not_id); $i++ )
        {
            $in .= $not_id[$i].',';
        }
        $in = trim($in,',') . ')';

        $newIpRow = 'a'.$this->userID.'a;';

        //CONCAT соединяет строки
        //REPLACE - заменяет в строкке
        $query = " UPDATE pushnotice SET ip=CONCAT(ip,'$newIpRow') WHERE id IN $in ";

        $addIPS = mysqli_query($this->connection, $query);

        if ( $addIPS ) return true;
        return false;
    }

    public function clearOldNotices()
    { // удаляем записи которым больше 2х дней
        $date = new \DateTime('-2 days');
        $formDate = $date->format('Y-m-d');
        $dellQuery = mysqli_query($this->connection, " DELETE FROM pushnotice WHERE date<'$formDate' " );
        if ( $dellQuery ) return true;
        return false;
    }

}