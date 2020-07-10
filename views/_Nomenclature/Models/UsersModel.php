<?php

namespace Views\_Nomenclature\Models;

use Views\_AddEdit\Models\Handler;

class UsersModel extends Handler
{

    const NAME_EMPTY = 345;
    const NO_SUCH_USER = 346;
    const INSERT_UPDATE_FAIL = 347;


    public function __construct()
    {
        parent::__construct();
        $this->connectDBLite();
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


    /**
     * @param array $data
     * @param bool $add
     * @return array
     * @throws \Exception
     */
    public function editUserData( array $data, bool $add = false ) : array
    {
        $userID = (int)$data['editUser_ID'];
        if ( !$add )
            if ( !$this->checkID( $userID,'users' ) ) return ['error' => self::NO_SUCH_USER ];

        $userFirstName  = mysqli_escape_string($this->connection, htmlentities( trim($data['userFirstName']  ),  ENT_QUOTES) );
        if ( empty($userFirstName) ) return ['error' => self::NAME_EMPTY ];

        $userSecondName = mysqli_escape_string($this->connection, htmlentities( trim($data['userSecondName'] ), ENT_QUOTES) );
        $userThirdName  = mysqli_escape_string($this->connection, htmlentities( trim($data['userThirdName']  ),  ENT_QUOTES) );

        $fio = $userFirstName;
        if ( !empty($userSecondName) )
        {
            $arrChars = preg_split('//u',$userSecondName,-1,PREG_SPLIT_NO_EMPTY);
            $fio .= " " . mb_strtoupper($arrChars[0],'UTF-8') . ".";
            if ( !empty($userThirdName) )
            {
                $arrChars = preg_split('//u',$userThirdName,-1,PREG_SPLIT_NO_EMPTY);
                $fio .=  mb_strtoupper($arrChars[0],'UTF-8') . ".";
            }
        }
        $fullFio = $userFirstName . " " . $userSecondName . " " . $userThirdName;

        $userLog = mysqli_escape_string($this->connection, htmlentities( trim($data['userLog']  ),  ENT_QUOTES) );
        $userPass = mysqli_escape_string($this->connection, htmlentities( trim($data['userPass']  ),  ENT_QUOTES) );

        $location = implode(',', array_unique($data['wcList']??[]));

        $userRulesPreset = $this->userRulesPreset($data['userMTProd']);

        $row = [
            [
                'id'=>$userID ? $userID : '',
                'login'=>$userLog,
                'pass'=>$userPass,
                'fio'=>$fio,
                'fullFio'=>$fullFio,
                'location'=>$location,
                'access'=>$userRulesPreset
            ]
        ];

        //debug($row,'ff',1);
        if ( $lastID = $this->insertUpdateRows($row,'users') ) return ['success' => $lastID];

        return ['error' => self::INSERT_UPDATE_FAIL ];
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

    /**
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
    public function dellUserData( int $userID ) : array
    {
        if ( !$this->checkID( $userID,'users' ) ) return ['error' => self::NO_SUCH_USER ];
        if ($this->baseSql("DELETE FROM users WHERE id='$userID'") ) return ['success' => $userID];

        return ['error' => self::INSERT_UPDATE_FAIL ];
    }
}