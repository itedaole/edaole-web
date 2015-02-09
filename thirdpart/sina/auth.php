<?php
@header('Content-Type:text/html;charset=utf-8'); 
session_start();
include_once( 'config.php' );
include_once( 'weibooauth.php' );
$c = new WeiboClient( WB_AKEY , WB_SKEY , $_SESSION['last_key']['oauth_token'] , $_SESSION['last_key']['oauth_token_secret']  );
$ms = $c->verify_credentials();
if(!$ms['id'] || !$ms['screen_name']) {
	need_login();
}

$sns = "sina:{$ms['id']}";
$exist_user = Table::Fetch('user', $sns, 'sns');
if ( $exist_user ) {
	Session::Set('user_id', $exist_user['id']);
	Utility::Redirect(get_loginpage(WEB_ROOT . '/index.php'));
}

$prompt_name = $ms['screen_name'];
$exist_user = Table::Fetch('user', $prompt_name, 'username');
while(!empty($exist_user)) {
	$prompt_name = $ms['screen_name'] .'_' . rand(100,999);
	$exist_user = Table::Fetch('user', $prompt_name, 'username');
}

$new_user = array(
	'username' => $prompt_name,
	'realname' => $ms['name'],
	'email' => "{$prompt_name}@t.sina.com.cn",
	'password' => rand(10000000,99999999),
	'gender' => $ms['gender'],
	'sns' => $sns,
);

if ( $user_id = ZUser::Create($new_user, true) ) {
	Session::Set('user_id', $user_id);
	Utility::Redirect(get_loginpage(WEB_ROOT . '/index.php'));
}

Utility::Redirect(WEB_ROOT . '/thirdpart/sina/login.php' );
      
 ?>
