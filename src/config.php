<?php

$servername = "mysql:3306";
$dbusername = "xpalfy";
$dbpassword = "Almaspite69";
$dbname = "final_zadanie";

function getDatabaseConnection()
{
    global $servername, $dbusername, $dbpassword, $dbname;
    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
    if ($conn->connect_error) {
        die("Connection error: " . $conn->connect_error);
    }
    return $conn;
}
