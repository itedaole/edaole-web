<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

$TP_NAME = 'QQ账号';
$_SESSION["appid"]    = $INI['qzone']['key'];
$_SESSION["appkey"]   = $INI['qzone']['sec'];
$_SESSION["callback"] = "{$INI['system']['wwwprefix']}/thirdpart/qzone/bind.php";
$_SESSION["scope"] = "get_user_info,add_share,add_topic,add_one_blog,add_weibo";
require_once(dirname(__FILE__) . '/qzone/qcapi.php');
