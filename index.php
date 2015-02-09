<?php
require_once(dirname(__FILE__) . '/app.php');
$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
$uachar = "/(nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|mobile)/i";
if(($ua == '' || preg_match($uachar, $ua))&& !strpos(strtolower($_SERVER['REQUEST_URI']),'wap'))
{
  redirect(WEB_ROOT."/wap");
}

if(!$INI['db']['host']) redirect( WEB_ROOT . '/install.php' );
if($city&&option_yes('rewritecity')){
	redirect(WEB_ROOT."/{$city['ename']}");
}

$request_uri = 'index';

$group_id = abs(intval($_GET['gid']));



if (option_yes('indexmulti')&& option_yes('indexpage')) {
	$city_id = abs(intval($city['id']));
	$now = time();
	$size = abs(intval($INI['system']['indexteam']));
	//if ($size<=1) return current_team($city_id);
	if ($size<=1) $size = 24;
	$condition = array( 
			'team_type' => 'normal',
			"begin_time < '{$now}'",
			"end_time > '{$now}'",
			);
	if($group_id) $condition['group_id']=$group_id;
	$condition[] = "(city_ids like '%@{$city_id}@%' or city_ids like '%@0@%') or (city_ids = '' and city_id in(0,{$city_id}))";
	$count = Table::Count('team', $condition);
	list($pagesize, $offset, $pagestring) = pagestring($count, $size);
	$teams = DB::LimitQuery('team', array(
				'condition' => $condition,
				'order' => 'ORDER BY `sort_order` DESC, `id` DESC',
				'size' => $pagesize,
				'offset' => $offset,
				));
	$disable_multi = true;
	
	die(require_once( dirname(__FILE__) . '/multi.php'));

}else{
	$team = $teams = index_get_team($city['id'],$group_id);	
	if ($team && $team['id']) {
		$_GET['id'] = abs(intval($team['id']));
		die(require_once( dirname(__FILE__) . '/team.php'));
	}
	elseif ($teams) {
		$disable_multi = true;
		die(require_once( dirname(__FILE__) . '/multi.php'));
	}
}



include template('subscribe');


