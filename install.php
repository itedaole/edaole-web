<?php
require_once( dirname(__FILE__) . '/include/application.php');
header('Content-Type: text/html; charset=UTF-8;'); 
Session::Init();

$writeable['c'] = is_writable(dirname(__FILE__) . '/include/configure/');
$writeable['d'] = is_writable(dirname(__FILE__) . '/include/data/');
$writeable['t'] = is_writable(dirname(__FILE__) . '/static/team/');
$writeable['u'] = is_writable(dirname(__FILE__) . '/static/user/');

$dbphp = dirname(__FILE__) . '/include/configure/db.php';
$systemphp = dirname(__FILE__) . '/include/configure/system.php';

if ( file_exists($dbphp) || file_exists($systemphp) ) {
	Session::Set('error', '最土团购系统已经安装，请不要重复安装，如需安装请删除 include\configure\system.php 和 include\configure\db.php');
}

if (is_get() ) {
	$db = array(
		'host' => 'localhost',
		'user' => 'root',
		'pass' => '',
		'name' => 'zuitu_db',
	);
	if (!is_writable(DIR_COMPILED)) {
		die( 'include/compiled/  - 目录必须设置为可写！');
	}
	die(include template('install_step'));
}

if ( file_exists($dbphp) || file_exists($systemphp) ) {
	redirect('install.php');
}

$db = $_POST['db'];
$m = mysql_connect($db['host'], $db['user'], $db['pass']);

if (!$writeable['c']) {
	Session::Set('error', 'include/configure/ 不可写');
	redirect('install.php');
}

if (!$writeable['d']) {
	Session::Set('error', 'include/data/ 不可写');
	redirect('install.php');
}

if (!$writeable['t']) {
	Session::Set('error', 'static/team/ 不可写');
	redirect('install.php');
}

if (!$writeable['u']) {
	Session::Set('error', 'static/user/ 不可写');
	redirect('install.php');
}

if ( !$m ) {
	Session::Set('error', '错误的数据库配置');
	redirect('install.php');
}

if ( !mysql_select_db($db['name'], $m) 
		&& !mysql_query("CREATE database `{$db['name']}`;", $m) ) {
	Session::Set('error', "选择数据库 {$db['name']} 错误，可能不存在？");
	redirect('install.php');
}
mysql_select_db($db['name'], $m);

$dir = dirname(__FILE__);
$sql = '';
$f = file($dir . '/include/db/db.sql');
foreach($f AS $l) {
	if ( strpos(trim($l), '--')===0 || strpos(trim($l), '/*') === 0 || !trim($l)) {
		continue;
	}
	$sql .= $l;
}

mysql_query("SET names UTF8;");
$sqls = explode(';', $sql);

foreach($sqls AS $sql) {
	mysql_query($sql, $m);
}

$PHP = $INI = array(
	'db' => $db,
);
if ( save_config() ) {
	Session::Set('notice', '最土团购程序商业版安装成功，请及时删除根目录下install.php文件！');
}
redirect('index.php');
