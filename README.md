# Agent64
Server monitoring agents for PHP



## Install
In a public directory on a webserver, run:
```
git clone https://github.com/web64/agent64.git
cd agent64/
cp sample-config.inc.php config.inc.php
```

Edit config.inc.php to match your set-up.


## Current Uptime Agents
Uptime agents for services will give a HTTP 500 on connection failure
* MySQL
* CrateDB
* Memcache
* Diskspace

...HTTP 500 error when disk usage is over 90%