<?php

class Monitor64client
{
	public $server_name = 'unnamed';
	public $php = array();
	public $mysql = array();
	public $disk = array();
	public $memory = array();
	public $websites = array();
	public $crons = array();
	public $error_a = array();
	public $load = array();
	public $services = array();


	function __construct()
	{
		$this->php = array(
			'cli'		=> '',
			'apache'	=> '',
			'running_crons'		=> 0,
			'running_crons_list'=> array(),
			'missing_extensions'=> array(),
			'ini_path'			=> php_ini_loaded_file(),
		);
		$this->mysql = array(
			'version'		=> '',
			'processlist'	=> array(),
			'databases'		=> array(),
			'process_count'		=> 0,

		);
		$this->disk = array(
			'total'	=> 0,
			'fullest_disk'	=> 0,
			'fullest_used_percent'	=> 0,
			'disk_list'		=> array()
			);
		$this->memory = array(
			'total'	=> 0,
			'used'	=> 0,
			'free'	=> 0,
			'used_percent'	=> 0,
			);

		$this->load = array(
			'1min'	=> 0,
			'5min'	=> 0,
			'15min'	=> 0,
		);
	}

	function get_load()
	{
		$sys_load = sys_getloadavg();
		if ( isset($sys_load[2]) )
		{
			$this->load['1min']  = $sys_load[0];
			$this->load['5min']  = $sys_load[1];
			$this->load['15min'] = $sys_load[2];
		}
	}

	function get_apache_websites( $dir = "/var/www/vhosts/" )
	{
		$d = @dir( $dir );

		while (false !== ($entry = $d->read()) )
		{
			if ($entry != '.' && $entry != '..')
				$this->websites[] =  $entry;
		}
		$d->close();
	}

	function get_nginx_sites()
	{
		exec("grep server_name /etc/nginx/sites-enabled/* -RiI", $servernames);
		if ( !empty($servernames) && is_array($servernames) )
		{
			foreach( $servernames as $line )
			{
				preg_match('/server_name (.+);$/i', $line, $matches);
				if ( !empty($matches[1]) )
				{
					$mon->websites[] = trim($matches[1]);
				}
			}
		}
	}

	function get_services()
	{
		exec("service --status-all |grep +", $_services);
		echo "<pre>";
		$this->services = [];

		foreach($_services as $s)
		{
			$this->services[] = trim(
				str_replace('[ + ]', '', $s)
			);
		}
	}

	function get_crons()
	{
		$d = @dir("/etc/cron.d/");

		while (false !== ($entry = $d->read()) )
		{
			$ignore_crons = array('.', '..', '.placeholder', 'mdadm', 'php', 'popularity-contest', 'sendmail', 'php5');
			if ( array_search($entry, $ignore_crons) === false)
				$this->crons[] =  $entry;
		}
		$d->close();
	}

	function get_servername()
	{
		exec("hostname", $hostname);
		$this->server_name = trim($hostname[0]);
	}

	// check all required extensions are installed
	function check_php_extensions()
	{
		$extensions = array('gd', 'intl', 'mbstring', 'openssl', 'pgsql', 'curl', 'memcache', 'mysqli');
		foreach( $extensions as $ext )
		{
			if ( !extension_loaded($ext) )
				$this->php['missing_extensions'][] = $ext;
		}
	}

	function parse_php()
	{
		exec("php -v", $cli_str);
		$this->php['cli'] = $this->get_version_num( $cli_str[0] );
		$this->php['apache'] = $this->get_version_num( phpversion() );

		// 32 or 64 bit
		// 2147483647
		$this->php['php_max_int'] = (string)PHP_INT_MAX; 
		if ( $this->php['php_max_int'] == '2147483647' )
			$this->php['int_size'] = '32bit';
		else
			$this->php['int_size'] = '64bit'; 

		exec("ps -ef|grep php", $php_crons);
		$this->php['running_crons']	= count($php_crons);

		for($x=1; $x < count($php_crons); $x++)
		{
			// 
			$tmp = preg_split('/\s+/',$php_crons[$x], 8);

			if ( isset($tmp[7]) )
				$this->php['running_crons_list'][] = $tmp[7];
		}

	}

	private function get_version_num($version)
	{
		//$regex = '/[\d]+[\.][\d]+/';  // 5.6
		$regex = '/\d+(?:\.*\d*)*/';	// 5.6.26
		if (preg_match($regex, $version, $matches))
   			return $matches[0]; //returning the first match 
		else
			return $version;
	}

 	function parse_df()
	{
		//exec("df -T -x tmpfs -x devtmpfs -P -B 1G",$df);
		exec("df -h -x tmpfs -x devtmpfs -x rootfs -B 1M", $data);
		for($x=1; $x < count($data); $x++)
		{
			$elements = preg_split('/\s+/',$data[$x]);
			//echo "<pre>" . print_r($elements, true) . "</pre>";
			$used_percent = (int)$elements[4];


			$this->disk['total'] += (int)$elements[1];
			if ( $used_percent > $this->disk['fullest_used_percent'] )
			{
				$this->disk['fullest_used_percent'] = $used_percent;
				$this->disk['fullest_disk'] 		= $elements[5];
			}

			$this->disk['disk_list'][] = array(
				'filesystem'	=> $elements[5],
				'used_percent'	=> $used_percent,
				'total'			=> (int)$elements[1],
				'used'			=> (int)$elements[2],
				'available'		=> (int)$elements[3],
			);

		}

	}

	function memory()
	{
		exec("free -m",$data);
		$elements = preg_split('/\s+/',$data[1]);

		$this->memory = array(
			'total'	=> (int)$elements[1],
			'used'	=> (int)$elements[2],
			'free'	=> (int)$elements[3],	
			'used_percent' => intVal(($elements[2] / $elements[1]) * 100)
		); 
		
	}

	function mysql()
	{
		$conn = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD);
		// Check connection
		if ($conn->connect_error)
		{
			$this->error_a[] = "Connection failed: " . $conn->connect_error;
			return;
		}	

		// processlist
		$result = $conn->query("SELECT db,time,command,state,info FROM information_schema.processlist WHERE command != 'Sleep';");
		while($row = $result->fetch_assoc()) 
		{
				$this->mysql['processlist'][] = $row;
		}
		$this->mysql['process_count'] = count( $this->mysql['processlist'] );

		// databases
		$result = $conn->query("SHOW DATABASES;");
		while($row = $result->fetch_assoc()) 
		{
			if ( $row['Database'] != 'information_schema' && $row['Database'] != 'mysql'  && $row['Database'] != 'sys' && $row['Database'] != 'performance_schema'  && $row['Database'] != 'phpmyadmin')
				$this->mysql['databases'][] = $row['Database'];
		}

		// MySQL Version
		$result = $conn->query("SELECT VERSION() AS mysql_verison;");
		$row = $result->fetch_assoc();
		$this->mysql['version'] = $this->get_version_num($row['mysql_verison']);

		$conn->close();
	}

    function get_os()
    {
         # description
        exec("lsb_release -d", $data );
        $data = str_replace("Description:\t", '', $data);
        $data = str_replace(" LTS", '', $data);
        $this->os = $data;
    }

    /*function get_ram_processes()
    {
        // needs to run as root
        exec("ps aux --sort -rss|head -10", $data );
        $this->top_ram_processes = $data;
    }*/
}