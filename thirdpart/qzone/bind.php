<?php
require_once(dirname(__FILE__).'/config.php');
if(is_login()) Utility::Redirect($pagehost);
$qc = new QC();
if($_GET['code']&&$_GET['state']){$qc->qq_callback();$qc->get_openid();Utility::Redirect('bind.php');}
$type = 'qzone';
$id = $qc->get_openid(); 
$ms = $qc->get_user_info();
$name = $ms['nickname'];
if(!$id) { need_login(); }

$sns = "qzone:{$id}";
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
	'password' => rand(10000000,99999999),
	'sns' => $sns,
);

if ( $user_id = ZUser::Create($new_user, true) ) {
	Session::Set('user_id', $user_id);
	Utility::Redirect(get_loginpage(WEB_ROOT . '/index.php'));
}

Utility::Redirect(WEB_ROOT . '/thirdpart/qzone/login.php' );