<?php
require_once(dirname(__FILE__) . '/config.php');

$o = new WeiboOAuth( WB_AKEY , WB_SKEY  );

$keys = $o->getRequestToken();
$aurl = $o->getAuthorizeURL( $keys['oauth_token'] ,false , $INI['system']['wwwprefix'] . '/thirdpart/sina/callback.php');
$_SESSION['keys'] = $keys;
redirect($aurl);
