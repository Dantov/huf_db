<?php
return [
    "adm_test" => [
        'host' => 'localhost',
        'dbname' => DATABASE,
        'username' => 'adm_test',
        'password' => 'V7L0QJk3YOHvMqnC',
        'charset' => 'utf8',
        'access' => [1], // Список  из user access, кто может заходить под этим пользователем
    ],
    "User" => [
        'host' => 'localhost',
        'dbname' => DATABASE,
        'username' => 'User',
        'password' => 'RexJ7uiLpnBE2fDO',
        'charset' => 'utf8',
        'access' => [ 3,4,6,7 ],
    ],
    "User2" => [
        'host' => 'localhost',
        'dbname' => DATABASE,
        'username' => 'User2',
        'password' => 'P8M28BfzNLnHoSxE',
        'charset' => 'utf8',
        'access' => [ 2,5,8,10,11,122 ],
    ],
    "guest" => [
        'host' => 'localhost',
        'dbname' => DATABASE,
        'username' => 'guest',
        'password' => 'VlbVgxqd1tDytg1w',
        'charset' => 'utf8',
        'access' => [ 0 ],
    ],
    "Admin" => [
        'host' => 'localhost',
        'dbname' => DATABASE,
        'username' => 'Admin',
        'password' => 'CpM6NLyBLXk4sWCo',
        'charset' => 'utf8',
        'access' => [ 1 ],
    ],
];