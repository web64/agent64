<?php

$data = file_get_contents("http://". CRATE_HOST .":". CRATE_PORT);

if ( !empty($data) )
{
	header('Content-Type: application/json');
	echo $data;
}else{
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
}