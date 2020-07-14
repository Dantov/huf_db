<?php

namespace Views\_Nomenclature\Models;

use Views\_AddEdit\Models\Handler;
use Views\_Globals\Models\User;

class UsersModel extends Handler
{
    /**
    * Errors
    */
    const LOG_PASS_EMPTY = 344;
    const NAME_EMPTY = 345;
    const NO_SUCH_USER = 346;
    const INSERT_UPDATE_FAIL = 347;
    const PERMISSION_DENIED = 348;

    const USER_ADD_SUCCESS = 349;
    const PERMISSIONS_ADD_SUCCESS = 350;


    public function __construct()
    {
        parent::__construct();
        $this->connectDBLite();
    }

    /**
     * @throws \Exception
     */
    public function getAllPermissions()
    {
        return $this->findAsArray("SELECT id,name,description FROM permissions");
    }



    /**
     * @throws \Exception
     */
    public function sortUsersLocations()
    {
        $workingCentersDB = $this->getWorkingCentersDB();
        $users = $this->getUsers(true);

        foreach ( $users as &$user )
        {
            $uLocations = explode(',', $user['location']);
            $uLocationsNames = [];
            foreach ($workingCentersDB as $workingCenters)
            {
                foreach ($workingCenters as $workingCenter)
                {
                    if ( in_array( $workingCenter['id'], $uLocations) )
                    {
                        $uLocationsNames[$workingCenter['name']] .= $workingCenter['descr'] . ", ";
                    }
                }
            }
            $user['locNames'] = $uLocationsNames;
        }

        return $users;
    }

    public function addUserPermissions( array &$user ) : void
    {
        $allPermissions = $this->getAllPermissions();
        $userPermissions = $this->findAsArray("SELECT permission_id as id FROM user_permissions WHERE user_id='{$user['id']}'");

        $user['permissions'] = [];
        foreach ($userPermissions as $permID)
        {
            foreach ($allPermissions as $permission)
            {
                if ( $permission['id'] == $permID['id'] ) $user['permissions'][$permID['id']] = $permission;
            }
        }
    }

    /**
     * @param array $data
     * Пост данные
     * @param bool $add
     * Флаг добавления нового юзера
     *
     * @return array
     * @throws \Exception
     */
    public function editUserData( array $data, bool $add = false ) : array
    {
        // Проверка доступа для редактирования данных юзера
        if ( !User::permission('nomUsers_edit') ) return ['error' => self::PERMISSION_DENIED ];

        // А есть ли такой юзер?
        $userID = (int)$data['editUser_ID'];
        if ( !$add )
            if ( !$this->checkID( $userID,'users' ) ) return ['error' => self::NO_SUCH_USER ];
        
        // Берем старый доступ для проверки, может ли текущий изменять его данные
        $access_old = (int)$this->findOne("SELECT access FROM users WHERE id='$userID'")['access'];
        if ( $access_old === 1 && (User::getAccess() !== 1) ) return ['error' => self::PERMISSION_DENIED ];


        $userFirstName  = mysqli_escape_string($this->connection, htmlentities( trim($data['userFirstName']  ),  ENT_QUOTES) );
        if ( empty($userFirstName) ) return ['error' => self::NAME_EMPTY ];

        $userSecondName = mysqli_escape_string($this->connection, htmlentities( trim($data['userSecondName'] ), ENT_QUOTES) );
        $userThirdName  = mysqli_escape_string($this->connection, htmlentities( trim($data['userThirdName']  ),  ENT_QUOTES) );

        $fio = $userFirstName;
        if ( !empty($userSecondName) )
        {
            $arrChars = preg_split('//u',$userSecondName, -1, PREG_SPLIT_NO_EMPTY);
            $fio .= " " . mb_strtoupper($arrChars[0],'UTF-8') . ".";
            if ( !empty($userThirdName) )
            {
                $arrChars = preg_split('//u',$userThirdName, -1, PREG_SPLIT_NO_EMPTY);
                $fio .=  mb_strtoupper($arrChars[0],'UTF-8') . ".";
            }
        }
        $fullFio = $userFirstName . " " . $userSecondName . " " . $userThirdName;

        $userLog = mysqli_escape_string($this->connection, htmlentities( trim($data['userLog']  ),  ENT_QUOTES) );
        $userPass = mysqli_escape_string($this->connection, htmlentities( trim($data['userPass']  ),  ENT_QUOTES) );

        $location = implode(',', array_unique($data['wcList']??[]));
        $presetID = $this->userRulesPreset( $data['userMTProd'] );

        $newUserRow = [
            [
                'id'=>$userID ? $userID : '',
                'login'=>$userLog,
                'pass'=>$userPass,
                'fio'=>$fio,
                'fullFio'=>$fullFio,
                'location'=>$location,
                'access'=>$presetID
            ]
        ];

        $result = [];
        if ( $lastID = $this->insertUpdateRows($newUserRow,'users') )
        {
            $result = ['success' => self::USER_ADD_SUCCESS];
        } else {
            $result = ['error' => self::INSERT_UPDATE_FAIL ];
        }

        // Вносим данные в табл user_permissions
        if ( $access_old != $presetID )
        {
            $permissionPresetOrigin = $this->permissionsPreset( $presetID );
            $permArr = [
                'id' => '',
                'user_id' => !$add ? $userID : $lastID,
                'permission_id' => '',
                'date' => date('Y-m-d'),
            ];
            $permissionsPreset = [];
            foreach ($permissionPresetOrigin as $value) 
            {
               $permArr['permission_id'] = $value;
               $permissionsPreset[] = $permArr;
            }
            // удалим старые разрешения
            if ( !$add ) $this->baseSql("DELETE FROM user_permissions WHERE user_id='$userID'");

            if ( $this->insertUpdateRows($permissionsPreset,'user_permissions') ) {
                $result = ['success' => self::PERMISSIONS_ADD_SUCCESS];
            } else {
                $result = ['error' => self::INSERT_UPDATE_FAIL ];
            }
        }

        return $result;
    }

