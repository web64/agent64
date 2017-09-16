<?php
require('config.inc.php');
require('inc/Monitor64Client.php');

$mon = new Monitor64Client();


$mon->parse_df(); 

if ( $mon->fullest_used_percent < 90 )
{
    echo $mon->fullest_used_percent;
}else
{
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
}





