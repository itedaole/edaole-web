<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$aliapy_config['partner']      = $INI['alipay']['mid'];

//安全检验码，以数字和字母组成的32位字符
$aliapy_config['key']          = $INI['alipay']['sec'];

//页面跳转同步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
//return_url的域名不能写成http://localhost/user.logistics.address.query_php_utf8/return_url.php ，否则会导致return_url执行无效
$aliapy_config['return_url']   = $INI['system']['wwwprefix'] .'/alifast/returnadd_url.php';



//签名方式 不需修改
$aliapy_config['sign_type']    = 'MD5';

//字符编码格式 目前支持 gbk 或 utf-8
$aliapy_config['input_charset']= 'utf-8';

//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$aliapy_config['transport']    = 'http';
?>