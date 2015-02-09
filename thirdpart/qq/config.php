<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
define('QQ_KEY', $INI['qq']['key']);
define('QQ_SEC', $INI['qq']['sec']);
define('QQ_CALLBACK', "{$INI['system']['wwwprefix']}/thirdpart/qq/callback.php");
define('QQ_CALLBACK_LOGIN', "{$INI['system']['wwwprefix']}/thirdpart/qq/index.php");
require_once(dirname(__FILE__) . '/qqoauth.php');