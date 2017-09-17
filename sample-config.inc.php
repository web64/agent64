<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting( E_ALL | E_STRICT );

// MySQL
define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PASSWORD', '');


// CrateDB (crate.io)
define('CRATE_HOST', 'localhost');
define('CRATE_PORT', 4200);


// CrateDB (crate.io)
define('MEMCACHE_HOST', 'localhost');
define('MEMCACHE_PORT', 11211);


// ElasticSearch 
define('ELASTIC_HOST', 'localhost');
define('ELASTIC_PORT', 9200);

// Redis
define('REDIS_HOST', 'localhost');
define('REDIS_PORT', 6379);


// Beanstalk
define('BEANSTALK_HOST', 'localhost');
define('BEANSTALK_PORT', 11300);