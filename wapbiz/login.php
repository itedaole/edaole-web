<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

if ( $_POST ) {
	$login_partner = ZPartner::GetLogin($_POST['username'], $_POST['password']);
	if ( !$login_partner ) {
		Session::Set('error', '用户名密码不匹配！');
		redirect( WEB_ROOT . '/wapbiz/login.php');
	} else {
		Session::Set('partner_id', $login_partner['id']);
		redirect( WEB_ROOT . '/wapbiz/index.php');
	}
}
$pagetitle = $INI['system']['abbreviation'].'商家登陆';
include template('mb_login');
