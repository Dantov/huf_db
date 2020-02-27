<?php
$dbConfig = require "db_config.php";
$connection = mysqli_connect($dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['dbname']);
//$connection = mysqli_connect("localhost", "root", "12345678", "huf_models");

if (!$connection) {

    $errno = mysqli_connect_errno();
    $errtext = mysqli_connect_error();
    header("location: " . _views_HTTP_ ."errors/errMysqlConn.php?errno=$errno&errtext=$errtext");

    exit;
}

mysqli_set_charset($connection, $dbConfig['charset']);