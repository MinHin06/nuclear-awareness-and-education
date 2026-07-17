<?php
$localhost = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'assignment';

$dbConn = mysqli_connect($localhost, $user, $pass, $dbName);

if (mysqli_connect_errno()) {
    // Stop execution silently (or log internally)
    exit;
}