<?php
require('inc/Monitor64Client.php');

$mon = new Monitor64Client();


$mon->parse_df(); 
if ( $mon->disk['fullest_used_percent'] < 90 )
{
    echo $mon->disk['fullest_used_percent'];
}else
{
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
}
