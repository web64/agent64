<?php
require('config.inc.php');
/**
    Check MySQL connection
*/

$conn = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD);

if ( $conn->connect_error )
{
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo "MySQL ERROR!";
}

if ($result = $mysqli->query("SHOW SLAVE STATUS")) {
    $row = $result->fetch_assoc();
    print_r( $row );
    //printf("Select returned %d rows.\n", $result->num_rows);

    /* free result set */
    $result->close();
}

$mysqli->close();
