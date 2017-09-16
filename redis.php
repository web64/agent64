<?php
require('config.inc.php');
/**
    Check Redis Connection 
*/

$fp = fsockopen(REDIS_HOST, REDIS_PORT, $errCode, $errStr, 1);
if (!$fp) {
    // $errStr ($errCode)
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo "Redis Connection Error \n";
}
else {
    fclose($fp);
    echo "OK!";
} 