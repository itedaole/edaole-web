<?php 
require_once(dirname(__FILE__).'/config.php');
$qq = new QQ();
$qq->callback();
Utility::Redirect(WEB_ROOT . '/thirdpart/qq/bind.php' );