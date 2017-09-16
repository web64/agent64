<?php
require('config.inc.php');
/**
    Check MySQL connection
*/

$conn = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD);

if ( !$conn->connect_error )
{
	echo "OK!";
}else{
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo "MySQL ERROR!";
}
