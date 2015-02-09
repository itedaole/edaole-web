<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_login(true);
$condition = array( 
		'user_id' => $login_user_id, 
		'team_id > 0', 
		);

$selector = strval($_GET['s']);
if( $selector == 'unpay' ) {
	$condition['state'] = 'unpay';
	$suffix = '_unpay';
}
else {
	$condition['state'] = 'pay';
}

$count = Table::Count('order', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 10, true);
$orders = DB::LimitQuery('order', array(
	'condition' => $condition,
	'order' => 'ORDER BY id DESC, team_id DESC',
	'size' => $pagesize,
	'offset' => $offset,
));

$team_ids = Utility::GetColumn($orders, 'team_id');
$teams = Table::Fetch('team', $team_ids);
foreach($teams AS $tid=>$one){
	team_state($one);
	$teams[$tid] = $one;
}

$pagetitle = '我的订单';
include template("wap_myorder{$suffix}_json");
