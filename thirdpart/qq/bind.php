<?php
require_once(dirname(__FILE__).'/config.php');

if(is_login()) Utility::Redirect($pagehost);

$qq = new QQ();
$userinfo = $qq->getUserInfo();
$userinfo= json_decode($userinfo);

$id = $_SESSION['openid'];
$type = 'QQ';
$name = $type.'_'.$userinfo->nickname;
if(!$id) { need_login(); }

$sns = "QQ:{$id}";
$exist_user = Table::Fetch('user', $sns, 'sns');
if ( $exist_user ) {
	Session::Set('user_id', $exist_user['id']);
	Utility::Redirect(get_loginpage(WEB_ROOT . '/index.php'));
}

$prompt_name = $name;
$exist_user = Table::Fetch('user', $prompt_name, 'username');
while(!empty($exist_user)) {
	$prompt_name = $name .'_' . rand(100,999);
	$exist_user = Table::Fetch('user', $prompt_name, 'username');
}

$new_user = array(
	'username' => $prompt_name,
	'realname' => $userinfo->nickname,
	'email' => "{$prompt_name}@qq.edaole.com.cn",
	'password' => rand(10000000,99999999),
	'gender' => $userinfo->gender=='男'?'M':'F',
	'sns' => $sns
);

if ( $user_id = ZUser::Create($new_user, true) ) {
	Session::Set('user_id', $user_id);
	Utility::Redirect(get_loginpage(WEB_ROOT . '/index.php'));
}

Utility::Redirect(WEB_ROOT . '/index.php');
