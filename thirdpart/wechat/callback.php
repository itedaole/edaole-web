<?php 
require_once(dirname(__FILE__).'/config.php');
$wechat = new Wechat();
$wechat->callback();
Utility::Redirect(WEB_ROOT . '/thirdpart/wechat/bind.php' );