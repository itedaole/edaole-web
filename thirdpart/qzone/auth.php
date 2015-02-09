<?php
include_once( 'config.php' );
include_once( 'txwboauth.php' );

$c = new WeiboClient( WB_AKEY , WB_SKEY , $_SESSION['last_key']['oauth_token'] , $_SESSION['last_key']['oauth_token_secret']  );
$ms=$c->pub_abc($_SESSION['last_key']['openid']);

$id = $_SESSION['last_key']['openid'];
$name = $ms['nickname'];


if(!$id) {
	need_login();
}
$type = "qzone";
$sns = "qzone:".$id;
$exist_user = Table::Fetch('user', $sns, 'sns');
if ( $exist_user ) {
	Session::Set('user_id', $exist_user['id']);
	Utility::Redirect(get_loginpage(WEB_ROOT . '/index.php'));
}

$prompt_name = $ms['nickname'];
$exist_user = Table::Fetch('user', $prompt_name, 'username');
while(!empty($exist_user)) {
	$prompt_name = $ms['nickname'] .'_' . rand(100,999);
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

Utility::Redirect(WEB_ROOT . '/thirdpart/qzone/index.php' );
      

