<?php
require('config.inc.php');
/**
    Check Beanstalk Connection 
*/

$fp = fsockopen(BEANSTALK_HOST, BEANSTALK_PORT, $errCode, $errStr, 1);
if (!$fp) {
    // $errStr ($errCode)
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo "Beanstalk Connection Error [{$errStr}]\n";
}
else {
    fclose($fp);
    echo "OK!";
} 