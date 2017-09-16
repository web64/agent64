<?php
// Updated: 16 Sept 2017 - Moved to Github
// Updated: 4 sept 2017 - added PHP_INT_MAX 32/64 bit check
// Updated: 3 July 2017 - added load
// Updated: 26 Apr 2017 13:30 - added PHP extension check, websites & crons
// Updated: 6 Oct 2016: 17:45

require('config.inc.php');
require('inc/Monitor64Client.php');

$mon = new Monitor64Client();

$mon->get_servername();
$mon->memory();
$mon->parse_php();
$mon->check_php_extensions();
$mon->parse_df(); // check disk space
$mon->mysql();
$mon->get_websites();
$mon->get_crons();
$mon->get_load();

header('Content-Type: application/json');
echo json_encode($mon);

