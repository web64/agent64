<?php
require('config.inc.php');
/**
    Check CrateDB Connection
*/

$memcache = new Memcache;
$memcache->connect(MEMCACHE_HOST, MEMCACHE_PORT);

$statuses = $memcache->getStats();

if ( $statuses === false )
{
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
	echo "Memcache ERROR!";
}else{
	echo "OK!";
}
