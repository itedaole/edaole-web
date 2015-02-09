<?php
require_once(dirname(__FILE__).'/config.php');
$wechat = new Wechat();
$wechat->login();