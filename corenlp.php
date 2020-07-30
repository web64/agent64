<?php
require('config.inc.php');
/**
    Check CoreNLP Connection 

*/

$data = file_get_contents("http://localhost:9000");

if ( !empty($data) )
{
	echo "OK";
}else{
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo "NLP Server Connection ERROR!";
}
