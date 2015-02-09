<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

if (is_post()) {
	$user = Table::Fetch('user', strval($_POST['email']), 'email');
	if ( $user ) {
		$user['recode'] = $user['recode'] ? $user['recode'] : md5(json_encode($user));
		Table::UpdateCache('user', $user['id'], array(
			'recode' => $user['recode'],
		));
		mail_repass($user);
		Session::Set('reemail', $user['email']);
		redirect( WEB_ROOT .'/account/repass.php?action=ok');
	}
	Session::Set('error', '你的Email没有在本站注册');
	redirect( WEB_ROOT . '/account/repass.php');
}

$action = strval($_GET['action']);
if ( $action == 'ok') {
	die(include template('account_repass_ok'));
}
$pagetitle = '重设密码';
include template('account_repass');
