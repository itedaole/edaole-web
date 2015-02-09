<?php
@header('Content-Type:text/html;charset=utf-8'); 
session_start();
include_once( 'config.php' );
include_once( 'weibooauth.php' );


$o = new WeiboOAuth( WB_AKEY , WB_SKEY , $_SESSION['keys']['oauth_token'] , $_SESSION['keys']['oauth_token_secret']  );

$last_key = $o->getAccessToken(  $_REQUEST['oauth_verifier'] ) ;//获取ACCESSTOKEN

$_SESSION['last_key'] = $last_key;

   if(!option_yes('firstsinalogin')){ 
	Utility::Redirect( WEB_ROOT . '/thirdpart/sina/auth.php' );
	}

Utility::Redirect( WEB_ROOT . '/account/sina_bind.php' );

