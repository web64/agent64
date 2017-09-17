<?php
require('config.inc.php');
/**
    Check MySQL connection
*/
$max_delay = 10;

$conn = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD);

if ( $conn->connect_error )
{
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo "MySQL ERROR!";
    exit;
}

if ($result = $conn->query("SHOW SLAVE STATUS;")) 
{
    $row = $result->fetch_assoc();

    if ( $row['Seconds_Behind_Master'] > $max_delay )
    {
    	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    	echo "MySQL Replication ERROR!";
    	exit;
    }else
    {
    	echo "OK! - Lag: {$row['Seconds_Behind_Master']}";
    }
}

