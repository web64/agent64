<?php
require('config.inc.php');
/**
    Check ElasticSearch Connection 

*/

$data = file_get_contents("http://localhost:5000");

if ( !empty($data) )
{
	header('Content-Type: application/json');
	echo $data;
}else{
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo "NLP Server Connection ERROR!";
}
