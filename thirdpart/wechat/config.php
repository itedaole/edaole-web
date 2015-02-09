<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
define('WECHAT_KEY', $INI['wechat']['key']);
define('WECHAT_SEC', $INI['wechat']['sec']);
define('WECHAT_APP_KEY', $INI['wechat']['app_key']);
define('WECHAT_APP_SEC', $INI['wechat']['app_sec']);
define('WECHAT_CALLBACK', "{$INI['system']['wwwprefix']}/thirdpart/wechat/callback.php");
define('WECHAT_CALLBACK_LOGIN', "{$INI['system']['wwwprefix']}/thirdpart/wechat/index.php");
require_once(dirname(__FILE__) . '/wechatoauth.php');