<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_login();
$condition = array( 
	 'user_id' => $login_user_id,
	 'team_id > 0', 	 
 );


$count = Table::Count('comment', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 10);
$orders = DB::LimitQuery('comment', array(
	'condition' => $condition,
	'order' => 'ORDER BY team_id DESC, id ASC',
	'size' => $pagesize,
	'offset' => $offset,
));

$team_ids = Utility::GetColumn($orders, 'team_id');
$teams = Table::Fetch('team', $team_ids);
foreach($teams AS $tid=>$one){
	team_state($one);
	$teams[$tid] = $one;
}

$pagetitle = '我的平价';
include template('account_comment');
