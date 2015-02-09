<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager(true);

$version = strval(SYS_VERSION);
$subversion = strval(SYS_SUBVERSION);
$action = strval($_GET['action']);

if ( 'db' == $action ) {
	$r = zuitu_upgrade($action, $version);
    log_admin('system', '升级数据库结构');
	Session::Set('notice', '数据库结构升级成功，数据库已经是最新版本');
	redirect( WEB_ROOT . '/manage/system/upgrade.php' );
}

$version_meta_new = zuitu_version_new('version',$version);

//print_r($version_meta_new);


$version_meta = zuitu_version($version);
$newversion = strval($version_meta_new['0']['l']);
$newsubversion = strval($version_meta_new['1']['l']);
$software = $version_meta['software'];


$install = $version_meta['install'];
$patch = $version_meta['patch'];
$patchdesc = $version_meta['patchdesc'];
$upgrade = $version_meta['upgrade'];
$upgradedesc = $version_meta['upgradedesc'];


$isnew = ( $newversion==$version && $subversion == $newsubversion ) ;

include template('manage_system_upgrade');
