<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
define('WB_AKEY', $INI['sina']['key']);
define('WB_SKEY', $INI['sina']['sec']);

require_once(dirname(__FILE__) . '/weibooauth.php');
