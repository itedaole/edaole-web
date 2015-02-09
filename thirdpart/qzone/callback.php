<?php
@header('Content-Type:text/html;charset=utf-8'); 
session_start();
include_once( 'config.php' );
include_once( 'txwboauth.php' );

$a=$_GET['oauth_token'];
$b=$_GET['oauth_vericode'];
$o = new WeiboOAuth( WB_AKEY,WB_SKEY,$_SESSION['keys']['oauth_token'],$_SESSION['keys']['oauth_token_secret']  );

$last_key = $o->getAccessToken($b,$a ) ;//获取ACCESSTOKEN
//print_r($last_key);
$_SESSION['last_key']=$last_key;

if(!option_yes('firstqzonelogin')){ 
	Utility::Redirect( WEB_ROOT . '/thirdpart/qzone/auth.php' );
	}

Utility::Redirect(WEB_ROOT . '/account/qzone_bind.php' );
