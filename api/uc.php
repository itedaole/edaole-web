<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');
require_once(WWW_ROOT . '/uc_client/lib/xml.class.php');
if ( !defined('UC_KEY')) exit('Access Denied');
ob_get_clean();

error_reporting(0);

define('API_RETURN_SUCCEED', '1');
define('API_RETURN_FAILED', '-1');
define('API_RETURN_FORBIDDEN', '-2');

$get = $post = array();
$code = strval(@$_GET['code']);
parse_str(uc_api_x_authcode($code, 'DECODE', UC_KEY), $get);
$timestamp = time();
if(empty($get)) {
	die('Invalid Request');
} elseif($timestamp - $get['time'] > 3600) {
	die('Authracation has expiried');
}
$action = $get['action'];
$post = xml_unserialize(file_get_contents('php://input'));

//action
if( in_array($get['action'], array(
				'test',
				'renameuser', 
				'synlogin',
				'synlogout',
				'updatepw', 
				))) {
	$funcname = "uc_api_{$get['action']}";
	if ( function_exists($funcname) ) {
		exit($funcname($get, $post));
	}
} 
exit(API_RETURN_FAILED);

/* communicate with ucenter */
function uc_api_test($get, $post) {
	return API_RETURN_SUCCEED;
}

/* rename user */
function uc_api_renameuser($get, $post) {
	$usernameold = $get['oldusername'];
	$usernamenew = $get['newusername'];
	if(strtolower(UC_CHARSET)!='utf-8') { $usernameold = mb_convert_encoding($usernameold, 'UTF-8', UC_CHARSET); }
	if(strtolower(UC_CHARSET)!='utf-8') { $usernamenew = mb_convert_encoding($usernamenew, 'UTF-8', UC_CHARSET); }
	$u = Table::Fetch('user', $usernameold, 'username');
	if ( $u ) { 
		Table::UpdateCache('user', $u['id'], array(
					'username' => $usernamenew,
					));
	}
	return API_RETURN_SUCCEED;
}

/* synchronize login */
function uc_api_synlogin($get, $post) {
	$username = $get['username'];
	if(strtolower(UC_CHARSET)!='utf-8') { $username = mb_convert_encoding($username, 'UTF-8', UC_CHARSET); }
	$u = Table::Fetch('user', $username, 'username');
	if ($u) ZLogin::Login($u['id']);
	return API_RETURN_SUCCEED;
}

/* synchronize logout */
function uc_api_synlogout($get, $post) {
	header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	ZLogin::NoRemember();
	if (isset($_SESSION['user_id'])) {
		unset($_SESSION['user_id']);
	}
	return API_RETURN_SUCCEED;
}

/* update password */
function uc_api_updatepw($get, $post) {
	$username = $get['username'];
	$password = ZUser::GenPassword($get['password']);
	if(strtolower(UC_CHARSET)!='utf-8') { $username = mb_convert_encoding($username, 'UTF-8', UC_CHARSET); }
	$u = Table::Fetch('user', $username, 'username');
	if ( $u && $u['password'] != $password ) {
		Table::UpdateCache('user', $u['id'], array(
			'password' => $password,
		));
		if ($_SESSION['user_id'] && $_SESSION['user_id'] == $u['id']) {
			unset($_SESSION['user_id']);
		}
	}
	return API_RETURN_SUCCEED;
}

function uc_api_x_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;

	$key = md5($key ? $key : UC_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}

function uc_api_x_stripslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = _stripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}
