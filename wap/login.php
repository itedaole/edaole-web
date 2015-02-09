<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

if ( $login_user_id ) { 
	redirect('index.php'); 
}

if ( $_POST ) {
	$login_user = ZUser::GetLogin($_POST['email'], $_POST['password']);
	if ( !$login_user ) {
		Session::Set('error', '登录失败');
		redirect('login.php');
	} else if (option_yes('emailverify')
			&& $login_user['enable']=='N'
			&& $login_user['secret']
			) {
		Session::Set('error', "您的邮箱{$login_user['email']}还没有通过验证");
		redirect('login.php');
	} else {
		Session::Set('user_id', $login_user['id']);
		ZLogin::Remember($login_user);
		redirect('index.php');
	}
}

$currefer = strval($_GET['r']);
if ($currefer) { Session::Set('loginpage', udecode($currefer)); }
$pagetitle = '登录';
include template('m_login');