    /**
     * @param string $rule
     * @return array|bool
     */
    protected function userRulesPreset( string $rule = '' )
    {
        $rulesPreset = [
            'mt_admin' => 1,
            'mt_moder' => 122,
            'mt_modell' => 2,
            'mt_modellHM' => 5,
            'mt_oper' => 3,
            'mt_prod' => 4,
            'mt_tech' => 7,
            'mt_pdo' => 8,
        ];

        if ( !empty($rule) )
        {
            if ( array_key_exists($rule, $rulesPreset) ) return $rulesPreset[$rule];
            return false;
        }
        return $rulesPreset;
    }
    protected function permissionsPreset( int $presetID ) : array
    {
        if ( $presetID === 1 )
        {
            $allPerm = $this->findAsArray("SELECT id FROM permissions");
            foreach ($allPerm as $key => $value) 
            {
                $allPerm[$key] = $value['id'];
            }
        }

        $result = [
            // админ
            1 => $allPerm ?? [],
            // Moderator
            122 => [
                35,36,38,40,42
            ],
            // 3D modeller
            2 => [
                1,2,3,4,5,6,8,9,10,13,15,16,17,18,19,20,21,22,23,24,25,27,28,29,31,32,33,35,36,37,38,45,52
            ],
            // 3D Printing
            3 => [
                11,24,28,32,33,35,36,38,45,50,49
            ],
            // производство
            4 => [
                24,28,45
            ],
            // Модельер-доработчтк
            5 => [
                7,12,19,20,21,23,24,26,27,28,32,33,35,38,45,51
            ],
            // Технолог ЮВ (Валентин)
            7 => [
                28,32,33,36,38,45,48
            ],
            // ПДО
            8 => [
                3,7,10,13,21,22,23,24,27,28,35,36,38,45
            ],
        ];

        if ( array_key_exists($presetID, $result) ) return $result[$presetID];
        return [];
    }

    /**
     * Добавляем нового пользователя
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function addUserData($data) : array
    {
        return $this->editUserData($data, true);
    }

    /**
     * @param $userID
     * @return array
     * @throws \Exception
     */
    public function dellUserData( int $userID, string $userMTProd ) : array
    {
        if ( !User::permission('nomUsers_edit') ) return ['error' => self::PERMISSION_DENIED ];

        $access_old = (int)$this->findOne("SELECT access FROM users WHERE id='$userID'")['access'];
        if ( $access_old === 1 && (User::getAccess() !== 1) ) return ['error' => self::PERMISSION_DENIED ];

        if ( !$this->checkID( $userID,'users' ) ) return ['error' => self::NO_SUCH_USER ];
        
        $this->baseSql("DELETE FROM user_permissions WHERE user_id='$userID'");
        if ($this->baseSql("DELETE FROM users WHERE id='$userID'") ) return ['success' => $userID];

        return ['error' => self::INSERT_UPDATE_FAIL ];
    }
}